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
	use \Library\Variable\Request as VRequest;
	use \Library\Variable\Session as VSession;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Admin\Helper\Helper as Helper;
	
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
			* Display retrieved users
			*
			* @access	private
		*/
		
		private function display_users(){
		
			echo '<section id="labels">';
			
			foreach($this->_content as $user){
			
				$edit = null;
				
				if($user->_id != $this->_user['user_id'])
					$edit = '&id='.$user->_id;
				
				Html::label($user->_id, $edit, $user->_username, $user->_publicname, $user->_role, $user->_email, $user->_avatar);
					 	
			}
			
			echo '</section>';
		
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