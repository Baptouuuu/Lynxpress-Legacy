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
	
	namespace Admin\Categories;
	use \Admin\Master\Master as Master;
	use Exception;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Library\Models\Category as Category;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Get as VGet;
	use \Admin\Session\Session as Session;
	
	/**
		* Manage Categories
		*
		* Handle display and categories administration
		*
		* @package		Administration
		* @subpackage	Controllers
		* @namespace	Categories
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master{
	
		private $_content = null;
	
		/**
			* Class constructor
			*
			* @access	public
		*/
			
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Categories';
			
			if($this->_user['settings']){
			
				$this->create();
				$this->delete();
			
				$this->get_cats();
			
			}
		
		}
		
		/**
			* Retrieve all categories from the database
			*
			* @access	private
		*/
		
		private function get_cats(){
		
			try{
			
				$to_read['table'] = 'category';
				$to_read['columns'] = array('CATEGORY_ID');
				$to_read['order'] = array('category_type', 'ASC');
				$this->_content = $this->_db->read($to_read);
				
				if(!empty($this->_content))
					foreach($this->_content as &$cat)
						$cat = new Category($cat['CATEGORY_ID']);
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMesaage());
			
			}
		
		}
		
		/**
			* Display related admin parts link
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_user['settings'])
				Html::menu(true);
			else
				Html::menu(false);
		
		}
		
		/**
			* Display inputs that permits to add a new category
			*
			* @access	private
		*/
		
		private function display_add(){
		
			Html::add();
		
		}
		
		/**
			* Display actions button that can be applicable on categories
			*
			* @access	private
		*/
		
		private function display_actions(){
		
			Html::delete_button();
		
		}
		
		/**
			* Display a html table with all categories
			*
			* @access	private
		*/
		
		private function display_table(){
		
			Html::table('o');
			
			if(!empty($this->_content)){
			
				foreach($this->_content as $cat)
					Html::table_row($cat->_id, $cat->_name, $cat->_type);
			
			}else{
			
				echo '<tr colspan="3">'.'There\'s no category yet</tr>';
			
			}
			
			Html::table('c');
		
		}
		
		/**
			* Method that display the page
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			if($this->_user['settings']){
			
				echo $this->_action_msg;
			
				Html::form('o', 'post', 'index.php?ns=categories&ctl=manage');
				
				$this->display_add();
				$this->display_actions();
				$this->display_table();
				$this->display_actions();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Check if a category is used
			*
			* @access	private
			* @param	integer [$id] Category id to check
			* @param	string [$type] Category type to check
			* @return	boolean If the category is used the method return true, instead it returns false
		*/
		
		private function check_usage($id, $type){
		
			if($type == 'post'){
			
				$to_read['table'] = 'post';
				$to_read['columns'] = array('POST_ID');
				$to_read['condition_columns'][':c'] = 'post_category';
				$to_read['condition_select_types'][':c'] = 'LIKE';
				$to_read['condition_values'][':c'] = '%'.$id.'%';
				$to_read['value_types'][':c'] = 'str';
			
			}elseif($type == 'album'){
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('MEDIA_ID');
				$to_read['condition_columns'][':c'] = 'media_category';
				$to_read['condition_select_types'][':c'] = 'LIKE';
				$to_read['condition_values'][':c'] = '%'.$id.'%';
				$to_read['value_types'][':c'] = 'str';
				$to_read['condition_types'][':t'] = 'AND';
				$to_read['condition_columns'][':t'] = 'media_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'album';
				$to_read['value_types'][':t'] = 'str';
			
			}elseif($type == 'video'){
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('MEDIA_ID');
				$to_read['condition_columns'][':c'] = 'media_category';
				$to_read['condition_select_types'][':c'] = 'LIKE';
				$to_read['condition_values'][':c'] = '%'.$id.'%';
				$to_read['value_types'][':c'] = 'str';
				$to_read['condition_types'][':t'] = 'AND';
				$to_read['condition_columns'][':t'] = 'media_type';
				$to_read['condition_select_types'][':t'] = 'LIKE';
				$to_read['condition_values'][':t'] = 'video%';
				$to_read['value_types'][':t'] = 'str';
			
			}

			$content = $this->_db->read($to_read);
			
			if(count($content) == 0)
				return false;
			else
				return true;
		
		}
		
		/**
			* Check if there are at least one category per type
			*
			* if not we create a new one named uncategorized
			*
			* @access	private
			* @param	string [$type] Category type to check
		*/
		
		private function check_empty($type){
		
			$to_read['table'] = 'category';
			$to_read['columns'] = array('CATEGORY_ID');
			$to_read['condition_columns'][':t'] = 'category_type';
			$to_read['condition_select_types'][':t'] = '=';
			$to_read['condition_values'][':t'] = $type;
			$to_read['value_types'][':t'] = 'str';
			
			$cat = $this->_db->read($to_read);
			
			if(empty($cat)){
			
				try{
				
					$cat = new Category();
					$cat->_name = 'uncategorized';
					$cat->_type = $type;
					$cat->create();
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
		
		/**
			* Method that permits to create a new category
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::add_cat(false) && VPost::name() && VPost::type() != 'no'){
			
				try{
			
					$cat = new Category();
					$cat->_name = VPost::name();
					$cat->_type = VPost::type();
					$cat->create();
					
					Session::monitor_activity('created a new category: '.$cat->_name);
					
					$result = true;
					
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::created($result);
			
			}elseif(VPost::add_cat(false) && (!VPost::name() || VPost::type() == 'no')){
			
				$this->_action_msg = ActionMessages::custom_wrong('Make sure you\'ve filled all inputs!');
			
			}
		
		}
		
		/**
			* Method that permits to delete one or more categories
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::delete(false) && $this->_user['delete_content']){
			
				if(VPost::category_id()){
				
					try{
					
						foreach(VPost::category_id() as $id){
						
							$cat = new Category();
							$cat->_id = $id;
							$cat->read('_name');
							$cat->read('_type');
							$type = $cat->_type;
							
							if($this->check_usage($id, $type))
								throw new Exception('Can\'t delete '.$cat->_name.' because it\'s used!');
							
							$cat->delete();
							
							$this->check_empty($type);
						
						}
						
						Session::monitor_activity('deleted '.count(VPost::category_id()).' category(ies)');
						
						$result = true;
					
					}catch(Exception $e){
					
						$result = $e->getMessage();
					
					}
					
					$this->_action_msg = ActionMessages::deleted($result);
				
				}
			
			}elseif(VGet::action() == 'delete' && VGet::id()){
			
				try{
				
					$cat = new Category();
					$cat->_id = VGet::id();
					$cat->read('_name');
					$cat->read('_type');
					$type = $cat->_type;
					
					if($this->check_usage(VGet::id(), $type))
						throw new Exception('Can\'t delete '.ucwords($cat->_name).' because it\'s used!');
					
					$cat->delete();
					
					$this->check_empty($type);
					
					Session::monitor_activity('deleted a category');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::deleted($result);
			
			}elseif((VPost::delete(false) || (VGet::action() == 'delete' && VGet::id())) && !$this->_user['delete_content']){
			
				$this->_action_msg = ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>