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
	
	namespace Admin\Activity;
	use \Admin\Master as Master;
	use Exception;
	use \Library\Variable\Post as VPost;
	use \Library\Models\User as User;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	
	/**
		* Manage Activity
		*
		* Display all activity history and can reset it
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Activity
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		private $_activity = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Activity';
			
			if($this->_user['settings']){
			
				$this->delete();
				
				$this->get_content();
			
			}
		
		}
		
		/**
			* Retrieve all activity saved in database
			*
			* @access	private
		*/
		
		private function get_content(){
		
			try{
			
				$to_read['table'] = 'activity';
				$to_read['columns'] = array('*');
				$to_read['order'] = array('date', 'DESC');
				
				$this->_activity = $this->_db->read($to_read);
				
				if(!empty($this->_activity)){
				
					foreach ($this->_activity as &$value) {
					
						$user = new User();
						$user->_id = $value['USER_ID'];
						$user->read('_username');
						$user->read('_email');
						$value['username'] = $user->_username;
						$value['email'] = $user->_email;
					
					}
				
				}
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Display page menu
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_user['settings'])
				Html::menu();
			else
				HtmlSettings::menu();
		
		}
		
		/**
			* Display table activity
			*
			* @access	private
		*/
		
		private function display_table(){
		
			Html::table('o');
			
			if(!empty($this->_activity)){
			
				foreach ($this->_activity as $activity)
					Html::table_row($activity['username'], $activity['email'], $activity['data'], $activity['date']);
			
			}else{
			
				echo '<tr><td colspan="3">No activity yet</td></tr>';
			
			}
			
			Html::table('c');
		
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
				
				Html::form('o', 'post', 'index.php?ns=activity&ctl=manage');
				
				Html::b_reset();
				$this->display_table();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Delete all registered activity
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::reset(false) && $this->_user['delete_content']){
			
				try{
				
					$this->_db->query('TRUNCATE TABLE '.DB_PREFIX.'activity');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::deleted($result);
			
			}elseif(VPost::reset(false) && !$this->_user['delete_content']){
			
				
			
			}
		
		}
	
	}

?>