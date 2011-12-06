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
	use \Library\Models\Setting as Setting;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	
	/**
		* Posts Setting Page
		*
		* Handles posts settings
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Posts
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class SettingPage extends Master{
	
		private $_setting = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Post Settings';
			
			if($this->_user['settings']){
			
				$this->get_setting();
				
				$this->update();
			
			}
		
		}
		
		/**
			* Retrieve post settings
			*
			* @access	private
		*/
		
		private function get_setting(){
		
			try{
			
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('SETTING_ID');
				$to_read['condition_columns'][':t'] = 'setting_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'post';
				$to_read['value_types'][':t'] = 'str';
				
				$this->_setting = $this->_db->read($to_read);
				
				if(empty($this->_setting)){
				
					$this->_setting = new Setting();
					$this->_setting->_name = 'Post';
					$this->_setting->_type = 'post';
					$this->_setting->_data = json_encode(array('media' => false));
					
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
			* Display related admin parts link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::sp_menu();
		
		}
		
		/**
			* Display available buttons
			*
			* @access	private
		*/
		
		private function display_settings(){
		
			Html::settings($this->_setting->_data['media']);
		
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
				
				Html::form('o', 'post', 'index.php?ns=posts&ctl=settingpage');
				
				$this->display_settings();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update setting
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update_setting(false)){
			
				try{
				
					$array = VPost::settings(array('media' => false));
					
					$settings = array('media' => false);
					
					foreach($settings as $key => &$value)
						if(in_array($key, $array))
							$value = true;
					
					$this->_setting->_data = json_encode($settings);
					$this->_setting->update('_data', 'str');
					$this->_setting->_data = json_decode($this->_setting->_data, true);
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}
		
		}
	
	}

?>