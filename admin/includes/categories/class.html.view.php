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
	
	namespace Admin\Categories;
	use \Admin\Html\Html as Master;
	
	/**
		* Html Categories
		*
		* Contains all html for categories class
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html extends Master{
	
		/**
			* Display the menu for categories page
			*
			* @static
			* @access	public
			* @param	boolean [$bool] If $bool is set to true means the user has the right to access settings pages
		*/
		
		public static function menu($bool){
		
			if($bool){
			
				echo '<div id="menu">'.
					 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=categories&ctl=manage">Categories</a></span>'.
					 '</div>';
			
			}else{
			
				echo '<div id="menu">'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php">Dashboard</a></span>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display form to add a new category
			*
			* @static
			* @access	public
		*/
		
		public static function add(){
		
			echo '<div id="new_cat">'.
				 	'<input id="cat_name" class="input" type="text" name="name" value="" placeholder="New category" />&nbsp;'.
				 	'<select name="type">'.
				 		'<option value="no">Choose a type</option>'.
				 		'<option value="album">Album</option>'.
				 		'<option value="post">Post</option>'.
				 		'<option value="video">Video</option>'.
				 	'</select>'.
				 	'&nbsp;<input class="button" type="submit" name="add_cat" value="Add" />'.
				 '</div>';
		
		}
		
		/**
			* Display a button to delete category(ies)
			*
			* @static
			* @access	public
		*/
		
		public static function delete_button(){
		
			echo '<input class="button" type="submit" name="delete" value="Delete" />';
		
		}
		
		/**
			* Display categories table
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
								'<th class="column_checkbox" scope="col"><input type="checkbox" name="category[]" /></th>'.
								'<th class="column_name">Name</th>'.
								'<th class="column_type">Type</th>'.
							'</tr>'.
						'</thead>'.
						'<tfoot>'.
							'<tr>'.
								'<th class="column_checkbox" scope="col"><input type="checkbox" name="category[]" /></th>'.
								'<th class="column_title">Name</th>'.
								'<th class="column_author">Type</th>'.
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
			* @param	integer [$id] Category id
			* @param	string [$name] Category name
			* @param	string [$type] Category type
		*/
		
		public static function table_row($id, $name, $type){
		
			echo '<tr>'.
				 	'<th class="column_checkbox" scope="row"><input type="checkbox" name="category_id[]" value="'.$id.'" /></th>'.
				 	'<td class="column_name">'.
				 		ucwords($name).
				 		'<div class="post_actions"><a class="red" href="index.php?ns=categories&ctl=manage&action=delete&id='.$id.'">Delete Permanently</a></div>'.
				 	'</td>'.
				 	'<td class="column_type">'.ucwords($type).'</td>'.
				 '</tr>';
		
		}
	
	}

?>