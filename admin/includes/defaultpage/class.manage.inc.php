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
	
	namespace Admin\DefaultPage;
	use \Admin\Master\Master as Master;
	use Exception;
	use \Library\Models\Setting as Setting;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Library\Models\Post as Post;
	use \Library\Models\Media as Media;
	use \Library\Variable\Post as Vpost;
	use \Admin\Session\Session as Session;
	
	/**
		* Manage Default Page
		*
		* Display default page selection in front end
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	DefaultPage
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		private $_setting = null;
		private $_content = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Default Page';
			
			if($this->_user['settings']){
			
				$this->get_content();
				
				$this->update();
				
				$method = 'get_'.$this->_setting->_data['type'];
				
				$this->$method();
			
			}
		
		}
		
		/**
			* Retrieve saved setting
			*
			* @access	private
		*/
		
		private function get_content(){
		
			try{
			
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('SETTING_ID');
				$to_read['condition_columns'][':t'] = 'setting_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'default_page';
				$to_read['value_types'][':t'] = 'str';
				
				$this->_setting = $this->_db->read($to_read);
				
				if(empty($this->_setting)){
				
					$this->_setting = new Setting();
					$this->_setting->_name = 'Default Page';
					$this->_setting->_type = 'default_page';
					$this->_setting->_data = json_encode(array('type' => 'posts', 'view' => 'all'));
					
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
			* Retrieve published post
			*
			* @access	private
		*/
		
		private function get_posts(){
		
			try{
			
				$to_read['table'] = 'post';
				$to_read['columns'] = array('POST_ID');
				$to_read['condition_columns'][':s'] = 'post_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				
				$this->_content = $this->_db->read($to_read);
				
				if(!empty($this->_content))
					foreach($this->_content as &$post)
						$post = new Post($post['POST_ID']);
				
				$all = new Post();
				$all->_title = 'All';
				$all->_permalink = 'all';
				
				array_unshift($this->_content, $all);
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve published albums
			*
			* @access	private
		*/
		
		private function get_albums(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('MEDIA_ID');
				$to_read['condition_columns'][':t'] = 'media_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'album';
				$to_read['value_types'][':t'] = 'str';
				$to_read['condition_types'][':s'] = 'AND';
				$to_read['condition_columns'][':s'] = 'media_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				
				$this->_content = $this->_db->read($to_read);
				
				if(!empty($this->_content))
					foreach($this->_content as &$album)
						$album = new Media($album['MEDIA_ID']);
				
				$all = new Media();
				$all->_name = 'All';
				$all->_id = 'all';
				
				array_unshift($this->_content, $all);
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve published videos
			*
			* @access	private
		*/
		
		private function get_video(){
		
			try{
			
				$this->_content = array();
				
				$all = new Media();
				$all->_name = 'All';
				$all->_id = 'all';
				
				array_unshift($this->_content, $all);
			
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
		
			Html::menu();
		
		}
		
		/**
			* Display default page selection form
			*
			* @access	private
		*/
		
		private function display_settings(){
		
			Html::selection_type($this->_setting->_data);
			
			$method = 'display_'.$this->_setting->_data['type'];
			
			$this->$method();
		
		}
		
		/**
			* Display post listing
			*
			* @access	private
		*/
		
		private function display_posts(){
		
			Html::content('o', 'Posts');
			
			foreach($this->_content as $post)
				Html::content_line($post->_permalink, $post->_title, $this->_setting->_data['view']);
			
			Html::content('c');
		
		}
		
		/**
			* Display album listing
			*
			* @access	private
		*/
		
		private function display_albums(){
		
			Html::content('o', 'Albums');
			
			foreach($this->_content as $album)
				Html::content_line($album->_id, $album->_name, $this->_setting->_data['view']);
			
			Html::content('c');
		
		}
		
		/**
			* Display video listing
			*
			* @access	private
		*/
		
		private function display_video(){
		
			Html::content('o', 'Videos');
			
			foreach($this->_content as $album)
				Html::content_line($album->_id, $album->_name, $this->_setting->_data['view']);
			
			Html::content('c');
		
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
			
				Html::form('o', 'post', '#');
				
				$this->display_settings();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update default page setting
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update(false)){
			
				try{
				
					$array = array('type' => '', 'view' => '');
					
					if($this->_setting->_data['type'] != VPost::type())
						$array['view'] = 'all';
					else
						$array['view'] = VPost::view();
					
					$array['type'] = VPost::type();
					$this->_setting->_data = json_encode($array);
					
					$this->_setting->update('_data', 'str');
					
					$this->_setting->_data = json_decode($this->_setting->_data, true);
					
					Session::monitor_activity('changed default page setting');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}
		
		}
	
	}
	
?>