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
	use \Library\Database\Database as Database;
	use \Library\Variable\Cookie as VCookie;
	use \Library\Variable\Session as VSession;
	use \Library\Variable\Server as VServer;
	
	/**
		* Session
		*
		* Session handle the browser with its version and if it supports html5
		*
		* Permits to know via VSession::html5() it the user can handle HTML 5
		*
		* And via VSession::renderer() you will know the browser renderer engine
		*
		* @package	Site
		* @author	Baptiste Langlade lynxpressorg@gmail.com
		* @version	1.0.1
		* @final
	*/
	
	final class Session{
	
		private $_db = null;
		private $_html5 = null;
		private $_renderer = null;
		private $_forbidden = null;
		private $_bots = array('Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Googlebot/2.1 (+http://www.googlebot.com/bot.html)', 'Googlebot/2.1 (+http://www.google.com/bot.html)', 'Googlebot-Image/1.0', 'msnbot/2.1', 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)', 'YahooSeeker/1.2 (compatible; Mozilla 4.0; MSIE 5.5; yahooseeker at yahoo-inc dot com ; http://help.yahoo.com/help/us/shop/merchant/)');
		const CONTROLLER = false;
		
		/**
			* Session constructor
			*
			* Check if cookie or session variables exists to retrieve directly informations
			*
			* Otherwise it calls get_browser method
			*
			* @access public
		*/
		
		public function __construct(){
		
			session_start();
			
			$this->_db =& Database::load();
			
			if(VCookie::lynxpress()){
			
				$array = json_decode(stripslashes(VCookie::lynxpress()), true);
				
				$_SESSION['html5'] = $array['html5'];
				$_SESSION['renderer'] = $array['renderer'];
				$this->_html5 = VSession::html5();
				$this->_renderer = VSession::renderer();
			
			}elseif(!VSession::html5() && !VSession::renderer()){
			
				$this->get_browser();
				$_SESSION['html5'] = $this->_html5;
				$_SESSION['renderer'] = $this->_renderer;
				setcookie('lynxpress', json_encode(array('html5' => $this->_html5, 'renderer' => $this->_renderer)), time()+(365*24*60*60));
			
			}else{
			
				$this->_html5 = VSession::html5();
				$this->_renderer = VSession::renderer();
			
			}
		
		}
		
		/**
			* Method to get informations about the browser and stock them into attributes
			*
			* @access private
		*/
		
		private function get_browser(){
		
			$iphone = strpos(VServer::HTTP_USER_AGENT(), 'iPhone;');
			$android = strpos(VServer::HTTP_USER_AGENT(), 'Android');
			$ipad = strpos(VServer::HTTP_USER_AGENT(), 'iPad');
			$webkit = strpos(VServer::HTTP_USER_AGENT(), 'AppleWebKit/');
			$gecko = strpos(VServer::HTTP_USER_AGENT(), 'Firefox/');
			$presto = strpos(VServer::HTTP_USER_AGENT(), 'Presto/');
			$trident = strpos(VServer::HTTP_USER_AGENT(), 'Trident/'); 
			
			if($iphone !== false || $android !== false || $ipad !== false){
				
				$this->_renderer = 'mobile';
				$this->_html5 = false;
				
			}else{
				
				if($webkit !== false){
					
					$webkit_version = substr(VServer::HTTP_USER_AGENT(), $webkit, 20);
					
					if($webkit_version >=  'AppleWebKit/533.18.1')
						$this->_html5 = true;
					else
						$this->_html5 = false;
					
					$this->_renderer = 'webkit';
				
				}elseif($gecko !== false){
				
					$gecko_version = substr(VServer::HTTP_USER_AGENT(), $gecko, 9);
					$ff10up = substr(VServer::HTTP_USER_AGENT(), $gecko, 10);
					
					if($gecko_version >= 'Firefox/4')
						$this->_html5 = true;
					elseif($ff10up >= 'Firefox/10')
						$this->_html5 = true;
					else
						$this->_html5 = false;
					
					$this->_renderer = 'gecko';
				
				}elseif($presto !== false){
				
					$this->_html5 = false;
					$this->_renderer = 'presto';
				
				}elseif($trident !== false){
				
					$this->_html5 = false;
					$this->_renderer = 'trident';
				
				}elseif(in_array(VServer::HTTP_USER_AGENT(), $this->_bots)){
					
					$this->_html5 = true;
					$this->_renderer = 'bot';
				
				}else{
				
					$this->_html5 = false;
					$this->_renderer = 'unknown';
				
				}
			}	
		
		}
		
		/**
			* Method to get html5 or renderer attributes
			*
			* @access	public
			* @param	string [$attr]
			* @return	string
		*/
		
		public function __get($attr){
		
			if(in_array($attr, array('_html5', '_renderer')))
				return $this->$attr;
			else
				return 'The Lynx is not here!';
		
		}
	
	}

?>