<?php
//error_reporting(0);
if(isset($_POST['signup']))
{
$fname=trim($_POST['fullname']);
$email=strtolower(trim($_POST['emailid']));
$mobile=trim($_POST['mobileno']);
$plainPassword=$_POST['password'];
if ($fname === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[0-9]{10,15}$/', $mobile) || strlen($plainPassword) < 8) {
    echo "<script>alert('Please enter a valid name, email, mobile number, and password of at least 8 characters.');</script>";
} else {
    $existing = $dbh->prepare('SELECT id FROM tblusers WHERE EmailId = :email LIMIT 1');
    $existing->execute([':email' => $email]);
    if ($existing->fetch()) {
        echo "<script>alert('This email address is already registered.');</script>";
    } else {
      $password = password_hash($plainPassword, PASSWORD_DEFAULT);
      try {
        $sql="INSERT INTO tblusers(FullName,EmailId,ContactNo,Password) VALUES(:fname,:email,:mobile,:password)";
        $query = $dbh->prepare($sql);
        $query->execute([':fname' => $fname, ':email' => $email, ':mobile' => $mobile, ':password' => $password]);
        echo "<script>alert('Registration successful. You can now log in.');</script>";
      } catch (PDOException $e) {
        echo "<script>alert('This email address is already registered.');</script>";
      }
    }
}
}

?>


<script>
function checkAvailability() {
$("#loaderIcon").show();
jQuery.ajax({
url: "check_availability.php",
data:'emailid='+$("#emailid").val(),
type: "POST",
success:function(data){
$("#user-availability-status").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}
</script>
<script type="text/javascript">
function valid()
{
if(document.signup.password.value!= document.signup.confirmpassword.value)
{
alert("Password and Confirm Password Field do not match  !!");
document.signup.confirmpassword.focus();
return false;
}
return true;
}
</script>
<div class="modal fade" id="signupform">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-auth-close aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Sign Up</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="signup_wrap">
            <div class="col-md-12 col-sm-6">
              <form  method="post" name="signup" onSubmit="return valid();">
                <div class="form-group">
                  <input type="text" class="form-control" name="fullname" placeholder="Full Name" required="required">
                </div>
                      <div class="form-group">
                  <input type="text" class="form-control" name="mobileno" placeholder="Mobile Number" maxlength="10" required="required">
                </div>
                <div class="form-group">
                  <input type="email" class="form-control" name="emailid" id="emailid" onBlur="checkAvailability()" placeholder="Email Address" required="required">
                   <span id="user-availability-status" style="font-size:12px;"></span> 
                </div>
                <div class="form-group">
                  <input type="password" class="form-control" name="password" placeholder="Password" required="required">
                </div>
                <div class="form-group">
                  <input type="password" class="form-control" name="confirmpassword" placeholder="Confirm Password" required="required">
                </div>
                <div class="form-group checkbox">
                  <input type="checkbox" id="terms_agree" required="required" checked="">
                  <label for="terms_agree">I Agree with <a href="#">Terms and Conditions</a></label>
                </div>
                <div class="form-group">
                  <input type="submit" value="Sign Up" name="signup" id="submit" class="btn btn-block">
                </div>
              </form>
            </div>
            
          </div>
        </div>
      </div>
      <div class="modal-footer text-center">
        <p>Already got an account? <a href="#loginform" data-auth-open="loginform">Login Here</a></p>
      </div>
    </div>
  </div>
</div>
