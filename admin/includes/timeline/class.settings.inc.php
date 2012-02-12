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
	use \Library\Variable\Session as VSession;
	use \Library\Models\Setting as Setting;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Get as VGet;
	use \Library\Curl\Curl as Curl;
	use Exception;
	
	/**
		* Setting Timeline
		*
		* Manage settings for the timeline, such as which website to follow
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Timeline
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Settings extends Master{
	
		private $_prefs = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Timeline Settings';
			
			$this->get_prefs();
			
			$this->create();
			$this->delete();
		
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
				
				$this->_prefs = new Setting($pref[0]['SETTING_ID']);
				$this->_prefs->_data = json_decode($this->_prefs->_data, true);
			
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
		
			Html::sg_menu();
		
		}
		
		/**
			* Display followed websites
			*
			* @access	private
		*/
		
		private function display_websites(){
		
			echo '<section id="labels">';
			
			if(!empty($this->_prefs->_data['timeline']))
				foreach($this->_prefs->_data['timeline'] as $key => $website)
					Html::sg_label($key, $website['title'], $website['url']);
			else
				echo 'Your currently following no website!';
			
			echo '</section>';
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			echo $this->_action_msg;
			
			Html::form('o', 'post', 'index.php?ns=timeline&ctl=settings');
			
			Html::sg_add_form();
			
			$this->display_websites();
			
			Html::form('c');
		
		}
		
		/**
			* Add a website to the timeline
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::add(false)){
			
				try{
				
					foreach($this->_prefs->_data['timeline'] as $website)
						if($website['url'] == VPost::url())
							throw new Exception('Website already in your timeline with the name "'.$website['title'].'"');
					
					$curl = new Curl(VPost::url().'admin/index.php?ns=rpc&ctl=timeline&action=check');
					
					if($curl->_content != '{"lynxpress":"true"}')
						throw new Exception('Wished website is not running Lynxpress! Or not a compatible version!');
					
					$data = $this->_prefs->_data;
					$data['timeline'][] = array('title' => VPost::title(), 'url' => VPost::url());
					$this->_prefs->_data = $data;
					
					$this->_prefs->_data = json_encode($this->_prefs->_data);
					$this->_prefs->update('_data', 'str');
					$this->_prefs->_data = json_decode($this->_prefs->_data, true);
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::pref_updated($result);
			
			}
		
		}
		
		/**
			* Remove one website from the timeline
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VGet::action() == 'remove' && VGet::id(false) !== false){
			
				try{
				
					$data = $this->_prefs->_data;
					unset($data['timeline'][VGet::id()]);
					$this->_prefs->_data = json_encode($data);
					
					$this->_prefs->update('_data', 'str');
					
					$this->_prefs->_data = json_decode($this->_prefs->_data, true);
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::pref_updated($result);
			
			}
		
		}
	
	}

?>