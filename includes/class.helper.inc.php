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
	
	namespace Site\Helper;
	use \Library\Database\Database as Database;
	use \Library\Variable\Get as VGet;
	
	/**
		* Main
		*
		* Main helper for frontend containing generic functions
		*
		* @package		Site
		* @subpackage	Helper
		* @namespace	Helper
		* @author		Baptiste Langalde lynxpressorg@gmail.com
		* @version		1.1
		* @abstract
	*/
	
	abstract class Helper{
	
		const CONTROLLER = false;
		
		/**
			* Method to retrieve categories to put them in the menu
			*
			* @static
			* @access	public
			* @param	array [$menu] Menu to populate (passed by reference)
			* @param	string [$type] Category type
		*/
		
		public static function get_categories(&$menu, $type = 'post'){
		
			$db =& Database::load();
			
			$to_read['table'] = 'category';
			$to_read['columns'] = array('CATEGORY_ID', 'category_name');
			$to_read['condition_columns'][':t'] = 'category_type';
			$to_read['condition_select_types'][':t'] = '=';
			$to_read['condition_values'][':t'] = $type;
			$to_read['value_types'][':t'] = 'str';
			$to_read['order'] = array('category_name', 'ASC');
			
			$tmp = $db->read($to_read);
			
			foreach($tmp as $cat)
				$menu[$cat['CATEGORY_ID']] = $cat['category_name'];
		
		}
	
	}
	
	/**
		* Posts
		*
		* Regroup methods to modify posts content
		*
		* @package		Site
		* @subpackage	Helper
		* @namespace	Helper
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Posts{
	
		const CONTROLLER = false;
		
		/**
			* Build a search category link
			*
			* @static
			* @access	public
			* @param	integer [$id] Category id
			* @param	string [$name] Category name
			* @return	string
		*/
		
		public static function make_category_link($id, $name){
		
			return '<a href="'.PATH.'?ctl=search&cat='.$id.'" title="Posts for this category">'.ucwords($name).'</a>';
		
		}
		
		/**
			* Build a search tag link
			*
			* @static
			* @access	public
			* @param	string [$name] Tag name
			* @return	string
		*/
		
		public static function make_tag_link($name){
		
			return '<a href="'.PATH.'?ctl=search&tag='.trim($name).'" title="Search all posts with this tag">'.strtolower(trim($name)).'</a>';
		
		}
		
		/**
			* Method to determine the number where to crop a content
			*
			* Useful for a listing of posts to not display the whole article but without cropping in the middle of an html tag
			*
			* @static
			* @access	public
			* @param	string [$content]
			* @return	integer
		*/
		
		public static function crop_length($content){
		
			$tags = array('<a ' => '</a>', '<ul' => '</ul>', '<img ' => '/>', '<strong>' => '</strong>', '<em>' => '</em>', '<blockquote>' => '</blockquote>', '<code' => '</code>');
			
			foreach($tags as $start_tag => $end_tag)
				if(stripos($content, $start_tag) !== false)
					$pos_start_tags[$start_tag] = stripos($content, $start_tag);
			
			if(isset($pos_start_tags)){
				
				$first_tag_position = min($pos_start_tags);
			
				foreach($pos_start_tags as $start_tag => $position)
					if($position == $first_tag_position)
						$tag_to_find = $start_tag;
			
			}
			
			if(!isset($tag_to_find)){
			
				$crop_length = 500;
			
			}else{
			
				$crop_length = stripos($content, $tags[$tag_to_find]);
				
				switch($tag_to_find){
				
					case '<a ':
						$crop_length += 4;
						break;
					
					case '<ul':
						$crop_length += 5;
						break;
					
					case '<img ':
						$crop_length += 2;
						break;
					
					case '<strong>':
						$crop_length += 9;
						break;
					
					case '<em>':
						$crop_length += 5;
						break;
					
					case '<blockquote>':
						$crop_length += 13;
						break;
					
					case '<code':
						$crop_length += 7;
						break;
			
				}
			
			}
			
			return $crop_length;
		
		}
		
		/**
			* Check if current controller can display an archive list
			*
			* @static
			* @access	public
			* @return	boolean
		*/
		
		public static function check_pub_dates(){
		
			if(in_array(VGet::ctl(), array('posts', 'search')))
				return true;
			else
				return false;
		
		}
		
		/**
			* Display a list of publication dates for news
			*
			* @static
			* @access	public
			* @param	string [$pattern] Date structure to display
			* @param	integer [$max] Maximum months to display
		*/
		
		public static function pub_dates($pattern = 'F Y', $max = 12){
		
			if(self::check_pub_dates()){
			
				$db =& Database::load();
				
				$to_read['table'] = 'post';
				$to_read['columns'] = array('post_date');
				
				$dates = $db->read($to_read);
				
				if(!empty($dates))
					foreach($dates as &$date)
						$date = substr($date['post_date'], 0, 7);
				
				$dates = array_unique($dates);
				
				if(!empty($dates))
					foreach($dates as $key => $date)
						if($key > $max)
							unset($dates[$key]);
				
				echo '<ul>';
				
				if(!empty($dates))
					foreach($dates as $date)
						echo '<li><a href="index.php?ctl=search&q=date:'.$date.'">'.date($pattern, strtotime($date)).'</a></li>';
				
				echo '</ul>';
			
			}
		
		}
	
	}
	
	/**
		* Menu Helper
		*
		* Function applied to website menu
		*
		* @package		Site
		* @subpackage	Helper
		* @namespace	Helper
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Menu{
	
		const CONTROLLER = false;
		
		/**
			* Retrieve setting menu to extend website menu
			*
			* @static
			* @access	public
			* @return	array
		*/
		
		public static function extend(){
		
			$db =& Database::load();
			
			$to_read['table'] = 'setting';
			$to_read['columns'] = array('setting_data');
			$to_read['condition_columns'][':t'] = 'setting_type';
			$to_read['condition_select_types'][':t'] = '=';
			$to_read['condition_values'][':t'] = 'site_menu';
			$to_read['value_types'][':t'] = 'str';
			
			$data = $db->read($to_read);
			
			if(empty($data))
				return array();
			
			return json_decode($data[0]['setting_data'], true);
		
		}
	
	}

?>