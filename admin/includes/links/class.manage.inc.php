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
	
	namespace Admin\Links;
	use \Admin\Master\Master as Master;
	use Exception;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Library\Models\Link as Link;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Admin\Settings\Html as HtmlSettings;
	use \Admin\Session\Session as Session;
	use \Admin\Helper\Helper as Helper;
	
	/**
		* Manage Links
		*
		* Handles modification and deletion of links
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Links
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		private $_content = null;
		private $_levels = array(1 => 'Very High', 2 => 'High', 3 => 'Normal', 4 => 'Low', 5 => 'Very Low');
		private $_search = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			$this->_title = 'Links';
			
			if(VPost::search_button(false))
				$this->_search = VPost::search('foo');
			
			if($this->_user['settings']){
			
				$this->update();
				$this->delete();
				
				$this->get_links();
			
			}
		
		}
		
		/**
			* Get links id and then create object with those ids
			*
			* @access	private
		*/
		
		private function get_links(){
		
			try{
			
				$to_read['table'] = 'link';
				$to_read['columns'] = array('LINK_ID');
				$to_read['order'] = array('link_priority', 'ASC');
				
				if(VPost::search_button(false)){
				
					$to_read['condition_columns'][':name'] = 'link_name';
					$to_read['condition_select_types'][':name'] = 'LIKE';
					$to_read['condition_values'][':name'] = '%'.$this->_search.'%';
					$to_read['value_types'][':name'] = 'str';
				
				}
				
				$this->_content = $this->_db->read($to_read);
				
				if(!empty($this->_content))
					foreach($this->_content as &$link)
						$link = new Link($link['LINK_ID']);
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessage($e->getMessage());
			
			}
		
		}
		
		/**
			* Dsplay related admin parts link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_user['settings'])
				Html::ml_menu();
			else
				HtmlSettings::menu();
		
		}
		
		/**
			* Display applicable action buttons
			*
			* @access	private
			* @param	string [$position] $position can only be 'top' or 'butt'
		*/
		
		private function display_actions($position){
		
			if($position == 'top'){
			
				Html::ml_actions('o');
					 	
			 	foreach($this->_levels as $key => $value)
			 		Html::opt_priority($key, $value);
					 	
				Html::ml_actions('c');
			
			}elseif($position == 'butt'){
			
				Html::ml_delete();
			
			}
		
		}
		
		/**
			* Display a html table with retrieved links
			*
			* @access	private
		*/
		
		private function display_table(){
		
			Html::table('o');
			
			if(!empty($this->_content))
				foreach($this->_content as $link)
					Html::table_row($link->_id, $link->_name, $link->_link, $link->_rss_link, $link->_notes, $this->_levels[$link->_priority]);
			else
				if(VPost::search_button(false))
					echo '<tr><td colspan="6">No link found</td></tr>';
			
			Html::table('c');
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			if($this->_user['settings']){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', 'index.php?ns=links&ctl=manage');
				
				$this->display_actions('top');
				
				$this->display_table();
				
				$this->display_actions('butt');
				
				echo Helper::datalist('titles', $this->_content, '_name');
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update priority links attribute
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::change(false) && in_array(VPost::change_priority(), range(1, 5)) && Vpost::link_id()){
			
				try{
				
					foreach(VPost::link_id() as $id){
					
						$link = new Link();
						$link->_id = $id;
						$link->_priority = VPost::change_priority();
						$link->update('_priority', 'int');
						
						$this->_action_msg = ActionMessages::updated($link->_result_action);
					
					}
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
		
		/**
			* Delete links
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::delete(false) && VPost::link_id() && $this->_user['delete_content']){
			
				try{
				
					foreach(VPost::link_id() as $id){
					
						$link = new Link();
						$link->_id = $id;
						$link->delete();
						
						$this->_action_msg = ActionMessages::deleted($link->_result_action);
					
					}
					
					Session::monitor_activity('deleted '.count(VPost::link_id()).' link(s)');
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}elseif(VGet::action() == 'delete' && VGet::id() && $this->_user['delete_content']){
			
				try{
				
					$link = new Link();
					$link->_id = Vget::id();
					$link->delete();
					
					Session::monitor_activity('deleted a link');
					
					$this->_action_msg = ActionMessages::deleted($link->_result_action);
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}elseif((VPost::delete(false) || VGet::action() == 'delete') && $this->_user['delete_content'] === false){
			
				$this->_action_msg = ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>