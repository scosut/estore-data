<?php
	header('Access-Control-Allow-Origin: *');

	# load config
	require_once "../../../config/index.php";

	# load Database
	require_once "../../../classes/Database.php";

	# load Product
	require_once "../../../classes/Order.php";

	# load Validate
	require_once "../../../classes/Validate.php";

	$obj = new Order();

	$rest_json = file_get_contents("php://input");
	$_POST     = json_decode($rest_json, true);
	$_POST     = filter_var_array($_POST, FILTER_SANITIZE_STRING);

	$data = Validate::setProperties(array_keys($_POST), $_POST);

	if ($obj->updateOrder($data)) {
		$order = $obj->getOrderById($data->id->value);
		echo json_encode(['succeeded' => true, 'order' => $order]);
	}
	else {
		echo json_encode(['succeeded' => false, 'message' => 'could not update order']);
	}
?>