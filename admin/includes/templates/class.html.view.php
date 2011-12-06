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
	
	namespace Admin\Templates;
	use \Library\Media\Media as Media;
	use \Admin\Html\Html as Master;
	
	/**
		* Html Templates
		*
		* Contains all html for templates classes
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Templates
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html extends Master{
	
		/**
			* Display menu for templates add page
			*
			* @static
			* @access	public
			* @param	boolean [$bool] If the user can access settings
		*/
		
		public static function nt_menu($bool = false){
		
			if($bool){
			
				echo '<div id="menu">'.
					 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
					 	'<span class="menu_item"><a href="index.php?ns=templates&ctl=manage">Templates</a></span>'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=templates&ctl=add">Add</a></span>'.
					 '</div>';
			
			}else{
			
				echo '<div id="menu">'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display form to add a template
			*
			* @static
			* @access	public
		*/
		
		public static function nt_form(){
		
			echo '<h3>Add a template to your website</h3>'.
				 '<div id="new_tpl">'.
				 	'<lable for="tpl">Upload a template archive:</label>&nbsp;&nbsp;&nbsp;&nbsp;<input id="tpl" type="file" name="tpl" />'.
				 	'<input id="upload" class="button button_publish" type="submit" name="upload" value="Upload" /><br/>'.
				 	'<span class="indication">(The maximum upload file size is set to '.Media::max_upload().'MB)</span>'.
				 '</div>';
		
		}
		
		/**
			* Display menu for templates manage page
			*
			* @static
			* @access	public
			* @param	boolean [$bool] If the user can access settings
		*/
		
		public static function mt_menu($bool = false){
		
			if($bool){
			
				echo '<div id="menu">'.
					 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=templates&ctl=manage">Templates</a></span>'.
					 	'<span class="menu_item"><a href="index.php?ns=templates&ctl=add">Add</a></span>'.
					 '</div>';
			
			}else{
			
				echo '<div id="menu">'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display templates action and the current template used
			*
			* @static
			* @access	public
			* @param	string [$template]
		*/
		
		public static function mt_actions($template){
		
			echo '<h3>You\'re currently using: "'.ucwords($template).'"</h3>'.
				 '<input class="button" type="submit" name="delete" value="Delete" />&nbsp;&nbsp;'.
				 '<input class="button" type="submit" name="update" value="Use" />';
		
		}
		
		/**
			* Display a template label
			*
			* @static
			* @access	public
			* @param	integer [$id]
			* @param	string [$name]
			* @param	string [$author]
			* @param	string [$url]
		*/
		
		public static function template_label($id, $name, $author, $url){
		
			echo '<div class="template_label">'.
					'<label for="tpl_'.$id.'">'.
					 	'<div class="check_label">'.
					 		'<input id="tpl_'.$id.'" type="radio" name="tpl_id" value="'.$id.'" />'.
					 	'</div>'.
					'</label>'.
				 	'<div class="content_label">'.
					 	'Name: <span class="tplname">'.$name.'</span><br/>'.
					 	'Author: <span class="tplauthor">'.$author.'</span><br/>'.
					 	'Url : <span class="tplurl"><a href="'.$url.'" target="_blank">'.$url.'</a></span>'.
					'</div>'.
				 '</div>';
		
		}
	
	}

?>