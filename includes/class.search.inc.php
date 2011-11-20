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
	use \Library\Models\Post as Post;
	use \Library\Models\User as User;
	use \Library\Models\Category as Category;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Session as VSession;
	use Exception;
	
	/**
		* Search
		*
		* Search handle retrieving posts from a search, a tag or a category
		*
		* @package		Site
		* @subpackage	Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	class Search extends Master{
	
		private $_search = null;
		private $_words_to_find = null;
		private $_tag = null;
		private $_cat = null;
		private $_by_date = null;
		private $_nb_pages = null;
		private $_nb_results = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->build_search();
			$this->get_content();
			$this->build_menu();
			parent::build_title();
		
		}
		
		/**
			* Retrieve wanted posts from the database
			*
			* @access	private
		*/
		
		private function get_content(){
		
			try{
		
				$to_read['table'] = $this->_sql_table;
				$to_read['columns'] = array('POST_ID');
				
				if(!empty($this->_search)){
				
					foreach($this->_words_to_find as $key => $word){
						
						$search = '%'.$word.'%';
						$to_read['condition_types'][":title$key"] = 'OR';
						$to_read['condition_columns']['group'][":title$key"] = 'post_title';
						$to_read['condition_select_types'][":title$key"] = 'LIKE';
						$to_read['condition_values'][":title$key"] = $search;
						$to_read['value_types'][":title$key"] = 'str';
						$to_read['condition_types'][":content$key"] = 'OR';
						$to_read['condition_columns']['group'][":content$key"] = 'post_content';
						$to_read['condition_select_types'][":content$key"] = 'LIKE';
						$to_read['condition_values'][":content$key"] = $search;
						$to_read['value_types'][":content$key"] = 'str';
					
					}
				
				}elseif(!empty($this->_tag)){
				
					$to_read['condition_columns'][':tag'] = 'post_tags';
					$to_read['condition_select_types'][':tag'] = 'LIKE';
					$to_read['condition_values'][':tag'] = '%'.$this->_tag.'%';
					$to_read['value_types'][':tag'] = 'str';
				
				}elseif(!empty($this->_cat)){
				
					$to_read['condition_columns'][':cat'] = 'post_category';
					$to_read['condition_select_types'][':cat'] = 'LIKE';
					$to_read['condition_values'][':cat'] = '%'.$this->_cat.'%';
					$to_read['value_types'][':cat'] = 'str';
				
				}elseif(!empty($this->_by_date)){
				
					$to_read['condition_columns'][':date'] = 'post_date';
					$to_read['condition_select_types'][':date'] = 'LIKE';
					$to_read['condition_values'][':date'] = $this->_by_date.'%';
					$to_read['value_types'][':date'] = 'str';
				
				}
				
				$to_read['condition_types'][':status'] = 'AND';
				$to_read['condition_columns'][':status'] = 'post_status';
				$to_read['condition_select_types'][':status'] = '=';
				$to_read['condition_values'][':status'] = 'publish';
				$to_read['value_types'][':status'] = 'str';
				$to_read['limit'] = array($this->_limit_start, parent::ITEMS_PAGE);
				$to_read['order'] = array('post_date', 'DESC');
				
				$this->_content = $this->_db->read($to_read);
				
				$this->get_nb_pages($to_read);
				
				if(!empty($this->_content)){
				
					foreach($this->_content as &$post){
					
						$post = new Post($post['POST_ID']);
						
						$user = new User();
						$user->_id = $post->_author;
						$user->read('_publicname');
						
						$post->_author_name = $user->_publicname;
						
						$updated = $post->__get('_updated');
						
						if($updated == 'yes'){
						
							$user ->_id = $post->_update_author;
							$user->read('_publicname');
							
							$post->_update_author_name = $user->_publicname;
						
						}
					
					}
				
				}
			
			}catch(Exception $e){
			
				@error_log($e->getMessage(), 1, WS_EMAIL);
				header('Location: 404.php');
			
			}
		
		}
		
		/**
			* Get maximum pages to know if wether or not to display page navigation
			*
			* Method is called in get_content method in order to write only once condition for the search
			*
			* @access	private
			* @param	$to_read, array
		*/
		
		private function get_nb_pages($to_read){
		
			unset($to_read['order']);
			unset($to_read['limit']);
			$to_read['columns'][0] = 'COUNT(POST_ID) AS id';
			
			$nb = $this->_db->read($to_read);
			
			$this->_nb_pages = ceil($nb[0]['id']/parent::ITEMS_PAGE);
			$this->_nb_results = $nb[0]['id'];
		
		}
		
		/**
			* Extract wanted words and set them in associated attributes
			*
			* @access	private
		*/
		
		private function build_search(){
		
			if(substr(VGet::q(), 0, 4) == 'date'){
			
				$this->_by_date = trim(substr(VGet::q(), 5));
			
			}elseif(VGet::q()){
			
				$this->_words_to_find = array_unique(explode(' ', trim(VGet::q())));
				$this->_search = implode(' ', $this->_words_to_find);
					
			
			}elseif(VGet::tag()){
			
				$this->_tag = VGet::tag();
			
			}elseif(VGet::cat()){
			
				$this->_cat = VGet::cat();
			
			}else{
			
				header('Location: 404.php');
			
			}
		
		}
		
		/**
			* Build the menu at the right side of search input
			*
			* @access	private
		*/
		
		private function build_menu(){
		
			if(!empty($this->_search)){
			
				$this->_menu = array($this->_nb_results.' Result(s) for the search "'.$this->_search.'"');
			
			}elseif(!empty($this->_tag)){
			
				$this->_menu = array($this->_nb_results.' Post(s) referenced with the tag "'.ucfirst($this->_tag).'"');
			
			}elseif(!empty($this->_cat)){
			
				try{
				
					$cat = new Category($this->_cat);
					
					$this->_menu = array($this->_nb_results.' Post(s) categorized in "'.ucwords($cat->_name).'"');
				
				}catch(Exception $e){
				
					header('Location: 404.php');
				
				}
			
			}elseif(!empty($this->_by_date)){
			
				$this->_menu = array($this->_nb_results.' Post(s) published on '.date('M Y', strtotime($this->_by_date)));
			
			}
		
		}
		
		/**
			* Display the posts listing
			*
			* @access	private
		*/
		
		private function display_listing(){
		
			if(!VSession::html5())
				echo '<ul id="listing_articles">';
			
			if(!empty($this->_content)){
			
				foreach($this->_content as $article){
				
					$crop_length = Helper\Posts::crop_length($article->_content);
					
					$cats = explode(',', $article->_category);
					
					try{
					
						foreach($cats as &$cat){
						
							$id = $cat;
							$infos = new Category($id);
							$cat = Helper\Posts::make_category_link($id, $infos->_name);
						
						}
					
					}catch(Exception $e){
					
						@error_log($e->getMessage(), 1, WS_EMAIL);
					
					}
					
					$content = nl2br(substr($article->_content, 0, $crop_length));
					
					Html::listing_article($article->_title, $article->_permalink, $cats, $article->_date, $article->_author_name, $content);
				
				}
			
			}
			
			if(!VSession::html5())
				echo '</ul>';
			
			if(VSession::renderer() != 'mobile' && !empty($this->_content))
				Html::navigation($this->_page, $this->_nb_pages, parent::link_navigation());
		
		}
		
		/**
			* Display an error message what says there's no result for the search
			*
			* @access	private
		*/
		
		private function display_error(){
		
			if(!empty($this->_search)){
			
				$msg = $this->_search;
			
			}elseif(!empty($this->_by_date)){
			
				$msg = date('M Y', strtotime($this->_by_date));
			
			}elseif(!empty($this->_tag)){
			
				$msg = $this->_tag;
			
			}elseif(!empty($this->_cat)){
			
				try{
				
					$cat = new Category($this->_cat);
					$msg = ucwords($cat->_name);
				
				}catch(Exception $e){
				
					header('Location: 404.php');
				
				}
			
			}
			
			if(!VSession::html5())
				echo '<ul id="listing_articles">';
			
			Html::no_content('No results founded for "'.$msg.'"');
			
			if(!VSession::html5())
				echo '</ul>';
		
		}
		
		/**
			* Display page_content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			if(!empty($this->_content))
				$this->display_listing();
			else
				$this->display_error();
		
		}
	
	}

?>