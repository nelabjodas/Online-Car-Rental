<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
if (empty($_SESSION['booking_csrf_token'])) {
    $_SESSION['booking_csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['booking_action'] ?? '';
    $bookingId = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['booking_csrf_token'], $csrfToken) || $bookingId === false || !in_array($action, array('confirm', 'cancel'), true)) {
        $error = 'Invalid booking request. Please refresh the page and try again.';
    } else {
        try {
            $dbh->beginTransaction();
            $bookingQuery = $dbh->prepare('SELECT VehicleId, Status FROM tblbooking WHERE id = :id FOR UPDATE');
            $bookingQuery->execute(array(':id' => $bookingId));
            $booking = $bookingQuery->fetch(PDO::FETCH_ASSOC);

            if (!$booking) {
                throw new RuntimeException('Booking was not found.');
            }

            $currentStatus = (int) $booking['Status'];
            if ($action === 'confirm') {
                if ($currentStatus !== 0) {
                    throw new RuntimeException($currentStatus === 1 ? 'This booking is already confirmed.' : 'A cancelled booking cannot be confirmed.');
                }
                $update = $dbh->prepare('UPDATE tblbooking SET Status = 1 WHERE id = :id');
                $update->execute(array(':id' => $bookingId));
                $msg = 'Booking successfully confirmed.';
            } else {
                if ($currentStatus === 2) {
                    throw new RuntimeException('This booking is already cancelled.');
                }
                $update = $dbh->prepare('UPDATE tblbooking SET Status = 2 WHERE id = :id');
                $update->execute(array(':id' => $bookingId));
                $restoreStock = $dbh->prepare('UPDATE tblvehicles SET AvailableQuantity = AvailableQuantity + 1 WHERE id = :vehicle_id');
                $restoreStock->execute(array(':vehicle_id' => (int) $booking['VehicleId']));
                $msg = $restoreStock->rowCount() === 1
                    ? 'Booking successfully cancelled and vehicle availability restored.'
                    : 'Booking successfully cancelled. Its old vehicle record no longer exists, so no inventory was restored.';
            }
            $dbh->commit();
        } catch (Throwable $e) {
            if ($dbh->inTransaction()) {
                $dbh->rollBack();
            }
            $error = $e->getMessage();
        }
    }
}


 ?>

<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#3e454c">
	
	<title>Car Rental Portal |Admin Manage testimonials   </title>

	<!-- Font awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Sandstone Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Bootstrap Datatables -->
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<!-- Bootstrap social button library -->
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<!-- Bootstrap select -->
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<!-- Bootstrap file input -->
	<link rel="stylesheet" href="css/fileinput.min.css">
	<!-- Awesome Bootstrap checkbox -->
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<!-- Admin Stye -->
	<link rel="stylesheet" href="css/style.css">
  <style>
		.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
		</style>

</head>

<body>
	<?php include('includes/header.php');?>

	<div class="ts-main-content">
		<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">

						<h2 class="page-title">Manage Bookings</h2>

						<!-- Zero Configuration Table -->
						<div class="panel panel-default">
							<div class="panel-heading">Bookings Info</div>
							<div class="panel-body">
							<?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
								<table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
										<th>#</th>
											<th>Name</th>
											<th>Vehicle</th>
											<th>From Date</th>
											<th>To Date</th>
											<th>Message</th>
											<th>Status</th>
											<th>Posting date</th>
											<th>Action</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
										<th>#</th>
										<th>Name</th>
											<th>Vehicle</th>
											<th>From Date</th>
											<th>To Date</th>
											<th>Message</th>
											<th>Status</th>
											<th>Posting date</th>
											<th>Action</th>
										</tr>
									</tfoot>
									<tbody>

<?php $sql = "SELECT COALESCE(tblusers.FullName, 'Unknown user') AS FullName, COALESCE(tblbrands.BrandName, 'Deleted brand') AS BrandName, COALESCE(tblvehicles.VehiclesTitle, CONCAT('Deleted vehicle #', tblbooking.VehicleId)) AS VehiclesTitle, tblbooking.FromDate, tblbooking.ToDate, tblbooking.message, tblbooking.VehicleId AS vid, tblvehicles.id AS vehicleRecordId, tblbooking.Status, tblbooking.PostingDate, tblbooking.id FROM tblbooking LEFT JOIN tblvehicles ON tblvehicles.id = tblbooking.VehicleId LEFT JOIN tblusers ON tblusers.EmailId = tblbooking.userEmail LEFT JOIN tblbrands ON tblvehicles.VehiclesBrand = tblbrands.id";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{				?>	
										<tr>
											<td><?php echo htmlentities($cnt);?></td>
											<td><?php echo htmlentities($result->FullName);?></td>
											<td><?php if ($result->vehicleRecordId) { ?><a href="edit-vehicle.php?id=<?php echo (int) $result->vehicleRecordId; ?>"><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></a><?php } else { echo htmlentities($result->VehiclesTitle); } ?></td>
											<td><?php echo htmlentities($result->FromDate);?></td>
											<td><?php echo htmlentities($result->ToDate);?></td>
											<td><?php echo htmlentities($result->message);?></td>
											<td><?php 
if($result->Status==0)
{
echo htmlentities('Not Confirmed yet');
} else if ($result->Status==1) {
echo htmlentities('Confirmed');
}
 else{
 	echo htmlentities('Cancelled');
 }
										?></td>
											<td><?php echo htmlentities($result->PostingDate);?></td>
										<td>
<form method="post" style="display:inline" onsubmit="return confirm('Do you really want to confirm this booking?');">
  <input type="hidden" name="csrf_token" value="<?php echo htmlentities($_SESSION['booking_csrf_token']); ?>">
  <input type="hidden" name="booking_id" value="<?php echo (int) $result->id; ?>">
  <input type="hidden" name="booking_action" value="confirm">
  <button type="submit" class="btn btn-link btn-xs"<?php echo ((int) $result->Status !== 0) ? ' disabled' : ''; ?>>Confirm</button>
</form>
/
<form method="post" style="display:inline" onsubmit="return confirm('Do you really want to cancel this booking?');">
  <input type="hidden" name="csrf_token" value="<?php echo htmlentities($_SESSION['booking_csrf_token']); ?>">
  <input type="hidden" name="booking_id" value="<?php echo (int) $result->id; ?>">
  <input type="hidden" name="booking_action" value="cancel">
  <button type="submit" class="btn btn-link btn-xs"<?php echo ((int) $result->Status === 2) ? ' disabled' : ''; ?>>Cancel</button>
</form>
 / <a href="../invoice.php?booking=<?php echo (int) $result->id; ?>" target="_blank">Invoice</a>
</td>

										</tr>
										<?php $cnt=$cnt+1; }} ?>
										
									</tbody>
								</table>

						

							</div>
						</div>

					

					</div>
				</div>

			</div>
		</div>
	</div>

	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
</body>
</html>
<?php } ?>
