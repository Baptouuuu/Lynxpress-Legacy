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
	
	namespace Admin\Update;
	use \Admin\Master\Master as Master;
	use \Admin\Helper\Helper as Helper;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Library\Variable\Post as VPost;
	use \Library\Curl\Curl as Curl;
	use \Library\File\File as File;
	use \Library\Database\Backup as Backup;
	use \Library\Mail\Mail as Mail;
	use Exception;
	
	/**
		* Update Lynxpress
		*
		* This controller will check if there's update available for lynxpress
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Update
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Update';
			
			if($this->_user['settings']){
			
				$this->_action_msg = ActionMessages::ws_update_check(Helper::check_update());
				
				$this->update();
			
			}
		
		}
		
		/**
			* Display related admin parts link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_user['settings'])
				Html::menu(true);
			else
				Html::menu();
		
		}
		
		/**
			* Display form to launch update
			*
			* @access	private
		*/
		
		private function display_form(){
		
			Html::update_form();
		
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
				
				Html::form('o', 'post', 'index.php?ns=update&ctl=manage');
				
				$this->display_form();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update lynxpress
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update()){
			
				try{
				
					if(Helper::check_update() === false)
						throw new Exception('No update available!');
						
					//make a backup of the database first, with an email sent to webmaster with the whole dump
					$bk = new Backup();
					$bk->save('backup/dump-'.date('Y-m-d-H:i:s').'.sql');
					
					$html = new File();
					$html->_content = '<!--The Lynx is not here!-->';
					$html->save('backup/index.html');
					
					$mail = new Mail(WS_EMAIL, 'Databse dump made before update at '.date('Y-m-d H:i:s'), $bk->_sql);
					$mail->send();
					//end backup
						
					//retrieve json manifest from the server
					$manifest = new Curl('http://update.lynxpress.org/manifest.json');
					$manifest = json_decode($manifest->_content, true);
					
					//retrieve zip with all files inside
					$curl_zip = new Curl('http://versions.lynxpress.org/Lynxpress-'.$manifest['version'].'.zip');
					
					if($curl_zip->_content == '<!--The Lynx is not here!-->')
						throw new Exception('Can\'t retrieve lynxpress archive');
					
					$zip = new File();
					$zip->_content = $curl_zip->_content;
					$zip->save('tmp/update.zip');
					
					unset($zip);
					unset($curl_zip);
					
					File::unzip('tmp/update.zip', 'tmp/update/');
					
					File::delete('tmp/update.zip');
					
					//check if all files are readable
					foreach($manifest['src'] as $src)
						File::read('tmp/update/Lynxpress-'.$manifest['version'].'/'.$src);
					
					//replace all files registered in the manifest
					foreach($manifest['src'] as $key => $src){
					
						File::read('tmp/update/Lynxpress-'.$manifest['version'].'/'.$src)->save($manifest['dest'][$key]);
						
						File::delete('tmp/update/Lynxpress-'.$manifest['version'].'/'.$src);
					
					}
					
					//execute special queries
					foreach($manifest['queries'] as $query)
						$this->_db->query(str_replace('{{prefix}}', DB_PREFIX, $query));
					
					//remove files
					foreach($manifest['remove'] as $file)
						File::delete($file, false);
					
					$config = File::read(PATH.'config.php');
					$config->_content = str_replace('(\'WS_VERSION\', \''.WS_VERSION.'\')', '(\'WS_VERSION\', \''.$manifest['version'].'\')', $config->_content);
					$config->save();
					
					unset($config);
					
					$config = File::read(PATH.'config.sample.php');
					$config->_content = str_replace('(\'WS_VERSION\', \''.WS_VERSION.'\')', '(\'WS_VERSION\', \''.$manifest['version'].'\')', $config->_content);
					$config->save();
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::ws_update($result);
			
			}
		
		}
	
	}

?>