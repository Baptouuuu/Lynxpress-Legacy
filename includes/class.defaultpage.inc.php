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
	use \Library\Database\Database as Database;
	use \Library\Models\Setting as Setting;
	use \Library\Mail\Mail as Mail;
	use Exception;
	
	/**
		* DefaultPage
		*
		* Permit to reroute user in function of default page saved in setting
		*
		* @package		Site
		* @subpackage	Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0.1
		* @final
	*/
	
	final class DefaultPage{
	
		private $_db = null;
		private $_setting = null;
		private $_controller = null;
		private $_title = null;
		private $_menu = null;
		const CONTROLLER = true;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			$this->_db =& Database::load();
			
			$this->get_setting();
			$this->route();
			
			//used if blog is set as default page
			$this->_title = $this->_controller->_title;
			$this->_menu = $this->_controller->_menu;
		
		}
		
		/**
			* Retrieve default page setting
			*
			* @access	private
		*/
		
		private function get_setting(){
		
			try{
			
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('SETTING_ID');
				$to_read['condition_columns'][':t'] = 'setting_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'default_page';
				$to_read['value_types'][':t'] = 'str';
				
				$this->_setting = $this->_db->read($to_read);
				
				$this->_setting = new Setting($this->_setting[0]['SETTING_ID']);
				
				$this->_setting->_data = json_decode($this->_setting->_data, true);
			
			}catch(Exception $e){
			
				$mail = new Mail(WS_EMAIL, 'Couldn\'t retrieve default page setting', $e->getMessage());
				$mail->send();
			
			}
		
		}
		
		/**
			* Reroute user in function of the setting value
			*
			* If the setting is to display the blog, there's no redirection and it loads Posts class
			*
			* @access	private
		*/
		
		private function route(){
		
			switch($this->_setting->_data['type']){
			
				case 'albums':
					$_GET['ctl'] = 'albums';
					
					if($this->_setting->_data['view'] != 'all')
						$_GET['album'] = $this->_setting->_data['view'];
					
					$this->_controller = new Albums();
					break;
				
				case 'video':
					$_GET['ctl'] = 'video';
					
					$this->_controller = new Video();
					break;
				
				case 'posts':
					$_GET['ctl'] = 'posts';
					
					if($this->_setting->_data['view'] != 'all')
						$_GET['news'] = $this->_setting->_data['view'];
					
					$this->_controller = new Posts();
					break;
			
			}
		
		}
		
		/**
			* Display post listing in case blog is set as default page
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->_controller->display_content();
		
		}
		
		/**
			* Method to get the title or the menu elements
			*
			* @access	public
			* @param	string [$attr] Only "_title" and "_menu" are allowed
			* @return	mixed
		*/
		
		public function __get($attr){
		
			if(in_array($attr, array('_title', '_menu')))
				return $this->$attr;
			else
				return 'The lynx is not here!';
		
		}
	
	}

?>