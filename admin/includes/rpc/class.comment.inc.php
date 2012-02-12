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
	
	namespace Admin\Rpc;
	use \Library\Variable\Post as VPost;
	use \Library\Models\Comment as MComment;
	use Exception;
	
	/**
		* Post a comment from a distant website
		*
		* CURL POST to http://:website/admin/?ns=rpc&ctl=comment with an array as parameter
		* Array as to be built as follow:
		* array(
		* 	'name' => 'username',
		*	'content' => 'comment content',
		*	'email' => 'user email',
		*	'id' => 'post or album id',
		*	'type' => 'post or media'
		* )
		* Return a JSON file
		*
		* @package		Administration
		* @namespace	Rpc
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Comment extends Master{
	
		private $_data = null;
		private $_return_msg = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->create();
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			echo json_encode($this->_return_msg);
		
		}
		
		/**
			* Check if post data are correct
			*
			* @access	private
		*/
		
		private function check_data(){
		
			$this->_data = VPost::all();
			
			if(!isset($this->_data['name']) || !isset($this->_data['content']) || !isset($this->_data['email']) || !isset($this->_data['id']) || !isset($this->_data['type'])){
			
				$this->_return_msg = array('message' => 'Data missing!');
				return false;
			
			}
			
			return true;
		
		}
		
		/**
			* Create a comment
			*
			* @access	private
		*/
		
		private function create(){
		
			if($this->check_data()){
			
				try{
				
					$class = '\\Library\\Models\\'.ucfirst($this->_data['type']);
					
					$el = new $class();
					$el->_id = $this->_data['id'];
					$el->read('_allow_comment');
					
					if($el->_allow_comment == 'closed')
						throw new Exception('Comments are closed!');
					
					$comment = new MComment();
					$comment->_name = $this->_data['name'];
					$comment->_email = $this->_data['email'];
					$comment->_content = $this->_data['content'];
					$comment->_rel_id = $this->_data['id'];
					$comment->_rel_type = $this->_data['type'];
					$comment->_status = 'pending';
					$comment->create();
					
					$this->_return_msg = array('message' => true);
				
				}catch(Exception $e){
				
					$this->_return_msg = array('message' => $e->getMessage());
				
				}
			
			}
		
		}
	
	}

?>