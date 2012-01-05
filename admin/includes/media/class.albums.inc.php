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
	use \Library\Models\User as User;
	use \Library\Media\Media as HandleMedia;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Admin\Session\Session as Session;
	use \Admin\Helper\Helper as Helper;
	
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
				
				}elseif((VGet::action() == 'edit' || VGet::action() == 'upload' || VGet::action() == 'edit_image') && VGet::id()){
				
					$to_read['condition_types'][':id'] = 'AND';
					$to_read['condition_columns'][':id'] = 'MEDIA_ID';
					$to_read['condition_select_types'][':id'] = '=';
					$to_read['condition_values'][':id'] = VGet::id();
					$to_read['value_types'][':id'] = 'int';
					
					if(VGet::action() == 'edit')
						$this->get_pictures();
					elseif(VGet::action() == 'edit_image' && VGet::pid())
						$this->get_picture();
				
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
			* Retrieve a specific image of an album
			*
			* @access	private
		*/
		
		private function get_picture(){
		
			try{
			
				$this->_pictures[0] = new Media(VGet::pid());
			
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
			
				if((VGet::action() == 'edit' || VGet::action() == 'upload' || VGet::action() == 'edit_image') && VGet::id())
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
			
			echo '<section id="labels">';
			
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
			
			echo '</section>';
			
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
					 		
					 		Html::ma_picture_label($picture->_id, $picture->_permalink, $dirname, $filename, $picture->_name, $picture->_author_name, $picture->_date, $this->_albums[0]->_id);
					 	
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
			* Display a form to edit a picture
			*
			* @access	private
		*/
		
		private function display_edit_pic(){
		
			Html::form('o', 'post', 'index.php?ns=media&ctl=albums&action=edit&id='.$this->_albums[0]->_id);
			
			$dirname = dirname($this->_pictures[0]->_permalink).'/';
			$fname = basename($this->_pictures[0]->_permalink);
			
			Html::ma_edit_image($this->_pictures[0]->_name, $dirname, $fname, $this->_pictures[0]->_description, $this->_pictures[0]->_permalink, $this->_pictures[0]->_id);
			
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
				
				}elseif(VGet::action() == 'edit_image' && VGet::id() && VGet::pid()){
				
					$this->display_edit_pic();
				
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
							$picture->_type = $mime;
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
			
			}elseif(VPost::update_image(false)){
			
				try{
								
					$media = new Media();
					$media->_id = VPost::pid();
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
							@rmdir(PATH.$permalink);
							
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