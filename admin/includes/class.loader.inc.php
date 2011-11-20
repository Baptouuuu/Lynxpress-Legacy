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
	
	namespace Admin;
	use Exception;
	
	/**
		* Loader
		*
		* Class to autoload php files
		*
		* Files names are determined with there namespaces
		*
		* @package		Administration
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Loader{
	
		/**
			* Load php files thanks to there namespaces
			*
			* @static
			* @access	private
		*/
		
		private static function autoloader($class){
		
			$namespaces = explode('\\', strtolower($class));
			
			if($namespaces[0] == 'library'){
			
				$require = 'library/class.'.$namespaces[1].'.library.php';
				
			}elseif($namespaces[0] == 'admin'){
			
				$require = 'includes/class.'.$namespaces[1];
				
				if(isset($namespaces[2]) && $namespaces[2] == 'html')
					$require .= '.view.php';
				else
					$require .= '.inc.php';
			
			}
			
			if(!file_exists($require))
				throw new Exception('Namespace "'.$namespaces[0].'" doesn\'t exists');
			
			require_once $require;
			
			if(!class_exists($class))
				throw new Exception('Controller "'.end($namespaces).'" doesn\'t exists');
		
		}
		
		/**
			* Register method to autoload php classes files
			*
			* @static
			* @access	public
		*/
		
		public static function load(){
		
			spl_autoload_register(null, false);
			spl_autoload_extensions('.inc.php, .view.php, .library.php');
			spl_autoload_register('self::autoloader', true);
		
		}
	
	}

?>