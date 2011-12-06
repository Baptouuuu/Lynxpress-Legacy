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
	
	namespace Admin\Update;
	use \Admin\Html\Html as Master;
	
	/**
		* Html Update
		*
		* Contains all html for update class
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Update
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html extends Master{
	
		/**
			* Display menu for update page
			*
			* @static
			* @access	public
			* @param	boolean [$bool] If the user can access settings
		*/
		
		public static function menu($bool = false){
		
			if($bool){
			
				echo '<div id="menu">'.
					 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=update&ctl=manage">Update</a></span>'.
					 '</div>';
			
			}else{
			
				echo '<div id="menu">'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display form to update Lynxpress
			*
			* @static
			* @access	public
		*/
		
		public static function update_form(){
		
			echo '<h3>Click the button below to update "'.WS_NAME.'"</h3>'.
				 '<input class="button button_publish submit" type="submit" name="update" value="Update Lynxpress" /><br/>'.
				 '<p class="indication">'.
				 	'(Before update, please backup your website files to prevent any problem. A database backup will be made and sent to '.WS_EMAIL.')<br/>'.
				 	'(Please don\'t quit this page while updating.)'.
				 '</p>';
		
		}
	
	}

?>