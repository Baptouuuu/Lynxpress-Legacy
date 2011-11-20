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
	
	namespace Admin\ActionMessages;
	
	/**
		* ActionMessages
		*
		* Regroup methods to display action messages
		*
		* Messages are mainly returned in _action_msg attribute
		*
		* @package		Administration
		* @namespace	ActionMessages
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class ActionMessages{
	
		const MSG_W_B = '<div class="message well">';
		const MSG_E_B = '<div class="message error">';
		const MSG_E = '</div>';
		
		/**
			* Method that returns message if user don't have permission to access to an area
			*
			* @static
			* @access	public
			* @return	string Message if the user doesn't have the right to access an administration page
		*/
		
		public static function part_no_perm(){
		
			return self::MSG_E_B.'You can\'t manage this part!'.self::MSG_E;
		
		}
		
		/**
			* Method that returns message if user don't have permission to do an action
			*
			* @static
			* @access	public
			* @return	string Message if the user doesn't have the right to do a specific action, for example for deleting content
		*/
		
		public static function action_no_perm(){
		
			return self::MSG_E_B.'You don\'t have the permission to do this!'.self::MSG_E;
		
		}
		
		/**
			* Method that returns appropriated message on post creation event
			*
			* @static
			* @access	public
			* @param	mixed [$bool]
			* @return	string Message if the post has been created or not, or the message from a raisen exception
		*/
		
		public static function new_post_create($bool){
		
			if($bool === true)
				return self::MSG_W_B.'Article successfully created'.self::MSG_E;	
			elseif($bool === false)
				return self::MSG_E_B.'There\'s a problem creating your article!'.self::MSG_E;
			else
				return self::MSG_E_B.$bool.self::MSG_E;		//this case happen when an exception is raisen and return the error
		
		}
		
		/**
			* Method that returns appropriated message on post update event
			*
			* @static
			* @access	public
			* @param	mixed [$bool]
			* @return	string Message if the post has been updated or not, or the message from a raisen exception
		*/
		
		public static function post_update($bool){
		
			if($bool === true)
				return self::MSG_W_B.'Article successfully modified'.self::MSG_E;	
			elseif($bool === false)
				return self::MSG_E_B.'There\'s a problem editing your article!'.self::MSG_E;
			else
				return self::MSG_E_B.$bool.self::MSG_E;		//this case happen when an exception is raisen and return the error code
		
		}
		
		/**
			* Method that returns appropriated message on trashing event with number of items trashed
			*
			* @static
			* @access	public
			* @param	array [$array]
			* @return	string Message with the number of elements trashed
		*/
		
		public static function trashed($array){
			
			if(is_array($array)){
				
				$completed = 0;
				$aborted = 0;
				$msg = null;
				$ids = array();
				
				foreach($array as $key => $action_result){
				
					if($action_result === true){
				
						$completed++;
						array_push($ids, $key);
				
					}elseif($action_result === false){
				
						$aborted++;
				
					}
				
				}
				
				if(!empty($completed))
					$msg .= self::MSG_W_B.(($completed > 1)?"$completed items":'Item').' moved to trash. <a href="?action=untrash&id='.implode(',', $ids).'">Undo</a>'.self::MSG_E;
				
				if(!empty($msg) && !empty($aborted))
					$msg .= '<br/>';
					
				if(!empty($aborted))
					$msg .= self::MSG_E_B.(($aborted > 1)?"$aborted items":'One item').' not moved!'.self::MSG_E;
					
				return $msg;
		
			}else{
		
				return self::MSG_E_B.'Can\'t verify your request (not array)'.self::MSG_E;
		
			}
			
		}
		
		/**
			* Method that returns appropriated message on untrashing event with number of items untrashed
			*
			* @static
			* @access	public
			* @param	array [$array]
			* @return	string Message with the number of elements untrashed
		*/
		
		public static function untrashed($array){
		
			if(is_array($array)){
				
				$completed = 0;
				$aborted = 0;
				$msg = null;
				
				foreach($array as $action_result){
				
					if($action_result === true)
						$completed++;
					elseif($action_result === false)
						$aborted++;
					
				}
				
				if(!empty($completed))
					$msg .= self::MSG_W_B.(($completed > 1)?"$completed items":'Item').' restored from trash.'.self::MSG_E;
				
				if(!empty($msg) && !empty($aborted))
					$msg .= '<br/>';
					
				if(!empty($aborted))
					$msg .= self::MSG_E_B.(($aborted > 1)?"$aborted items":'One item').' not restored!'.self::MSG_E;
					
				return $msg;
			
			}else{
			
				return self::MSG_E_B.'Can\'t verify your request (not array)'.self::MSG_E;
			
			}
		
		}
		
		/**
			* Method that returns appropriated message on update event
			*
			* @static
			* @access	public
			* @param	mixed [$bool]
			* @return	string Message if elements has been updated or not, or the message from a raisen exception
		*/
		
		public static function updated($bool){
		
			if($bool === true)
				return self::MSG_W_B.'Item(s) updated.'.self::MSG_E;
			elseif($bool === false)
				return self::MSG_E_B.'Item(s) not updated!'.self::MSG_E;
			else
				return self::MSG_E_B.$bool.self::MSG_E;		//this case happen when an exception is raisen and return the error code
		
		}
		
		/**
			* Method that return appropriated message on deletion event
			*
			* @static
			* @access	public
			* @param	mixed [$bool]
			* @return	string Message if elements has been deleted or not, or the message from a raisen exception
		*/
		
		public static function deleted($bool){
		
			if($bool === true)
				return self::MSG_W_B.'Item(s) deleted permanently.'.self::MSG_E;
			elseif($bool === false)
				return self::MSG_E_B.'Item(s) not deleted!'.self::MSG_E;
			else
				return self::MSG_E_B.$bool.self::MSG_E;		//this case happen when an exception is raisen and return the error code
		
		}
		
		/**
			* Method that returns appropriated message when a comment is replied
			*
			* @static
			* @access	public
			* @param	mixed [$bool]
			* @return	string Message if a respond to a comment has been created or not, or the message from a raisen exception
		*/
		
		public static function reply_comment($bool){
		
			if($bool === true)
				return self::MSG_W_B.'Comment replied.'.self::MSG_E;
			elseif($bool === false)
				return self::MSG_E_B.'Not replied!'.self::MSG_E;
			else
				return self::MSG_E_B.$bool.self::MSG_E;		//this case happen when an exception is raisen and return the error code
		
		}
		
		/**
			* Method that returns appropriated message on comment update event with the number of comment updated
			*
			* @static
			* @access	public
			* @param	array [$array]
			* @return	string Message with the number of updated comments
		*/
		
		public static function update_comment($array){
		
			if(is_array($array)){
				
				$completed = 0;
				$aborted = 0;
				$msg = null;
				
				foreach($array as $action_result){
				
					if($action_result === true)
						$completed++;
					elseif($action_result === false)
						$aborted++;
					
				}
				
				if(!empty($completed))
					$msg .= self::MSG_W_B.(($completed > 1)?"$completed items":'Item').' modified.'.self::MSG_E;
				
				if(!empty($msg) && !empty($aborted))
					$msg .= '<br/>';
					
				if(!empty($aborted))
					$msg .= self::MSG_E_B.(($aborted > 1)?"$aborted items":'One item').' not modified!'.self::MSG_E;
					
				return $msg;
			
			}else{
			
				return self::MSG_E_B.'Can\'t verify your request (not array)'.self::MSG_E;
			
			}
		
		}
		
		/**
			* Method that returns appropriated message on profile update event
			*
			* @static
			* @access	public
			* @param	mixed [$bool]
			* @return	string Message if a profile has been updated or not, or the message from a raisen exception
		*/
		
		public static function profile_update($bool){
		
			if($bool === true)
				return self::MSG_W_B.'Profile updated'.self::MSG_E;
			elseif($bool === false)
				return self::MSG_E_B.'Profile update impossible!'.self::MSG_E;
			else
				return self::MSG_E_B.$bool.self::MSG_E;
		
		}
		
		/**
			* Method that returns appropriated message when administrator change user role with the number of modified ones
			*
			* @static
			* @access	public
			* @param	array [$array]
			* @return	string Message with the number of updated profiles
		*/
		
		public static function change_role($array){
		
			if(is_array($array)){
				
				$completed = 0;
				$aborted = 0;
				$msg = null;
				
				foreach($array as $action_result){
				
					if($action_result === true)
						$completed++;
					elseif($action_result === false)
						$aborted++;
					
				}
				
				if(!empty($completed))
					$msg .= self::MSG_W_B.(($completed > 1)?"$completed profiles":'Profile').' updated.'.self::MSG_E;
				
				if(!empty($msg) && !empty($aborted))
					$msg .= '<br/>';
					
				if(!empty($aborted))
					$msg .= self::MSG_E_B.(($aborted > 1)?"$aborted profiles":'One profile').' not updated!'.self::MSG_E;
					
				return $msg;
			
			}else{
			
				return self::MSG_E_B.'Can\'t verify your request (not array)'.self::MSG_E;
			
			}
		
		}
		
		/**
			* Method that returns appropriated message on profile deletion with the number on deleted ones
			*
			* @static
			* @access	public
			* @param	array [$array]
			* @return	string Message with the number of deleted profiles
		*/
		
		public static function delete_profile($array){
		
			if(is_array($array)){
				
				$completed = 0;
				$aborted = 0;
				$msg = null;
				
				foreach($array as $action_result){
				
					if($action_result === true)
						$completed++;
					elseif($action_result === false)
						$aborted++;
					
				}
				
				if(!empty($completed))
					$msg .= self::MSG_W_B.(($completed > 1)?"$completed profiles":'Profile').' deleted successfully.'.self::MSG_E;
				
				if(!empty($msg) && !empty($aborted))
					$msg .= '<br/>';
					
				if(!empty($aborted))
					$msg .= self::MSG_E_B.(($aborted > 1)?"$aborted profiles":'One profile').' deletion aborted!'.self::MSG_E;
					
				return $msg;
			
			}else{
			
				return self::MSG_E_B.'Can\'t verify your request (not array)'.self::MSG_E;
			
			}
			
		
		}
		
		/**
			* Generic method that returns message for item(s) creation
			*
			* @static
			* @access	public
			* @param	mixed [$bool]
			* @return	string Message if elements has been created or not, or the message from a raisen exception
		*/
		
		public static function created($bool){
		
			if($bool === true)
				return self::MSG_W_B.'Item(s) created'.self::MSG_E;
			elseif($bool === false)
				return self::MSG_E_B.'Item(s) not created!'.self::MSG_E;
			else
				return self::MSG_E_B.$bool.self::MSG_E;
		
		}
		
		/**
			* Returns message on template update
			*
			* @static
			* @access	public
			* @param	mixed [$bool]
			* @return	string Message if template has been updated or not, or the message from a raisen exception
		*/
		
		public static function template_updated($bool){
		
			if($bool === true)
				return self::MSG_W_B.'Template updated'.self::MSG_E;
			elseif($bool === false)
				return self::MSG_E_B.'Template not updated!'.self::MSG_E;
			else
				return self::MSG_E_B.$bool.self::MSG_E;
		
		}
		
		/**
			* Returns message on template deleteion
			*
			* @static
			* @access	public
			* @param	mixed [$bool]
			* @return	string Message if template has been deleted or not, or the message from a raisen exception
		*/
		
		public static function template_deleted($bool){
		
			if($bool === true)
				return self::MSG_W_B.'Template deleted'.self::MSG_E;
			elseif($bool === false)
				return self::MSG_E_B.'Template not deleted!'.self::MSG_E;
			else
				return self::MSG_E_B.$bool.self::MSG_E;
		
		}
		
		/**
			* Generic method that returns wanted message on success action
			*
			* @static
			* @access	public
			* @param	string [$msg]
			* @return	string Message with a style for a good action
		*/
		
		public static function custom_good($msg){
		
			return self::MSG_E_B.$msg.self::MSG_E;
		
		}
		
		/**
			* Generic method that returns wanted message on aborted action
			*
			* @static
			* @access	public
			* @param	string [$msg]
			* @return	string Message with a style for a wrong action
		*/
		
		public static function custom_wrong($msg){
		
			return self::MSG_E_B.$msg.self::MSG_E;
		
		}
	
	}

?>