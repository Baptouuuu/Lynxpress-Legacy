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
		* @version		1.1
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
		
			return self::custom_wrong('You can\'t manage this part!');
		
		}
		
		/**
			* Method that returns message if user don't have permission to do an action
			*
			* @static
			* @access	public
			* @return	string Message if the user doesn't have the right to do a specific action, for example for deleting content
		*/
		
		public static function action_no_perm(){
		
			return self::custom_wrong('You don\'t have the permission to do this!');
		
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
				return self::custom_good('Article successfully created');	
			elseif($bool === false)
				return self::custom_wrong('There\'s a problem creating your article!');
			else
				return self::custom_wrong($bool);		//this case happen when an exception is raisen and return the error
		
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
				return self::custom_good('Article successfully modified');	
			elseif($bool === false)
				return self::custom_wrong('There\'s a problem editing your article!');
			else
				return self::custom_wrong($bool);		//this case happen when an exception is raisen and return the error code
		
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
					$msg .= self::custom_good((($completed > 1)?"$completed items":'Item').' moved to trash. <a href="index.php?ns=posts&ctl=manage&action=untrash&id='.implode(',', $ids).'">Undo</a>');
				
				if(!empty($msg) && !empty($aborted))
					$msg .= '<br/>';
					
				if(!empty($aborted))
					$msg .= self::custom_wrong((($aborted > 1)?"$aborted items":'One item').' not moved!');
					
				return $msg;
		
			}else{
		
				return self::custom_wrong('Can\'t verify your request (not array)');
		
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
					$msg .= self::custom_good((($completed > 1)?"$completed items":'Item').' restored from trash.');
				
				if(!empty($msg) && !empty($aborted))
					$msg .= '<br/>';
					
				if(!empty($aborted))
					$msg .= self::custom_wrong((($aborted > 1)?"$aborted items":'One item').' not restored!');
					
				return $msg;
			
			}else{
			
				return self::custom_wrong('Can\'t verify your request (not array)');
			
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
				return self::custom_good('Item(s) updated.');
			elseif($bool === false)
				return self::custom_wrong('Item(s) not updated!');
			else
				return self::custom_wrong($bool);		//this case happen when an exception is raisen and return the error code
		
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
				return self::custom_good('Item(s) deleted permanently.');
			elseif($bool === false)
				return self::custom_wrong('Item(s) not deleted!');
			else
				return self::custom_wrong($bool);		//this case happen when an exception is raisen and return the error code
		
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
				return self::custom_good('Comment replied.');
			elseif($bool === false)
				return self::custom_wrong('Not replied!');
			else
				return self::custom_wrong($bool);		//this case happen when an exception is raisen and return the error code
		
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
					$msg .= self::custom_good((($completed > 1)?"$completed items":'Item').' modified.');
				
				if(!empty($msg) && !empty($aborted))
					$msg .= '<br/>';
					
				if(!empty($aborted))
					$msg .= self::custom_wrong((($aborted > 1)?"$aborted items":'One item').' not modified!');
					
				return $msg;
			
			}else{
			
				return self::custom_wrong('Can\'t verify your request (not array)');
			
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
				return self::custom_good('Profile updated');
			elseif($bool === false)
				return self::custom_wrong('Profile update impossible!');
			else
				return self::custom_wrong($bool);
		
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
					$msg .= self::custom_good((($completed > 1)?"$completed profiles":'Profile').' updated.');
				
				if(!empty($msg) && !empty($aborted))
					$msg .= '<br/>';
					
				if(!empty($aborted))
					$msg .= self::custom_wrong((($aborted > 1)?"$aborted profiles":'One profile').' not updated!');
					
				return $msg;
			
			}else{
			
				return self::custom_wrong('Can\'t verify your request (not array)');
			
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
					$msg .= self::custom_good((($completed > 1)?"$completed profiles":'Profile').' deleted successfully.');
				
				if(!empty($msg) && !empty($aborted))
					$msg .= '<br/>';
					
				if(!empty($aborted))
					$msg .= self::custom_wrong((($aborted > 1)?"$aborted profiles":'One profile').' deletion aborted!');
					
				return $msg;
			
			}else{
			
				return self::custom_wrong('Can\'t verify your request (not array)');
			
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
				return self::custom_good('Item(s) created');
			elseif($bool === false)
				return self::custom_wrong('Item(s) not created!');
			else
				return self::custom_wrong($bool);
		
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
				return self::custom_good('Template updated');
			elseif($bool === false)
				return self::custom_wrong('Template not updated!');
			else
				return self::custom_wrong($bool);
		
		}
		
		/**
			* Returns message on template deletion
			*
			* @static
			* @access	public
			* @param	mixed [$bool]
			* @return	string Message if template has been deleted or not, or the message from a raisen exception
		*/
		
		public static function template_deleted($bool){
		
			if($bool === true)
				return self::custom_good('Template deleted');
			elseif($bool === false)
				return self::custom_wrong('Template not deleted!');
			else
				return self::custom_wrong($bool);
		
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
		
			return self::MSG_W_B.$msg.self::MSG_E;
		
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
		
		/**
			* Returns message on website update check
			*
			* @static
			* @access	public
			* @param	boolean [$bool]
			* @param	boolean [$link] Display a link to update page
			* @return	string Message saying if there's an update
		*/
		
		public static function ws_update_check($bool, $link = false){
		
			$msg = '';
			
			if($link === true)
				$msg = ' <a href="index.php?ns=update&ctl=manage" title="Follow this link to update your Lynxpress">Update</a>';
			
			if($bool === true)
				return self::custom_good('An update is available.'.$msg);
			elseif($bool === false)
				return self::custom_wrong('No update available!');
		
		}
		
		/**
			* Returns message if wether or not the website has been updated
			*
			* @static
			* @access	public
			* @param	mixed [$value]
			* @return	string
		*/
		
		public static function ws_update($value){
		
			if($value === true)
				return self::custom_good('Lynxpress has been updated');
			else
				return self::custom_wrong($value);
		
		}
		
		/**
			* Returns a message when user preferences are updated
			*
			* @static
			* @access	public
			* @param	mixed [$bool]
			* @return	string
		*/
		
		public static function pref_updated($bool){
		
			if($bool === true)
				return self::custom_good('Preferences updated');
			else
				return self::custom_wrong($bool);
		
		}
	
	}

?>