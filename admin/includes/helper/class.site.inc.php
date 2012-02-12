<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011-2012
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
	use \Library\Models\Setting as Setting;
	use Exception;
	
	/**
		* Site Helper
		*
		* Helper for frontend containing generic functions
		*
		* @package		Administration
		* @namespace	Helper
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Site{
	
		/**
			* Add an element to the site menu
			*
			* @static
			* @access	public
			* @param	string [$ctl] Controller name
			* @param	string [$name] Name that will be displayed
			* @return	boolean
		*/
		
		public static function add_to_menu($ctl, $name){
		
			try{
			
				$db =& Database::load();
				
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('SETTING_ID');
				$to_read['condition_columns'][':t'] = 'setting_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'site_menu';
				$to_read['value_types'][':t'] = 'str';
				
				$setting = $db->read($to_read);
				
				if(empty($setting)){
				
					$setting = new Setting();
					$setting->_name = 'Site Menu';
					$setting->_type = 'site_menu';
					$setting->_data = json_encode(array(array('ctl' => $ctl, 'name' => $name)));
					$setting->create();
				
				}else{
				
					$setting = new Setting($setting[0]['SETTING_ID']);
					$data = json_decode($setting->_data, true);
					$data[] = array('ctl' => $ctl, 'name' => $name);
					$setting->_data = json_encode($data);
					$setting->update('_data', 'str');
				
				}
				
				return true;
			
			}catch(Exception $e){
			
				return false;
			
			}
		
		}
	
	}

?>