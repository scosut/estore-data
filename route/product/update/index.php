<?php
	header('Access-Control-Allow-Origin: *');

	# load config
	require_once "../../../config/index.php";

	# load Database
	require_once "../../../classes/Database.php";

	# load Product
	require_once "../../../classes/Product.php";

	# load Validate
	require_once "../../../classes/Validate.php";

	$obj  = new Product();
	$post = json_decode($_POST['product'], true);
	$post = filter_var_array($post, FILTER_SANITIZE_STRING);
	$data = Validate::setProperties(array_keys($post), $post);
	$target_dir = dirname( __FILE__, 4) . "\\uploads\\";

	Validate::isNotEmpty($data->name, "Please enter name.");

	if (Validate::isNotEmpty($data->price, "Please enter price.")) {
		if (Validate::isNumeric($data->price, "Price is not a valid number.")) {
			Validate::isGreaterThan($data->price, 0, "Price must be greater than zero.");
		}
	}

	Validate::isNotEmpty($data->brand, "Please enter brand.");

	if (Validate::isNotEmpty($data->quantity, "Please enter quantity.")) {
		if (Validate::isOnlyDigits($data->quantity, "Quantity is not a valid number.")) {
			Validate::isGreaterThan($data->quantity, 0, "Quantity must be greater than zero.");
		}
	}

	Validate::isNotEmpty($data->description, "Please enter description.");

	if (isset($_FILES['file'])) {
		Validate::checkImageFile($data->image, $target_dir);
	}

	if (Validate::isValid($data)) {
		$errors_file = "";
		
		if (isset($_FILES['file'])) {
			$target_file = $target_dir . basename($_FILES['file']['name']);
			$extension   = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));		
			$productId   = $data->id->value;	
			$fileName    = "product_$productId.$extension";
			
			if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir.basename($fileName))) {
				$data->image->value = "http://" . $_SERVER['SERVER_NAME'] . "/uploads/product_$productId.$extension";
				$errors_file = "";
			}
			else {
				$errors_file = "Error uploading file.";
			}
		}
		
		if (strlen($errors_file) === 0) {
			if ($obj->updateProduct($data)) {
				$products = $obj->getProducts();
				echo json_encode(['succeeded' => true, 'products' => $products]);
			}
			else {
				echo json_encode(['succeeded' => false]);
			}
		}
		else {
			echo json_encode(['succeeded' => false, 'errors' => $errors_file]);
		}	
	}
	else {
		echo json_encode(['succeeded' => false, 'errors' => Validate::getErrors($data)]);
	}
?>