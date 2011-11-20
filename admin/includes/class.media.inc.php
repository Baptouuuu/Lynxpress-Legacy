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
	use \Admin\Master as Master;
	use Exception;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Request as VRequest;
	use \Library\Variable\Files as VFiles;
	use \Library\Models\Media as Media;
	use \Library\Models\User as User;
	use \Library\Media\Media as HandleMedia;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Admin\Session as Session;
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
					
					$this->_media->create();
					
					Session::monitor_activity('has upload a file named: '.$this->_media->_name);
					
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
					
					$this->_media->create();
					
					Session::monitor_activity('registered a new video named: '.$this->_media->_name);
					
					header('Location: index.php?ns=media&ctl=manage&action=edit&type=video&id='.$this->_media->_id);
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
	
	}
	
	/**
		* Manage Medias
		*
		* Handles administration of uploaded files and linked videos
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Media
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		private $_view_type = null;
		private $_medias = null;
		private $_search = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			if(VRequest::type() && in_array(VRequest::type(), array('image', 'video', 'alien')))
				$this->_view_type = VRequest::type();
			else
				$this->_view_type = 'image';
				
			$this->build_title();
			
			if($this->_user['media']){
			
				if(VPost::search_button(false))
					$this->_search = trim(VPost::search('foo'));
				
				if($this->_view_type == 'video')
					Helper::get_categories($this->_categories, $this->_action_msg, 'video');
				
				$this->update();
				$this->delete();
				
				$this->get_medias();
			
			}
		
		}
		
		/**
			* Retrieve wanted medias metadata from the database
			*
			* @access	private
		*/
		
		private function get_medias(){
		
			try{
		
				$to_read['table'] = 'media';
				$to_read['columns'] = array('MEDIA_ID');
				
				if(VPost::search_button(false)){
				
					$to_read['condition_columns'][':name'] = 'media_name';
					$to_read['condition_select_types'][':name'] = 'LIKE';
					$to_read['condition_values'][':name'] = '%'.$this->_search.'%';
					$to_read['value_types'][':name'] = 'str';
				
				}elseif(VPost::filter(false)){
				
					if(VPost::date('all') !== 'all'){
					
						$to_read['condition_columns'][':date'] = 'media_date';
						$to_read['condition_select_types'][':date'] = 'LIKE';
						$to_read['condition_values'][':date'] = VPost::date('1970-01').'%';
						$to_read['value_types'][':date'] = 'str';
					
					}
					
					if(VPost::category('all') !== 'all'){
					
						$to_read['condition_types'][':cat'] = 'AND';
						$to_read['condition_columns'][':cat'] = 'media_category';
						$to_read['condition_select_types'][':cat'] = 'LIKE';
						$to_read['condition_values'][':cat'] = '%'.VPost::category().'%';
						$to_read['value_types'][':cat'] = 'str';
					
					}
				
				}elseif(VGet::action() == 'edit' && VGet::id()){
				
					$to_read['condition_columns'][':id'] = 'MEDIA_ID';
					$to_read['condition_select_types'][':id'] = '=';
					$to_read['condition_values'][':id'] = VGet::id();
					$to_read['value_types'][':id'] = 'int';
					
				
				}elseif(VGet::author()){
				
					$to_read['condition_columns'][':author'] = 'media_author';
					$to_read['condition_select_types'][':author'] = '=';
					$to_read['condition_values'][':author'] = VGet::author();
					$to_read['value_types'][':author'] = 'int';
				
				}
				
				$to_read['condition_types'][':type'] = 'AND';
				$to_read['condition_columns'][':type'] = 'media_type';
				$to_read['condition_select_types'][':type'] = 'LIKE';
				$to_read['condition_values'][':type'] = $this->_view_type.'%';
				$to_read['value_types'][':type'] = 'str';
				
				$to_read['order'] = array('media_date', 'desc');
				
				$this->_medias = $this->_db->read($to_read);
				
				if(!empty($this->_medias)){
				
					foreach($this->_medias as &$item){
					
						$item = new Media($item['MEDIA_ID']);
						
						$user = new User();
						$user->_id = $item->_author;
						$user->read('_username');
						
						$item->_author_name = $user->_username;
					
					}
				
				}elseif(empty($this->_medias) && VGet::action() == 'edit'){
				
					throw new Exception('Invalid media!');
				
				}
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				$this->_medias = null;
				$this->_medias[0] = new Media();
			
			}
		
		}
		
		/**
			* Retrieve all external videos
			*
			* @access	private
			* @return	array
		*/
		
		private function get_aliens(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('MEDIA_ID');
				$to_read['condition_columns'][':t'] = 'media_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'alien';
				$to_read['value_types'][':t'] = 'str';
				
				$aliens = $this->_db->read($to_read);
				
				if(!empty($aliens)){
				
					foreach($aliens as &$alien){
					
						$id = $alien['MEDIA_ID'];
						$alien = new Media();
						$alien->_id = $id;
						$alien->read('_name');
					
					}
				
				}
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
			
			return $aliens;
		
		}
		
		/**
			* Build page title
			*
			* @access	private
		*/
		
		private function build_title(){
		
			switch($this->_view_type){
			
				case 'image':
					$this->_title = 'Images';
					break;
				
				case 'video':
					$this->_title = 'Videos';
					break;
				
				case 'alien':
					$this->_title = 'External videos';
					break;
			
			}
		
		}
		
		/**
			* Display related admin parts link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_user['media']){
			
				if(VGet::action() == 'edit')
					Html::mm_menu(true, true, $this->_user['album_photo'], $this->_medias[0]->_name);
				else
					Html::mm_menu(true, false, $this->_user['album_photo']);
			
			}else{
			
				Html::mm_menu(false);
			
			}
		
		}
		
		/**
			* Display file selection type menu
			*
			* @access	private
		*/
		
		private function display_view_types(){
		
			$to_read['table'] = 'media';
			$to_read['columns'] = array('media_type');
			
			$types = $this->_db->read($to_read);
			
			$count_image = 0;
			$count_video = 0;
			$count_alien = 0;
			
			foreach($types as $type){
				$typ = substr($type['media_type'], 0, 5);
			
				switch($typ){
					case 'image':
						$count_image++;
						break;
					case 'video':
						$count_video++;
						break;
					case 'alien':
						$count_alien++;
						break;
				}
			}
			
			$image = 'Images';
			$video = 'Videos';
			$alien = 'External videos';
			
			switch($this->_view_type){
				
				case 'image':
					$image = '<span class="a_selected">Images</span>';
					break;
				
				case 'video':
					$video = '<span class="a_selected">Videos</span>';
					break;
				
				case 'alien':
					$alien = '<span class="a_selected">External videos</span>';
					break;
			
			}
			
			Html::mm_type_menu($image, $count_image, $video, $count_video, $alien, $count_alien);
		
		}
		
		/**
			* Display applicable actions buttons
			*
			* @access	private
			* @param	string [$position] $position can only contain 'top' or 'butt'
		*/
		
		private function display_actions($position){
		
			if(!empty($this->_medias)){
				
				if($position == 'top'){
					
					foreach($this->_medias as $item){
				
						$key = substr($item->_date, 0, 7);
						$dates[$key] = date('F Y', strtotime($item->_date));
				
					}
				
					$dates = array_unique($dates);
					
					Html::mm_delete();
					Html::mm_actions('o', $this->_view_type);
					
					foreach($dates as $key => $date)
						Html::mm_opt_actions($key, $date);
					
					if($this->_view_type == 'video'){
					
						Html::mm_video_cat();
							 	
					 	foreach($this->_categories as $key => $value)
					 		Html::mm_opt_actions($key, ucwords($value));
					
					}
					
					Html::mm_actions('c');
					
				}elseif($position == 'butt'){
					
					Html::mm_delete();
					
				}
			
			}
		
		}
		
		/**
			* Display an html table with retrieved medias
			*
			* @access	private
		*/
		
		private function display_table(){
		
			Html::mm_table('o');
			
			if(!empty($this->_medias)){
				
				foreach($this->_medias as $item){
					
					switch($this->_view_type){
					
						case 'image':
							$fname = basename($item->_permalink);
							$dirname = dirname($item->_permalink).'/';
							$path = $dirname.'150-'.$fname;
							$links = Html::mm_image_links($dirname, $fname);
							break;
						
						case 'video':
							$path = 'images/thumb_video.png';
							$links = Html::mm_video_link($item->_permalink);
							break;
						
						case 'alien':
							$path = 'images/thumb_alien.png';
							$links = Html::mm_alien_link($item->_embed_code);
							break;
					
					}
					
					Html::mm_table_row($item->_id, $path, $item->_name, $item->_type, $this->_view_type, $item->_author, $item->_author_name,$item->_date, $links, $this->_view_type);
					
				}
			
			}else{
			
				if(VPost::search_button(false))
					echo '<tr><td colspan="4">No media found</td></tr>';
				else
					echo '<tr><td colspan="7">There is no media yet.</td></tr>';
			
			}
			
			Html::mm_table('c');
		
		}
		
		/**
			* Display image edition form
			*
			* @access	private
		*/
		
		private function display_edit_image(){
		
			$fname = basename($this->_medias[0]->_permalink);
			$dirname = dirname($this->_medias[0]->_permalink).'/';
			
			Html::mm_edit_image($this->_medias[0]->_name, $dirname, $fname, $this->_medias[0]->_description, $this->_medias[0]->_permalink, $this->_medias[0]->_id, VGet::type());
		
		}
		
		/**
			* Display video edition form
			*
			* @access	private
		*/
		
		private function display_edit_video(){
		
			$aliens = $this->get_aliens();
		
			Html::mm_edit_video('o', $this->_medias[0]->_name, $this->_medias[0]->_permalink, $this->_medias[0]->_description);
				 		
	 		$categories = explode(',', $this->_medias[0]->_category);
	 		
	 		foreach($this->_categories as $key => $value)
	 			Html::category($key, $value, $categories);
				 		
			Html::mm_edit_video('m');
					
	 		if(!empty($aliens))
	 			foreach($aliens as $alien)
	 				Html::mm_opt_actions($alien->_id, $alien->_name, $this->_medias[0]->_attachment);
				 		
			Html::mm_edit_video('c', $this->_medias[0]->_permalink, $this->_medias[0]->_id, VGet::type());
		
		}
		
		/**
			* Display external video edition form
			*
			* @access	private
		*/
		
		private function display_edit_alien(){
		
			Html::mm_edit_alien($this->_medias[0]->_name, $this->_medias[0]->_embed_code, $this->_medias[0]->_description, $this->_medias[0]->_id, VGet::type());
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			if($this->_user['media']){
				
				echo $this->_action_msg;
				
				echo '<div id="list_wrapper">';
				
				Html::form('o', 'post', 'index.php?ns=media&ctl=manage');
				
				if(VGet::action() == 'edit' && VGet::type() && VGet::id()){
				
					$method = 'display_edit_'.VGet::type();
					$this->$method();
				
				}else{
				
					$this->display_view_types();
					$this->display_actions('top');
					$this->display_table();
					$this->display_actions('butt');
					
					echo Helper::datalist('titles', $this->_medias, '_title');
				
				}
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update medias metadata, and also file manipulation when requested
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update_image(false)){
			
				try{
				
					$media = new Media();
					$media->_id = VPost::id();
					$media->_name = VPost::name();
					$media->_description = VPost::description();
					$media->update('_name', 'str');
					$media->update('_description', 'str');
					
					if(VPost::rotate() != 'no'){
					
						$media->read('_permalink');
						$media->read('_type');

						$path = $media->_permalink;
						$fname = basename($path);
						$dirname = dirname($path).'/';
						
						$img = new HandleMedia();
						$img->_mime = $media->_type;
						
						$img->load(PATH.$path);
						$img->rotate(VPost::rotate());
						
						$img->load(PATH.$dirname.'1000-'.$fname);
						$img->rotate(VPost::rotate());
						
						$img->load(PATH.$dirname.'300-'.$fname);
						$img->rotate(VPost::rotate());
						
						$img->load(PATH.$dirname.'150-'.$fname);
						$img->rotate(VPost::rotate());
					
					}
					
					if(VPost::flip() != 'no'){
					
						$media->read('_permalink');
						$media->read('_type');

						$path = $media->_permalink;
						$fname = basename($path);
						$dirname = dirname($path).'/';
						
						$img = new HandleMedia();
						$img->_mime = $media->_type;
						
						$img->load(PATH.$path);
						$img->flip(VPost::flip());
						
						$img->load(PATH.$dirname.'1000-'.$fname);
						$img->flip(VPost::flip());
						
						$img->load(PATH.$dirname.'300-'.$fname);
						$img->flip(VPost::flip());
						
						$img->load(PATH.$dirname.'150-'.$fname);
						$img->flip(VPost::flip());
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}elseif(VPost::update_video(false)){
			
				try{
				
					$media = new Media();
					$media->_id = VPost::id();
					$media->_name = VPost::name();
					$media->_description = VPost::description();
					$media->_category = implode(',', VPost::cat(array()));
					$media->update('_name', 'str');
					$media->update('_description', 'str');
					$media->update('_category', 'str');
					
					if(VPost::attach() != 'no'){
					
						$media->_attachment = VPost::attach();
						$media->update('_attachment', 'int');
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}elseif(VPost::update_alien(false)){
			
				try{
				
					$media = new Media();
					$media->_id = VPost::id();
					$media->_name = VPost::name();
					$media->_description = VPost::description();
					$media->_embed_code = VPost::embed();
					
					$media->update('_name', 'str');
					$media->update('_description', 'str');
					$media->update('_embed_code', 'str');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}
		
		}
		
		/**
			* Delete medias from database and on hard drive
			*
			* @access	private
		*/
		
		private function delete(){
		
			if($this->_user['delete_content'] && VPost::delete(false) && VPost::media_id()){
			
				$results = array();
				$global_result = true;
				
				foreach(VPost::media_id() as $id){
				
					try{
						
						$media = new Media();
						$media->_id = $id;
						$media->read('_permalink');
						
						$path = $media->_permalink;
						
						$media->delete();
						unset($media);
						
						HandleMedia::delete(PATH.$path);
						
						$this->_db->query('DELETE FROM `'.DB_PREFIX.'comment` WHERE comment_rel_id = '.$id.' AND comment_rel_type = "media"');
						
						array_push($results, true);
					
					}catch(Exception $e){
					
						array_push($results, false);
					
					}
				
				}
				
				foreach($results as $result)
					if($result !== true)
						$global_result = false;
				
				Session::monitor_activity('deleted '.count(VPost::media_id()).' file(s)');
				
				$this->_action_msg = ActionMessages::deleted($global_result);
			
			}elseif($this->_user['delete_content'] && VGet::action() == 'delete' && VGet::id()){
			
				try{
				
					$media = new Media();
					$media->_id = VGet::id();
					$media->read('_permalink');
					
					$path = $media->_permalink;
					
					$media->delete();
					unset($media);
					
					HandleMedia::delete(PATH.$path);
					
					$this->_db->query('DELETE FROM `'.DB_PREFIX.'comment` WHERE comment_rel_id = '.VGet::id().' AND comment_rel_type = "media"');
					
					Session::monitor_activity('deleted a file');
					
					$result = true;
				
				}catch(Exception $e){
				
					error_log($e->getMessage(), 0);
					$result = false;
				
				}
				
				$this->_action_msg = ActionMessages::deleted($result);
			
			}elseif(!$this->_user['delete_content'] && (VPost::delete(false) || VGet::action() == 'delete')){
			
				$this->_action_msg = ActionMessages::action_no_perm();
			
			}
		
		}
	
	}
	
	/**
		* Manage Albums
		*
		* Handles administration of photo albums
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Media
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Albums extends Master{
	
		private $_albums = null;
		private $_pictures = array();
		private $_search = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			if(VPost::search_button(false))
				$this->_search = trim(VPost::search('foo'));
			
			$this->_title = 'Albums';
			
			if($this->_user['album_photo']){
			
				$this->create();
				$this->update();
				$this->delete();
			
				$this->get_albums();
				Helper::get_categories($this->_categories, $this->_action_msg, 'album');
			
			}
		
		}
		
		/**
			* Retrieve albums from the database
			*
			* @access	private
		*/
		
		private function get_albums(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('MEDIA_ID');
				$to_read['condition_columns'][':t'] = 'media_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'album';
				$to_read['value_types'][':t'] = 'str';
				$to_read['order'] = array('media_date', 'DESC');
				
				if(VPost::filter(false)){
				
					if(VPost::date() != 'all'){
					
						$to_read['condition_types'][':d'] = 'AND';
						$to_read['condition_columns'][':d'] = 'media_date';
						$to_read['condition_select_types'][':d'] = 'LIKE';
						$to_read['condition_values'][':d'] = VPost::date('1970-01').'%';
						$to_read['value_types'][':d'] = 'str';
					
					}
					
					if(VPost::category() != 'all'){
					
						$to_read['condition_types'][':c'] = 'AND';
						$to_read['condition_columns'][':c'] = 'media_category';
						$to_read['condition_select_types'][':c'] = 'LIKE';
						$to_read['condition_values'][':c'] = '%'.VPost::category().'%';
						$to_read['value_types'][':c'] = 'str';
					
					}
				
				}elseif(VPost::search_button(false)){
				
					$to_read['condition_types'][':n'] = 'AND';
					$to_read['condition_columns'][':n'] = 'media_name';
					$to_read['condition_select_types'][':n'] = 'LIKE';
					$to_read['condition_values'][':n'] = '%'.$this->_search.'%';
					$to_read['value_types'][':n'] = 'str';
				
				}elseif((VGet::action() == 'edit' || VGet::action() == 'upload') && VGet::id()){
				
					$to_read['condition_types'][':id'] = 'AND';
					$to_read['condition_columns'][':id'] = 'MEDIA_ID';
					$to_read['condition_select_types'][':id'] = '=';
					$to_read['condition_values'][':id'] = VGet::id();
					$to_read['value_types'][':id'] = 'int';
					
					if(VGet::action() == 'edit')
						$this->get_pictures();
				
				}
				
				$this->_albums = $this->_db->read($to_read);
				
				if(!empty($this->_albums)){
				
					foreach($this->_albums as &$album){
					
						$album = new Media($album['MEDIA_ID']);
						
						$user = new User();
						$user->_id = $album->_author;
						$user->read('_username');
						
						$album->_author_name = $user->_username;
					
					}
				
				}elseif(empty($this->_content) && (VGet::action() == 'edit' || VGet::action() == 'upload')){
				
					$this->_albums[0] = new Media();
					throw new Exception('Invalid album!');
				
				}
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve all pictures attached to an album when you're in album edit view
			*
			* @access	private
		*/
		
		private function get_pictures(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('MEDIA_ID');
				$to_read['condition_columns'][':id'] = 'media_album';
				$to_read['condition_select_types'][':id'] = '=';
				$to_read['condition_values'][':id'] = VGet::id();
				$to_read['value_types'][':id'] = 'int';
				$to_read['order'] = array('media_name', 'ASC');
				
				$this->_pictures = $this->_db->read($to_read);
				
				if(!empty($this->_pictures)){
				
					foreach($this->_pictures as &$pic){
					
						$pic = new Media($pic['MEDIA_ID']);
						
						$user = new User();
						$user->_id = $pic->_author;
						$user->read('_username');
						
						$pic->_author_name = $user->_username;
					
					}
				
				}
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Display related admin parts link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_user['album_photo']){
			
				if((VGet::action() == 'edit' || VGet::action() == 'upload') && VGet::id())
					Html::ma_menu(true, true, $this->_albums[0]->_id, $this->_albums[0]->_name);
				else
					Html::ma_menu(true, false);
			
			}else{
			
				Html::ma_menu(false);
			
			}
		
		}
		
		/**
			* Display applicable actions buttons
			*
			* @access	private
		*/
		
		private function display_actions(){
		
			if(!empty($this->_albums)){
			
				foreach($this->_albums as $album){
				
					$key = substr($album->_date, 0, 7);
					$dates[$key] = date('F Y', strtotime($album->_date));
				
				}
				
				$dates = array_unique($dates);
			
			}else{
			
				$dates = array();
			
			}
			
			Html::ma_actions('o');

			foreach($dates as $key => $date)
				Html::mm_opt_actions($key, $date);

			Html::ma_actions('m');

			foreach($this->_categories as $key => $cat)
				Html::mm_opt_actions($key, ucwords($cat));

			Html::ma_actions('c');
		
		}
		
		/**
			* Display all retrieved albums
			*
			* @access	private
		*/
		
		private function display_albums(){
		
			Html::form('o', 'post', 'index.php?ns=media&ctl=albums');
			
			$this->display_actions();
			
			echo '<div id="labels">';
			
			if(!empty($this->_albums)){
			
				foreach($this->_albums as $album){
				
					$cats = explode(',', $album->_category);
					
					if(empty($cats[0]))
						$cats = array();
					
					$cats_name = array();
					
					foreach($cats as $cat)
						array_push($cats_name, $this->_categories[$cat]);
					
					Html::ma_album_label($album->_id, $album->_permalink, $album->_name, $album->_status, $album->_author_name, $album->_date, $cats_name);
				
				}
			
			}else{
			
				echo 'No albums yet';
			
			}
			
			echo '</div>';
			
			Html::form('c');
		
		}
		
		/**
			* Display applicable actions buttons when you're in album edit view
			*
			* @access	private
		*/
		
		private function display_album_actions(){
		
			$status = $this->_albums[0]->_status;
			
			echo '<div id="album_actions">';
			
			if($status == 'draft'){
			
				Html::ma_save();
				Html::ma_view($this->_albums[0]->_id, true);
				Html::ma_publish();
			
			}elseif($status == 'publish'){
			
				Html::ma_save();
				Html::ma_view($this->_albums[0]->_id);
				Html::ma_publish(false);
			
			}
			
			echo '</div>';
		
		}
		
		/**
			* Display album edition page
			*
			* @access	private
		*/
		
		private function display_edit_album(){
		
			$cats = explode(',', $this->_albums[0]->_category);
			
			Html::form('o', 'post', 'index.php?ns=media&ctl=albums&action=edit&id='.$this->_albums[0]->_id);
			
			$this->display_album_actions();
			
			Html::ma_edit('o', $this->_albums[0]->_id, $this->_albums[0]->_permalink, $this->_albums[0]->_author_name, $this->_albums[0]->_date, $this->_albums[0]->_allow_comment, $this->_albums[0]->_name, $this->_albums[0]->_description);
					 		
			foreach($this->_categories as $key => $value)
				Html::category($key, $value, $cats);
					 	
			Html::ma_edit('m', $this->_albums[0]->_id);
					 
					 if(!empty($this->_pictures)){
					 
					 	echo '<div id="labels">';
					 	
					 	foreach($this->_pictures as $picture){
					 	
					 		$dirname = dirname($picture->_permalink).'/';
					 		$filename = basename($picture->_permalink);
					 		
					 		Html::ma_picture_label($picture->_id, $picture->_permalink, $dirname, $filename, $picture->_name, $picture->_author_name, $picture->_date);
					 	
					 	}
					 	
					 	echo '</div>';
					 
					 }else{
					 
					 	echo 'No photos in this album yet';
					 
					 }
					 
			Html::ma_edit('c');
			
			Html::form('c');
		
		}
		
		/**
			* Display an upload form to add new pictures to an album
			*
			* @access	private
		*/
		
		private function display_upload(){
		
			Html::form('o', 'post', 'index.php?ns=media&ctl=albums&action=edit&id='.$this->_albums[0]->_id, true);
			
			Html::ma_upload($this->_albums[0]->_name, $this->_albums[0]->_id);
			
			Html::form('c');
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			if($this->_user['album_photo']){
			
				echo $this->_action_msg;
				
				echo '<div id="list_wrapper">';
				
				if(VGet::action() == 'edit' && VGet::id()){
				
					$this->display_edit_album();
				
				}elseif(VGet::action() == 'upload' && VGet::id()){
				
					$this->display_upload();
				
				}else{
				
					$this->display_albums();
					
					echo Helper::datalist('titles', $this->_albums, '_name');
				
				}
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Move uploaded files in the associated album directory and insert metadata in the database
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::upload(false) && !empty($_FILES)){
			
				try{
			
					$album = new Media();
					$album->_id = VPost::album_id();
					$album->read('_name');
					$album->read('_permalink');
					$path = $album->_permalink;
					
					foreach(VFiles::all() as $key => $img){
					
						if(empty($img['name']))
							continue;
						
						$pic = new HandleMedia();
						$pic->load_upload($key);
						
						$name = Helper::remove_accent($pic->_name);
						$mime = $pic->_mime;
						
						if(substr($mime, 0, 5) == 'image'){
						
							if(file_exists(PATH.$path.$name))
								throw new Exception('The file "'.$name.'" already exists');
							
							$pic->save(PATH.$path.$name);
							$pic->thumb(150, 0);
							$pic->thumb(300, 0);
							$pic->thumb(1000, 0);
							
							$picture = new Media();
							$picture->_name = $name;
							$picture->_type = 'attach';
							$picture->_author = $this->_user['user_id'];
							$picture->_album = $album->_id;
							$picture->_allow_comment = 'closed';
							$picture->_permalink = $path.$name;
							$picture->_status = 'publish';
							$picture->create();
						
						}
					
					}
					
					Session::monitor_activity('added new photos to '.$album->_name);
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::created($result);
			
			}
		
		}
		
		/**
			* Update album and pictures metadatas
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::apply_action(false) && in_array(VPost::action(), array('publish', 'unpublish'))){
			
				switch(VPost::action()){
				
					case 'publish':
						$status = 'publish';
						break;
					
					case 'unpublish':
						$status = 'draft';
						break;
				
				}
				
				if(VPost::album_id()){
				
					try{
				
						foreach(VPost::album_id() as $id){
						
							$album = new Media();
							$album->_id = $id;
							$album->_status = $status;
							$album->update('_status', 'str');
						
						}
						
						$result = true;
					
					}catch(Exception $e){
					
						$result = $e->getMessage();
					
					}
					
					$this->_action_msg = ActionMessages::updated($result);
				
				}
			
			}elseif(VPost::save(false)){
			
				try{
				
					$album = new Media();
					$album->_id = VPost::album_id();
					$album->_name = VPost::name();
					$album->_description = VPost::description();
					$album->_allow_comment = VPost::allow_comment('closed');
					$album->_category = implode(',', VPost::cat(array()));
					
					$album->update('_name', 'str');
					$album->update('_description', 'str');
					$album->update('_allow_comment', 'str');
					$album->update('_category', 'str');
					
					foreach($_POST as $key => $value){
					
						$pic = substr($key, 0, 3);
						
						if($pic == 'pic'){
						
							$id = substr($key, 3);
							$picture = new Media();
							$picture->_id = $id;
							$picture->_name = VPost::$key();
							$picture->update('_name', 'str');
						
						}
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}elseif(VPost::publish(false)){
			
				try{
				
					$album = new Media();
					$album->_id = VPost::album_id();
					$album->_status = 'publish';
					$album->_name = VPost::name();
					$album->_description = VPost::description();
					$album->_allow_comment = VPost::allow_comment('closed');
					$album->_category = implode(',', VPost::cat(array()));
					
					$album->update('_name', 'str');
					$album->update('_description', 'str');
					$album->update('_allow_comment', 'str');
					$album->update('_category', 'str');
					$album->update('_status', 'str');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}elseif(VPost::unpublish(false)){
			
				try{
				
					$album = new Media();
					$album->_id = VPost::album_id();
					$album->_status = 'draft';
					$album->update('_status', 'str');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}
		
		}
		
		/**
			* Delete files on hard drive and metadata in database
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::apply_action(false) && VPost::action() == 'delete' && $this->_user['delete_content']){
			
				if(VPost::album_id()){
				
					try{
					
						foreach(VPost::album_id() as $id){
						
							$album = new Media();
							$album->_id = $id;
							$album->read('_permalink');
							
							$to_read['table'] = 'media';
							$to_read['columns'] = array('MEDIA_ID');
							$to_read['condition_columns'][':id'] = 'media_album';
							$to_read['condition_select_types'][':id'] = '=';
							$to_read['condition_values'][':id'] = $id;
							$to_read['value_types'][':id'] = 'int';
							
							$ids = $this->_db->read($to_read);
							
							if(!empty($ids)){
							
								foreach($ids as $pid){
								
									$pic = new Media();
									$pic->_id = $pid['MEDIA_ID'];
									$pic->read('_permalink');
									$permalink = $pic->_permalink;
									HandleMedia::delete(PATH.$permalink);
									$pic->delete();
								
								}
							
							}
							
							$permalink = $album->_permalink;
							HandleMedia::delete(PATH.$permalink.'cover.png');
							rmdir(PATH.$permalink);
							
							$album->delete();
							
							$this->_db->query('DELETE FROM `'.DB_PREFIX.'comment` WHERE comment_rel_id = '.$id.' AND comment_rel_type = "media"');
						
						}
						
						Session::monitor_activity('deleted '.count(VPost::album_id()).' album(s)');
						
						$result = true;
					
					}catch(Exception $e){
					
						$result = $e->getMessage();
					
					}
					
					$this->_action_msg = ActionMessages::deleted($result);
				
				}
			
			}elseif(VGet::action() == 'delete' && VGet::id() && $this->_user['delete_content']){
			
				try{
				
					$pic = new Media();
					$pic->_id = VGet::id();
					$pic->read('_permalink');
					$permalink = $pic->_permalink;
					HandleMedia::delete(PATH.$permalink);
					$pic->delete();
					
					$this->_db->query('DELETE FROM `'.DB_PREFIX.'comment` WHERE comment_rel_id = '.VGet::id().' AND comment_rel_type = "media"');
					
					Session::monitor_activity('deleted a picture of an album');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::deleted($result);
			
			}elseif(VPost::delete_pics(false)){
			
				if(VPost::picture_id()){
				
					try{
					
						foreach(VPost::picture_id() as $id){
						
							$pic = new Media();
							$pic->_id = $id;
							$pic->read('_permalink');
							$permalink = $pic->_permalink;
							HandleMedia::delete(PATH.$permalink);
							$pic->delete();
						
						}
						
						Session::monitor_activity('deleted '.count(VPost::picture_id(array())).' picture(s) of an album');
						
						$result = true;
					
					}catch(Exception $e){
					
						$result = $e->getMessage();
					
					}
				
				}
			
			}elseif(((VPost::apply_action(false) && VPost::action() == 'delete') || VGet::action() == 'delete' || VPost::delete_pics(false)) && !$this->_user['delete_content']){
			
				$this->_action_msg = ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>