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
	
	namespace Admin\Timeline;
	use \Admin\Html\Html as Master;
	
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
	
	abstract class Html extends Master{
	
		/**
			* Display the menu of dashboard page
			*
			* @static
			* @access	public
		*/
		
		public static function tm_menu(){
		
			echo '<div id="menu">'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=timeline&ctl=manage">Timeline</a></span>'.
				 	'<span class="menu_item"><a href="index.php?ns=timeline&ctl=settings">Settings</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display followed websites structure
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function tm_websites($part){
		
			if($part == 'o'){
			
				echo '<section id="websites">'.
					 	'<ul>';
			
			}elseif($part == 'c'){
			
				echo	'</ul>'.
					 '</section>';
			
			}
		
		}
		
		/**
			* Display a website in the list
			*
			* @static
			* @access	public
			* @param	integer [$key] Website key in preferences array
			* @param	string [$name] Website name
			* @param	string [$url] Website url
		*/
		
		public static function tm_website($key, $name, $url){
		
			echo '<li>'.
					'<a href="index.php?ns=timeline&ctl=manage&website='.$key.'" title="View '.$name.' timeline ('.$url.')">'.
						$name.
					'</a>'.
				 '</li>';
		
		}
		
		/**
			* Display timeline structure
			*
			* @static
			* @access	public
			* @param	string [$part]
			* @param	string [$website] Website name currently viewed
		*/
		
		public static function tm_timeline($part, $website = ''){
		
			if($part == 'o'){
			
				echo '<section id="timeline">'.
						'<h3>'.$website.'</h3>'.
						'<ul>';
			
			}elseif($part == 'c'){
			
				echo	'</ul>'.
					 '</section>';
			
			}
		
		}
		
		/**
			* Display a website post in the timeline
			*
			* @static
			* @access	public
			* @param	string [$website] Website key in preferences array
			* @param	string [$title] Post title
			* @param	string [$permalink] Post slug
			* @param	string [$date] Post publication date
		*/
		
		public static function tm_post($website, $title, $permalink, $date){
		
			echo '<li>'.
					'<div class="title">'.
						$title.
					'</div>'.
					'<div class="infos">'.
						'<div class="pubdate">'.
							date('d/m/Y @ H:i', strtotime($date)).
						'</div>'.
						'<div class="view">'.
							'<a href="index.php?ns=timeline&ctl=post&website='.$website.'&slug='.$permalink.'">'.
								'view'.
							'</a>'.
						'</div>'.
					'</div>'.
				 '</li>';
		
		}
		
		/**
			* Display a list of periods to show
			*
			* @static
			* @access	public
			* @param	integer [$website] Website key
			* @param	string [$last_visit]
		*/
		
		public static function tm_periods($website, $last_visit){
		
			echo '<section id="periods">'.
					'<ul>'.
						'<li>'.
							'<a href="index.php?ns=timeline&ctl=manage&website='.$website.'&since='.$last_visit.'" title="View posts since your last visit">'.
								'Since your last login'.
							'</a>'.
						'</li>'.
						'<li>'.
							'<a href="index.php?ns=timeline&ctl=manage&website='.$website.'&since='.date('Y-m-d', (time()-(3600*24))).'" title="View posts for past day">'.
								'One Day'.
							'</a>'.
						'</li>'.
						'<li>'.
							'<a href="index.php?ns=timeline&ctl=manage&website='.$website.'&since='.date('Y-m-d', (time()-(3600*24*7))).'" title="View posts for the past week">'.
								'One Week'.
							'</a>'.
						'</li>'.
						'<li>'.
							'<a href="index.php?ns=timeline&ctl=manage&website='.$website.'&since='.date('Y-m-d', (time()-(3600*24*14))).'" title="View posts for the past two weeks">'.
								'Two Weeks'.
							'</a>'.
						'</li>'.
						'<li>'.
							'<a href="index.php?ns=timeline&ctl=manage&website='.$website.'&since='.date('Y-m-d', (time()-(3600*24*30))).'" title="View posts for the past month">'.
								'One Month'.
							'</a>'.
						'</li>'.
					'</ul>'.
				 '</section>';
							
		
		}
		
		/**
			* Message displayed if no post retrieved
			*
			* @static
			* @access	public
			* @param	string [$since]
		*/
		
		public static function tm_no_post($since){
		
			echo '<li>'.
					'<span class="no_content">'.
						'No posts published since '.date('d/m/Y', strtotime($since)).
					'</span>'.
				 '</li>';
		
		}
		
		/**
			* Display the menu of timeline settings page
			*
			* @static
			* @access	public
		*/
		
		public static function sg_menu(){
		
			echo '<div id="menu">'.
				 	'<span class="menu_item"><a href="index.php?ns=timeline&ctl=manage">Timeline</a></span>'.
				 	'<span id="menu_selected" class="menu_item"><a href="index.php?ns=timeline&ctl=settings">Settings</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display a form to add a new site to follow
			*
			* @static
			* @access	public
		*/
		
		public static function sg_add_form(){
		
			echo '<input class="input tm_form" type="text" name="title" placeholder="Website title" required />'.
				 '<input class="input tm_form" type="url" name="url" placeholder="http://blog.lynxpress.org/" required />'.
				 '<input class="button button_publish submit" type="submit" name="add" value="Add To Timeline" />';
		
		}
		
		/**
			* Display a website label for setting page
			*
			* @static
			* @access	public
			* @param	integer [$id]
			* @param	string [$title]
			* @param	string [$url]
		*/
		
		public static function sg_label($id, $title, $url){
		
			echo '<div class="tm_label">'.
				 	'<div class="delete">'.
				 		'<a href="index.php?ns=timeline&ctl=settings&action=remove&id='.$id.'">x</a>'.
				 	'</div>'.
				 	'<div class="content_label">'.
				 		'Title: <span class="tmtitle">'.$title.'</span><br/>'.
				 		'Url: <span class="tmurl"><a href="'.$url.'" target="_blank">'.$url.'</a></span>'.
				 	'</div>'.
				 '</div>';
		
		}
		
		/**
			* Display the menu of timeline post page
			*
			* @static
			* @access	public
			* @param	string [$website] Website name of the watched post
		*/
		
		public static function pt_menu($website){
		
			echo '<div id="menu">'.
				 	'<span class="menu_item"><a href="index.php?ns=timeline&ctl=manage">Timeline</a></span>'.
				 	'<span id="menu_selected" class="menu_item"><a href="#">'.$website.'</a></span>'.
				 	'<span class="menu_item"><a href="index.php?ns=timeline&ctl=settings">Settings</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display retrieved post
			*
			* @static
			* @access	public
			* @param	string [$title]
			* @param	string [$content]
			* @param	string [$date]
			* @param	string [$author]
			* @param	string [$website] Website title
			* @param	string [$url] Full url to the post
		*/
		
		public static function pt_post($title, $content, $date, $author, $website, $url){
		
			echo '<section id="tm_post">'.
				 	'<h2>'.$title.'</h2>'.
				 	'<details>'.
				 		'<summary>Published by <span>'.$author.'</span> the '.date('d/m/Y @ H:i', strtotime($date)).'</summary>'.
				 	'</details>'.
				 	'<article>'.nl2br($content).'</article>'.
				 	'<div id="ext_link">'.
				 		'<a class="a_button" href="'.$url.'" target="_blank">View this post on '.$website.'</a>'.
				 	'</div>'.
				 '</section>';
		
		}
		
		/**
			* Display comments list structure
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function pt_comments($part){
		
			if($part == 'o'){
			
				echo '<aside id="tm_comments">'.
					 	'<h3>Comments</h3>';
				
				self::form('o', 'post', '#');
				
				echo 	'<div id="form">'.
							'<textarea name="content" class="base_txta" wrap="soft" placeholder="Your comment" required></textarea><br/>'.
							'<input class="submit button button_publish" type="submit" name="submit" value="Submit" />'.
						'</div>';
				
				self::form('c');
				
				echo	'<ul>';
			
			}elseif($part == 'c'){
			
				echo 	'</ul>'.
					 '</aside>';
			
			}
		
		}
		
		/**
			* Display a comment
			*
			* @static
			* @access	public
			* @param	string [$name]
			* @param	string [$content]
			* @param	string [$date]
		*/
		
		public static function pt_comment($name, $content, $date){
		
			echo '<li>'.
				 	'<details>'.
				 		'<summary>By '.$name.' the '.date('d/m/Y @ H:i', strtotime($date)).'</summary>'.
				 	'</details>'.
				 	'<p class="com_content">'.
				 		nl2br($content).
				 	'</p>'.
				 '</li>';
		
		}
	
	}

?>