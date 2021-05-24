<?php
	header('Access-Control-Allow-Origin: *');

	# load config
	require_once "../../../config/index.php";

	# load Database
	require_once "../../../classes/Database.php";

	# load Order
	require_once "../../../classes/Order.php";

	# load Product
	require_once "../../../classes/Product.php";

	# load Validate
	require_once "../../../classes/Validate.php";

	$obj = new Order();

	$rest_json = file_get_contents("php://input");
	$_POST     = json_decode($rest_json, true);
	$_POST     = filter_var_array($_POST, FILTER_SANITIZE_STRING);
	$items     = $_POST['items'];
	unset($_POST['items']);

	$data = Validate::setProperties(array_keys($_POST), $_POST);

	$orderId = $obj->addOrder($data)->newRecordId;
	$bln = false;

	if (is_numeric($orderId)) {
		foreach($items as $item) {
			$bln = $obj->addOrderItem($item['quantity'], $item['productId'], $orderId);
			if ($bln === false) {
				break;
			}
		}

		if ($bln) {
			$order = $obj->getOrderById($orderId);
			$obj   = new Product();
			$products = $obj->getProducts();
			echo json_encode(['succeeded' => true, 'order' => $order, 'products' => $products]);
		}
		else {
			echo json_encode(['succeeded' => false, 'message' => 'Could not add order item.']);
		}
	}
	else {
		echo json_encode(['succeeded' => false, 'message' => 'Could not get order ID.']);
	}
?>