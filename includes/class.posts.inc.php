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
	use \Library\Models\Post as Post;
	use \Library\Models\User as User;
	use \Library\Models\Category as Category;
	use Exception;
	
	/**
		* Posts
		*
		* Handle all display about posts
		*
		* @package		Site
		* @subpackage	Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Posts extends Master{
	
		private $_view_type = null;
		private $_nb_pages = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			if(VGet::news())
				$this->_view_type = 'news';
			else
				$this->_view_type = 'listing';
			
			Helper\Helper::get_categories($this->_menu);
			$this->build_menu();
			$this->get_content();
			$this->get_nb_pages();
			parent::build_title();
		
		}
		
		/**
			* Retrieve wanted posts
			*
			* @access	private
		*/
		
		private function get_content(){
		
			try{
		
				$to_read['table'] = $this->_sql_table;
				$to_read['columns'] = array('POST_ID');
				$to_read['condition_columns'][':s'] = 'post_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				
				if($this->_view_type == 'news'){
				
					$to_read['condition_types'][':p'] = 'AND';
					$to_read['condition_columns'][':p'] = 'post_permalink';
					$to_read['condition_select_types'][':p'] = '=';
					$to_read['condition_values'][':p'] = VGet::news();
					$to_read['value_types'][':p'] = 'str';
				
				}
				
				if(VGet::preview())
					$to_read['condition_values'][':s'] = 'draft';
				
				$to_read['order'] = array('post_date', 'DESC');
				$to_read['limit'] = array($this->_limit_start, parent::ITEMS_PAGE);
				
				$this->_content = $this->_db->read($to_read);
				
				if(!empty($this->_content)){
				
					foreach($this->_content as &$post){
					
						$post = new Post($post['POST_ID']);
						
						$user = new User();
						$user->_id = $post->_author;
						$user->read('_publicname');
						
						$post->_author_name = $user->_publicname;
						
						$updated = $post->_updated;
						
						if($updated == 'yes'){
						
							$user->_id = $post->_update_author;
							$user->read('_publicname');
							
							$post->_update_author_name = $user->_publicname;
						
						}
					
					}
				
				}elseif(empty($this->_content) && $this->_view_type == 'news' && !VGet::preview()){
				
					header('Location: 404.php');
				
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
		
			foreach($this->_menu as $key => &$value)
				$value = Helper\Posts::make_category_link($key, $value);
		
		}
		
		/**
			* Display the posts listing
			*
			* @access	private
		*/
		
		private function display_listing(){
		
			if(!VSession::html5() || VSession::renderer() == 'mobile')
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
					
						@error_log($e->getMessage().' file: '.__FILE__.'; line: '.__LINE__, 1, WS_EMAIL);
					
					}
					
					$content = nl2br(substr($article->_content, 0, $crop_length));
					
					Html::listing_article($article->_title, $article->_permalink, $cats, $article->_date, $article->_author_name, $content);
				
				}
			
			}else{
			
				Html::no_content('There\'s no post right now.');
			
			}
			
			if(!VSession::html5() || VSession::renderer() == 'mobile')
				echo '</ul>';
			
			if(VSession::renderer() != 'mobile' && !empty($this->_content))
				Html::navigation($this->_page, $this->_nb_pages, parent::link_navigation());
		
		}
		
		/**
			* Display a specific post
			*
			* @access	private
		*/
		
		private function display_news(){
		
			if(!empty($this->_content)){
			
				$cats = explode(',', $this->_content[0]->_category);
				
				try{
				
					foreach($cats as &$cat){
					
						$id = $cat;
						$infos = new Category($id);
						$cat = Helper\Posts::make_category_link($id, $infos->_name);
					
					}
				
				}catch(Exception $e){
				
					@error_log($e->getMessage().' file: '.__FILE__.'; line: '.__LINE__, 1, WS_EMAIL);
				
				}
				
				$content = nl2br($this->_content[0]->_content);
				
				$tags = explode(',', $this->_content[0]->_tags);
				
				foreach($tags as &$tag)
					$tag = Helper\Posts::make_tag_link($tag);
				
				$link = WS_URL.'?ctl=posts&news='.$this->_content[0]->_title;
				
				Html::html5('o', 'id="news_alone">');
				
				Html::article_alone($this->_content[0]->_title, $this->_content[0]->_date, $this->_content[0]->_author_name, $cats, $content, $tags, $link, $this->_content[0]->_updated, $this->_content[0]->_update_author_name);
				
				if(VSession::renderer() != 'mobile'){
									
					//create comment section
					if($this->_content[0]->_allow_comment == 'open'){
					
						$c = new Comments($this->_content[0]->_id);
						$c->display_content();
					
					}else{
					
						Html::comment_closed();
					
					}
					
				}
				
				Html::html5('c');
			
			}
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$method = 'display_'.$this->_view_type;
			
			$this->$method();
		
		}
		
		/**
			* Determine maximum pages number
			*
			* @access	private
		*/
		
		private function get_nb_pages(){
		
			$to_read['table'] = $this->_sql_table;
			$to_read['columns'] = array('COUNT(POST_ID) AS id');
			$to_read['condition_columns'][':s'] = 'post_status';
			$to_read['condition_select_types'][':s'] = '=';
			$to_read['condition_values'][':s'] = 'publish';
			$to_read['value_types'][':s'] = 'str';
			
			if($this->_view_type == 'news'){
			
				$to_read['condition_types'][':p'] = 'AND';
				$to_read['condition_columns'][':p'] = 'post_permalink';
				$to_read['condition_select_types'][':p'] = '=';
				$to_read['condition_values'][':p'] = VGet::news();
				$to_read['value_types'][':p'] = 'str';
			
			}
			
			$nb = $this->_db->read($to_read);
			
			$this->_nb_pages = ceil($nb[0]['id']/parent::ITEMS_PAGE);
		
		}
	
	}

?>