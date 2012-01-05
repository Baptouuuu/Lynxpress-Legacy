<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011
		* @license		http://www.gnu.org/licenses/gpl.html GNU GPL V3
		* @package		Lynxpress
		* @subpackage	Templates
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
	
	namespace Templates\Bobcat;
	use \Library\Variable\Session as VSession;
	use \Site\Social as Social;
	use \Site\Html as MHtml;
	
	/**
		* Html
		*
		* Class contains basic views of the website
		*
		* @package		Templates
		* @subpackage	Main
		* @namespace	Main
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html{
	
		/**
			* Method that return the good path to include the header
			*
			* @static
			* @access	public
			* @return	string
		*/
		
		public static function header(){
		
			if(VSession::renderer() == 'mobile'){
			
				return 'html/header_mobile.php';
			
			}else{
			
				if(VSession::html5())
					return 'html/header_html5.php';
				else
					return 'html/header.php';
			
			}
		
		}
		
		/**
			* Method that return the good path to include the footer
			*
			* @static
			* @access	public
			* @return	string
		*/
		
		public static function footer(){
		
			if(VSession::renderer() == 'mobile'){
			
				return 'html/footer_mobile.php';
			
			}else{
			
				if(VSession::html5())
					return 'html/footer_html5.php';
				else
					return 'html/footer.php';
			
			}
		
		}
		
		/**
			* Method to display an article in a list view
			*
			* @static
			* @access	public
			* @param	string [$title] Post title
			* @param	string [$permalink] Post permalink
			* @param	array [$categories] Post categories
			* @param	string [$date] Post creation date
			* @param	string [$author] Post author public name
			* @param	string [$content] Post content
		*/
		
		public static function listing_article($title, $permalink, $categories, $date, $author, $content){
		
			if(VSession::renderer() == 'mobile'){
			
				echo '<li>'.
						'<h4><a href="'.PATH.'?ctl=posts&news='.$permalink.'"><span class="la_title">'.$title.'</span><img class="follow_link" src="images/follow_link.png" alt=""></a></h4>'.
					'</li>';
			
			}else{
			
				if(VSession::html5())
					echo '<section class="post">';
				else
					echo '<li>';
				
				echo	'<h3><a href="'.PATH.'?ctl=posts&news='.$permalink.'" title="View '.$title.'">'.$title.'</a></h3>';
				
				if(VSession::html5()){
				
					echo '<details>'.
						 	'<summary>'.date('D M \t\h\e dS', strtotime($date)).'</summary>'.
						 	'Published by '.$author.' in '.implode(' | ', $categories).
						 '</details>';
				
				}else{
				
					echo '<div id="details">'.date('D M \t\h\e dS', strtotime($date)).' by '.$author.'</div>';
				
				}
							
				if(Vsession::html5())
					echo '<article>';
				else
					echo '<div class="listing_news">';
				
				
				echo		$content.' ... <a class="read_more" href="'.PATH.'?ctl=posts&news='.$permalink.'" title="Lire la suite">Read more</a>';
				
				if(Vsession::html5())
					echo '</article></section>';
				else
					echo '</div></li>';
			
			}
		
		}
		
		/**
			* Method to display a whole post
			*
			* @static
			* @access	public
			* @param	string [$title] Post title
			* @param	string [$date] Post creation date
			* @param	string [$author] Post author public name
			* @param	array [$categories] Post categories
			* @param	string [$content] Post content
			* @param	array [$tags] Post tags
			* @param	string [$link] Post permalink
			* @param	string [$updated] "yes" if the post has been updated
			* @param	string [$u_author] Post update author public name
		*/
		
		public static function article_alone($title, $date, $author, $categories, $content, $tags, $link, $updated, $u_author = null){
		
			if(VSession::renderer() == 'mobile'){
				
				echo '<h4>'.$title.'</h4>'.
					'<div id="news_info">Published the '.date('F d, Y', strtotime($date)).' by '.$author.'</div>'.
					'<div id="news_content">'.
						$content.
					'</div>';
				
			}else{
			
				$edited = null;
				if($updated == 'yes' && $author != $u_author)
					$edited = ' Edited by <a href="'.PATH.'?ctl=author&author='.$u_author.'&rel=author" title="Profile">'.$u_author.'</a>';
				
				echo '<h3>'.$title.'</h3>';
				
				if(Vsession::html5()){
				
					echo '<details><summary>Published the <time datetime="'.$date.'" pubdate>'.date('F d, Y', strtotime($date)).'</time> by <a href="'.PATH.'?ctl=author&author='.$author.'&rel=author" title="Profile">'.$author.'</a>, Categories: '.implode(' | ', $categories).$edited.'</summary></details><article>';
				
				}else{
				
					echo '<div id="news_info">Published the '.$date.' by <a href="'.PATH.'?ctl=author&author='.$author.'&rel=author" title="Profile">'.$author.'</a>, Categories: '.implode(' | ', $categories).$edited.'</div><div id="news_content">';
				
				}
				
				echo $content;
				
				if(Vsession::html5())
					echo '</article>';
				else
					echo '</div>';
					
				Social::share($title, $link);
				echo '<br/>'.
					 '<span id="tags">Tags: '.implode(', ', $tags).'</span>';
				
			}
		
		}
		
		/**
			* Method to display message when there's no content
			*
			* @static
			* @access	public
			* @param	string [$message] Message to display when there's no content
		*/
		
		public static function no_content($message){
		
			if(Vsession::html5())
				echo '<section id="no_content">'.$message.'</section><section class="pb_img"><img src="images/problem.png" title="There is a problem" /></section>';
			else
				echo '<li id="no_content">'.$message.'</li><li class="pb_img"><img src="images/problem.png" title="There is a problem" /></li>';
		
		}
		
		/**
			* Method to display a comment form 
			*
			* @static
			* @access	public
			* @param	string [$name] Comment name
			* @param	string [$email] Comment email
			* @param	string [$content] Comment content
			* @param	boolean [$error_name]
			* @param	boolean [$error_email]
			* @param	boolean [$error_content]
			* @param	boolean [$error_captcha]
			* @param	string [$form_side_image]
			* @param	int [$n1]
			* @param	int [$n2]
			* @param	string [$result]
		*/
		
		public static function comment_form($name, $email, $content, $error_name, $error_email, $error_content, $error_captcha, $form_side_image, $n1, $n2, $result){
		
			echo '<div id="respond">'.
					'<form method="post" action="#respond" accept-charset="utf-8">'.
						'<input class="input_text '.(($error_name)?'wrong':'').'" id="respond_name" type="text" name="respond_name" value="'.$name.'" placeholder="Your name" required /><label for="respond_name">Name *</label><br/>'.
						'<input class="input_text '.(($error_email)?'wrong':'').'" id="respond_email" type="email" name="respond_email" value="'.$email.'" placeholder="lynx@press.org" required /><label for="respond_email">E-mail *</label><br/>'.
						'<textarea id="respond_content'.(($error_content)?'_wrong':'').'" name="respond_content" wrap="soft" placeholder="Want to say something?" required >'.$content.'</textarea><br/>'.
						'How many does '.$n1.' + '.$n2.'?&nbsp;&nbsp;&nbsp;<input id="captcha" class="input_text '.(($error_captcha)?'wrong':'').'" type="number" name="number" max="200" min="0" required/><br/>'.
						'<input type="hidden" name="result" value="'.$result.'" />'.
						'<input id="respond_submit" type="submit" name="submit_comment" value="Submit Comment" />'.
					'</form>'.
				'</div>';
		
		}
		
		/**
			* Method to display message after comment form has been submitted
			*
			* @static
			* @access	public
			* @param	boolean [$bool]
		*/
		
		public static function submitted_form($bool){
		
			echo '<div id="respond">';
			
			if($bool)
				echo '<span id="respond_well">Comment submitted, waiting for moderation</span>';
			else
				echo '<span id="respond_error">Impossible to submit your comment</span>';
				
			echo '</div>';
		
		}
		
		/**
			* Method to display a comment
			*
			* @static
			* @access	public
			* @param	integer [$id] Comment id
			* @param	string [$name] Comment name
			* @param	string [$date] Comment creation date
			* @param	string [$link] Comment permalink
			* @param	string [$content] Comment content
		*/
		
		public static function comment($id, $name, $date, $link, $content){
		
			echo '<div class="comments" id="comment_'.$id.'">'.
					'<div class="comment_info">'.
						'By <a style="text-decoration: none;" href="#comment_'.$id.'">'.$name.'</a> '.
						'the '.date('d/m/Y @ H:i', strtotime($date)).
						' | <a href="'.PATH.'?'.$link.'&respond_to='.$name.'#respond" title="Respond to '.$name.'">Respond</a>'.
					'</div>'.
					'<div class="comment_content">'.$content.'</div>'.
				 '</div>';
		
		}
		
		/**
			* Display a message when comments are closed<br/>
			* this function is not called in Comments class<br/>
			* but has to be called in controllers
			*
			* @static
			* @access	public
		*/
		
		public static function comment_closed(){
		
			echo '<div id="respond">'.
				 	'<span id="closed_comment">Comments are currently closed</span>'.
				 '</div>';
		
		}
		
		/**
			* Display a header bar for albums
			*
			* @static
			* @access	public
			* @param	string [$add] Additional text for the header bar
		*/
		
		public static function header_albums($add){
		
			echo '<h3 id="header_album">Albums'.((!empty($add))?' > '.ucwords($add):'').'</h3>';
		
		}
		
		/**
			* Display a label for an album in a listing
			*
			* @static
			* @access	public
			* @param	integer [$id] Album id
			* @param	string [$title] Album name
			* @param	string [$link] Album directory path
			* @param	string [$description] Album description
		*/
		
		public static function album_label($id, $title, $link, $description){
		
			if(Vsession::html5()){
			
				echo '<figure class="album">'.
					 	'<a href="'.PATH.'?ctl=albums&album='.$id.'"><img src="'.$link.'cover.png" /></a>'.
					 	'<figcaption>'.
					 		'<a href="'.PATH.'?ctl=albums&album='.$id.'">'.$title.'</a>'.
					 	'</figcaption>'.
					 '</figure>';
			
			}else{
			
				echo '<li>'.
					 	'<a href="'.PATH.'?ctl=albums&album='.$id.'">'.
					 		'<img src="'.$link.'cover.png" /><br/>'.
					 		$title.
					 	'</a>'.
					 '</li>';
			
			}
		
		}
		
		/**
			* Display additional informations about an album
			*
			* @static
			* @access	public
			* @param	integer [$id] Album id
			* @param	string [$author] Album author public name
			* @param	string [$date] Album creation date
			* @param	string [$description] Album description
			* @param	string [$title] Album title
			* @param	string [$link] Album permalink
			
		*/
		
		public static function album_details($id, $author, $date, $description, $title, $link){
		
			if(VSession::renderer() != 'mobile'){
			
				if(VSession::html5() && VSession::renderer() != 'gecko'){
				
					echo '<details id="album_details" open>'.
						 	'<summary>Created by '.$author.' the <time datetime="'.$date.'" pubdate>'.date('M d, Y @ H:i', strtotime($date)).'</time></summary>'.
						 	'<p>'.
						 		$description.
						 	'</p>'.
						 	'<span id="view_comments"><a href="'.PATH.'?ctl=albums&album='.$id.'&comments=view">View Comments</a></span><br/><br/>'.
						 '</details>';
				
				}else{
				
					echo '<div id="album_details">'.
						 	'Created by '.$author.' the '.date('M d, Y @ H:i', strtotime($date)).
						 	'<p>'.
						 		$description.
						 	'</p>'.
						 	'<span id="view_comments"><a href="'.PATH.'?ctl=albums&album='.$id.'&comments=view">View Comments</a></span><br/><br/>'.
						 '</div>';
				
				}
				
				Social::share($title, $link);
			
			}
		
		}
		
		/**
			* Display a picture
			*
			* @static
			* @access	public
			* @param	string [$mini] Picture thumb path
			* @param	string [$full] Picture path
			* @param	string [$name] Picture name
			* @param	string [$desc] Picture description
		*/
		
		public static function album_picture($mini, $full, $name, $desc){
		
			echo '<li>'.
				 	'<a href="'.$full.'" rel="fancybox" title="'.$name.' | '.$desc.'">'.
				 		'<img src="'.$mini.'" alt="'.$name.'" />'.
				 	'</a>'.
				 '</li>';
		
		}
		
		/**
			* Display a header bar for videos
			*
			* @static
			* @access	public
			* @param	string [$add] Additional text for the header bar
		*/
		
		public static function header_videos($add){
		
			echo '<h3 id="header_video">Videos'.((!empty($add))?' > '.ucwords($add):'').'</h3>';
		
		}
		
		/**
			* Display a video with its informations
			*
			* @static
			* @access	public
			* @param	string [$name] Video name
			* @param	string [$author] Video author public name
			* @param	string [$link] Video path
			* @param	string [$embed_code] Attached video embed code
			* @param	string [$description] Video description
			* @param	string [$date] Video upload date
		*/
		
		public static function video($name, $author, $link, $embed_code, $description, $date){
		
			if(VSession::html5()){
			
				echo '<section class="video">'.
					 	'<video width="590" src="'.$link.'" controls preload="auto">'.
					 		$embed_code.													//in case the browser doesn't support video tag yet
					 	'</video>'.
					 	'<details>'.
					 		'<summary>"'.$name.'" is a video made by '.$author.'</summary>'.
					 		'<p>(uploaded the <time datetime="'.$date.'" pubdate>'.date('M d, Y @ H:i', strtotime($date)).'</time>)</p>'.
					 		'<p>'.
					 			$description.
					 		'</p>'.
					 	'</details>'.
					 '</section>';
			
			}else{
			
				echo '<li class="video">'.
					 	'<div class="video_element">';
					 		
							if(!empty($embed_code)){
							
								echo $embed_code;
							
							}else{
							
								echo '<p>'.
									 	'Please get a newer browser in order to see this video.<br/>'.
									 	'Or contact the website owner to ask him to link a video to this one.<br/>'.
									 	'Still, you can download the file <a href="'.$link.'" title="Dowload '.$name.'">&gt;here&lt;</a>.'.
									 '</p>';
							
							}
				
				echo 	'</div>'.
					 	'<div class="video_details">'.
					 		'"'.$name.'" is a video made by '.$author.' (uploaded the '.date('M d, Y @ H:i', strtotime($date)).')'.
					 		'<p>'.
					 			$description.
					 		'</p>'.
					 	'</div>'.
					 '</li>';
			
			}
		
		}
		
		/**
			* Display a header bar for authors
			*
			* @static
			* @access	public
			* @param	string [$add] Additional text for header bar
		*/
		
		public static function header_authors($add){
		
			echo '<h3 id="header_author">Website authors'.((!empty($add))?' > '.ucwords($add):'').'</h3>';
		
		}
		
		/**
			* Display an author card
			*
			* @static
			* @access	public
			* @param	string [$publicname] User public name
			* @param	string [$email] User email
			* @param	string [$website] User website
			* @param	string [$msn] User msn address
			* @param	string [$twitter] User twitter url
			* @param	string [$facebook] User facebokk url
			* @param	string [$google] User google+ url
			* @param	string [$avatar] User avatar path
			* @param	string [$bio] User description
		*/
		
		public static function author($publicname, $email, $website, $msn, $twitter, $facebook, $google, $avatar, $bio){
		
			if(VSession::html5())
				echo '<section class="author">';
			else
				echo '<li class="author">';
			
			echo '<h3>'.$publicname.'</h3>'.
			 	 '<div class="author_bio">'.
			 	 	nl2br($bio).
			 	 '</div>'.
			 	 '<div class="author_links">';
				
				 if(!empty($twitter))
				 	echo '<a class="author_twitter" href="'.$twitter.'" target="_blank">Twitter</a>';
				 else
					echo '<a class="author_twitter" href="http://twitter.com" target="_blank">Twitter</a>';
				
				 if(!empty($facebook))
					echo '<a  class="author_facebook" href="'.$facebook.'" target="_blank">Facebook</a>';
				 else
					echo '<a  class="author_facebook" href="http://facebook.com" target="_blank">Facebook</a>';
				
				 if(!empty($google))
					echo '<a  class="author_google" href="'.$google.'?rel=me" target="_blank">Google+</a>';
				 else
					echo '<a class="author_google" href="http://plus.google.com" target="_blank">Google+</a>';
				 	
			echo 	'</div>';
			
			if(VSession::html5())
				echo '</section>';
			else
				echo '</li>';
		
		}
		
		/**
			* Display a header bar for contact page
			*
			* @static
			* @access	public
		*/
		
		public static function header_contact(){
		
			echo '<h3 id="header_contact">Contact</h3>';
		
		}
		
		/**
			* Display contact form
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function contact($part){
		
			if(VSession::html5()){
			
				if($part == 'o'){
				
					echo '<section id="contact_form">'.
							'<select id="receiver" name="receiver">'.
								'<option value="'.WS_EMAIL.'">Select an author</option>';
				
				}elseif($part == 'c'){
				
					echo	'</select>'.
							'<input class="input" type="email" name="c_email" placeholder="lynx@press.org" required /><br/>'.
							'<input class="input" type="text" name="c_object" placeholder="Object" required /><br/>'.
							'<textarea class="input" wrap="soft" name="c_content" placeholder="Your message" required></textarea><br/>'.
							'<input id="sc" type="submit" name="submit" value="Submit" />'.
						 '</section>';
				
				}
			
			}else{
			
				if($part == 'o'){
				
					echo '<div id="contact_form">'.
							'<select id="receiver" name="receiver">'.
								'<option value="'.WS_EMAIL.'">Select an author</option>';
				
				}elseif($part == 'c'){
				
					echo	'</select>'.
							'<input class="input" type="email" name="c_email" value="lynx@press.org" onfocus="if ( this.value == this.defaultValue ) this.value = \'\';" onblur="if ( this.value == \'\' ) this.value = this.defaultValue" required /><br/>'.
							'<input class="input" type="text" name="c_object" value="Object" onfocus="if ( this.value == this.defaultValue ) this.value = \'\';" onblur="if ( this.value == \'\' ) this.value = this.defaultValue" required /><br/>'.
							'<textarea class="input" wrap="soft" name="c_content" onfocus="if ( this.value == this.defaultValue ) this.value = \'\';" onblur="if ( this.value == \'\' ) this.value = this.defaultValue" required>Your message</textarea><br/>'.
							'<input id="sc" type="submit" name="submit" value="Submit" />'.
						 '</div>';
				
				}
			
			}
		
		}
		
		/**
			* Display message after contact form has been submitted
			*
			* @static
			* @access	public
			* @param	mixed [$result]
		*/
		
		public static function contact_submitted($result){
		
			MHtml::html5('o', 'id="contact_form">');
			
			if($result === 'false email')
				echo '<span id="c_error">Your e-mail is not a valid one</span>';
			elseif($result === false)
				echo '<span id="c_error">You need to fill all inputs</span>';
			else
				echo '<span id="c_well">Your message has been sent</span>';
			
			MHtml::html5('c');
		
		}
		
		/**
			* Display a header for links page
			*
			* @static
			* @access	public
		*/
		
		public static function header_links(){
		
			
		
		}
		
		/**
			* Method to display a related link
			*
			* @static
			* @access	public
			* @param	string [$name] Link name
			* @param	string [$link] Link url
			* @param	string [$rss_link] Link RSS url
			* @param	string [$notes] Link notes
			* @param	integer [$priority] Link priority level
		*/
		
		public static function related_link($name, $link, $rss_link, $notes, $priority){
		
			if(VSession::html5()){
			
				echo '<section class="link">'.
						'<h3>'.$name.'</h3>'.
						'<details open>'.
							'<summary>Website links</summary>'.
							'<p>'.
								'Website: <a href="'.$link.'">'.$link.'</a><br/>'.
								'Feed: <a href="'.$rss_link.'">'.$rss_link.'</a>'.
							'</p>'.
						'</details>'.
						'<section class="description">'.
							nl2br($notes).
						'</section>'.
					'</section>';
			
			}
		
		}
		
		/**
			* Visual for 404 page
			*
			* @static
			* @access	public
		*/
		
		public static function _404(){
		
			echo '<div id="msg404">'.
				 	'<img src="images/404.png" alt="" title="404 Page Not Found" />'.
				 	'<p>'.
				 		'404 - Page Not Found'.
				 	'<p>'.
				 '</div>';
		
		}
		
		/**
			* Method to display navigation bar
			*
			* @static
			* @access	public
			* @param	integer [$p] Actual page
			* @param	integer [$max] Maximum of available pages
			* @param	string [$link] Complement in the url
		*/
		
		public static function navigation($p, $max, $link){
		
			if(Vsession::html5()){
			
				if($p < $max){
			
					echo '<div id="prev">'.
							'<a href="'.PATH.'?'.$link.'p='.($p+1).'">Previous Page</a>'.
						'</div>';
			
				}
				
				if($p > 1){
			
					echo '<div id="next">'.
							'<a href="'.PATH.'?'.$link.'p='.($p-1).'">Next Page</a>'.
						'</div>';
			
				}
				
			}else{
				
				echo '<ul id="nav">';
				
				if($p < $max){
			
					echo '<li>'.
							'<div id="prev">'.
								'<a href="'.PATH.'?'.$link.'p='.($p+1).'">Previous Page</a>'.
							'</div>'.
						'</li>';
			
				}
				
				if($p > 1){
			
					echo '<li>'.
							'<div id="next">'.
								'<a href="'.PATH.'?'.$link.'p='.($p-1).'">Next Page</a>'.
							'</div>'.
						'</li>';
			
				}
				
				echo '</ul>';
				
			}
		
		}
	
	}

?>