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
	use \Admin\Master\Master as Master;
	use Exception;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Request as VRequest;
	use \Library\Variable\Get as VGet;
	use \Library\Models\Post as Post;
	use \Library\Models\User as User;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Admin\Session\Session as Session;
	use \Admin\Helper\Helper as Helper;
	
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

?>