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
	
	namespace Admin\Links;
	use \Admin\Html\Html as Master;
	
	/**
		* Html Links
		*
		* Contains all html for links classes
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Links
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html extends Master{
	
		/**
			* Display menu for new link page
			*
			* @static
			* @access	public
			* @param	boolean [$bool]
		*/
		
		public static function nl_menu($bool){
		
			if($bool){
			
				echo '<div id="menu">'.
					 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
					 	'<span class="menu_item"><a href="index.php?ns=links&ctl=manage">Links</a></span>'.
					 	'<span id="menu_selected" class="menu_item">Editing</span>'.
					 	'<span class="menu_item"><a href="index.php?ns=links&ctl=add">Add</a></span>'.
					 '</div>';
			
			}else{
			
				echo '<div id="menu">'.
					 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
					 	'<span class="menu_item"><a href="index.php?ns=links&ctl=manage">Links</a></span>'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=links&ctl=add">Add</a></span>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display new link form
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
			* @param	string [$name] Link name
			* @param	string [$link] Link url
			* @param	string [$rss_link] Link RSS url
			* @param	string [$notes] Link notes
		*/
		
		public static function nl_form($part, $name = '', $link = '', $rss_link = '', $notes = ''){
		
			if($part == 'o'){
			
				echo '<table class="form_table" cellspacing="0">'.
						'<tbody>'.
							'<tr>'.
								'<th>'.
									'<label for="name">Link name <span class="indication">(required)</span></label>'.
								'</th>'.
								'<td>'.
									'<input id="name" class="user_input_text" name="name" type="text" value="'.$name.'" placeholder="LynxPress" required />'.
								'</td>'.
							'</tr>'.
							'<tr>'.
								'<th>'.
									'<label for="url">URL <span class="indication">(required)</span></label>'.
								'</th>'.
								'<td>'.
									'<input id="url" class="user_input_text" name="url" type="url" value="'.$link.'" placeholder="http://lynxpress.org" required />'.
								'</td>'.
							'</tr>'.
							'<tr>'.
								'<th>'.
									'<label for="rss">RSS URL</label>'.
								'</th>'.
								'<td>'.
									'<input id="rss" class="user_input_text" name="rss" type="url" value="'.$link.'" placeholder="http://lynxpress.org/feed.php" />'.
								'</td>'.
							'</tr>'.
							'<tr>'.
								'<th>'.
									'<label for="notes">Notes</label>'.
								'</th>'.
								'<td>'.
									'<textarea id="notes" class="base_txta" name="notes" rows="6" cols="30" wrap="soft" placeholder="Say something about this link">'.$notes.'</textarea>'.
								'</td>'.
							'</tr>'.
							'<tr>'.
								'<th>'.
									'<label for="lvl">Priority</label>'.
								'</th>'.
								'<td>'.
									'<select id="lvl" name="lvl">';
			
			}elseif($part == 'c'){
			
				echo				'</select>'.
								'</td>'.
							'</tr>'.
						'</tbody>'.
					 '</table>';
			
			}
		
		}
		
		/**
			* Display a priority line in a dropdown
			*
			* @static
			* @access	public
			* @param	integer [$id] Id of priority level
			* @param	string [$name] Name of priority level
			* @param	integer [$wanted] Id of priority level stored for the link
		*/
		
		public static function opt_priority($id, $name, $wanted = ''){
		
			echo '<option value="'.$id.'" '.(($wanted == $id)?'selected':'').'>'.$name.'</option>';
		
		}
		
		/**
			* Display add button for a new link
			*
			* @static
			* @access	public
		*/
		
		public static function nl_add(){
		
			echo '<input class="submit button button_publish" type="submit" name="new_link" value="Add New Link" />';
		
		}
		
		/**
			* Display update button to edit a link
			*
			* @static
			* @access	public
		*/
		
		public static function nl_update(){
		
			echo '<input class="submit button button_publish" type="submit" name="update_link" value="Update Link" />';
		
		}
		
		/**
			* Display menu for manage links page
			*
			* @static
			* @access	public
		*/
		
		public static function ml_menu(){
		
			echo '<div id="menu">'.
				 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=links&ctl=manage">Links</a></span>'.
				 	'<span class="menu_item"><a href="index.php?ns=links&ctl=add">Add</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display delete button
			*
			* @static
			* @access	public
		*/
		
		public static function ml_delete(){
		
			echo '<input class="button" type="submit" name="delete" value="Delete" />&nbsp;';
		
		}
		
		/**
			* Display actions for links
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
		*/
		
		public static function ml_actions($part){
		
			if($part == 'o'){
			
				echo '<div id="select_post_status">'.
					 	'<span id="search"><input id="search_input" type="text" name="search" placeholder="Search" list="titles" />'.
					 	'<input class="button" type="submit" name="search_button" value="Search Links" /></span>'.
					 '</div>';
					 self::ml_delete();
				echo '<select name="change_priority">'.
					 	'<option value="no">Change priority to...</option>';
			
			}elseif($part == 'c'){
			
				echo '</select> '.
					 '<input class="button" type="submit" name="change" value="Change" />';
			
			}
		
		}
		
		/**
			* Display links list table
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
								'<th class="column_checkbox" scope="col"><input type="checkbox" name="link[]" /></th>'.
								'<th class="column_name">Name</th>'.
								'<th class="column_link">Link</th>'.
								'<th class="column_rss">RSS Link</th>'.
								'<th class="column_notes">Notes</th>'.
								'<th class="column_priority">Priority</th>'.
							'</tr>'.
						'</thead>'.
						'<tfoot>'.
							'<tr>'.
								'<th class="column_checkbox" scope="col"><input type="checkbox" name="link[]" /></th>'.
								'<th class="column_name">Name</th>'.
								'<th class="column_link">Link</th>'.
								'<th class="column_rss">RSS Link</th>'.
								'<th class="column_notes">Notes</th>'.
								'<th class="column_priority">Priority</th>'.
							'</tr>'.
						'</tfoot>'.
						'<tbody>';
			
			}elseif($part == 'c'){
			
				echo 	'</tbody>'.
					 '</table>';
			
			}
		
		}
		
		/**
			* Display a links table row
			*
			* @static
			* @access	public
			* @param	integer [$id] Link id
			* @param	string [$name] Link name
			* @param	string [$link] Link url
			* @param	string [$rss_link] Link RSS url
			* @param	string [$notes] Link notes
			* @param	string [$priority] Priority level name
		*/
		
		public static function table_row($id, $name, $link, $rss_link, $notes, $priority){
		
			echo '<tr>'.
					'<th class="column_checkbox" scope="row"><input type="checkbox" name="link_id[]" value="'.$id.'" /></th>'.
					'<td class="column_name">'.
						'<a href="index.php?ns=links&ctl=add&action=edit&id='.$id.'" title="Edit “'.$name.'“">'.$name.'</a>'.
						'<div class="row_actions">'.
							'<a href="index.php?ns=links&ctl=add&action=edit&id='.$id.'" title="Edit this item">Edit</a> | '.
							'<a class="red" href="index.php?ns=links&ctl=manage&action=delete&id='.$id.'" title="Delete this item">Delete permanently</a>'.
						'</div>'.
					'</td>'.
					'<td class="column_link">'.
						'<a href="'.$link.'" target="_blank">'.$link.'</a>'.
					'</td>'.
					'<td class="column_rss">'.
						'<a href="'.$rss_link.'" target="_blank">'.$rss_link.'</a>'.
					'</td>'.
					'<td class="column_notes">'.
						htmlspecialchars(nl2br($notes)).
					'</td>'.
					'<td class="column_">'.
						$priority.
					'</td>'.
				'</tr>';
		
		}
	
	}

?>