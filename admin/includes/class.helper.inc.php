<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011
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
	
	namespace Admin\Helper;
	use \Library\Database\Database as Database;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	
	/**
		* Helper
		*
		* Main helper for backend containing generic functions
		*
		* @package		Administration
		* @namespace	Helper
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Helper{
	
		/**
			* Retrieve all categories from the database for a specific type
			*
			* @static
			* @access	public
			* @param	array [$array] Array to populate with categories (passed by reference)
			* @param	string [$msg] Attribute to return a message to if error raisen (passed by reference)
			* @param	string [$type] Category type that has to be retrieved
		*/
		
		public static function get_categories(&$array, &$msg, $type){
		
			try{
			
				$db =& Database::load();
				
				$to_read['table'] = 'category';
				$to_read['columns'] = array('CATEGORY_ID', 'category_name');
				$to_read['condition_columns'][':t'] = 'category_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = $type;
				$to_read['value_types'][':t'] = 'str';
				$cats = $db->read($to_read);
				
				if(is_array($cats))
					foreach($cats as $cat)
						$array[$cat['CATEGORY_ID']] = $cat['category_name'];
			
			}catch(Exception $e){
			
				$msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Remove all accents from a string and replace it with a normal character with the same case
			*
			* @static
			* @access	public
			* @param	string [$string] Initial string
			* @return	string String with accents removed
		*/
		
		public static function remove_accent($string){
		
			$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
		
			$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
		                
			return str_replace($a, $b, $string);
			
		}
		
		/**
			* Create a slug from a string, alias it make a string url-friendly
			*
			* @static
			* @access	public
			* @param	string [$string] Initial string
			* @return	string URL-friendly string
		*/
		
		public static function slug($string){
		
			return mb_strtolower(preg_replace(array('/[^a-zA-Z0-9 \'-]/', '/[ -\']+/', '/^-|-$/'), array('', '-', ''), self::remove_accent($string)));
		
		}
		
		/**
			* Build an html datalist from an array for search input
			*
			* @static
			* @access	public
			* @param	integer [$id] Datalist id
			* @param	array [$array] Array of objects
			* @param	string [$column] Object attribute to set in datalist
			* @return	mixed if $array is not an array it returns false, otherwise it returns html datalist
		*/
		
		public static function datalist($id, $array, $column){
		
			if(is_array($array)){
				
				$datalist = '<datalist id="'.$id.'">';
				
				foreach($array as $value)
					$datalist .= '<option value="'.$value->$column.'">';
				
				$datalist .= '</datalist>';
				
				return $datalist;
				
			}else{
			
				return false;
			
			}
		
		}
		
		/**
			* Build the password hash stored in database, make a md5 of concatenation of username, salt (from config.php) and the password
			*
			* @static
			* @access	public
			* @param	string [$username] 
			* @param	string [$pwd]
			* @return	string
		*/
		
		public static function make_password($username, $pwd){
		
			return md5($username.SALT.$pwd);
		
		}
		
		/**
			* Generate a salt
			*
			* @static
			* @access	public
			* @return	string
		*/
		
		public static function generate_salt(){
		
			return base64_encode(md5(md5(uniqid().mt_rand(time().mt_rand().(time()+rand()), (time()+rand()).mt_rand().time()))));
		
		}
	
	}

?>