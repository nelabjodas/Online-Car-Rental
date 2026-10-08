<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<link rel="stylesheet" href="assets/css/redesign.css" type="text/css">

<header>

  <div class="default-header">
    <div class="container">
      <div class="row">

        <div class="col-sm-3 col-md-2">
          <div class="logo">
            <a href="index.php" aria-label="DriveNow home">
              <img src="assets/images/logg.png" alt="DriveNow"/>
            </a>
          </div>
        </div>

        <div class="col-sm-9 col-md-10">

          <div class="header_info">

            <div class="header_widgets">

              <?php
              if (empty($_SESSION['login'])) {
              ?>

                <!-- Login / Register - shown before login -->
                <div class="login_btn">
                  <a href="#loginform"
                     class="btn btn-xs uppercase"
                     data-auth-open="loginform">
                     Login / Register
                  </a>
                </div>

              <?php
              }
              ?>

            </div>

          </div>

        </div>

      </div>
    </div>
  </div>


  <!-- Navigation -->
  <nav id="navigation_bar" class="navbar navbar-default">

    <div class="container">

      <div class="navbar-header">

        <button id="menu_slide"
                data-target="#navigation"
                aria-expanded="false"
                data-toggle="collapse"
                class="navbar-toggle collapsed"
                type="button">

          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>

        </button>

      </div>


      <div class="header_wrap">


        <!-- Profile dropdown - shown only after login -->
        <?php
        if (!empty($_SESSION['login'])) {
        ?>

        <div class="user_login">

          <ul>

            <li class="dropdown">

              <?php
              $email = $_SESSION['login'];

              $sql = "SELECT FullName FROM tblusers WHERE EmailId=:email";

              $query = $dbh->prepare($sql);

              $query->bindParam(
                  ':email',
                  $email,
                  PDO::PARAM_STR
              );

              $query->execute();

              $results = $query->fetchAll(PDO::FETCH_OBJ);

              if ($query->rowCount() > 0) {

                foreach ($results as $result) {
              ?>

                  <a href="#"
                     class="dashboard-toggle"
                     data-toggle="dropdown"
                     aria-haspopup="true"
                     aria-expanded="false"
                     onclick="return toggleUserDashboard(event, this);">

                    <i class="fa fa-user-circle"
                       aria-hidden="true"></i>

                    <?php echo htmlentities($result->FullName); ?>

                    <i class="fa fa-angle-down"
                       aria-hidden="true"></i>

                  </a>

              <?php
                }
              }
              ?>


              <!-- Profile dropdown menu -->
            <ul class="dropdown-menu">

                <li>
                  <a href="profile.php">
                    Profile Settings
                  </a>
                </li>

                <li>
                  <a href="update-password.php">
                    Update Password
                  </a>
              </li>
                <li> <a href="my-booking.php">My Booking</a> </li>
                <li> <a href="post-testimonial.php">Post a Testimonial</a> </li>
                <li> <a href="my-testimonials.php">My Testimonial</a> </li>
                <li> <a href="logout.php">Sign Out</a> </li>
            </ul>
            </li>
          </ul>
        </div>
        <?php
        }
        ?>


        <!-- Search -->
        <div class="header_search">

          <div id="search_toggle">
            <i class="fa fa-search" aria-hidden="true"></i>
          </div>

          <form action="#" method="get" id="header-search-form">
            <input type="text"
                   placeholder="Search..."
                   class="form-control">
            <button type="submit">
              <i class="fa fa-search" aria-hidden="true"></i>
            </button>

          </form>
        </div>
      </div>


      <!-- Main navigation -->
      <div class="collapse navbar-collapse" id="navigation">
        <ul class="nav navbar-nav">

          <li> <a href="index.php">Home</a> </li>
          <li> <a href="page.php?type=aboutus">About Us</a> </li>
          <li> <a href="car-listing.php">Browse cars</a> </li>
          <li> <a href="page.php?type=faqs">FAQs</a> </li>
          <li> <a href="contact-us.php">Contact Us</a> </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Navigation end -->

</header>

<script>
function toggleUserDashboard(event, toggle) {
  event.preventDefault();
  event.stopPropagation();

  var dropdown = toggle.parentNode;
  var isOpen = dropdown.classList.toggle('open');
  toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  return false;
}

document.addEventListener('click', function () {
  var openDropdowns = document.querySelectorAll('.user_login .dropdown.open');
  for (var i = 0; i < openDropdowns.length; i++) {
    openDropdowns[i].classList.remove('open');
    var toggle = openDropdowns[i].querySelector('.dashboard-toggle');
    if (toggle) {
      toggle.setAttribute('aria-expanded', 'false');
    }
  }
});
</script>
