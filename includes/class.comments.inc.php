<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011-2012
		* @license		http://www.gnu.org/licenses/gpl.html GNU GPL V3
		* @package		Lynxpress
		* @subpackage	Site
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
	
	namespace Site;
	use \Library\Models\Comment as Comment;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Session as VSession;
	use Exception;
	
	/**
		* Comments
		*
		* Handles displaying and submitting comments
		*
		* @package		Site
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0.1
		* @final
	*/
	
	final class Comments extends Master{
	
		private $_id = null;
		private $_comments = null;
		private $_comment = null;
		private $_question = null;
		private $_errors = array('name' => '', 'email' => '', 'content' => '', 'question' => '');
		private $_submitted = null;
		const SQL_TABLE = 'comment';
		const CONTROLLER = false;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	integer [$id] Element id
		*/
		
		public function __construct($id){
		
			if(empty($id))
				throw new Exception('Element id missing');
			
			parent::__construct();
			
			$this->_id = $id;
			
			$this->get_content();
			
			$this->_comment = new Comment();
			
			if(VSession::visitor_name(false) && VSession::visitor_email(false)){
			
				$this->_comment->_name = VSession::visitor_name();
				$this->_comment->_email = VSession::visitor_email();
			
			}
			
			$this->question();
			
			if(VGet::respond_to())
				$this->_comment->_content = '@'.VGet::respond_to();
			
			if(VPost::submit_comment(false))
				$this->create();
		
		}
		
		/**
			* Retrieve comments
			*
			* @access	private
		*/
		
		private function get_content(){
		
			try{
			
				$to_read['table'] = self::SQL_TABLE;
				$to_read['columns'] = array('COMMENT_ID');
				$to_read['condition_types'] = array(':rt' => 'AND', ':st' => 'AND');
				$to_read['condition_columns'] = array(':ri' => 'comment_rel_id', ':rt' => 'comment_rel_type', ':st' => 'comment_status');
				$to_read['condition_select_types'] = array(':ri' => '=', ':rt' => '=', ':st' => '=');
				$to_read['condition_values'] = array(':ri' => $this->_id, ':rt' => $this->_sql_table, ':st' => 'approved');
				$to_read['value_types'] = array(':ri' => 'int', ':rt' => 'str', ':st' => 'str');
				$to_read['order'] = array('comment_date', 'ASC');
				
				$this->_comments = $this->_db->read($to_read);
				
				if(!empty($this->_comments))
					foreach($this->_comments as &$comment)
						$comment = new Comment($comment['COMMENT_ID']);
			
			}catch(Exception $e){
			
				@error_log($e->getMessage().' file: '.__FILE__.'; line: '.__LINE__, 1, WS_EMAIL);
			
			}
		
		}
		
		/**
			* Create random numbers to see if it's a human
			*
			* @access	private
		*/
		
		private function question(){
		
			$this->_question[] = mt_rand(0, 10);
			$this->_question[] = mt_rand(0, 10);
			$this->_question[] = md5($this->_question[0] + $this->_question[1]);
		
		}
		
		/**
			* Build link for comments permalink
			*
			* @access	private
			* @return	string
		*/
		
		private function build_link(){
		
			switch($this->_pid){
			
				case 'posts':
					$link = 'ctl=posts&news='.VGet::news();
					break;
				
				case 'albums':
					$link = 'ctl=albums&&album='.VGet::album();
					break;
			
			}
			
			return $link;
		
		}
		
		/**
			* Display comment form
			*
			* @access	private
		*/
		
		private function display_form(){
		
			(empty($this->_comments))?$form_side_image = 'new':$form_side_image = 'persuit';
			
			if($this->_submitted === true)
				Html::submitted_form(true);
			elseif($this->_submitted === false && (empty($this->_errors['name']) || empty($this->_errors['email']) || empty($this->_errors['content']) || empty($this->_errors['question'])))
				Html::submitted_form(false);
			else
				Html::comment_form($this->_comment->_name, $this->_comment->_email, $this->_comment->_content, $this->_errors['name'], $this->_errors['email'], $this->_errors['content'], $this->_errors['question'], $form_side_image, $this->_question[0], $this->_question[1], $this->_question[2]);
		
		}
		
		/**
			* Display retrieved comments
			*
			* @access	private
		*/
		
		private function display_comments(){
		
			if(!empty($this->_comments)){
			
				$com_link = $this->build_link();
			
				Html::html5('o');
				
				foreach($this->_comments as $comment)
					Html::comment($comment->_id, $comment->_name, $comment->_date, $com_link, nl2br($comment->_content), $comment->_email);
				
				Html::html5('c');
			
			}
		
		}
		
		/**
			* Display comments part
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_form();
			$this->display_comments();
		
		}
		
		/**
			* Set data in a comment object and returns errors if data doesn't fit
			*
			* @access	private
		*/
		
		private function check_data(){
		
			if(!VPost::respond_name())
				$this->_errors['name'] = true;
			else
				$this->_comment->_name = VPost::respond_name();
			
			if(!VPost::respond_email() || !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , VPost::respond_email()))
				$this->_errors['email'] = true;
			else
				$this->_comment->_email = VPost::respond_email();
			
			if(!VPost::respond_content())
				$this->_errors['content'] = true;
			else
				$this->_comment->_content = VPost::respond_content();
			
			if(!VPost::number() || (md5(VPost::number()) != VPost::result()))
				$this->_errors['question'] = true;
				
			if(!empty($this->_errors['name']) || !empty($this->_errors['email']) || !empty($this->_errors['content']) || !empty($this->_errors['question']))
				return false;
			else
				return true;
		
		}
		
		/**
			* Create new comment
			*
			* @access	private
		*/
		
		private function create(){
		
			if($this->check_data()){
			
				try{
				
					$this->_comment->_name = VPost::respond_name();
					$this->_comment->_email = VPost::respond_email();
					$this->_comment->_content = VPost::respond_content();
					$this->_comment->_rel_id = $this->_id;
					$this->_comment->_rel_type = $this->_sql_table;
					$this->_comment->_status = 'pending';
					
					$this->_comment->create();
					
					$this->_submitted = true;
					
					$_SESSION['visitor_name'] = $this->_comment->_name;
					$_SESSION['visitor_email'] = $this->_comment->_email;
				
				}catch(Exception $e){
				
					$this->_submitted = false;
				
				}
			
			}
		
		}
	
	}

?>