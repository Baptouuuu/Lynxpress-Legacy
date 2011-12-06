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
	use \Admin\Settings\Html as HtmlSettings;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Library\Models\Setting as Setting;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Files as VFiles;
	use \Library\File\File as File;
	use Exception;
	
	/**
		* Add plugin
		*
		* Handles adding new plugins to lynxpress
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Plugins
		* @author		Baptiste Langlade lynxpressorg@gmail.com
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
			
			$this->_title = 'Add Plugin';
			
			if($this->_user['settings'])
				$this->create();
		
		}
		
		/**
			* Display related admin part links
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_user['settings'])
				Html::ap_menu();
			else
				HtmlSettings::menu();
		
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
				
				Html::form('o', 'post', 'index.php?ns=plugins&ctl=add', true);
				
				Html::ap_form();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Add a new Plugin
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::upload(false)){
			
				try{
				
					$plg = VFiles::plg();
					
					if($plg['error'] != 0)
						throw new Exception('No file uploaded');
					
					$tmp = 'tmp/plg_'.md5_file($plg['tmp_name']).'/';
					
					File::unzip($plg['tmp_name'], $tmp);
					
					$json = File::read($tmp.'manifest.json');
					$conf = json_decode($json->_content, true);
					
					//check if manifest is complete
					if(!isset($conf['name']) || !isset($conf['namespace']) || !isset($conf['entry_point']) || !isset($conf['author']) || !isset($conf['url']) || !isset($conf['admin']) || !isset($conf['site']) || !isset($conf['library']) || !isset($conf['queries']) || !isset($conf['uninstall']))
						throw new Exception('Manifest invalid');
					
					if(is_dir('includes/'.$conf['namespace']))
						throw new Exception('The namespace "'.$conf['namespace'].'" is already taken');
					
					if(is_dir('library/'.$conf['namespace']))
						throw new Exception('The namespace "'.$conf['namespace'].'" is already taken');
					
					//if one of files doesn't exists, an exception will be raised
					foreach($conf['admin'] as $file)
						File::read($tmp.'admin/'.$file);
					
					//if one of files doesn't exists, an exception will be raised
					foreach($conf['site'] as $file){
					
						if(file_exists(PATH.'includes/'.$file))
							throw new Exception('The file "'.$file.'" already exists in site directory');
						
						File::read($tmp.'site/'.$file);
					
					}
					
					//if one of files doesn't exists, an exception will be raised
					foreach($conf['library'] as $file)
						File::read($tmp.'library/'.$file);
					
					foreach($conf['admin'] as $file){
					
						File::move($tmp.'admin/'.$file, 'includes/'.$conf['namespace'].'/'.$file);
						File::delete($tmp.'admin/'.$file);
					
					}
					
					foreach($conf['site'] as $file){
					
						File::move($tmp.'site/'.$file, PATH.'includes/'.$file);
						File::delete($tmp.'site/'.$file);
					
					}
					
					foreach($conf['library'] as $file){
					
						File::move($tmp.'library/'.$file, 'library/'.$conf['namespace'].'/'.$file);
						File::delete($tmp.'library/'.$file);
					
					}
					
					foreach($conf['queries'] as $query)
						$this->_db->query(str_replace('{{prefix}}', DB_PREFIX, $query));
					
					File::delete($tmp.'manifest.json');
					
					$setting = new Setting();
					$setting->_name = $conf['name'];
					$setting->_type = 'plugin';
					$setting->_data = json_encode($conf);
					$setting->create();
					
					header('Location: index.php?ns=plugins&ctl=manage');
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
	
	}

?>