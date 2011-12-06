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
	
	namespace Admin\Settings;
	use \Admin\Master\Master as Master;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	
	/**
		* Manage Settings
		*
		* Display all settings sections available
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Settings
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Settings';
		
		}
		
		/**
			* Display related admin parts link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::menu();
		
		}
		
		/**
			* Display settings links
			*
			* @access	private
		*/
		
		private function display_settings(){
		
			echo '<section id="labels">';
			
			Html::categories();
			Html::post();
			Html::users();
			Html::roles();
			Html::social();
			Html::default_page();
			Html::template();
			Html::plugins();
			Html::links();
			Html::activity();
			Html::update();
			
			echo '</section>';
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			if($this->_user['settings'])
				$this->display_settings();
			else
				echo ActionMessages::part_no_perm();
		
		}
	
	}

?>