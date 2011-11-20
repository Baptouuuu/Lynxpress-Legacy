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
	
	namespace Library\File;
	use Exception;
	use ZipArchive;
	
	/**
		* File
		*
		* Manipulate text files
		*
		* @package		Library
		* @subpackage	File
		* @namespace	File
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
	*/
	
	class File{
	
		private $_path = null;
		private $_name = null;
		private $_directory = null;
		private $_content = null;
		
		/**
			* Return an File object with the content of the sile set into the attribute
			*
			* @static
			* @access	public
			* @param	string [$path]
		*/
		
		public static function read($path){
		
			$file = new File();
			
			$file->_path = $path;
			$file->_name = basename($path);
			$file->_directory = dirname($path).'/';
			$handle = @fopen($path, 'r');
			
			if($handle === false)
				throw new Exception('File can\'t be read ("'.$path.'")');
			
			$file->_content = @fread($handle, filesize($path));
			
			return $file;
		
		}
		
		/**
			* Save the loaded file to a specific place with the content inside the content attribute
			*
			* @access	public
			* @param	string [$path] If empty, the current path will be used
		*/
		
		public function save($path = ''){
		
			if(empty($path))
				$path = $this->_path;
			
			$handle = @fopen($path, 'w');
			
			if($handle === false)
				throw new Exception('File can\'t be read ("'.$path.'")');
			
			@fwrite($handle, $this->_content);
			@fclose($handle);
		
		}
		
		/**
			* Move a file
			*
			* @static
			* @access	public
			* @param	string [$src] Source file path
			* @param	string [$dest] Destination file path
		*/
		
		public static function move($src, $dest){
		
			$dirname = dirname($dest).'/';
			
			if(!is_dir($dirname))
				$result = mkdir($dirname, 0777, true);
			else
				$result = true;
			
			if($result === false)
				throw new Exception('Folder can\'t be created');
			
			$file = self::read($src);
			$file->save($dest);
		
		}
		
		/**
			* Unzip an archive to a specific folder
			*
			* @static
			* @access	public
			* @param	string [$src] Source path
			* @param	string [$dest] Detination path
		*/
		
		public static function unzip($src, $dest){
		
			if(!is_dir($dest))
				$return = @mkdir($dest, 0777, true);
			else
				$return = true;
			
			if($return === false)
				throw new Exception('Folder can\'t be created');
			
			$return = null;
			
			$zip = new ZipArchive();
			$return = $zip->open($src);
			
			if($return === false)
				throw new Exception('Archive can\'t be opened');
			
			$zip->extractTo($dest);
			$zip->close();
		
		}
		
		/**
			* Delete a file
			*
			* @static
			* @access	public
			* @param	sting [$path]
		*/
		
		public static function delete($path){
		
			$filename = basename($path);
			$dirname = dirname($path).'/';
			
			if(file_exists($path))
				@unlink($path);
			else
				throw new Exception('File doesn\'t exists');
			
			$dir = @scandir($dirname);
			
			$unix_dir = array('.', '..');
			
			if(empty($dir) || $dir === $unix_dir)
				@rmdir($dirname);
		
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