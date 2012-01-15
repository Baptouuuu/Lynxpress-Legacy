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
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Session as VSession;
	use \Library\Models\User as User;
	use \Library\Models\Media as Media;
	use Exception;
	
	/**
		* Author
		*
		* Handles displaying author
		*
		* @package		Site
		* @subpackage	Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Author extends Master{
	
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			parent::build_title();
			$this->_menu = array('Description of website author(s)');
			
			$this->get_content();
		
		}
		
		/**
			* Retrieve users
			*
			* @access	private
		*/
		
		private function get_content(){
		
			try{
			
				$to_read['table'] = $this->_sql_table;
				$to_read['columns'] = array('USER_ID');
				
				if(VGet::author()){
				
					$to_read['condition_columns'][':p'] = 'user_publicname';
					$to_read['condition_select_types'][':p'] = '=';
					$to_read['condition_values'][':p'] = VGet::author();
					$to_read['value_types'][':p'] = 'str';
				
				}
				
				$this->_content = $this->_db->read($to_read);
				
				if(!empty($this->_content)){
				
					foreach ($this->_content as &$user){
					
						$user = new User($user['USER_ID']);
						
						$a = $user->_avatar;
						
						if(!empty($a)){
						
							$m = new Media();
							$m->_id = $user->_avatar;
							$m->read('_permalink');
							
							$dirname = dirname($m->_permalink).'/';
							$filename = basename($m->_permalink);
							
							$user->_avatar = $dirname.'150-'.$filename;
						
						}
					
					}
				
				}
			
			}catch(Exception $e){
			
				@error_log($e->getMessage().' file: '.__FILE__.'; line: '.__LINE__, 1, WS_EMAIL);
				header('Location: 404.php');
			
			}
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			Html::header_authors(VGet::author());
			
			if(!empty($this->_content)){
			
				if(!VSession::html5())
					echo '<ul>';
				
				foreach ($this->_content as $user)
					Html::author($user->_publicname, $user->_email, $user->_website, $user->_msn, $user->_twitter, $user->_facebook, $user->_google, $user->_avatar, $user->_bio);
				
				if(!VSession::html5())
					echo '</ul>';
			
			}else{
			
				Html::no_content('Wanted user doesn\'t exist');
			
			}
		
		}
	
	}

?>