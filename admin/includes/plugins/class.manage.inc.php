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
	
	namespace Admin\Plugins;
	use \Admin\Master\Master as Master;
	use \Admin\Settings\Html as HtmlSettings;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Library\Models\Setting as Setting;
	use \Library\Variable\Post as VPost;
	use \Library\File\File as File;
	use Exception;
	
	/**
		* Manage plugins
		*
		* Handles plugins administration
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Plugins
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		private $_plugins = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Plugins';
			
			if($this->_user['settings']){
			
				$this->delete();
				
				$this->get_plugins();
			
			}
		
		}
		
		/**
			* Retrieve all plugins informations
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
					foreach($this->_plugins as &$plugin){
					
						$plugin = new Setting($plugin['SETTING_ID']);
						$plugin->_data = json_decode($plugin->_data, true);
					
					}
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Display related admin part links
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_user['settings'])
				Html::mp_menu();
			else
				HtmlSettings::menu();
		
		}
		
		/**
			* Display actions bar
			*
			* @access	private
		*/
		
		private function display_actions(){
		
			Html::mp_actions();
		
		}
		
		/**
			* Display all installed plugins
			*
			* @access	private
		*/
		
		private function display_plugins(){
		
			echo '<section id="labels">';
			
			if(!empty($this->_plugins))
				foreach($this->_plugins as $plugin)
					Html::plg_label($plugin->_id, $plugin->_data['name'], $plugin->_data['author'], $plugin->_data['url']);
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
			
			if($this->_user['settings']){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', 'index.php?ns=plugins&ctl=manage');
				
				$this->display_actions();
				$this->display_plugins();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Delete a plugin
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::delete(false) && VPost::plg_id() && $this->_user['delete_content']){
			
				try{
				
					$plg = new Setting(VPost::plg_id());
					$plg->_data = json_decode($plg->_data, true);
					
					foreach($plg->_data['admin'] as $file)
						File::delete('includes/'.$plg->_data['namespace'].'/'.$file);
					
					foreach($plg->_data['site'] as $file)
						File::delete(PATH.'includes/'.$file);
					
					foreach($plg->_data['library'] as $file)
						File::delete('library/'.$plg->_data['namespace'].'/'.$file);
					
					foreach($plg->_data['uninstall'] as $query)
						$this->_db->query(str_replace('{{prefix}}', DB_PREFIX, $query));
					
					File::delete(PATH.'css/'.$plg->_data['namespace'].'.css', false);
					
					$plg->delete();
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::deleted($result);
			
			}elseif(VPost::delete(false) && !$this->_user['delete_content']){
			
				$this->_action_msg = ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>