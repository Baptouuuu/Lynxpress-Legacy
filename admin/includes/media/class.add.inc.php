<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011
		* @license		http://www.gnu.org/licenses/gpl.html GNU GPL V3
		* @package		Lynxpress
		* @subpackage	Administration
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
	
	namespace Admin\Media;
	use \Admin\Master\Master as Master;
	use Exception;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Files as VFiles;
	use \Library\Models\Media as Media;
	use \Library\Media\Media as HandleMedia;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Admin\Session\Session as Session;
	use \Admin\Helper\Helper as Helper;
	
	/**
		* Add Media
		*
		* Handles uploading new files and link external videos to the website
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Media
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Add extends Master{
	
		private $_view_type = null;
		private $_media = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			if(VGet::view() && in_array(VGet::view(), array('upload', 'linkage', 'album', 'video')))
				$this->_view_type = VGet::view();
			else
				$this->_view_type = 'upload';
			
			if($this->_view_type == 'album')
				Helper::get_categories($this->_categories, $this->_action_msg, 'album');
			
			$this->build_title();
			
			$this->_media = new Media();
			
			if($this->_user['media'])
				$this->create();
		
		}
		
		/**
			* Build page title in function on which page is viewed
			*
			* @access	private
		*/
		
		private function build_title(){
		
			switch($this->_view_type){
			
				case 'upload':
					$this->_title = 'Upload a Media';
					break;
				
				case 'linkage':
					$this->_title = 'Link a Video';
					break;
				
				case 'album':
					$this->_title = 'Create an Album';
					break;
				
				case 'video':
					$this->_title = 'Register your video';
					break;
			
			}
		
		}
		
		/**
			* Display related admin part links
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_user['media'])
				Html::nm_menu(true, $this->_user['album_photo']);
			else
				Html::nm_menu(false);
		
		}
		
		/**
			* Display a menu to select upload type form
			*
			* @access	private
		*/
		
		private function display_type(){
		
			$upload = 'Upload';
			$album = 'Album';
			$linkage = 'Linkage';
			$video = 'Video';
			
			switch($this->_view_type){
			
				case 'upload':
					$upload = '<span class="a_selected">Upload</span>';
					break;
			
				case 'album':
					$album = '<span class="a_selected">Album</span>';
					break;
			
				case 'linkage':
					$linkage = '<span class="a_selected">Linkage</span>';
					break;
				
				case 'video':
					$video = '<span class="a_selected">Video</span>';
			
			}
			
			Html::nm_type_menu($upload, $album, $linkage, $video, $this->_user['album_photo']);
		
		}
		
		/**
			* Display upload file form
			*
			* @access	private
		*/
		
		private function display_upload(){
		
			Html::nm_upload();
		
		}
		
		/**
			* Display photo album creation form
			*
			* @access	private
		*/
		
		private function display_album(){
		
			if($this->_user['album_photo']){
			
				Html::nm_album('o');
						 
				foreach($this->_categories as $key => $value)
					Html::category($key, $value);
				
				Html::nm_album('c');
			
			}
		
		}
		
		/**
			* Display a form to link an external video to the website
			*
			* @access	private
		*/
		
		private function display_linkage(){
		
			Html::nm_linkage();
		
		}
		
		/**
			* Display form to register a video uploaded via ftp
			*
			* @access	private
		*/
		
		private function display_video(){
		
			Html::nm_video('o');
			
			$media = new HandleMedia();
			$mimes = $media->_allowed;
			
			foreach($mimes as $mime)
				if(substr($mime, 0, 5) == 'video')
					Html::video_category($mime);
			
			Html::nm_video('c');
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			if($this->_user['media']){
			
				$action = 'display_'.$this->_view_type;
			
				echo $this->_action_msg;
				
				$this->display_type();
				
				echo '<div id="new_media">';
				
				$this->$action();
				
				echo '</div>';
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Move uploaded files to the right place and insert metadata in the database
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::upload(false)){
			
				try{
					
					$path = 'content/'.date('Y/m/');
					
					$img = new HandleMedia();
					$img->load_upload('file');
					
					$name = Helper::remove_accent($img->_name);
					$mime = $img->_mime;
					
					if(file_exists(PATH.$path.$name))
						throw new Exception('The file "'.$name.'" already exists');
					
					$img->save(PATH.$path.$name);
					
					if(substr($mime, 0, 5) == 'image'){
					
						$img->thumb(150, 0);
						$img->thumb(300, 0);
						$img->thumb(1000, 0);
						
						$this->_media->_status = 'draft';
					
					}elseif(substr($mime, 0, 5) == 'video'){
					
						$this->_media->_status = 'publish';
					
					}
					
					$this->_media->_name = $name;
					$this->_media->_type = $mime;
					$this->_media->_author = $this->_user['user_id'];
					$this->_media->_allow_comment = 'closed';
					$this->_media->_permalink = $path.$name;
					$this->_media->_album = 0;
					
					$this->_media->create();
					
					Session::monitor_activity('has upload a file named: '.$this->_media->_name);
					
					if(substr($mime, 0, 5) == 'video')
						header('Location: index.php?ns=media&ctl=manage&type=video');
					else
						header('Location: index.php?ns=media&ctl=manage');
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}elseif(VPost::create_album(false) && $this->_user['album_photo']){
			
				if(!VPost::name()){
				
					$this->_action_msg = ActionMessages::custom_wrong('Album name missing');
				
				}else{
				
					try{
					
						$name = VPost::name();
						$path = 'content/albums/'.Helper::slug($name).'/';
						
						if(file_exists(PATH.$path))
							throw new Exception('The album "'.$name.'" already exists');
						
						$this->_media->_name = $name;
						$this->_media->_type = 'album';
						$this->_media->_author = $this->_user['user_id'];
						$this->_media->_status = 'draft';
						$this->_media->_permalink = $path;
						$this->_media->_description = stripslashes(VPost::description());
						$this->_media->_category = implode(',', VPost::cat(array()));
						$this->_media->_allow_comment = VPost::allow_comment('closed');
						$this->_media->_album = 0;
						
						$img = new HandleMedia();
						$img->load_upload('cover');
						
						$img->save(PATH.$path.'cover.png');
						$img->thumb(150, 0);
						$img->thumb(300, 0);
						$img->thumb(1000, 0);
						
						$this->_media->create();
						
						Session::monitor_activity('created an album named: '.$this->_media->_name);
						
						header('Location: index.php?ns=media&ctl=albums&action=edit&id='.$this->_media->_id);
					
					}catch(Exception $e){
					
						$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
					
					}
				
				}
			
			}elseif(VPost::link_alien(false)){
			
				if(!VPost::name() || !VPost::embed_code()){
				
					$this->_action_msg = ActionMessages::custom_wrong('There\'s missing informations');
				
				}else{
				
					try{
					
						$this->_media->_name = VPost::name();
						$this->_media->_type = 'alien';
						$this->_media->_author = $this->_user['user_id'];
						$this->_media->_status = 'draft';
						$this->_media->_allow_comment = 'closed';
						$this->_media->_permalink = Helper::slug(VPost::name());
						$this->_media->_embed_code = VPost::embed_code();
						$this->_media->_album = 0;
						
						$this->_media->create();
						
						Session::monitor_activity('linked a new video named: '.$this->_media->_name);
						
						header('Location: index.php?ns=media&ctl=manage&type=video');
					
					}catch(Exception $e){
					
						$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
					
					}
				
				}
			
			}elseif(VPost::register_video(false)){
			
				try{
				
					if(!file_exists(PATH.VPost::url()))
						throw new Exception('Video not found');
					
					if(!VPost::mime())
						throw new Exception('Video mime type missing');
					
					$this->_media->_name = VPost::name();
					$this->_media->_type = VPost::mime();
					$this->_media->_author = $this->_user['user_id'];
					$this->_media->_status = 'publish';
					$this->_media->_allow_comment = 'closed';
					$this->_media->_permalink = VPost::url();
					$this->_media->_album = 0;
					
					$this->_media->create();
					
					Session::monitor_activity('registered a new video named: '.$this->_media->_name);
					
					header('Location: index.php?ns=media&ctl=manage&action=edit&type=video&id='.$this->_media->_id);
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
	
	}

?>