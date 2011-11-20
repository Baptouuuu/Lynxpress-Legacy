<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011
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
	
	namespace Library\Variable;
	
	/**
		* VGet
		*
		* Method to retrieve Get variables,
		* VGet::all() permits to retrieve all $_GET variables,
		* and will set empty variables with your wished default value
		*
		* To retrieve your wished variable, use VGet::my_variable($default), 
		* $default will be returned if the variable is empty or doesn't exists,
		* Notice that $default is optionnal!
		*
		* @package		Library
		* @subpackage	Variables
		* @namespace	Variable
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Get{
	
		/**
			* Return all Get variables, if a variable is empty it's set to the parameter variable
			*
			* @static
			* @access	public
			* @param	mixed [$default]
			* @return	array
		*/
		
		public static function all($default = null){
		
			foreach($_GET as $key => &$value)
				if(empty($_GET[$key]))
					$value = $default;
			
			return $_GET;
		
		}
		
		/**
			* Return wanted Get variable, if it's not set or it's empty, we return the default value
			*
			* @static
			* @access	public
			* @param	string [$variable] Wished variable
			* @param	array [$default]
			* @return	mixed
		*/
		
		public static function __callStatic($variable, $default = array()){
		
			if(!isset($default[0]))
				$default[0] = null;
			
			if(!isset($_GET[$variable]) || empty($_GET[$variable]))
				return $default[0];
			else
				return $_GET[$variable];
		
		}
	
	}
	
	/**
		* VPost
		*
		* Method to retrieve Post variables,
		* VPot::all() permits to retrieve all $_POST variables,
		* and will set empty variables with your wished default value
		*
		* To retrieve your wished variable, use VPost::my_variable($default),
		* $default will be returned if the variable is empty or doesn't exists,
		* Notice that $default is optionnal!
		*
		* @package		Library
		* @subpackage	Variables
		* @namespace	Variable
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Post{
	
		/**
			* Return all Post variables, if a variable is empty it's set to the parameter variable
			*
			* @static
			* @access	public
			* @param	mixed [$default]
			* @return	array
		*/
		
		public static function all($default = null){
		
			foreach($_POST as $key => &$value)
				if(empty($_POST[$key]))
					$value = $default;
			
			return $_POST;
		
		}
		
		/**
			* Return wanted Post variable, if it's not set or it's empty, we return the default value
			*
			* @static
			* @access	public
			* @param	string [$variable] Wished variable
			* @param	array [$default]
			* @return	mixed
		*/
		
		public static function __callStatic($variable, $default = array()){
		
			if(!isset($default[0]))
				$default[0] = null;
			
			if(!isset($_POST[$variable]) || empty($_POST[$variable]))
				return $default[0];
			else
				return $_POST[$variable];
		
		}
	
	}
	
	/**
		* VRequest
		*
		* Method to retrieve Request variables,
		* VRequest::all() permits to retrieve all $_REQUEST variables,
		* and will set empty variables with your wished default value,
		*
		* To retrieve your wished variable, use VRequest::my_variable($default),
		* $default will be returned if the variable is empty or doesn't exists,
		* Notice that $default is optionnal!
		*
		* @package		Library
		* @subpackage	Variables
		* @namespace	Variable
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Request{
	
		/**
			* Return all Request variables, if a variable is empty it's set to the parameter variable
			*
			* @static
			* @access	public
			* @param	mixed [$default]
			* @return	array
		*/
		
		public static function all($default = null){
		
			foreach($_REQUEST as $key => &$value)
				if(empty($_REQUEST[$key]))
					$value = $default;
			
			return $_REQUEST;
		
		}
		
		/**
			* Return wanted Request variable, if it's not set or it's empty, we return the default value
			*
			* @static
			* @access	public
			* @param	string [$variable] Wished variable
			* @param	array [$default]
			* @return	mixed
		*/
		
		public static function __callStatic($variable, $default = array()){
		
			if(!isset($default[0]))
				$default[0] = null;
			
			if(!isset($_REQUEST[$variable]) || empty($_REQUEST[$variable]))
				return $default[0];
			else
				return $_REQUEST[$variable];
		
		}
	
	}
	
	/**
		* VFiles
		*
		* Method to retrieve Files variables,
		* VFiles::all() permits to retrieve all $_FILES variables,
		* and will set empty variables with your wished default value
		*
		* To retrieve your wished variable, use VFiles::my_variable($default),
		* $default will be returned if the variable is empty or doesn't exists,
		* Notice that $default is optionnal!
		*
		* @package		Library
		* @subpackage	Variables
		* @namespace	Variable
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Files{
	
		/**
			* Return all Files variables, if a variable is empty it's set to the parameter variable
			*
			* @static
			* @access	public
			* @param	mixed [$default]
			* @return	array
		*/
		
		public static function all($default = null){
		
			foreach($_FILES as $key => &$value)
				if(empty($_FILES[$key]))
					$value = $default;
			
			return $_FILES;
		
		}
		
		/**
			* Return wanted Files variable, if it's not set or it's empty, we return the default value
			*
			* @static
			* @access	public
			* @param	string [$variable] Wished variable
			* @param	array [$default]
			* @return	mixed
		*/
		
		public static function __callStatic($variable, $default = array()){
		
			if(!isset($default[0]))
				$default[0] = null;
			
			if(!isset($_FILES[$variable]) || empty($_FILES[$variable]))
				return $default[0];
			else
				return $_FILES[$variable];
		
		}
	
	}
	
	/**
		* VCookie
		*
		* Method to retrieve Cookie variables,
		* VCookie::all() permits to retrieve all $_COOKIE variables,
		* and will set empty variables with your wished default value,
		*
		* To retrieve your wished variable, use VCookie::my_variable($default),
		* $default will be returned if the variable is empty or doesn't exists,
		* Notice that $default is optionnal!
		*
		* @package		Library
		* @subpackage	Variables
		* @namespace	Variable
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Cookie{
	
		/**
			* Return all Cookie variables, if a variable is empty it's set to the parameter variable
			*
			* @static
			* @access	public
			* @param	mixed [$default]
			* @return	array
		*/
		
		public static function all($default = null){
		
			foreach($_COOKIE as $key => &$value)
				if(empty($_COOKIE[$key]))
					$value = $default;
			
			return $_COOKIE;
		
		}
		
		/**
			* Return wanted Cookie variable, if it's not set or it's empty, we return the default value
			*
			* @static
			* @access	public
			* @param	string [$variable] Wished variable
			* @param	array [$default]
			* @return	mixed
		*/
		
		public static function __callStatic($variable, $default = array()){
		
			if(!isset($default[0]))
				$default[0] = null;
			
			if(!isset($_COOKIE[$variable]) || empty($_COOKIE[$variable]))
				return $default[0];
			else
				return $_COOKIE[$variable];
		
		}
	
	}
	
	/**
		* VSession
		*
		* Method to retrieve Session variables,
		* VSession::all() permits to retrieve all $_SESSION variables,
		* and will set empty variables with your wished default value
		*
		* To retrieve your wished variable, use VSession::my_variable($default),
		* $default will be returned if the variable is empty or doesn't exists,
		* Notice that $default is optionnal!
		*
		* @package		Library
		* @subpackage	Variables
		* @namespace	Variable
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Session{
	
		/**
			* Return all Session variables, if a variable is empty it's set to the parameter variable
			*
			* @static
			* @access	public
			* @param	mixed [$default]
			* @return	array
		*/
		
		public static function all($default = null){
		
			foreach($_SESSION as $key => &$value)
				if(empty($_SESSION[$key]))
					$value = $default;
			
			return $_SESSION;
		
		}
		
		/**
			* Return wanted Session variable, if it's not set or it's empty, we return the default value
			*
			* @static
			* @access	public
			* @param	string [$variable] Wished variable
			* @param	array [$default]
			* @return	mixed
		*/
		
		public static function __callStatic($variable, $default = array()){
		
			if(!isset($default[0]))
				$default[0] = null;
			
			if(!isset($_SESSION[$variable]) || empty($_SESSION[$variable]))
				return $default[0];
			else
				return $_SESSION[$variable];
		
		}
	
	}
	
	/**
		* VServer
		*
		* Method to retrieve Server variables,
		* VServer::all() permits to retrieve all $_SERVER variables,
		* and will set empty variables with your wished default value
		*
		* To retrieve your wished variable, use VServer::my_variable($default),
		* $default will be returned if the variable is empty or doesn't exists,
		* Notice that $default is optionnal!
		*
		* @package		Library
		* @subpackage	Variables
		* @namespace	Variable
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Server{
	
		/**
			* Return all Server variables, if a variable is empty it's set to the parameter variable
			*
			* @static
			* @access	public
			* @param	mixed [$default]
			* @return	array
		*/
		
		public static function all($default = null){
		
			foreach($_SERVER as $key => &$value)
				if(empty($_SERVER[$key]))
					$value = $default;
			
			return $_SERVER;
		
		}
		
		/**
			* Return wanted Server variable, if it's not set or it's empty, we return the default value
			*
			* @static
			* @access	public
			* @param	string [$variable] Wished variable
			* @param	array [$default]
			* @return	mixed
		*/
		
		public static function __callStatic($variable, $default = array()){
		
			if(!isset($default[0]))
				$default[0] = null;
			
			if(!isset($_SERVER[$variable]) || empty($_SERVER[$variable]))
				return $default[0];
			else
				return $_SERVER[$variable];
		
		}
	
	}

?>