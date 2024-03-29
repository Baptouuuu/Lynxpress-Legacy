<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<meta name="author" content="Langlade Baptiste" />
		<meta name="generator" content="Lynxpress" />
		<title><?php echo $title.' | '.WS_NAME ?></title>
		<link rel="index" href="<?php echo WS_URL ?>" title="<?php echo WS_NAME ?>" />
		<link rel="icon" type="image/png" href="images/lynxpress-mini.png" />
		<link rel="alternate" type="application/rss+xml" title="<?php echo WS_NAME ?>" href="feed.php" />
		<link rel="alternate" type="application/xml" title="Sitemap" href="sitemap.php" />
		<link rel="stylesheet" type="text/css" href="includes/templates/main/css/mobile.css" />
		<link rel="stylesheet" type="text/css" href="photoswipe/photoswipe.css" />
		
		<script type="text/javascript" src="photoswipe/klass.min.js"></script>
		<script type="text/javascript" src="photoswipe/code.photoswipe-3.0.4.min.js"></script>
		
		<script type="text/javascript">
			document.addEventListener('DOMContentLoaded', function(){
			
				var myPhotoSwipe = Code.PhotoSwipe.attach( window.document.querySelectorAll('#album a'), { enableMouseWheel: false , enableKeyboard: false } );
			
			}, false);
		</script>
		
	</head>

	<body>
    
    	<div id="header">
        	
        	<ul>
        		<li>
        			<img src="images/lynxpress_header.png" alt=""/>
        		</li>
        		<li class="tmenu">
        			<a href="<?php echo WS_URL ?>">
        				<?php echo WS_NAME ?>
        			</a>
        		</li>
        		<li class="tmenu">
        			<a href="<?php echo WS_URL ?>?ctl=posts">Blog</a>
        		</li>
        		<li class="tmenu">
        			<a href="<?php echo WS_URL ?>?ctl=albums">Albums</a>
        		</li>
        		<li class="tmenu">
        			<a href="<?php echo WS_URL ?>?ctl=video">Videos</a>
        		</li>
        		<li class="tmenu">
        			<a href="<?php echo WS_URL ?>?ctl=links">Links</a>
        		</li>
        	</ul>
            
        </div>
        
        <div id="corps">            
        	                        
        	<div id="content">
        	                        
        		