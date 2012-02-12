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
	
	namespace Admin\DefaultPage;
	use \Admin\Html\Html as Master;
	
	/**
		* Html Default Page
		*
		* Contains all html for DefaultPage class
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	DefaultPage
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0.1
		* @abstract
	*/
	
	abstract class Html extends Master{
	
		/**
			* Display menu for default page setting page
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=defaultpage&ctl=manage">Default Page</a></span>'.
				 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display default page type selection form
			*
			* @static
			* @access	public
			* @param	array [$data]
		*/
		
		public static function selection_type($data){
		
			echo '<h3>Default page type</h3>'.
				 '<input class="button button_publish submit" type="submit" name="update" value="Update" /><br/>'.
				 '<div id="labels">'.
				 	'<div class="setting_label">'.
				 		'<div class="label_img">'.
				 			'<input id="post" type="radio" name="type" value="posts" '.(($data['type'] == 'posts')?'checked':'').' />'.
				 		'</div>'.
				 		'<div class="label_name">'.
				 			'<label for="post">Blog</label>'.
				 		'</div>'.
				 	'</div>'.
				 	'<div class="setting_label">'.
				 		'<div class="label_img">'.
				 			'<input id="pic" type="radio" name="type" value="albums" '.(($data['type'] == 'albums')?'checked':'').' />'.
				 		'</div>'.
				 		'<div class="label_name">'.
				 			'<label for="pic">Albums</label>'.
				 		'</div>'.
				 	'</div>'.
				 	'<div class="setting_label">'.
				 		'<div class="label_img">'.
				 			'<input id="vid" type="radio" name="type" value="video" '.(($data['type'] == 'video')?'checked':'').' />'.
				 		'</div>'.
				 		'<div class="label_name">'.
				 			'<label for="vid">Videos</label>'.
				 		'</div>'.
				 	'</div>'.
				 '</div>';
		
		}
		
		/**
			* Display content structure
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain 'o' or 'c'
			* @param	string [$legend]
		*/
		
		public static function content($part, $legend = ''){
		
			if($part == 'o'){
			
				echo '<h3>Select a specific item</h3>'.
					 '<fieldset id="cats">'.
					 	'<legend>'.$legend.'</legend>';
			
			}elseif($part == 'c'){
			
				echo '</fieldset>';
			
			}
		
		}
		
		/**
			* Display an item in content structure
			*
			* @static
			* @access	public
			* @param	mixed [$value]
			* @param	string [$title]
			* @param	string [$chosen] Item saved
		*/
		
		public static function content_line($value, $title, $chosen){
		
			echo '<span class="acat"><input id="view'.$value.'" type="radio" name="view" value="'.$value.'" '.(($value == $chosen)?'checked':'').' /> <label for="view'.$value.'">'.$title.'</label></span>';
		
		}
	
	}

?>