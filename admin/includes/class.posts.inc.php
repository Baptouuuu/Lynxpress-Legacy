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
	
	namespace Admin\Posts;
	use \Admin\Master as Master;
	use Exception;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Request as VRequest;
	use \Library\Variable\Get as VGet;
	use \Library\Models\Post as Post;
	use \Library\Models\Setting as Setting;
	use \Library\Models\Media as Media;
	use \Library\Models\User as User;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Admin\Session as Session;
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
		* @version		1.0
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
	
	/**
		* Manage Posts
		*
		* Handles posts administration
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Posts
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		private $_search = null;
		private $_status = null;
		private $_content = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			if(VPost::search_button(false))
				$this->_search = trim(VPost::search('foo'));
			
			//set the status in the attribute for more readability
			if(VRequest::post_status(false) && !VPost::empty_trash(false)){
				
				switch(VRequest::post_status()){
				
					case 'publish':
						$this->_status = 'publish';
						break;
				
					case 'draft':
						$this->_status = 'draft';
						break;
				
					case 'trash':
						$this->_status = 'trash';
						break;
				
					default:
						$this->_status = 'all';
				
				}
			
			}else{
			
				$this->_status = 'all';
			
			}
			
			$this->build_title();
			
			if($this->_user['post']){
			
				Helper::get_categories($this->_categories, $this->_action_msg, 'post');
			
				$this->trash();
				$this->untrash();
				$this->delete();
			
				$this->get_posts();
			
			}
		
		}
		
		/**
			* Retrieve posts from the database
			*
			* @access	private
		*/
		
		private function get_posts(){
		
			try{
			
				$to_read['table'] = 'post';
				$to_read['columns'] = array('POST_ID');
				
				if(VPost::filter(false)){
					
					if(VPost::date('all') == 'all' && VPost::category('all') == 'all'){
						
						switch($this->_status){
						
							case 'all':
								$to_read['condition_select_types'][':status'] = '!=';
								$to_read['condition_values'][':status'] = 'trash';
								break;
						
							default:
								$to_read['condition_select_types'][':status'] = '=';
								$to_read['condition_values'][':status'] = $this->_status;
								break;
						
						}
						
						$to_read['condition_columns'][':status'] = 'post_status';
						$to_read['value_types'][':status'] = 'str';
						
					}else{
						
						if(VPost::date('all') != 'all'){
						
							$to_read['condition_columns'][':date'] = 'post_date';
							$to_read['condition_select_types'][':date'] = 'LIKE';
							$to_read['condition_values'][':date'] = VPost::date().'%';
							$to_read['value_types'][':date'] = 'str';
						
						}
						
						if(VPost::category('all') != 'all'){
						
							$to_read['condition_types'][':cat'] = 'AND';
							$to_read['condition_columns'][':cat'] = 'post_category';
							$to_read['condition_select_types'][':cat'] = 'LIKE';
							$to_read['condition_values'][':cat'] = '%'.VPost::category().'%';
							$to_read['value_types'][':cat'] = 'str';
						
						}
						
						$to_read['condition_types'][':status'] = 'AND';
						$to_read['condition_columns'][':status'] = 'post_status';
						$to_read['value_types'][':status'] = 'str';
						
						switch($this->_status){
						
							case 'all':
								$to_read['condition_select_types'][':status'] = '!=';
								$to_read['condition_values'][':status'] = 'trash';
								break;
						
							default:
								$to_read['condition_select_types'][':status'] = '=';
								$to_read['condition_values'][':status'] = $this->_status;
								break;
						
						}
					
					}
					
				}elseif(VPost::search_button(false)){
					
					$search = '%'.$this->_search.'%';
					
					$to_read['condition_columns'][':search'] = 'post_title';
					$to_read['condition_select_types'][':search'] = 'LIKE';
					$to_read['condition_values'][':search'] = $search;
					$to_read['value_types'][':search'] = 'str';
					$to_read['condition_types'][':status'] = 'AND';
					$to_read['condition_columns'][':status'] = 'post_status';
					$to_read['value_types'][':status'] = 'str';
					
					switch($this->_status){
					
						case 'all':
							$to_read['condition_select_types'][':status'] = '!=';
							$to_read['condition_values'][':status'] = 'trash';
							break;
					
						default:
							$to_read['condition_select_types'][':status'] = '=';
							$to_read['condition_values'][':status'] = $this->_status;
							break;
					
					}
					
				}elseif(VGet::author()){
					
					$to_read['condition_columns'][':author'] = 'post_author';
					$to_read['condition_select_types'][':author'] = '=';
					$to_read['condition_values'][':author'] = VGet::author(1);
					$to_read['value_types'][':author'] = 'str';
					$to_read['condition_types'][':status'] = 'AND';
					$to_read['condition_columns'][':status'] = 'post_status';
					$to_read['condition_select_types'][':status'] = '!=';
					$to_read['condition_values'][':status'] = 'trash';
					$to_read['value_types'][':status'] = 'str';
					
				}elseif($this->_status != 'all'){
					
					$to_read['condition_columns'][':status'] = 'post_status';
					$to_read['condition_select_types'][':status'] = '=';
					$to_read['condition_values'][':status'] = $this->_status;
					$to_read['value_types'][':status'] = 'str';
					
				}else{
					
					$to_read['condition_columns'][':status'] = 'post_status';
					$to_read['condition_select_types'][':status'] = '!=';
					$to_read['condition_values'][':status'] = 'trash';
					$to_read['value_types'][':status'] = 'str';
					
				}
				
				$to_read['order'] = array('post_date', 'desc');
				
				$this->_content = $this->_db->read($to_read);
				
				if(!empty($this->_content)){
				
					foreach($this->_content as &$value){
					
						$id = $value['POST_ID'];
						$value = new Post($id);
						$value->_id = $id;
						$value->read('_title');
						$value->read('_date');
						$value->read('_author');
						$value->read('_status');
						$value->read('_category');
						$value->read('_tags');
						$value->read('_permalink');
					
					}
				
				}
				
				$to_read = null;
				
				//setting the number of comments per post
				$to_read['table'] = 'comment';
				$to_read['columns'] = array('comment_rel_id');
				
				$comments = $this->_db->read($to_read);
				
				if(is_array($comments) && !empty($this->_content)){
					
					foreach($this->_content as &$article){
					
						$count = 0;
						
						foreach($comments as $comment)
							if($comment['comment_rel_id'] == $article->_id)
								$count++;
						
						$article->_comment = $count;
					
					}
					
				}elseif(!empty($this->_content)){
					
					foreach($this->_content as &$article)
						$article->_comment = 0;
				
				}
				
				unset($comments);
				//end comments
				
				//setting the author username per post via its id
				if(!empty($this->_content)){
				
					foreach($this->_content as &$post){
					
						$user = new User();
						$user->_id = $post->_author;
						$user->read('_username');
						$post->_author_name = $user->_username;
					
					}
				
				}
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Build page title
			*
			* @access	private
		*/
		
		private function build_title(){
		
			if(VPost::search_button(false))
				$this->_title = 'Posts > Search results for "'.$this->_search.'"';
			elseif($this->_status != 'all')
				$this->_title = ucfirst($this->_status).'ed Posts';
			else
				$this->_title = 'Posts';
		
		}
		
		/**
			* Display related admin parts link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::mp_menu();
		
		}
		
		/**
			* Display a menu to choose posts type to retrieve
			*
			* @access	private
		*/
		
		private function display_post_status(){
		
			$to_read['table'] = 'post';
			$to_read['columns'] = array('post_status');
			
			$status = $this->_db->read($to_read);
			
			$count_publish = 0;
			$count_draft = 0;
			$count_trash = 0;
			
			foreach($status as $stat){
				
				switch($stat['post_status']){
				
					case 'publish':
						$count_publish++;
						break;
				
					case 'draft':
						$count_draft++;
						break;
				
					case 'trash':
						$count_trash++;
						break;
				
				}
			
			}
			
			$count = $count_publish + $count_draft;
			
			$all = 'All';
			$publish = 'Published';
			$draft = 'Draft';
			$trash = 'Trash';
			
			switch($this->_status){
			
				case 'all':
					$all = '<span class="a_selected">All</span>';
					break;
			
				case 'publish':
					$publish = '<span class="a_selected">Published</span>';
					break;
			
				case 'draft':
					$draft = '<span class="a_selected">Draft</span>';
					break;
			
				case 'trash':
					$trash = '<span class="a_selected">Trash</span>';
					break;
			
			}
			
			Html::mp_type_menu($all, $count, $publish, $count_publish, $draft, $count_draft, $trash, $count_trash);
		
		}
		
		/**
			* Display applicable actions
			*
			* @access	private
			* @param	string [$position] $position can only contain 'top' or 'butt'
		*/
		
		private function display_actions($position){
		
			if(!empty($this->_content)){
			
				if($position == 'top'){
					
					foreach($this->_content as $post){
					
						$key = substr($post->_date, 0, 7);
						$dates[$key] = date('F Y', strtotime($post->_date));
					
					}
					
					$dates = array_unique($dates);
					
					if($this->_status == 'trash'){
					
						Html::mp_restore();
						Html::mp_delete();
					
					}else{
					
						Html::mp_trash();
					
					}
					
					Html::mp_actions('o', $this->_status);
					
					foreach($dates as $key => $date)
						Html::option($key, $date);
					
					Html::mp_actions('m');
						
					foreach($this->_categories as $key => $category)
						Html::option($key, ucwords($category));
						 
					Html::mp_actions('c');
					
					if($this->_status == 'trash')
						Html::mp_empty();
					
				}elseif($position == 'butt'){
					
					if($this->_status == 'trash'){
					
						Html::mp_restore();
						Html::mp_delete();
						Html::mp_empty();
					
					}else{
					
						Html::mp_trash();
					
					}
					
				}
			}
		
		}
		
		/**
			* Display an html table with all retrieved posts
			*
			* @access	private
		*/
		
		private function display_table(){
		
			Html::table('o');
			
			if(!empty($this->_content)){
				
				foreach($this->_content as $post){
				
					$draft_y = null;
					if(($this->_status == 'all' || $this->_status == 'trash') && $post->_status == 'draft')
						$draft_y = ' - <span class="bold">Draft</span>';
						
					$preview = null;
					if($post->_status == 'draft')
						$preview = 'preview=true&';
						
					if($this->_status == 'trash'){
				
						$actions = Html::mp_restore_link($post->_id);
						$actions .= Html::mp_delete_link($post->_id);
										
					}else{
				
						$actions = Html::mp_edit_link($post->_id);
						$actions .= Html::mp_trash_link($post->_id, $this->_status);
						$actions .= Html::mp_view_link($preview, $post->_permalink, $post->_title);
				
					}
					
					$key_cats = explode(',', $post->_category);
					$cats = array();
					
					foreach($key_cats as $key)
						array_push($cats, ucfirst($this->_categories[$key]));
					
					$cats = implode(', ', $cats);
					
					Html::table_row($post->_id, $post->_title, $draft_y, $actions, $post->_author, $post->_author_name, $cats, $post->_tags, $post->_comment, $post->_date);
					
				}
			
			}else{
			
				if(VPost::search_button(false)){
				
					$no_post = '<tr><td colspan="7">No post found';
					if($this->_status == 'trash')
						$no_post .= ' in Trash';
					
					echo $no_post.'</td></tr>';
			
				}else{
			
					if(VRequest::post_status() == 'trash')
						echo '<tr><td colspan="7">No post in Trash</td></tr>';
					else
						echo '<tr><td colspan="7">There is no post yet.</td></tr>';
					
				}
			
			}
			
			Html::table('c');
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			if($this->_user['post']){
			
				echo $this->_action_msg;
				
				echo '<div id="list_wrapper">';
				
				Html::form('o', 'post', 'index.php?ns=posts&ctl=manage');
						
				$this->display_post_status();
				$this->display_actions('top');
				$this->display_table();
				$this->display_actions('butt');
				
				echo Helper::datalist('titles', $this->_content, '_title');
				
				Html::form('c');
				
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update selected posts and set their status to trash
			*
			* @access	private
		*/
		
		private function trash(){
		
			if((VPost::trash(false) || VRequest::action() == 'trash') && $this->_user['delete_content']){
				
				try{
				
					$action_return = array();
					
					if(VPost::trash(false)){
						
						foreach(VPost::post_id(array()) as $id){
						
							$post = new Post();
							$post->_id = $id;
							$post->_status = 'trash';
							$post->update('_status', 'str');
							
							$action_return["$id"] = $post->_result_action;
							unset($post);
						
						}
						
					}elseif(VGet::action() == 'trash' && VGet::id()){
					
						$post = new Post();
						$post->_id = VGet::id();
						$post->_status = 'trash';
						$post->update('_status', 'str');
						
						$action_return[VGet::id()] = $post->_result_action;
						unset($post);
					
					}
					
					$this->_action_msg = ActionMessages::trashed($action_return);
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}elseif((VPost::trash(false) || VRequest::action() == 'trash') && $this->_user['delete_content'] === false){
			
				$this->_action_msg = ActionMessages::action_no_perm();
			
			}
		
		}
		
		/**
			* Update selected posts and set their status to draft
			*
			* @access	private
		*/
		
		private function untrash(){
		
			if(((VGet::action() == 'untrash' && VGet::id()) || VPost::restore(false)) && $this->_user['delete_content']){
				
				try{
				
					$ids = null;
					$action_return = array();
					
					if(VGet::action() == 'untrash' && VGet::id())
						$ids = explode(',', VGet::id());
					elseif(VPost::restore(false))
						$ids = VPost::post_id();
					
					foreach($ids as $id){
					
						$post = new Post();
						$post->_id = $id;
						$post->_status = 'draft';
						$post->update('_status', 'str');
						
						$action_return["$id"] = $post->_result_action;
						unset($post);
					
					}
					
					$this->_action_msg = ActionMessages::untrashed($action_return);
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}elseif(((VGet::action() == 'untrash' && VGet::id()) || VPost::restore(false)) && $this->_user['delete_content'] === false){
			
				$this->_action_msg = ActionMessages::action_no_perm();
			
			}
		
		}
		
		/**
			* Delete selected posts
			*
			* @access	private
		*/
		
		private function delete(){
		
			if((((VRequest::action() == 'delete') && VRequest::id()) || VPost::delete(false) || VPost::empty_trash(false)) && $this->_user['delete_content']){
				
				try{
				
					$post = new Post();
					
					if(VGet::action() == 'delete' && VGet::id()){
						
						$post->_id = VGet::id();
						$post->delete();
						
						$this->_db->query('DELETE FROM `'.DB_PREFIX.'comment` WHERE comment_rel_id = '.VGet::id().' AND comment_rel_type = "post"');
						
						$result = $post->_result_action;
						
					}elseif(VPost::delete(false)){
						
						foreach(VPost::post_id() as $id){
						
							$post->_id = $id;
							$post->delete();
							
							$this->_db->query('DELETE FROM `'.DB_PREFIX.'comment` WHERE comment_rel_id = '.$id.' AND comment_rel_type = "post"');
						
						}
						
						$result = $post->_result_action;
						
					}elseif(VPost::empty_trash(false)){
						
						$to_read['table'] = 'post';
						$to_read['columns'] = array('POST_ID');
						$to_read['condition_columns'][':s'] = 'post_status';
						$to_read['condition_select_types'][':s'] = '=';
						$to_read['condition_values'][':s'] = 'trash';
						$to_read['value_types'][':s'] = 'str';
						
						$posts = $this->_db->read($to_read);
						
						foreach ($posts as $post)
							$this->_db->query('DELETE FROM `'.DB_PREFIX.'comment` WHERE comment_rel_id = '.$post['POST_ID'].' AND comment_rel_type = "post"');
						
						$to_delete['table'] = 'post';
						$to_delete['condition_columns'][':status'] = 'post_status';
						$to_delete['condition_values'][':status'] = 'trash';
						$to_delete['value_types'][':status'] = 'str';
						
						$result = $this->_db->delete($to_delete);
						
					}
					
					Session::monitor_activity('deleted post(s)');
					
					$this->_action_msg = ActionMessages::deleted($result);
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
				
			}elseif(((VRequest::action() == 'delete' && VRequest::id()) || VPost::delete(false) || VPost::empty_trash(false)) && $this->_user['delete_content'] === false){
			
				$this->_action_msg = ActionMessages::action_no_perm();
			
			}
		
		}
	
	}
	
	/**
		* Posts Setting Page
		*
		* Handles posts settings
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Posts
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	class SettingPage extends Master{
	
		private $_setting = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Post Settings';
			
			if($this->_user['settings']){
			
				$this->get_setting();
				
				$this->update();
			
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
				
				if(empty($this->_setting)){
				
					$this->_setting = new Setting();
					$this->_setting->_name = 'Post';
					$this->_setting->_type = 'post';
					$this->_setting->_data = json_encode(array('media' => false));
					
					$this->_setting->create();
					
					$this->_setting->_data = json_decode($this->_setting->_data, true);
				
				}else{
				
					$this->_setting = new Setting($this->_setting[0]['SETTING_ID']);
					$this->_setting->_data = json_decode($this->_setting->_data, true);
				
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
		
			Html::sp_menu();
		
		}
		
		/**
			* Display available buttons
			*
			* @access	private
		*/
		
		private function display_settings(){
		
			Html::settings($this->_setting->_data['media']);
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			if($this->_user['settings']){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', 'index.php?ns=posts&ctl=settingpage');
				
				$this->display_settings();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update setting
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update_setting(false)){
			
				try{
				
					$array = VPost::settings(array('media' => false));
					
					$settings = array('media' => false);
					
					foreach($settings as $key => &$value)
						if(in_array($key, $array))
							$value = true;
					
					$this->_setting->_data = json_encode($settings);
					$this->_setting->update('_data', 'str');
					$this->_setting->_data = json_decode($this->_setting->_data, true);
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}
		
		}
	
	}

?>