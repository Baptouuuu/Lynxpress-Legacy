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
	
	namespace Library\Media;
	use Exception;
	
	/**
		* Media
		*
		* Manipulate files (images or video), all file manipulation has to occur inside a try{}catch(){}
		*
		* Use this class to manipulate media files
		*
		* @package		Library
		* @subpackage	Media
		* @namespace	Media
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
	*/
	
	class Media{
	
		private $_file = null;
		private $_name = null;
		private $_width = null;
		private $_height = null;
		private $_mime = null;
		private $_path = null;
		private $_allowed = array('image/gif', 'image/jpeg', 'image/png', 'video/mp4', 'video/mov', 'video/mpg', 'video/mpeg');
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			self::check_ext();
		
		}
		
		/**
			* In order to load correctly a file with this method
			*
			* after instanciation and before this method, you have to
			*
			* set the mime type with __set method
			*
			* @access	public
			* @param	string [$path] path to the file
		*/
		
		public function load($path){
		
			if(!file_exists($path)){
			
				throw new Exception('Inexistant file');
			
			}elseif(substr($this->_mime, 0, 5) == 'image'){
			
				$this->_file = $path;
				$this->_name = basename($this->_file);
				$attr = getimagesize($this->_file);
				$this->_width = $attr[0];
				$this->_height = $attr[1];
			
			}elseif(substr($this->_mime, 0, 5) == 'video'){
			
				$this->_file = $path;
				$this->_name = basename($this->_file);
			
			}else{
			
				throw new Exception('Unknown media type');
			
			}
		
		}
		
		/**
			* Load a file from an unpload with the name attribute passed in the form
			*
			* Retrieve all informations about the file in attributes
			*
			* @access	public
			* @param	string [$name] Html file input name
		*/
		
		public function load_upload($name){
		
			if($_FILES[$name]['error'] == 0){
			
				if(substr($_FILES[$name]['type'], 0, 5) == 'image'){
				
					$this->_file = $_FILES[$name]['tmp_name'];
					$this->_mime = image_type_to_mime_type(exif_imagetype($this->_file));
					$this->_name = $_FILES[$name]['name'];
					$attr = getimagesize($this->_file);
					$this->_width = $attr[0];
					$this->_height = $attr[1];
				
				}elseif(substr($_FILES[$name]['type'], 0, 5) == 'video'){
				
					$this->_file = $_FILES[$name]['tmp_name'];
					$this->_mime = $_FILES[$name]['type'];
					$this->_name = $_FILES[$name]['name'];
				
				}else{
				
					throw new Exception('Unknown media type');
				
				}
			
			}else{
			
				throw new Exception('Upload failed');
			
			}
			
			if(!in_array($this->_mime, $this->_allowed))
				throw new Exception('Format not supported');
		
		}
		
		/**
			* Move current file to a wanted directory
			*
			* @access	public
			* @param	string [$path] Destination path
		*/
		
		public function save($path){
		
			$dirname = dirname($path);
			
			if(!is_dir($dirname))
				$return = @mkdir($dirname, 0777, true);
			else
				$return = true;
			
			if($return === false)
				throw new Exception('Folder can\'t be created');
			
			$return = null;
			$return = @move_uploaded_file($this->_file, $path);
			
			if($return === false)
				throw new Exception('File can\'t be moved');
			else
				$this->_file = $path;
		
		}
		
		/**
			* Thumb create a thumbnail of an image corresponding of given dimensions
			*
			* if one of the parameters are empty it calculate the image ratio to determine the missing param
			*
			* @access	public
			* @param	integer [$width] Wished width of the thumb
			* @param	integer [$height] Wished height of the thumb
		*/
		
		public function thumb($width, $height){
		
			@ini_set('memory_limit', '256M');
			
			if(empty($width))
				$width = $this->ratio($this->_width, $height, $this->_height);
			elseif(empty($height))
				$height = $this->ratio($this->_height, $width, $this->_width);
		
			switch($this->_mime){
			
				case 'image/gif':
					$src = imagecreatefromgif($this->_file);
					break;
				
				case 'image/jpeg':
					$src = imagecreatefromjpeg($this->_file);
					break;
				
				case 'image/png':
					$src = imagecreatefrompng($this->_file);
					break;
			
			}
			
			if(isset($src) && $src !== false){
			
				$dest = imagecreatetruecolor($width, $height);
				imagecolortransparent($dest, imagecolorallocate($dest, 0, 0, 0));	//make transparent black background
				$dirname = dirname($this->_file).'/';
				$name = $width.'-';
				$name .= basename($this->_file);
				
				imagecopyresampled($dest, $src, 0, 0, 0, 0, $width, $height, $this->_width, $this->_height);
				
				switch($this->_mime){
				
					case 'image/gif':
						$result = @imagegif($dest, $dirname.$name);
						break;
					
					case 'image/jpeg':
						$result = @imagejpeg($dest, $dirname.$name);
						break;
					
					case 'image/png':
						$result = @imagepng($dest, $dirname.$name);
						break;
				
				}
				
				if($result === false)
					throw new Exception('File can\'t be created');
			
			}else{
			
				throw new Exception('Source image not loadable');
			
			}
		
		}
		
		/**
			* Rotate the image to the wanted degree
			*
			* @access	public
			* @param	integer [$degree]
		*/
		
		public function rotate($degree){
		
			@ini_set('memory_limit', '256M');
		
			switch($this->_mime){
			
				case 'image/gif':
					$src = imagecreatefromgif($this->_file);
					break;
				
				case 'image/jpeg':
					$src = imagecreatefromjpeg($this->_file);
					break;
				
				case 'image/png':
					$src = imagecreatefrompng($this->_file);
					break;
			
			}
			
			if(isset($src) && $src !== false){
			
				$src = imagerotate($src, $degree, 0);
				
				switch($this->_mime){
				
					case 'image/gif':
						$result = @imagegif($src, $this->_file);
						break;
					
					case 'image/jpeg':
						$result = @imagejpeg($src, $this->_file);
						break;
					
					case 'image/png':
						$result = @imagepng($src, $this->_file);
						break;
				
				}
				
				if($result === false)
					throw new Exception('File can\'t be modified');
			
			}else{
			
				throw new Exception('Source image not loadable');
			
			}
		
		}
		
		/**
			* Flip an image
			*
			* @access	public
			* @param	string [$align] Can be set to 'h' or 'v'
		*/
		
		public function flip($align){
		
			@ini_set('memory_limit', '256M');
			
			switch($this->_mime){
			
				case 'image/gif':
					$src = imagecreatefromgif($this->_file);
					break;
				
				case 'image/jpeg':
					$src = imagecreatefromjpeg($this->_file);
					break;
				
				case 'image/png':
					$src = imagecreatefrompng($this->_file);
					break;
			
			}
			
			if(isset($src) && $src !== false){
			
				$dest = imagecreatetruecolor($this->_width, $this->_height);
				
				if($align == 'h'){
				
					imagecopyresampled($dest, $src, 0, 0, ($this->_width - 1), 0, $this->_width, $this->_height, (0 - $this->_width), $this->_height);
				
				}elseif($align == 'v'){
				
					imagecopyresampled($dest, $src, 0, 0, 0, ($this->_height - 1), $this->_width, $this->_height, $this->_width, (0 - $this->_height));
				
				}
				
				switch($this->_mime){
				
					case 'image/gif':
						$result = @imagegif($dest, $this->_file);
						break;
					
					case 'image/jpeg':
						$result = @imagejpeg($dest, $this->_file);
						break;
					
					case 'image/png':
						$result = @imagepng($dest, $this->_file);
						break;
				
				}
				
				if($result === false)
					throw new Exception('File can\'t be flipped');
			
			}else{
			
				throw new Exception('Source image not loadable');
			
			}
		
		}
		
		/**
			* Delete a file and thumbnails too
			*
			* @static
			* @access	public
			* @param	string [$path] Path to the file
		*/
		
		public static function delete($path){
		
			self::check_ext();
			
			$fname = basename($path);
			$dirname = dirname($path).'/';
			
			try{
			
				@unlink($dirname.'150-'.$fname);
				@unlink($dirname.'300-'.$fname);
				@unlink($dirname.'1000-'.$fname);
				@unlink($path);
			
			}catch(Exception $e){
			
				error_log($e->getMessage(), 0);
				return false;
			
			}
		
		}
		
		/**
			* Make a ratio with three dimensions and return the fourth one
			*
			* @access	public
			* @param	integer [$m1]
			* @param	integer [$m2]
		 	* @param	integer [$d1]
		 	* @return	integer
		*/
		
		public function ratio($m1, $m2, $d1){
		
			$result = $m1 * $m2;
			$result /= $d1;
			$result = floor($result);
			
			return $result;
		
		}
		
		/**
			* Check if gd and exif extension are loaded, if not it raises an Exception
			*
			* @static
			* @access	private
		*/
		
		private static function check_ext(){
		
			if(!extension_loaded('gd'))
				throw new Exception('You can\'t handle media because GD extension is not loaded');
			
			if(!extension_loaded('exif'))
				throw new Exception('You can\'t handle media because Exif extension is not loaded');
		
		}
		
		/**
			* Determine maximum upload size allowed
			*
			* @static
			* @access	public
			* @return	integer
		*/
		
		public static function max_upload(){
		
			$upload = (int)(ini_get('upload_max_filesize'));
			$post = (int)(ini_get('post_max_size'));
			$memory = (int)(ini_get('memory_limit'));
			
			return min($upload, $post, $memory);
		
		}
		
		/**
			* Set a value to an attribute
			*
			* @access	public
			* @param	string [$attr] Attribute name
			* @param	mixed [$value] Attribute value to set
		*/
		
		public function __set($attr, $value){
		
			$this->$attr = $value;
		
		}
		
		/**
			* Get value of an attribute
			*
			* @access	public
			* @param	string [$attr] Attribute name
			* @return	mixed Attribute value
		*/
		
		public function __get($attr){
		
			return $this->$attr;
		
		}
	
	}

?>