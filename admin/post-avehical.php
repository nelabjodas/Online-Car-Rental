<?php
session_start();
error_reporting(0);
include('includes/config.php');
function validateVehicleImage(array $file): array
{
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Please upload a valid vehicle image.');
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('Each image must be 5 MB or smaller.');
    }

    $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

    if (!isset($allowedTypes[$mimeType])) {
        throw new Exception('Only JPG, PNG, and WebP images are allowed.');
    }

    return array(
        'extension' => $allowedTypes[$mimeType],
        'hash' => hash_file('sha256', $file['tmp_name']),
        'tmp_name' => $file['tmp_name']
    );
}

function uploadVehicleImage(array $image): string
{
    $filename = 'vehicle_' . bin2hex(random_bytes(16)) . '.' . $image['extension'];
    $destination = __DIR__ . '/img/vehicleimages/' . $filename;

    if (!move_uploaded_file($image['tmp_name'], $destination)) {
        throw new Exception('Unable to save the uploaded image.');
    }

    return $filename;
}
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{ 

if(isset($_POST['submit']))
  {
$vehicletitle=$_POST['vehicletitle'];
$brand=$_POST['brandname'];
$vehicleoverview=$_POST['vehicalorcview'];
$priceperday=$_POST['priceperday'];
$fueltype=$_POST['fueltype'];
$modelyear=$_POST['modelyear'];
$seatingcapacity=$_POST['seatingcapacity'];
$availablequantity=filter_input(INPUT_POST, 'availablequantity', FILTER_VALIDATE_INT, array('options' => array('min_range' => 0)));
try {
    $imageFields = array('img1', 'img2', 'img3', 'img4');
    if (($_FILES['img5']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $imageFields[] = 'img5';
    }

    $images = array();
    $hashes = array();
    foreach ($imageFields as $field) {
        $images[$field] = validateVehicleImage($_FILES[$field]);
        if (in_array($images[$field]['hash'], $hashes, true)) {
            throw new Exception('Please select a different photo for each image field.');
        }
        $hashes[] = $images[$field]['hash'];
    }

    $vimage1 = uploadVehicleImage($images['img1']);
    $vimage2 = uploadVehicleImage($images['img2']);
    $vimage3 = uploadVehicleImage($images['img3']);
    $vimage4 = uploadVehicleImage($images['img4']);
    $vimage5 = isset($images['img5']) ? uploadVehicleImage($images['img5']) : null;
} catch (Exception $e) {
    $error = $e->getMessage();
}
$airconditioner=$_POST['airconditioner'];
$powerdoorlocks=$_POST['powerdoorlocks'];
$antilockbrakingsys=$_POST['antilockbrakingsys'];
$brakeassist=$_POST['brakeassist'];
$powersteering=$_POST['powersteering'];
$driverairbag=$_POST['driverairbag'];
$passengerairbag=$_POST['passengerairbag'];
$powerwindow=$_POST['powerwindow'];
$cdplayer=$_POST['cdplayer'];
$centrallocking=$_POST['centrallocking'];
$crashcensor=$_POST['crashcensor'];
$leatherseats=$_POST['leatherseats'];

if ($availablequantity === false) {
    $error = 'Please enter a valid available quantity.';
}

if (empty($error)) {
try {
$sql="INSERT INTO tblvehicles(VehiclesTitle,VehiclesBrand,VehiclesOverview,PricePerDay,FuelType,ModelYear,SeatingCapacity,AvailableQuantity,Vimage1,Vimage2,Vimage3,Vimage4,Vimage5,AirConditioner,PowerDoorLocks,AntiLockBrakingSystem,BrakeAssist,PowerSteering,DriverAirbag,PassengerAirbag,PowerWindows,CDPlayer,CentralLocking,CrashSensor,LeatherSeats) VALUES(:vehicletitle,:brand,:vehicleoverview,:priceperday,:fueltype,:modelyear,:seatingcapacity,:availablequantity,:vimage1,:vimage2,:vimage3,:vimage4,:vimage5,:airconditioner,:powerdoorlocks,:antilockbrakingsys,:brakeassist,:powersteering,:driverairbag,:passengerairbag,:powerwindow,:cdplayer,:centrallocking,:crashcensor,:leatherseats)";
$query = $dbh->prepare($sql);
$query->bindParam(':vehicletitle',$vehicletitle,PDO::PARAM_STR);
$query->bindParam(':brand',$brand,PDO::PARAM_STR);
$query->bindParam(':vehicleoverview',$vehicleoverview,PDO::PARAM_STR);
$query->bindParam(':priceperday',$priceperday,PDO::PARAM_STR);
$query->bindParam(':fueltype',$fueltype,PDO::PARAM_STR);
$query->bindParam(':modelyear',$modelyear,PDO::PARAM_STR);
$query->bindParam(':seatingcapacity',$seatingcapacity,PDO::PARAM_STR);
$query->bindParam(':availablequantity',$availablequantity,PDO::PARAM_INT);
$query->bindParam(':vimage1',$vimage1,PDO::PARAM_STR);
$query->bindParam(':vimage2',$vimage2,PDO::PARAM_STR);
$query->bindParam(':vimage3',$vimage3,PDO::PARAM_STR);
$query->bindParam(':vimage4',$vimage4,PDO::PARAM_STR);
$query->bindParam(':vimage5',$vimage5,PDO::PARAM_STR);
$query->bindParam(':airconditioner',$airconditioner,PDO::PARAM_STR);
$query->bindParam(':powerdoorlocks',$powerdoorlocks,PDO::PARAM_STR);
$query->bindParam(':antilockbrakingsys',$antilockbrakingsys,PDO::PARAM_STR);
$query->bindParam(':brakeassist',$brakeassist,PDO::PARAM_STR);
$query->bindParam(':powersteering',$powersteering,PDO::PARAM_STR);
$query->bindParam(':driverairbag',$driverairbag,PDO::PARAM_STR);
$query->bindParam(':passengerairbag',$passengerairbag,PDO::PARAM_STR);
$query->bindParam(':powerwindow',$powerwindow,PDO::PARAM_STR);
$query->bindParam(':cdplayer',$cdplayer,PDO::PARAM_STR);
$query->bindParam(':centrallocking',$centrallocking,PDO::PARAM_STR);
$query->bindParam(':crashcensor',$crashcensor,PDO::PARAM_STR);
$query->bindParam(':leatherseats',$leatherseats,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
$msg="Vehicle posted successfully";
}
else 
{
	$error="Something went wrong. Please try again";
}

} catch (Throwable $e) {
    $error = 'Unable to publish the vehicle. Check that MySQL is running and that the database has the latest schema.';
    error_log($e->getMessage());
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
	
	<title>Car Rental Portal | Admin Post Vehicle</title>

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
					
						<h2 class="page-title">Post A Vehicle</h2>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Basic Info</div>
<?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data">
<div class="form-group">
<label class="col-sm-2 control-label">Vehicle Title<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="vehicletitle" class="form-control" required>
</div>
<label class="col-sm-2 control-label">Select Brand<span style="color:red">*</span></label>
<div class="col-sm-4">
<select class="selectpicker" name="brandname" required>
<option value=""> Select </option>
<?php $ret="select id,BrandName from tblbrands";
$query= $dbh -> prepare($ret);
//$query->bindParam(':id',$id, PDO::PARAM_STR);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
if($query -> rowCount() > 0)
{
foreach($results as $result)
{
?>
<option value="<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->BrandName);?></option>
<?php }} ?>

</select>
</div>
</div>
											
<div class="hr-dashed"></div>
<div class="form-group">
<label class="col-sm-2 control-label">Vehical Overview<span style="color:red">*</span></label>
<div class="col-sm-10">
<textarea class="form-control" name="vehicalorcview" rows="3" required></textarea>
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Price Per Day (&#8377; INR)<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="number" name="priceperday" class="form-control" min="0" step="1" required>
</div>
<label class="col-sm-2 control-label">Select Fuel Type<span style="color:red">*</span></label>
<div class="col-sm-4">
<select class="selectpicker" name="fueltype" required>
<option value=""> Select </option>

<option value="Petrol">Petrol</option>
<option value="Diesel">Diesel</option>
<option value="CNG">CNG</option>
</select>
</div>
</div>


<div class="form-group">
<label class="col-sm-2 control-label">Model Year<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="modelyear" class="form-control" required>
</div>
<label class="col-sm-2 control-label">Seating Capacity<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="seatingcapacity" class="form-control" required>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">Available Cars<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="number" name="availablequantity" class="form-control" min="0" step="1" value="0" required>
<span class="help-block">Number of units customers can book for this vehicle model.</span>
</div>
</div>
<div class="hr-dashed"></div>


<div class="form-group">
<div class="col-sm-12">
<h4><b>Upload Images</b></h4>
<p id="imageUploadMessage" class="image-upload-message" role="status" aria-live="polite"></p>
</div>
</div>

<div class="modal fade" id="duplicatePhotoModal" tabindex="-1" role="dialog" aria-labelledby="duplicatePhotoTitle">
  <div class="modal-dialog" role="document">
    <div class="modal-content duplicate-photo-modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="duplicatePhotoTitle"><i class="fa fa-exclamation-triangle"></i> Duplicate photo not accepted</h4>
      </div>
      <div class="modal-body" id="duplicatePhotoText"></div>
      <div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">Choose another photo</button></div>
    </div>
  </div>
</div>

<div class="modal fade" id="usedPhotoModal" tabindex="-1" role="dialog" aria-labelledby="usedPhotoTitle">
  <div class="modal-dialog" role="document">
    <div class="modal-content duplicate-photo-modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="usedPhotoTitle"><i class="fa fa-info-circle"></i> Photo already used</h4>
      </div>
      <div class="modal-body" id="usedPhotoText"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="discardUsedPhoto" data-dismiss="modal">Discard photo</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Use photo anyway</button>
      </div>
    </div>
  </div>
</div>


<div class="form-group">
<div class="col-sm-4">
Image 1 <span style="color:red">*</span><input type="file" name="img1" class="vehicle-photo-input" accept="image/jpeg,image/png,image/webp" required><div class="photo-preview"></div>
</div>
<div class="col-sm-4">
Image 2<span style="color:red">*</span><input type="file" name="img2" class="vehicle-photo-input" accept="image/jpeg,image/png,image/webp" required><div class="photo-preview"></div>
</div>
<div class="col-sm-4">
Image 3<span style="color:red">*</span><input type="file" name="img3" class="vehicle-photo-input" accept="image/jpeg,image/png,image/webp" required><div class="photo-preview"></div>
</div>
</div>


<div class="form-group">
<div class="col-sm-4">
Image 4<span style="color:red">*</span><input type="file" name="img4" class="vehicle-photo-input" accept="image/jpeg,image/png,image/webp" required><div class="photo-preview"></div>
</div>
<div class="col-sm-4">
Image 5<input type="file" name="img5" class="vehicle-photo-input" accept="image/jpeg,image/png,image/webp"><div class="photo-preview"></div>
</div>

</div>
<div class="hr-dashed"></div>									
</div>
</div>
</div>
</div>
							

<div class="row">
<div class="col-md-12">
<div class="panel panel-default">
<div class="panel-heading">Accessories</div>
<div class="panel-body">


<div class="form-group">
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="airconditioner" name="airconditioner" value="1">
<label for="airconditioner"> Air Conditioner </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="powerdoorlocks" name="powerdoorlocks" value="1">
<label for="powerdoorlocks"> Power Door Locks </label>
</div></div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="antilockbrakingsys" name="antilockbrakingsys" value="1">
<label for="antilockbrakingsys"> AntiLock Braking System </label>
</div></div>
<div class="checkbox checkbox-inline">
<input type="checkbox" id="brakeassist" name="brakeassist" value="1">
<label for="brakeassist"> Brake Assist </label>
</div>
</div>



<div class="form-group">
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="powersteering" name="powersteering" value="1">
<input type="checkbox" id="powersteering" name="powersteering" value="1">
<label for="inlineCheckbox5"> Power Steering </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="driverairbag" name="driverairbag" value="1">
<label for="driverairbag">Driver Airbag</label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="passengerairbag" name="passengerairbag" value="1">
<label for="passengerairbag"> Passenger Airbag </label>
</div></div>
<div class="checkbox checkbox-inline">
<input type="checkbox" id="powerwindow" name="powerwindow" value="1">
<label for="powerwindow"> Power Windows </label>
</div>
</div>


<div class="form-group">
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="cdplayer" name="cdplayer" value="1">
<label for="cdplayer"> CD Player </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox h checkbox-inline">
<input type="checkbox" id="centrallocking" name="centrallocking" value="1">
<label for="centrallocking">Central Locking</label>
</div></div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="crashcensor" name="crashcensor" value="1">
<label for="crashcensor"> Crash Sensor </label>
</div></div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="leatherseats" name="leatherseats" value="1">
<label for="leatherseats"> Leather Seats </label>
</div>
</div>
</div>




											<div class="form-group">
												<div class="col-sm-8 col-sm-offset-2">
													<button class="btn btn-default" type="reset">Cancel</button>
													<button class="btn btn-primary" name="submit" type="submit">Save changes</button>
												</div>
											</div>

										</form>
									</div>
								</div>
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
	<style>
	  .vehicle-photo-input + .photo-preview { display:block; margin-top:10px; max-width:100%; }
	  .vehicle-photo-input + .photo-preview img { border:1px solid #d8e0ec; border-radius:8px; box-shadow:0 3px 10px rgba(15,23,42,.10); display:block; height:120px !important; max-width:100%; object-fit:cover; width:100% !important; }
	  .vehicle-photo-input + .photo-preview .remove-photo { background:#dc2626; border:0; border-radius:5px; color:#fff; font-size:11px; font-weight:700; margin-top:7px; padding:6px 10px; }
	  @media (min-width:768px){.vehicle-photo-input + .photo-preview{width:190px}.vehicle-photo-input + .photo-preview img{width:190px !important;}}
	</style>
	<script>
	(function () {
		var inputs = Array.prototype.slice.call(document.querySelectorAll('.vehicle-photo-input'));
		var message = document.getElementById('imageUploadMessage');
		var allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
		var previouslyUsedInput = null;

		function showMessage(text, type) {
			message.textContent = text;
			message.className = 'image-upload-message ' + type;
		}

		function showDuplicatePopup(text) {
			document.getElementById('duplicatePhotoText').textContent = text;
			$('#duplicatePhotoModal').modal('show');
		}

		function checkPreviouslyUsedPhoto(input, hash) {
			fetch('check-vehicle-image.php', {
				method: 'POST',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				body: 'hash=' + encodeURIComponent(hash)
			}).then(function (response) {
				return response.ok ? response.json() : null;
			}).then(function (result) {
				if (!result || !result.used || input.dataset.photoHash !== hash) return;
				previouslyUsedInput = input;
				document.getElementById('usedPhotoText').textContent = 'This photo is already used by another vehicle. You may keep it by choosing “Use photo anyway”, or remove it and select a different photo.';
				$('#usedPhotoModal').modal('show');
			}).catch(function () {
				// The final server-side validation still checks the selected image files.
			});
		}

		document.getElementById('discardUsedPhoto').addEventListener('click', function () {
			if (!previouslyUsedInput) return;
			previouslyUsedInput.value = '';
			delete previouslyUsedInput.dataset.photoHash;
			showMessage('Photo discarded. Please select a different image.', 'info');
			previouslyUsedInput = null;
		});

		function hashFile(file) {
			return file.arrayBuffer().then(function (buffer) {
				return crypto.subtle.digest('SHA-256', buffer);
			}).then(function (hash) {
				return Array.prototype.map.call(new Uint8Array(hash), function (byte) {
					return byte.toString(16).padStart(2, '0');
				}).join('');
			});
		}

		inputs.forEach(function (input) {
			input.addEventListener('change', function () {
				var file = input.files[0];
				delete input.dataset.photoHash;
				if (!file) return;
				var preview = input.parentNode.querySelector('.photo-preview');
				preview.innerHTML = '<img src="' + URL.createObjectURL(file) + '" alt="Selected photo"><button type="button" class="remove-photo">Remove</button>';
				if (allowedTypes.indexOf(file.type) === -1 || file.size > 5 * 1024 * 1024) {
					input.value = '';
					showMessage('Only JPG, PNG, or WebP images up to 5 MB are accepted.', 'error');
					return;
				}
				if (!window.crypto || !crypto.subtle) {
					showMessage('Photo will be checked when you submit the form.', 'info');
					return;
				}
				var requestId = String(Date.now()) + Math.random();
				input.dataset.hashRequest = requestId;
				showMessage('Checking selected photo…', 'info');
				hashFile(file).then(function (hash) {
					if (input.dataset.hashRequest !== requestId) return;
					var matchingInput = inputs.find(function (otherInput) {
						return otherInput !== input && otherInput.dataset.photoHash === hash;
					});
					if (matchingInput) {
						input.value = '';
						delete input.dataset.photoHash;
						var duplicateText = 'This is the same photo as ' + matchingInput.name.replace('img', 'Image ') + '. Each vehicle image must be different. Please select another photo.';
						showMessage(duplicateText, 'error');
						showDuplicatePopup(duplicateText);
						return;
					}
					input.dataset.photoHash = hash;
					showMessage('Photo accepted. You can select the next image.', 'success');
					checkPreviouslyUsedPhoto(input, hash);
				}).catch(function () {
					showMessage('Photo will be checked when you submit the form.', 'info');
				});
			});
		});
		document.addEventListener('click', function (event) { if (!event.target.classList.contains('remove-photo')) return; var input=event.target.closest('.col-sm-4').querySelector('.vehicle-photo-input'); input.value=''; delete input.dataset.photoHash; event.target.parentNode.innerHTML=''; });
	}());
	</script>
</body>
</html>
<?php } ?>
