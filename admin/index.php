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
	
	require_once 'needed.php';
	
	use \Library\Variable\Get as VGet;
		
	try{
	
		$controller = '\\Admin\\'.ucfirst(VGet::ns('dashboard')).'\\'.ucfirst(VGet::ctl('manage'));
		
		$page = new $controller();
		
		if($page->html === true)
			require_once INC.'html/header.php';
		
		$page->display_content();
		
		if($page->html === true)
			require_once INC.'html/footer.html';
	
	}catch(Exception $e){
	
		die('<h1>'.$e->getMessage().'</h1>');
	
	}

?>