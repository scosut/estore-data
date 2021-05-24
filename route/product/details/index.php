<?php
	header('Access-Control-Allow-Origin: *');

	# load config
	require_once "../../../config/index.php";

	# load Database
	require_once "../../../classes/Database.php";

	# load Product
	require_once "../../../classes/Product.php";

	$obj = new Product();
	$id  = $_GET['id'];
	$product = $obj->getProductById($id);

	echo json_encode($product);	
?>