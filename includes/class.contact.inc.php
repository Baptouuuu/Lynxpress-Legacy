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
	
	namespace Site;
	use \Library\Variable\Post as VPost;
	use \Library\Mail\Mail as Mail;
	
	/**
		* Contact
		*
		* Handles contact form
		*
		* @package		Site
		* @subpackage	Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0.1
		* @final
	*/
	
	final class Contact extends Master{
	
		private $_result = null;
		private $_users = null;
		const CONTROLLER = true;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			parent::build_title();
			
			$this->_menu = array('You can use this form to contact an author');
			
			$this->get_users();
			
			$this->send();
		
		}
		
		/**
			* Retrieve all users
			*
			* @access	private
		*/
		
		private function get_users(){
		
			$to_read['table'] = 'user';
			$to_read['columns'] = array('user_publicname', 'user_email');
			
			$this->_users = $this->_db->read($to_read);
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			if(VPost::submit(false)){
			
				Html::header_contact();
				Html::contact_submitted($this->_result);
			
			}else{
			
				echo '<form method="post" action="#" accept-charset="utf-8">';
				
				Html::header_contact();
				Html::contact('o');
				
				foreach($this->_users as $user)
					Html::option($user['user_email'], $user['user_publicname']);
				
				Html::contact('c');
				
				echo '</form>';
			
			}
		
		}
		
		/**
			* Send mail to webmaster
			*
			* @access	private
		*/
		
		private function send(){
		
			if(VPost::submit(false)){
			
				if(!VPost::c_email() || !VPost::c_object() || !VPost::c_content()){
				
					$this->_result = false;
				
				}elseif(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , VPost::c_email())){
				
					$this->_result = 'false email';
				
				}else{
				
					$mail = new Mail(VPost::recaiver(), VPost::c_object(), VPost::c_content(), VPost::c_email());
					$mail->send();
					$this->_result = true;
				
				}
			
			}
		
		}
	
	}

?>