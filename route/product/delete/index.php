<?php
	header('Access-Control-Allow-Origin: *');

	# load config
	require_once "../../../config/index.php";

	# load Database
	require_once "../../../classes/Database.php";

	# load Product
	require_once "../../../classes/Product.php";

	$rest_json = file_get_contents("php://input");
	$_POST     = json_decode($rest_json, true);
	$id        = $_POST['id'];

	$obj = new Product();
	$obj->deleteProduct($id);
	$products = $obj->GetProducts();

	echo json_encode($products);
?>