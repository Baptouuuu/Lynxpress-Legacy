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
	
	namespace Admin\Templates;
	use \Admin\Master\Master as Master;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Library\Models\Setting as Setting;
	use \Library\Variable\Post as VPost;
	use \Library\File\File as File;
	use Exception;
	
	/**
		* Manage Templates
		*
		* Choose current template and delete them
		*
		* @package		Templates
		* @subpackage	Controllers
		* @namespace	Templates
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		private $_setting = null;
		private $_templates = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			$this->_title = 'Templates';
			
			if($this->_user['settings']){
			
				$this->get_setting();
				
				$this->delete();
				$this->update();
				
				$this->get_templates();
			
			}
		
		}
		
		/**
			* Retrieve all templates
			*
			* @access	private
		*/
		
		private function get_templates(){
		
			try{
			
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('SETTING_ID');
				$to_read['condition_columns'][':t'] = 'setting_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'template';
				$to_read['value_types'][':t'] = 'str';
				
				$this->_templates = $this->_db->read($to_read);
				
				if(!empty($this->_templates))
					foreach($this->_templates as &$template){
					
						$template = new Setting($template['SETTING_ID']);
						$template->_data = json_decode($template->_data, true);
					
					}
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve current template setting
			*
			* @access	private
		*/
		
		private function get_setting(){
		
			try{
			
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('SETTING_ID');
				$to_read['condition_columns'][':t'] = 'setting_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'current_template';
				$to_read['value_types'][':t'] = 'str';
				
				$this->_setting = $this->_db->read($to_read);
				
				if(!empty($this->_setting))
					$this->_setting = new Setting($this->_setting[0]['SETTING_ID']);
				else
					throw new Exception('Current template setting doesn\'t exist');
			
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
		
			if($this->_user['settings'])
				Html::mt_menu(true);
			else
				Html::mt_menu();
		
		}
		
		/**
			* Display actions bar with the name of current template used
			*
			* @access	private
		*/
		
		private function display_actions(){
		
			Html::mt_actions($this->_setting->_name);
		
		}
		
		/**
			* Display retrieved templates
			*
			* @access	private
		*/
		
		private function display_templates(){
		
			echo '<section id="labels">';
			
			foreach($this->_templates as $template)
				Html::template_label($template->_id, $template->_data['name'], $template->_data['author'], $template->_data['url']);
			
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
				
				Html::form('o', 'post', '#');
				
				$this->display_actions();
				$this->display_templates();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update current template setting
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update(false) && VPost::tpl_id()){
			
				try{
				
					$tpl = new Setting(VPost::tpl_id());
					$tpl->_data = json_decode($tpl->_data, true);
					
					$this->_setting->_name = $tpl->_data['name'];
					$this->_setting->_data = $tpl->_data['namespace'];
					$this->_setting->update('_name', 'str');
					$this->_setting->update('_data', 'str');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::template_updated($result);
			
			}
		
		}
		
		/**
			* Delete a template
			*
			* Current and main template will raise an error
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::delete(false) && VPost::tpl_id() && $this->_user['delete_content']){
			
				try{
				
					$tpl = new Setting(VPost::tpl_id());
					$tpl->_data = json_decode($tpl->_data, true);
				
					if($tpl->_data['namespace'] == $this->_setting->_data)
						throw new Exception('Template currently used, action aborted');
					
					if($tpl->_data['namespace'] == 'main' || $tpl->_data['namespace'] == 'bobcat')
						throw new Exception('Default template can\'t be deleted, action aborted');
					
					foreach($tpl->_data['files'] as $file)
						File::delete(PATH.'includes/templates/'.$tpl->_data['namespace'].'/'.$file);
					
					$tpl->delete();
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::template_deleted($result);
			
			}elseif(VPost::delete(false) && !$this->_user['delete_content']){
			
				$this->_action_msg = ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>