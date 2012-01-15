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
	use \Library\Variable\Files as VFiles;
	use \Library\File\File as File;
	use Exception;
	
	/**
		* Add Template
		*
		* Upload a new template
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Templates
		* @author		Baptiste langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Add extends Master{
	
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Add template';
			
			if($this->_user['settings'])
				$this->create();
		
		}
		
		/**
			* Display related admin parts link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_user['settings'])
				Html::nt_menu(true);
			else
				Html::nt_menu();
		
		}
		
		/**
			* Display form to add a template
			*
			* @access	private
		*/
		
		private function display_form(){
		
			Html::nt_form();
		
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
				
				Html::form('o', 'post', '#', true);
				
				$this->display_form();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Upload and move into place a new template
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::upload(false)){
			
				try{
				
					$tpl = VFiles::tpl();
					
					if($tpl['error'] != 0)
						throw new Exception('No file uploaded');
				
					$tmp = 'tmp/tpl_'.md5_file($tpl['tmp_name']).'/';
					
					File::unzip($tpl['tmp_name'], $tmp);
					
					$json = File::read($tmp.'manifest.json');
					$conf = json_decode($json->_content, true);
					
					//check if the manifest is complete
					if(!isset($conf['name']) || !isset($conf['author']) || !isset($conf['url']) || !isset($conf['namespace']) || !isset($conf['files']))
						throw new Exception('Invalid manifest!');
					
					if(is_dir(PATH.'includes/templates/'.$conf['namespace'].'/'))
						throw new Exception('Template already exist');
					
					//if one of files doesn't exists, an exception will be raised
					foreach($conf['files'] as $file)
						File::read($tmp.$file);
					
					foreach($conf['files'] as $file){
					
						File::move($tmp.$file, PATH.'includes/templates/'.$conf['namespace'].'/'.$file);
						File::delete($tmp.$file);
					
					}
					
					File::delete($tmp.'manifest.json');
					
					$setting = new Setting();
					$setting->_name = $conf['name'];
					$setting->_type = 'template';
					$setting->_data = json_encode($conf);
					$setting->create();
					
					header('Location: index.php?ns=templates&ctl=manage');
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
	
	}

?>