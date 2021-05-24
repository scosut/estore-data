<?php
	header('Access-Control-Allow-Origin: *');

	# load config
	require_once "../../config/index.php";

	# load Database
	require_once "../../classes/Database.php";

	# load Product
	require_once "../../classes/Product.php";

	$obj = new Product();
	$products = $obj->getProducts();

	echo json_encode($products);
?>