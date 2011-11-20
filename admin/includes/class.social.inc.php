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
	
	namespace Admin\Social;
	use \Admin\Master as Master;
	use Exception;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Library\Models\Setting as Setting;
	use \Library\Variable\Post as VPost;
	
	/**
		* Manage Social
		*
		* Display all available share buttons
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Social
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		private $_setting = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Social';
			
			if($this->_user['settings']){
			
				$this->get_content();
				$this->update();
			
			}
		
		}
		
		/**
			* Retrieve saved settings
			*
			* @access	private
		*/
		
		private function get_content(){
		
			try{
			
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('SETTING_ID');
				$to_read['condition_columns'][':t'] = 'setting_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'share_buttons';
				$to_read['value_types'][':t'] = 'str';
				
				$this->_setting = $this->_db->read($to_read);
				
				if(empty($this->_setting)){
				
					$this->_setting = new Setting();
					$this->_setting->_name = 'Share Buttons';
					$this->_setting->_type = 'share_buttons';
					$this->_setting->_data = json_encode(array());
					
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
		
			Html::menu();
		
		}
		
		/**
			* Display available buttons
			*
			* @access	private
		*/
		
		private function display_settings(){
		
			Html::settings($this->_setting->_data);
		
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
			
				Html::form('o', 'post', 'index.php?ns=social&ctl=manage');
				
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
				
					$this->_setting->_data = json_encode(VPost::networks(array()));
					$this->_setting->update('_data', 'str');
					$this->_setting->_data = json_decode($this->_setting->_data, true);
					
					$result = true;
				
				}catch(Exception $e){
				
					$resut = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}
		
		}
	
	}

?>