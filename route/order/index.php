<?php
	header('Access-Control-Allow-Origin: *');

	# load config
	require_once "../../config/index.php";

	# load Database
	require_once "../../classes/Database.php";

	# load Order
	require_once "../../classes/Order.php";

	$obj = new Order();
	$orders = $obj->getOrders();

	echo json_encode($orders);
?>