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
		* Comment
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
	
	final class Comment extends Model implements ModelInterface{
	
		private $_id = null;
		private $_name = null;
		private $_email = null;
		private $_content = null;
		private $_rel_id = null;
		private $_rel_type = null;
		private $_status = null;
		private $_date = null;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	integer [$id] Comment id (optional)
		*/
		
		public function __construct($id = false){
		
			parent::__construct();
			
			$this->_sql_table = 'comment';
			
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
				$this->read('_email');
				$this->read('_content');
				$this->read('_rel_id');
				$this->read('_rel_type');
				$this->read('_status');
				$this->read('_date');
			
			}catch(Exception $e){
			
				throw new Exception(__CLASS__.' can\'t load because '.$e->getMessage());
			
			}
			
		}
		
		/**
			* Create method to add a row in comment table
			*
			* After creation success, the id of the row is inserted in id attribute
			*
			* @access	public
		*/
		
		public function create(){
		
			$to_create['table'] = $this->_sql_table;
			$to_create['columns'] = array(':name' => 'comment_name', 
										  ':email' => 'comment_email', 
										  ':content' => 'comment_content', 
										  ':rid' => 'comment_rel_id', 
										  ':rtype' => 'comment_rel_type', 
										  ':status' => 'comment_status');
			$to_create['values'] = array(':name' => $this->_name, 
										 ':email' => $this->_email, 
										 ':content' => $this->_content, 
										 ':rid' => $this->_rel_id,
										 ':rtype' => $this->_rel_type,
										 ':status' => $this->_status);
			$to_create['types'] = array(':name' => 'str',
										':email' => 'str',
										':content' => 'str',
										':rid' => 'int',
										':rtype' => 'str',
										':status' => 'str');
			
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
			* @param	string [$attr] Comment attribute
		*/
		
		public function read($attr){
		
			$this->$attr = parent::m_read($this->_id, $attr);
		
		}
		
		/**
			* Update the item via its id
			*
			* @access	public
			* @param	string [$attr] Comment attribute
			* @param	string [$type] Comment attribute data type
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
			* @param	string [$attr] Comment attribute
			* @param	mixed [$value] Comment attribute value
		*/
		
		public function __set($attr, $value){
		
			if($attr == '_content' || $attr == '_email')
				$this->$attr = stripslashes($value);
			elseif($attr == '_name')
				$this->$attr = stripslashes(htmlspecialchars($value));
			else
				$this->$attr = $value;
		
		}
		
		/**
			* Get method to return an object attribute value
			*
			* @access	public
			* @param	string [$attr] Comment attribute
		*/
		
		public function __get($attr){
		
			if(isset($this->$attr))
				return $this->$attr;
			else
				return false;
		
		}
	
	}

?>