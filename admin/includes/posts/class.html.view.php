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
	
	namespace Admin\Posts;
	use \Admin\Html\Html as Master;
	
	/**
		* Html Posts
		*
		* Contains all html for post classes
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Posts
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html extends Master{
	
	
		/**
			* Display menu for new post page
			*
			* @static
			* @access	public
			* @param	boolean [$edit]
		*/
		
		public static function np_menu($edit = false){
		
			if($edit){
			
				echo '<div id="menu">'.
					 	'<span class="menu_item"><a href="index.php?ns=posts&ctl=add">Add</a></span>'.
					 	'<span id="menu_selected" class="menu_item">Editing</span>'.
					 	'<span class="menu_item"><a href="index.php?ns=posts&ctl=manage">Posts</a></span>'.
					 '</div>';
			
			}else{
			
				echo '<div id="menu">'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=posts&ctl=add">Add</a></span>'.
					 	'<span class="menu_item"><a href="index.php?ns=posts&ctl=manage">Posts</a></span>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display form to add a post
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
			* @param	string [$param1] if o -> title else -> comment
			* @param	string [$param2] if o -> permalink else -> tags
			* @param	string [$action] Action to perform
			* @param	string [$status] Post status
			* @param	integer [$id] Post id
			* @param	string [$content] Post content
		*/
		
		public static function np_form($part, $param1, $param2, $action = '', $status = '', $id = 0, $content = ''){
		
			if($part == 'o'){
			
				echo '<div id="post_info">'.
					 	'<div id="title_wrap">'.
					 		'<input id="new_title" type="text" name="title" value="'.$param1.'" placeholder="Enter title here" required />';
				
				if(!empty($param2) && $action == 'to_update'){
				
					$preview = null;
					
					if($status == 'draft')
						$preview = '&preview=true';
						
						echo '<p id="permalink">'.
							 	'Permalink: <a class="a_button" href="'.PATH.'?ctl=posts&news='.$param2.$preview.'" title="View \''.$param1.'\'" target="_blank">'.WS_URL.'?ctl=posts&news='.$param2.$preview.'</a>'.
							 '</p>';
				
				}
				
				echo 		'<input type="hidden" name="id" value="'.$id.'" />'.
					 		'<input type="hidden" name="action" value="'.$action.'" />'.
					 	'</div>'.
					 	'<div id="post_div">'.
					 		'<div id="editor_container">'.
					 			'<textarea class="base_txta" rows="20" name="content" wrap="soft" placeholder="Type your post here" required>'.$content.'</textarea>'.
					 		'</div>'.
					 		'<fieldset id="cats">'.
					 			'<legend>Categories</legend>';
			
			}elseif($part == 'c'){
			
				echo 		'</fieldset>'.
					 		'<input id="allow_comment" type="checkbox" name="allow_comment" value="open" '.(($param1 == 'open')?'checked="true"':'').' /><label for="allow_comment">Allow comments</label><br/>'.
					 		'<br/>'.
					 		'<label for="post_tags">Tags</label><input id="post_tags" type="text" name="tags" value="'.$param2.'" placeholder="Separate your tags with commas" />'.
					 	'</div>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display pictures fieldset structure
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
		*/
		
		public static function np_pictures($part){
		
			if($part == 'o'){
			
				echo '<section id="fd_pics">'.
					 	'<fieldset>'.
					 		'<legend>Images</legend>';

			}elseif($part == 'c'){
					 		
				echo 	'</fieldset>'.
					 '</section>';
			
			}
		
		}
		
		/**
			* Display pictures informations
			*
			* @static
			* @access	public
			* @param	integer [$id] Image id
			* @param	string [$name] Image name
			* @param	string [$full] Image path
		*/
		
		public static function np_pic($id, $name, $full){
		
			echo '<div class="np_pic">'.
				 	'(<a class="fancybox" href="'.PATH.$full.'" title="'.$name.'">view</a>)'.
				 	'<input id="pic'.$id.'" type="text" value="'.htmlspecialchars('<img src="'.$full.'" />').'" readonly /> '.
				 	'<label for="pic'.$id.'">'.$name.'</label> '.
				 '</div>';
		
		}
		
		/**
			* Display videos fieldset structure
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
		*/
		
		public static function np_videos($part){
		
			if($part == 'o'){
						
				echo '<section id="fd_vids">'.
					 	'<fieldset>'.
					 		'<legend>Videos</legend>';

			}elseif($part == 'c'){
					 		
				echo 	'</fieldset>'.
					 '</section>';
			
			}
		
		}
		
		/**
			* Display video informations
			*
			* @static
			* @access	public
			* @param	integer [$id] Video id
			* @param	string [$name] Video name
			* @param	string [$link] Video path
			* @param	string [$embed_code] External attached video embed code
		*/
		
		public static function np_vid($id, $name, $link, $embed_code){
		
			echo '<div class="np_vid">'.
				 	'<input id="vid'.$id.'" type="text" value="'.htmlspecialchars('<video src="'.$link.'" controls preload="auto">'.$embed_code.'</video>').'" readonly />'.
				 	'<label for="vid'.$id.'">'.$name.'</label>'.
				 '</div>';
		
		}
		
		/**
			* Display category input
			*
			* @static
			* @access	public
			* @param	integer [$id] Category id
			* @param	string [$name] Category name
			* @param	array [$array] If $id is in this array the category will be checked
		*/
		
		public static function category($id, $name, $array = array()){
		
			echo '<span class="acat"><input id="cat'.$id.'" type="checkbox" name="categories[]" value="'.$id.'" '.((in_array($id, $array))?'checked':'').' /> <label for="cat'.$id.'">'.ucwords($name).'</label></span>';
		
		}
		
		/**
			* Display actions
			*
			* @static
			* @access	public
			* @param	array [$actions]
			* @param	string [$permalink]
		*/
		
		public static function np_actions($actions, $permalink = ''){
		
			echo '<div id="post_actions">';
			
			if(in_array('draft', $actions))
				echo '<input class="button" type="submit" name="save_draft" value="Save Draft" />';
			
			if(in_array('preview', $actions))
				echo '&nbsp;&nbsp;<a class="a_button" href="'.PATH.'?preview=true&ctl=posts&news='.$permalink.'" target="_blank">Preview</a>';
			
			if(in_array('view', $actions))
				echo '&nbsp;&nbsp;<a class="a_button" href="'.PATH.'?ctl=posts&news='.$permalink.'" target="_blank">View Post</a>';
			
			if(in_array('publish', $actions))
				echo '<input class="button button_publish" type="submit" name="publish" value="Publish" />';
			
			if(in_array('update', $actions))
				echo '<input class="button button_publish" type="submit" name="publish" value="Update" />';
			
			echo '</div>';
		
		}
		
		/**
			* Display menu for manage posts page
			*
			* @static
			* @access	public
		*/
		
		public static function mp_menu(){
		
			echo '<div id="menu">'.
				 	'<span class="menu_item"><a href="index.php?ns=posts&ctl=add">Add</a></span>'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=posts&ctl=manage">Posts</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display type selection menu
			*
			* @static
			* @access	public
			* @param	string [$all]
			* @param	integer [$count]
			* @param	string [$publish]
			* @param	integer [$count_publish]
			* @param	string [$draft]
			* @param	integer [$count_draft]
			* @param	string [$trash]
			* @param	integer [$count_trash]
		*/
		
		public static function mp_type_menu($all, $count, $publish, $count_publish, $draft, $count_draft, $trash, $count_trash){
		
			echo '<div id="select_post_status">'.
					'<a href="index.php?ns=posts&ctl=manage">'.$all.'</a> ('.$count.') | '.
					'<a href="index.php?ns=posts&ctl=manage&post_status=publish">'.$publish.'</a> ('.$count_publish.') | '.
					'<a href="index.php?ns=posts&ctl=manage&post_status=draft">'.$draft.'</a> ('.$count_draft.') | '.
					'<a href="index.php?ns=posts&ctl=manage&post_status=trash">'.$trash.'</a> ('.$count_trash.')'.
					'<span id="search"><input id="search_input" type="text" name="search" placeholder="Search" list="titles" />'.
					'<input class="button" type="submit" name="search_button" value="Search Posts" /></span>'.
				'</div>';
		
		}
		
		/**
			* Display restore button
			*
			* @static
			* @access	public
		*/
		
		public static function mp_restore(){
		
			echo '<input class="button" type="submit" name="restore" value="Restore" />&nbsp;';
		
		}
		
		/**
			* Display delete button
			*
			* @static
			* @access	public
		*/
		
		public static function mp_delete(){
		
			echo '<input class="button" type="submit" name="delete" value="Delete" />&nbsp;&nbsp;';
		
		}
		
		/**
			* Display trash button
			*
			* @static
			* @access	public
		*/
		
		public static function mp_trash(){
		
			echo '<input class="button" type="submit" name="trash" value="Move to Trash" />&nbsp;&nbsp;';
		
		}
		
		/**
			* Display empty trash button
			*
			* @static
			* @access	public
		*/
		
		public static function mp_empty(){
		
			echo '<input class="button" type="submit" name="empty_trash" value="Empty Trash" />';
		
		}
		
		/**
			* Display actions for posts
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o", "m" or "c"
			* @param	string [$status] Posts status currently viewed
		*/
		
		public static function mp_actions($part, $status = ''){
		
			if($part == 'o'){
			
				echo '<input type="hidden" name="post_status" value="'.$status.'" />'.
					 '<select name="date">'.
						'<option value="all">Show all dates</option>';
			
			}elseif($part == 'm'){
			
				echo '</select>'.
					 '<select name="category">'.
					 	'<option value="all">Show all categories</option>';
			
			}elseif($part == 'c'){
			
				echo '</select>'.
					 '<input class="button" type="submit" name="filter" value="Filter"  />&nbsp;';
			
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
			* Display posts table
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
								'<th class="column_checkbox" scope="col"><input type="checkbox" name="post[]" /></th>'.
								'<th class="column_title">Title</th>'.
								'<th class="column_author">Author</th>'.
								'<th class="column_categories">Categories</th>'.
								'<th class="column_tags">Tags</th>'.
								'<th class="column_comments">Comments</th>'.
								'<th class="column_date">Date</th>'.
							'</tr>'.
						'</thead>'.
						'<tfoot>'.
							'<tr>'.
								'<th class="column_checkbox" scope="col"><input type="checkbox" name="post[]" /></th>'.
								'<th class="column_title">Title</th>'.
								'<th class="column_author">Author</th>'.
								'<th class="column_categories">Categories</th>'.
								'<th class="column_tags">Tags</th>'.
								'<th class="column_comments">Comments</th>'.
								'<th class="column_date">Date</th>'.
							'</tr>'.
						'</tfoot>'.
						'<tbody>';
			
			}elseif($part == 'c'){
			
				echo 	'</tbody>'.
					 '</table>';
			
			}
		
		}
		
		/**
			* Return restore link
			*
			* @static
			* @access	public
			* @param	integer [$id] Post id
			* @return	string
		*/
		
		public static function mp_restore_link($id){
		
			return '<a class="orange" href="index.php?ns=posts&ctl=manage&action=untrash&id='.$id.'&post_status=trash" title="Restore this item">Restore</a> |';
		
		}
		
		/**
			* Return delete link
			*
			* @static
			* @access	public
			* @param	integer [$id] Post id
			* @return	string
		*/
		
		public static function mp_delete_link($id){
		
			return '<a class="red" href="index.php?ns=posts&ctl=manage&action=delete&id='.$id.'&post_status=trash" title="Delete">Delete Permanently</a>';
		
		}
		
		/**
			* Return edit link
			*
			* @static
			* @access	public
			* @param	integer [$id] Post id
			* @return	string
		*/
		
		public static function mp_edit_link($id){
		
			return '<a href="index.php?ns=posts&ctl=add&action=edit&id='.$id.'" title="Edit this item">Edit</a> | ';
		
		}
		
		/**
			* Return trash link
			*
			* @static
			* @access	public
			* @param	integer [$id] Post id
			* @param	string [$status] Posts status currently viewed
			* @return	string
		*/
		
		public static function mp_trash_link($id, $status){
		
			return '<a class="red" href="index.php?ns=posts&ctl=manage&action=trash&id='.$id.'&post_status='.$status.'" title="Trash this item">Trash</a> | ';
		
		}
		
		/**
			* Return view link
			*
			* @static
			* @access	public
			* @param	string [$preview]
			* @param	string [$permalink] Post permalink
			* @param	string [$title] Post title
			* @return	string
		*/
		
		public static function mp_view_link($preview, $permalink, $title){
		
			return '<a href="'.PATH.'?ctl=posts&'.$preview.'news='.$permalink.'" title="View “'.$title.'“" target="_blank" >View</a>';
		
		}
		
		/**
			* Display a table row
			*
			* @static
			* @access	public
			* @param	integer [$id] Post id
			* @param	string [$title] Post title
			* @param	string [$draft] If the post is a draft
			* @param	string [$actions] Applicable actions
			* @param	integer [$author] Author id
			* @param	string [$author_name] Author username
			* @param	string [$cats] Post categories
			* @param	string [$tags] Post tags
			* @param	integer [$comment] Post comments number
			* @param	string [$date] Creation date
		*/
		
		public static function table_row($id, $title, $draft, $actions, $author, $author_name, $cats, $tags, $comment, $date){
		
			echo '<tr>'.
					'<th class="column_checkbox" scope="row"><input type="checkbox" name="post_id[]" value="'.$id.'" /></th>'.
					'<td class="column_title">'.
						'<a href="index.php?ns=posts&ctl=add&action=edit&id='.$id.'" title="Edit “'.$title.'“">'.$title.'</a>'.$draft.
						'<div class="row_actions">'.
							$actions.
						'</div>'.
					'</td>'.
					'<td class="column_author">'.
						'<a href="index.php?ns=posts&ctl=manage&author='.$author.'">'.$author_name.'</a>'.
					'</td>'.
					'<td class="column_categories">'.
						$cats.
					'</td>'.
					'<td class="column_tags">'.
						$tags.
					'</td>'.
					'<td class="column_comments">'.
						$comment.
					'</td>'.
					'<td class="column_date">'.
						date('Y/m/d', strtotime($date)).
					'</td>'.
				'</tr>';
		
		}
		
		/**
			* Display menu for post setting page
			*
			* @static
			* @access	public
		*/
		
		public static function sp_menu(){
		
			echo '<div id="menu">'.
				 	'<span class="menu_item"><a href="index.php?ns=settings&ctl=manage">Settings</a></span>'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=posts&ctl=settingpage">Posts</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display form to activate post settings
			*
			* @static
			* @access	public
			* @param	boolean [$media]
		*/
		
		public static function settings($media){
		
			echo '<h3>Check settings you want to activate</h3>'.
				 '<input class="button button_publish submit" type="submit" name="update_setting" value="Update" /><br/>'.
				 '<div id="labels">'.
					 '<div class="setting_label">'.
					 	'<div class="label_img">'.
					 		'<input id="media" type="checkbox" name="settings[]" value="media" '.(($media)?'checked':'').' />'.
					 	'</div>'.
					 	'<div class="label_name">'.
					 		'<label for="media">Display media for post edition</label>'.
					 	'</div>'.
					 '</div>'.
				 '</div>';
		
		}
	
	}

?>