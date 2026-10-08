<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($dbh)) {
    require_once __DIR__ . '/config.php';
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';

if (isset($_POST['update'])) {
    if (
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $message = 'Invalid request. Please try again.';
    } else {
        $email = strtolower(trim($_POST['email']));
        $mobile = trim($_POST['mobile']);
        $newpassword = $_POST['newpassword'];
        $confirmpassword = $_POST['confirmpassword'];

        if (strlen($newpassword) < 8) {
            $message = 'Password must contain at least 8 characters.';
        } elseif ($newpassword !== $confirmpassword) {
            $message = 'Passwords do not match.';
        } else {
            $query = $dbh->prepare(
                "SELECT EmailId FROM tblusers
                 WHERE EmailId = :email AND ContactNo = :mobile
                 LIMIT 1"
            );
            $query->execute([
                ':email' => $email,
                ':mobile' => $mobile
            ]);

            $user = $query->fetch(PDO::FETCH_OBJ);

            if ($user) {
                $passwordHash = password_hash($newpassword, PASSWORD_DEFAULT);

                $update = $dbh->prepare(
                    "UPDATE tblusers
                     SET Password = :password
                     WHERE EmailId = :email AND ContactNo = :mobile"
                );
                $update->execute([
                    ':password' => $passwordHash,
                    ':email' => $email,
                    ':mobile' => $mobile
                ]);

                $message = 'Password changed successfully. You can now log in.';
            } else {
                $message = 'Email address or mobile number is incorrect.';
            }
        }
    }
}
?>

<div class="modal fade" id="forgotpassword">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-auth-close>&times;</button>
        <h3 class="modal-title">Password Recovery</h3>
      </div>

      <div class="modal-body">
        <?php if ($message) { ?>
          <p><?php echo htmlspecialchars($message); ?></p>
        <?php } ?>

        <form method="post">
          <input type="hidden" name="csrf_token"
                 value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

          <div class="form-group">
            <input type="email" name="email" class="form-control"
                   placeholder="Your email address" required>
          </div>

          <div class="form-group">
            <input type="text" name="mobile" class="form-control"
                   placeholder="Your registered mobile number" required>
          </div>

          <div class="form-group">
            <input type="password" name="newpassword" class="form-control"
                   placeholder="New password (minimum 8 characters)"
                   required minlength="8">
          </div>

          <div class="form-group">
            <input type="password" name="confirmpassword" class="form-control"
                   placeholder="Confirm new password"
                   required minlength="8">
          </div>

          <div class="form-group">
            <input type="submit" name="update"
                   value="Reset My Password" class="btn btn-block">
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
