<?php
	header('Access-Control-Allow-Origin: *');

	# load config
	require_once "../../../config/index.php";

	# load Database
	require_once "../../../classes/Database.php";

	# load User
	require_once "../../../classes/User.php";

	# load Validate
	require_once "../../../classes/Validate.php";

	$obj = new User();

	$rest_json = file_get_contents("php://input");
	$_POST     = json_decode($rest_json, true);
	$_POST     = filter_var_array($_POST, FILTER_SANITIZE_STRING);	

	$data = Validate::setProperties(array_keys($_POST), $_POST);

	Validate::isNotEmpty($data->name, "Please enter name.");
				
	if (Validate::isNotEmpty($data->email, "Please enter email.")) {
		if (Validate::isValidEmail($data->email, "Please enter valid email.")) {
			Validate::toggleError($data->email, $obj->findUserByEmail($data), "Email is already taken.");
		}
	}														 
				
	if (Validate::isNotEmpty($data->password, "Please enter password.")) {
		Validate::isMinLength($data->password, 5, "Password must be at least 5 characters.");
	}
				
	if (Validate::isNotEmpty($data->confirm, "Please confirm password.")) {
		Validate::doMatch($data->password, $data->confirm, "Passwords do not match.");
	}

	if (Validate::isValid($data)) {		
		if ($obj->register($data)) {
			echo json_encode(['succeeded' => true]);
		}
		else {
			echo json_encode(['succeeded' => false]);
		}
	}
	else {
		echo json_encode(['succeeded' => false, 'errors' => Validate::getErrors($data)]);
	}
?>