<?php
	class User {
		private $db;
		
		public function __construct() {
			$this->db = new Database();
		}
		
		# find user by email
		public function findUserByEmail($data) {
			$this->db->query("CALL spFindUserByEmail(:_email)");

			$this->db->bind(":_email", strtolower($data->email->value));
			$row = $this->db->single();
			
			return empty($row) ? false : true;
		}
		
		# search other users emails
		public function searchOtherUsersEmails($data) {
			$this->db->query("CALL spSearchOtherUsersEmails(:_id, :_email)");
			
			$params = [
				":_id"    => $data->id->value,
				":_email" => strtolower($data->email->value)
			];
			
			$this->db->bindArray($params);
			$row = $this->db->single();
			
			return empty($row) ? false : true;
		}
		
		# get user by id
		public function getUserById($id) {
			$this->db->query("CALL spGetUserById(:_id)");
			$this->db->bind(":_id", $id);
			
			return $this->db->single();
		}
		
		# register user
		public function register($data) {
			$this->db->query("CALL spRegisterUser(:_name, :_email, :_password, :_role)");
			
			$params = [
				":_name"     => $data->name->value, 
				":_email"    => strtolower($data->email->value), 
				":_password" => password_hash($data->password->value, PASSWORD_DEFAULT),
				":_role"     => $data->role->value
			];
			
			$this->db->bindArray($params);
			
			return $this->db->execute();
		}
		
		# login user
		public function login($data) {
			$this->db->query("CALL spFindUserByEmail(:_email)");
			$this->db->bind(":_email", strtolower($data->email->value));
			$row = $this->db->single();
			
			if (password_verify($data->password->value, $row->password)) {
				return $row;
			}
			else {
				return false;
			}
		}
		
		public function updateUser($data) {
			$this->db->query("CALL spUpdateUser(:_id, :_name, :_email, :_password)");
			
			$params = [
				":_id"       => $data->id->value,
				":_name"     => $data->name->value, 
				":_email"    => strtolower($data->email->value), 
				":_password" => strlen($data->password->value) > 0 ? password_hash($data->password->value, PASSWORD_DEFAULT) : null
			];
			
			$this->db->bindArray($params);
			
			return $this->db->execute();
		}
	}
?>