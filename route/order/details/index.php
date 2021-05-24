<?php
	header('Access-Control-Allow-Origin: *');

	# load config
	require_once "../../../config/index.php";

	# load Database
	require_once "../../../classes/Database.php";

	# load Order
	require_once "../../../classes/Order.php";

	$obj = new Order();
	$id  = $_GET['id'];
	$order = $obj->getOrderById($id);

	echo json_encode($order);
?>