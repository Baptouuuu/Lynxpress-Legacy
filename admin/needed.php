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
	
	define('PATH', '../');
	define('INC', 'includes/');
	
	require_once PATH.INC.'class.install.inc.php';
	
	//If the config file doesn't exist or the database install is not made, redirect to install.php
	if(!file_exists(PATH.'config.php') || (file_exists(PATH.'config.php') && !\Install\Install::check_installed(PATH)))
		header('Location: '.PATH.'install.php');
	
	require_once INC.'class.loader.inc.php';
	\Admin\Loader::load();

?>