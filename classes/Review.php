<?php
	class Review {
		private $db;
		
		public function __construct() {
			$this->db = new Database();
		}
		
		public function addReview($data) {
			$this->db->query("CALL spAddReview(:_rating, :_comments, :_dateReviewed, :_productId, :_userId)");
			
			$params = [
				":_rating"       => $data->rating->value, 
				":_comments"     => $data->comments->value,
				":_dateReviewed" => $data->dateReviewed->value,
				":_productId"    => $data->productId->value,
				":_userId"       => $data->userId->value
			];
			
			$this->db->bindArray($params);
			
			return $this->db->execute();
		}
	}
?>