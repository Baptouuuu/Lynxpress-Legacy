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
	
	namespace Admin\Session;
	use \Admin\Html\Html as Master;
	
	/**
		* Html for session classes
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Session
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0.1
		* @abstract
	*/
	
	abstract class Html extends Master{
	
		/**
			* Display login form
			*
			* @static
			* @access	public
			* @param	string [$msg] Error message
		*/
		
		public static function login($msg){
		
			echo '<!DOCTYPE html>'.
				 	'<html xmlns="http://www.w3.org/1999/xhtml">'.
				 		'<head>'.
							'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.
							'<title>Connexion to '.WS_NAME.'</title>'.
							'<link rel="index" href="'.WS_URL.'" title="'.WS_NAME.'" />'.
							'<link rel="icon" type="image/png" href="'.PATH.'images/lynxpress-mini.png" />'.
							'<link rel="stylesheet" type="text/css" href="'.PATH.'css/admin.css" />'.
						'</head>'.
						'<body>'.
							'<header>'.
								'<nav>'.
									'<a href="../">Back to website</a>'.
								'</nav>'.
							'</header>'.
							'<section id="wrapper">'.
								'<form method="post" action="#">'.
									'<div id="login">'.
										'<h2>Connexion</h2>'.
										$msg.
										'<br />'.
										'<input class="input" type="text" name="login" placeholder="Username" autofocus required /><br />'.
										'<br />'.
										'<input class="input" type="password" name="password" placeholder="Password" required /><br />'.
										'<br />'.
										'<input class="submit_login" type="submit" name="" value="Connexion" />'.
									'</div>'.
								'</form>'.
							'</section>'.
						'</body>'.
					'</html>';
		
		}
	
	}

?>