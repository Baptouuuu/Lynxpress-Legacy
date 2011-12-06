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
	
	require_once 'needed.php';
		
	use \Library\Variable\Post as VPost;
		
	try{
	
		$session = new \Admin\Session\Session();
		
		if(VPost::login(false))
			$session->login();
	
	}catch(Exception $e){
	
		die('<h1>'.$e->getMessage().'</h1>');
	
	}

?>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Connexion to <?php echo WS_NAME ?></title>
		<link rel="index" href="<?php echo WS_URL ?>" title="<?php echo WS_NAME ?>" />
		<link rel="icon" type="image/png" href="<?php echo PATH ?>images/lynxpress-mini.png" />
		<link rel="stylesheet" type="text/css" href="<?php echo PATH ?>css/admin.css" />

	</head>

	<body>
	
		<header>
			<nav>
				<a href="../">Back to website</a>
			</nav>
		</header>
		
		<form method="POST" action="#">
			<div id="login">
				<h2>Connexion</h2>
				<br />
				<input class="login" type="text" name="login" placeholder="Username" required /><br />
				<br />
				<input class="login" type="password" name="password" placeholder="Password" required /><br />
				<br />
				<input class="submit_login" type="submit" name="" value="Connexion" />
			</div>
		</form>
	
	</body>
</html>