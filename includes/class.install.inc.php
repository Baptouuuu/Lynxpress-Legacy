<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011-2012
		* @license		http://www.gnu.org/licenses/gpl.html GNU GPL V3
		* @package		Lynxpress
		* @subpackage	Installation
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
	
	namespace Install;
	use \Library\Variable\Post as VPost;
	use \Library\Models\Category as Category;
	use \Library\Models\Post as Post;
	use \Library\Models\Setting as Setting;
	use \Library\Models\User as User;
	use \Admin\Helper\Helper as Helper;
	use PDO;
	use Exception;
	
	/**
		* Install
		*
		* Handles website installation
		*
		* @package		Installation
		* @subpackage	Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Install{
	
		private $_db = null;
		private $_db_host = null;
		private $_db_name = null;
		private $_db_user = null;
		private $_db_pwd = null;
		private $_db_prefix = null;
		private $_ws_url = null;
		private $_ws_name = null;
		private $_ws_email = null;
		private $_username = null;
		private $_password = null;
		private $_conf = null;
		private $_result = null;
		const VERSION = '1.2';
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			if(file_exists('config.php') && self::check_installed())
				header('Location: index.php');
			elseif(file_exists('config.php') && !self::check_installed())
				$this->_result = 'run install';
			
			if(VPost::install(false))
				$this->install();
			elseif(VPost::install_woc(false))
				$this->install_woc();
		
		}
		
		/**
			* Check if database is installed when config.php exists
			*
			* @static
			* @access	public
			* @param	string [$path] Path to config.php
			* @return	boolean
		*/
		
		public static function check_installed($path = ''){
		
			try{
			
				require_once $path.'config.php';
				
				$db = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST.';', DB_USER, DB_PWD, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
				
				$read = $db->prepare('SELECT COUNT(*) FROM `'.DB_PREFIX.'user`');
				$read->execute();
				
				if($read->errorCode() != '00000')
					throw new Exception('cannot read');
				
				return true;
			
			}catch(Exception $e){
			
				return false;
			
			}
		
		}
		
		/**
			* Install website
			*
			* @access	private
		*/
		
		private function install(){
		
			$this->_db_host = VPost::db_host();
			$this->_db_name = VPost::db_name();
			$this->_db_user = VPost::db_user();
			$this->_db_pwd = VPost::db_pwd();
			$this->_db_prefix = VPost::db_prefix();
			$this->_ws_url = VPost::ws_url();
			$this->_ws_name = VPost::ws_name();
			$this->_ws_email = VPost::ws_email();
			$this->_username = VPost::username();
			$this->_password = VPost::password();
			
			$conf = "<?php\n";
			$conf .= "\n";
			$conf .= "\t/**\n";
			$conf .= "\t\t* @author\t\tBaptiste Langlade\n";
			$conf .= "\t\t* @copyright\t2011-2012\n";
			$conf .= "\t\t* @license\t\thttp://www.gnu.org/licenses/gpl.html GNU GPL V3\n";
			$conf .= "\t\t* @package\t\tLynxpress\n";
			$conf .= "\t\t*\n";
			$conf .= "\t\t* This file is part of Lynxpress.\n";
			$conf .= "\t\t*\n";
			$conf .= "\t\t*   Lynxpress is free software: you can redistribute it and/or modify\n";
			$conf .= "\t\t*   it under the terms of the GNU General Public License as published by\n";
			$conf .= "\t\t*   the Free Software Foundation, either version 3 of the License, or\n";
			$conf .= "\t\t*   (at your option) any later version.\n";
			$conf .= "\t\t*\n";
			$conf .= "\t\t*   Lynxpress is distributed in the hope that it will be useful,\n";
			$conf .= "\t\t*   but WITHOUT ANY WARRANTY; without even the implied warranty of\n";
			$conf .= "\t\t*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n";
			$conf .= "\t\t*   GNU General Public License for more details.\n";
			$conf .= "\t\t*\n";
			$conf .= "\t\t*   You should have received a copy of the GNU General Public License\n";
			$conf .= "\t\t*   along with Lynxpress.  If not, see http://www.gnu.org/licenses/.\n";
			$conf .= "\t*/\n";
			$conf .= "\n";
			$conf .= "\t/**\n";
			$conf .= "\t\t* Configuration of the website\n";
			$conf .= "\t*/\n";
			$conf .= "\n";
			$conf .= "\tdefine('WS_NAME', '".$this->_ws_name."');\n";
			$conf .= "\tdefine('WS_URL', '".$this->_ws_url."');\n";
			$conf .= "\tdefine('WS_EMAIL', '".$this->_ws_email."');\n";
			$conf .= "\tdefine('DB_HOST', '".$this->_db_host."');\n";
			$conf .= "\tdefine('DB_NAME', '".$this->_db_name."');\n";
			$conf .= "\tdefine('DB_USER', '".$this->_db_user."');\n";
			$conf .= "\tdefine('DB_PWD', '".$this->_db_pwd."');\n";
			$conf .= "\tdefine('DB_PREFIX', '".$this->_db_prefix."');\n";
			$conf .= "\n";
			$conf .= "\tdefine('SALT', '".Helper::generate_salt()."');		//after installation, don't change this constant\n";
			$conf .= "\n";
			$conf .= "\tdefine('WS_VERSION', '".self::VERSION."');\n";
			$conf .= "\n";
			$conf .= "?>";
			
			$this->_conf = $conf;
			
			try{
			
				//config.php creation
				$config = @fopen('config.php', 'w');
				
				if(!$config)
					throw new Exception('false fopen');
				
				fwrite($config, $conf);
				fclose($config);
				//end config creation
				
				//try to connect to database, if not exception raisen and we create it
				$this->_db = new PDO('mysql:dbname='.$this->_db_name.';host='.$this->_db_host.';', $this->_db_user, $this->_db_pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
				
				
				require 'config.php';
				
				//create tables
				$this->create_activity();
				$this->create_category();
				$this->create_comment();
				$this->create_link();
				$this->create_media();
				$this->create_post();
				$this->create_setting();
				$this->create_user();
				
				$this->_result = 'successful';
			
			}catch(Exception $e){
			
				if($e->getMessage() == 'false fopen'){
				
					$this->_result = 'false fopen';
				
				}elseif($e->getMessage() == 'SQLSTATE[28000] [1045] Access denied for user \''.$this->_db_user.'\'@\''.$this->_db_host.'\' (using password: YES)'){
				
					$this->_result = 'false connect';
					unlink('config.php');
				
				}elseif($e->getMessage() == 'SQLSTATE[42000] [1049] Unknown database \''.$this->_db_name.'\''){
				
					try{
					
						$this->_db = new PDO('mysql:host='.$this->_db_host.';', $this->_db_user, $this->_db_pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
						
						$this->create_database();
						
						$this->_db = new PDO('mysql:dbname='.$this->_db_name.';host='.$this->_db_host.';', $this->_db_user, $this->_db_pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
						
						require 'config.php';
						
						$this->create_activity();
						$this->create_category();
						$this->create_comment();
						$this->create_link();
						$this->create_media();
						$this->create_post();
						$this->create_setting();
						$this->create_user();
						
						$this->_result = 'successful';
					
					}catch(Exception $e){
					
						if($e->getMessage() == 'false create')
							$this->_result = 'false create';
						else
							$this->_result = 'unknown';
						
						unlink('config.php');
					
					}
				
				}elseif($e->getMessage() == 'false create'){
				
					$this->_result = 'false create';
					unlink('config.php');
				
				}else{
				
					$this->_result = 'unknown';
					unlink('config.php');
				
				}
			
			}
		
		}
		
		/**
			* Install database with config.php already created
			*
			* @access	private
		*/
		
		private function install_woc(){
		
			require_once 'config.php';
		
			$this->_db_host = DB_HOST;
			$this->_db_name = DB_NAME;
			$this->_db_user = DB_USER;
			$this->_db_pwd = DB_PWD;
			$this->_db_prefix = DB_PREFIX;
			$this->_ws_url = WS_URL;
			$this->_ws_name = WS_NAME;
			$this->_ws_email = WS_EMAIL;
			$this->_username = VPost::username();
			$this->_password = VPost::password();
			
			try{
			
				//try to connect to database, if not exception raisen and we create it
				$this->_db = new PDO('mysql:dbname='.$this->_db_name.';host='.$this->_db_host.';', $this->_db_user, $this->_db_pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
				
				//create tables
				$this->create_activity();
				$this->create_category();
				$this->create_comment();
				$this->create_link();
				$this->create_media();
				$this->create_post();
				$this->create_setting();
				$this->create_user();
				
				$this->_result = 'successful';
			
			}catch(Exception $e){
			
				if($e->getMessage() == 'SQLSTATE[42000] [1049] Unknown database \''.$this->_db_name.'\''){
				
					try{
					
						$this->_db = new PDO('mysql:host='.$this->_db_host.';', $this->_db_user, $this->_db_pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
						
						$this->create_database();
						
						$this->_db = new PDO('mysql:dbname='.$this->_db_name.';host='.$this->_db_host.';', $this->_db_user, $this->_db_pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
						
						$this->create_activity();
						$this->create_category();
						$this->create_comment();
						$this->create_link();
						$this->create_media();
						$this->create_post();
						$this->create_setting();
						$this->create_user();
						
						$this->_result = 'successful';
					
					}catch(Exception $e){
					
						if($e->getMessage() == 'false create')
							$this->_result = 'false create';
						else
							$this->_result = 'unknown';
					
					}
				
				}elseif($e->getMessage() == 'false create'){
				
					$this->_result = 'false create';
				
				}else{
				
					$this->_result = 'unknown';
				
				}
			
			}
		
		}
		
		/**
			* Create database
			*
			* @access	private
		*/
		
		private function create_database(){
		
			$sql = 'CREATE DATABASE `'.$this->_db_name.'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
			
			$create = $this->_db->prepare($sql);
			$create->execute();
			
			if($create->errorCode() != '00000')
				throw new Exception('false create');
		
		}
		
		/**
			* Create activity table
			*
			* @access	private
		*/
		
		private function create_activity(){
		
			$sql = 'CREATE TABLE `'.$this->_db_prefix.'activity` (
			  `USER_ID` int(11) unsigned NOT NULL,
			  `data` tinytext NOT NULL,
			  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
			
			$create = $this->_db->prepare($sql);
			$create->execute();
			
			if($create->errorCode() != '00000')
				throw new Exception('false create');
		
		}
		
		/**
			* Create category table
			*
			* @access	private
		*/
		
		private function create_category(){
		
			$sql = 'CREATE TABLE `'.$this->_db_prefix.'category` (
			  `CATEGORY_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `category_name` tinytext NOT NULL,
			  `category_type` tinytext NOT NULL,
			  PRIMARY KEY (`CATEGORY_ID`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
			
			$create = $this->_db->prepare($sql);
			$create->execute();
			
			if($create->errorCode() != '00000'){
			
				throw new Exception('false create');
			
			}else{
			
				$cat = new Category();
				$cat->_name = 'uncategorized';
				$cat->_type = 'post';
				$cat->create();
				
				$cat= new Category();
				$cat->_name = 'uncategorized';
				$cat->_type = 'album';
				$cat->create();
				
				$cat = new Category();
				$cat->_name = 'uncategorized';
				$cat->_type = 'video';
				$cat->create();
			
			}
		
		}
		
		/**
			* Create comment table
			*
			* @access	private
		*/
		
		private function create_comment(){
		
			$sql = 'CREATE TABLE `'.$this->_db_prefix.'comment` (
			  `COMMENT_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `comment_name` tinytext NOT NULL,
			  `comment_email` tinytext NOT NULL,
			  `comment_content` text NOT NULL,
			  `comment_rel_id` int(11) unsigned NOT NULL,
			  `comment_rel_type` varchar(5) NOT NULL DEFAULT \'post\',
			  `comment_status` varchar(8) NOT NULL DEFAULT \'pending\',
			  `comment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`COMMENT_ID`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
			
			$create = $this->_db->prepare($sql);
			$create->execute();
			
			if($create->errorCode() != '00000')
				throw new Exception('false create');
		
		}
		
		/**
			* Create link table
			*
			* @access	private
		*/
		
		private function create_link(){
		
			$sql = 'CREATE TABLE `'.$this->_db_prefix.'link` (
			  `LINK_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `link_name` tinytext NOT NULL,
			  `link_link` tinytext NOT NULL,
			  `link_rss_link` tinytext,
			  `link_notes` text,
			  `link_priority` int(1) NOT NULL,
			  PRIMARY KEY (`LINK_ID`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
			
			$create = $this->_db->prepare($sql);
			$create->execute();
			
			if($create->errorCode() != '00000')
				throw new Exception('false create');
		
		}
		
		/**
			* Create media table
			*
			* @access	private
		*/
		
		private function create_media(){
		
			$sql = 'CREATE TABLE `'.$this->_db_prefix.'media` (
			  `MEDIA_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `media_name` tinytext NOT NULL,
			  `media_type` varchar(10) NOT NULL,
			  `media_author` int(11) NOT NULL,
			  `media_album` int(11) DEFAULT NULL,
			  `media_status` varchar(7) NOT NULL DEFAULT \'draft\',
			  `media_category` tinytext,
			  `media_allow_comment` varchar(6) NOT NULL DEFAULT \'closed\',
			  `media_permalink` tinytext NOT NULL,
			  `media_embed_code` text,
			  `media_description` text,
			  `media_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `media_attachment` int(11) unsigned DEFAULT NULL,
			  PRIMARY KEY (`MEDIA_ID`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
			
			$create = $this->_db->prepare($sql);
			$create->execute();
			
			if($create->errorCode() != '00000')
				throw new Exception('false create');
		
		}
		
		/**
			* Create post table
			*
			* @access	private
		*/
		
		private function create_post(){
		
			$sql = 'CREATE TABLE `'.$this->_db_prefix.'post` (
			  `POST_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `post_title` tinytext NOT NULL,
			  `post_content` text NOT NULL,
			  `post_allow_comment` varchar(6) NOT NULL DEFAULT \'closed\',
			  `post_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `post_author` int(11) NOT NULL,
			  `post_status` varchar(7) NOT NULL DEFAULT \'draft\',
			  `post_category` varchar(29) NOT NULL,
			  `post_tags` tinytext NOT NULL,
			  `post_permalink` tinytext NOT NULL,
			  `post_updated` varchar(3) NOT NULL DEFAULT \'no\',
			  `post_update_author` int(11) DEFAULT NULL,
			  PRIMARY KEY (`POST_ID`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
			
			$create = $this->_db->prepare($sql);
			$create->execute();
			
			if($create->errorCode() != '00000'){
			
				throw new Exception('false create');
			
			}else{
			
				$post = new Post();
				$post->_title = 'Hello World!';
				$post->_content = "Welcome\n";
				$post->_content .= "\n";
				$post->_content .= "This is your first post on Lynxpress. You can edit or delete this one to start blogging.\n";
				$post->_content .= "\n";
				$post->_content .= "I hope you'll like this new CMS.";
				$post->_allow_comment = 'open';
				$post->_author = 1;
				$post->_status = 'publish';
				$post->_category = 1;
				$post->_tags = 'hello, world';
				$post->_permalink = 'hello-world';
				$post->create();
			
			}
		
		}
		
		/**
			* Create setting table
			*
			* @access	private
		*/
		
		private function create_setting(){
		
			$sql = 'CREATE TABLE `'.$this->_db_prefix.'setting` (
			  `SETTING_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `setting_name` text NOT NULL,
			  `setting_type` tinytext NOT NULL,
			  `setting_data` text COMMENT \'data generally stored as a json encoded array\',
			  PRIMARY KEY (`SETTING_ID`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
			
			$create = $this->_db->prepare($sql);
			$create->execute();
			
			if($create->errorCode() != '00000'){
			
				throw new Exception('false create');
			
			}else{
			
				$setting = new Setting();
				$setting->_name = 'Share Buttons';
				$setting->_type = 'share_buttons';
				$setting->_data = '["facebook","twitter","google"]';
				$setting->create();
				
				$setting = new Setting();
				$setting->_name = 'All Roles';
				$setting->_type = 'all_roles';
				$setting->_data = '[]';
				$setting->create();
				
				$setting = new Setting();
				$setting->_name = 'Post';
				$setting->_type = 'post';
				$setting->_data = json_encode(array('media' => false));
				$setting->create();
				
				$setting = new Setting();
				$setting->_name = 'Default Page';
				$setting->_type = 'default_page';
				$setting->_data = json_encode(array('type' => 'posts', 'view' => 'all'));
				$setting->create();
				
				$setting = new Setting();
				$setting->_name = 'Main Template';
				$setting->_type = 'current_template';
				$setting->_data = 'main';
				$setting->create();
				
				$setting = new Setting();
				$setting->_name = 'Main template';
				$setting->_type = 'template';
				$setting->_data = '{"name":"Main Template","namespace":"main","files":["class.html.view.php","css/html5.css","css/mobile.css","css/style.css","html/footer_html5.php","html/footer_mobile.php","html/footer.php","html/header_html5.php","html/header_mobile.php","html/header.php","index.html"],"author":"Baptiste Langlade","url":"http://www.lynxpress.org"}';
				$setting->create();
				
				$setting = new Setting();
				$setting->_name = 'Bobcat Template';
				$setting->_type = 'template';
				$setting->_data = '{"name":"Bobcat Template","namespace":"bobcat","author":"Baptiste Langlade","url":"http://www.lynxpress.org","files":["class.html.view.php","css/html5.css","css/mobile.css","css/style.css","css/index.html","html/footer_html5.php","html/footer_mobile.php","html/footer.php","html/header_html5.php","html/header_mobile.php","html/header.php","html/index.html","index.html"]}';
				$setting->create();
			
			}
		
		}
		
		/**
			* Create user table
			*
			* @access	private
		*/
		
		private function create_user(){
		
			$sql = 'CREATE TABLE `'.$this->_db_prefix.'user` (
			  `USER_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_username` varchar(20) NOT NULL,
			  `user_nickname` varchar(20) NOT NULL,
			  `user_firstname` tinytext,
			  `user_lastname` tinytext,
			  `user_publicname` tinytext NOT NULL,
			  `user_password` tinytext NOT NULL,
			  `user_email` tinytext NOT NULL,
			  `user_website` tinytext,
			  `user_msn` tinytext,
			  `user_twitter` tinytext,
			  `user_facebook` tinytext,
			  `user_google` tinytext,
			  `user_avatar` int(11) DEFAULT NULL COMMENT \'media id\',
			  `user_bio` text,
			  `user_role` varchar(20) NOT NULL DEFAULT \'editor\',
			  PRIMARY KEY (`USER_ID`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
			
			$create = $this->_db->prepare($sql);
			$create->execute();
			
			if($create->errorCode() != '00000'){
			
				throw new Exception('false create');
			
			}else{
			
				$user = new User();
				$user->_username = $this->_username;
				$user->_nickname = $this->_username;
				$user->_publicname = $this->_username;
				$user->_password = $this->_password;
				$user->_email = $this->_ws_email;
				$user->_role = 'administrator';
				$user->create();
			
			}
		
		}
		
		/**
			* Display installer
			*
			* @access	private
		*/
		
		private function display_install(){
		
			echo '<form method="post" action="install.php" accept-charset="utf-8">';
			
			Html::header_installation();
			Html::database();
			Html::website();
			Html::login();
			Html::install();
			
			echo '</form>';
		
		}
		
		/**
			* Display installer without database
			*
			* @access	private
		*/
		
		private function display_install_woc(){
		
			echo '<form method="post" action="install.php" accept-charset="utf-8">';
			
			Html::header_installation();
			Html::login();
			Html::install('install_woc');
			
			echo '</form>';
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			Html::header();
			
			if($this->_result == 'false fopen')
				Html::config_error($this->_conf);
			elseif($this->_result == 'false create')
				Html::create_error();
			elseif($this->_result == 'false connect')
				Html::connect_error();
			elseif($this->_result == 'unknown')
				Html::unknown_error();
			elseif($this->_result == 'successful')
				Html::success();
			elseif($this->_result == 'run install')
				$this->display_install_woc();
			else
				$this->display_install();
		
			Html::footer();
		
		}
	
	}

?>