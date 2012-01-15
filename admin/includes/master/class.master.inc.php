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
	
	namespace Admin\Master;
	use \Library\Database\Database as Database;
	use \Admin\Session\Session as Session;
	use Exception;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	
	/**
		* Master
		*
		* Master class for all page controller, calls session verification methods
		*
		* Contains reusable methods which could be called every where in the admin
		*
		* @package		Administration
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Master{
	
		protected $_db = null;
		protected $_title = null;
		protected $_user = array();
		protected $_categories = array();
		private $_session_fct = null;
		protected $_action_msg = null;
		protected $_display_html = null;
		
		/**
			* Class constructor
			*
			* Constructor has to be called in each child class constructor before any function
			*
			* In order to verify session validity and then retrieve user permissions
			*
			* @access	protected
		*/
		
		protected function __construct(){
		
			if(!extension_loaded('json'))
				throw new Exception('Json not loaded');
			
			$this->_db =& Database::load();
			$this->_session_fct = new Session();
			$this->_session_fct->verify_session();
			$this->permission();
			
			$this->_display_html = true;
		
		}
		
		/**
			* Retrieve logged user permissions from Session class
			*
			* @access	private
		*/
		
		private function permission(){
		
			$auth = $this->_session_fct->verify_permission();
			
			foreach($auth as $key => $value)
				$this->_user["$key"] = $value;
		
		}
		
		/**
			* Return a message if call to undefined method
			*
			* @access	public
			* @param	string [$name] Method name
			* @param	array [$arguments] Array of all arguments passed to the unknown method
			* @return	string Error message
		*/
		
		public function __call($name, $arguments){
		
			return 'The lynx didn\'t show up calling '.$name;
		
		}
		
		/**
			* Return a message if call to undefined method in static context
			*
			* @static
			* @access	public
			* @param	string [$name] Method name
			* @param	array [$arguments] Array of all arguments passed to the unknown method
			* @return	string Error message
		*/
		
		public static function __callStatic($name, $arguments){
		
			return 'The lynx didn\'t show up calling '.$name;
		
		}
		
		/**
			* Function to get attributes from outside the object
			*
			* @access	public
			* @param	string [$attr] Only "title" and "settings" are allowed
			* @return	mixed
		*/
		
		public function __get($attr){
		
			if($attr == 'title')
				return $this->_title;
			elseif($attr == 'settings')
				return $this->_user['settings'];
			elseif($attr == 'html')
				return $this->_display_html;
			else
				return 'The lynx is not here!';
		
		}
	
	}

?>