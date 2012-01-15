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
	use \Library\Models\Link as Link;
	use \Library\Variable\Session as VSession;
	use Exception;
	
	/**
		* Links
		*
		* Handle all display about related links
		*
		* You can use this class to display your sources for example
		*
		* Or to add easily feed links in html head tag
		*
		* @package		Site
		* @subpackage	Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Links extends Master{
	
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Links';
			$this->_menu = array('External sources you should take a look to');
			
			$this->get_content();
		
		}
		
		/**
			* Retrieve related links
			*
			* @access	private
		*/
		
		private function get_content(){
		
			try{
			
				$to_read['table'] = 'link';
				$to_read['columns'] = array('LINK_ID');
				$to_read['order'] = array('link_priority', 'ASC');
				$this->_content = $this->_db->read($to_read);
				
				if(!empty($this->_content))
					foreach ($this->_content as &$value)
						$value = new Link($value['LINK_ID']);
			
			}catch(Exception $e){
			
				header('Location: 404.php');
			
			}
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			if(!empty($this->_content)){
			
				Html::header_links();
				
				if(!VSession::html5())
					echo '<ul>';
				
				foreach ($this->_content as $link)
					Html::related_link($link->_name, $link->_link, $link->_rss_link, $link->_notes, $link->_priority);
				
				if(!Vsession::html5())
					echo '</ul>';
			
			}else{
			
				Html::no_content('There\'s no link registered yet.');
			
			}
		
		}
	
	}

?>