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
	
	namespace Admin\Loader;
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
		* @version		1.0.1
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
			
				$require = 'library/'.$namespaces[1].'/class.'.$namespaces[2].'.library.php';
				
			}elseif($namespaces[0] == 'admin'){
			
				$require = 'includes/'.$namespaces[1].'/class.'.$namespaces[2];
				
				if(isset($namespaces[2]) && $namespaces[2] == 'html')
					$require .= '.view.php';
				elseif(isset($namespaces[2]) && $namespaces[2] == 'lang')
					$require .= '.i18n.php';
				else
					$require .= '.inc.php';
			
			}
			
			if(!file_exists($require))
				throw new Exception('Namespace "'.$class.'" doesn\'t exists');
			
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
		
			if(!extension_loaded('spl'))
				die('SPL extension not loaded!');
			
			spl_autoload_register(null, false);
			spl_autoload_extensions('.inc.php, .view.php, .library.php');
			spl_autoload_register('self::autoloader', true);
		
		}
	
	}

?>