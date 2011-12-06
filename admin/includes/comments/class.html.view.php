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
	
	namespace Admin\Comments;
	use \Admin\Html\Html as Master;
	
	/**
		* Html Comments
		*
		* Contains all html for comments class
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Comments
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html extends Master{
	
		/**
			* Display menu for comments page
			*
			* @static
			* @access	public
			* @param	string [$title]
		*/
		
		public static function menu($title){
		
			echo '<div id="menu">'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=comments&ctl=manage">'.$title.'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display status selection menu
			*
			* @static
			* @access	public
			* @param	string [$pending]
			* @param	integer [$count_pending] Number of pending comments
			* @param	string [$approved]
			* @param	integer [$count_approved] Number of approved comments
			* @param	string [$spam]
			* @param	integer [$count_spam] Number of comments marked as spam
			* @param	string [$trash]
			* @param	integer [$count_trash] Number of trashed comments
		*/
		
		public static function status_menu($pending, $count_pending, $approved, $count_approved, $spam, $count_spam, $trash, $count_trash){
		
			echo '<div id="select_post_status">'.
					'<a href="index.php?ns=comments&ctl=manage">'.$pending.'</a> ('.$count_pending.') | '.
					'<a href="index.php?ns=comments&ctl=manage&comment_status=approved">'.$approved.'</a> ('.$count_approved.') | '.
					'<a href="index.php?ns=comments&ctl=manage&comment_status=spam">'.$spam.'</a> ('.$count_spam.') | '.
					'<a href="index.php?ns=comments&ctl=manage&comment_status=trash">'.$trash.'</a> ('.$count_trash.')'.
					'<span id="search"><input id="search_input" type="text" name="search" placeholder="Search" list="names" />'.
					'<input class="button" type="submit" name="search_button" value="Search Comments" /></span>'.
				'</div>';
		
		}
		
		/**
			* Display select action dropdown
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
		*/
		
		public static function select_action($part){
		
			if($part == 'o')
				echo '<select name="action"><option>Actions...</option>';
			elseif($part == 'c')
				echo '</select>';
		
		}
		
		/**
			* Display approve action in actions dropdown
			* 
			* @static
			* @access	public
		*/
		
		public static function opt_approve(){
		
			echo '<option value="approve">Approve</option>';
		
		}
		
		/**
			* Display unapprove action in actions dropdown
			*
			* @static
			* @access	public
		*/
		
		public static function opt_unapprove(){
		
			echo '<option value="unapprove">Unapprove</option>';
		
		}
		
		/**
			* Display spam action in actions dropdown
			*
			* @static
			* @access	public
		*/
		
		public static function opt_spam(){
		
			echo '<option value="spam">Mark as Spam</option>';
		
		}
		
		/**
			* Display unspam action in actions dropdown
			*
			* @static
			* @access	public
		*/
		
		public static function opt_unspam(){
		
			echo '<option value="unspam">Not Spam</option>';
		
		}
		
		/**
			* Display trash action in actions dropdown
			*
			* @static
			* @access	public
		*/
		
		public static function opt_trash(){
		
			echo'<option value="trash">Move to Trash</option>';
		
		}
		
		/**
			* Display restore action in actions dropdown
			*
			* @static
			* @access	public
		*/
		
		public static function opt_restore(){
		
			echo '<option value="restore">Restore</option>';
		
		}
		
		/**
			* Display delete action in actions dropdown
			*
			* @static
			* @access	public
		*/
		
		public static function opt_delete(){
		
			echo '<option value="delete">Delete Permanently</option>';
		
		}
		
		/**
			* Display apply button
			*
			* @static
			* @access	public
		*/
		
		public static function apply(){
		
			echo '<input class="button" type="submit" name="apply_action" value="Apply" />';
		
		}
		
		/**
			* Display empty button
			*
			* @static
			* @access	public
			* @param	string [$value] Name displayed for the user
		*/
		
		public static function b_empty($value){
		
			echo '<input class="button" type="submit" name="empty" value="'.$value.'" />';
		
		}
		
		/**
			* Display hidden input containing the comment status
			*
			* @static
			* @access	public
			* @param	string [$status] Comments status currently viewed
		*/
		
		public static function status($status){
		
			echo '<input type="hidden" name="comment_status" value="'.$status.'" />';
		
		}
		
		/**
			* Display comments table
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
		*/
		
		public static function table($part){
		
			if($part == 'o'){
			
				echo '<table id="table">'.
						'<thead>'.
							'<tr>'.
								'<th class="column_checkbox" scope="col"><input type="checkbox" name="comments[]" /></th>'.
								'<th class="column_author">Author</th>'.
								'<th class="column_comment">Comment</th>'.
								'<th class="column_response">In Response To</th>'.
							'</tr>'.
						'</thead>'.
						'<tfoot>'.
							'<tr>'.
								'<th class="column_checkbox" scope="col"><input type="checkbox" name="comments[]" /></th>'.
								'<th class="column_author">Author</th>'.
								'<th class="column_comment">Comment</th>'.
								'<th class="column_response">In Response To</th>'.
							'</tr>'.
						'</tfoot>'.
						'<tbody>';
			
			}elseif($part == 'c'){
			
				echo 	'</tbody>'.
					 '</table>';
			
			}
		
		}
		
		/**
			* Display approve action link
			*
			* @static
			* @access	public
			* @param	integer [$id] Comment id to approve
			* @param	string [$status] Comments status currently viewed
			* @return	string Hyperlink to approve a comment
		*/
		
		public static function h_approve($id, $status){
		
			return '<a class="green" href="index.php?ns=comments&ctl=manage&action=approve&comment_id='.$id.'&comment_status='.$status.'">Approve</a> | ';
		
		}
		
		/**
			* Display unapprove action link
			*
			* @static
			* @access	public
			* @param	integer [$id] Comment id to unapprove
			* @param	string [$status] Comments status currently viewed
			* @return	string Hyperlink to unapprove a comment
		*/
		
		public static function h_unapprove($id, $status){
		
			return '<a class="orange" href="index.php?ns=comments&ctl=manage&action=unapprove&comment_id='.$id.'&comment_status='.$status.'">Unapprove</a> | ';
		
		}
		
		/**
			* Display link to reply to a comment
			*
			* @static
			* @access	public
			* @param	integer [$id] Comment id to reply to
			* @param	string [$name] Name of the person to reply to
			* @param	integer [$rel_id] Post id or Media id
			* @param	string [$rel_type] Can be "post" or "media"
			* @return	string Hyperlink to go to the form to reply to a comment
		*/
		
		public static function h_reply($id, $name, $rel_id, $rel_type){
		
			return '<a href="index.php?ns=comments&ctl=manage&action=reply&comment_name='.$name.'&comment_id='.$id.'&id='.$rel_id.'&type='.$rel_type.'">Reply</a> | ';
		
		}
		
		/**
			* Display link to edit a comment
			*
			* @static
			* @access	public
			* @param	integer [$id] Comment id
			* @param	string [$status] Comment status
			* @return	string Hyperlink to go to the form to update the comment
		*/
		
		public static function h_edit($id, $status){
		
			return '<a href="index.php?ns=comments&ctl=manage&action=edit&comment_id='.$id.'&comment_status='.$status.'">Edit</a> | ';
		
		}
		
		/**
			* Display spam action link
			*
			* @static
			* @access	public
			* @param	integer [$id] Comment id to mark as spam
			* @param	string [$status] Comments status currently viewed
			* @return	string Hyperlink to mark as spam a comment
		*/
		
		public static function h_spam($id, $status){
		
			return '<a class="orange" href="index.php?ns=comments&ctl=manage&action=spam&comment_id='.$id.'&comment_status='.$status.'">Spam</a> | ';
		
		}
		
		/**
			* Display unspam action link
			*
			* @static
			* @access	public
			* @param	integer [$id] Comment id to unspam
			* @param	string [$status] Comments status currently viewed
			* @return	string Hyperlink to unspam a comment
		*/
		
		public static function h_unspam($id, $status){
		
			return '<a class="orange" href="index.php?ns=comments&ctl=manage&action=unspam&comment_id='.$id.'&comment_status='.$status.'">Not Spam</a> | ';			
		
		}
		
		/**
			* Display trash action link
			*
			* @static
			* @access	public
			* @param	integer [$id] Comment id to trash
			* @param	string [$status] Comments status currently viewed
			* @return	string Hyperlink to trash a comment
		*/
		
		public static function h_trash($id, $status){
		
			return '<a class="red" href="index.php?ns=comments&ctl=manage&action=trash&comment_id='.$id.'&comment_status='.$status.'">Trash</a>';
		
		}
		
		/**
			* Display restore action link
			*
			* @static
			* @access	public
			* @param	integer [$id] Comment id to restore
			* @param	string [$status] Comments status currently viewed
			* @return	string Hyperlink to restore a comment
		*/
		
		public static function h_restore($id, $status){
		
			return '<a class="orange" href="index.php?ns=comments&ctl=manage&action=restore&comment_id='.$id.'&comment_status='.$status.'">Restore</a> | ';
		
		}
		
		/**
			* Display delete action link
			*
			* @static
			* @access	public
			* @param	integer [$id] Comment id to delete permanently
			* @param	string [$status] Comments status currently viewed
			* @return	string Hyperlink to delete a comment permanently
		*/
		
		public static function h_delete($id, $status){
		
			return '<a class="red" href="index.php?ns=comments&ctl=manage&action=delete&comment_id='.$id.'&comment_status='.$status.'">Delete Permanently</a>';
		
		}
		
		/**
			* Display a comments table row
			*
			* @static
			* @access	public
			* @param	integer [$id] Comment id
			* @param	string [$name] Comment name
			* @param	string [$email] Comment email
			* @param	string [$pre_permalink] Pid parameter
			* @param	string [$rel_permalink] Permalink of the element
			* @param	string [$date] Comment date
			* @param	string [$content] Comment date
			* @param	string [$actions] Available actions for the comment
			* @param	string [$link_edit] Url to edit the element
			* @param	integer [$rel_id] Id of the element
			* @param	string [$rel_type] Type of the element
			* @param	string [$status] Comments status currently viewed
		*/
		
		public static function table_row($id, $name, $email, $pre_permalink, $rel_permalink, $date, $content, $actions, $link_edit, $rel_id, $rel_type, $status){
		
			echo '<tr>'.
					'<th class="column_checkbox" scope="row"><input type="checkbox" name="comment_id[]" value="'.$id.'" /></th>'.
					'<td class="column_author">'.
						'<span class="strong">'.$name.'</span><br/>'.
						'<a href="mailto:'.$email.'">'.$email.'</a>'.
					'</td>'.
					'<td class="column_comment">'.
						'<a href="'.PATH.'index.php?'.$pre_permalink.$rel_permalink.'#comment_'.$id.'" target="_blank">'.date('d/m/Y @ H:i', strtotime($date)).'</a><br/>'.
						htmlspecialchars(nl2br($content)).'<br/>'.
						'<div class="row_actions">'.
							$actions.
						'</div>'.
					'</td>'.
					'<td class="column_reponse">'.
						'<a href="'.$link_edit.
						'<a href="index.php?ns=comments&ctl=manage&action=by_type&id='.$rel_id.'&type='.$rel_type.'&comment_status='.$status.'">All comments</a>'.
					'</td>'.
				'</tr>';
		
		}
		
		/**
			* Display reply form to a comment
			*
			* @static
			* @access	public
			* @param	integer [$comment_id] Comment id
			* @param	string [$comment_name] Comment name
			* @param	integer [$id] Id of the element
			* @param	string [$type] Type of the element
		*/
		
		public static function reply($comment_id, $comment_name, $id, $type){
		
			echo '<div id="comment_reply">'.
					'<textarea id="comment_content" class="base_txta" name="comment_content" rows="12" wrap="soft">@<a href="#comment_'.$comment_id.'">'.$comment_name.'</a>: </textarea><br/>'.
					'<input type="hidden" name="id" value="'.$id.'" />'.
					'<input type="hidden" name="type" value="'.$type.'" />'.
					'<input type="hidden" name="comment_status" value="approved" />'.
					'<a class="a_button" href="comments.php">Cancel</a>'.
					'<input class="button button_publish" type="submit" name="submit" value="Submit Reply" />'.
				'</div>';
		
		}
		
		/**
			* Display edit form for a comment
			*
			* @static
			* @access	public
			* @param	string [$date] Comment date
			* @param	integer [$comment_id] Comment id
			* @param	string [$name] Comment name
			* @param	string [$email] Comment email
			* @param	string [$content] Comment content
		*/
		
		public static function edit($date, $comment_id, $name, $email, $content){
		
			echo '<div id="comment_edit">'.
					 '<div id="comment_edit_author">'.
						'<div class="widget_title">'.
							'Author'.
						'</div>'.
						'<div class="comment_edit_content">'.
							'<label for="comment_edit_name">Name:</label><input id="comment_edit_name" class="input_soft" type="text" name="comment_name" value="'.$name.'" /><br/>'.
							'<label for="comment_edit_email">E-mail(<a href="mailto:'.$email.'">send e-mail</a>):</label><input id="comment_edit_email" class="input_soft" type="text" name="comment_email" value="'.$email.'" />'.
							'<input type="hidden" name="comment_id" value="'.$comment_id.'" />'.
						'</div>'.
					'</div><br/>'.
					'<textarea id="comment_content" class="base_txta" name="comment_content" rows="12" wrap="soft">'.$content.'</textarea>'.
					'<div id="comment_edit_meta">'.
						'<div class="widget_title">'.
							'Status'.
						'</div>'.
						'<div id="comment_edit_content">'.
							'<div id="status">'.
								'<input id="a" type="radio" name="comment_status" value="approved" checked /> <label for="a"><span class="green">Approved</span> </label>&nbsp;'.
								'<input id="p" type="radio" name="comment_status" value="pending" /> <label for="p"><span class="orange">Pending</span></label> &nbsp;'.
								'<input id="s" type="radio" name="comment_status" value="spam" /> <label for="s"><span class="red">Spam</span></label>'.
							'</div>'.
							'<div id="comment_date">'.
								'Submitted on <span class="bold">'.date('M d, Y @ H:i', strtotime($date)).'</span><br/>'.
							'</div>'.
						'</div>'.
						'<div id="comment_edit_footer">'.
							'<a id="move_to_trash" href="index.php?ns=comments&ctl=manage&action=trash&comment_id='.$comment_id.'">Move to Trash</a>'.
							'<input class="button button_publish" type="submit" name="submit" value="Update Comment" />'.
						'</div>'.
					'</div>'.
				'</div>';
		
		}
	
	}

?>