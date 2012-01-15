<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011-2012
		* @license		http://www.gnu.org/licenses/gpl.html GNU GPL V3
		* @package		Lynxpress
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
	
	namespace Library\Curl;
	use Exception;
	
	/**
		* Curl
		*
		* Used to retrieve content from other websites
		*
		* @package		Library
		* @subpackage	Curl
		* @namespace	Curl
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
	*/
	
	class Curl{
	
		private $_c = null;
		private $_url = null;
		private $_follow = null;
		private $_content = null;
		
		/**
			* Class Constructor
			*
			* @access	public
			* @param	string [$url] If a url is passed, the connection will automatically be done
		*/
		
		public function __construct($url = false){
		
			self::check_ext();
			
			$this->_c = curl_init();
			$this->_follow = true;
			
			if($url !== false){
			
				$this->_url = $url;
				$this->connect();
			
			}
		
		}
		
		/**
			* Connect to the specified url
			*
			* @access	public
		*/
		
		public function connect(){
		
			if(empty($this->_url))
				throw new Exception('Please mention a url');
			
			curl_setopt($this->_c, CURLOPT_URL, $this->_url);
			curl_setopt($this->_c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->_c, CURLOPT_HEADER, false);
			curl_setopt($this->_c, CURLOPT_FOLLOWLOCATION, $this->_follow);
			
			$this->_content = curl_exec($this->_c);
			
			if($this->_content === false)
				throw new Exception('Error trying to connect to "'.$this->_url.'" (Error: "'.curl_error($this->_c).'")');
		
		}
		
		/**
			* Method to set data in the object
			*
			* @access	public
			* @param	string [$attr]
			* @param	mixed [$value]
		*/
		
		public function __set($attr, $value){
		
			$this->$attr = $value;
		
		}
		
		/**
			* Check if curl extension is loaded
			*
			* @static
			* @access	private
		*/
		
		private static function check_ext(){
		
			if(!extension_loaded('curl'))
				throw new Exception('Curl extension not loaded!');
		
		}
		
		/**
			* Method to get value of an attribute
			*
			* @access	public
			* @param	string [$attr]
		*/
		
		public function __get($attr){
		
			return $this->$attr;
		
		}
		
		/**
			* Class destructor close url connection
			*
			* @access	public
		*/
		
		public function __destruct(){
		
			curl_close($this->_c);
		
		}
	
	}

?>