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
	
	namespace Admin\Users;
	use \Admin\Master\Master as Master;
	use Exception;
	use \Library\Models\User as User;
	use \Admin\Roles\Roles as Roles;
	use \Library\Variable\Post as VPost;
	use \Admin\Settings\Html as HtmlSettings;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Admin\Session\Session as Session;
	use \Library\Mail\Mail as Mail;
	
	/**
		* Add User
		*
		* Handles creation of a new user
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Users
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Add extends Master{
	
		private $_new_user = null;
		private $_roles = null;
	
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Add New User';
			
			if($this->_user['settings']){
			
				$this->_new_user = new User();
				
				$roles =& Roles::load();
				$this->_roles = $roles->_roles;
			
				if(VPost::new_user(false))
					$this->create();
			
			}
		
		}
		
		/**
			* Display related admin parts link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_user['settings'])
				Html::nu_menu();
			else
				HtmlSettings::menu();
		
		}
		
		/**
			* Display form to add a new user
			*
			* @access	private
		*/
		
		private function display_form(){
		
			Html::nu_form('o', $this->_new_user->_username, $this->_new_user->_email, $this->_new_user->_firstname, $this->_new_user->_lastname, $this->_new_user->_website);
									
			foreach($this->_roles as $role)
				Html::option($role, ucfirst($role));
									
			Html::nu_form('c');
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			if($this->_user['settings']){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', '#');
				
				$this->display_form();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Set passed data into the user object and return errors if data doesn't fit
			*
			* @access	private
			* @return	boolean
		*/
		
		private function check_post_data(){
		
			$results = array();
			$errors = array();
			
			array_push($results, $this->_new_user->__set('_username', VPost::username()));
			array_push($results, $this->_new_user->__set('_nickname', VPost::username()));
			array_push($results, $this->_new_user->__set('_publicname', VPost::username()));
			array_push($results, $this->_new_user->__set('_email', VPost::email()));
			array_push($results, $this->_new_user->__set('_firstname', VPost::firstname()));
			array_push($results, $this->_new_user->__set('_lastname', VPost::lastname()));
			
			if(VPost::website())
				array_push($results, $this->_new_user->__set('_website', VPost::website()));
			
			array_push($results, $this->_new_user->__set('_role', VPost::role()));
			
			if(VPost::pwd() == VPost::re_pwd())
				array_push($results, $this->_new_user->__set('_password', VPost::pwd()));
			else
				array_push($results, 'Passwords doesn\'t match');
			
			foreach($results as $result)
				if($result !== true)
					array_push($errors, '<li>- '.$result.'</li>');
			
			if(!empty($errors)){
			
				$error_msg = 'Check your informations:<br/><ul>'.implode('', $errors).'</ul>';
				$this->_action_msg = ActionMessages::custom_wrong($error_msg);
				return false;
			
			}else{
			
				return true;
			
			}
		
		}
		
		/**
			* Create a new user
			*
			* @access	private
		*/
		
		private function create(){
		
			if($this->check_post_data()){
					
				try{
					
					$this->_new_user->create();
					
					Session::monitor_activity('added a new member: '.$this->_new_user->_username);
					
					if($this->_new_user->_result_action === true && VPost::send_pwd(false)){
					
						$to = $this->_new_user->_email;
						$subject = 'Your password for '.WS_NAME;
						$message = 'This is your password: '.$this->_new_user->_password;
						
						$mail = new Mail($this->_new_user->_email, $subject, $message);
						$mail->send();
						
						header('Location: index.php?ns=users&ctl=manage');
					
					}elseif($this->_new_user->_result_action === true){
					
						header('Location: index.php?ns=users&ctl=manage');
					
					}
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
				
			}
		
		}
	
	}

?>