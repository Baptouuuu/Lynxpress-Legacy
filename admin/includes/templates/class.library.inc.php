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
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Get as VGet;
	use \Library\Curl\Curl as Curl;
	use \Library\File\File as File;
	use \Library\Models\Setting as Setting;
	use Exception;
	
	/**
		* Library Templates
		*
		* Retrieve templates from github
		*
		* @package		Templates
		* @subpackage	Controllers
		* @namespace	Templates
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Library extends Master{
	
		private $_templates = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Templates Library';
			
			if($this->_user['settings']){
			
				$this->get_templates();
				$this->create();
			
			}
		
		}
		
		/**
			* Retrieve the template list from lynxpress.org
			*
			* @access	private
		*/
		
		private function get_templates(){
		
			try{
			
				$url = 'http://lynxpress.org/admin/index.php?ns=rpc&action=get_templates';
				
				if(VPost::search_button(false) && VPost::search(false))
					$url .= '&search='.VPost::search();
				else
					$url .= '&limit=10';
				
				$curl = new Curl($url);
				
				$this->_templates = json_decode($curl->_content, true);
				
				if(isset($this->_templates['message']))
					throw new Exception($this->_templates['message']);
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				$this->_templates = array();
			
			}
		
		}
		
		/**
			* Display related admin parts links
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::lib_menu();
		
		}
		
		/**
			* Display retrieved templates
			*
			* @access	private
		*/
		
		private function display_templates(){
		
			Html::form('o', 'post', 'index.php?ns=templates&ctl=library');
			
			Html::lib_actions();
			
			echo '<section id="labels">';
			
			if(!empty($this->_templates))
				foreach($this->_templates as $tpl)
					Html::lib_tpl_label($tpl['user'], $tpl['repo'], $tpl['download'], $tpl['description'], $tpl['website']);
			else
				echo 'No templates found!';
			
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
				
				$this->display_templates();
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Install a template from github
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
					$zip->save('tmp/template.zip');
					
					$tmp = 'tmp/tpl_'.md5_file('tmp/template.zip').'/';
					
					File::unzip('tmp/template.zip', $tmp);
					
					File::delete('tmp/template.zip');
					
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
					
					$this->_action_msg = ActionMessages::custom_good('Template "'.$setting->_name.'" installed');
			
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
	
	}

?>