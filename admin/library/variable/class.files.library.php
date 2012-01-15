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
	
	namespace Library\Variable;
	
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

?>