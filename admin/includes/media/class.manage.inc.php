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
	use \Library\Variable\Request as VRequest;
	use \Library\Models\Media as Media;
	use \Library\Models\User as User;
	use \Library\Media\Media as HandleMedia;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Admin\Session\Session as Session;
	use \Admin\Helper\Helper as Helper;
	
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

?>