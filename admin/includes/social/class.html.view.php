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
	
	namespace Admin\Social;
	use \Admin\Html\Html as Master;
	
	/**
		* Html Settings
		*
		* Contains all html for social class
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Social
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html extends Master{
	
		/**
			* Display menu for manage social page
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
				 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=social&ctl=manage">Share Buttons</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display form to activate share buttons
			*
			* @static
			* @access	public
			* @param	array [$data] Activated buttons
		*/
		
		public static function settings($data){
		
			echo '<h3>Check social networks you want to activate share buttons</h3>'.
				 '<input class="button button_publish submit" type="submit" name="update_setting" value="Update" /><br/>'.
				 '<div id="labels">'.
					 '<div class="setting_label">'.
					 	'<div class="label_img">'.
					 		'<input id="efbs" type="checkbox" name="networks[]" value="facebook" '.((in_array('facebook', $data))?'checked':'').' />'.
					 	'</div>'.
					 	'<div class="label_name">'.
					 		'<label for="efbs">Facebook</label>'.
					 	'</div>'.
					 '</div>'.
					 '<div class="setting_label">'.
					 	'<div class="label_img">'.
					 		'<input id="etws" type="checkbox" name="networks[]" value="twitter" '.((in_array('twitter', $data))?'checked':'').' />'.
					 	'</div>'.
					 	'<div class="label_name">'.
					 		'<label for="etws">Twitter</label>'.
					 	'</div>'.
					 '</div>'.
					 '<div class="setting_label">'.
					 	'<div class="label_img">'.
					 		'<input id="eggs" type="checkbox" name="networks[]" value="google" '.((in_array('google', $data))?'checked':'').' />'.
					 	'</div>'.
					 	'<div class="label_name">'.
					 		'<label for="eggs">Google+</label>'.
					 	'</div>'.
					 '</div>'.
				 '</div>';
		
		}
	
	}

?>