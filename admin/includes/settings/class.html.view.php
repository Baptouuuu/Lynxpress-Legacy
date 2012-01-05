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
	
	namespace Admin\Settings;
	
	/**
		* Html Settings
		*
		* Contains all html for settings class
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Settings
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html{
	
		/**
			* Display menu for setting page
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
				 	'<span  id="menu_selected" class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display categories link
			*
			* @static
			* @access	public
		*/
		
		public static function categories(){
		
			echo '<div class="setting_label">'.
					'<div id="label_categories" class="label_img">'.
						'&nbsp;'.
					'</div>'.
					'<div class="label_name">'.
						'<a href="index.php?ns=categories&ctl=manage">Categories</a>'.
					'</div>'.
				'</div>';
		
		}
		
		/**
			* Display users link
			*
			* @static
			* @access	public
		*/
		
		public static function users(){
		
			echo '<div class="setting_label">'.
					'<div id="label_users" class="label_img">'.
						'&nbsp;'.
					'</div>'.
					'<div class="label_name">'.
						'<a href="index.php?ns=users&ctl=manage" title="Manage users">Users</a>'.
					'</div>'.
				'</div>';
		
		}
		
		/**
			* Display links link
			*
			* @static
			* @access	public
		*/
		
		public static function links(){
		
			echo '<div class="setting_label">'.
					'<div id="label_links" class="label_img">'.
						'&nbsp;'.
					'</div>'.
					'<div class="label_name">'.
						'<a href="index.php?ns=links&ctl=manage" title="Manage links">Links</a>'.
					'</div>'.
				'</div>';
		
		}
		
		/**
			* Display social link
			*
			* @static
			* @access	public
		*/
		
		public static function social(){
		
			echo '<div class="setting_label">'.
					'<div id="label_social" class="label_img">'.
						'&nbsp;'.
					'</div>'.
					'<div class="label_name">'.
						'<a href="index.php?ns=social&ctl=manage" title="Manage share buttons">Social</a>'.
					'</div>'.
				'</div>';
		
		}
		
		/**
			* Display user roles link
			*
			* @static
			* @access	public
		*/
		
		public static function roles(){
		
			echo '<div class="setting_label">'.
					'<div id="label_roles" class="label_img">'.
						'&nbsp;'.
					'</div>'.
					'<div class="label_name">'.
						'<a href="index.php?ns=roles&ctl=manage" title="Manage user roles">User Roles</a>'.
					'</div>'.
				'</div>';
		
		}
		
		/**
			* Display activity link
			*
			* @static
			* @access	public
		*/
		
		public static function activity(){
		
			echo '<div class="setting_label">'.
					'<div id="label_activity" class="label_img">'.
						'&nbsp;'.
					'</div>'.
					'<div class="label_name">'.
						'<a href="index.php?ns=activity&ctl=manage" title="View all activity history">Activity</a>'.
					'</div>'.
				'</div>';
		
		}
		
		/**
			* Display posts settings link
			*
			* @static
			* @access	public
		*/
		
		public static function post(){
		
			echo '<div class="setting_label">'.
					'<div id="label_post" class="label_img">'.
						'&nbsp;'.
					'</div>'.
					'<div class="label_name">'.
						'<a href="index.php?ns=posts&ctl=settingpage">Posts</a>'.
					'</div>'.
				'</div>';
		
		}
		
		/**
			* Display default page settings link
			*
			* @static
			* @access	public
		*/
		
		public static function default_page(){
		
			echo '<div class="setting_label">'.
					'<div id="label_default_page" class="label_img">'.
						'&nbsp;'.
					'</div>'.
					'<div class="label_name">'.
						'<a href="index.php?ns=defaultpage&ctl=manage">Default page</a>'.
					'</div>'.
				'</div>';
		
		}
		
		/**
			* Display template settings link
			*
			* @static
			* @access	public
		*/
		
		public static function template(){
		
			echo '<div class="setting_label">'.
				 	'<div id="label_template" class="label_img">'.
				 		'&nbsp;'.
				 	'</div>'.
				 	'<div class="label_name">'.
				 		'<a href="index.php?ns=templates&ctl=manage">Templates</a>'.
				 	'</div>'.
				 '</div>';
		
		}
		
		/**
			* Display update setting link
			*
			* @static
			* @access	public
		*/
		
		public static function update(){
		
			echo '<div class="setting_label">'.
					'<div id="label_update" class="label_img">'.
						'&nbsp;'.
					'</div>'.
					'<div class="label_name">'.
						'<a href="index.php?ns=update&ctl=manage" title="Update your Lynxpress">Update</a>'.
					'</div>'.
				'</div>';
		
		}
		
		/**
			* Display plugins setting link
			*
			* @static
			* @access	public
		*/
		
		public static function plugins(){
		
			echo '<div class="setting_label">'.
					'<div class="label_img label_plugins">'.
						'&nbsp;'.
					'</div>'.
					'<div class="label_name">'.
						'<a href="index.php?ns=plugins&ctl=manage">Plugins</a>'.
					'</div>'.
				'</div>';
		
		}
	
	}

?>