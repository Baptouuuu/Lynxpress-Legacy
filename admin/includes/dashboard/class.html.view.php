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
	
	namespace Admin\Dashboard;
	
	/**
		* Html Dashboard
		*
		* Contains all html for dashboard class
		*
		* @package		Administration
		* @subpackage	Views
		* @namespace	Dashboard
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html{
	
		/**
			* Display the menu of dashboard page
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php">Dashboard</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display activity widget structure
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
		*/
		
		public static function widget_activity($part){
		
			if($part == 'o'){
			
				echo '<div id="activity" class="widget">'.
				 	'<div class="widget_title">'.
				 		'Administration Activity'.
				 	'</div>'.
				 	'<div id="activity_content">';
			
			}elseif($part == 'c'){
			
				echo	'</div>'.
					 '</div>';
			
			}	
		
		}
		
		/**
			* Display an activity line
			*
			* @static
			* @access	public
			* @param	string [$name] Username
			* @param	string [$data] Action message logged
			* @param	string [$date] Date of the action
		*/
		
		public static function activity($name, $data, $date){
		
			echo '<p>'.$name.' '.$data.' ('.date('Y/m/d @ H:i', strtotime($date)).')</p>';
		
		}
		
		/**
			* Display comments widget structure
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
		*/
		
		public static function widget_comments($part){
		
			if($part == 'o'){
			
				echo '<div id="recent_comments" class="widget">'.
						'<div class="widget_title">'.
							'Comments'.
						'</div>'.
						'<div id="comments_list">';
			
			}elseif($part == 'c'){
			
				echo 		'<div class="text_right">'.
					 			'<a class="view_all" href="index.php?ns=comments&ctl=manage&comment_status=pending" title="View all pending comments">View All</a>'.
					 		'</div>'.
					 	'</div>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display a comment line
			*
			* @static
			* @access	public
			* @param	integer [$id] Comment id
			* @param	string [$name] Comment name
			* @param	string [$rel_permalink] Element permalink
			* @param	string [$rel_title] Element name
			* @param	string [$content] Comment content
		*/
		
		public static function comment($id, $name, $rel_permalink, $rel_title, $content){
		
			echo '<div class="comments_item">'.
					'<h4><span class="comments_user">From '.$name.' on</span> <a target="_blank" href="'.PATH.'?'.$rel_permalink.'">'.$rel_title.'</a></h4>'.
					'<p>'.htmlspecialchars($content).'</p>'.
					'<p class="action">'.
						'<span class="green"><a href="index.php?ns=comments&ctl=manage&action=approve&comment_id='.$id.'">Approve</a></span> | '.
						'<span class="orange"><a href="index.php?ns=comments&ctl=manage&action=unapprove&comment_id='.$id.'">Unapprove</a></span> | '.
						'<span class="red"><a href="index.php?ns=comments&ctl=manage&action=spam&comment_id='.$id.'">Spam</a></span> | '.
						'<span class="red"><a href="index.php?ns=comments&ctl=manage&action=trash&comment_id='.$id.'">Trash</a></span>'.
					'</p>'.
				'</div>';
		
		}
		
		/**
			* Display quickpress widget structure
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
		*/
		
		public static function widget_quickpress($part){
		
			if($part == 'o'){
			
				echo '<div id="quick_press" class="widget">'.
						'<div class="widget_title">'.
							'New Post'.
						'</div>'.
						'<form method="post" action="index.php?ns=posts&ctl=add">'.
							'<h5><label for="qp_title">Title</label></h5> <input id="qp_title" class="input_text" type="text" name="title" value="" /><br/>'.
							'<h5><label for="qp_content">Content</label></h5> <textarea id="qp_content" class="input_text" name="content" rows="4" cols="70" wrap="soft"></textarea>'.
							'<h5><label for="qp_tags">Tags</label></h5> <input id="qp_tags" class="input_text" type="text" name="post_tags" value="" /><br />'.
							'<input type="hidden" name="action" value="to_insert" />'.
							'<input type="hidden" name="post_allow_comment" value="open" />'.
							'<input type="hidden" name="action" value="to_insert" />'.
							'<h5><label for="qp_categories">Categories</label></h5>'.
							'<div id="qp_categories">';
			
			}elseif($part == 'c'){
			
				echo		'</div>'.
							'<div id="submit">'.
								'<input class="button" type="submit" name="save_draft" value="Save Draft" />'.
								'<input class="button" type="reset" name="reset" value="Reset" />'.
								'<input class="button button_publish" type="submit" name="publish" value="Publish" />'.
							'</div>'.
						'</form>'.
					'</div>';
			
			}
		
		}
		
		/**
			* Display a category input
			*
			* @static
			* @access	public
			* @param	integer [$id] Post category id
			* @param	string [$name] Post category name
		*/
		
		public static function category($id, $name){
		
			echo '<input id="cat'.$id.'" type="checkbox" name="categories[]" value="'.$id.'" /><label for="cat'.$id.'">'.ucwords($name).'</label> ';
		
		}
		
		/**
			* Display draft widget structure
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
		*/
		
		public static function widget_draft($part){
		
			if($part == 'o'){
			
				echo '<div id="recent_drafts" class="widget">'.
						'<div class="widget_title">'.
							'Drafts'.
						'</div>'.
						'<div id="drafts_list">'.
							'<ul>';
			
			}elseif($part == 'c'){
			
				echo 		'</ul>'.
							'<div class="text_right">'.
								'<a class="view_all" href="index.php?ns=posts&ctl=manage&post_status=draft" title="View all drafts">View All</a>'.
							'</div>'.
						'</div>'.
					'</div>';
			
			}
		
		}
		
		/**
			* Display a draft line
			*
			* @static
			* @access	public
			* @param	integer [$id] Post id
			* @param	string [$title] Post title
			* @param	string [$date] Post date
			* @param	string [$content] Post content
		*/
		
		public static function draft($id, $title, $date, $content){
		
			echo '<li>'.
					'<h4>'.
						'<a href="index.php?ns=posts&ctl=add&action=edit&id='.$id.'">'.$title.'</a>'.
						'<abbr title="'.date('j/m/Y H:i', strtotime($date)).'"> '.date('F j, Y', strtotime($date)).'</abbr>'.
					'</h4>'.
					'<p>'.htmlspecialchars(substr($content, 0, 200)).'...</p>'.
				'</li>';
		
		}
	
	}

?>