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
	
	namespace Admin\Roles;
	use \Admin\Master as Master;
	use Exception;
	use \Library\Database\Database as Database;
	use \Library\Models\Setting as Setting;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Get as VGet;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	
	/**
		* Roles
		*
		* Stocks user roles and their authorizations
		*
		* An authorization array is built as follows:
		* <code>
		* array(
		*	'dashboard' => boolean,
		*	'post' => boolean,
		* 	'media' => boolean,
		* 	'album_photo' => boolean,
		* 	'comments' => boolean,
		* 	'delete_content' => boolean,
		*	'settings' => boolean
		* )
		* </code>
		* Authorization array is mainly used with _user attributes in controllers
		*
		* in order to know if the user can manage an administration page
		*
		* @package		Administration
		* @namespace	Roles
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Roles{
	
		private $_roles_auth = array();
		private $_roles = array();			//need this to make easy to check if a user has an existing role
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			$this->get_content();
			$this->initialize();
		
		}
		
		/**
			* Method to get an instance of the roles object
			*
			* @static
			* @access	public
			* @return	object
		*/
		
		public static function &load(){
		
			static $inst;
			
			if(!is_object($inst))
				$inst = new Roles();
			
			return $inst;
		
		}
		
		/**
			* Retrieve stored roles
			*
			* @access	private
		*/
		
		private function get_content(){
		
			try{
			
				$db =& Database::load();
				
				//retrieve array of all roles
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('setting_data');
				$to_read['condition_columns'][':t'] = 'setting_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'all_roles';
				$to_read['value_types'][':t'] = 'str';
				
				$all_roles = $db->read($to_read);
				
				if(!empty($all_roles))
					$this->_roles = json_decode($all_roles[0]['setting_data'], true);
				else
					$this->_roles = array();
				//end first retrieve
				
				$to_read = null;
				
				//retrieve each role with its authorizations
				if(!empty($this->_roles)){
				
					$to_read['table'] = 'setting';
					$to_read['columns'] = array('SETTING_ID');
					$to_read['condition_columns'][':t'] = 'setting_type';
					$to_read['condition_select_types'][':t'] = '=';
					$to_read['condition_values'][':t'] = 'role';
					$to_read['value_types'][':t'] = 'str';
					
					$roles = $db->read($to_read);
					
					foreach ($roles as $value) {
					
						$role = new Setting($value['SETTING_ID']);
						$name = $role->_name;
						
						$this->_roles_auth["_$name"] = json_decode($role->_data, true);
					
					}
				
				}
				//end second retrieve
			
			}catch(Exception $e){
			
				$this->_roles = array();
				$this->_roles_auth = array();
			
			}
		
		}
		
		/**
			* Refresh the roles list, called in case of modification in database
			*
			* @access	public
		*/
		
		public function refresh(){
		
			$this->get_content();
			$this->initialize();
		
		}
		
		/**
			* Insert in attributes default roles
			*
			* @access	private
		*/
		
		private function initialize(){
		
			array_push($this->_roles, 'administrator', 'editor', 'author');
			
			$this->_roles_auth['_administrator'] = array('dashboard' => true, 'post' => true, 'media' => true, 'album_photo' => true, 'comments' => true, 'delete_content' => true, 'settings' => true);
			$this->_roles_auth['_editor'] =  array('dashboard' => true, 'post' => true, 'media' => true, 'album_photo' => false, 'comments' => true, 'delete_content' => true, 'settings' => false);
			$this->_roles_auth['_author'] = array('dashboard' => true, 'post' => true, 'media' => true, 'album_photo' => false, 'comments' => false, 'delete_content' => false, 'settings' => false);
		
		}
		
		/**
			* Return an attribute value
			*
			* "_roles" will return an array of all roles
			*
			* Otherwise if you want your "myrole" authorization array, call __get('_myrole')
			*
			* @access	public
			* @param	string [$attr]
			* @return	array
		*/
		
		public function __get($attr){
		
			if($attr == '_roles')
				return $this->$attr;
			else
				return $this->_roles_auth[$attr];
		
		}
	
	}
	
	/**
		* Manage Roles
		*
		* Handle creation/modification/deletion of users roles
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Roles
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		private $_setting = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'User Roles';
			
			if($this->_user['settings']){
			
				$this->create();
				$this->update();
				$this->delete();
				
				$this->_setting =& Roles::load();
				$this->_setting->refresh();
			
			}
		
		}
		
		/**
			* Display related admin parts link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::manageroles_menu();
		
		}
		
		/**
			* Display form to add a new role
			*
			* @access	private
		*/
		
		private function display_add(){
		
			Html::add_form();
		
		}
		
		/**
			* Display table of all roles
			*
			* @access	private
		*/
		
		private function display_table(){
		
			Html::roles_table('o');
			
			foreach ($this->_setting->_roles as $role) {
			
				$name = '_'.$role;
			
				$auth = $this->_setting->$name;
				
				Html::roles_table_row($role, $auth['dashboard'], $auth['post'], $auth['media'], $auth['album_photo'], $auth['comments'], $auth['delete_content'], $auth['settings']);
			
			}
			
			Html::roles_table('c');
		
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
				
				Html::form('o', 'post', 'index.php?ns=roles&ctl=manage');
			
				$this->display_add();
				$this->display_table();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Check posted data
			*
			* @access	private
			* @return	boolean
		*/
		
		private function check_post_data(){
		
			$errors = array();
		
			if(str_word_count(VPost::role()) != 1)
				array_push($errors, '<li>- The role can contain only one word</li>');
			
			if(!empty($errors)){
			
				$error_msg = 'Check your informations:<br/><ul>'.implode('', $errors).'</ul>';
				$this->_action_msg = ActionMessages::custom_wrong($error_msg);
				return false;
			
			}else{
			
				return true;
			
			}
		
		}
		
		/**
			* Create a new role
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::add_role(false) && $this->check_post_data()){
			
				try{
				
					$new_role = new Setting();
					$new_role->_name = strtolower(VPost::role());
					$new_role->_type = 'role';
					$new_role->_data = json_encode(array('dashboard' => false, 'post' => false, 'media' => false, 'album_photo' => false, 'comments' => false, 'delete_content' => false, 'settings' => false));
					$new_role->create();
					
					//update array containing all roles name
					$to_read['table'] = 'setting';
					$to_read['columns'] = array('SETTING_ID');
					$to_read['condition_columns'][':t'] = 'setting_type';
					$to_read['condition_select_types'][':t'] = '=';
					$to_read['condition_values'][':t'] = 'all_roles';
					$to_read['value_types'][':t'] = 'str';
					
					$all_roles = $this->_db->read($to_read);
					
					//if the setting does't exist, we create it with the new role
					if(empty($all_roles)){
					
						$all_roles = new Setting();
						$all_roles->_name = 'All roles';
						$all_roles->_type = 'all_roles';
						$all_roles->_data = json_encode(array(strtolower(VPost::role())));
						$all_roles->create();
					
					}else{
					
						$all_roles = new Setting($all_roles[0]['SETTING_ID']);
						$roles = json_decode($all_roles->_data, true);
						array_push($roles, strtolower(VPost::role()));
						$all_roles->_data = json_encode($roles);
						$all_roles->update('_data', 'str');
					
					}
					//end update
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::created($result);
			
			}
		
		}
		
		/**
			* Update all roles at one time
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update_roles(false)){
			
				try{
				
					$setting =& Roles::load();
					
					$to_read['table'] = 'setting';
					$to_read['columns'] = array('SETTING_ID');
					$to_read['condition_columns'][':t'] = 'setting_type';
					$to_read['condition_select_types'][':t'] = '=';
					$to_read['condition_values'][':t'] = 'role';
					$to_read['value_types'][':t'] = 'str';
					$to_read['condition_types'][':n'] = 'AND';
					$to_read['condition_columns'][':n'] = 'setting_name';
					$to_read['condition_select_types'][':n'] = '=';
					$to_read['value_types'][':n'] = 'str';
				
					foreach ($setting->_roles as $role) {
				
						if(!in_array($role, array('administrator', 'editor', 'author'))){
						
							$name = 'auth_'.$role;
						
							$array = VPost::$name(array());
							
							$auth = array('dashboard' => false, 'post' => false, 'media' => false, 'album_photo' => false, 'comments' => false, 'delete_content' => false, 'settings' => false);
							
							foreach ($auth as $key => &$value)
								if(in_array($key, $array))
									$value = true;
							
							$to_read['condition_values'][':n'] = $role;
							
							$role = $this->_db->read($to_read);
							
							$role = new Setting($role[0]['SETTING_ID']);
							
							$role->_data = json_encode($auth);
							$role->update('_data', 'str');
						
						}
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}
		
		}
		
		/**
			* Delete a role
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VGet::action(false) == 'delete' && !in_array(VGet::role(), array('administrator', 'editor', 'author')) && $this->_user['delete_content']){
			
				try{
				
					$to_read['table'] = 'user';
					$to_read['columns'] = array('USER_ID');
					$to_read['condition_columns'][':r'] = 'user_role';
					$to_read['condition_select_types'][':r'] = '=';
					$to_read['condition_values'][':r'] = VGet::role();
					$to_read['value_types'][':r'] = 'str';
					
					$users = $this->_db->read($to_read);
					
					if(!empty($users))
						throw new Exception('Can\'t delete the role "'.ucfirst(VGet::role()).'" because a user is using it!');
					
					$to_read = null;
					
					$to_read['table'] = 'setting';
					$to_read['columns'] = array('SETTING_ID');
					$to_read['condition_columns'][':t'] = 'setting_type';
					$to_read['condition_select_types'][':t'] = '=';
					$to_read['condition_values'][':t'] = 'role';
					$to_read['value_types'][':t'] = 'str';
					$to_read['condition_types'][':n'] = 'AND';
					$to_read['condition_columns'][':n'] = 'setting_name';
					$to_read['condition_select_types'][':n'] = '=';
					$to_read['condition_values'][':n'] = VGet::role();
					$to_read['value_types'][':n'] = 'str';
					
					$role = $this->_db->read($to_read);
					
					$role = new Setting($role[0]['SETTING_ID']);
					$role->delete();
					
					$to_read = null;
					
					$to_read['table'] = 'setting';
					$to_read['columns'] = array('SETTING_ID');
					$to_read['condition_columns'][':t'] = 'setting_type';
					$to_read['condition_select_types'][':t'] = '=';
					$to_read['condition_values'][':t'] = 'all_roles';
					$to_read['value_types'][':t'] = 'str';
					
					$roles = $this->_db->read($to_read);
					
					$roles = new Setting($roles[0]['SETTING_ID']);
					
					$array = json_decode($roles->_data, true);
					
					foreach ($array as $key => $value)
						if($value == VGet::role())
							unset($array[$key]);
					
					$roles->_data = json_encode($array);
					$roles->update('_data', 'str');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::deleted($result);
			
			}elseif(VGet::action(false) == 'delete' && !$this->_user['delete_content']){
			
				$this->_action_msg = ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>