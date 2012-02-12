<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011-2012
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
	
	namespace Admin\Posts;
	use \Admin\Master\Master as Master;
	use Exception;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Request as VRequest;
	use \Library\Models\Post as Post;
	use \Library\Models\Setting as Setting;
	use \Library\Models\Media as Media;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Admin\Session\Session as Session;
	use \Admin\Helper\Helper as Helper;
	
	/**
		* Add Post
		*
		* Handles creation and edition for a post
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Posts
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0.1
		* @final
	*/
	
	final class Add extends Master{
	
		private $_post = null;
		private $_action = null;	//can be set to "to_insert" or to "to_update"
		private $_setting = null;
		private $_videos = null;
		private $_pictures = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			$this->_title = 'Manage Post';
			
			if($this->_user['post']){
			
				$this->get_post();
				Helper::get_categories($this->_categories, $this->_action_msg, 'post');
				$this->get_setting();
				$this->get_medias();
				
				if(VPost::action() == 'to_insert')
					$this->create();
				elseif(VPost::action() == 'to_update')
					$this->update();
			
			}
		
		}
		
		/**
			* If in edit mode create a new post object, else create an empty post object
			*
			* @access	private
		*/
		
		private function get_post(){
		
			if((VRequest::action() == 'edit' || VRequest::action() == 'to_update') && VRequest::id()){
				
				try{
					
					$this->_post = new Post(VRequest::id());
					$this->_action = 'to_update';
					
				}catch(Exception $e){
					
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
					$this->_post = new Post();
					$this->_action = 'to_insert';
					
				}
			
			}else{
			
				$this->_post = new Post();
				$this->_action = 'to_insert';
			
			}
		
		}
		
		/**
			* Retrieve post settings
			*
			* @access	private
		*/
		
		private function get_setting(){
		
			try{
			
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('SETTING_ID');
				$to_read['condition_columns'][':t'] = 'setting_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'post';
				$to_read['value_types'][':t'] = 'str';
				
				$this->_setting = $this->_db->read($to_read);
				
				$this->_setting = new Setting($this->_setting[0]['SETTING_ID']);
				
				$this->_setting->_data = json_decode($this->_setting->_data, true);
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve all videos and pictures
			*
			* @access	private
		*/
		
		private function get_medias(){
		
			if($this->_setting->_data['media']){
			
				try{
				
					$to_read['table'] = 'media';
					$to_read['columns'] = array('MEDIA_ID');
					$to_read['condition_columns'][':t'] = 'media_type';
					$to_read['condition_select_types'][':t'] = 'LIKE';
					$to_read['condition_values'][':t'] = 'image/%';
					$to_read['value_types'][':t'] = 'str';
					$to_read['condition_types'][':album'] = 'AND';
					$to_read['condition_columns'][':album'] = 'media_album';
					$to_read['condition_select_types'][':album'] = '=';
					$to_read['condition_values'][':album'] = '0';
					$to_read['value_types'][':album'] = 'int';
					$to_read['order'] = array('media_date', 'DESC');
					
					$this->_pictures = $this->_db->read($to_read);
					
					if(!empty($this->_pictures))
						foreach ($this->_pictures as &$m)
							$m = new Media($m['MEDIA_ID']);
					
					$to_read['condition_values'][':t'] = 'video/%';
					
					$this->_videos = $this->_db->read($to_read);
					
					if(!empty($this->_videos)){
					
						foreach ($this->_videos as &$m) {
						
							$m = new Media($m['MEDIA_ID']);
							
							$a = $m->_attachment;
							
							if(!empty($a)){
							
								$a = new Media($m->_attachment);
								$m->_embed_code = $a->_embed_code;
							
							}
						
						}
					
					}
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
		
		/**
			* Display related admin parts link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_action == 'to_insert')
				Html::np_menu();
			elseif($this->_action == 'to_update')
				Html::np_menu(true);
		
		}
		
		/**
			* Display actions buttons
			*
			* @access	private
		*/
		
		private function display_actions(){
		
			if($this->_action == 'to_insert'){
			
				Html::np_actions(array('draft', 'publish'));
			
			}elseif($this->_action == 'to_update'){
				
				if($this->_post->_status == 'publish')
					Html::np_actions(array('view', 'update'), $this->_post->_permalink);
				else
					Html::np_actions(array('draft', 'preview', 'publish'), $this->_post->_permalink);
				
			}
		
		}
		
		/**
			* Display form to create or edit a post
			*
			* @access	private
		*/
		
		private function display_form(){
		
			Html::np_form('o', $this->_post->_title, $this->_post->_permalink, $this->_action, $this->_post->_status, $this->_post->_id, $this->_post->_content);
			$permalink = $this->_post->_permalink;
				 			
 			$categories = explode(',', $this->_post->_category);
 			
 			foreach($this->_categories as $key => $value)
 				Html::category($key, $value, $categories);
				 			
			Html::np_form('c', $this->_post->_allow_comment, $this->_post->_tags);
		
		}
		
		/**
			* Display medias
			*
			* @access	private
		*/
		
		private function display_medias(){
		
			if($this->_setting->_data['media']){
			
				Html::np_pictures('o');
				
				foreach ($this->_pictures as $m)
					Html::np_pic($m->_id, $m->_name, $m->_permalink);
				
				Html::np_pictures('c');
				
				Html::np_videos('o');
				
				foreach ($this->_videos as $v)
					Html::np_vid($v->_id, $v->_name, $v->_permalink, $v->_embed_code);
				
				Html::np_videos('c');
			
			}
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			if($this->_user['post'] == true){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', 'index.php?ns=posts&ctl=add');
				
				$this->display_actions();
				$this->display_form();
				$this->display_medias();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Set data from the form into the object
			*
			* If errors detected in the object it's returned into an array
			*
			* @access	private
			* @return	boolean
		*/
		
		private function check_post_data(){
		
			$results = array();
			$errors = array();
			
			array_push($results, $this->_post->__set('_title', VPost::title()));
			array_push($results, $this->_post->__set('_content', VPost::content()));
			array_push($results, $this->_post->__set('_allow_comment', VPost::allow_comment('closed')));
			
			if(VPost::publish(false))
				array_push($results, $this->_post->__set('_status', 'publish'));
			else
				array_push($results, $this->_post->__set('_status', 'draft'));
			
			array_push($results, $this->_post->__set('_category', implode(',', VPost::categories(array()))));			//insertion of an empty aarray to return error message defined in the object
			array_push($results, $this->_post->__set('_tags', VPost::tags('divers')));
			
			if($this->_action == 'to_insert')
				array_push($results, $this->_post->__set('_permalink', Helper::slug($this->_post->__get('_title'))));	//we should make it in create method, but we need to handle the error
			
			
			foreach($results as $result)
				if($result !== true)
					array_push($errors, '<li>- '.$result.'</li>');
			
			if(!empty($errors)){
				
				$error_msg = 'Check your informations:<br/><ul>'.implode('', $errors).'</ul>';
				$this->_action_msg = ActionMessages::custom_wrong($error_msg);
				return false;
			
			}else{
			
				return true;
			
			}
		
		}
		
		/**
			* Insert a new post into the database
			*
			* @access	private
		*/
		
		private function create(){
		
			if($this->check_post_data()){

				$this->_post->_author = $this->_user['user_id'];	//we set this here because it's specific to the creation
				
				try{
				
					//check if permalink already used
					$to_read['table'] = 'post';
					$to_read['columns'] = array('post_permalink');
					$to_read['condition_columns'][':p'] = 'post_permalink';
					$to_read['condition_select_types'][':p'] = '=';
					$to_read['condition_values'][':p'] = $this->_post->_permalink;
					$to_read['value_types'][':p'] = 'str';
					
					$perm = $this->_db->read($to_read);
					
					if(!empty($perm))
						throw new Exception('Generated permalink already in use. Please change your post title!');
				
					$this->_post->create();
					$this->_action_msg = ActionMessages::new_post_create(true);
					$this->_action = 'to_update';
					
					Session::monitor_activity('created a new article named: "'.$this->_post->_title.'" (status: '.$this->_post->_status.')');
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::new_post_create($e->getMessage());
					$this->_action = 'to_insert';
					
				}

			}else{
			
				$this->_action = 'to_insert';
			
			}
		
		}
		
		/**
			* Update an existing post
			*
			* @access	private
		*/
		
		private function update(){
		
			if($this->check_post_data()){
			
				try{
				
					$old = new Post();
					$old->_id = $this->_post->_id;
					$old->read('_status');
					
					//if post move from draft to published, creation date is updated
					if($old->_status == 'draft' && $this->_post->_status == 'publish'){
					
						$this->_post->_date = date('Y-m-d H:i:s');
						$this->_post->update('_date', 'str');
					
					}
					
					$this->_post->update('_title', 'str');
					$this->_post->update('_content', 'str');
					$this->_post->update('_allow_comment', 'str');
					$this->_post->update('_date', 'str');
					$this->_post->update('_status', 'str');
					$this->_post->update('_tags', 'str');
					$this->_post->update('_category', 'str');
					
					if($this->_post->_status == 'publish'){
					
						$this->_post->_updated = 'yes';
						$this->_post->_update_author = $this->_user['user_id'];
						$this->_post->update('_updated', 'str');
						$this->_post->update('_update_author', 'int');
					
					}
					
					$this->_action_msg = ActionMessages::post_update(true);
					
					Session::monitor_activity('updated the post "'.$this->_post->_title.'" (status: '.$this->_post->_status.')');
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::post_update(ucfirst($e->getMessage()));
				
				}
			
			}
			
			$this->_action = 'to_update';
		
		}
	
	}

?>