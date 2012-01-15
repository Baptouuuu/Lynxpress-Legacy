<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011-2012
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
	use \Admin\Master\Master as Master;
	use Exception;
	use \Library\Database\Database as Database;
	use \Library\Models\Setting as Setting;
	
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

?>