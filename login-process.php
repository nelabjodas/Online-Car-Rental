<?php
session_start();
require_once __DIR__ . '/includes/config.php';

function loginRedirect(string $fallback = 'index.php'): void
{
    $returnUrl = $_POST['return_url'] ?? $fallback;
    // Redirect only to a local page so this endpoint cannot be used for phishing.
    if (!is_string($returnUrl) || $returnUrl === '' || str_starts_with($returnUrl, '//') || parse_url($returnUrl, PHP_URL_HOST) !== null) {
        $returnUrl = $fallback;
    }

    header('Location: ' . $returnUrl);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['login'])) {
    loginRedirect();
}

$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !is_string($password)) {
    $_SESSION['login_error'] = 'Please enter a valid email address and password.';
    $_SESSION['auth_modal'] = 'loginform';
    loginRedirect();
}

$query = $dbh->prepare('SELECT EmailId, Password, FullName FROM tblusers WHERE EmailId = :email LIMIT 1');
$query->execute([':email' => $email]);
$user = $query->fetch(PDO::FETCH_OBJ);

$passwordIsValid = $user && (
    password_verify($password, $user->Password) ||
    (preg_match('/^[a-f0-9]{32}$/i', $user->Password) && hash_equals(strtolower($user->Password), md5($password)))
);

if (!$passwordIsValid) {
    $_SESSION['login_error'] = 'Invalid email or password.';
    $_SESSION['auth_modal'] = 'loginform';
    loginRedirect();
}

if (password_needs_rehash($user->Password, PASSWORD_DEFAULT) || preg_match('/^[a-f0-9]{32}$/i', $user->Password)) {
    $upgrade = $dbh->prepare('UPDATE tblusers SET Password = :password WHERE EmailId = :email');
    $upgrade->execute([':password' => password_hash($password, PASSWORD_DEFAULT), ':email' => $user->EmailId]);
}

session_regenerate_id(true);
$_SESSION['login'] = $user->EmailId;
$_SESSION['fname'] = $user->FullName;
loginRedirect();
