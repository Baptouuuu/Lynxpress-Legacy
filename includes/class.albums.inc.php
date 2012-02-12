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
	use \Library\Models\Category as Category;
	use Exception;
	
	/**
		* Albums
		*
		* Handles displaying picture albums
		*
		* @package		Site
		* @subpackage	Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0.1
		* @final
	*/
	
	final class Albums extends Master{
	
		private $_album = null;
		private $_category = null;
		const CONTROLLER = true;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			Helper\Helper::get_categories($this->_menu, 'album');
			
			$this->load_cat();
			
			$this->get_content();
			$this->build_title();
			$this->build_menu();
		
		}
		
		/**
			* Retrieve Albums from the database
			*
			* @access	private
		*/
		
		private function get_content(){
		
			try{
		
				$to_read['table'] = $this->_sql_table;
				$to_read['columns'] = array('MEDIA_ID');
				
				if(VGet::album()){
				
					$to_read['condition_columns'][':album'] = 'media_album';
					$to_read['condition_select_types'][':album'] = '=';
					$to_read['condition_values'][':album'] = VGet::album();
					$to_read['value_types'][':album'] = 'str';
					$to_read['order'] = array('media_name', 'ASC');
					
					$this->_album = new Media(VGet::album());
					
					$user = new User();
					$user->_id = $this->_album->_author;
					$user->read('_publicname');
					
					$this->_album->_author_name = $user->_publicname;
				
				}elseif(VGet::cat()){
				
					$to_read['condition_columns'][':cat'] = 'media_category';
					$to_read['condition_select_types'][':cat'] = 'LIKE';
					$to_read['condition_values'][':cat'] = '%'.VGet::cat().'%';
					$to_read['value_types'][':cat'] = 'str';
					$to_read['condition_types'][':type'] = 'AND';
					$to_read['condition_columns'][':type'] = 'media_type';
					$to_read['condition_select_types'][':type'] = '=';
					$to_read['condition_values'][':type'] = 'album';
					$to_read['value_types'][':type'] = 'str';
					$to_read['order'] = array('media_date', 'DESC');
				
				}else{
				
					$to_read['condition_columns'][':type'] = 'media_type';
					$to_read['condition_select_types'][':type'] = '=';
					$to_read['condition_values'][':type'] = 'album';
					$to_read['value_types'][':type'] = 'str';
					$to_read['order'] = array('media_date', 'DESC');
				
				}
				
				$to_read['condition_types'][':status'] = 'AND';
				$to_read['condition_columns'][':status'] = 'media_status';
				$to_read['condition_select_types'][':status'] = '=';
				$to_read['condition_values'][':status'] = 'publish';
				$to_read['value_types'][':status'] = 'str';
				
				$this->_content = $this->_db->read($to_read);
				
				if(!empty($this->_content)){
				
					foreach($this->_content as &$element)
						$element = new Media($element['MEDIA_ID']);
				
				}elseif(empty($this->_content) && VGet::album()){
				
					header('Location: 404.php');
				
				}
			
			}catch(Exception $e){
			
				@error_log($e->getMessage().' file: '.__FILE__.'; line: '.__LINE__, 1, WS_EMAIL);
				header('Location: 404.php');
			
			}
		
		}
		
		/**
			* Load category in attribute if set
			*
			* @access	private
		*/
		
		private function load_cat(){
		
			try{
			
				if(VGet::cat()){
				
					$cat = new Category(VGet::cat());
					
					$this->_category = $cat->_name;
				
				}
			
			}catch(Exception $e){
			
				header('Location: 404.php');
			
			}
		
		}
		
		/**
			* Redefine master method for the title
			*
			* @access	protected
		*/
		
		protected function build_title(){
		
			parent::build_title();
			
			if(VGet::album() && !empty($this->_album))
				$this->_title = ucwords($this->_album->_name);
		
		}
		
		/**
			* Modify category to html link
			*
			* @access	private
		*/
		
		private function build_menu(){
		
			foreach($this->_menu as $key => &$value)
				$value = '<a href="'.PATH.'?ctl='.$this->_pid.'&cat='.$key.'" title="Albums for this category">'.ucwords($value).'</a>';
		
		}
		
		/**
			* Display albums listing
			*
			* @access	private
		*/
		
		private function display_albums(){
		
			if(!empty($this->_content)){
			
				Html::header_albums($this->_category);
			
				if(!VSession::html5())
					echo '<ul id="listing_albums">';
				else
					echo '<section id="listing_albums">';
				
				foreach($this->_content as $album)
					Html::album_label($album->_id, $album->_name, $album->_permalink, $album->_description);
				
				if(!VSession::html5())
					echo '</ul>';
				else
					echo '</section>';
			
			}else{
			
				Html::header_albums($this->_category);
			
				if(!VSession::html5())
					echo '<ul>';
				
				Html::no_content('There\'s no albums right now.');
				
				if(!VSession::html5())
					echo '</ul>';
			
			}
		
		}
		
		/**
			* Display a single album with all pictures
			*
			* @access	private
		*/
		
		private function display_album(){
		
			Html::header_albums($this->_album->_name);
			Html::album_details($this->_album->_id, $this->_album->_author_name, $this->_album->_date, nl2br($this->_album->_description), $this->_album->_name, WS_URL.'?ctl='.$this->_pid.'&album='.$this->_album->_id);
			
			Html::html5('o', 'id="album">');
			
			echo '<ul>';
			
			foreach($this->_content as $picture){
			
				$permalink = $picture->_permalink;
				
				$folder = dirname($permalink).'/';
				$file = basename($permalink);
				
				Html::album_picture($folder.'150-'.$file, $permalink, $picture->_name, $picture->_description);
			
			}
			
			echo '</ul>';
			
			Html::html5('c');
		
		}
		
		/**
			* Display comments about an album
			*
			* @access	private
		*/
		
		private function display_comments(){
		
			Html::header_albums($this->_album->_name.' > Comments');
			Html::album_details($this->_album->_id, $this->_album->_author_name, $this->_album->_date, nl2br($this->_album->_description), $this->_album->_name, WS_URL.'?ctl='.$this->_pid.'&album='.$this->_album->_id);
			
			echo '<br/><span id="go_back_album"><a href="'.PATH.'?ctl='.$this->_pid.'&album='.VGet::album().'">Go Back</a></span>';
			
			if(VSession::renderer() != 'mobile'){
			
				//create comment section
				if($this->_album->_allow_comment == 'open'){
				
					$c = new Comments($this->_album->_id);
					$c->display_content();
				
				}else{
				
					Html::comment_closed();
				
				}
			
			}
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			if(!empty($this->_album) && VGet::comments(false) !== false)
				$this->display_comments();
			elseif(!empty($this->_album))
				$this->display_album();
			else
				$this->display_albums();
		
		}
	
	}

?>