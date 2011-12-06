<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011
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
		* Media
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
	
	final class Media extends Model implements ModelInterface{
	
		private $_id = null;
		private $_name = null;
		private $_type = null;
		private $_author = null;
		private $_album = null;
		private $_status = null;
		private $_category = null;
		private $_allow_comment = null;
		private $_permalink = null;
		private $_embed_code = null;
		private $_description = null;
		private $_date = null;
		private $_attachment = null;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	integer [$id] Media id (optional)
		*/
		
		public function __construct($id = false){
		
			parent::__construct();
			
			$this->_sql_table = 'media';
			
			if($id !== false){
			
				$this->_id = $id;
				$this->load();
			
			}
		
		}
		
		/**
			* Load method read a set of attributes at a time
			*
			* @access	public
		*/
		
		public function load(){
		
			try{
			
				$this->read('_name');
				$this->read('_type');
				$this->read('_author');
				$this->read('_album');
				$this->read('_status');
				$this->read('_category');
				$this->read('_allow_comment');
				$this->read('_permalink');
				$this->read('_embed_code');
				$this->read('_description');
				$this->read('_date');
				$this->read('_attachment');
			
			}catch(Exception $e){
			
				throw new Exception(__CLASS__.' can\'t load because '.$e->getMessage());
			
			}
		
		}
		
		/**
			* Create method to add a row in media table
			*
			* After creation success, the id of the row is inserted in id attribute
			*
			* @access	public
		*/
		
		public function create(){
		
			$to_create['table'] = $this->_sql_table;
			$to_create['columns'] = array(':name' => 'media_name', 
										  ':type' => 'media_type', 
										  ':auth' => 'media_author', 
										  ':album' => 'media_album', 
										  ':status' => 'media_status', 
										  ':cat' => 'media_category', 
										  ':com' => 'media_allow_comment', 
										  ':slug' => 'media_permalink', 
										  ':code' => 'media_embed_code', 
										  ':desc' => 'media_description', 
										  ':date' => 'media_date');
			$to_create['values'] = array(':name' => $this->_name, 
										 ':type' => $this->_type, 
										 ':auth' => $this->_author, 
										 ':album' => $this->_album, 
										 ':status' => $this->_status,
										 ':cat' => $this->_category,
										 ':com' => $this->_allow_comment,
										 ':slug' => $this->_permalink,
										 ':code' => $this->_embed_code,
										 ':desc' => $this->_description,
										 ':date' => $this->_date);
			$to_create['types'] = array(':name' => 'str',
										':type' => 'str',
										':auth' => 'int',
										':album' => 'int',
										':status' => 'str',
										':cat' => 'str',
										':com' => 'str',
										':slug' => 'str',
										':code' => 'str',
										':desc' => 'str',
										':date' => 'str');
			
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
			* @param	string [$attr] Media attribute
		*/
		
		public function read($attr){
		
			$this->$attr = parent::m_read($this->_id, $attr);
		
		}
		
		/**
			* Update the item via its id
			*
			* @access	public
			* @param	string [$attr] Media attribute
			* @param	string [$type] Media attribute data type
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
			* Set method to update an attribute value in the object
			*
			* @access	public
			* @param	string [$attr] Media attribute
			* @param	mixed [$value] Media attribute value
		*/
		
		public function __set($attr, $value){
		
			if($attr == '_name')
				$this->$attr = stripslashes(trim($value));
			elseif($attr == '_description' || $attr == '_embed_code')
				$this->$attr = stripslashes($value);
			else
				$this->$attr = $value;
		
		}
		
		/**
			* Get method to return an object attribute value
			*
			* @access	public
			* @param	string [$attr] Media attribute
		*/
		
		public function __get($attr){
		
			if(isset($this->$attr))
				return $this->$attr;
			else
				return false;
		
		}
	
	}

?>