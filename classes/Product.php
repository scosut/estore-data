<?php
	class Product {
		private $db;
		
		public function __construct() {
			$this->db = new Database();
		}
		
		private function fixRating($products) {
			foreach($products as $p) {
				$rating = $p->reviewRating;
				$rating = $rating === null ? "review-0-0" : "review-".str_replace(".", "-", $rating);
				$p->reviewRating = $rating;
			}
			
			return $products;
		}
		
		# find products
		public function getProducts() {
			$this->db->query("CALL spGetProducts()");
			$results = $this->db->resultSet();
			
			return $this->fixRating($results);
		}
		
		public function addProduct($data) {
			$this->db->query("CALL spAddProduct(:name, :price, :image, :brand, :quantity, :description)");
			
			$params = [
				":name"        => $data->name->value, 
				":price"       => $data->price->value, 
				":image"       => $data->image->value,
				":brand"       => $data->brand->value,
				":quantity"    => $data->quantity->value,
				":description" => $data->description->value
			];
			
			$this->db->bindArray($params);
			
			# execute
			if ($this->db->execute()) {
				return true;
			}
			else {
				return false;
			}
		}
		
		public function updateProduct($data) {
			$this->db->query("CALL spUpdateProduct(:id, :name, :price, :image, :brand, :quantity, :description)");
			
			$params = [
				":id"          => $data->id->value,
				":name"        => $data->name->value, 
				":price"       => $data->price->value, 
				":image"       => $data->image->value,
				":brand"       => $data->brand->value,
				":quantity"    => $data->quantity->value,
				":description" => $data->description->value
			];
			
			$this->db->bindArray($params);
			
			# execute
			if ($this->db->execute()) {
				return true;
			}
			else {
				return false;
			}
		}
		
		public function deleteProduct($id) {
			$this->db->query("CALL spDeleteProduct(:id)");
			$this->db->bind(":id", $id);
			
			# execute
			if ($this->db->execute()) {
				return true;
			}
			else {
				return false;
			}
		}
		
		public function getProductById($id) {
			$this->db->query("CALL spGetProductById(:id)");
			$this->db->bind(":id", $id);
			$results = $this->db->resultSet();
			$reviews = [];
			
			$results = $this->fixRating($results);

			foreach ($results as $result) {
				if ($result->reviewCount > 0) {
					$reviews[] = [
						'id'           => $result->reviewId, 
						'rating'       => $result->rating, 
						'comments'     => $result->comments, 
						'dateReviewed' => $result->dateReviewed, 
						'userId'       => $result->userId, 
						'userName'     => $result->userName
					];
				}
			}
			
			return [
				'id'           => $results[0]->productId, 
				'name'         => $results[0]->name,
				'image'        => $results[0]->image,
				'brand'        => $results[0]->brand,
				'quantity'     => $results[0]->quantity,
				'description'  => $results[0]->description,
				'reviewRating' => $results[0]->reviewRating,
				'reviewCount'  => $results[0]->reviewCount,
				'reviews'      => $reviews
			];
		}
	}
?>