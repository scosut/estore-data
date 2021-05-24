<?php
	header('Access-Control-Allow-Origin: *');

	# load config
	require_once "../../../config/index.php";

	# load Database
	require_once "../../../classes/Database.php";

	# load Review
	require_once "../../../classes/Review.php";

	# load Product
	require_once "../../../classes/Product.php";

	# load Validate
	require_once "../../../classes/Validate.php";

	$rest_json = file_get_contents("php://input");
	$_POST     = json_decode($rest_json, true);
	$_POST     = filter_var_array($_POST, FILTER_SANITIZE_STRING);
	$productId = $_POST['productId'];

	$data = Validate::setProperties(array_keys($_POST), $_POST);

	Validate::isNotEmpty($data->rating, "Please select a rating.");
				
	Validate::isNotEmpty($data->comments, "Please enter the comments.");

	if (Validate::isValid($data)) {
		$obj = new Review();
		
		if ($obj->addReview($data)) {
			$obj      = new Product();
			$product  = $obj->getProductById($productId);
			$products = $obj->getProducts();
			
			echo json_encode(['succeeded' => true, 'product' => $product, 'products' => $products]);
		}
		else {
			echo json_encode(['succeeded' => false]);
		}
	}
	else {
		echo json_encode(['succeeded' => false, 'errors' => Validate::getErrors($data)]);
	}
?>