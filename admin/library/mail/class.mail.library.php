<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011-2012
		* @license		http://www.gnu.org/licenses/gpl.html GNU GPL V3
		* @package		Lynxpress
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
	
	namespace Library\Mail;
	
	/**
		* Mail
		*
		* Used to send Mail
		*
		* @package		Library
		* @subpackage	Mail
		* @namespace	Mail
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
	*/
	
	class Mail{
	
		private $_receivers = null;
		private $_sender = null;
		private $_subject = null;
		private $_message = null;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	mixed [$receivers] String or array accepted
			* @param	string [$subject]
			* @param	string [$message]
			* @param	string [$sender]
		*/
		
		public function __construct($receivers = '', $subject = '', $message = '', $sender = WS_EMAIL){
		
			$this->_receivers = $receivers;
			$this->_subject = $subject;
			$this->_message = $message;
			$this->_sender = $sender;
		
		}
		
		/**
			* Send mail
			*
			* @access	public
		*/
		
		public function send(){
		
			$headers = 'From: '.$this->_sender."\r\n".
					   'Reply-to: '.$this->_sender."\r\n".
					   'X-Mailer: php/'.phpversion();
			
			if(is_array($this->_receivers))
				implode(', ', $this->_receivers);
			
			mail($this->_receivers, $this->_subject, $this->_message, $headers);
		
		}
		
		/**
			* Set a value to an attribute
			*
			* @access	public
			* @param	string [$attr] Attribute name
			* @param	mixed [$value] Attribute value to set
		*/
		
		public function __set($attr, $value){
		
			$this->$attr = $value;
		
		}
		
		/**
			* Get value of an attribute
			*
			* @access	public
			* @param	string [$attr] Attribute name
			* @return	mixed Attribute value
		*/
		
		public function __get($attr){
		
			return $this->$attr;
		
		}
	
	}

?>