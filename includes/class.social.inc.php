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
	use \Library\Database\Database as Database;
	
	/**
		* Social
		*
		* Handles display of social network sharing buttons
		*
		* @package		Site
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Social{
	
		/**
			* Retrieve share buttons setting
			*
			* @static
			* @access	public
			* @return	array
		*/
		
		public static function get_setting(){
		
			$db =& Database::load();
			
			$to_read['table'] = 'setting';
			$to_read['columns'] = array('setting_data');
			$to_read['condition_columns'][':t'] = 'setting_type';
			$to_read['condition_select_types'][':t'] = '=';
			$to_read['condition_values'][':t'] = 'share_buttons';
			$to_read['value_types'][':t'] = 'str';
			
			$array = $db->read($to_read);
			
			$array = json_decode($array[0]['setting_data']);
			
			return $array;
		
		}
		
		/**
			* Display facebook share button
			*
			* @static
			* @access	public
			* @param	string [$title]
			* @param	string [$link]
		*/
		
		public static function display_facebook($title, $link){
		
			echo '<a id="sfacebook" rel="nofollow" href="http://www.facebook.com/sharer.php?u='.urlencode($link).'&t='.urlencode($title).'" title="Partager sur facebook" target="_blank">Facebook</a> ';
		
		}
		
		/**
			* Display twitter tweet button
			*
			* @static
			* @access	public
			* @param	string [$title]
			* @param	string [$link]
		*/
		
		public static function display_twitter($title, $link){
		
			echo '<a id="stwitter" rel="nofollow" href="http://twitter.com/share?url='.urlencode($link).'&amp;via=Lynxpressorg&amp;text='.urlencode($title).'" title="Tweet" target="_blank">Tweet</a>';
		
		}
		
		/**
			* Display google plus one button
			*
			* @static
			* @access	public
		*/
		
		public static function display_google(){
		
			echo '<script type="text/javascript" src="http://apis.google.com/js/plusone.js">'.
				 	'{lang: \'fr\'}'.
				 '</script>'.
				 '<div id="plus-1"><g:plusone></g:plusone></div>';
		
		}
		
		/**
			* Display activated sharing buttons
			*
			* @static
			* @access	public
			* @param	string [$title]
			* @param	string [$link]
		*/
		
		public static function share($title, $link){
		
			$setting = self::get_setting();
		
			echo '<div id="share_bar">';
			
			foreach ($setting as $value){
			
				$action = 'display_'.$value;
				self::$action($title, $link);
			
			}
			
			echo '</div>';
		
		}
	
	}

?>