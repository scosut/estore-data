<?php
	class Order {
		private $db;
		
		public function __construct() {
			$this->db = new Database();
		}
		
		# find products
		public function getOrders() {
			$this->db->query("CALL spGetOrders()");
			$results = $this->db->resultSet();
			
			return $results;
		}
		
		public function getOrderById($id) {
			$this->db->query("CALL spGetOrderById(:_orderId)");
			$this->db->bind(":_orderId", $id);
			$results = $this->db->resultSet();
			$items = [];
			
			foreach ($results as $result) {
				$items[] = [
					'id'        => $result->itemId,
					'quantity'  => $result->itemQuantity,
					'productId' => $result->productId,
					'name'      => $result->name,
					'price'     => $result->price,
					'image'     => $result->image
				];
			}
			
			return [
				'id'            => $results[0]->orderId,
				'userId'        => $results[0]->userId,
				'userName'      => $results[0]->userName,
				'userEmail'     => $results[0]->userEmail,
				'shipping'      => [
														'address' => $results[0]->address, 
														'city'    => $results[0]->city,
														'postal'  => $results[0]->postal,
														'country' => $results[0]->country
													 ],
				'payment'       => $results[0]->payment,
				'datePlaced'    => $results[0]->datePlaced,
				'datePaid'      => $results[0]->datePaid,
				'dateDelivered' => $results[0]->dateDelivered,
				'items'         => $items
			];			
		}
		
		public function addOrder($data) {
			$this->db->query("CALL spAddOrder(:_address, :_city, :_postal, :_country, :_payment, :_datePlaced, :_userId)");
			
			$params = [
				":_address"    => $data->address->value, 
				":_city"       => $data->city->value, 
				":_postal"     => $data->postal->value,
				":_country"    => $data->country->value,
				":_payment"    => $data->payment->value,
				":_datePlaced" => $data->datePlaced->value,
				":_userId"     => $data->userId->value
			];
			
			$this->db->bindArray($params);
			
			# execute
			return $this->db->executeAndGetId();
		}
		
		public function addOrderItem($quantity, $productId, $orderId) {
			$this->db->query("CALL spAddOrderItem(:_quantity, :_productId, :_orderId)");
			
			$params = [
				":_quantity"  => $quantity,
				":_productId" => $productId, 
				":_orderId"   => $orderId
			];
			
			$this->db->bindArray($params);
			
			return $this->db->execute();
		}
		
		public function updateOrder($data) {
			$this->db->query("CALL spUpdateOrder(:_orderId, :_datePaid, :_dateDelivered)");
			
			$params = [
				":_orderId"       => $data->id->value,
				":_datePaid"      => strlen($data->datePaid->value) > 0 ? $data->datePaid->value : null, 
				":_dateDelivered" => strlen($data->dateDelivered->value) > 0 ? $data->dateDelivered->value : null
			];
			
			$this->db->bindArray($params);
			
			return $this->db->execute();
		}		
	}
?>