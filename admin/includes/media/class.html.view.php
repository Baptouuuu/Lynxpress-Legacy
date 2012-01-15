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
	
	namespace Admin\Media;
	use \Library\Media\Media as HandleMedia;
	use \Admin\Html\Html as Master;
	
	/**
		* Html Media
		*
		* Contains all html for media classes
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Media
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html extends Master{
	
		/**
			* Display menu for new media page
			*
			* @static
			* @access	public
			* @param	boolean [$bool]
			* @param	boolean [$can_album]
		*/
		
		public static function nm_menu($bool, $can_album = false){
		
			if($bool){
			
				echo '<div id="menu">'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=media&ctl=add">Add</a></span>'.
					 	'<span class="menu_item"><a href="index.php?ns=media&ctl=manage">Media</a></span>'.
					 	(($can_album)?'<span class="menu_item"><a href="index.php?ns=media&ctl=albums">Albums</a></span>':'').
					 '</div>';
			
			}else{
			
				echo '<div id="menu">'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=media&ctl=manage">Media</a></span>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display upload type selection menu
			*
			* @static
			* @access	public
			* @param	string [$upload]
			* @param	string [$album]
			* @param	string [$linkage]
			* @param	string [$video]
			* @param	boolean [$can_album]
		*/
		
		public static function nm_type_menu($upload, $album, $linkage, $video, $can_album){
		
			echo '<div id="select_post_status">'.
					'<a href="index.php?ns=media&ctl=add&view=upload">'.$upload.'</a> | '.
					(($can_album)?'<a href="index.php?ns=media&ctl=add&view=album">'.$album.'</a> | ':'').
					'<a href="index.php?ns=media&ctl=add&view=linkage">'.$linkage.'</a> | '.
					'<a href="index.php?ns=media&ctl=add&view=video">'.$video.'</a>'.
				'</div>';
		
		}
		
		/**
			* Display file upload form
			*
			* @static
			* @access	public
		*/
		
		public static function nm_upload(){
		
			self::form('o', 'post', '#', true);
			
			echo '<label for="file">Select a file to upload:</label>&nbsp;&nbsp;&nbsp;&nbsp;<input id="file" name="file" type="file" />'.
				 '<input id="upload" class="button button_publish" type="submit" name="upload" value="Upload" /><br/>'.
				 '<span class="indication">(The maximum upload file size is set to '.HandleMedia::max_upload().'MB)</span><br/>'.
				 '<span class="indication">(If you want to upload a video too large, upload it via ftp and use this <a href="index.php?ns=media&ctl=add&view=video">form</a> to register it)</span><br/>';
			
			self::form('c');
		
		}
		
		/**
			* Display album creation form
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
		*/
		
		public static function nm_album($part){
		
			if($part == 'o'){
			
				self::form('o', 'post', '#', true);
				
				echo '<input id="media_name" type="text" name="name" placeholder="Album name" required /><br/>'.
					 '<textarea id="media_desc" class="base_txta" rows="10" name="description" wrap="soft" placeholder="A description of your album"></textarea><br/>'.
					 '<fieldset id="cats">'.
					 	'<legend>Categories</legend>';
			
			}elseif($part == 'c'){
			
				echo '</fieldset>'.
					 '<input id="allow_comment" type="checkbox" name="allow_comment" value="open" /> <label for="allow_comment">Allow comments</label>'.
					 '<span id="cover_line"><label for="cover">Cover:</label> <input id="cover" name="cover" type="file" required /></span><br/>'.
					 '<input class="submit button button_publish" type="submit" name="create_album" value="Create Album" />';
				
				self::form('c');
			
			}
		
		}
		
		/**
			* Display a category input
			*
			* @static
			* @access	public
			* @param	integer [$id] Category id
			* @param	string [$name] Category name
			* @param	array [$array] If $id is in this array, the category is checked
		*/
		
		public static function category($id, $name, $array = array()){
		
			echo '<span class="acat"><input id="cat'.$id.'" type="checkbox" name="cat[]" value="'.$id.'" '.((in_array($id, $array))?'checked':'').' /> <label for="cat'.$id.'">'.ucwords($name).'</label></span>';
		
		}
		
		/**
			* Display linkage form
			*
			* @static
			* @access	public
		*/
		
		public static function nm_linkage(){
		
			self::form('o', 'post', '#');
			
			echo '<input id="media_name" type="text" name="name" placeholder="Video title" required /><br/>'.
				 '<textarea id="media_desc" class="base_txta" rows="10" name="embed_code" wrap="soft" placeholder="Embed code" required></textarea></br>'.
				 '<input class="submit button button_publish" type="submit" name="link_alien" value="Link" />';
			
			self::form('c');
		
		}
		
		/**
			* Display form to register a video uploaded via ftp
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain 'o' or 'c'
		*/
		
		public static function nm_video($part){
		
			if($part == 'o'){
			
				self::form('o', 'post', '#');
				
				echo '<input id="media_name" type="text" name="name" placeholder="Video name" required /><br/>'.
					 '<fieldset id="cats">'.
					 	'<legend>Video mime type</legend>';
			
			}elseif($part == 'c'){
			
				echo '</fieldset>'.
					 '<label for="video_url">Video url:</label> <input id="video_url" class="user_input_text" type="text" name="url" placeholder="content/'.date('Y/m/').'" required /><br/>'.
					 '<span class="indication">(Upload your video inside content folder to work correctly. You should use directory convention too, putting your content inside folder with year and month.)</span><br/>'.
					 '<input class="submit button button_publish" type="submit" name="register_video" value="Register Video" />';
			
				self::form('c');
			
			}
		
		}
		
		/**
			* Display a video category input
			*
			* @static
			* @access	public
			* @param	string [$name] Category name
		*/
		
		public static function video_category($name){
		
			echo '<span class="acat"><input id="cat'.$name.'" type="radio" name="mime" value="'.$name.'" /> <label for="cat'.$name.'">'.$name.'</label></span>';
		
		}
		
		/**
			* Display menu for manage media page
			*
			* @static
			* @access	public
			* @param	boolean [$bool]
			* @param	boolean [$edit]
			* @param	boolean [$can_album]
			* @param	string [$name] File name
		*/
		
		public static function mm_menu($bool, $edit = false, $can_album = false, $name = ''){
		
			if($bool){
			
				if($edit){
				
					echo '<div id="menu">'.
						 	'<span class="menu_item"><a href="index.php?ns=media&ctl=add">Add</a></span>'.
						 	'<span class="menu_item"><a href="index.php?ns=media&ctl=manage">Media</a></span>'.
						 	'<span  id="menu_selected" class="menu_item">Editing '.$name.'</span>'.
						 	(($can_album)?'<span class="menu_item"><a href="index.php?ns=media&ctl=albums">Albums</a></span>':'').
						 '</div>';
				
				}else{
				
					echo '<div id="menu">'.
						 	'<span class="menu_item"><a href="index.php?ns=media&ctl=add">Add</a></span>'.
						 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=media&ctl=manage">Media</a></span>'.
						 	(($can_album)?'<span class="menu_item"><a href="index.php?ns=media&ctl=albums">Albums</a></span>':'').
						 '</div>';
				
				}
			
			}else{
			
				echo '<div id="menu">'.
					 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=media&ctl=manage">Media</a></span>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display media type selection menu
			*
			* @static
			* @access	public
			* @param	string [$image]
			* @param	integer [$count_image]
			* @param	string [$video]
			* @param	integer [$count_video]
			* @param	string [$alien]
			* @param	integer [$count_alien]
		*/
		
		public static function mm_type_menu($image, $count_image, $video, $count_video, $alien, $count_alien){
		
			echo '<div id="select_post_status">'.
					'<a href="index.php?ns=media&ctl=manage">'.$image.'</a> ('.$count_image.') | '.
					'<a href="index.php?ns=media&ctl=manage&type=video">'.$video.'</a> ('.$count_video.') | '.
					'<a href="index.php?ns=media&ctl=manage&type=alien">'.$alien.'</a> ('.$count_alien.')'.
					'<span id="search"><input id="search_input" type="text" name="search" placeholder="Search" list="titles" />'.
					'<input class="button" type="submit" name="search_button" value="Search Medias" /></span>'.
				'</div>';
		
		}
		
		/**
			* Display a delete button
			*
			* @static
			* @access	public
		*/
		
		public static function mm_delete(){
		
			echo '<input class="button" type="submit" name="delete" value="Delete" />&nbsp;&nbsp;';
		
		}
		
		/**
			* Display actions menu
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
			* @param	string [$type] Medias type currently viewed
		*/
		
		public static function mm_actions($part, $type = ''){
		
			if($part == 'o'){
			
				echo '<input type="hidden" name="type" value="'.$type.'" />'.
					 '<select name="date">'.
						'<option value="all">Show all dates</option>';
			
			}elseif($part == 'c'){
			
				echo '</select>'.
					 '<input class="button" type="submit" name="filter" value="Filter"  />';
			
			}
		
		}
		
		/**
			* Display an element for actions dropdowns
			*
			* @static
			* @access	public
			* @param	mixed [$key]
			* @param	mixed [$value]
			* @param	mixed [$wanted]
		*/
		
		public static function mm_opt_actions($key, $value, $wanted = ''){
		
			echo '<option value="'.$key.'" '.(($key == $wanted)?'selected':'').'>'.$value.'</option>';
		
		}
		
		/**
			* Display a category dropdown
			*
			* @static
			* @access	public
		*/
		
		public static function mm_video_cat(){
		
			echo '</select>'.
				 '<select name="category">'.
				 	'<option value="all">Show all categories</option>';
		
		}
		
		/**
			* Display medias table
			* 
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
		*/
		
		public static function mm_table($part){
		
			if($part == 'o'){
			
				echo '<table id="table">'.
						'<thead>'.
							'<tr>'.
								'<th class="column_checkbox" scope="col"><input type="checkbox" name="media[]" /></th>'.
								'<th class="column_file">File</th>'.
								'<th class="column_author">Author</th>'.
								'<th class="column_date">Date</th>'.
								'<th class="column_links">Links</th>'.
							'</tr>'.
						'</thead>'.
						'<tfoot>'.
							'<tr>'.
								'<th class="column_checkbox" scope="col"><input type="checkbox" name="media[]" /></th>'.
								'<th class="column_file">File</th>'.
								'<th class="column_author">Author</th>'.
								'<th class="column_date">Date</th>'.
								'<th class="column_links">Links</th>'.
							'</tr>'.
						'</tfoot>'.
						'<tbody>';
			
			}elseif($part == 'c'){
			
				echo 	'</tbody>'.
					 '</table>';
			
			}
		
		}
		
		/**
			* Return files links for an image
			*
			* @static
			* @access	public
			* @param	string [$dirname] Directory path of the file
			* @param	string [$filename] File name
			* @return	string
		*/
		
		public static function mm_image_links($dirname, $filename){
		
			$links = '<input class="user_input_text" type="text" value="'.$dirname.$filename.'" readonly/><br/>';
			$links .= '<input class="user_input_text" type="text" value="'.$dirname.'1000-'.$filename.'" readonly/><br/>';
			$links .= '<input class="user_input_text" type="text" value="'.$dirname.'300-'.$filename.'" readonly/><br/>';
			$links .= '<input class="user_input_text" type="text" value="'.$dirname.'150-'.$filename.'" readonly/>';
			
			return $links;
		
		}
		
		/**
			* Return link for a video
			*
			* @static
			* @access	public
			* @param	string [$permalink] Path to the file
			* @return	string
		*/
		
		public static function mm_video_link($permalink){
		
			return '<input class="user_input_text" type="text" value="'.$permalink.'" readonly/>';
		
		}
		
		/**
			* Return link for an external video
			*
			* @static
			* @access	public
			* @param	string [$embed_code] Code of the external video
			* @return	string
		*/
		
		public static function mm_alien_link($embed_code){
		
			return '<textarea class="base_txta" wrap="soft" rows="5" readonly>'.$embed_code.'</textarea>';
		
		}
		
		/**
			* Display a medias table row
			*
			* @static
			* @access	public
			* @param	integer [$id] Media id
			* @param	string [$path] Path to the thumb
			* @param	string [$name] Media name
			* @param	string [$type] Media mime type
			* @param	string [$view_type] Media type currently viewed
			* @param	integer [$author] Author id
			* @param	string [$author_name] Author username
			* @param	string [$date] Upload date
			* @param	string [$links]
			* @param	string [$status] Media status
		*/
		
		public static function mm_table_row($id, $path, $name, $type, $view_type, $author, $author_name, $date, $links, $status){
		
			echo '<tr>'.
					'<th class="column_checkbox" scope="row">'.
						'<input id="media'.$id.'" type="checkbox" name="media_id[]" value="'.$id.'" /><br/>'.
						'<label for="media'.$id.'">'.
							'<img class="file_thumb" src="'.PATH.$path.'" title="'.$name.'" />'.
						'</label>'.
					'</th>'.
					'<td class="column_file">'.
						'<span class="strong">'.$name.'</span>'.
						'<p>'.$type.'</p>'.
						'<div class="row_actions">'.
							'<a href="index.php?ns=media&ctl=manage&action=edit&type='.$view_type.'&id='.$id.'">Edit</a> | '.
							'<a class="red" href="index.php?ns=media&ctl=manage&action=delete&type='.$view_type.'&id='.$id.'">Delete permanently</a>'.
						'</div>'.
					'</td>'.
					'<td class="column_author">'.
						'<a href="index.php?ns=media&ctl=manage&type='.$status.'&author='.$author.'">'.$author_name.'</a>'.
					'</td>'.
					'<td class="column_date">'.
						date('Y/m/d @ H:i', strtotime($date)).
					'</td>'.
					'<td>'.
						$links.
					'</td>'.
				'</tr>';
		
		}
		
		/**
			* Display form to edit an image
			*
			* @static
			* @access	public
			* @param	string [$name] Image name
			* @param	string [$dirname] Directory path of the file
			* @param	string [$fname] File name
			* @param	string [$description] Image description
			* @param	string [$permalink] Path to the file
			* @param	integer [$id] Image id
			* @param	string [$type] Image mime type
		*/
		
		public static function mm_edit_image($name, $dirname, $fname, $description, $permalink, $id, $type){
		
			echo '<div id="edit_media">'.
				 	'<input id="media_name" type="text" name="name" value="'.$name.'" placeholder="Image title" required /><br/>'.
				 	'<br/>'.
				 	'<img src="'.PATH.$dirname.'1000-'.$fname.'" alt="'.$name.'" title="'.$name.'" /><br/>'.
				 	'<select id="flip" name="flip">'.
				 		'<option value="no">Flip</option>'.
				 		'<option value="h">Horizontally</option>'.
				 		'<option value="v">Vertically</option>'.
				 	'</select><br/>'.
				 	'<select id="rotate" name="rotate">'.
				 		'<option value="no">Rotation</option>'.
				 		'<option value="90">90°</option>'.
				 		'<option value="180">180</option>'.
				 		'<option value="270">-90°</option>'.
				 	'</select><br/>'.
				 	'<br/>'.
				 	'<textarea id="media_desc" class="base_txta" rows="10" name="description" wrap="soft" placeholder="A little description of your photo">'.$description.'</textarea><br/>'.
				 	'<label for="fsize">Image url</label>: <input id="fsize" class="user_input_text" type="text" value="'.$permalink.'" readonly /> <span class="indication">(full size)</span><br/>'.
				 	'<label for="size15">Image url</label>: <input id="size15" class="user_input_text" type="text" value="'.$dirname.'150-'.$fname.'" readonly /> <span class="indication">(image with 150 pixels width)</span><br/>'.
				 	'<label for="size3">Image url</label>: <input id="size3" class="user_input_text" type="text" value="'.$dirname.'300-'.$fname.'" readonly /> <span class="indication">(image with 300 pixels width)</span><br/>'.
				 	'<label for="size1">Image url</label>: <input id="size1" class="user_input_text" type="text" value="'.$dirname.'1000-'.$fname.'" readonly /> <span class="indication">(image with 1000 pixels width)</span><br/>'.
				 	'<input class="submit button button_publish" type="submit" name="update_image" value="Update" />'.
				 	'<input type="hidden" name="id" value="'.$id.'" />'.
				 	'<input type="hidden" name="type" value="'.$type.'" />'.
				 '</div>';
		
		}
		
		/**
			* Display form to edit a video
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o", "m" or "c"
			* @param	string [$param1] if o -> name else -> permalink
			* @param	mixed [$param2] if o -> permalink else -> id
			* @param	string [$param3] if o -> description else -> type
		*/
		
		public static function mm_edit_video($part, $param1 = '', $param2 = '', $param3 = ''){
		
			if($part == 'o'){
			
				echo '<div id="edit_media">'.
					 	'<input id="media_name" type="text" name="name" value="'.$param1.'" placeholder="Video title" required /><br/>'.
					 	'<br/>'.
					 	'<video width="640" src="'.PATH.$param2.'" controls>'.
					 	'</video><br/>'.
					 	'<br/>'.
					 	'<textarea id="media_desc" class="base_txta" rows="10" name="description" wrap="soft" placeholder="A little description of your video">'.$param3.'</textarea><br/>'.
					 	'<fieldset id="cats">'.
					 		'<legend>Categories</legend>';
			
			}elseif($part == 'm'){
				 		
				echo 	'</fieldset>'.
					 	'<select id="attach" name="attach">'.
					 		'<option value="no">Attach to…</option>';
			
			}elseif($part == 'c'){
				 		
				echo	'</select>'.
					 	'&nbsp;&nbsp;<span class="indication">Attaching a video file to an external video permits to load the last one if the user browser doesn\'t support &lt;video&gt; html5 tag</span><br/>'.
					 	'<br/>'.
					 	'<label for="link">Video url</label>: <input id="link" class="user_input_text" type="text" value="'.$param1.'" readonly /><br/>'.
					 	'<input class="submit button button_publish" type="submit" name="update_video" value="Update" />'.
					 	'<input type="hidden" name="id" value="'.$param2.'" />'.
					 	'<input type="hidden" name="type" value="'.$param3.'" />'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display form to edit a linked video
			*
			* @static
			* @access	public
			* @param	string [$name] External video name
			* @param	string [$embed_code] External video embed code
			* @param	string [$description] External video description
			* @param	integer [$id] External video id
			* @param	string [$type] External video type
		*/
		
		public static function mm_edit_alien($name, $embed_code, $description, $id, $type){
		
			echo '<div id="edit_media">'.
				 	'<input id="media_name" type="text" name="name" value="'.$name.'" placeholder="Video title" required /><br/>'.
				 	'<br/>'.
				 	'<div id="embed">'.
				 		$embed_code.
				 	'</div>'.
				 	'<br/>'.
				 	'<textarea id="media_desc" class="base_txta" rows="10" name="description" wrap="soft" placeholder="A little description of your video">'.$description.'</textarea><br/>'.
				 	'<textarea id="media_desc" class="base_txta" rows="10" name="embed" wrap="soft" placeholder="Embed code of your video">'.$embed_code.'</textarea><br/>'.
				 	'<input class="submit button button_publish" type="submit" name="update_alien" value="Update" />'.
				 	'<input type="hidden" name="id" value="'.$id.'" />'.
				 	'<input type="hidden" name="type" value="'.$type.'" />'.
				 '</div>';
		
		}
		
		/**
			* Display menu for manage albums page
			*
			* @static
			* @access	public
			* @param	boolean [$bool]
			* @param	boolean [$edit]
			* @param	integer [$id]
			* @param	string [$name]
		*/
		
		public static function ma_menu($bool, $edit = false, $id = 0, $name = ''){
		
			if($bool){
			
				if($edit){
				
					echo '<div id="menu">'.
						 	'<span class="menu_item"><a href="index.php?ns=media&ctl=add">Add</a></span>'.
						 	'<span class="menu_item"><a href="index.php?ns=media&ctl=manage">Media</a></span>'.
						 	'<span class="menu_item"><a href="index.php?ns=media&ctl=albums">Albums</a></span>'.
						 	'<span  id="menu_selected" class="menu_item"><a href="index.php?ns=media&ctl=albums&action=edit&id='.$id.'">Editing '.$name.'</a></span>'.
						 '</div>';
				
				}else{
				
					echo '<div id="menu">'.
						 	'<span class="menu_item"><a href="index.php?ns=media&ctl=add">Add</a></span>'.
						 	'<span class="menu_item"><a href="index.php?ns=media&ctl=manage">Media</a></span>'.
						 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=media&ctl=albums">Albums</a></span>'.
						 '</div>';
				
				}
			
			}else{
			
				echo '<div id="menu">'.
					 	'<span class="menu_item"><a href="index.php?ns=media&ctl=manage">Media</a></span>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display actions bar
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o", "m" or "c"
		*/
		
		public static function ma_actions($part){
		
			if($part == 'o'){
			
				echo '<select name="action">'.
					 	'<option value="no">Actions...</option>'.
					 	'<option value="publish">Publish</option>'.
					 	'<option value="unpublish">Unpublish</option>'.
					 	'<option value="delete">Delete</option>'.
					 '</select>'.
					 '<input class="button" type="submit" name="apply_action" value="Apply" />&nbsp;&nbsp;'.
					 '<select name="date">'.
					 	'<option value="all">Show all dates</option>';
			
			}elseif($part == 'm'){
			
				echo '</select>'.
					 '<select name="category">'.
					 	'<option value="all">Show all categories</option>';
			
			}elseif($part == 'c'){
			
				echo '</select>'.
					 '<input class="button" type="submit" name="filter" value="Filter" />'.
					 '<span id="search"><input id="search_input" type="text" name="search" placeholder="Search" list="titles" />'.
					 '<input class="button" type="submit" name="search_button" value="Search Users" /></span>';
			
			}
		
		}
		
		/**
			* Display an album label
			*
			* @static
			* @access	public
			* @param	integer [$id] Album id
			* @param	string [$permalink] Album directory path
			* @param	string [$name] Album name
			* @param	string [$status] Album status
			* @param	string [$author] Author username
			* @param	string [$date] Creation date
			* @param	array [$categories] Album categories
		*/
		
		public static function ma_album_label($id, $permalink, $name, $status, $author, $date, $categories){
		
			echo '<div class="album_label">'.
					'<label for="album_'.$id.'">'.
					 	'<div class="check_label">'.
					 		'<input id="album_'.$id.'" type="checkbox" name="album_id[]" value="'.$id.'" />'.
					 	'</div>'.
					'</label>'.
				 	'<div class="content_label">'.
					 	'<div class="label_cover">'.
					 		'<a href="index.php?ns=media&ctl=albums&action=edit&id='.$id.'" title="Edit"><img src="'.PATH.$permalink.'150-cover.png" alt="Edit" /></a>'.
					 	'</div>'.
					 	'<div class="user_info_side">'.
					 		'Name: <span class="lname">'.$name.'</span><br/>'.
						 	'Status: <span class="lstatus">'.ucfirst($status).'</span><br/>'.
						 	'Author: <span class="lauth">'.ucfirst($author).'</span><br/>'.
						 	'Creation: <span class="ldate">'.date('F d, Y', strtotime($date)).'</span><br/>'.
						 	'Categories: <span class="lcat">'.(ucwords(implode(', ', $categories))).'</span>'.
						'</div>'.
					'</div>'.
				 '</div>';
		
		}
		
		/**
			* Display save button
			*
			* @static
			* @access	public
		*/
		
		public static function ma_save(){
		
			echo '<input class="button" type="submit" name="save" value="Save" />';
		
		}
		
		/**
			* Display link to view album
			*
			* @static
			* @access	public
			* @param	integer [$id] Album id
			* @param	boolean [$preview] If the album is published or still in draft status
		*/
		
		public static function ma_view($id, $preview = false){
		
			echo '&nbsp;&nbsp;<a class="a_button" href="'.PATH.'?ctl=albums&album='.$id.(($preview)?'&preview=true':'').'" target="_blank">'.(($preview)?'Preview':'View Album').'</a>';
		
		}
		
		/**
			* Display publish button
			*
			* @static
			* @access	public
			* @param	boolean [$publish]
		*/
		
		public static function ma_publish($publish = true){
		
			echo '<input class="button button_publish" type="submit" '.(($publish)?'name="publish" value="Publish"':'name="unpublish" value="Unpublish"').' />';
		
		}
		
		/**
			* Display album edit form
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o", "m" or "c"
			* @param	integer [$id] Album id
			* @param	string [$permalink] Album directory path
			* @param	string [$author_name] Author username
			* @param	string [$date] Creation date
			* @param	string [$comment] If comments are allowed or not
			* @param	string [$name] Album name
			* @param	string [$description] Album description
		*/
		
		public static function ma_edit($part, $id = 0, $permalink = '', $author_name = '', $date = '', $comment = '', $name = '', $description = ''){
		
			if($part == 'o'){
			
				echo '<div id="edit_media">'.
						 '<div id="ea_main">'.
						 	'<div id="ea_cover">'.
						 		'<img src="'.PATH.$permalink.'300-cover.png" alt="Cover" />'.
						 	'</div>'.
						 	'<div id="ea_meta">'.
						 		'Created by: '.$author_name.'<br/>'.
						 		'The: '.date('Y/m/d @ H:i', strtotime($date)).'<br/>'.
						 		'<input id="allow_comment" type="checkbox" name="allow_comment" value="open" '.(($comment == 'open')?'checked':'').' /> <label for="allow_comment">Allow comments</label>'.
						 		'<input type="hidden" name="album_id" value="'.$id.'" />'.
						 	'</div>'.
							'<div id="ea_infos">'.
							 	'<input id="media_name" type="text" name="name" value="'.$name.'" placeholder="Album Title" required /><br/>'.
							 	'<textarea class="base_txta" id="media_desc" name="description" wrap="soft" rows="10" placeholder="A description of your album">'.$description.'</textarea><br/>'.
							 	'<fieldset id="cats">'.
							 		'<legend>Categories</legend>';
			
			}elseif($part == 'm'){
			
				echo	 		'</fieldset>'.
						 	'</div>'.
						 '</div>'.
						 '<div>'.
						 	'<input class="button" type="submit" name="delete_pics" value="Delete Pictures" />'.
						 	'&nbsp;&nbsp;<a class="a_button" href="index.php?ns=media&ctl=albums&action=upload&id='.$id.'">Upload New Pictures</a>'.
						 	'&nbsp;&nbsp;<span class="indication">(Indication: pictures are ordered with their name)</span>'.
						 '</div>';
			
			}elseif($part == 'c'){
			
				echo '</div>';
			
			}
		
		}
		
		/**
			* Display a picture label
			*
			* @static
			* @access	public
			* @param	integer [$id] Picture id
			* @param	string [$permalink] Picture path
			* @param	string [$dirname] Album directory path
			* @param	string [$filename] Picture filename
			* @param	string [$name] Picture name
			* @param	string [$author_name] Author username
			* @param	string [$date] Upload date
			* @param	integer [$album] Album id
		*/
		
		public static function ma_picture_label($id, $permalink, $dirname, $filename, $name, $author_name, $date, $album){
		
			echo '<div class="album_label">'.
					'<label for="album_'.$id.'">'.
					 	'<div class="check_label">'.
					 		'<input id="album_'.$id.'" type="checkbox" name="picture_id[]" value="'.$id.'" />'.
					 	'</div>'.
					'</label>'.
				 	'<div class="content_label">'.
					 	'<div class="label_cover">'.
					 		'<a class="fancybox" href="'.PATH.$permalink.'" title="'.$name.'"><img src="'.PATH.$dirname.'150-'.$filename.'" alt="Picture" /></a>'.
					 	'</div>'.
					 	'<div class="user_info_side">'.
					 		'Name: <span class="lname"><input class="pic_input_text" type="text" name="pic'.$id.'" value="'.$name.'"/></span><br/>'.
						 	'Author: <span class="lauth">'.ucfirst($author_name).'</span><br/>'.
						 	'Creation: <span class="ldate">'.date('Y/m/d @ H:i', strtotime($date)).'</span><br/>'.
						 	'<a href="index.php?ns=media&ctl=albums&action=edit_image&id='.$album.'&pid='.$id.'">Edit</a> | '.
						 	'<a class="red" href="index.php?ns=media&ctl=albums&action=delete&id='.$id.'">Delete permanently</a>'.
						'</div>'.
					'</div>'.
				 '</div>';
		
		}
		
		/**
			* Display form to add pictures to an album
			*
			* @static
			* @access	public
			* @param	string [$name] Album name
			* @param	integer [$id] Album id
		*/
		
		public static function ma_upload($name, $id){
		
			echo '<div id="edit_media">'.
				 	'<h3>Upload new pictures for "'.$name.'"</h3>'.
				 	'<span class="indication">Remember, uploading a file with a name of an existing file will replace it</span><br/>'.
				 	'<br/>'.
				 	'<input type="file" name="picture1" /><br/>'.
				 	'<input type="file" name="picture2" /><br/>'.
				 	'<input type="file" name="picture3" /><br/>'.
				 	'<input type="file" name="picture4" /><br/>'.
				 	'<input type="file" name="picture5" /><br/>'.
				 	'<input type="file" name="picture6" /><br/>'.
				 	'<input type="file" name="picture7" /><br/>'.
				 	'<input type="file" name="picture8" /><br/>'.
				 	'<input type="file" name="picture9" /><br/>'.
				 	'<input type="file" name="picture10" /><br/>'.
				 	'<br/>'.
				 	'<span class="indication">'.'Please don\'t quit this page while uploading</span><br/>'.
				 	'<span class="indication">The maximum upload file size is set to '.HandleMedia::max_upload().'MB</span><br/>'.
				 	'<input type="hidden" name="album_id" value="'.$id.'" />'.
				 	'<input class="submit button button_publish" type="submit" name="upload" value="Add Pictures" />'.
				 '</div>';
		
		}
		
		/**
			* Display form to edit an image of an album
			*
			* @static
			* @access	public
			* @param	string [$name] Image name
			* @param	string [$dirname] Directory path of the file
			* @param	string [$fname] File name
			* @param	string [$description] Image description
			* @param	string [$permalink] Path to the file
			* @param	integer [$id] Album picture id
		*/
		
		public static function ma_edit_image($name, $dirname, $fname, $description, $permalink, $pid){
		
			echo '<div id="edit_media">'.
				 	'<input id="media_name" type="text" name="name" value="'.$name.'" placeholder="Image title" required /><br/>'.
				 	'<br/>'.
				 	'<img src="'.PATH.$dirname.'1000-'.$fname.'" alt="'.$name.'" title="'.$name.'" /><br/>'.
				 	'<select id="flip" name="flip">'.
				 		'<option value="no">Flip</option>'.
				 		'<option value="h">Horizontally</option>'.
				 		'<option value="v">Vertically</option>'.
				 	'</select><br/>'.
				 	'<select id="rotate" name="rotate">'.
				 		'<option value="no">Rotation</option>'.
				 		'<option value="90">90°</option>'.
				 		'<option value="180">180</option>'.
				 		'<option value="270">-90°</option>'.
				 	'</select><br/>'.
				 	'<br/>'.
				 	'<textarea id="media_desc" class="base_txta" rows="10" name="description" wrap="soft" placeholder="A little description of your photo">'.$description.'</textarea><br/>'.
				 	'<label for="fsize">Image url</label>: <input id="fsize" class="user_input_text" type="text" value="'.$permalink.'" readonly /> <span class="indication">(full size)</span><br/>'.
				 	'<label for="size15">Image url</label>: <input id="size15" class="user_input_text" type="text" value="'.$dirname.'150-'.$fname.'" readonly /> <span class="indication">(image with 150 pixels width)</span><br/>'.
				 	'<label for="size3">Image url</label>: <input id="size3" class="user_input_text" type="text" value="'.$dirname.'300-'.$fname.'" readonly /> <span class="indication">(image with 300 pixels width)</span><br/>'.
				 	'<label for="size1">Image url</label>: <input id="size1" class="user_input_text" type="text" value="'.$dirname.'1000-'.$fname.'" readonly /> <span class="indication">(image with 1000 pixels width)</span><br/>'.
				 	'<input class="submit button button_publish" type="submit" name="update_image" value="Update" />'.
				 	'<input type="hidden" name="pid" value="'.$pid.'" />'.
				 '</div>';
		
		}
	
	}

?>