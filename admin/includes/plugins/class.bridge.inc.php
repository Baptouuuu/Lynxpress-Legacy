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
	
	namespace Admin\Plugins;
	use \Admin\Master\Master as Master;
	use \Library\Models\Setting as Setting;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use Exception;
	
	/**
		* Bridge plugins
		*
		* Makes a bridge between Lynxpress and plugins displaying a link to access them
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Plugins
		* @author		Baptiste Langalde lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Bridge extends Master{
	
		private $_plugins = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Plugins';
			
			$this->get_plugins();
		
		}
		
		/**
			* Retrieve all plugins installed
			*
			* @access	private
		*/
		
		private function get_plugins(){
		
			try{
			
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('SETTING_ID');
				$to_read['condition_columns'][':t'] = 'setting_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'plugin';
				$to_read['value_types'][':t'] = 'str';
				
				$this->_plugins = $this->_db->read($to_read);
				
				if(!empty($this->_plugins))
					foreach($this->_plugins as &$plg){
					
						$plg = new Setting($plg['SETTING_ID']);
						$plg->_data = json_decode($plg->_data, true);
					
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
		
			Html::bp_menu();
		
		}
		
		/**
			* Display plugins links
			*
			* @access	private
		*/
		
		private function display_plugins(){
		
			echo '<section id="labels">';
			
			if(!empty($this->_plugins))
				foreach($this->_plugins as $plg)
					Html::plg_link_label($plg->_name, $plg->_data['namespace'], $plg->_data['entry_point']);
			else
				echo 'No plugins installed right now!';
			
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
			
			$this->display_plugins();
		
		}
	
	}

?>