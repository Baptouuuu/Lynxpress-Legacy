<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011-2012
		* @license		http://www.gnu.org/licenses/gpl.html GNU GPL V3
		* @package		Lynxpress
		* @subpackage	Site
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
	
	/**
		* This is the main page of public part of the website
		*
		* Page object is created in function of the pid url parameter inside the switch
		*
		* If there's no param, the list of posts is made thanks to the Posts class
		*
		*
		* If you want to add a public feature, just add the wished pid inside the switch
		*
		* All the content you want to display has to be called inside the display_content
		*
		* method inside your object
	*/
	
	namespace Site;
	use \Library\Variable\Get as VGet;
	use Exception;
	
	define('PATH', '');
	define('ADMIN', 'admin/');
	define('INC', 'includes/');
	
	try{
	
		require_once INC.'class.install.inc.php';
		
		//If the config file doesn't exist or the database install is not made, redirect to install.php
		if(!file_exists('config.php') || (file_exists('config.php') && !\Install\Install::check_installed()))
			header('Location: install.php');
		
		require_once 'config.php';
		require_once INC.'class.loader.inc.php';
		
		Loader::load();
		
		$controller = '\\Site\\'.ucfirst(VGet::ctl('defaultpage'));
		
		//forbidden classes
		if($controller::CONTROLLER === false)
			throw new Exception('Unknown controllers');
		
		new Session();
		
		$page = new $controller();
		
		$cache = new Cache();
		
		if($cache->_exist === false){
		
			$cache->build('s');
			
			$title = $page->_title;
			$menu = $page->_menu;
			
			require_once Html::header();
			
			$page->display_content();
			
			require_once Html::footer();
			
			$cache->build('e');
		
		}else{
		
			readfile($cache->_url);
		
		}
	
	}catch(Exception $e){
	
		header('Location: 404.php');
	
	}

?>