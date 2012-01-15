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
	
	namespace Admin\Users;
	use \Admin\Html\Html as Master;
	
	/**
		* Html Users
		*
		* Contains all html for users classes
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Users
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html extends Master{
	
		/**
			* Display menu for new user page
			*
			* @static
			* @access	public
		*/
		
		public static function nu_menu(){
		
			echo '<div id="menu">'.
				 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
				 	'<span class="menu_item"><a href="index.php?ns=users&ctl=manage">Users</a></span>'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=users&ctl=add">Add</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display form to add a new user
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
			* @param	string [$username] Username
			* @param	string [$email] User email
			* @param	string [$firstname] User firstname
			* @param	string [$lastname] User lastname
			* @param	string [$website] User website
		*/
		
		public static function nu_form($part, $username = '', $email = '', $firstname = '', $lastname = '', $website = ''){
		
			if($part == 'o'){
			
				echo '<table class="form_table" cellspacing="0">'.
						'<tbody>'.
							'<tr>'.
								'<th>'.
									'<label for="username">Username <span class="indication">(required)</span></label>'.
								'</th>'.
								'<td>'.
									'<input id="username" class="user_input_text" name="username" type="text" value="'.$username.'" required />'.
								'</td>'.
							'</tr>'.
							'<tr>'.
								'<th>'.
									'<label for="email">E-mail <span class="indication">(required)</span></label>'.
								'</th>'.
								'<td>'.
									'<input id="email" class="user_input_text" name="email" type="email" value="'.$email.'" required />'.
								'</td>'.
							'</tr>'.
							'<tr>'.
								'<th>'.
									'<label for="firstname">First Name</label>'.
								'</th>'.
								'<td>'.
									'<input id="firstname" class="user_input_text" name="firstname" type="text" value="'.$firstname.'" />'.
								'</td>'.
							'</tr>'.
							'<tr>'.
								'<th>'.
									'<label for="lastname">Last Name</label>'.
								'</th>'.
								'<td>'.
									'<input id="lastname" class="user_input_text" name="lastname" type="text" value="'.$lastname.'" />'.
								'</td>'.
							'</tr>'.
							'<tr>'.
								'<th>'.
									'<label for="website">Website</label>'.
								'</th>'.
								'<td>'.
									'<input id="website" class="user_input_text" name="website" type="url" value="'.$website.'" />'.
								'</td>'.
							'</tr>'.
							'<tr>'.
								'<th>'.
									'<label for="pwd">Password <span class="indication">(twice, required)</span></label>'.
								'</th>'.
								'<td>'.
									'<input id="pwd" class="user_input_text" name="pwd" type="password" required /><br/>'.
									'<input name="re_pwd" class="user_input_text" type="password" required />'.
								'</td>'.
							'</tr>'.
							'<tr>'.
								'<th>'.
									'<label for="send_pwd">Send Password?</label>'.
								'</th>'.
								'<td>'.
									'<input id="send_pwd" name="send_pwd" type="checkbox" value="yes" /> Send this password to the new user by email.'.
								'</td>'.
							'</tr>'.
							'<tr>'.
								'<th>'.
									'<label for="role">Role</label>'.
								'</th>'.
								'<td>'.
									'<select id="role" name="role">';
			
			}elseif($part == 'c'){
			
				echo				'</select>'.
								'</td>'.
							'</tr>'.
						'</tbody>'.
					 '</table>'.
					 '<input class="submit button button_publish" type="submit" name="new_user" value="Add New User" />';
			
			}
		
		}
		
		/**
			* Display an option in a dropdown
			*
			* @static
			* @access	public
			* @param	mixed [$key]
			* @param	mixed [$value]
			* @param	array [$array]
		*/
		
		public static function option($key, $value, $array = array()){
		
			echo '<option value="'.$key.'" '.((in_array($key, $array))?'selected':'').'>'.$value.'</option>';
		
		}
		
		/**
			* Display menu for profile page
			*
			* @static
			* @access	public
			* @param	boolean [$edit]
		*/
		
		public static function up_menu($edit = false){
		
			if($edit){
			
				echo '<div id="menu">'.
						 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
						 	'<span class="menu_item"><a href="index.php?ns=users&ctl=manage">Users</a></span>'.
						 	'<span id="menu_selected" class="menu_item">Editing</span>'.
						 	'<span class="menu_item"><a href="index.php?ns=users&ctl=add">Add</a></span>'.
						 '</div>';
			
			}else{
			
				echo '<div id="menu">'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=users&ctl=profile">Profile</a></span>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display name informations in user profile
			*
			* @static
			* @access	public
			* @param	string [$username] User username
			* @param	strind [$firstname] User firstname
			* @param	string [$lastname] User lastname
			* @param	string [$nickname] User nickname
			* @param	string [$publicname] User public name
			* @param	string [$role] User role
		*/
		
		public static function up_name($username, $firstname, $lastname, $nickname, $publicname, $role){
		
			echo '<h3>Name</h3>'.
				 '<table class="form_table">'.
				 	'<tbody>'.
				 		'<tr>'.
				 			'<th>'.
				 				'Username'.
				 			'</th>'.
				 			'<td>'.
				 				'<input class="user_input_text" type="text" value="'.$username.'" disabled />'.
				 				'<input name="username" type="hidden" value="'.$username.'" />'.
				 			'</td>'.
				 		'</tr>'.
				 		'<tr>'.
				 			'<th>'.
				 				'<label for="firstname">First Name</label>'.
				 			'</th>'.
				 			'<td>'.
				 				'<input id="firstname" class="user_input_text" name="firstname" type="text" value="'.$firstname.'" />'.
				 			'</td>'.
				 		'</tr>'.
				 		'<tr>'.
				 			'<th>'.
				 				'<label for="lastname">Last Name</label>'.
				 			'</th>'.
				 			'<td>'.
				 				'<input id="lastname" class="user_input_text" name="lastname" type="text" value="'.$lastname.'" />'.
				 			'</td>'.
				 		'</tr>'.
				 		'<tr>'.
				 			'<th>'.
				 				'<label for="nickname">Nickname <span class="indication">(required)</span></label>'.
				 			'</th>'.
				 			'<td>'.
				 				'<input id="nickname" class="user_input_text" name="nickname" type="text" value="'.$nickname.'" required />'.
				 			'</td>'.
				 		'</tr>'.
				 		'<tr>'.
				 			'<th>'.
				 				'<label for="public_name">Display name publicly as</label>'.
				 			'</th>'.
				 			'<td>'.
				 				$publicname.
							'</td>'.
						'</tr>'.
						$role.
				 	'</tbody>'.
				 '</table>';
		
		}
		
		/**
			* Display avatar selection in user profile
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
			* @param	string [$link] Path to user avatar
		*/
		
		public static function up_avatar($part, $link = ''){
		
			if($part == 'o'){
			
				echo '<h3>Avatar</h3>'.
					 '<table class="form_table">'.
					 	'<tbody>'.
					 		'<tr>'.
					 			'<th>'.
					 				'<img src="'.PATH.((!empty($link))?$link:'images/user.png').'" alt="avatar" />'.
					 			'</th>'.
					 			'<td>'.
					 				'<section id="avatars">'.
					 					'<figure class="avatar">'.
				 						 	'<label for="fig0"><img src="'.PATH.'images/user.png" /></label>'.
				 						 	'<figcaption>'.
				 						 		'<input id="fig0" type="radio" name="avatar" value="0" />'.
				 						 	'</figcaption>'.
				 						 '</figure>';
			
			}elseif($part == 'c'){
					 				
				echo 				'</section>'.
								'</td>'.
					 		'</tr>'.
					 	'</tbody>'.
					 '</table>';
			
			}
		
		}
		
		/**
			* Display a figure for avatar selection
			*
			* @static
			* @access	public
			* @param	integer [$id] Image id
			* @param	string [$permalink] Image path
			* @param	string [$current] Current image chosen by the user
		*/
		
		public static function up_fig($id, $permalink, $current){
		
			echo '<figure class="avatar">'.
				 	'<label for="fig'.$id.'"><img src="'.PATH.$permalink.'" /></label>'.
				 	'<figcaption>'.
				 		'<input id="fig'.$id.'" type="radio" name="avatar" value="'.$id.'" '.(($permalink == $current)?'checked':'').' />'.
				 	'</figcaption>'.
				 '</figure>';
		
		}
		
		/**
			* Display contact informations in user profile
			*
			* @static
			* @access	public
			* @param	string [$email] User email
			* @param	string [$website] User website
			* @param	string [$msn] User msn address
			* @param	string [$twitter] User twitter url
			* @param	string [$facebook] User facebook url
			* @param	string [$google] User google+ url
		*/
		
		public static function up_contact($email, $website, $msn, $twitter, $facebook, $google){
		
			echo '<h3>Contact Info</h3>'.
			 	 '<table class="form_table">'.
			 		'<tbody>'.
			 			'<tr>'.
			 				'<th>'.
			 					'<label for="email">E-mail <span class="indication">(required)</span></label>'.
			 				'</th>'.
			 				'<td>'.
			 					'<input id="email" class="user_input_text" name="email" type="email" value="'.$email.'" required />'.
			 				'</td>'.
			 			'</tr>'.
			 			'<tr>'.
			 				'<th>'.
			 					'<label for="website">Website</label>'.
			 				'</th>'.
			 				'<td>'.
			 					'<input id="website" class="user_input_text" name="website" type="url" value="'.$website.'" />'.
			 				'</td>'.
			 			'</tr>'.
			 			'<tr>'.
			 				'<th>'.
			 					'<label for="msn">MSN</label>'.
			 				'</th>'.
			 				'<td>'.
			 					'<input id="msn" class="user_input_text" name="msn" type="email" value="'.$msn.'" />'.
			 				'</td>'.
			 			'</tr>'.
			 			'<tr>'.
			 				'<th>'.
			 					'<label for="twitter">Twitter</label>'.
			 				'</th>'.
			 				'<td>'.
			 					'<input id="twitter" class="user_input_text" name="twitter" type="url" value="'.$twitter.'" />'.
			 				'</td>'.
			 			'</tr>'.
			 			'<tr>'.
			 				'<th>'.
			 					'<label for="fb">Facebook</label>'.
			 				'</th>'.
			 				'<td>'.
			 					'<input id="fb" class="user_input_text" name="fb" type="url" value="'.$facebook.'" />'.
			 				'</td>'.
			 			'</tr>'.
			 			'<tr>'.
			 				'<th>'.
			 					'<label for="google">Google+</label> '.
			 					'<span class="indication">'.
			 						'(<a href="https://spreadsheets.google.com/spreadsheet/viewform?formkey=dHdCLVRwcTlvOWFKQXhNbEgtbE10QVE6MQ&ndplr=1" title="Inform Google that you are an author for this website" target="_blank">submit authentication</a>)'.
			 					'</span>'.
			 				'</th>'.
			 				'<td>'.
			 					'<input id="google" class="user_input_text" name="google" type="url" value="'.$google.'" />'.
			 				'</td>'.
			 			'</tr>'.
			 		'</tbody>'.
			 	 '</table>';
		
		}
		
		/**
			* Display about informations in user profile
			*
			* @static
			* @access	public
			* @param	string [$bio] User description
		*/
		
		public static function up_about($bio){
		
			echo '<h3>About the user</h3>'.
				 	'<table class="form_table">'.
				 		'<tbody>'.
				 			'<tr>'.
				 				'<th>'.
				 					'<label for="bio">Biographical Info</label>'.
				 				'</th>'.
				 				'<td>'.
				 					'<textarea id="bio" class="base_txta" name="bio" rows="6" cols="30" wrap="soft">'.$bio.'</textarea><br/>'.
				 					'<span class="indication">Share a bit about yourself. This may be shown publicly.</span>'.
				 				'</td>'.
				 			'</tr>'.
								'<tr>'.
									'<th>'.
										'<label for="new_pwd">New Password</label>'.
									'</th>'.
									'<td>'.
										'<input id="new_pwd" class="user_input_text new_pwd" name="new_pwd" type="password" /> <span class="indication">If you would like to change the password type a new one. Otherwise leave this blank.</span><br/>'.
										'<input class="user_input_text new_pwd" name="re_new_pwd" type="password" /> <span class="indication">Type your new password again</span>'.
									'</td>'.
								'</tr>'.
							'</tbody>'.
						'</table>';
		
		}
		
		/**
			* Return an option in a dropdown for user profile
			*
			* @static
			* @access	public
			* @param	string [$cmp1]
			* @param	string [$cmp2]
		*/
		
		public static function up_option($cmp1, $cmp2){
		
			return '<option '.(($cmp1 == $cmp2)?'selected':'').'>'.$cmp2.'</option>';
		
		}
		
		/**
			* Display update button
			*
			* @static
			* @access	public
		*/
		
		public static function up_update(){
		
			echo '<input class="button button_publish submit" type="submit" name="update_profile" value="Update Profile" />';
		
		}
		
		/**
			* Display menu for manage users page
			*
			* @static
			* @access	public
			* @param	boolean [$bool]
		*/
		
		public static function mu_menu($bool = false){
		
			if($bool){
			
				echo '<div id="menu">'.
					 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=users&ctl=manage">Users</a></span>'.
					 	'<span class="menu_item"><a href="index.php?ns=users&ctl=add">Add</a></span>'.
					 '</div>';
			
			}else{
			
				echo '<div id="menu">'.
					 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display role selection menu
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
		*/
		
		public static function mu_role_menu($part){
		
			if($part == 'o'){
			
				echo '<div id="select_post_status">';
			
			}elseif($part == 'c'){
			
				echo '<span id="search"><input id="search_input" type="text" name="search" placeholder="Search" list="titles" />'.
					'<input class="button" type="submit" name="search_button" value="Search Users" /></span>'.
				'</div>';
			
			}
		
		}
		
		/**
			* Display a link in role selection menu
			*
			* @static
			* @access	public
			* @param	string [$role]
			* @param	integer [$count]
			* @param	boolean [$current]
		*/
		
		public static function mu_role_menu_link($role, $count, $current){
		
			echo '<a href="index.php?ns=users&ctl=manage&role='.$role.'">'.(($current)?'<span class="a_selected">':'').ucfirst($role).(($current)?'</span>':'').'</a> ('.$count.') | ';
		
		}
		
		/**
			* Display delete button
			*
			* @static
			* @access	public
		*/
		
		public static function mu_delete(){
		
			echo '<input class="button" type="submit" name="delete" value="Delete" />&nbsp;';
		
		}
		
		/**
			* Display actions bar for manage users page
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
			* @param	string [$role] Role currently viewed
		*/
		
		public static function mu_actions($part, $role = ''){
		
			if($part == 'o'){
			
				self::mu_delete();
				echo '<select name="change_role">'.
						'<option value="no">Change role to...</option>';
			
			}elseif($part == 'c'){
			
				echo '</select> '.
					 '<input class="button" type="submit" name="change" value="Change" />'.
					 '<input type="hidden" name="role" value="'.$role.'" />';
			
			}
		
		}
		
		/**
			* Display a user label
			*
			* @static
			* @access	public
			* @param	integer [$id] User id
			* @param	string [$edit] Url to edit user
			* @param	string [$username] User username
			* @param	string [$publicname] User public name
			* @param	string [$role] User role
			* @param	string [$email] User email
			* @param	string [$avatar] User avatar path
		*/
		
		public static function label($id, $edit, $username, $publicname, $role, $email, $avatar){
		
			echo '<div class="user_label">'.
					'<label for="user_'.$id.'">'.
					 	'<div class="check_label">'.
					 		'<input id="user_'.$id.'" type="checkbox" name="user_id[]" value="'.$id.'" />'.
					 	'</div>'.
					'</label>'.
				 	'<div class="content_label">'.
					 	'<div class="user_avatar">'.
					 		'<a href="index.php?ns=users&ctl=profile'.$edit.'" title="Edit"><img src="'.PATH.((!empty($avatar))?$avatar:'images/user.png').'" alt="Edit" /></a>'.
					 	'</div>'.
					 	'<div class="user_info_side">'.
					 		'Username: <span class="luname">'.$username.'</span><br/>'.
						 	'Public Name: <span class="lpname">'.ucwords(strtolower($publicname)).'</span><br/>'.
						 	'Role: <span class="lrole">'.ucfirst($role).'</span><br/>'.
						 	'<a href="mailto:'.$email.'" title="Send mail to '.$username.'">e-mail</a>'.
						'</div>'.
					'</div>'.
				 '</div>';
		
		}
	
	}

?>