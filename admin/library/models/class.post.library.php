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
	
	namespace Library\Models;
	use Exception;
	
	require_once 'class.master.library.php';
	
	/**
		* Post
		*
		* It represents an item of the associated database table
		*
		* @package		Library
		* @subpackage	Models
		* @namespace	Models
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Post extends Model implements ModelInterface{
	
		private $_id = null;
		private $_title = null;
		private $_content = null;
		private $_allow_comment = null;
		private $_date = null;
		private $_author = null;
		private $_status = null;
		private $_category = null;
		private $_tags = null;
		private $_permalink = null;
		private $_updated = null;
		private $_update_author = null;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	integer [$id] Post id (optional)
		*/
		
		public function __construct($id = false){
		
			parent::__construct();
			
			$this->_sql_table = 'post';
			
			if($id !== false){
			
				$this->_id = $id;
				$this->load();
			
			}else{
			
				$this->ini();
			
			}
		
		}
		
		/**
			* Initialize attributes of the object
			*
			* @access	private
		*/
		
		private function ini(){
		
			$this->_allow_comment = 'open';
		
		}
		
		/**
			* Load method read a set of attributes at a time
			*
			* @access	public
		*/
		
		public function load(){
		
			try{
			
				$this->read('_title');
				$this->read('_content');
				$this->read('_allow_comment');
				$this->read('_date');
				$this->read('_author');
				$this->read('_status');
				$this->read('_category');
				$this->read('_tags');
				$this->read('_permalink');
				$this->read('_updated');
				$this->read('_update_author');
			
			}catch(Exception $e){
			
				throw new Exception(__CLASS__.' can\'t load because '.$e->getMessage());
			
			}
			
		}
		
		/**
			* Create method to add a row in post table
			*
			* After creation success, the id of the row is inserted in id attribute
			*
			* @access	public
		*/
		
		public function create(){
		
			$to_create['table'] = $this->_sql_table;
			$to_create['columns'] = array(':title' => 'post_title', 
										  ':content' => 'post_content', 
										  ':com' => 'post_allow_comment', 
										  ':auth' => 'post_author', 
										  ':status' => 'post_status', 
										  ':cat' => 'post_category', 
										  ':tags' => 'post_tags', 
										  ':slug' => 'post_permalink');
			$to_create['values'] = array(':title' => $this->_title, 
										 ':content' => $this->_content, 
										 ':com' => $this->_allow_comment, 
										 ':auth' => $this->_author,
										 ':status' => $this->_status,
										 ':cat' => $this->_category,
										 ':tags' => $this->_tags,
										 ':slug' => $this->_permalink);
			$to_create['types'] = array(':title' => 'str',
										':content' => 'str',
										':com' => 'str',
										':auth' => 'int',
										':status' => 'str',
										':cat' => 'str',
										':tags' => 'str',
										':slug' => 'str');
			
			$is_int = $this->_db->create($to_create);
			
			if(is_int($is_int)){
			
				$this->_id = $is_int;
				$this->_result_action = true;
			
			}else{
			
				throw new Exception('There\'s a problem creating your '.__CLASS__);
			
			}
		
		}
		
		/**
			* Read an attribute via a given id
			*
			* @access	public
			* @param	string [$attr] Post attribute
		*/
		
		public function read($attr){
		
			$this->$attr = parent::m_read($this->_id, $attr);
		
		}
		
		/**
			* Update the item via its id
			*
			* @access	public
			* @param	string [$attr] Post attribute
			* @param	string [$type] Post attribute data type
		*/
		
		public function update($attr, $type){
		
			parent::m_update($this->_id, $attr, $type);
		
		}
		
		/**
			* Delete the item in the database
			*
			* @access	public
		*/
		
		public function delete(){
		
			parent::m_delete($this->_id);
		
		}
		
		/**
			* Method to check if data passed via __set method are good for the object
			*
			* @access	private
			* @param	string [$attr] Post attribute
			* @param	mixed [$value] Post attribute value
			* @return	mixed true if no errors, otherwise return an error string
		*/
		
		private function check_data($attr, $value){
		
			switch($attr){
			
				case '_title':
					if(empty($value)){
					
						$error = 'Missing title';
					
					}elseif($value == 'Enter title here'){
					
						$error = 'Invalid title';
					
					}else{
					
						$title = explode(' ', $value);
						$check = false;
						
						foreach($title as $word)
							if(strlen($word) > 2)
								$check = true;
						
						if(!$check)
							$error = 'Title has to contain at least one word bigger than 2 characters';	
					
					}
					break;
				
				case '_content':
					if(empty($value))
						$error = 'Missing content';
					elseif($value == 'Type your post here')
						$error = 'Invalid content';
					break;
				
				case '_category':
					if(empty($value))
						$error = 'Missing at least one category';
					break;
				
				case '_tags':
					if($value == 'Separate your tags with commas')
						$error = 'Invalid tags';
					break;
			
			}
			
			if(isset($error))
				return $error;
			else
				return true;
		
		}
		
		/**
			* Set method to update an attribute value in the object
			*
			* @access	public
			* @param	string [$attr] Post attribute
			* @param	mixed [$value] Post attribute value
			* @return	mixed true if no errors, otherwise return an error string
		*/
		
		public function __set($attr, $value){
		
			$checked = $this->check_data($attr, $value);
			
			if($checked === true){
			
				if($attr == '_title')
					$this->$attr = stripslashes(trim($value));
				elseif($attr == '_content' || $attr == '_tags')
					$this->$attr = stripslashes($value);
				else
					$this->$attr = $value;
			
				return true;
			
			}else{
			
				return $checked;	//contain the error message
			
			}
		
		}
		
		/**
			* Get method to return an object attribute value
			*
			* @access	public
			* @param	string [$attr] Post attribute
		*/
		
		public function __get($attr){
		
			if(isset($this->$attr))
				return $this->$attr;
			else
				return false;
		
		}
	
	}

?>