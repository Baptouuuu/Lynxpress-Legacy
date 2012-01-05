<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011
		* @license		http://www.gnu.org/licenses/gpl.html GNU GPL V3
		* @package		Lynxpress
		* @subpackage	Site
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
	
	namespace Site;
	use \Library\Database\Database as Database;
	use \Library\Models\User as User;
	use \Library\Variable\Get as VGet;
	use SimpleXMLElement;
	
	/**
		* XML
		*
		* Permits to build the rss feed and the sitemap
		*
		* @package		Site
		* @subpackage	Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
	*/
	
	class XML{
	
		private $_db = null;
		private $_type = null;
		private $_content = array();
		private $_xml = null;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	string [$type]
		*/
		
		public function __construct($type = 'rss'){
		
			self::check_ext();
			
			$this->_db =& Database::load();
			
			if(in_array($type, array('rss', 'sitemap')))
				$this->_type = $type;
			else
				$this->_type = 'rss';
			
			$this->get_content();
			$this->build_xml();
		
		}
		
		/**
			* Retrieve content depending of xml type wanted
			*
			* @access	private
		*/
		
		private function get_content(){
		
			if($this->_type == 'rss'){
		
				$to_read['table'] = 'post';
				$to_read['columns'] = array('POST_ID AS guid', 'post_title AS title', 'post_content AS description', 'post_date AS pubDate', 'post_author AS author', 'post_permalink AS link');
				$to_read['condition_columns'][':s'] = 'post_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				
				if(VGet::cat()){
				
					$to_read['condition_types'][':cat'] = 'AND';
					$to_read['condition_columns'][':cat'] = 'post_category';
					$to_read['condition_select_types'][':cat'] = 'LIKE';
					$to_read['condition_values'][':cat'] = '%'.VGet::cat().'%';
					$to_read['value_types'][':cat'] = 'str';
				
				}
				
				$this->_content = $this->_db->read($to_read);
				
				if(!empty($this->_content)){
				
					foreach ($this->_content as &$value) {
					
						$user = new User($value['author']);
						$value['author'] = $user->_publicname;
						
						$value['title'] = htmlspecialchars($value['title']);
						$value['description'] = htmlspecialchars(substr($value['description'], 0, 200));
					
					}
				
				}
			
			}elseif($this->_type == 'sitemap'){
			
				$to_read['table'] = 'post';
				$to_read['columns'] = array('post_permalink AS SPLink', 'post_date AS SPPubDate');
				$to_read['condition_columns'][':s'] = 'post_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				
				$this->_content = $this->_db->read($to_read);
				$to_read = null;
				
				$to_read['table'] = 'media';
				$to_read['columns'] = array('MEDIA_ID AS SMLink', 'media_date AS SMPubDate');
				$to_read['condition_columns'][':t'] = 'media_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'album';
				$to_read['value_types'][':t'] = 'str';
				$to_read['condition_types'][':s'] = 'AND';
				$to_read['condition_columns'][':s'] = 'media_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				
				$media = $this->_db->read($to_read);
				
				if(!empty($media)){
				
					foreach ($media as $value)
						array_push($this->_content, $value);
				
				}
			
			}
		
		}
		
		/**
			* Build xml
			*
			* @access	private
		*/
		
		private function build_xml(){
		
			if($this->_type == 'rss'){
		
				$this->_xml = new SimpleXMLElement('<rss/>');
				$this->_xml->addAttribute('version', '2.0');
				
				$channel = $this->_xml->addChild('channel');
				$channel->addChild('title', WS_NAME);
				$channel->addChild('link', WS_URL);
				$channel->addChild('description', '');
				$channel->addChild('generator', 'Home made ;)');
			
			}elseif($this->_type == 'sitemap'){
			
				$this->_xml = new SimpleXMLElement('<urlset/>');
				$this->_xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			
			}
		
			if(!empty($this->_content)){
			
				if($this->_type == 'rss'){
				
					foreach($this->_content as &$element){
					
						$item = $channel->addChild('item');
						
						foreach($element as $key => &$value){
						
							switch($key){
							
								case 'pubDate':
									$item->addChild('pubDate', date(DATE_RFC822, strtotime($value)));
									break;
								
								case 'link':
									$item->addChild('link', WS_URL.htmlspecialchars('?pid=post&news='.$value));
									break;
								
								default:
									$item->addChild($key, $value);
									break;
							
							}
						
						}
					
					}
				
				}elseif($this->_type == 'sitemap'){
				
					foreach($this->_content as &$element){
					
						$url = $this->_xml->addChild('url');
						
						foreach($element as $key => &$value){
						
							switch($key){
							
								case 'SPLink':
									$url->addChild('loc', WS_URL.htmlspecialchars('?pid=post&news='.$value));
									break;
								
								case 'SPPubDate':
									$url->addChild('lastmod', date('Y-m-d', strtotime($value)));
									break;
								
								case 'SMLink':
									$url->addChild('loc', WS_URL.htmlspecialchars('?pid=pic&album='.$value));
									break;
								
								case 'SMPubDate':
									$url->addChild('lastmod', date('Y-m-d', strtotime($value)));
									break;
								
								default:
									$url->addChild($key, $value);
									break;
							
							}
						
						}
						
						$url->addChild('changefreq', 'never');
					
					}
				
				}
			
			}
		
		}
		
		/**
			* Display xml
			*
			* @access	public
		*/
		
		public function build(){
		
			header('Content-Type: application/xml; charset=utf-8');
			echo $this->_xml->asXML();
		
		}
		
		/**
			* Check if SimpleXml extension is loaded
			*
			* @static
			* @access	private
		*/
		
		private static function check_ext(){
		
			if(!extension_loaded('SimpleXML'))
				throw new Exception('SimpleXML extension not loaded!');
		
		}
	
	}

?>