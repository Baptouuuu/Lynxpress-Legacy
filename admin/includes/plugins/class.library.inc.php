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
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Library\Curl\Curl as Curl;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Get as VGet;
	use \Library\File\File as File;
	use \Library\Models\Setting as Setting;
	use Exception;
	
	/**
		* Library plugins
		*
		* Retrieve plugins from github
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Plugins
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Library extends Master{
	
		private $_plugins = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Plugins Library';
			
			if($this->_user['settings']){
			
				$this->get_plugins();
				$this->create();
			
			}
		
		}
		
		/**
			* Retrieve the plugin list from lynxpress.org
			*
			* @access	private
		*/
		
		private function get_plugins(){
		
			try{
			
				$url = 'http://lynxpress.org/admin/index.php?ns=rpc';
				
				if(VPost::search_button(false) && VPost::search(false))
					$url .= '&search='.VPost::search();
				else
					$url .= '&limit=10';
				
				$curl = new Curl($url);
				
				$this->_plugins = json_decode($curl->_content, true);
				
				if(isset($this->_plugins['message']))
					throw new Exception($this->_plugins['message']);
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				$this->_plugins = array();
			
			}
		
		}
		
		/**
			* Display related admin part links
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::lib_menu();
		
		}
		
		/**
			* Display retrieved plugins
			*
			* @access	private
		*/
		
		private function display_plugins(){
		
			Html::form('o', 'post', 'index.php?ns=plugins&ctl=library');
			
			Html::lib_actions();
			
			echo '<section id="labels">';
			
			if(!empty($this->_plugins))
				foreach($this->_plugins as $plugin)
					Html::lib_plg_label($plugin['user'], $plugin['repo'], $plugin['download'], $plugin['description'], $plugin['website']);
			else
				echo 'No plugins found!';
			
			echo '</section>';
			
			Html::form('c');
		
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
				
				$this->display_plugins();
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Install a plugin from github
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VGet::action() == 'install' && VGet::user() && VGet::repo() && VGet::download()){
			
				try{
				
					$curl = new Curl('https://api.github.com/repos/'.VGet::user().'/'.VGet::repo().'/downloads');
					$downloads = json_decode($curl->_content, true);
					
					if(empty($downloads))
						throw new Exception('Archive doesn\'t exist on Github');
					
					if(isset($downloads['message']))
						throw new Exception($downloads['message']);
					
					$url = null;
					
					foreach($downloads as $download)
						if($download['name'] == VGet::download()){
						
							if($download['content_type'] != 'application/zip')
								throw new Exception('Invalid archive type! (.zip only)');
							else
								$url = $download['html_url'];
						
						}
					
					unset($curl);
					
					$curl = new Curl($url);
					
					$zip = new File();
					$zip->_content = $curl->_content;
					$zip->save('tmp/plugin.zip');
					
					$tmp = 'tmp/plg_'.md5_file('tmp/plugin.zip').'/';
					
					File::unzip('tmp/plugin.zip', $tmp);
					
					File::delete('tmp/plugin.zip');
					
					$json = File::read($tmp.'manifest.json');
					$conf = json_decode($json->_content, true);
					
					//check if manifest is complete
					if(!isset($conf['name']) || !isset($conf['namespace']) || !isset($conf['entry_point']) || !isset($conf['author']) || !isset($conf['url']) || !isset($conf['admin']) || !isset($conf['site']) || !isset($conf['library']) || !isset($conf['queries']) || !isset($conf['uninstall']))
						throw new Exception('Invalid manifest');
					
					if(is_dir('includes/'.$conf['namespace']) || is_dir('library/'.$conf['namespace']))
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
					
					if(isset($conf['css']))
						foreach($conf['css'] as $file){
						
							File::move($tmp.'css/'.$file, PATH.'css/'.$conf['namespace'].'.css');
							File::delete($tmp.'css/'.$file);
						
						}
					
					foreach($conf['queries'] as $query)
						$this->_db->query(str_replace('{{prefix}}', DB_PREFIX, $query));
					
					File::delete($tmp.'manifest.json');
					
					$setting = new Setting();
					$setting->_name = $conf['name'];
					$setting->_type = 'plugin';
					$setting->_data = json_encode($conf);
					$setting->create();
					
					$this->_action_msg = ActionMessages::custom_good('Plugin "'.$setting->_name.'" installed');
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
					
					//remove files
					foreach($conf['admin'] as $file)
						File::delete($tmp.'admin/'.$file, false);
					
					foreach($conf['site'] as $file)
						File::delete($tmp.'site/'.$file, false);
					
					foreach($conf['library'] as $file)
						File::delete($tmp.'library/'.$file, false);
				
				}
			
			}
		
		}
	
	}

?>