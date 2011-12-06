<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011
		* @license		http://www.gnu.org/licenses/gpl.html GNU GPL V3
		* @package		Lynxpress
		*
		* This file is part of Lynxpress.
		*
		*   Lynxpress is free software: you can redistribute it and/or modify
		*   it under the terms of the GNU General Public License as published by
		*   the Free Software Foundation, either version 3 of the License, or
		*   (at your option) any later version.
		*
		*   Lynxpress is distributed in the hope that it will be useful,
		*   but WITHOUT ANY WARRANTY; without even the implied warranty of
		*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		*   GNU General Public License for more details.
		*
		*   You should have received a copy of the GNU General Public License
		*   along with Lynxpress.  If not, see http://www.gnu.org/licenses/.
	*/
	
	namespace Library\Models;
	use \Admin\Helper\Helper as Helper;
	use Exception;
	
	require_once 'class.master.library.php';
	
	/**
		* User
		*
		* It represents an item of the associated database table
		*
		* @package		Library
		* @subpackage	Models
		* @namespace	Models
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class User extends Model implements ModelInterface{
	
		private $_id = null;
		private $_username = null;
		private $_nickname = null;
		private $_firstname = null;
		private $_lastname = null;
		private $_publicname = null;
		private $_password = null;
		private $_email = null;
		private $_website = null;
		private $_msn = null;
		private $_twitter = null;
		private $_facebook = null;
		private $_google = null;
		private $_avatar = null;
		private $_bio = null;
		private $_role = null;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	integer [$id] User id (optional)
		*/
		
		public function __construct($id = false){
		
			parent::__construct();
			
			$this->_sql_table = 'user';
			
			if($id !== false){
			
				$this->_id = $id;
				$this->load();
			
			}
		
		}
		
		/**
			* Load method read a set of attributes at a time
			*
			* @access	public
		*/
		
		public function load(){
		
			try{
			
				$this->read('_username');
				$this->read('_nickname');
				$this->read('_firstname');
				$this->read('_lastname');
				$this->read('_publicname');
				$this->read('_email');
				$this->read('_website');
				$this->read('_msn');
				$this->read('_twitter');
				$this->read('_facebook');
				$this->read('_google');
				$this->read('_avatar');
				$this->read('_bio');
				$this->read('_role');
			
			}catch(Exception $e){
			
				throw new Exception(__CLASS__.' can\'t load because '.$e->getMessage());
			
			}
		
		}
		
		/**
			* Create method to add a row in user table
			*
			* After creation success, the id of the row is inserted in id attribute
			*
			* @access	public
		*/
		
		public function create(){
		
			$to_create['table'] = $this->_sql_table;
			$to_create['columns'] = array(':name' => 'user_username', 
										  ':nname' => 'user_nickname', 
										  ':fname' => 'user_firstname', 
										  ':lname' => 'user_lastname', 
										  ':pname' => 'user_publicname', 
										  ':pwd' => 'user_password', 
										  ':mail' => 'user_email', 
										  ':web' => 'user_website', 
										  ':msn' => 'user_msn', 
										  ':tweet' => 'user_twitter', 
										  ':fb' => 'user_facebook', 
										  ':gg' => 'user_google', 
										  ':av' => 'user_avatar',
										  ':bio' => 'user_bio', 
										  ':role' => 'user_role');
			$to_create['values'] = array(':name' => $this->_username, 
										 ':nname' => $this->_nickname, 
										 ':fname' => $this->_firstname, 
										 ':lname' => $this->_lastname, 
										 ':pname' => $this->_publicname,
										 ':pwd' => Helper::make_password($this->_username, $this->_password),
										 ':mail' => $this->_email,
										 ':web' => $this->_website,
										 ':msn' => $this->_msn,
										 ':tweet' => $this->_twitter,
										 ':fb' => $this->_facebook,
										 ':gg' => $this->_google,
										 ':av' => $this->_avatar,
										 ':bio' => $this->_bio,
										 ':role' => $this->_role);
			$to_create['types'] = array(':name' => 'str',
										':nname' => 'str',
										':fname' => 'str',
										':lname' => 'str',
										':pname' => 'str',
										':pwd' => 'str',
										':mail' => 'str',
										':web' => 'str',
										':msn' => 'str',
										':tweet' => 'str',
										':fb' => 'str',
										':gg' => 'str',
										':av' => 'int',
										':bio' => 'str',
										':role' => 'str');
			
			$is_int = $this->_db->create($to_create);
			
			if(is_int($is_int)){
			
				$this->_id = $is_int;
				$this->_result_action = true;
			
			}else{
			
				throw new Exception('There\'s a problem creating your '.__CLASS__);
			
			}
		
		}
		
		/**
			* Read an attribute via a given id
			*
			* @access	public
			* @param	string [$attr] User attribute
		*/
		
		public function read($attr){
		
			$this->$attr = parent::m_read($this->_id, $attr);
		
		}
		
		/**
			* Update the item via its id
			*
			* @access	public
			* @param	string [$attr] User attribute
			* @param	string [$type] User attribute data type
		*/
		
		public function update($attr, $type){
		
			parent::m_update($this->_id, $attr, $type);
		
		}
		
		/**
			* Delete the item in the database
			*
			* @access	public
		*/
		
		public function delete(){
		
			parent::m_delete($this->_id);
		
		}
		
		/**
			* Method to check if data passed via __set method are good for the object
			*
			* @access	private
			* @param	string [$attr] User attribute
			* @param	mixed [$value] User attribute value
			* @return	mixed true if no errors, otherwise return an error string
		*/
		
		private function check_data($attr, $value){
		
			switch($attr){
			
				case '_username':
					if(empty($value))
						$error = 'Username missing';
					elseif(strlen($value) > 20)
						$error = 'Username too long';
					break;
				
				case '_nickname':
					if(empty($value))
						$error = 'Nickname missing';
					elseif(strlen($value) > 20)
						$error = 'Nickname too long';
					break;
				
				case '_password':
					if(empty($value))
						$error = 'Empty password';
					break;
				
				case '_email':
					if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , $value))
						$error = 'Invalid e-mail';
					elseif(empty($value))
						$error = 'E-mail missing';
					break;
				
				case '_website':
					if(!empty($value) && substr($value, 0, 7) != 'http://')
						$error = 'Website url has to begin with "http://"';
					break;
				
				case '_msn':
					if(!empty($value) && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , $value))
						$error = 'Invalid msn address';
					break;
				
				case '_twitter':
					if(!empty($value) && substr($value, 0, 7) != 'http://')
						$error = 'Twitter url has to begin with "http://"';
					break;
				
				case '_facebook':
					if(!empty($value) && substr($value, 0, 7) != 'http://')
						$error = 'Facebook url has to begin with "http://"';
					break;
				
				case '_google':
					if(!empty($value) && substr($value, 0, 7) != 'http://')
						$error = 'Google+ url has to begin with "http://"';
					break;
			
			}
			
			if(isset($error))
				return $error;
			else
				return true;
		
		}
		
		/**
			* Set method to update an attribute value in the object
			*
			* @access	public
			* @param	string [$attr] User attribute
			* @param	mixed [$value] User attribute value
			* @return	mixed true if no errors, otherwise return an error string
		*/
		
		public function __set($attr, $value){
		
			$checked = $this->check_data($attr, $value);
			
			if($checked === true){
			
				$this->$attr = stripslashes($value);
				return true;
			
			}else{
			
				return $checked;	//contain the error message
			
			}
		
		}
		
		/**
			* Get method to return an object attribute value
			*
			* @access	public
			* @param	string [$attr] User attribute
		*/
		
		public function __get($attr){
		
			if(isset($this->$attr))
				return $this->$attr;
			else
				return false;
		
		}
	
	}

?>