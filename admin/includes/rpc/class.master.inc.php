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
	
	namespace Admin\Rpc;
	use \Library\Database\Database as Database;
	
	/**
		* Master RPC
		*
		* Handle basic rpc functions
		*
		* @package		Administration
		* @namespace	Rpc
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Master{
	
		protected $_db = null;
		protected $_display_html = null;
		protected $_content = null;
		protected $_url = null;
		const LIFETIME = 60;			//lifetime of one minutes
		
		/**
			* Class constructor
			*
			* @access	protected
		*/
		
		protected function __construct(){
		
			$this->_db =& Database::load();
			$this->_display_html = false;
		
		}
		
		/**
			* Check if a cache file exist with the given url
			*
			* @access	protected
		*/
		
		protected function check_cache(){
			
			if(file_exists($this->_url) && filemtime($this->_url) > (time()-self::LIFETIME))
				return true;
			else
				return false;
		
		}
		
		/**
			* Function to get attributes from outside the object
			*
			* @access	public
			* @param	string [$attr] Only "title" and "settings" are allowed
			* @return	mixed
		*/
		
		public function __get($attr){
		
			if($attr == 'html')
				return $this->_display_html;
			else
				return 'The lynx is not here!';
		
		}
	
	}

?>