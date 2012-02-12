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
	use \Library\Variable\Get as VGet;
	use \Library\Models\User as User;
	use \Library\File\File as File;
	use Exception;
	
	/**
		* Rpc Post
		*
		* Permits to retrieve a whole post with associated comments
		* CURL GET http://:website/admin/?ns=rpc&ctl=post&slug=:post_permalink
		* Return a JSON file
		*
		* @package		Administration
		* @namespace	Rpc
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Post extends Master{
	
		private $_slug = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			try{
			
				$this->_slug = VGet::slug();
				
				if(empty($this->_slug))
					throw new Exception('No permalink found!');
				
				$this->_url = 'cache/post/'.$this->_slug.'.json';
				
				if($this->check_cache() === false){
				
					$this->get_content();
					
					$cache = new File();
					$cache->_content = json_encode($this->_content);
					$cache->save($this->_url);
				
				}else{
				
					$cache = File::read($this->_url);
					$this->_content = json_decode($cache->_content, true);
				
				}
			
			}catch(Exception $e){
			
				$this->_content = array('message' => $e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve a post from a given permalink, and with associated comments
			*
			* @access	private
		*/
		
		private function get_content(){
		
			try{
				
				$to_read['table'] = 'post';
				$to_read['columns'] = array('POST_ID', 'post_title', 'post_content', 'post_allow_comment', 'post_date', 'post_author');
				$to_read['condition_columns'][':p'] = 'post_permalink';
				$to_read['condition_select_types'][':p'] = '=';
				$to_read['condition_values'][':p'] = $this->_slug;
				$to_read['value_types'][':p'] = 'str';
				$to_read['condition_types'][':s'] = 'AND';
				$to_read['condition_columns'][':s'] = 'post_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				
				$post = $this->_db->read($to_read);
				
				if(empty($post))
					throw new Exception('No post found!');
				
				$user = new User();
				$user->_id = $post[0]['post_author'];
				$user->read('_publicname');
				
				$post[0]['post_author'] = $user->_publicname;
				
				$this->_content['post'] = $post[0];
				
				if($post[0]['post_allow_comment'] == 'open'){
				
					$to_read = null;
					
					$to_read['table'] = 'comment';
					$to_read['columns'] = array('comment_name', 'comment_content', 'comment_date');
					$to_read['condition_columns'][':r'] = 'comment_rel_id';
					$to_read['condition_select_types'][':r'] = '=';
					$to_read['condition_values'][':r'] = $post[0]['POST_ID'];
					$to_read['value_types'][':r'] = 'int';
					$to_read['condition_types'][':t'] = 'AND';
					$to_read['condition_columns'][':t'] = 'comment_rel_type';
					$to_read['condition_select_types'][':t'] = '=';
					$to_read['condition_values'][':t'] = 'post';
					$to_read['value_types'][':t'] = 'str';
					$to_read['condition_types'][':s'] = 'AND';
					$to_read['condition_columns'][':s'] = 'comment_status';
					$to_read['condition_select_types'][':s'] = '=';
					$to_read['condition_values'][':s'] = 'approved';
					$to_read['value_types'][':s'] = 'str';
					$to_read['order'] = array('comment_date', 'DESC');
					
					$this->_content['comments'] = $this->_db->read($to_read);
				
				}else{
				
					$this->_content['comments'] = array();
				
				}
			
			}catch(Exception $e){
			
				$this->_content = array('message' => $e->getMessage());
			
			}
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			echo json_encode($this->_content);
		
		}
	
	}

?>