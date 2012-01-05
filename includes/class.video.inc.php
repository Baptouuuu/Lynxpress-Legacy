<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011
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
	use \Library\Models\Media as Media;
	use \Library\Models\User as User;
	use Exception;
	
	/**
		* Video
		*
		* Display all video uploaded on this website
		*
		* @package		Site
		* @subpackage	Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Video extends Master{
	
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();

			Helper\Helper::get_categories($this->_menu, 'video');
			$this->build_menu();
			
			//If try to select an inexistant category, redirect to 404
			if(VGet::cat() && !isset($this->_menu[VGet::cat()]))
				header('Location: 404.php');
			
			$this->get_content();
			
			$this->_title = 'Videos';
		
		}
		
		/**
			* Retrieve video metadatas from database
			*
			* @access	private
		*/
		
		private function get_content(){
		
			try{
		
				$to_read['table'] = $this->_sql_table;
				$to_read['columns'] = array('MEDIA_ID');
				$to_read['condition_columns'][':t'] = 'media_type';
				$to_read['condition_select_types'][':t'] = 'LIKE';
				$to_read['condition_values'][':t'] = 'video%';
				$to_read['value_types'][':t'] = 'str';
				$to_read['condition_types'][':s'] = 'AND';
				$to_read['condition_columns'][':s'] = 'media_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				
				if(VGet::cat(false)){
				
					$to_read['condition_types'][':cat'] = 'AND';
					$to_read['condition_columns'][':cat'] = 'media_category';
					$to_read['condition_select_types'][':cat'] = 'LIKE';
					$to_read['condition_values'][':cat'] = '%'.VGet::cat().'%';
					$to_read['value_types'][':cat'] = 'str';
				
				}
				
				$this->_content = $this->_db->read($to_read);
				
				if(!empty($this->_content)){
				
					foreach($this->_content as &$media){
					
						$media = new Media($media['MEDIA_ID']);
						
						//retrieve user public name
						$user = new User();
						$user->_id = $media->_author;
						$user->read('_publicname');
						$media->_author_publicname = $user->_publicname;
						
						$attached = $media->_attachment;
						
						if(!empty($attached)){
						
							$attach = new Media();
							$attach->_id = $media->_attachment;
							$attach->read('_embed_code');
							
							$media->_embed_code = $attach->_embed_code;
						
						}
					
					}
				
				}
			
			}catch(Exception $e){
			
				@error_log($e->getMessage().' file: '.__FILE__.'; line: '.__LINE__, 1, WS_EMAIL);
				header('Location: 404.php');
			
			}
		
		}
		
		/**
			* Modify category to html link
			*
			* @access	private
		*/
		
		private function build_menu(){
		
			foreach ($this->_menu as $key => &$value)
				$value = '<a href="'.PATH.'?ctl='.$this->_pid.'&cat='.$key.'" title="Videos for this category">'.ucwords($value).'</a>';
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			if(VGet::cat())
				$add = $this->_menu[VGet::cat()];
			else
				$add = null;
			
			Html::header_videos($add);
			
			if(!empty($this->_content)){
			
				Html::html5('o', 'id="videos">');
				
				if(!VSession::html5())
					echo '<ul>';
				
				foreach($this->_content as $video)
					Html::video($video->_name, $video->_author_publicname, $video->_permalink, $video->_embed_code, nl2br($video->_description), $video->_date);
				
				if(!VSession::html5())
					echo '</ul>';
				
				Html::html5('c');
			
			}else{
			
				if(!VSession::html5())
					echo '<ul>';
				
				Html::no_content('There\'s no videos right now.');
				
				if(!VSession::html5())
					echo '</ul>';
			
			}
		
		}
	
	}

?>