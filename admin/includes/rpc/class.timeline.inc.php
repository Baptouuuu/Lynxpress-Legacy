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
	
	namespace Admin\Rpc;
	use \LIbrary\Variable\Get as VGet;
	use \Library\File\File as File;
	use Exception;
	
	/**
		* RPC Timeline
		*
		* Generate json which is used for timelines
		* CURL GET http://:website/admin/?ns=rpc&ctl=timeline&since=:Y-m-d
		* Return a JSON file
		*
		* @package		Administration
		* @namespace	Rpc
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0.1
		* @final
	*/
	
	final class Timeline extends Master{
	
		private $_since = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_since = VGet::since(date('Y-m-d'));
			
			$this->_url = 'cache/timeline/'.$this->_since.'.json';
			
			if($this->check_cache() === false){
			
				$this->_content = array('title' => WS_NAME, 'url' => WS_URL, 'content' => array());
				
				$this->get_content();
				
				$cache = new File();
				$cache->_content = json_encode($this->_content);
				$cache->save($this->_url);
			
			}else{
			
				$cache = File::read($this->_url);
				$this->_content = json_decode($cache->_content, true);
			
			}
		
		}
		
		/**
			* Retrieve posts since a specified date
			* If no date specified, retrieve posts published for the current date
			*
			* @access	private
		*/
		
		private function get_content(){
		
			try{
			
				$to_read['table'] = 'post';
				$to_read['columns'] = array('post_title', 'post_date', 'post_permalink');
				$to_read['condition_columns'][':d'] = 'post_date';
				$to_read['condition_select_types'][':d'] = '>';
				$to_read['condition_values'][':d'] = $this->_since.' 00:00:00';
				$to_read['value_types'][':d'] = 'str';
				$to_read['condition_types'][':s'] = 'AND';
				$to_read['condition_columns'][':s'] = 'post_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				$to_read['order'] = array('post_date', 'DESC');
				
				$this->_content['content'] = $this->_db->read($to_read);
			
			}catch(Exception $e){
			
				$this->_content['content'] = array();
			
			}
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			if(VGet::action() == 'check')
				echo '{"lynxpress":"true"}';
			else
				echo json_encode($this->_content);
		
		}
	
	}

?>