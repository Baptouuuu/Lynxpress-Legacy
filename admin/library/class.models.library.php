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
	use \Library\Database\Database as Database;
	use \Admin\Helper\Helper as Helper;
	use Exception;
	
	/**
		* ModelInterface
		*
		* Interface for all models
		*
		* @package		Library
		* @subpackage	Models
		* @namespace	Models
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
	*/
	
	Interface ModelInterface{
	
		public function load();
		public function create();
		public function read($attr);
		public function update($attr, $type);
		public function delete();
		public function __set($attr, $value);
		public function __get($attr);
	
	}
	
	/**
		* Model
		*
		* It's a base class to create classes that represents sql tables
		*
		* @package		Library
		* @subpackage	Models
		* @namespace	Models
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Model{
	
		protected $_db = null;
		protected $_sql_table = null;
		protected $_result_action = null;
		
		/**
			*Class constructor
			*
			* @access	protected
		*/
		
		protected function __construct(){
		
			$this->_db =& Database::load();
		
		}
		
		/**
			* Generic read method
			*
			* @access	protected
			* @param	integer [$id] Element id
			* @param	string [$attr] Element attribute
			* @return	mixed Value from the database
		*/
		
		protected function m_read($id, $attr){
		
			if(!empty($id)){
			
				$to_read['table'] = $this->_sql_table;
				$to_read['columns'] = array($this->_sql_table.$attr);
				$to_read['condition_columns'][':id'] = strtoupper($this->_sql_table).'_ID';
				$to_read['condition_select_types'][':id'] = '=';
				$to_read['condition_values'][':id'] = $id;
				$to_read['value_types'][':id'] = 'int';
				$result = $this->_db->read($to_read);
				
				if(isset($result[0])){
			
					$this->_result_action = true;
					return $result[0][$this->_sql_table.$attr];
			
				}else{
			
					throw new Exception('can\'t read '.ucfirst(substr($attr, 1)).' attribute');
			
				}
			
			}
		
		}
		
		/**
			* Generic method to update an database element
			*
			* @access	protected
			* @param	integer [$id] Element id
			* @param	string [$attr] Element attribute
			* @param	mixed [$type] Element data type
		*/
		
		protected function m_update($id, $attr, $type){
		
			if(!empty($id)){
		
				$to_update['table'] = $this->_sql_table;
				$to_update['columns'] = array(':attr' => $this->_sql_table.$attr);
				$to_update['condition_columns'] = array(':id' => strtoupper($this->_sql_table).'_ID');
				$to_update['column_values'] = array(':attr' => $this->$attr, ':id' => $id);
				$to_update['value_types'] = array(':attr' => $type, ':id' => 'int');
				$this->_result_action = $this->_db->update($to_update);
				
				if($this->_result_action === false)
					throw new Exception('can\'t update '.ucfirst(substr($attr, 1)));
			
			}
		
		}
		
		/**
			* Generic method to delete database element
			*
			* @access	protected
			* @param	integer [$id] Element id
		*/
		
		protected function m_delete($id){
		
			if(!empty($id)){
			
				$to_delete['table'] = $this->_sql_table;
				$to_delete['condition_columns'] = array(':id' => strtoupper($this->_sql_table).'_ID');
				$to_delete['condition_values'] = array(':id' => $id);
				$to_delete['value_types'] = array(':id' => 'int');
				$this->_result_action = $this->_db->delete($to_delete);
				
				if($this->_result_action === false)
					throw new Exception('can\'t delete '.ucfirst(substr($attr, 1)));
			
			}
		
		}
	
	}
	
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
		
			if($attr == '_content' || $attr == '_name' || $attr == '_email')
				$this->$attr = stripslashes($value);
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
	
	/**
		* Link
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
	
	final class Link extends Model implements ModelInterface{
	
		private $_id = null;
		private $_name = null;
		private $_link = null;
		private $_rss_link = null;
		private $_notes = null;
		private $_priority = null;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	integer [$id] Link id (optional)
		*/
		
		public function __construct($id = false){
		
			parent::__construct();
			
			$this->_sql_table = 'link';
			
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
				$this->read('_link');
				$this->read('_rss_link');
				$this->read('_notes');
				$this->read('_priority');
			
			}catch(Exception $e){
			
				throw new Exception(__CLASS__.' can\'t load because '.$e->getMessage());
			
			}
			
		}
		
		/**
			* Create method to add a row in link table
			*
			* After creation success, the id of the row is inserted in id attribute
			*
			* @access	public
		*/
		
		public function create(){
		
			$to_create['table'] = $this->_sql_table;
			$to_create['columns'] = array(':name' => 'link_name', 
										  ':link' => 'link_link', 
										  ':rss' => 'link_rss_link', 
										  ':notes' => 'link_notes', 
										  ':lvl' => 'link_priority');
			$to_create['values'] = array(':name' => $this->_name, 
										 ':link' => $this->_link, 
										 ':rss' => $this->_rss_link, 
										 ':notes' => $this->_notes,
										 ':lvl' => $this->_priority);
			$to_create['types'] = array(':name' => 'str',
										':link' => 'str',
										':rss' => 'str',
										':notes' => 'str',
										':lvl' => 'int');
			
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
			* @param	string [$attr] Link attribute
		*/
		
		public function read($attr){
		
			$this->$attr = parent::m_read($this->_id, $attr);
		
		}
		
		/**
			* Update the item via its id
			*
			* @access	public
			* @param	string [$attr] Link attribute
			* @param	string [$type] Link attribute data type
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
			* @param	string [$attr] Link attribute
			* @param	string [$value] Link attribute value
			* @return	mixed true if no errors, otherwise return an error string
		*/
		
		private function check_data($attr, $value){
		
			switch($attr){
			
				case '_name':
					if(empty($value))
						$error = 'Name missing';
					elseif(strlen($value) > 20)
						$error = 'Name too long';
					break;
				
				case '_link':
					if(!empty($value) && substr($value, 0, 7) != 'http://')
						$error = 'Url has to begin with "http://"';
					break;
				
				case '_rss_link':
					if(!empty($value) && substr($value, 0, 7) != 'http://')
						$error = 'RSS url has to begin with "http://"';
					break;
				
				case '_priority':
					if(empty($value))
						$error = 'Priority level is missing';
					elseif(!in_array($value, range(1, 5)))
						$error = 'Priority level not existing';
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
			* @param	string [$attr] Link attribute
			* @param	string [$value] Link attribute value
			* @return	mixed true if no errors, otherwise return an error string
		*/
		
		public function __set($attr, $value){
		
			$checked = $this->check_data($attr, $value);
			
			if($checked === true){
			
				$this->$attr = stripslashes($value);
				return true;
			
			}else{
			
				return $checked;	//contain the error message
			
			}
		
		}
		
		/**
			* Get method to return an object attribute value
			*
			* @access	public
			* @param	string [$attr] Link attribute
		*/
		
		public function __get($attr){
		
			if(isset($this->$attr))
				return $this->$attr;
			else
				return false;
		
		}
	
	}
	
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
	
	/**
		* User
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
	
	final class User extends Model implements ModelInterface{
	
		private $_id = null;
		private $_username = null;
		private $_nickname = null;
		private $_firstname = null;
		private $_lastname = null;
		private $_publicname = null;
		private $_password = null;
		private $_email = null;
		private $_website = null;
		private $_msn = null;
		private $_twitter = null;
		private $_facebook = null;
		private $_google = null;
		private $_avatar = null;
		private $_bio = null;
		private $_role = null;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	integer [$id] User id (optional)
		*/
		
		public function __construct($id = false){
		
			parent::__construct();
			
			$this->_sql_table = 'user';
			
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
			
				$this->read('_username');
				$this->read('_nickname');
				$this->read('_firstname');
				$this->read('_lastname');
				$this->read('_publicname');
				$this->read('_email');
				$this->read('_website');
				$this->read('_msn');
				$this->read('_twitter');
				$this->read('_facebook');
				$this->read('_google');
				$this->read('_avatar');
				$this->read('_bio');
				$this->read('_role');
			
			}catch(Exception $e){
			
				throw new Exception(__CLASS__.' can\'t load because '.$e->getMessage());
			
			}
		
		}
		
		/**
			* Create method to add a row in user table
			*
			* After creation success, the id of the row is inserted in id attribute
			*
			* @access	public
		*/
		
		public function create(){
		
			$to_create['table'] = $this->_sql_table;
			$to_create['columns'] = array(':name' => 'user_username', 
										  ':nname' => 'user_nickname', 
										  ':fname' => 'user_firstname', 
										  ':lname' => 'user_lastname', 
										  ':pname' => 'user_publicname', 
										  ':pwd' => 'user_password', 
										  ':mail' => 'user_email', 
										  ':web' => 'user_website', 
										  ':msn' => 'user_msn', 
										  ':tweet' => 'user_twitter', 
										  ':fb' => 'user_facebook', 
										  ':gg' => 'user_google', 
										  ':av' => 'user_avatar',
										  ':bio' => 'user_bio', 
										  ':role' => 'user_role');
			$to_create['values'] = array(':name' => $this->_username, 
										 ':nname' => $this->_nickname, 
										 ':fname' => $this->_firstname, 
										 ':lname' => $this->_lastname, 
										 ':pname' => $this->_publicname,
										 ':pwd' => Helper::make_password($this->_username, $this->_password),
										 ':mail' => $this->_email,
										 ':web' => $this->_website,
										 ':msn' => $this->_msn,
										 ':tweet' => $this->_twitter,
										 ':fb' => $this->_facebook,
										 ':gg' => $this->_google,
										 ':av' => $this->_avatar,
										 ':bio' => $this->_bio,
										 ':role' => $this->_role);
			$to_create['types'] = array(':name' => 'str',
										':nname' => 'str',
										':fname' => 'str',
										':lname' => 'str',
										':pname' => 'str',
										':pwd' => 'str',
										':mail' => 'str',
										':web' => 'str',
										':msn' => 'str',
										':tweet' => 'str',
										':fb' => 'str',
										':gg' => 'str',
										':av' => 'int',
										':bio' => 'str',
										':role' => 'str');
			
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
			* @param	string [$attr] User attribute
		*/
		
		public function read($attr){
		
			$this->$attr = parent::m_read($this->_id, $attr);
		
		}
		
		/**
			* Update the item via its id
			*
			* @access	public
			* @param	string [$attr] User attribute
			* @param	string [$type] User attribute data type
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
			* @param	string [$attr] User attribute
			* @param	mixed [$value] User attribute value
			* @return	mixed true if no errors, otherwise return an error string
		*/
		
		private function check_data($attr, $value){
		
			switch($attr){
			
				case '_username':
					if(empty($value))
						$error = 'Username missing';
					elseif(strlen($value) > 20)
						$error = 'Username too long';
					break;
				
				case '_nickname':
					if(empty($value))
						$error = 'Nickname missing';
					elseif(strlen($value) > 20)
						$error = 'Nickname too long';
					break;
				
				case '_password':
					if(empty($value))
						$error = 'Empty password';
					break;
				
				case '_email':
					if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , $value))
						$error = 'Invalid e-mail';
					elseif(empty($value))
						$error = 'E-mail missing';
					break;
				
				case '_website':
					if(!empty($value) && substr($value, 0, 7) != 'http://')
						$error = 'Website url has to begin with "http://"';
					break;
				
				case '_msn':
					if(!empty($value) && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , $value))
						$error = 'Invalid msn address';
					break;
				
				case '_twitter':
					if(!empty($value) && substr($value, 0, 7) != 'http://')
						$error = 'Twitter url has to begin with "http://"';
					break;
				
				case '_facebook':
					if(!empty($value) && substr($value, 0, 7) != 'http://')
						$error = 'Facebook url has to begin with "http://"';
					break;
				
				case '_google':
					if(!empty($value) && substr($value, 0, 7) != 'http://')
						$error = 'Google+ url has to begin with "http://"';
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
			* @param	string [$attr] User attribute
			* @param	mixed [$value] User attribute value
			* @return	mixed true if no errors, otherwise return an error string
		*/
		
		public function __set($attr, $value){
		
			$checked = $this->check_data($attr, $value);
			
			if($checked === true){
			
				$this->$attr = stripslashes($value);
				return true;
			
			}else{
			
				return $checked;	//contain the error message
			
			}
		
		}
		
		/**
			* Get method to return an object attribute value
			*
			* @access	public
			* @param	string [$attr] User attribute
		*/
		
		public function __get($attr){
		
			if(isset($this->$attr))
				return $this->$attr;
			else
				return false;
		
		}
	
	}
	
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
	
	/**
		* Category
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
	
	final class Category extends Model implements ModelInterface{
	
		private $_id = null;
		private $_name = null;
		private $_type = null;
		
		/**
			* Cass constructor
			*
			* @access	public
			* @param	integer [$id] Category id (optional)
		*/
		
		public function __construct($id = false){
		
			parent::__construct();
			
			$this->_sql_table = 'category';
			
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
			
			}catch(Exception $e){
			
				throw new Exception(__CLASS__.' can\'t load because '.$e->getMessage());
			
			}
		
		}
		
		/**
			* Create method to add a row in category table
			*
			* After creation success, the id of the row is inserted in id attribute
			*
			* @access	public
		*/
		
		public function create(){
		
			$to_create['table'] = $this->_sql_table;
			$to_create['columns'] = array(':name' => 'category_name', 
										  ':type' => 'category_type');
			$to_create['values'] = array(':name' => $this->_name, 
										 ':type' => $this->_type);
			$to_create['types'] = array(':name' => 'str',
										':type' => 'str');
			
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
			* @param	string [$attr] Category attribute
		*/
		
		public function read($attr){
		
			$this->$attr = parent::m_read($this->_id, $attr);
		
		}
		
		/**
			* Update the item via its id
			*
			* @access	public
			* @param	string [$attr] Category attribute
			* @param	string [$type] Category attribute data type
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
			* @param	string [$attr] Category attribute
			* @param	mixed [$value] Category attribute value
		*/
		
		public function __set($attr, $value){
		
			if($attr == '_name')
				$this->$attr = stripslashes(trim($value));
			elseif($attr == '_type')
				$this->$attr = stripslashes($value);
			else
				$this->$attr = $value;
		
		}
		
		/**
			* Get method to return an object attribute value
			*
			* @access	public
			* @param	string [$attr] Category attribute
		*/
		
		public function __get($attr){
		
			if(isset($this->$attr))
				return $this->$attr;
			else
				return false;
		
		}
	
	}
	
	/**
		* Setting
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
	
	final class Setting extends Model implements ModelInterface{
	
		private $_id = null;
		private $_name = null;
		private $_type = null;
		private $_data = null;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	integer [$id] Setting id (optional)
		*/
		
		public function __construct($id = false){
		
			parent::__construct();
			
			$this->_sql_table = 'setting';
			
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
				$this->read('_data');
			
			}catch(Exception $e){
			
				throw new Exception(__CLASS__.' can\'t load because '.$e->getMessage());
			
			}
		
		}
		
		/**
			* Create method to add a row in setting table
			*
			* After creation success, the id of the row is inserted in id attribute
			*
			* @access	public
		*/
		
		public function create(){
		
			$to_create['table'] = $this->_sql_table;
			$to_create['columns'] = array(':name' => 'setting_name', 
										  ':type' => 'setting_type',
										  ':data' => 'setting_data');
			$to_create['values'] = array(':name' => $this->_name, 
										 ':type' => $this->_type,
										 ':data' => $this->_data);
			$to_create['types'] = array(':name' => 'str',
										':type' => 'str',
										':data' => 'str');
			
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
			* @param	string [$attr] Setting attribute
		*/
		
		public function read($attr){
		
			$this->$attr = parent::m_read($this->_id, $attr);
		
		}
		
		/**
			* Update the item via its id
			*
			* @access	public
			* @param	string [$attr] Setting attribute
			* @param	string [$type] Setting attribute data type
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
			* @param	string [$attr] Setting attribute
			* @param	mixed [$value] Setting attribute value
		*/
		
		public function __set($attr, $value){
		
			if($attr == '_name')
				$this->$attr = stripslashes($value);
			else
				$this->$attr = $value;
		
		}
		
		/**
			* Get method to return an object attribute value
			*
			* @access	public
			* @param	string [$attr] Setting attribute
		*/
		
		public function __get($attr){
		
			if(isset($this->$attr))
				return $this->$attr;
			else
				return false;
		
		}
	
	}

?>