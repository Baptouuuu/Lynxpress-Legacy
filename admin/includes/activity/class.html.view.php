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
	
	namespace Admin\Activity;
	use \Admin\Html\Html as Master;
	
	/**
		* Html Activity
		*
		* Contains all html for activity class
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Activity
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0.1
		* @abstract
	*/
	
	abstract class Html extends Master{
	
		/**
			* Display menu for activity page
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=activity&ctl=manage">Activity</a></span>'.
				 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display activity table structure
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
		*/
		
		public static function table($part){
		
			if($part == 'o'){
			
				echo '<table id="table">'.
						'<thead>'.
							'<tr>'.
								'<th class="column_user">User</th>'.
								'<th class="column_message">Message</th>'.
								'<th class="column_date">Date</th>'.
							'</tr>'.
						'</thead>'.
						'<tfoot>'.
							'<tr>'.
								'<th class="column_user">User</th>'.
								'<th class="column_message">Message</th>'.
								'<th class="column_date">Date</th>'.
							'</tr>'.
						'</tfoot>'.
						'<tbody>';
			
			}elseif($part == 'c'){
			
				echo 	'</tbody>'.
					 '</table>';
			
			}
		
		}
		
		/**
			* Display a table row
			*
			* @static
			* @access	public
			* @param	string [$username] Username of who made the action
			* @param	string [$email] Email of who made the action
			* @param	string [$message] Action message
			* @param	string [$date] Date of the action
		*/
		
		public static function table_row($username, $email, $message, $date){
		
			echo '<tr>'.
				 	'<td>'.$username.' (<a href="mailto:'.$email.'">e-mail</a>)</td>'.
				 	'<td>'.$message.'</td>'.
				 	'<td>'.date('Y/m/d @ H:i:s', strtotime($date)).'</td>'.
				 '</tr>';
		
		}
		
		/**
			* Display a reset button
			*
			* @static
			* @access	public
		*/
		
		public static function b_reset(){
		
			echo '<input class="button" type="submit" name="reset" value="Reset Activity" />';
		
		}
	
	}

?>