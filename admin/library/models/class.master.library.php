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
	use \Library\Database\Database as Database;
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

?>