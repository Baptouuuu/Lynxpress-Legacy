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
	
	namespace Library\Database;
	use PDO;
	use Exception;
	
	/**
		* Database
		*
		* Database class integrates a CRUD to handle all actions with the sql database
		*
		* @package		Library
		* @subpackage	Database
		* @namespace	Database
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Database{
	
		private $_c = null;
		private $_connected = null;
		private $_result_action = null;
		const FETCH_ASSOC = PDO::FETCH_ASSOC;
		const FETCH_NUM = PDO::FETCH_NUM;
		const FETCH_OBJ = PDO::FETCH_OBJ;
		
		/**
			* Class constructor
			*
			* Connect the website to the database, if connection fails it stops all the application
			*
			* send a mail to the webmaster and display an error message
			*
			* @access	public
		*/
		
		public function __construct(){
		
			try{
			
				if(!defined('DB_NAME') && !defined('DB_HOST') && !defined('DB_USER') && !defined('DB_PWD') && !defined('DB_PREDFIX'))
					throw new Exception('Lynxpress can\'t find database constants!');
				
				$this->_c = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST.';', DB_USER, DB_PWD, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
				$this->_connected = true;
			
			}catch(Exception $e){
			
				@error_log($e->getMessage(), 1, WS_EMAIL);
				$this->_connected = false;
				die('<h1>Error Establishing Database Connection</h1>');
			
			}
		
		}
		
		/**
			* Method to get an instance of the database object
			*
			* @static
			* @access	public
			* @return	object Database instance
		*/
		
		public static function &load(){
		
			static $inst;
			
			if(!is_object($inst))
				$inst = new Database();
			
			return $inst;
		
		}
		
		/**
			* Method to insert data in the database
			*
			* It takes an array as parameter and has to be built as follows:
			* <code>
			* array(
			*	'table' => 'table_name',
			*	'columns' => array(':sql_key' => 'column_name'),
			*	'values' => array(':sql_key' => 'value'),
			*	'types' => array(':sql_key' => 'type')
			* )
			* </code>
			*
			* @access	public
			* @param	array [$array]
			* @return	mixed On succes return id of created element, if creation fails return false, for other errors it returns the message error
		*/
		
		public function create($array){
		
			if($this->_connected){
				
				if(is_array($array) && isset($array['table']) && isset($array['columns']) && isset($array['values']) && isset($array['types']) && !empty($array)){
					
					try{
				
						$columns = null;
						$values = null;
						
						foreach($array['columns'] as $sql_key => $column){
				
							$columns .= $column;
							$values .= $sql_key;
							
							if($column != end($array['columns'])){
				
								$columns .= ', ';
								$values .= ', ';
				
							}
				
						}
							
						$sql = 'INSERT INTO '.DB_PREFIX.$array['table'].' ('.$columns.') VALUES ('.$values.')';
						
						$create = $this->_c->prepare($sql);
						
						foreach($array['values'] as $sql_key => &$value)
							$create->bindParam($sql_key, $value, $this->param_type($array['types']["$sql_key"]));
						
						$create->execute();
						
						if($create->errorCode() == '00000'){
						
							return (int)($this->_c->lastInsertId());
						
						}else{
						
							error_log($create->errorCode(), 0);
							return false;
						
						}
						
					}catch(PDOException $e){
					
						return 'Error code: '.$e->getMessage();
					
					}
					
				}else{
				
					return 'the given array is not valid!';
				
				}
			
			}else{
			
				return 'Database not connected';
			
			}
		
		}
		
		/**
			* Method to get data from the database
			*
			* It takes an array as parameter and has to be built as follows:
			* <code>
			* array(
			*	'table' => 'table_name',
			*	'columns' => array('column_name'),
			*	'condition_types' => array(':sql_key' => 'type'),				
			*	'condition_columns' => array(':sql_key' => 'column_name'),		
			*	'condition_select_types' => array(':sql_key' => 'select_type'),	
			*	'condition_values' => array(':sql_key' => 'value'),				
			*	'value_types' => array(':sql_key' => 'type'),					
			*	'order' => array('column_name', 'type'),						
			*	'limit => array('first_value', 'step')							
			* )
			* </code>
			*
			* condition_types value can be "OR" or "AND", this element is optionnal
			*
			* condition_columns is optional
			*
			* condition_select_types can be "=", "!=", ">", "<", ">=", "<=", "LIKE", "IS NULL" or "IS NOT NULL", this element is optional
			*
			* condition_values is optional
			*
			* value_types is optional
			*
			* second value for "order" can be "ASC" or "DESC", this element is optional
			*
			* limit is optional
			*
			*
			* If you have one condition column, condition_columns, condition_select_types, condition_values and value_types are mandatory
			*
			* For more than one condition column, condition_types is mandatory too
			*
			* @access	public
			* @param	array [$array]
			* @param	int [$fetch] $fetch can be set to Database::FETCH_ASSOC, Database::FETCH_NUM or Database::FETCH_OBJ
			* @return	mixed On success returns an associative array, if an error occur while reading it returns false, otherwise for any problem it returns a message
		*/
		
		public function read($array, $fetch = self::FETCH_ASSOC){
		
			if($this->_connected){
			
				if(is_array($array) && isset($array['table']) && isset($array['columns'])){
					
					try{
			
						$columns = implode(', ', $array['columns']);
						$conditions = null;
						$order = null;
						$limit = null;
						
						if(isset($array['condition_columns']) && !empty($array['condition_columns'])){
						
							$conditions = ' WHERE ';
							reset($array['condition_columns']);
							$first_key = key($array['condition_columns']);
							
							foreach($array['condition_columns'] as $sql_key => $column){
							
								if($sql_key == $first_key){
							
									if($sql_key == 'group'){
										
										$conditions .= '(';
										
										reset($array['condition_columns']['group']);
										$sub_first_key = key($array['condition_columns']['group']);
										
										foreach($column as $sub_sql_key => $sub_column){
									
											if($sub_sql_key == $sub_first_key)
												$conditions .= $sub_column.' '.$array['condition_select_types']["$sub_sql_key"].' '.$sub_sql_key;
											else
												$conditions .= ' '.$array['condition_types']["$sub_sql_key"].' '.$sub_column.' '.$array['condition_select_types']["$sub_sql_key"].' '.$sub_sql_key;
											
										}
										
										$conditions .= ')'; 
										
									}else{
									
										$conditions .= $column.' '.$array['condition_select_types']["$sql_key"].' '.$sql_key;
									
									}
								
								}else{
								
									if($sql_key == 'group'){
										
										$conditions .= ' AND (';
										
										reset($array['condition_columns']['group']);
										$sub_first_key = key($array['condition_columns']['group']);
										
										foreach($column as $sub_sql_key => $sub_column){
											
											if($sub_sql_key == $sub_first_key)
												$conditions .= $sub_column.' '.$array['condition_select_types']["$sub_sql_key"].' '.$sub_sql_key;
											else
												$conditions .= ' '.$array['condition_types']["$sub_sql_key"].' '.$sub_column.' '.$array['condition_select_types']["$sub_sql_key"].' '.$sub_sql_key;
											
										}
										
										$conditions .= ')'; 
										
									}else{
									
										$conditions .= ' '.$array['condition_types']["$sql_key"].' '.$column.' '.$array['condition_select_types']["$sql_key"].' '.$sql_key;
									
									}
								
								}
								
							}
						
						}//end if conditions
						
						if(isset($array['order']) && !empty($array['order']))
							$order = ' ORDER BY '.$array['order'][0].' '.$array['order'][1];
						
						if(isset($array['limit']) && !empty($array['limit']))
							$limit = ' LIMIT '.$array['limit'][0].', '.$array['limit'][1];
						
						$sql = 'SELECT '.$columns.' FROM '.DB_PREFIX.$array['table'].$conditions.$order.$limit;
						
						$read = $this->_c->prepare($sql);
						
						if(isset($array['condition_values']) && !empty($array['condition_values']))
							foreach($array['condition_values'] as $sql_key => &$value)
								$read->bindParam($sql_key, $value, $this->param_type($array['value_types']["$sql_key"]));
						
						$read->execute();
						
						if($read->errorCode() == '00000')
							return $read->fetchAll($fetch);
						else
							return false;
						
					}catch(PDOException $e){
					
						return 'Error code: '.$e->getMessage();
					
					}
					
				}else{
				
					return 'the given array is not valid!';
				
				}
			
			}else{
			
				return 'Database not connected';
			
			}
		
		}
		
		/**
			* Method to update data in database
			*
			* It takes an array as parameter and as to be built as follows:
			* <code>
			* array(
			*	'table' => 'table_name',
			*	'columns' => array(':sql_key' => 'column_name'),
			*	'column_values' => array(':sql_key' => 'value'),
			*	'value_types' => array(':sql_key' => 'type'),
			*	'condition_columns' => array(':sql_key' => 'column_name')
			* )
			* </code>
			*
			* @access	public
			* @param	array [$array]
			* @return	mixed On success returns true, if an error occur while updating it returns false, otherwise for any problem it returns a message
		*/
		
		public function update($array){
		
			if($this->_connected){
				
				if(is_array($array) && isset($array['table']) && isset($array['columns']) && isset($array['column_values']) && isset($array['value_types']) && isset($array['condition_columns'])){
					
					try{
						
						$values = null;
						$conditions = null;
						
						foreach($array['columns'] as $sql_key => $column){
						
							$values .= $column.' = '.$sql_key;
							
							if($column != end($array['columns']))
								$values .= ', ';
						
						}
						
						foreach($array['condition_columns'] as $sql_key => $column){
						
							$conditions .= $column.' = '.$sql_key;
							
							if($column != end($array['condition_columns']))
								$conditions .= ' AND ';
						
						}
						
						$sql = 'UPDATE '.DB_PREFIX.$array['table'].' SET '.$values.' WHERE '.$conditions;
						
						$update = $this->_c->prepare($sql);
						
						foreach($array['column_values'] as $sql_key => &$value)
							$update->bindParam($sql_key, $value, $this->param_type($array['value_types']["$sql_key"]));
						
						$update->execute();
						
						if($update->errorCode() == '00000')
							return true;
						else
							return false;
						
					}catch(PDOException $e){
					
						return 'Error code: '.$e->getMessage();
					
					}
					
				}else{
				
					return 'the given array is not valid!';
				
				}
			
			}else{
			
				return 'Database not connected';
			
			}
		
		}
		
		/**
			* Method to delete data in the database
			*
			* It takes an array as param and has to be built as follows:
			* <code>
			* array(
			*	'table' => 'table_name',
			*	'condition_columns' => array(':sql_key' => 'column_name'),
			*	'condition_values' => array(':sql_key' => 'value'),
			*	'value_types' => array(':sql_key' => 'type')
			* )
			* </code>
			*
			* @access	public
			* @param	array [$array]
			* @return	mixed On success returns true, if an error occur while it returns false, otherwise for any problem it returns a message
		*/
		
		public function delete($array){
		
			if($this->_connected){
				
				if(is_array($array) && isset($array['table']) && isset($array['condition_columns']) && isset($array['condition_values']) && isset($array['value_types'])){
					
					try{
						
						$sql = null;
						
						foreach($array['condition_columns'] as $sql_key => $column)
							$sql .= 'DELETE FROM '.DB_PREFIX.$array['table'].' WHERE '.$column.' = '.$sql_key.'; ';
						
						$delete = $this->_c->prepare($sql);
						
						foreach($array['condition_values'] as $sql_key => &$value)
							$delete->bindParam($sql_key, $value, $this->param_type($array['value_types']["$sql_key"]));
						
						$delete->execute();
						
						if($delete->errorCode() == '00000')
							return true;
						else
							return false;
						
					}catch(PDOException $e){
					
						return 'Error code: '.$e->getMessage();
					
					}
					
				}else{
				
					return 'the given array is not valid!';
				
				}
			
			}else{
			
				return 'Database not connected';
			
			}
		
		}
		
		/**
			* Execute a sql query
			*
			* Only used for special queries as CREATE, TRUNCATE, DROP or DELETE if you have more than one condition column
			*
			* Be careful, this method doesn't protect your query against SQL Injection
			*
			* @access	public
			* @param	string [$query]
			* @return	mixed On success returns true, if an error occur while executing the query it returns false, otherwise for any problem it returns a message
		*/
		
		public function query($query){
		
			if($this->_connected){
			
				try{
				
					$delete = $this->_c->prepare($query);
					$delete->execute();
					
					if($delete->errorCode() == '00000')
						return true;
					else
						return false;
				
				}catch(Exception $e){
				
					return 'Error code: '.$e->getMessage();
				
				}
			
			}else{
			
				return 'Database not connected';
			
			}
		
		}
		
		/**
			* Method to adapt type parameter in the CRUD into a PDO type
			*
			* @access	private
			* @param	string [$param_type] $param_type can contain "int", "str", "bool" or "null"
			* @return	integer
		*/
		
		private function param_type($param_type){
		
			switch($param_type){
			
				case 'int':
					$type = PDO::PARAM_INT;
					break;
			
				case 'str':
					$type = PDO::PARAM_STR;
					break;
			
				case 'bool':
					$type = PDO::PARAM_BOOL;
					break;
			
				case 'null':
					$type = PDO::PARAM_NULL;
					break;
			
				default:
					throw new Exception('Unknown data type!');
					break;
			
			}
			
			return $type;
		
		}
		
		/**
			* Method to destruct the object, and close database connection
			*
			* @access	public
		*/
		
		public function __destruct(){
		
			unset($this->_c);
		
		}
	
	}

?>