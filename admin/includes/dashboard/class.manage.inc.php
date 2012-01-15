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
	
	namespace Admin\Dashboard;
	use \Admin\Master\Master as Master;
	use Exception;
	use \Library\Models\User as User;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Library\Models\Comment as Comment;
	use \Library\Models\Post as Post;
	use \Library\Models\Media as Media;
	use \Admin\Helper\Helper as Helper;
	
	/**
		* Manage Dashboard
		*
		* Display little widgets about some informations of the website
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Dashboard
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		private $_activity = null;
		private $_comments = null;
		private $_last_post_id = null;
		private $_drafts = null;
		private $_events = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Dashboard';
			
			if($this->_user['dashboard']){
			
				$this->get_activity();
				$this->get_recent_comments();
				$this->get_draft();
				Helper::get_categories($this->_categories, $this->_action_msg, 'post');
				
				if(Helper::check_update() === true)
					$this->_action_msg = ActionMessages::ws_update_check(true, true);
			
			}
		
		}
		
		/**
			* Retrieve admin side logged activity
			*
			* @access	private
		*/
		
		private function get_activity(){
		
			if($this->_user['settings']){
			
				try{
				
					$to_read['table'] = 'activity';
					$to_read['columns'] = array('USER_ID', 'data', 'date');
					$to_read['limit'] = array(0, 10);
					$to_read['order'] = array('date', 'desc');
					
					$this->_activity = $this->_db->read($to_read);
					
					if(!empty($this->_activity)){
					
						foreach($this->_activity as &$act){
						
							$user = new User();
							$user->_id = $act['USER_ID'];
							$user->read('_username');
							$act['name'] = $user->_username;
						
						}
					
					}
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
		
		/**
			* Retrieve some recent comments pending to be approved
			*
			* @access	private
		*/
		
		private function get_recent_comments(){
		
			if($this->_user['comments']){
				
				try{
				
					$to_read['table'] = 'comment';
					$to_read['columns'] = array('COMMENT_ID');
					$to_read['condition_columns'][':status'] = 'comment_status';
					$to_read['condition_select_types'][':status'] = '=';
					$to_read['condition_values'][':status'] = 'pending';
					$to_read['value_types'][':status'] = 'str';
					$to_read['order'] = array('comment_date', 'DESC');
					$to_read['limit'] = array(0, 3);
					
					$this->_comments = $this->_db->read($to_read);
					
					if(!empty($this->_comments)){
					
						foreach($this->_comments as &$comment){
						
							$comment = new Comment($comment['COMMENT_ID']);
							
							if($comment->_rel_type == 'post'){
								
								$post = new Post();
								$post->_id = $comment->_rel_id;
								$post->read('_title');
								$post->read('_permalink');
								
								$comment->_rel_title = $post->_title;
								$comment->_rel_permalink = 'ctl=posts&news='.$post->_permalink;
							
							}elseif($comment->_rel_type == 'media'){
							
								$media = new Media();
								$media->_id = $comment->_rel_id;
								$media->read('_name');
								
								$comment->_rel_title = $media->_name;
								$comment->_rel_permalink = 'ctl=albums&album='.$media->_id;
							
							}
						
						}
					
					}
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
				
			}
		
		}
		
		/**
			* Retrieve some drafted post
			*
			* @access	private
		*/
		
		private function get_draft(){
		
			if($this->_user['post']){
				
				try{
				
					$to_read['table'] = 'post';
					$to_read['columns'] = array('POST_ID', 'post_title', 'post_content', 'post_date');
					$to_read['condition_columns'][':status'] = 'post_status';
					$to_read['condition_select_types'][':status'] = '=';
					$to_read['condition_values'][':status'] = 'draft';
					$to_read['value_types'][':status'] = 'str';
					
					$this->_drafts = $this->_db->read($to_read);
					
					if(!empty($this->_drafts))
						foreach($this->_drafts as &$post)
							$post = new Post($post['POST_ID']);
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
		
		/**
			* Display part link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::menu();
		
		}
		
		/**
			* Display admin side logged activity
			*
			* @access	private
		*/
		
		private function display_activity(){
		
			if($this->_user['settings']){
			
				Html::widget_activity('o');
					 	
			 	if(empty($this->_activity))
			 		echo 'No activity';
			 	else
			 		foreach($this->_activity as $act)
			 			Html::activity($act['name'], $act['data'], $act['date']);
					 		
				Html::widget_activity('c');
			
			}
		
		}
		
		/**
			* Display comments retrieved in the get_recent_comments method
			*
			* @access	private
		*/
		
		private function display_recent_comments(){
		
			if($this->_user['comments']){
				Html::widget_comments('o');
						
				if(empty($this->_comments))
					echo 'There is no comment yet';
				else
					foreach($this->_comments as $comment)
						Html::comment($comment->_id, $comment->_name, $comment->_rel_permalink, $comment->_rel_title, $comment->_content);
				
				Html::widget_comments('c');
				
			}
		
		}
		
		/**
			* Display a quick form to publish a post
			*
			* @access	private
		*/
		
		private function display_quickpress(){
		
			if($this->_user['post']){
				
				Html::widget_quickpress('o');
								
				foreach($this->_categories as $key => $cat)
					Html::category($key, $cat);
								
				Html::widget_quickpress('c');
			
			}
		
		}
		
		/**
			* Display post drafts retrieved in the get_draft method
			*
			* @access	private
		*/
		
		private function display_draft(){
		
			if($this->_user['post']){
				
				Html::widget_draft('o');
					
				if(empty($this->_drafts))
					echo '<li id="no_draft">There no draft at the moment</li>';
				else
					foreach($this->_drafts as $draft)
						Html::draft($draft->_id, $draft->_title, $draft->_date, $draft->_content);
				
				Html::widget_draft('c');
			
			}
		
		}
		
		/**
			* Method that display the page
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			if($this->_user['dashboard']){
			
				echo $this->_action_msg;
				
				echo '<div id="dashbord_wrapper">'.
					 	'<div class="widget_wrapper widget_wrapper_left">';
						
				$this->display_activity();
				$this->display_recent_comments();
				
				echo '</div>'.
					'<div class="widget_wrapper">';
					
				$this->display_quickpress();
				$this->display_draft();
				
				echo '</div>';
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
	
	}

?>