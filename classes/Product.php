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
			$this->db->query("CALL spAddProduct(:_name, :_price, :_image, :_brand, :_quantity, :_description)");
			
			$params = [
				":_name"        => $data->name->value, 
				":_price"       => $data->price->value, 
				":_image"       => $data->image->value,
				":_brand"       => $data->brand->value,
				":_quantity"    => $data->quantity->value,
				":_description" => $data->description->value
			];
			
			$this->db->bindArray($params);
			
			return $this->db->executeAndGetId();			
		}
		
		public function getProductById($id) {
			$this->db->query("CALL spGetProductById(:_id)");
			$this->db->bind(":_id", $id);
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
				'price'        => $results[0]->price,
				'image'        => $results[0]->image,
				'brand'        => $results[0]->brand,
				'quantity'     => $results[0]->quantity,
				'description'  => $results[0]->description,
				'reviewRating' => $results[0]->reviewRating,
				'reviewCount'  => $results[0]->reviewCount,
				'purchasers'   => explode(",", $results[0]->purchasers),
				'reviews'      => $reviews
			];
		}
		
		public function updateProduct($data) {
			$this->db->query("CALL spUpdateProduct(:_id, :_name, :_price, :_image, :_brand, :_quantity, :_description)");
			
			$params = [
				":_id"          => $data->id->value,
				":_name"        => $data->name->value, 
				":_price"       => $data->price->value, 
				":_image"       => strlen($data->image->value) > 0 ? $data->image->value : null,
				":_brand"       => $data->brand->value,
				":_quantity"    => $data->quantity->value,
				":_description" => $data->description->value
			];
			
			$this->db->bindArray($params);
			
			return $this->db->execute();
		}
		
		public function deleteProduct($id) {
			$this->db->query("CALL spDeleteProduct(:_id)");
			$this->db->bind(":_id", $id);
			
			return $this->db->execute();
		}		
	}
?>