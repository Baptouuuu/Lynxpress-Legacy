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
	
	namespace Admin\Html;
	
	/**
		* Main html class
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html{
	
		/**
			* Display form tag
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
			* @param	string [$method] Method to transmit data, can be "get" or "post"
			* @param	string [$action] Destination page to transmit data
			* @param	boolean [$enctype] If the form will upload files or not
		*/
		
		public static function form($part, $method = '', $action = '', $enctype = false){
		
			if($part == 'o'){
			
				echo '<form method="'.$method.'" action="'.$action.'" accept-charset="utf-8" '.(($enctype)?'enctype="multipart/form-data"':'').'>';
			
			}elseif($part == 'c'){
			
				echo '</form>';
			
			}
		
		}
	
	}

?>