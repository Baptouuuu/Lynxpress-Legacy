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
	use Exception;
	
	/**
		* Logout
		*
		* Controller to logout from the admin panel
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Session
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Logout{
	
		protected $_session = null;
		protected $_display_html = null;
		
		public function __construct(){
		
			$this->_display_html = false;
			
			try{
			
				$this->_session = new Session();
				
				$this->_session->logout();
			
			}catch(Exception $e){
			
				die('<h1>'.$e->getMessage().'</h1>');
			
			}
		
		}
		
		public function display_content(){
		
			//no data displayed
		
		}
		
		public function __get($attr){
		
			if($attr = 'html')
				return $this->_display_html;
			else
				return 'The lynx is not here!';
		
		}
	
	}

?>