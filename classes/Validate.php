<?php
	class Validate {
		public static function isValid($obj) {
			foreach($obj as $key => $childObj) {
				if (strlen($childObj->error) > 0) {
					return false;
				}
			}
			
			return true;
		}
		
		public static function getFirstError($obj) {
			foreach($obj as $key => $childObj) {
				if (strlen($childObj->error) > 0) {
					return $key;
				}
			}
			
			return false;
		}
		
		public static function isNotEmpty($obj, $msg) {	
			$test = strlen(strval($obj->value)) === 0;
			
			return self::toggleError($obj, $test, $msg);
		}
		
		public static function isNumeric($obj, $msg) {
			$test = !is_numeric($obj->value);
			
			return self::toggleError($obj, $test, $msg);
		}
		
		public static function isOnlyDigits($obj, $msg) {
			$test = !ctype_digit($obj->value);
			
			return self::toggleError($obj, $test, $msg);
		}
		
		public static function isGreaterThan($obj, $min, $msg) {
			$test = intval($obj->value) <= $min;
			
			return self::toggleError($obj, $test, $msg);
		}
		
		public static function isMinLength($obj, $len, $msg) {
			$test = strlen($obj->value) < $len;
			
			return self::toggleError($obj, $test, $msg);
		}
		
		public static function isValidEmail($obj, $msg) {
			$test = !filter_var($obj->value, FILTER_VALIDATE_EMAIL);
			
			return self::toggleError($obj, $test, $msg);
		}
		
		public static function doMatch($obj1, $obj2, $msg) {
			$test = $obj1->value != $obj2->value;
			
			return self::toggleError($obj2, $test, $msg);
		}
		
		public static function checkImageFile($obj, $targetDir) {
			$errors_file   = "";

			// Check if image file is a actual image or fake image
			if (isset($_FILES['file'])) {
				$targetFile    = $targetDir . basename($_FILES['file']["name"]);
				$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

				$check = getimagesize($_FILES['file']["tmp_name"]);

				if ($check !== false) {			
					if ($_FILES['file']["size"] > 512000) {
						$errors_file = "File size cannot exceed 500KB.";
					}
					else {
						if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {
							$errors_file = "Only JPG, JPEG, and PNG files are allowed.";
						}
					}
				} 
				else {
					$errors_file = $_FILES['file']["name"] . " is not an image.";
				}
			}
			else {
				$errors_file = "Please select an image";
			}
			
			return self::toggleError($obj, strlen($errors_file) > 0, $errors_file);
		}
		
		public static function toggleError($obj, $bln, $msg) {
			if ($bln) {
				$obj->error = $msg;
				return false;
			}
			else {
				$obj->error = "";
				return true;
			}
		}
		
		public static function setProperties($props, $arr=[]) {
			$obj  = new stdClass();
			
			foreach($props as $prop) {
				$obj->$prop = new stdClass();
				$obj->$prop->value = array_key_exists($prop, $arr) ? trim($arr[$prop]) : "";
				$obj->$prop->error = "";
			}
			
			return $obj;
		}
		
		public static function getErrors($data) {
			$errors = new stdClass();

			foreach($data as $key => $value) {
				if ($value->error) {
					$errors->$key = $value->error;
				}
			}

			return $errors;
		}
	}
?>