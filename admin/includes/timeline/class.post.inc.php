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
	
	namespace Admin\Timeline;
	use \Admin\Master\Master as Master;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Library\Models\Setting as Setting;
	use \Library\Variable\Get as VGet;
	use \Library\Curl\Curl as Curl;
	use \Library\Variable\Post as VPost;
	use \Library\Models\User as User;
	use Exception;
	
	/**
		* Display a distant website post
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Timeline
		* @author		Baptiste Langlade baptouuuu@gmail.com
		* @final
	*/
	
	final class Post extends Master{
	
		private $_prefs = null;
		private $_key = null;
		private $_slug = null;
		private $_content = null;
		private $_url = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_key = VGet::website();
			$this->_slug = VGet::slug();
			
			$this->get_prefs();
			$this->get_post();
			$this->build_title();
			
			$this->create();
		
		}
		
		/**
			* Retrieve user preferences
			*
			* @access	private
		*/
		
		private function get_prefs(){
		
			try{
			
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('SETTING_ID');
				$to_read['condition_columns'][':t'] = 'setting_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'user_'.$this->_user['user_id'];
				$to_read['value_types'][':t'] = 'str';
				
				$pref = $this->_db->read($to_read);
				
				if(empty($pref))
					throw new Exception('Preferences not found!');
				
				$this->_prefs = new Setting($pref[0]['SETTING_ID']);
				
				$this->_prefs->_data = json_decode($this->_prefs->_data, true);
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve wished post
			*
			* @access	private
		*/
		
		private function get_post(){
		
			if(!empty($this->_prefs)){
			
				try{
				
					if(empty($this->_slug))
						throw new Exception('Post permalink missing!');
					
					if(!isset($this->_prefs->_data['timeline'][$this->_key]))
						throw new Exception('Wished website not found!');
					
					$url = $this->_prefs->_data['timeline'][$this->_key]['url'].'admin/index.php?ns=rpc&ctl=post&slug='.$this->_slug;
					
					$curl = new Curl($url);
					$curl->_content = json_decode($curl->_content, true);
					
					if(isset($curl->_content['message']))
						throw new Exception('Error on distant website! '.$this->_prefs->_data['timeline'][$this->_key]['title'].' says "'.$curl->_content['message'].'"');
					
					$this->_content = $curl->_content;
					
					$this->_url = $this->_prefs->_data['timeline'][$this->_key]['url'].'?ctl=posts&news='.$this->_slug;
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
		
		/**
			* Build page title
			*
			* @access	private
		*/
		
		private function build_title(){
		
			if(!empty($this->_content))
				$this->_title = 'Timeline > '.$this->_prefs->_data['timeline'][$this->_key]['title'].' > '.$this->_content['post']['post_title'];
			else
				$this->_title = 'Timeline > Post error';
		
		}
		
		/**
			* Display part link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			$website = 'Error';
			
			if(isset($this->_prefs->_data['timeline'][$this->_key]))
				$website = $this->_prefs->_data['timeline'][$this->_key]['title'];
			
			Html::pt_menu($website);
		
		}
		
		/**
			* Display retrieved post
			*
			* @access	private
		*/
		
		private function display_post(){
		
			Html::pt_post($this->_content['post']['post_title'], $this->_content['post']['post_content'], $this->_content['post']['post_date'], $this->_content['post']['post_author'], $this->_prefs->_data['timeline'][$this->_key]['title'], $this->_url);
		
		}
		
		/**
			* Display comments
			*
			* @access	private
		*/
		
		private function display_comments(){
		
			if($this->_content['post']['post_allow_comment'] == 'open'){
			
				Html::pt_comments('o');
			
				foreach($this->_content['comments'] as $com)
					Html::pt_comment($com['comment_name'], $com['comment_content'], $com['comment_date']);
			
				Html::pt_comments('c');
			
			}
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			echo $this->_action_msg;
			
			if(!empty($this->_content)){
			
				$this->display_comments();
				$this->display_post();
			
			}
		
		}
		
		/**
			* Create a comment on a distant website
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::submit(false) && VPost::content(false)){
			
				try{
				
					$user = new User();
					$user->_id = $this->_user['user_id'];
					$user->read('_publicname');
					$user->read('_email');
				
					$data = array('name' => $user->_publicname, 'email' => $user->_email, 'content' => VPost::content(), 'id' => $this->_content['post']['POST_ID'], 'type' => 'post');
					
					$url = $this->_prefs->_data['timeline'][$this->_key]['url'].'admin/index.php?ns=rpc&ctl=comment';
					
					$curl = new Curl();
					$curl->_post = true;
					$curl->_data = $data;
					$curl->_url = $url;
					$curl->connect();
					
					$msg = json_decode($curl->_content, true);
					
					if($msg['message'] !== true)
						throw new Exception('Error on distant website! '.$this->_prefs->_data['timeline'][$this->_key]['title'].' says "'.$msg['message'].'"');
					
					$this->_action_msg = ActionMessages::custom_good('Comment submitted');
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
	
	}

?>