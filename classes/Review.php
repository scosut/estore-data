<?php
	class Review {
		private $db;
		
		public function __construct() {
			$this->db = new Database();
		}
		
		public function getReviewsByProductId($productId) {
			$this->db->query("CALL spGetReviewsByProductId(:productId)");
			$this->db->bind(":productId", $productId);
			$results = $this->db->resultSet();
			
			return $results;
		}
		
		public function addReview($data) {
			$this->db->query("CALL spAddReview(:rating, :comments, :dateReviewed, :productId, :userId)");
			
			$params = [
				":rating"       => $data->rating->value, 
				":comments"     => $data->comments->value,
				":dateReviewed" => $data->dateReviewed->value,
				":productId"    => $data->productId->value,
				":userId"       => $data->userId->value
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
		
		public function getReviewById($id) {
			$this->db->query("CALL spGetReviewById(:id)");
			$this->db->bind(":id", $id);
			$row = $this->db->single();
			
			return $row;
		}
	}
?>