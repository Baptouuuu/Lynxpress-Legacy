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
	use \Library\Database\Database as Database;
	use \Library\Variable\Get as VGet;
	
	/**
		* Master
		*
		* Master load the database instance and determine some informations about the page viewed
		*
		* @package		Site
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Master{
	
		protected $_db = null;
		protected $_pid = null;
		protected $_sql_table = null;
		protected $_limit_start = null;
		protected $_page = null;
		protected $_title = null;
		protected $_menu = array();
		protected $_content = null;
		const ITEMS_PAGE = 10;

		/**
			* Class constructor
			*
			* Load the database instance and determine some informations about the page viewed
			*
			* This contructor has to be called in each child class before any actions
			*
			* @access	protected
		*/
		
		protected function __construct(){
		
			$this->_db =& Database::load();
			$this->pid();
			$this->page();
		
		}
		
		/**
			* Method to determine pid and sql table of the page
			*
			* @access private
		*/
		
		private function pid(){
		
			switch(VGet::ctl('posts')){
			
				case 'albums':
					$this->_pid = 'albums';
					$this->_sql_table = 'media';
					break;
				
				case 'video':
					$this->_pid = 'video';
					$this->_sql_table = 'media';
					break;
				
				case 'author':
					$this->_pid = 'author';
					$this->_sql_table = 'user';
					break;
				
				case 'search':
					$this->_pid = 'search';
					$this->_sql_table = 'post';
					break;
				
				case 'contact':
					$this->_pid = 'contact';
					break;
				
				default:
					$this->_pid = 'posts';
					$this->_sql_table = 'post';
					break;
			
			}
		
		}
		
		/**
			* Method to build page title
			*
			* @access	protected
		*/
		
		protected function build_title(){
		
			switch($this->_pid){
			
				case 'posts':
					if(VGet::news())
						$this->_title = $this->_content[0]->_title;
					elseif($this->_page != 1)
						$this->_title = 'Posts > Page '.$this->_page;
					else
						$this->_title = 'Posts';
					break;
				
				case 'albums':
					if($this->_page != 1)
						$this->_title = 'Albums > Page '.$this->_page;
					else
						$this->_title = 'Albums';
					break;
				
				case 'video':
					if($this->_page != 1)
						$this->_title = 'Videos > Page '.$this->_page;
					else
						$this->_title = 'Videos';
					break;
				
				case 'author':
					if(VGet::author())
						$this->_title = VGet::author();
					else
						$this->_title = 'Authors';
					break;
				
				case 'search':
					if($this->_page != 1)
						$this->_title = 'Search > Page '.$this->_page;
					else
						$this->_title = 'Search';
					break;
				
				case 'contact':
					$this->_title = 'Contact';
					break;
			
			}
		
		}
		
		/**
			* Method to determine page number and associated limit for sql queries
			*
			* @access private
		*/
		
		private function page(){
		
			if(!VGet::p()){
			
				$this->_limit_start = 0;
				$this->_page = 1;
			
			}else{
			
				if(VGet::p() < 1)
					$this->_page = 1;
				else
					$this->_page = VGet::p();
				
				$this->_limit_start = ($this->_page -1 ) * self::ITEMS_PAGE;
			
			}
		
		}
		
		/**
			* Method to build the complement link for navigation
			*
			* @access	protected
			* @return	string
		*/
		
		protected function link_navigation(){
		
			switch($this->_pid){
			
				case 'posts':
					$link = 'ctl=posts&';
					break;
			
				case 'search':
					if(VGet::tag())
						$link = 'ctl=search&tag='.VGet::tag().'&';
					elseif(VGet::cat())
						$link = 'ctl=search&cat='.VGet::cat().'&';
					else
						$link = 'ctl=search&q='.VGet::q().'&';
					break;
			
				default:
					$link = 'ctl='.$this->_pid.'&';
					break;
			
			}
				
			return $link;
		
		}
		
		/**
			* Return a message if call to undefined method
			*
			* @access	public
			* @param	string [$name] Method name
			* @param	array [$arguments] Array of all arguments passed to the unknown method
			* @return	string Error message
		*/
		
		public function __call($name, $arguments){
		
			return 'The lynx didn\'t show up calling '.$name;
		
		}
		
		/**
			* Return a message if call to undefined method in static context
			*
			* @static
			* @access	public
			* @param	string [$name] Method name
			* @param	array [$arguments] Array of all arguments passed to the unknown method
			* @return	string Error message
		*/
		
		public static function __callStatic($name, $arguments){
		
			return 'The lynx didn\'t show up calling '.$name;
		
		}
		
		/**
			* Method to get the title or the menu elements
			*
			* @access	public
			* @param	string [$attr] Only "_title" and "_menu" are allowed
			* @return	mixed
		*/
		
		public function __get($attr){
		
			if(in_array($attr, array('_title', '_menu')))
				return $this->$attr;
			else
				return 'The lynx is not here!';
		
		}
	
	}

?>