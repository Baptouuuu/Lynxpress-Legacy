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
	use \Library\Database\Database as Database;
	use \Library\Variable\Session as VSession;
	
	/**
		* Html
		*
		* Class contains basic views of the website
		*
		* @package		Site
		* @subpackage	Views
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0.1
		* @abstract
	*/
	
	abstract class Html{
	
		const TEMPLATES = '\\Templates\\';
		const HTML = '\\Html';
		const CONTROLLER = false;
		
		/**
			* Retrieve used template from database and put it in Session variable
			*
			* @static
			* @access	private
		*/
		
		private static function get_template(){
		
			static $template = null;
			
			if(empty($template)){
			
				$db =& Database::load();
				
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('setting_data');
				$to_read['condition_columns'][':t'] = 'setting_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'current_template';
				$to_read['value_types'][':t'] = 'str';
				
				$setting = $db->read($to_read);
				
				if(!empty($setting))
					$template = $setting[0]['setting_data'];
				else
					$template = 'main';
			
			}
			
			return $template;
		
		}
		
		/**
			* Method that return the good path to include the header
			*
			* @static
			* @access	public
			* @return	string
		*/
		
		public static function header(){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			return 'includes/templates/'.$template.'/'.$class::header();
		
		}
		
		/**
			* Method that return the good path to include the footer
			*
			* @static
			* @access	public
			* @return	string
		*/
		
		public static function footer(){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			return 'includes/templates/'.$template.'/'.$class::footer();
		
		}
		
		/**
			* Method to display an article in a list view
			*
			* @static
			* @access	public
			* @param	string [$title] Post title
			* @param	string [$permalink] Post permalink
			* @param	array [$categories] Post categories
			* @param	string [$date] Post creation date
			* @param	string [$author] Post author public name
			* @param	string [$content] Post content
		*/
		
		public static function listing_article($title, $permalink, $categories, $date, $author, $content){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::listing_article($title, $permalink, $categories, $date, $author, $content);
		
		}
		
		/**
			* Method to display a whole post
			*
			* @static
			* @access	public
			* @param	string [$title] Post title
			* @param	string [$date] Post creation date
			* @param	string [$author] Post author public name
			* @param	array [$categories] Post categories
			* @param	string [$content] Post content
			* @param	array [$tags] Post tags
			* @param	string [$link] Post permalink
			* @param	string [$updated] "yes" if the post has been updated
			* @param	string [$u_author] Post update author public name
		*/
		
		public static function article_alone($title, $date, $author, $categories, $content, $tags, $link, $updated, $u_author = null){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::article_alone($title, $date, $author, $categories, $content, $tags, $link, $updated, $u_author);
		
		}
		
		/**
			* Method to display message when there's no content
			*
			* @static
			* @access	public
			* @param	string [$message] Message to display when there's no content
		*/
		
		public static function no_content($message){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::no_content($message);
		
		}
		
		/**
			* Method to display a comment form 
			*
			* @static
			* @access	public
			* @param	string [$name] Comment name
			* @param	string [$email] Comment email
			* @param	string [$content] Comment content
			* @param	boolean [$error_name]
			* @param	boolean [$error_email]
			* @param	boolean [$error_content]
			* @param	string [$form_side_image]
			* @param	integer [$n1]
			* @param	integer [$n2]
			* @param	string [$result]
		*/
		
		public static function comment_form($name, $email, $content, $error_name, $error_email, $error_content, $error_captcha, $form_side_image, $n1, $n2, $result){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::comment_form($name, $email, $content, $error_name, $error_email, $error_content, $error_captcha, $form_side_image, $n1, $n2, $result);
		
		}
		
		/**
			* Method to display message after comment form has been submitted
			*
			* @static
			* @access	public
			* @param	boolean [$bool]
		*/
		
		public static function submitted_form($bool){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::submitted_form($bool);
		
		}
		
		/**
			* Method to display a comment
			*
			* @static
			* @access	public
			* @param	integer [$id] Comment id
			* @param	string [$name] Comment name
			* @param	string [$date] Comment creation date
			* @param	string [$link] Comment permalink
			* @param	string [$content] Comment content
			* @param	string [$email] Comment email
		*/
		
		public static function comment($id, $name, $date, $link, $content, $email){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::comment($id, $name, $date, $link, $content, $email);
		
		}
		
		/**
			* Display a message when comments are closed<br/>
			* this function is not called in Comments class<br/>
			* but has to be called in controllers
			*
			* @static
			* @access	public
		*/
		
		public static function comment_closed(){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::comment_closed();
		
		}
		
		/**
			* Display a header bar for albums
			*
			* @static
			* @access	public
			* @param	string [$add] Additional text for the header bar
		*/
		
		public static function header_albums($add){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::header_albums($add);
		
		}
		
		/**
			* Display a label for an album in a listing
			*
			* @static
			* @access	public
			* @param	integer [$id] Album id
			* @param	string [$title] Album name
			* @param	string [$link] Album directory path
			* @param	string [$description] Album description
		*/
		
		public static function album_label($id, $title, $link, $description){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::album_label($id, $title, $link, $description);
		
		}
		
		/**
			* Display additional informations about an album
			*
			* @static
			* @access	public
			* @param	integer [$id] Album id
			* @param	string [$author] Album author public name
			* @param	string [$date] Album creation date
			* @param	string [$description] Album description
			* @param	string [$title] Album title
			* @param	string [$link] Album permalink
			
		*/
		
		public static function album_details($id, $author, $date, $description, $title, $link){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::album_details($id, $author, $date, $description, $title, $link);
		
		}
		
		/**
			* Display a picture
			*
			* @static
			* @access	public
			* @param	string [$mini] Picture thumb path
			* @param	string [$full] Picture path
			* @param	string [$name] Picture name
			* @param	string [$desc] Picture description
		*/
		
		public static function album_picture($mini, $full, $name, $desc){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::album_picture($mini, $full, $name, $desc);
		
		}
		
		/**
			* Display a header bar for videos
			*
			* @static
			* @access	public
			* @param	string [$add] Additional text for the header bar
		*/
		
		public static function header_videos($add){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::header_videos($add);
		
		}
		
		/**
			* Display a video with its informations
			*
			* @static
			* @access	public
			* @param	string [$name] Video name
			* @param	string [$author] Video author public name
			* @param	string [$link] Video path
			* @param	string [$embed_code] Attached video embed code
			* @param	string [$description] Video description
			* @param	string [$date] Video upload date
		*/
		
		public static function video($name, $author, $link, $embed_code, $description, $date){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::video($name, $author, $link, $embed_code, $description, $date);
		
		}
		
		/**
			* Display a header bar for authors
			*
			* @static
			* @access	public
			* @param	string [$add] Additional text for header bar
		*/
		
		public static function header_authors($add){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::header_authors($add);
		
		}
		
		/**
			* Display an author card
			*
			* @static
			* @access	public
			* @param	string [$publicname] User public name
			* @param	string [$email] User email
			* @param	string [$website] User website
			* @param	string [$msn] User msn address
			* @param	string [$twitter] User twitter url
			* @param	string [$facebook] User facebokk url
			* @param	string [$google] User google+ url
			* @param	string [$avatar] User avatar path
			* @param	string [$bio] User description
		*/
		
		public static function author($publicname, $email, $website, $msn, $twitter, $facebook, $google, $avatar, $bio){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::author($publicname, $email, $website, $msn, $twitter, $facebook, $google, $avatar, $bio);
		
		}
		
		/**
			* Display a header bar for contact page
			*
			* @static
			* @access	public
		*/
		
		public static function header_contact(){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::header_contact();
		
		}
		
		/**
			* Display contact form
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function contact($part){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::contact($part);
		
		}
		
		/**
			* Display message after contact form has been submitted
			*
			* @static
			* @access	public
			* @param	mixed [$result]
		*/
		
		public static function contact_submitted($result){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::contact_submitted($result);
		
		}
		
		/**
			* Display a header for links page
			*
			* @static
			* @access	public
		*/
		
		public static function header_links(){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::header_links();
		
		}
		
		/**
			* Method to display a related link
			*
			* @static
			* @access	public
			* @param	string [$name] Link name
			* @param	string [$link] Link url
			* @param	string [$rss_link] Link RSS url
			* @param	string [$notes] Link notes
			* @param	integer [$priority] Link priority level
		*/
		
		public static function related_link($name, $link, $rss_link, $notes, $priority){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::related_link($name, $link, $rss_link, $notes, $priority);
		
		}
		
		/**
			* Visual for 404 page
			*
			* @static
			* @access	public
		*/
		
		public static function _404(){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::_404();
		
		}
		
		/**
			* Method to display navigation bar
			*
			* @static
			* @access	public
			* @param	integer [$p] Actual page
			* @param	integer [$max] Maximum of available pages
			* @param	string [$link] Complement in the url
		*/
		
		public static function navigation($p, $max, $link){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::navigation($p, $max, $link);
		
		}
		
		/**
			* Bridge function to a template method, used for a plugin to use its own methods
			*
			* @static
			* @access	public
			* @param	string [$name] Method name
			* @param	array [$arguments] Array of all arguments passed to the method
		*/
		
		public static function __callStatic($name, $arguments){
		
			$template = self::get_template();
			$class = self::TEMPLATES.ucfirst($template).self::HTML;
			
			$class::$name($arguments);
		
		}
		
		/**
			* Display an option for a dropdown
			*
			* @static
			* @access	public
			* @param	mixed [$value]
			* @param	mixed [$name]
		*/
		
		public static function option($value, $name){
		
			echo '<option value="'.$value.'">'.$name.'</option>';
		
		}
		
		/**
			* Method to build easily html5 or not tag
			*
			* @static
			* @access	public
			* @param	string [$tag] $tag can only contain "o" or "c"
			* @param	string [$chevron]
		*/
		
		public static function html5($tag, $chevron = '>'){
		
			if($tag == 'o'){
				
				if(Vsession::html5())
					echo '<section ';
				else
					echo '<div ';
				
				echo $chevron;
			
			}elseif($tag == 'c'){
			
				if(Vsession::html5())
					echo '</section>';
				else
					echo '</div>';
				
			}
			
		}
		
		/**
			* Create a datalist for search input
			*
			* @static
			* @access	public
			* @param	string [$list] Datalist id
		*/
		
		public static function datalist($list){
		
			$db =& Database::load();
			
			$to_read['table'] = 'post';
			$to_read['columns'] = array('post_title');
			$to_read['condition_columns'][':s'] = 'post_status';
			$to_read['condition_select_types'][':s'] = '=';
			$to_read['condition_values'][':s'] = 'publish';
			$to_read['value_types'][':s'] = 'str';
			
			$posts = $db->read($to_read);
			
			if(is_array($posts)){
				
				echo '<datalist id="'.$list.'">';
				
				foreach($posts as $value)
					echo '<option value="'.$value['post_title'].'">';
				
				echo '</datalist>';
				
			}
		
		}
	
	}

?>