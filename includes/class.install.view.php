<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011
		* @license		http://www.gnu.org/licenses/gpl.html GNU GPL V3
		* @package		Lynxpress
		* @subpackage	Installation
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
	
	namespace Install;
	
	/**
		* Html
		*
		* Class contains installation views
		*
		* @package		Installation
		* @subpackage	Views
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html{
	
		/**
			* Display html header
			*
			* @static
			* @access	public
		*/
		
		public static function header(){
		
			echo '<!DOCTYPE html >'.
			 	 '<html xmlns="http://www.w3.org/1999/xhtml">'.
			 		'<head>'.
			 			'<meta charset="utf-8" />'.
			 			'<title>Installation | Lynxpress</title>'.
			 			'<link rel="icon" type="image/png" href="images/lynxpress-mini.png" />'.
			 			'<link rel="stylesheet" type="text/css" href="css/install.css" />'.
			 		'</head>'.
			 		'<body>';
		
		}
		
		/**
			* Display installation message
			*
			* @static
			* @access	public
		*/
		
		public static function header_installation(){
		
			echo '<header>'.
				 	'<img src="images/lynxpress_install.png" alt="" />'.
				 	'<h3>Lynxpress</h3>'.
				 	'<p>'.
				 		'Welcome to the Lynxpress installation page! You have been redirected here because the configuration file doesn\'t exist.<br/>'.
				 		'During this installation you will need to know:'.
			 		'</p>'.
			 		'<ol>'.
			 			'<li>Database host</li>'.
			 			'<li>Database name</li>'.
			 			'<li>Database username</li>'.
			 			'<li>Database password</li>'.
			 			'<li>Database prefix (if you want to run multiple Lynxpress blogs in one database)</li>'.
			 			'<li>Website name</li>'.
			 			'<li>Website url (Important: url has to end with a "/")</li>'.
			 			'<li>Website owner e-mail address</li>'.
			 		'</ol>'.
			 		'<p>'.
			 			'If you have been redirected here and have already made this installation, it means that your config.php has been deleted. In this case, see the config.sample.php file to make a new configuration file.'.
			 		'</p>'.
				 '</header>';
		
		}
		
		/**
			* Display database informations form
			*
			* @static
			* @access	public
		*/
		
		public static function database(){
		
			echo '<section class="section">'.
				 	'<h3>Database Creation</h3>'.
				 	'<span class="label"><label for="db_host">Host</label></span> <input id="db_host" class="input" type="text" name="db_host" value="localhost" placeholder="Server hosting sql database" required /><br/>'.
				 	'<span class="label"><label for="db_name">Name</label></span> <input id="db_name" class="input" type="text" name="db_name" value="lynxpress" placeholder="Database name" required /><br/>'.
				 	'<span class="label"><label for="db_user">Username</label></span> <input id="db_user" class="input" type="text" name="db_user" value="username" placeholder="Sql server username" required /><br/>'.
				 	'<span class="label"><label for="db_pwd">Password</label></span> <input id="db_pwd" class="input" type="text" name="db_pwd" value="password" placeholder="Sql server password" required /><br/>'.
				 	'<span class="label"><label for="db_prefix">Prefix</label></span> <input id="db_prefix" class="input" type="text" name="db_prefix" value="lynx_" placeholder="Sql table prefix" />'.
				 '</section>';
		
		}
		
		/**
			* Display website informations form
			*
			* @static
			* @access	public
		*/
		
		public static function website(){
		
			echo '<section class="section">'.
				 	'<h3>Website</h3>'.
				 	'<span class="label"><label for="ws_name">Name</label></span> <input id="ws_name" class="input" type="text" name="ws_name" value="Lynxpress" placeholder="Website name" required /><br/>'.
				 	'<span class="label"><label for="ws_url">Url</label></span> <input id="ws_url" class="input" type="url" name="ws_url" placeholder="http://www.lynx.com/blog/" required /><br/>'.
				 	'<span class="label"><label for="ws_email">E-Mail</label></span> <input id="ws_email" class="input" type="email" name="ws_email" placeholder="example@lynxpress.org" required />'.
				 '</section>';
		
		}
		
		/**
			* Display login informations form
			*
			* @static
			* @access	public
		*/
		
		public static function login(){
		
			echo '<section class="section">'.
				 	'<h3>Login</h3>'.
				 	'<span class="label"><label for="username">Username</label></span> <input id="username" class="input" type="text" name="username" value="admin" placeholder="Login username" required /><br/>'.
				 	'<span class="label"><label for="password">Password</label></span> <input id="password" class="input" type="password" name="password" value="password" placeholder="Login password" required />'.
				 '</section>';
		
		}
		
		/**
			* Display install button
			*
			* @static
			* @access	public
			* @param	string [$name]
		*/
		
		public static function install($name = 'install'){
		
			echo '<section class="section">'.
				 	'<input id="install" type="submit" name="'.$name.'" value="Install Lynxpress" />'.
				 '</section>';
		
		}
		
		/**
			* Display configuration file error message
			*
			* @static
			* @access	public
			* @param	string [$conf] PHP code for config.php
		*/
		
		public static function config_error($conf){
		
			echo '<header>'.
					'<img src="images/lynxpress_install.png" alt="" />'.
					'<h3>Lynxpress > Installation error</h3>'.
					'<p class="message">'.
						'Lynxpress can\'t create your config.php file. Make sure you have the rights to write on this file and <a class="ta" href="install.php">try again</a>.<br/>'.
						'Or you can create it yourself with the following code and run the <a class="ta" href="install.php">install</a>.'.
					'</p>'.
					'<textarea>'.$conf.'</textarea>'.
				 '</header>';
		
		}
		
		/**
			* Display database creation error message
			*
			* @static
			* @access	public
		*/
		
		public static function create_error(){
		
			echo '<header>'.
					'<img src="images/lynxpress_install.png" alt="" />'.
					'<h3>Lynxpress > Installation error</h3>'.
					'<p class="message">'.
						'Lynxpress can\'t create your database. Make sure the given informations are correct and <a class="ta" href="install.php">try again</a>.<br/>'.
						'If the problem persists, check you have the rights CREATE to your database and verify the table prefix is not already used.'.
					'</p>'.
				 '</header>';
		
		}
		
		/**
			* Display unknown error message
			*
			* @static
			* @access	public
		*/
		
		public static function unknown_error(){
		
			echo '<header>'.
					'<img src="images/lynxpress_install.png" alt="" />'.
					'<h3>Lynxpress > Installation error</h3>'.
					'<p class="message">'.
						'An unknown error occured during Lynxpress installation. Please <a class="ta" href="install.php">try again</a>.'.
					'</p>'.
				 '</header>';
		
		}
		
		/**
			* Display success intallation message
			*
			* @static
			* @access	public
		*/
		
		public static function success(){
		
			echo '<header>'.
					'<img src="images/lynxpress_install.png" alt="" />'.
					'<h3>Lynxpress > Installation successfull</h3>'.
					'<p class="message">'.
						'You\'re Lynxpress blog is now installed, now you can loggin with your given username and password and start blogging.<br/>'.
						'<a id="start" href="admin/login.php">Start</a>'.
					'</p>'.
				 '</header>';
		
		}
		
		/**
			* Display html footer
			*
			* @static
			* @access	public
		*/
		
		public static function footer(){
		
			echo	'</body>'.
				 '</html>';
		
		}
	
	}

?>