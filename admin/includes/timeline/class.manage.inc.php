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
	use Exception;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Library\Variable\Session as VSession;
	use \Library\Variable\Get as VGet;
	use \Library\Models\Setting as Setting;
	use \Library\Curl\Curl as Curl;
	
	/**
		* Manage Timeline
		*
		* Display timeline from wished websites
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Timeline
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		private $_prefs = null;
		private $_timeline = null;
		private $_since = null;
		private $_key = null;		//website key in preferences array
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Timeline';
			
			$this->get_prefs();
			
			$this->_since = VGet::since(substr($this->_prefs->_data['last_visit'], 0, 10));
			
			$this->get_timeline();
		
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
				$to_read['condition_values'][':t'] = 'user_'.VSession::user_id();
				$to_read['value_types'][':t'] = 'str';
				
				$pref = $this->_db->read($to_read);
				
				if(empty($pref)){
				
					$this->_prefs = new Setting();
					$this->_prefs->_name = 'User preferences for "'.VSession::username().'"';
					$this->_prefs->_type = 'user_'.VSession::user_id();
					$this->_prefs->_data = json_encode(array('last_visit' => date('Y-m-d H:i:s'), 'timeline' => array(array('title' => 'Lynxpress Blog', 'url' => 'http://blog.lynxpress.org/'))));
					$this->_prefs->create();
				
				}else{
				
					$this->_prefs = new Setting($pref[0]['SETTING_ID']);
				
				}
				
				$this->_prefs->_data = json_decode($this->_prefs->_data, true);
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve the timeline of a website
			*
			* @access	private
		*/
		
		private function get_timeline(){
		
			try{
			
				$site = VGet::website();
				
				if(!empty($site) || $site === 0){
				
					$this->_key = VGet::website();
				
				}else{
				
					$data = $this->_prefs->_data['timeline'];
					reset($data);
					$this->_key = key($data);
				
				}	
				
				if(empty($this->_prefs->_data['timeline']))
					throw new Exception('No website in your preferences!');
			
				if(!isset($this->_prefs->_data['timeline'][$this->_key]))
					throw new Exception('Requested website not found!');
				
				$url = $this->_prefs->_data['timeline'][$this->_key]['url'].'admin/index.php?ns=rpc&ctl=timeline&since='.$this->_since;
				
				$curl = new Curl($url);
				
				$this->_timeline = json_decode($curl->_content, true);
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Display part link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::tm_menu();
		
		}
		
		/**
			* Display the list of followed websites
			*
			* @access	private
		*/
		
		private function display_websites(){
		
			Html::tm_websites('o');
			
			if(!empty($this->_prefs->_data['timeline']))
				foreach($this->_prefs->_data['timeline'] as $key => $site)
					Html::tm_website($key, $site['title'], $site['url']);
			
			Html::tm_websites('c');
		
		}
		
		/**
			* Display a website timeline
			*
			* @access	private
		*/
		
		private function display_timeline(){
		
			Html::tm_timeline('o', $this->_prefs->_data['timeline'][$this->_key]['title']);
			
			if(!empty($this->_timeline['content']))
				foreach($this->_timeline['content'] as $post)
					Html::tm_post($this->_key, $post['post_title'], $post['post_permalink'], $post['post_date']);
			else
				Html::tm_no_post($this->_since);
			
			Html::tm_timeline('c');
		
		}
		
		/**
			* Method that display the page
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			echo $this->_action_msg;
			
			if(!empty($this->_prefs->_data['timeline'])){
			
				Html::tm_periods($this->_key, substr($this->_prefs->_data['last_visit'], 0, 10));
				
				$this->display_websites();
				
				$this->display_timeline();
			
			}
		
		}
	
	}

?>