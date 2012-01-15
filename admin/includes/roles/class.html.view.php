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
	
	namespace Admin\Roles;
	use \Admin\Html\Html as Master;
	
	/**
		* Html Roles
		*
		* Contains all html for roles classes
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Roles
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html extends Master{
	
		/**
			* Display menu for ManageRoles controller
			*
			* @static
			* @access	public
		*/
		
		public static function manageroles_menu(){
		
			echo '<div id="menu">'.
				 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=roles&ctl=manage">User Roles</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display form to add a role
			*
			* @static
			* @access	public
		*/
		
		public static function add_form(){
		
			echo '<input id="new_role" class="input" type="text" name="role" placeholder="New role" /> <input class="button" type="submit" name="add_role" value="Add" />';
		
		}
		
		/**
			* Display table of all roles
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
		*/
		
		public static function roles_table($part){
		
			if($part == 'o'){
		
				echo '<table id="table">'.
						'<thead>'.
							'<tr>'.
								'<th class="column_role_name">Role name</th>'.
								'<th class="column_role_dashboard">Dashboard</th>'.
								'<th class="column_role_post">Post</th>'.
								'<th class="column_role_media">Media</th>'.
								'<th class="column_role_album_photo">Albums</th>'.
								'<th class="column_role_comments">Comments</th>'.
								'<th class="column_role_delete_content">Delete Content</th>'.
								'<th class="column_role_settings">Settings</th>'.
							'</tr>'.
						'</thead>'.
						'<tfoot>'.
							'<tr>'.
								'<th class="column_role_name">Role name</th>'.
								'<th class="column_role_dashboard">Dashboard</th>'.
								'<th class="column_role_post">Post</th>'.
								'<th class="column_role_media">Media</th>'.
								'<th class="column_role_album_photo">Albums</th>'.
								'<th class="column_role_comments">Comments</th>'.
								'<th class="column_role_delete_content">Delete Content</th>'.
								'<th class="column_role_settings">Settings</th>'.
							'</tr>'.
						'</tfoot>'.
						'<tbody>';
			
			}elseif($part == 'c'){
			
				echo 	'</tbody>'.
					 '</table>'.
					 '<input class="button button_publish submit" type="submit" name="update_roles" value="Update" /> &nbsp;<span class="indication">(Indication: Administrator, Editor and Author roles are hard coded, so you can\'t update them)</span>';
			
			}
		
		}
		
		/**
			* Display a row for roles table
			*
			* @static
			* @access	public
			* @param	string [$name] Role name
			* @param	boolean [$dashboard]
			* @param	boolean [$post]
			* @param	boolean [$media]
			* @param	boolean [$album_photo]
			* @param	boolean [$comments]
			* @param	boolean [$delete_content]
			* @param	boolean [$settings]
		*/
		
		public static function roles_table_row($name, $dashboard, $post, $media, $album_photo, $comments, $delete_content, $settings){
		
			$delete = '';
		
			if(!in_array($name, array('administrator', 'editor', 'author')))
				$delete = '<a class="red" href="index.php?ns=roles&ctl=manage&action=delete&role='.$name.'">Delete permanently</a>';
			
			echo '<tr>'.
				 	'<td class="column_role_name">'.
				 		ucfirst($name).'<br/>'.
				 		$delete.
				 	'</td>'.
				 	'<td class="column_role_dashboard">'.
				 		'<input type="checkbox" name="auth_'.$name.'[]" value="dashboard" '.(($dashboard)?'checked':'').' />'.
				 	'</td>'.
				 	'<td class="column_role_post">'.
				 		'<input type="checkbox" name="auth_'.$name.'[]" value="post" '.(($post)?'checked':'').' />'.
				 	'</td>'.
				 	'<td class="column_role_media">'.
				 		'<input type="checkbox" name="auth_'.$name.'[]" value="media" '.(($media)?'checked':'').' />'.
				 	'</td>'.
				 	'<td class="column_role_album_photo">'.
				 		'<input type="checkbox" name="auth_'.$name.'[]" value="album_photo" '.(($album_photo)?'checked':'').' />'.
				 	'</td>'.
				 	'<td class="column_role_comments">'.
				 		'<input type="checkbox" name="auth_'.$name.'[]" value="comments" '.(($comments)?'checked':'').' />'.
				 	'</td>'.
				 	'<td class="column_role_delete_content">'.
				 		'<input type="checkbox" name="auth_'.$name.'[]" value="delete_content" '.(($delete_content)?'checked':'').' />'.
				 	'</td>'.
				 	'<td class="column_role_settings">'.
				 		'<input type="checkbox" name="auth_'.$name.'[]" value="settings" '.(($settings)?'checked':'').' />'.
				 	'</td>'.
				 '</tr>';
		
		}
	
	}

?>