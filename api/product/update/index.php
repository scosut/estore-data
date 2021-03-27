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

	$obj = new Product();

	$rest_json = file_get_contents("php://input");
	$_POST     = json_decode($rest_json, true);
	$_POST     = filter_var_array($_POST, FILTER_SANITIZE_STRING);

	$data = Validate::setProperties(array_keys($_POST), $_POST);

	Validate::isNotEmpty($data->name, "Please enter name.");

	if (Validate::isNotEmpty($data->price, "Please enter price.")) {
		if (Validate::isNumeric($data->price, "Price is not a valid number.")) {
			Validate::isGreaterThan($data->price, 0, "Price must be greater than zero.");
		}
	}

	Validate::isNotEmpty($data->brand, "Please enter brand.");

	if (Validate::isNotEmpty($data->quantity, "Please enter quantity.")) {
		if (Validate::isNumeric($data->quantity, "Quantity is not a valid number.")) {
			Validate::isGreaterThan($data->quantity, 0, "Quantity must be greater than zero.");
		}
	}

	Validate::isNotEmpty($data->description, "Please enter description.");	

	if (Validate::isValid($data)) {
		if ($obj->updateProduct($data)) {
			$products = $obj->getProducts();
			echo json_encode(['succeeded' => true, 'products' => $products]);
		}
		else {
			echo json_encode(['succeeded' => false]);
		}
	}
	else {
		echo json_encode(['succeeded' => false, 'errors' => Validate::getErrors($data)]);
	}
?>