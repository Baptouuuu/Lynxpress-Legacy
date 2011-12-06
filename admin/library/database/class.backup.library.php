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
	use \Library\File\File as File;
	use Exception;
	
	/**
		* Backup Database
		*
		* Backup the whole database into a file
		*
		* @package		Library
		* @subpackage	Database
		* @namespace	Database
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
	*/
	
	class Backup{
	
		private $_db = null;
		private $_tables = array();
		private $_sql = null;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	mixed [$tables] Tables to backup
		*/
		
		public function __construct($tables = '*'){
		
			$this->_db =& Database::load();
			
			if($tables = '*')
				$this->get_tables();
			elseif(is_array($tables))
				$this->_tables = $tables;
			else
				throw new Exception('Invalid request');
			
			$this->_sql = '# ************************************************************'."\n";
			$this->_sql .= '# Lynxpress dump'."\n";
			$this->_sql .= '# Version '.WS_VERSION."\n";
			$this->_sql .= '# Database: '.DB_NAME."\n";
			$this->_sql .= '# Generation time: '.date(DATE_ATOM)."\n";
			$this->_sql .= '# ************************************************************'."\n\n";
			
			foreach($this->_tables as $table)
				$this->backup_table($table);
		
		}
		
		/**
			* Retrieve all tables
			*
			* @access	private
		*/
		
		private function get_tables(){
		
			$tables = $this->_db->query('SHOW TABLES', true, Database::FETCH_NUM);
			
			if(!empty($tables))
				foreach($tables as $table)
					array_push($this->_tables, $table[0]);
			else
				throw new Exception('Unable to retrieve tables');
		
		}
				
		/**
			* Put a table content with its create query in _sql attribute
			*
			* @access	private
			* @param	string [$table]
		*/
		
		private function backup_table($table){
		
			if(empty($table))
				throw new Exception('Table name is missing');
			
			$sql = '# Dump of '.$table."\n";
			$sql .= '# ------------------------------------------------------------'."\n\n";
			$sql .= 'DROP TABLE `'.$table.'`;'."\n\n";
			
			$create = $this->_db->query('SHOW CREATE TABLE '.$table, true, Database::FETCH_NUM);
			
			if(empty($create))
				throw new Exception('Can\'t retrieve create table statement for "'.$table.'"');
			
			$sql .= $create[0][1].";\n\n";
			
			$rows = $this->_db->query('SELECT * FROM `'.$table.'`', true);
			
			foreach($rows as $row){
			
				$sql .= 'INSERT INTO `'.$table.'` VALUES (';
				
				foreach($row as &$value)
					$value = '"'.addslashes($value).'"';
				
				$sql .= implode(', ', $row);
				
				$sql .= ');'."\n";
			
			}
			
			$this->_sql .= $sql."\n\n\n";
			
			unset($sql);
			unset($rows);
		
		}
		
		/**
			* Save dump to a sql file
			*
			* @access	public
			* @param	string [$path]
		*/
		
		public function save($path = 'dump.sql'){
		
			$dump = new File();
			$dump->_content = $this->_sql;
			$dump->save($path);
		
		}
		
		/**
			* Method to set data in the object
			*
			* @access	public
			* @param	string [$attr]
			* @param	mixed [$value]
		*/
		
		public function __set($attr, $value){
		
			$this->$attr = $value;
		
		}
		
		/**
			* Method to get value of an attribute
			*
			* @access	public
			* @param	string [$attr]
		*/
		
		public function __get($attr){
		
			return $this->$attr;
		
		}
	
	}

?>