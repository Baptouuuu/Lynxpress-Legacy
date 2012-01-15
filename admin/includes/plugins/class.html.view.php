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
	
	namespace Admin\Plugins;
	use \Admin\Html\Html as Master;
	use \Library\Variable\Post as VPost;
	
	/**
		* Html Plugins
		*
		* Contains all html for plugins classes
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Plugins
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html extends Master{
	
		/**
			* Display menu for new plugin page
			*
			* @static
			* @access	public
		*/
		
		public static function ap_menu(){
		
			echo '<div id="menu">'.
				 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
				 	'<span class="menu_item"><a href="index.php?ns=plugins&ctl=manage">Plugins</a></span>'.
				 	'<span class="menu_item"><a href="index.php?ns=plugins&ctl=library">Library</a></span>'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=plugins&ctl=add">Add</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Form to add a plugin
			*
			* @static
			* @access	public
		*/
		
		public static function ap_form(){
		
			echo '<h3>Add a plugin to your website</h3>'.
				 '<div id="new_plg">'.
				 	'<lable for="plg">Upload a plugin archive:</label>&nbsp;&nbsp;&nbsp;&nbsp;<input id="plg" type="file" name="plg" />'.
				 	'<input id="upload" class="button button_publish" type="submit" name="upload" value="Upload" />'.
				 '</div>';
		
		}
		
		/**
			* Display menu for plugin management page
			*
			* @static
			* @access	public
		*/
		
		public static function mp_menu(){
		
			echo '<div id="menu">'.
				 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=plugins&ctl=manage">Plugins</a></span>'.
				 	'<span class="menu_item"><a href="index.php?ns=plugins&ctl=library">Library</a></span>'.
				 	'<span class="menu_item"><a href="index.php?ns=plugins&ctl=add">Add</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display plugins action
			*
			* @static
			* @access	public
		*/
		
		public static function mp_actions(){
		
			echo '<h3>This is the list of all installed plugins</h3>'.
				 '<input class="button" type="submit" name="delete" value="Delete" />';
		
		}
		
		/**
			* Display a plugin label
			*
			* @static
			* @access	public
			* @param	integer [$id]
			* @param	string [$name]
			* @param	string [$author]
			* @param	string [$url]
		*/
		
		public static function plg_label($id, $name, $author, $url){
		
			echo '<div class="template_label">'.
					'<label for="plg_'.$id.'">'.
					 	'<div class="check_label">'.
					 		'<input id="plg_'.$id.'" type="radio" name="plg_id" value="'.$id.'" />'.
					 	'</div>'.
					'</label>'.
				 	'<div class="content_label">'.
					 	'Name: <span class="tplname">'.$name.'</span><br/>'.
					 	'Author: <span class="tplauthor">'.$author.'</span><br/>'.
					 	'Url : <span class="tplurl"><a href="'.$url.'" target="_blank">'.$url.'</a></span>'.
					'</div>'.
				 '</div>';
		
		}
		
		
		
		/**
			* Display menu for plugin bridge page
			*
			* @static
			* @access	public
		*/
		
		public static function bp_menu(){
		
			echo '<div id="menu">'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=plugins&ctl=bridge">Plugins</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display a plugin link label
			*
			* @static
			* @access	public
			* @param	string [$name]
			* @param	string [$namespace]
			* @param	string [$entry_point]
		*/
		
		public static function plg_link_label($name, $namespace, $entry_point){
		
			echo '<div class="setting_label">'.
					'<div class="label_img label_plugins">'.
						'&nbsp;'.
					'</div>'.
					'<div class="label_name">'.
						'<a href="index.php?ns='.$namespace.'&ctl='.$entry_point.'">'.$name.'</a>'.
					'</div>'.
				'</div>';
		
		}
		
		/**
			* Display menu for plugin library page
			*
			* @static
			* @access	public
		*/
		
		public static function lib_menu(){
		
			echo '<div id="menu">'.
				 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
				 	'<span class="menu_item"><a href="index.php?ns=plugins&ctl=manage">Plugins</a></span>'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=plugins&ctl=library">Library</a></span>'.
				 	'<span class="menu_item"><a href="index.php?ns=plugins&ctl=add">Add</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display plugins action in library
			*
			* @static
			* @access	public
		*/
		
		public static function lib_actions(){
		
			echo '<div id="lib_plg_act">'.
					 '<h3>This is the list of plugins registered on lynxpress.org</h3>'.
					 '<input id="search_input" type="text" name="search" value="'.VPost::search().'" placeholder="Search" />'.
					 '<input class="button" type="submit" name="search_button" value="Search Plugins" />'.
				 '</div>';
		
		}
		
		/**
			* Display a library plugin label
			*
			* @static
			* @access	public
			* @param	string [$user] Github user name
			* @param	string [$repo] Github repository
			* @param	string [$download] Filename to download
			* @param	string [$dec] Plugin description
			* @param	string [$website] Plugin website
		*/
		
		public static function lib_plg_label($user, $repo, $download, $desc, $website){
		
			echo '<div class="lib_plg_label">'.
					'<div class="content_label">'.
					 	'Author: <span class="plgauthor">'.$user.'</span><br/>'.
					 	'Url: <span class="plgurl"><a href="'.$website.'" target="_blank">'.$website.'</a></span><br/>'.
					 	'Description: <p class="plgdesc">'.$desc.'</p><br/>'.
					 	'<a href="http://github.com/'.$user.'/'.$repo.'" target="_blank">View on Github</a> | '.
					 	'<a class="green" href="index.php?ns=plugins&ctl=library&action=install&user='.$user.'&repo='.$repo.'&download='.$download.'">Install</a>'.
					'</div>'.
				 '</div>';
		
		}
	
	}

?>