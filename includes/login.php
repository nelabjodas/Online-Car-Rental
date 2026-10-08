<div class="modal fade" id="loginform">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-auth-close aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Login</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="login_wrap">
            <div class="col-md-12 col-sm-6">
              <?php if (!empty($_SESSION['login_error'])) { ?>
                <div class="alert alert-danger">
                  <?php echo htmlspecialchars($_SESSION['login_error']); unset($_SESSION['login_error']); ?>
                </div>
              <?php } ?>
              <form method="post" action="login-process.php">
                <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'index.php'); ?>">
                <div class="form-group">
                  <input type="email" class="form-control" name="email" placeholder="Email address*">
                </div>
                <div class="form-group">
                  <input type="password" class="form-control" name="password" placeholder="Password*">
                </div>
                <div class="form-group checkbox">
                  <input type="checkbox" id="remember">
               
                </div>
                <div class="form-group">
                  <input type="submit" name="login" value="Login" class="btn btn-block">
                </div>
              </form>
            </div>
           
          </div>
        </div>
      </div>
      <div class="modal-footer text-center">
        <p>Don't have an account? <a href="#signupform" data-auth-open="signupform">Signup Here</a></p>
        <p><a href="#forgotpassword" data-auth-open="forgotpassword">Forgot Password ?</a></p>
      </div>
    </div>
  </div>
</div>
<script src="assets/js/auth-modals.js"></script>
<?php if (!empty($_SESSION['auth_modal'])) { ?>
<script>document.addEventListener('DOMContentLoaded', function () { openAuthModal(<?php echo json_encode($_SESSION['auth_modal']); unset($_SESSION['auth_modal']); ?>); });</script>
<?php } ?>
