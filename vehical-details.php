<?php 
session_start();
include('includes/config.php');

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
error_reporting(0);
if (isset($_POST['submit'])) {
    if (
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        exit('Invalid form request. Please go back and try again.');
    }
    if (empty($_SESSION['login'])) {
        echo "<script>alert('Please log in before booking a vehicle.');</script>";
    } else {
        $fromdate = trim($_POST['fromdate']);
        $todate = trim($_POST['todate']);
        $message = trim($_POST['message']);
        $billingAddress = trim($_POST['billing_address'] ?? '');
        $shippingAddress = trim($_POST['shipping_address'] ?? '');
        $billingPhone = trim($_POST['billing_phone'] ?? '');
        $shippingPhone = trim($_POST['shipping_phone'] ?? '');
        $useremail = $_SESSION['login'];
        $vhid = (int) $_GET['vhid'];
        $status = 0;

        $from = DateTime::createFromFormat('Y-m-d', $fromdate);
        $to = DateTime::createFromFormat('Y-m-d', $todate);
        $isValidFrom = $from && $from->format('Y-m-d') === $fromdate;
        $isValidTo = $to && $to->format('Y-m-d') === $todate;

        if (!$isValidFrom || !$isValidTo || $from > $to || $from < new DateTime('today') || $vhid < 1 || $message === '' || $billingAddress === '' || $shippingAddress === '' || $billingPhone === '' || $shippingPhone === '') {
            echo "<script>alert('Please select valid booking dates.');</script>";
        } else {
            $fromdate = $from->format('Y-m-d');
            $todate = $to->format('Y-m-d');
            try {
                $dbh->beginTransaction();
                // This conditional update reserves one unit safely. It prevents stock going below zero.
                $reserveStock = $dbh->prepare('UPDATE tblvehicles SET AvailableQuantity = AvailableQuantity - 1, PopularityCount = PopularityCount + 1 WHERE id = :vhid AND AvailableQuantity > 0');
                $reserveStock->execute([':vhid' => $vhid]);

                if ($reserveStock->rowCount() !== 1) {
                    $dbh->rollBack();
                    echo "<script>alert('This vehicle is out of stock. Please choose another model.');</script>";
                } else {
                $sql = "INSERT INTO tblbooking
                        (userEmail, VehicleId, FromDate, ToDate, message, BillingAddress, ShippingAddress, BillingPhone, ShippingPhone, Status)
                        VALUES (:useremail, :vhid, :fromdate, :todate, :message, :billingaddress, :shippingaddress, :billingphone, :shippingphone, :status)";

                $query = $dbh->prepare($sql);
                $query->execute([
                    ':useremail' => $useremail,
                    ':vhid' => $vhid,
                    ':fromdate' => $fromdate,
                    ':todate' => $todate,
                    ':message' => $message,
                    ':billingaddress' => $billingAddress,
                    ':shippingaddress' => $shippingAddress,
                    ':billingphone' => $billingPhone,
                    ':shippingphone' => $shippingPhone,
                    ':status' => $status
                ]);
                $bookingId = (int) $dbh->lastInsertId();
                $dbh->commit();
                header('Location: invoice.php?booking=' . $bookingId . '&placed=1');
                exit;
                }
            } catch (Exception $e) {
                if ($dbh->inTransaction()) {
                    $dbh->rollBack();
                }
                echo "<script>alert('Unable to place the booking. Please try again.');</script>";
            }
        }
    }
}

?>


<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="keywords" content="">
<meta name="description" content="">
<title>Car Rental Port | Vehicle Details</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<!--Custome Style -->
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<!--OWL Carousel slider-->
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<!--slick-slider -->
<link href="assets/css/slick.css" rel="stylesheet">
<!--bootstrap-slider -->
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<!--FontAwesome Font Style -->
<link href="assets/css/font-awesome.min.css" rel="stylesheet">
<style>
  .vehicle-image-modal .modal-dialog { margin: 20px auto; max-width: 1000px; width: calc(100% - 30px); }
  .vehicle-image-modal .modal-content { background: #111; border: 0; border-radius: 8px; max-height: calc(100vh - 40px); overflow: hidden; padding: 38px 12px 12px; }
  .vehicle-image-modal .close { color: #fff; font-size: 32px; opacity: .9; position: absolute; right: 15px; top: 0; z-index: 1; }
  .vehicle-image-modal img { display: block; height: auto; margin: 0 auto; max-height: calc(100vh - 92px); max-width: 100%; object-fit: contain; width: auto; }
</style>

<!-- SWITCHER -->
		<link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/red.css" title="red" media="all" data-default-color="true" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/orange.css" title="orange" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/blue.css" title="blue" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/pink.css" title="pink" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/green.css" title="green" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/purple.css" title="purple" media="all" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/favicon-icon/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/images/favicon-icon/apple-touch-icon-114-precomposed.html">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/images/favicon-icon/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="assets/images/favicon-icon/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="assets/images/favicon-icon/favicon.png">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
</head>
<body>

<!-- Start Switcher -->
<?php include('includes/colorswitcher.php');?>
<!-- /Switcher -->  

<!--Header-->
<?php include('includes/header.php');?>
<!-- /Header --> 

<!--Listing-Image-Slider-->

<?php 
$vhid=intval($_GET['vhid']);
$sql = "SELECT tblvehicles.*,tblbrands.BrandName,tblbrands.id as bid  from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand where tblvehicles.id=:vhid";
$query = $dbh -> prepare($sql);
$query->bindParam(':vhid',$vhid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$vehicleInStock = false;
$availableQuantity = 0;
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{  
$_SESSION['brndid']=$result->bid;  
$availableQuantity = (int) $result->AvailableQuantity;
$vehicleInStock = $availableQuantity > 0;
?>  

<section id="listing_img_slider">
  <div><a href="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1);?>" class="vehicle-image-trigger"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1);?>" class="img-responsive" alt="View vehicle image" width="900" height="560"></a></div>
  <div><a href="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage2);?>" class="vehicle-image-trigger"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage2);?>" class="img-responsive" alt="View vehicle image" width="900" height="560"></a></div>
  <div><a href="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage3);?>" class="vehicle-image-trigger"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage3);?>" class="img-responsive" alt="View vehicle image" width="900" height="560"></a></div>
  <div><a href="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage4);?>" class="vehicle-image-trigger"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage4);?>" class="img-responsive"  alt="View vehicle image" width="900" height="560"></a></div>
  <?php if($result->Vimage5=="")
{

} else {
  ?>
  <div><a href="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage5);?>" class="vehicle-image-trigger"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage5);?>" class="img-responsive" alt="View vehicle image" width="900" height="560"></a></div>
  <?php } ?>
</section>
<!--/Listing-Image-Slider-->

<div class="modal fade vehicle-image-modal" id="vehicleImageModal" tabindex="-1" role="dialog" aria-label="Vehicle image preview">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <img id="vehicleImageModalPreview" src="" alt="Expanded vehicle image">
    </div>
  </div>
</div>


<!--Listing-detail-->
<section class="listing-detail">
  <div class="container">
    <div class="listing_detail_head row">
      <div class="col-md-9">
        <h2><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></h2>
      </div>
      <div class="col-md-3">
        <div class="price_info">
          <p>&#8377;<?php echo number_format((float) $result->PricePerDay, 0);?> </p>Per Day
         
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-9">
        <div class="main_features">
          <ul>
          
            <li> <i class="fa fa-calendar" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->ModelYear);?></h5>
              <p>Reg.Year</p>
            </li>
            <li> <i class="fa fa-cogs" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->FuelType);?></h5>
              <p>Fuel Type</p>
            </li>
       
            <li> <i class="fa fa-user-plus" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->SeatingCapacity);?></h5>
              <p>Seats</p>
            </li>
          </ul>
        </div>
        <div class="listing_more_info">
          <div class="listing_detail_wrap"> 
            <!-- Nav tabs -->
            <ul class="nav nav-tabs gray-bg" role="tablist">
              <li role="presentation" class="active"><a href="#vehicle-overview " aria-controls="vehicle-overview" role="tab" data-toggle="tab">Vehicle Overview </a></li>
          
              <li role="presentation"><a href="#accessories" aria-controls="accessories" role="tab" data-toggle="tab">Accessories</a></li>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content"> 
              <!-- vehicle-overview -->
              <div role="tabpanel" class="tab-pane active" id="vehicle-overview">
                
                <p><?php echo htmlentities($result->VehiclesOverview);?></p>
              </div>
              
              
              <!-- Accessories -->
              <div role="tabpanel" class="tab-pane" id="accessories"> 
                <!--Accessories-->
                <table>
                  <thead>
                    <tr>
                      <th colspan="2">Accessories</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Air Conditioner</td>
<?php if($result->AirConditioner==1)
{
?>
                      <td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?> 
   <td><i class="fa fa-close" aria-hidden="true"></i></td>
   <?php } ?> </tr>

<tr>
<td>AntiLock Braking System</td>
<?php if($result->AntiLockBrakingSystem==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else {?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
                    </tr>

<tr>
<td>Power Steering</td>
<?php if($result->PowerSteering==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>
                   

<tr>

<td>Power Windows</td>

<?php if($result->PowerWindows==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>
                   
 <tr>
<td>CD Player</td>
<?php if($result->CDPlayer==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Leather Seats</td>
<?php if($result->LeatherSeats==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Central Locking</td>
<?php if($result->CentralLocking==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Power Door Locks</td>
<?php if($result->PowerDoorLocks==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
                    </tr>
                    <tr>
<td>Brake Assist</td>
<?php if($result->BrakeAssist==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php  } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Driver Airbag</td>
<?php if($result->DriverAirbag==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
 </tr>
 
 <tr>
 <td>Passenger Airbag</td>
 <?php if($result->PassengerAirbag==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else {?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Crash Sensor</td>
<?php if($result->CrashSensor==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
        </div>
<?php }} ?>
   
      </div>
      
      <!--Side-Bar-->
      <aside class="col-md-3">
      
        <div class="share_vehicle">
          <p>Share: <a href="#"><i class="fa fa-facebook-square" aria-hidden="true"></i></a> <a href="#"><i class="fa fa-twitter-square" aria-hidden="true"></i></a> <a href="#"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a> <a href="#"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a> </p>
        </div>
        <div class="sidebar_widget">
          <div class="widget_heading">
            <h5><i class="fa fa-envelope" aria-hidden="true"></i><?php echo $vehicleInStock ? 'Book Now' : 'Out of Stock'; ?></h5>
          </div>
          <?php if ($vehicleInStock) { ?>
          <p><strong><?php echo htmlentities($availableQuantity); ?></strong> car<?php echo $availableQuantity === 1 ? '' : 's'; ?> available</p>
          <form method="post">
          <input type="hidden" name="csrf_token"
           value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-group">
              <input type="date" class="form-control" name="fromdate" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
              <input type="date" class="form-control" name="todate" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group"><textarea rows="3" class="form-control" name="billing_address" placeholder="Billing address" required></textarea></div>
            <div class="form-group"><input class="form-control" name="billing_phone" placeholder="Billing contact number" required></div>
            <label><input type="checkbox" id="sameShipping"> Shipping address is the same as billing address</label>
            <div class="form-group"><textarea rows="3" class="form-control" name="shipping_address" placeholder="Shipping address" required></textarea></div>
            <div class="form-group"><input class="form-control" name="shipping_phone" placeholder="Shipping contact number" required></div>
            <div class="form-group">
              <textarea rows="4" class="form-control" name="message" placeholder="Message" required></textarea>
            </div>
          <?php if($_SESSION['login'])
              {?>
              <div class="form-group">
                <input type="submit" class="btn"  name="submit" value="Book Now">
              </div>
              <?php } else { ?>
<a href="#loginform" class="btn btn-xs uppercase" data-auth-open="loginform">Login For Book</a>

              <?php } ?>
          </form>
          <?php } else { ?>
          <p class="text-danger"><strong>Out of stock</strong></p>
          <p>This vehicle model is currently unavailable. Please choose another car.</p>
          <?php } ?>
        </div>
      </aside>
      <!--/Side-Bar--> 
    </div>
    
    <div class="space-20"></div>
    <div class="divider"></div>
    
    <!--Similar-Cars-->
    <div class="similar_cars">
      <h3>Similar Cars</h3>
      <div class="row">
<?php 
$bid=$_SESSION['brndid'];
$sql="SELECT tblvehicles.VehiclesTitle,tblbrands.BrandName,tblvehicles.PricePerDay,tblvehicles.FuelType,tblvehicles.ModelYear,tblvehicles.id,tblvehicles.SeatingCapacity,tblvehicles.VehiclesOverview,tblvehicles.Vimage1,tblvehicles.AvailableQuantity from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand where tblvehicles.VehiclesBrand=:bid";
$query = $dbh -> prepare($sql);
$query->bindParam(':bid',$bid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{ ?>      
        <div class="col-md-3 grid_listing">
          <div class="product-listing-m gray-bg">
            <div class="product-listing-img"> <a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1);?>" class="img-responsive" alt="image" /> </a>
            </div>
            <div class="product-listing-content">
              <h5><a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></a></h5>
              <p class="list-price">&#8377;<?php echo number_format((float) $result->PricePerDay, 0);?></p>
              <p><?php echo (int) $result->AvailableQuantity > 0 ? htmlentities($result->AvailableQuantity) . ' available' : 'Out of stock'; ?></p>
          
              <ul class="features_list">
                
             <li><i class="fa fa-user" aria-hidden="true"></i><?php echo htmlentities($result->SeatingCapacity);?> seats</li>
                <li><i class="fa fa-calendar" aria-hidden="true"></i><?php echo htmlentities($result->ModelYear);?> model</li>
                <li><i class="fa fa-car" aria-hidden="true"></i><?php echo htmlentities($result->FuelType);?></li>
              </ul>
            </div>
          </div>
        </div>
 <?php }} ?>       

      </div>
    </div>
    <!--/Similar-Cars--> 
    
  </div>
</section>
<!--/Listing-detail--> 

<!--Footer -->
<?php include('includes/footer.php');?>
<!-- /Footer--> 

<!--Back to top-->
<div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
<!--/Back to top--> 

<!--Login-Form -->
<?php include('includes/login.php');?>
<!--/Login-Form --> 

<!--Register-Form -->
<?php include('includes/registration.php');?>

<!--/Register-Form --> 

<!--Forgot-password-Form -->
<?php include('includes/forgotpassword.php');?>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<script src="assets/switcher/js/switcher.js"></script>
<script src="assets/js/bootstrap-slider.min.js"></script> 
<script src="assets/js/slick.min.js"></script> 
<script src="assets/js/owl.carousel.min.js"></script>
<script>
  $(document).on('click', '.vehicle-image-trigger', function (event) {
    event.preventDefault();
    $('#vehicleImageModalPreview').attr('src', this.href);
    $('#vehicleImageModal').modal('show');
  });
  $('#sameShipping').on('change', function () {
    if (this.checked) { $('textarea[name="shipping_address"]').val($('textarea[name="billing_address"]').val()); $('input[name="shipping_phone"]').val($('input[name="billing_phone"]').val()); }
  });
</script>

</body>
</html>
