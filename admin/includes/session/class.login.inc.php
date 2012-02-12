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
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Get as VGet;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use Exception;
	
	/**
		* Login
		*
		* Landing page to login into admin panel
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Session
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0.1
		* @final
	*/
	
	final class Login{
	
		protected $_session = null;
		protected $_msg = null;
		protected $_display_html = null;
		
		public function __construct(){
		
			$this->_display_html = false;
			
			if(VGet::loggedout())
				$this->_msg = ActionMessages::custom_good('You\'ve been logged out');
			
			try{
			
				$this->_session = new Session();
				
				if(VPost::login(false))
					$this->_session->login();
			
			}catch(Exception $e){
			
				$this->_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		public function display_content(){
		
			Html::login($this->_msg);
		
		}
		
		public function __get($attr){
		
			if($attr = 'html')
				return $this->_display_html;
			else
				return 'The lynx is not here!';
		
		}
	
	}

?>