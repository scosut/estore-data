<?php
	header('Access-Control-Allow-Origin: *');

	# load config
	require_once "../../../config/index.php";

	# load Validate
	require_once "../../../classes/Validate.php";

	$rest_json = file_get_contents("php://input");
	$_POST     = json_decode($rest_json, true);
	$_POST     = filter_var_array($_POST, FILTER_SANITIZE_STRING);

	$data = Validate::setProperties(array_keys($_POST), $_POST);

	Validate::isNotEmpty($data->payment, "Please select payment method.");

	if (Validate::isValid($data)) {		
		echo json_encode(['succeeded' => true]);
	}
	else {
		echo json_encode(['succeeded' => false, 'errors' => Validate::getErrors($data)]);
	}
?>