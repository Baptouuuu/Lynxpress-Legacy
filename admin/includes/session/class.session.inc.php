<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011
		* @license		http://www.gnu.org/licenses/gpl.html GNU GPL V3
		* @package		Lynxpress
		* @subpackage	Administration
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
	
	namespace Admin\Session;
	use \Library\Database\Database as Database;
	use \Admin\Roles\Roles as Roles;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Session as VSession;
	use \Admin\Helper\Helper as Helper;
	
	/**
		* Session
		*
		* Handles user session
		*
		* Permits to check user credentials when accessing a page
		*
		* And retrieve user permissions from the Roles class
		*
		* @package	Administration
		* @author	Baptiste Langlade lynxpressorg@gmail.com
		* @version	1.0
		* @final
	*/
	
	final class Session{
	
		private $_db = null;
		private $_verified = null;
		private $_roles = null;
	
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			$this->_db =& Database::load();
			$this->_roles =& Roles::load();
			session_start();
		
		}
		
		/**
			* Log in the user if credentials are correct
			*
			* @access	public
		*/
		
		public function login(){
		
			$to_read['table'] = 'user';
			$to_read['columns'] = array('USER_ID', 'user_username', 'user_password');
			$to_read['condition_columns'][':name'] = 'user_username';
			$to_read['condition_types'][':name'] = 'AND';
			$to_read['condition_select_types'][':name'] = '=';
			$to_read['condition_values'][':name'] = VPost::login();
			$to_read['value_types'][':name'] = 'str';
			
			$user = $this->_db->read($to_read);
			
			if($user === false || empty($user)){
			
				header('Location: login.php');
			
			}else{
			
				if($user[0]['user_username'] == VPost::login() && $user[0]['user_password'] == Helper::make_password(VPost::login(), VPost::password())){
			
					$_SESSION['username'] = $user[0]['user_username'];
					$_SESSION['user_id'] = $user[0]['USER_ID'];
					
					header('Location: index.php');
			
				}else{
			
					header('Location: login.php');
			
				}
			
			}
		
		}
		
		/**
			* Logout a user
			*
			* @access	public
		*/
		
		public function logout(){
		
			session_destroy();
			header('Location: login.php');
		
		}
		
		/**
			* Check if the session is correct, else logout the user
			*
			* @access	public
		*/
		
		public function verify_session(){
		
			if(VSession::user_id()){
				
				$to_read['table'] = 'user';
				$to_read['columns'] = array('user_username');
				$to_read['condition_columns'][':id'] = 'USER_ID';
				$to_read['condition_types'][':id'] = 'AND';
				$to_read['condition_select_types'][':id'] = '=';
				$to_read['condition_values'][':id'] = VSession::user_id();
				$to_read['value_types'][':id'] = 'int';
				
				$user = $this->_db->read($to_read);
				
				if($user === false || empty($user)){
				
					session_destroy();
					header('Location: login.php');
				
				}else{
				
					if($user[0]['user_username'] != VSession::username()){
				
						session_destroy();
						header('Location: login.php');
				
					}else{
				
						$this->_verified = true;
				
					}
				
				}
		
			}else{
		
				session_destroy();
				header('Location: login.php');
		
			}
		
		}
		
		/**
			* If session is correct, retrieve user permissions
			*
			* @access	public
			* @return	mixed Authorization array if the user role exists, otherwise it returns false
		*/
		
		public function verify_permission(){
		
			if($this->_verified){
			
				$to_read['table'] = 'user';
				$to_read['columns'] = array('USER_ID', 'user_username', 'user_email', 'user_role');
				$to_read['condition_columns'][':id'] = 'USER_ID';
				$to_read['condition_types'][':id'] = 'AND';
				$to_read['condition_select_types'][':id'] = '=';
				$to_read['condition_values'][':id'] = VSession::user_id();
				$to_read['value_types'][':id'] = 'int';
				
				$user = $this->_db->read($to_read);
				
				if(in_array($user[0]['user_role'], $this->_roles->_roles)){
				
					$role = '_'.$user[0]['user_role'];
					
					$infos = $this->_roles->$role;
					
					$infos['user_id'] = $user[0]['USER_ID'];
					$infos['user_username'] = $user[0]['user_username'];
					$infos['user_email'] = $user[0]['user_email'];
					$infos['user_role'] = $user[0]['user_role'];
					
					return $infos;
				
				}else{
				
					return false;
				
				}
			
			}
		
		}
		
		/**
			* Log an action into database, logs are viewed by administrator in the dashboard
			*
			* @static
			* @access	public
			* @param	string [$msg] Action message to log
		*/
		
		public static function monitor_activity($msg){
		
			$db =& Database::load();
			
			$to_create['table'] = 'activity';
			$to_create['columns'] = array(':id' => 'USER_ID', ':data' => 'data', ':date' => 'date');
			$to_create['values'] = array(':id' => VSession::user_id(), ':data' => $msg, ':date' => date('Y-m-d H:i:s'));
			$to_create['types'] = array(':id' => 'int', ':data' => 'str', ':date' => 'str');
			
			$db->create($to_create);
		
		}
	
	}

?>