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
	use \Library\Variable\Session as VSession;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Library\File\File as File;
	
	/**
		* Cache
		*
		* Generate cache of the frontend
		*
		* @package		Site
		* @subpackage	Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
	*/
	
	class Cache{
	
		private $_url = null;
		private $_exist = null;
		private $_post = null;
		private $_get = null;
		const LIFETIME = 60;		//cache lifetime set to 1 minute
		const CONTROLLER = false;
		const ACTIVATED = true;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			$this->_url = 'cache/';
			
			$this->_post = VPost::all(false);
			$this->_get = VGet::all(false);
			
			$this->build_url();
			$this->exist();
		
		}
		
		/**
			* Determine cache filename
			*
			* @access	private
		*/
		
		private function build_url(){
		
			if(self::ACTIVATED === false)
				return false;
			
			if(VSession::renderer() == 'mobile')
				$this->_url .= 'mobile';
			elseif(VSession::html5())
				$this->_url .= 'html5';
			else
				$this->_url .= 'html';
			
			$this->_url .= '-ctl-'.VGet::ctl();
			
			if(VGet::ctl() == 'albums' && VGet::album() && !VGet::comments())
				$this->_url .= '-album-'.VGet::album();
			elseif(VGet::ctl() == 'search')
				foreach(VGet::all() as $key => $value)
					if($key != 'ctl')
						$this->_url .= '-'.$key.'-'.$value;
				
		
		}
		
		/**
			* Determine if cache file exists
			*
			* By default only the landing page of controllers are cached
			* This  restriction is due to some personal informations that
			* may be cached by accident (i.e when a comment form is submitted)
			* if all pages was cached
			*
			* There are two execptions: albums and search
			* An album is also cached because there's no personal informations
			* However albums comments page is not cached
			* All search pages are cached, no sensible informations concerned
			* with this controller
			*
			* Cache is not activated after a POST form
			*
			* @access	private
		*/
		
		private function exist(){
		
			if(self::ACTIVATED === false){
			
				$this->_exist = false;
				return;
			
			}
			
			$expiration = time()-self::LIFETIME;
			
			if((!empty($this->_post) || count($this->_get) > 1) && !in_array(VGet::ctl(), array('search', 'albums')))
				$this->_exist = false;
			elseif(VGet::ctl() == 'albums' && VGet::comments())
				$this->_exist = false;
			elseif(file_exists($this->_url) && filemtime($this->_url) > $expiration)
				$this->_exist = true;
			else
				$this->_exist = false;
				
		}
		
		/**
			* Build cache
			*
			* Exceptions of the exist method continue here
			* So cache generation is stopped if its not a landing page or
			* when we are on an album comments page and also when a form
			* has been submitted
			*
			* @access	public
			* @param	string [$action] Can be "s" or "e" (meaning start or end)
		*/
		
		public function build($action){
		
			if(self::ACTIVATED === false)
				return false;
			
			if((!empty($this->_post) || count($this->_get) > 1) && !in_array(VGet::ctl(), array('search', 'albums')))
				return true;
			elseif(VGet::ctl() == 'albums' && VGet::comments())
				return true;
			
			if($action == 's'){
			
				ob_start();
			
			}elseif($action == 'e'){
			
				$content = ob_get_contents();
				
				ob_end_flush();
				
				$cache = new File();
				$cache->_content = $content;
				$cache->save($this->_url);
			
			}else{
			
				throw new Exception('Unknown cache command');
			
			}
		
		}
				
		/**
			* Method to get class attribute
			*
			* @access	public
			* @param	string [$attr]
			* @return	mixed
		*/
		
		public function __get($attr){
		
			if(in_array($attr, array('_url', '_exist')))
				return $this->$attr;
			else
				return 'The lynx is not here!';
		
		}
	
	}

?>