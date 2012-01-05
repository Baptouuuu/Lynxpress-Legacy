<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011
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
	
	namespace Site;
	use \Library\Variable\Server as VServer;
	use \Library\Mail\Mail as Mail;
	use Exception;
	
	try{
	
		define('PATH', '');
		define('INC', 'includes/');
		define('ADMIN', 'admin/');
		
		require_once 'config.php';
		require_once INC.'class.loader.inc.php';
		
		Loader::load();
		
		new Session();
		
		$title = '404 Page Not Found';
		$menu = array('Sorry but the Lynx didn\'t show up');
		
		require_once Html::header();
		
		Html::_404();
		
		require_once Html::footer();
		
		$mail = new Mail(WS_EMAIL, '"404 not found reached', str_replace('",', "\",\n",json_encode(VServer::all())));
		$mail->send();
	
	}catch(Exception $e){
	
		die('<h1>'.$e->getMessage().'</h1>');
	
	}

?>