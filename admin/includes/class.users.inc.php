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
	use \Admin\Master as Master;
	use Exception;
	use \Library\Models\User as User;
	use \Library\Models\Media as Media;
	use \Admin\Roles\Roles as Roles;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Request as VRequest;
	use \Library\Variable\Session as Vsession;
	use \Admin\Settings\Html as HtmlSettings;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Admin\Session as Session;
	use \Admin\Helper\Helper as Helper;
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
	
	/**
		* Profile User
		*
		* Handles profile administration
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Users
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Profile extends Master{
	
		private $_profile = null;
		private $_pictures = null;
		private $_roles = null;
	
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			if(VGet::id() && $this->_user['settings']){
			
				$this->_title = 'Edit User';
				$this->_roles =& Roles::load();
				$this->_roles = $this->_roles->_roles;
			
			}else{
			
				$this->_title = 'Profile';
			
			}
			
			$this->get_user();
			$this->get_pictures();
			
			if(VPost::update_profile(false))
				$this->update();
		
		}
		
		/**
			* Retrieve all user data
			*
			* If the user is not a valid one, an empty user object is created
			*
			* to avoid problems calling html elements
			*
			* @access	private
		*/
		
		private function get_user(){
		
			try{
				
				if(VGet::id() && $this->_user['settings'])
					$this->_profile = new User(VGet::id());
				else
					$this->_profile = new User($this->_user['user_id']);
				
				$this->build_avatar();
				
			}catch(Exception $e){
				
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				$this->_profile = new User();
				$this->_profile->_id = $this->_user['user_id'];
			
			}
		
		}
		
		/**
			* Transform user avatar in permalink
			*
			* @access	private
		*/
		
		private function build_avatar(){
		
			$a = $this->_profile->_avatar;
			
			if(!empty($a)){
			
				try{
				
					$m = new Media($this->_profile->_avatar);
					$m->read('_permalink');
					
					$dirname = dirname($m->_permalink).'/';
					$filename = basename($m->_permalink);
					
					$this->_profile->_avatar = $dirname.'150-'.$filename;
				
				}catch(Exception $e){
				
					$this->_profile->_avatar = 0;
				
				}
			
			}
		
		}
		
		/**
			* Retrieve all pictures
			*
			* @access	private
		*/
		
		private function get_pictures(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('MEDIA_ID');
				$to_read['condition_columns'][':t'] = 'media_type';
				$to_read['condition_select_types'][':t'] = 'LIKE';
				$to_read['condition_values'][':t'] = 'image/%';
				$to_read['value_types'][':t'] = 'str';
				
				$this->_pictures = $this->_db->read($to_read);
				
				if(!empty($this->_pictures)){
				
					foreach ($this->_pictures as &$m) {
					
						$m = new Media($m['MEDIA_ID']);
						
						$dirname = dirname($m->_permalink).'/';
						$filename = basename($m->_permalink);
						
						$m->_permalink = $dirname.'150-'.$filename;
					
					}
				
				}else{
				
					$this->_pictures = array();
				
				}
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage);
			
			}
		
		}
		
		/**
			* Display related admin parts link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_user['settings'] && $this->_title == 'Edit User')
				Html::up_menu(true);
			else
				Html::up_menu();
		
		}
		
		/**
			* Display form in relation with user name
			*
			* @access	private
		*/
		
		private function display_name(){
		
			Html::up_name($this->_profile->_username, $this->_profile->_firstname, $this->_profile->_lastname, $this->_profile->_nickname, $this->display_public_name(), $this->display_select_role());
		
		}
		
		/**
			* Display form for avatar
			*
			* @access	private
		*/
		
		private function display_avatar(){
		
			Html::up_avatar('o', $this->_profile->_avatar);
			
			foreach ($this->_pictures as $m)
				Html::up_fig($m->_id, $m->_permalink, $this->_profile->_avatar);
			
			Html::up_avatar('c');
		
		}
		
		/**
			* Display form about additional informations of the user
			*
			* @access	private
		*/
		
		private function display_contact_info(){
		
			Html::up_contact($this->_profile->_email, $this->_profile->_website, $this->_profile->_msn, $this->_profile->_twitter, $this->_profile->_facebook, $this->_profile->_google);
		
		}
		
		/**
			* Display form to change password and write a little biography
			*
			* @access	private
		*/
		
		private function display_about(){
		
			Html::up_about($this->_profile->_bio);
		
		}
		
		/**
			* Build all available possibilities for a public name
			*
			* @access	private
			* @return	string
		*/
		
		private function display_public_name(){
		
			//need to do that because __get() method return a fatal error in empty() condition
			$fname = $this->_profile->_firstname;
			$lname = $this->_profile->_lastname;
			
			$options = '<option>'.$this->_profile->_username.'</option>';
			
			if($this->_profile->_nickname != $this->_profile->_username)
				$options .= Html::up_option($this->_profile->_publicname, $this->_profile->_nickname);
			
			if(!empty($fname))
				$options .= Html::up_option($this->_profile->_publicname, $this->_profile->_firstname);
						
			if(!empty($lname))
				$options .= Html::up_option($this->_publicname, $this->_profile->_lastname);
			
			if(!empty($fname) && !empty($lname)){
				$options .= Html::up_option($this->_profile->_publicname, $fname.' '.$lname);
				$options .= Html::up_option($this->_profile->_publicname, $lname.' '.$fname);
			}
			
			return '<select id="public_name" name="public_name">'.$options.'</select>';
		
		}
		
		/**
			* Display a dropdown menu to choose user role
			*
			* @access	private
			* @return	mixed
		*/
		
		private function display_select_role(){
		
			$id = $this->_profile->_id;
			
			if($this->_user['settings'] && $this->_user['user_id'] != $id){
				
				$select_role = '<tr>'.
									'<th>'.
										'<label for="role">Role</label>'.
									'</th>'.
									'<td>'.
										'<select id="role" name="role">';
											
											foreach($this->_roles as $role)
												$select_role .= '<option '.(($this->_profile->_role == $role)?'selected':'').' value="'.$role.'">'.ucwords($role).'</option>';
											
				$select_role .=			'</select>'.
									'</td>'.
								'</tr>';
			
				return $select_role;
			
			}else{
			
				return null;
			
			}
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			echo $this->_action_msg;
			
			Html::form('o', 'post', '#');
			
			$this->display_name();
			$this->display_avatar();
			$this->display_contact_info();
			$this->display_about();
			
			echo 	'<br/>';
			
			Html::up_update();
			
			Html::form('c');
		
		}
		
		/**
			* Set data in user object and returns errors if data doesn't fit
			*
			* @access	private
			* @return	boolean
		*/
		
		private function check_post_data(){
		
			$results = array();
			$errors = array();
			
			array_push($results, $this->_profile->__set('_firstname', VPost::firstname()));
			array_push($results, $this->_profile->__set('_lastname', VPost::lastname()));
			array_push($results, $this->_profile->__set('_nickname', VPost::nickname()));
			array_push($results, $this->_profile->__set('_publicname', VPost::public_name()));
			
			if(VPost::role(false))		//don't set when update own profile
				array_push($results, $this->_profile->__set('_role', VPost::role()));
			
			array_push($results, $this->_profile->__set('_email', VPost::email()));
			array_push($results, $this->_profile->__set('_website', VPost::website()));
			array_push($results, $this->_profile->__set('_msn', VPost::msn()));
			array_push($results, $this->_profile->__set('_twitter', VPost::twitter()));
			array_push($results, $this->_profile->__set('_facebook', VPost::fb()));
			array_push($results, $this->_profile->__set('_google', VPost::google()));
			array_push($results, $this->_profile->__set('_avatar', VPost::avatar()));
			array_push($results, $this->_profile->__set('_bio', VPost::bio()));
			
			if(VPost::new_pwd(false) && VPost::new_pwd() == VPost::re_new_pwd())
				array_push($results, $this->_profile->__set('_password', Helper::make_password($this->_profile->_username, VPost::new_pwd())));
			elseif(VPost::new_pwd(false) && VPost::new_pwd() != VPost::re_new_pwd())
				array_push($results, 'Passwords does\'t match');
			
			foreach($results as $result)
				if($result !== true)	//so it contains an error message
					array_push($errors, '<li>- '.$result.'<li>');
			
			if(!empty($errors)){
			
				$error_msg = 'Check your informations:<br/><ul>'.implode('', $errors).'</ul>';
				$this->_action_msg = ActionMessages::custom_wrong($error_msg);
				return false;
			
			}else{
			
				return true;
			
			}
		
		}
		
		/**
			* Update user data
			*
			* @access	private
		*/
		
		private function update(){
		
			if($this->check_post_data()){
					
				try{
					
					$this->_profile->update('_firstname', 'str');
					$this->_profile->update('_lastname', 'str');
					$this->_profile->update('_nickname', 'str');
					$this->_profile->update('_publicname', 'str');
					$this->_profile->update('_email', 'str');
					$this->_profile->update('_website', 'str');
					$this->_profile->update('_msn', 'str');
					$this->_profile->update('_twitter', 'str');
					$this->_profile->update('_facebook', 'str');
					$this->_profile->update('_google', 'str');
					$this->_profile->update('_avatar', 'int');
					$this->_profile->update('_bio', 'str');
					
					if(VPost::role(false))
						$this->_profile->update('_role', 'str');
					
					$pwd = $this->_profile->_password;
					if(!empty($pwd))
						$this->_profile->update('_password', 'str');
					
					$this->build_avatar();
					
					$this->_action_msg = ActionMessages::profile_update(true);
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::profile_update(ucfirst($e->getMessage()));
				
				}
			
			}
		
		}
	
	}
	
	/**
		* Manage
		*
		* Handles users administration
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Users
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		private $_content = null;
		private $_role = null;
		private $_roles = null;
		private $_search = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_roles =& Roles::load();
			$this->_roles = $this->_roles->_roles;
			
			if(VPost::search_button(false))
				$this->_search = trim(VPost::search('foo'));
			
			if(VRequest::role() && in_array(VRequest::role(), $this->_roles))
				$this->_role = VRequest::role();
			else
				$this->_role = 'all';
			
			$this->build_title();
			
			if($this->_user['settings']){
			
				$this->update();
				$this->delete();
				
				$this->get_users();
			
			}
		
		}
		
		/**
			* Retrieve wanted users
			*
			* @access	private
		*/
		
		private function get_users(){
		
			try{
			
				$to_read['table'] = 'user';
				$to_read['columns'] = array('USER_ID');
				
				if(VPost::search_button(false)){
					
					$to_read['condition_columns'][':uname'] = 'user_username';
					$to_read['condition_select_types'][':uname'] = 'LIKE';
					$to_read['condition_values'][':uname'] = '%'.$this->_search.'%';
					$to_read['value_types'][':uname'] = 'str';
					$to_read['condition_types'][':fname'] = 'OR';
					$to_read['condition_columns'][':fname'] = 'user_firstname';
					$to_read['condition_select_types'][':fname'] = 'LIKE';
					$to_read['condition_values'][':fname'] = '%'.$this->_search.'%';
					$to_read['value_types'][':fname'] = 'str';
					$to_read['condition_types'][':lname'] = 'OR';
					$to_read['condition_columns'][':lname'] = 'user_lastname';
					$to_read['condition_select_types'][':lname'] = 'LIKE';
					$to_read['condition_values'][':lname'] = '%'.$this->_search.'%';
					$to_read['value_types'][':lname'] = 'str';
					$to_read['condition_types'][':pname'] = 'OR';
					$to_read['condition_columns'][':pname'] = 'user_publicname';
					$to_read['condition_select_types'][':pname'] = 'LIKE';
					$to_read['condition_values'][':pname'] = '%'.$this->_search.'%';
					$to_read['value_types'][':pname'] = 'str';
				
				}elseif($this->_role != 'all'){
					
					$to_read['condition_columns'][':role'] = 'user_role';
					$to_read['condition_select_types'][':role'] = '=';
					$to_read['condition_values'][':role'] = $this->_role;
					$to_read['value_types'][':role'] = 'str';
				
				}
				
				$this->_content = $this->_db->read($to_read);
				
				if(!empty($this->_content)){
					
					foreach($this->_content as $key => &$value){
					
						$id = $value['USER_ID'];
						$value = new User();
						$value->_id = $id;
						$value->read('_username');
						$value->read('_publicname');
						$value->read('_email');
						$value->read('_avatar');
						$value->read('_role');
						
						$a = $value->_avatar;
						
						if(!empty($a)){
						
							try{
							
								$m = new Media();
								$m->_id = $value->_avatar;
								$m->read('_permalink');
								
								$dirname = dirname($m->_permalink).'/';
								$filename = basename($m->_permalink);
								
								$value->_avatar = $dirname.'150-'.$filename;
							
							}catch(Exception $e){
							
								$value->_avatar = 0;
							
							}
						
						}
					
					}
				
				}
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Build page title
			*
			* @access	private
		*/
		
		private function build_title(){
		
			if(VPost::search_button(false))
				$this->_title = 'Users > Search results for "'.$this->_search.'"';
			elseif($this->_role != 'all')
				$this->_title = ucfirst($this->_role).' Users';
			else
				$this->_title = 'Users';
		
		}
		
		/**
			* Display related admin parts link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_user['settings'])
				Html::mu_menu(true);
			else
				Html::mu_menu();
		
		}
		
		/**
			* Display menu to choose which user roles to display
			*
			* @access	private
		*/
		
		private function display_users_roles(){
		
			$to_read['table'] = 'user';
			$to_read['columns'] = array('user_role');
			
			$roles = $this->_db->read($to_read);
			
			$current_all = false;
			if($this->_role == 'all')
				$current_all = true;
			
			Html::mu_role_menu('o');
			Html::mu_role_menu_link('all', count($roles), $current_all);
			
			foreach ($this->_roles as $role) {
			
				$count = 0;
				$current = false;
				
				foreach ($roles as $user)
					if($role == $user['user_role'])
						$count++;
				
				if($role == $this->_role)
					$current = true;
				
				if($count !== 0)
					Html::mu_role_menu_link($role, $count, $current);
			
			}
			
			Html::mu_role_menu('c');
		
		}
		
		/**
			* Display applicable actions buttons
			*
			* @access	private
		*/
		
		private function display_actions(){
		
			Html::mu_actions('o');
				
			foreach ($this->_roles as $value)
				Html::option($value, ucfirst($value));
				
			Html::mu_actions('c', $this->_role);
		
		}
		
		/**
			* Display retrieve users
			*
			* @access	private
		*/
		
		private function display_users(){
		
			echo '<div id="labels">';
			
			foreach($this->_content as $user){
			
				$edit = null;
				
				if($user->_id != $this->_user['user_id'])
					$edit = '&id='.$user->_id;
				
				Html::label($user->_id, $edit, $user->_username, $user->_publicname, $user->_role, $user->_email, $user->_avatar);
					 	
			}
			
			echo '</div>';
		
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
				
				Html::form('o', 'post', 'index.php?ns=users&ctl=manage');
				
				$this->display_users_roles();
				$this->display_actions();
				
				$this->display_users();
				
				echo Helper::datalist('titles', $this->_content, '_publicname');
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update selected users
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::change(false) && in_array(VPost::change_role(), $this->_roles)){
				
				$results = array();
				
				foreach(VPost::user_id(array()) as $id){
						
					try{
					
						if($id == VSession::user_id())
							throw new Exception('Can\'t modify your own account role');
						
						$user = new User();
						$user->_id = $id;
						$user->_role = VPost::change_role();
						$user->update('_role', 'str');
						unset($user);
					
						array_push($results, true);
					
					}catch(Exception $e){
					
						array_push($results, false);
					
					}
					
				}
				
				$this->_action_msg = ActionMessages::change_role($results);
			
			}
		
		}
		
		/**
			* Delete selected users
			*
			* @access	private
		*/
		
		private function delete(){
		
			if((VPost::delete(false) && VPost::user_id()) && $this->_user['delete_content']){
			
				$results = array();
				
				foreach(VPost::user_id(array()) as $id){
			
					if($id != $this->_user['user_id']){
							
						try{
							
							$user = new User();
							$user->_id = $id;
							$user->delete();
							unset($user);
							
							array_push($results, true);
						
						}catch(Exception $e){
						
							array_push($results, false);
						
						}
						
					}
				
				}
				
				$this->_action_msg = ActionMessages::delete_profile($results);
				
			}elseif(VPost::delete(false) && $this->_user['delete_content'] === false){
			
				$this->_action_msg = ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>