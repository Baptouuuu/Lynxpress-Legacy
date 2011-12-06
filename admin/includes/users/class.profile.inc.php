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
	use \Library\Models\Media as Media;
	use \Admin\Roles\Roles as Roles;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Get as VGet;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Admin\Helper\Helper as Helper;
	
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

?>