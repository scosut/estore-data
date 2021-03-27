<?php
	class User {
		private $db;
		
		public function __construct() {
			$this->db = new Database();
		}
		
		# register user
		public function register($data) {
			$this->db->query("CALL spRegisterUser(:name, :email, :password, :role)");
			
			$params = [
				":name"     => $data->name->value, 
				":email"    => strtolower($data->email->value), 
				":password" => password_hash($data->password->value, PASSWORD_DEFAULT),
				":role"     => $data->role->value
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
		
		public function updateUser($data) {
			$this->db->query("CALL spUpdateUser(:id, :name, :email, :password)");
			
			$params = [
				":id"       => $data->id->value,
				":name"     => $data->name->value, 
				":email"    => strtolower($data->email->value), 
				":password" => strlen($data->password->value) > 0 ? password_hash($data->password->value, PASSWORD_DEFAULT) : null
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
		
		# login user
		public function login($data) {
			$this->db->query("CALL spFindUserByEmail(:email)");
			$this->db->bind(":email", strtolower($data->email->value));
			$row = $this->db->single();
			
			if (password_verify($data->password->value, $row->password)) {
				return $row;
			}
			else {
				return false;
			}
		}
		
		# find user by email
		public function findUserByEmail($data) {
			$this->db->query("CALL spFindUserByEmail(:email)");

			$this->db->bind(":email", strtolower($data->email->value));
			$row = $this->db->single();
			
			# check row			
			return $this->db->rowCount() > 0 ? true : false;
		}
		
		# search other users emails
		public function searchOtherUsersEmails($data) {
			$this->db->query("CALL spSearchOtherUsersEmails(:id, :email)");
			
			$params = [
				":id"    => $data->id->value,
				":email" => strtolower($data->email->value)
			];
			
			$this->db->bindArray($params);
			$row = $this->db->single();
			
			# check row			
			return $this->db->rowCount() > 0 ? true : false;
		}
		
		# get user by id
		public function getUserById($id) {
			$this->db->query("CALL spGetUserById(:id)");

			$this->db->bind(":id", $id);
			$row = $this->db->single();
			
			return $row;
		}
	}
?>