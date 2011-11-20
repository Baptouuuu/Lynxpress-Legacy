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
	
	namespace Admin\Comments;
	use \Admin\Master as Master;
	use Exception;
	use \Library\Variable\Request as VRequest;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Get as VGet;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Library\Models\Post as Post;
	use \Library\Models\Comment as Comment;
	use \Library\Models\Media as Media;
	use \Admin\Helper\Helper as Helper;
	
	/**
		* Manage Comments
		*
		* Handle comments moderation, comment edition and the possibility to reply to a comment
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Comments
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		private $_status = null;
		private $_content = null;
		private $_search = null;
		private $_actions = array('approve', 'unapprove', 'spam', 'unspam', 'trash', 'restore');
		private $_action_value = array('approve' => 'approved', 'unapprove' => 'pending', 'spam' => 'spam', 'unspam' => 'pending', 'trash' => 'trash', 'restore' => 'pending');
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			//set the status in the attribute for more readability
			if(VRequest::comment_status(false) && !isset($_POST['empty'])){
			
				switch(VRequest::comment_status()){
			
					case 'approved':
						$this->_status = 'approved';
						break;
			
					case 'spam':
						$this->_status = 'spam';
						break;
			
					case 'trash':
						$this->_status = 'trash';
						break;
			
					default:
						$this->_status = 'pending';
						break;
			
				}
			
			}else{
			
				$this->_status = 'pending';
			
			}
			
			if(VPost::search_button(false))
				$this->_search = VPost::search('foo');
			
			$this->_title = $this->build_title();
			
			if($this->_user['comments']){
			
				$this->create();
				$this->update();
				$this->delete();
			
				if(VGet::action() != 'reply')
					$this->get_comments();
			
			}
		
		}
		
		/**
			* Retrieve comments from database in function of the status, the type or via a search
			*
			* @access	private
		*/
		
		private function get_comments(){
		
			try{
		
				$to_read['table'] = 'comment';
				$to_read['columns'] = array('COMMENT_ID');
				
				if(VGet::action() == 'by_type' && VGet::id() && VGet::type() && VGet::comment_status()){
					
					$to_read['condition_columns'][':id'] = 'comment_rel_ID';
					$to_read['condition_select_types'][':id'] = '=';
					$to_read['condition_values'][':id'] = VGet::id();
					$to_read['value_types'][':id'] = 'int';
					$to_read['condition_types'][':status'] = 'AND';
					$to_read['condition_columns'][':status'] = 'comment_status';
					$to_read['condition_select_types'][':status'] = '=';
					$to_read['condition_values'][':status'] = $this->_status;
					$to_read['value_types'][':status'] = 'str';
					
				}elseif(VPost::search_button(false)){
					
					$to_read['condition_columns']['group'][':content'] = 'comment_content';
					$to_read['condition_select_types'][':content'] = 'LIKE';
					$to_read['condition_values'][':content'] = '%'.$this->_search.'%';
					$to_read['value_types'][':content'] = 'str';
					$to_read['condition_types'][':name'] = 'OR';
					$to_read['condition_columns']['group'][':name'] = 'comment_name';
					$to_read['condition_select_types'][':name'] = 'LIKE';
					$to_read['condition_values'][':name'] = '%'.$this->_search.'%';
					$to_read['value_types'][':name'] = 'str';
					$to_read['condition_types'][':email'] = 'OR';
					$to_read['condition_columns']['group'][':email'] = 'comment_email';
					$to_read['condition_select_types'][':email'] = 'LIKE';
					$to_read['condition_values'][':email'] = '%'.$this->_search.'%';
					$to_read['value_types'][':email'] = 'str';
					$to_read['condition_types'][':status'] = 'AND';
					$to_read['condition_columns'][':status'] = 'comment_status';
					$to_read['condition_select_types'][':status'] = '=';
					$to_read['condition_values'][':status'] = $this->_status;
					$to_read['value_types'][':status'] = 'str';
					
				}elseif(VGet::action() == 'edit' && VGet::comment_id()){
					
					$to_read['condition_columns'][':id'] = 'COMMENT_ID';
					$to_read['condition_select_types'][':id'] = '=';
					$to_read['condition_values'][':id'] = VGet::comment_id();
					$to_read['value_types'][':id'] = 'int';
					
				}else{
					
					$to_read['condition_columns'][':status'] = 'comment_status';
					$to_read['condition_select_types'][':status'] = '=';
					$to_read['condition_values'][':status'] = $this->_status;
					$to_read['value_types'][':status'] = 'str';
					
				}
				
				$to_read['order'] = array('comment_date', 'DESC');
				
				$this->_content = $this->_db->read($to_read);
				
				if(!empty($this->_content)){
					
					foreach($this->_content as &$comment){
					
						$comment = new Comment($comment['COMMENT_ID']);
						
						if($comment->_rel_type == 'post'){
							
							$post = new Post();
							$post->_id = $comment->_rel_id;
							$post->read('_title');
							$post->read('_permalink');
							
							$comment->_rel_title = $post->_title;
							$comment->_rel_permalink = $post->_permalink;
						
						}elseif($comment->_rel_type == 'media'){
						
							$media = new Media();
							$media->_id = $comment->_rel_id;
							$media->read('_name');
							
							$comment->_rel_title = $media->_name;
							$comment->_rel_permalink = $media->_id;
						
						}
					
					}
				
				}elseif(empty($this->_content) && VGet::action() == 'edit'){
				
					$this->_content[0] = new Comment();
					throw new Exception('Invalid comment!');
				
				}
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Build the html page title
			*
			* @access	private
		*/
		
		private function build_title(){
		
			if(VGet::action() == 'reply')
				return 'Comment > in reponse to "'.VGet::comment_name().'"';
			elseif(VGet::action() == 'edit')
				return 'Edit Comment';			
			elseif(VPost::search_button(false))
				return 'Comments > Search results for "'.$this->_search.'"';
			else
				return 'Comments';
		
		}
		
		/**
			* Display admin part link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::menu($this->_title);
		
		}
		
		/**
			* Display menu of comment status with the number of each one
			*
			* @access	private
		*/
		
		private function display_comment_status(){
		
			$to_read['table'] = 'comment';
			$to_read['columns'] = array('comment_status');
			$status = $this->_db->read($to_read);
			$to_read = null;
			
			$count_pending = 0;
			$count_approved = 0;
			$count_spam = 0;
			$count_trash = 0;
			
			for($i = 0; $i < count($status); $i++){
			
				switch($status[$i]['comment_status']){
					
					case 'pending':
						$count_pending++;
						break;
					
					case 'approved':
						$count_approved++;
						break;
					
					case 'spam':
						$count_spam++;
						break;
					
					case 'trash':
						$count_trash++;
						break;
				
				}
			
			}
			
			$pending = 'Pending';
			$approved = 'Approved';
			$spam = 'Spam';
			$trash = 'Trash';
			
			switch($this->_status){
			
				case 'pending':
					$pending = '<span class="a_selected">Pending</span>';
					break;
			
				case 'approved':
					$approved = '<span class="a_selected">Approved</span>';
					break;
			
				case 'spam':
					$spam = '<span class="a_selected">Spam</span>';
					break;
			
				case 'trash':
					$trash = '<span class="a_selected">Trash</span>';
					break;
			
			}
			
			Html::status_menu($pending, $count_pending, $approved, $count_approved, $spam, $count_spam, $trash, $count_trash);
		
		}
		
		/**
			* Display applicable action buttons on comments
			*
			* @access	private
			* @param	string [$position] $position can only contain 'top' or 'butt'
		*/
		
		private function display_actions($position){
		
			if($position == 'top'){
				
				switch($this->_status){
				
					case 'approved':
						Html::select_action('o');
						Html::opt_unapprove();
						Html::opt_spam();
						Html::opt_trash();
						Html::select_action('c');
						Html::apply();
						break;
						
					case 'spam':
						Html::select_action('o');
						Html::opt_approve();
						Html::opt_unspam();
						Html::opt_delete();
						Html::select_action('c');
						Html::apply();
						break;
						
					case 'trash':
						Html::select_action('o');
						Html::opt_restore();
						Html::opt_delete();
						Html::select_action('c');
						Html::apply();
						break;
						
					default:
						Html::select_action('o');
						Html::opt_approve();
						Html::opt_spam();
						Html::opt_trash();
						Html::select_action('c');
						Html::apply();
						break;
				
				}
			
			}elseif($position == 'butt'){
			
				switch($this->_status){
			
					case 'approved':
						Html::apply();
						Html::status($this->_status);
						break;
							
					case 'spam':
						Html::select_action('c');
						Html::apply();
						Html::b_empty('Empty Spam');
						Html::status($this->_status);
						break;
							
					case 'trash':
						Html::apply();
						Html::b_empty('Empty Trash');
						Html::status($this->_status);
						break;
							
					default:
						Html::apply();
						Html::status($this->_status);
						break;
				
				}
			
			}
		
		}
		
		/**
			* Display a html table with retrieved comments
			*
			* @access	private
		*/
		
		private function display_table(){
		
			Html::table('o');
					
			foreach($this->_content as $comment){
			
				switch($this->_status){
					
					case 'pending':
						$actions = Html::h_approve($comment->_id, $this->_status);
						$actions .= Html::h_reply($comment->_id, $comment->_name, $comment->_rel_id, $comment->_rel_type);
						$actions .= Html::h_edit($comment->_id, $this->_status);
						$actions .= Html::h_spam($comment->_id, $this->_status);
						$actions .= Html::h_trash($comment->_id, $this->_status);
						break; 
					
					case 'approved':
						$actions = Html::h_unapprove($comment->_id, $this->_status);
						$actions .= Html::h_reply($comment->_id, $comment->_name, $comment->_rel_id, $comment->_rel_type);
						$actions .= Html::h_edit($comment->_id, $this->_status);
						$actions .= Html::h_spam($comment->_id, $this->_status);
						$actions .= Html::h_trash($comment->_id, $this->_status);
						break;
					
					case 'spam':
						$actions = Html::h_edit($comment->_id, $this->_status);
						$actions .= Html::h_unspam($comment->_id, $this->_status);
						$actions .= Html::h_delete($comment->_id, $this->_status);
						break;
					
					case 'trash':
						$actions = Html::h_restore($comment->_id, $this->_status);
						$actions .= Html::h_delete($comment->_id, $this->_status);
						break;
				
				}
				
				if($comment->_rel_type == 'post'){
				
					$pre_permalink = 'ctl=posts&news=';
					$link_edit = 'index.php?ns=posts&ctl=add&action=edit&id='.$comment->_rel_id.'">'.$comment->_rel_title.'</a> (<a href="'.PATH.'index.php?ctl=posts&news='.$comment->_rel_permalink.'" target="_blank">View</a>)<br/>';
				
				}elseif($comment->_rel_type == 'media'){
				
					$pre_permalink = 'ctl=albums&album=';
					$link_edit = 'index.php?ns=media&ctl=albums&action=edit&id='.$comment->_rel_id.'">'.$comment->_rel_title.'</a> (<a href="'.PATH.'index.php?ctl=albums&album='.$comment->_rel_id.'" target="_blaank">View</a>)<br/>';
				
				}
				
				Html::table_row($comment->_id, $comment->_name, $comment->_email, $pre_permalink, $comment->_rel_permalink, $comment->_date, $comment->_content, $actions, $link_edit, $comment->_rel_id, $comment->_rel_type, $this->_status);
				
			}
			
			Html::table('c');
		
		}
		
		/**
			* Display a form to reply directly to another comment
			*
			* @access	private
		*/
		
		private function display_reply(){
		
			Html::reply(VGet::comment_id(), VGet::comment_name(), VGet::id(), VGet::type());
		
		}
		
		/**
			* Display a form to edit a comment
			*
			* @access	private
		*/
		
		private function display_edit(){
		
			Html::edit($this->_content[0]->_date, VGet::comment_id(), $this->_content[0]->_name, $this->_content[0]->_email, $this->_content[0]->_content);
		
		}
		
		/**
			* Method that display the page
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			if($this->_user['comments']){
				
				echo $this->_action_msg.
					'<div id="list_wrapper">';
				
				Html::form('o', 'post', 'index.php?ns=comments&ctl=manage');
				
				if(VGet::action() == 'edit'){
				
					$this->display_edit();
				
				}elseif(VGet::action() == 'reply'){
				
					$this->display_reply();
				
				}else{
				
					$this->display_comment_status();
					
					if(!empty($this->_content)){
					
						$this->display_actions('top');
						$this->display_table();
						$this->display_actions('butt');
					
					}else{
					
						Html::table('o');
						
						if(VPost::search_button(false)){
					
							$no_post = '<tr><td colspan="4">No comments found';
							if($this->_status == 'trash')
								$no_post .= ' in Trash';
							
							echo $no_post.'</td></tr>';
						
						}else{
						
							echo '<tr><td colspan="4">There is no comments yet.</td></tr>';
						
						}
						
						Html::table('c');
			
					}
				}
				
				echo Helper::datalist('names', $this->_content, '_name');
				
				Html::form('c');
				
				echo '</div>';
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Method that permits to create a new comment
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::submit() == 'Submit Reply'){
			
				$comment = new Comment();
				$comment->_name = $this->_user['user_username'];
				$comment->_email = $this->_user['user_email'];
				$comment->_content = VPost::comment_content();
				$comment->_rel_id = VPost::id();
				$comment->_rel_type = VPost::type();
				$comment->_status = 'approved';
				
				try{
				
					$comment->create();
					
					$this->_action_msg = ActionMessages::reply_comment(true);
				
				}catch(Exception $e){
					
					$this->_action_msg = ActionMessages::reply_comment($e->getMessage());
					
				}
				
			}
		
		}
		
		/**
			* Method that permits to update a comment
			*
			* @access	private
		*/
		
		private function update(){
		
			$action_return = array();
		
			if(VPost::submit() == 'Update Comment'){
				
				$comment = new Comment();
				$comment->_id = VPost::comment_id();
				$comment->_name = VPost::comment_name();
				$comment->_email = VPost::comment_email();
				$comment->_content = VPost::comment_content();
				$comment->_status = VPost::comment_status();
				
				try{
					
					$comment->update('_name', 'str');
					$comment->update('_email', 'str');
					$comment->update('_content', 'str');
					$comment->update('_status', 'str');
					
					array_push($action_return, true);
				
				}catch(Exception $e){
				
					array_push($action_return, false);
				
				}
				
			}elseif((VGet::action() || VPost::apply_action(false)) && in_array(VRequest::action(), $this->_actions) && VRequest::comment_id()){

				if(VPost::apply_action(false)){
					
					foreach(VPost::comment_id() as $id){
						
						try{
							
							$comment = new Comment();
							$comment->_id = $id;
							$comment->_status = $this->_action_value[VPost::action()];
							$comment->update('_status', 'str');
							unset($comment);
							
							array_push($action_return, true);
						
						}catch(Exception $e){
						
							array_push($action_return, false);
						
						}
						
					}
					
				}else{
					
					try{
						
						$comment = new Comment();
						$comment->_id = VGet::comment_id();
						$comment->_status = $this->_action_value[VGet::action()];
						$comment->update('_status', 'str');
						unset($comment);
						
						array_push($action_return, true);
					
					}catch(Exception $e){
					
						array_push($action_return, false);
					
					}
					
				}
			
			}
			
			if(!empty($action_return))
				$this->_action_msg = ActionMessages::update_comment($action_return);
		
		}
		
		/**
			* Method that permits to delete one or more comments at a time
			*
			* @access	private
		*/
		
		private function delete(){
		
			if((isset($_POST['empty']) || VRequest::action() == 'delete') && $this->_user['delete_content']){
				
				if(isset($_POST['empty']) && VPost::comment_status() && in_array(VPost::comment_status(), array('spam', 'trash'))){
					
					$to_delete['table'] = 'comment';
					$to_delete['condition_columns'][':status'] = 'comment_status';
					$to_delete['condition_values'][':status'] = VPost::comment_status();
					$to_delete['value_types'][':status'] = 'str';
					$global_result = $this->_db->delete($to_delete);
					
				}elseif(VPost::action() == 'delete' && VPost::comment_id()){
					
					$results = array();
					$global_result = true;
					
					foreach(VPost::comment_id() as $id){
					
						try{
							
							$comment = new Comment();
							$comment->_id = $id;
							$comment->delete();
							unset($comment);
							
							array_push($results, true);
						
						}catch(Exception $e){
						
							array_push($results, false);
						
						}
					
					}
					
					foreach($results as $result){
						
						if($result !== true)
							$global_result = false;
					}
					
				}elseif(VGet::action() == 'delete' && VGet::comment_id()){
				
					try{
						
						$comment = new Comment();
						$comment->_id = VGet::comment_id();
						$comment->delete();
						$global_result = true;
						
					}catch(Exception $e){
					
						$global_result = false;
					
					}
				
				}
				
				if(isset($global_result))
					$this->_action_msg = ActionMessages::deleted($global_result);
				
			}elseif((isset($POST['empty']) || VRequest::action() == 'delete') && $this->_user['delete_content'] === false){
			
				$this->_action_msg = ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>