<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8">
		<meta name="author" content="Langlade Baptiste">
		<meta name="generator" content="Lynxpress" />
		<title><?php echo $title.' | '.WS_NAME ?></title>
		<link rel="index" href="<?php echo WS_URL ?>" title="<?php echo WS_NAME ?>">
		<link rel="icon" type="image/png" href="images/lynxpress-mini.png">
		<link rel="alternate" type="application/rss+xml" title="<?php echo WS_NAME ?>" href="feed.php">
		<link rel="alternate" type="application/xml" title="Sitemap" href="sitemap.php">
		<link rel="stylesheet" type="text/css" href="includes/templates/main/css/style.css">
		<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox-1.3.4.css" media="screen" />
		
		<script src="js/jquery-1.4.3.min.js"></script>
		<script type="text/javascript" src="fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
		<script type="text/javascript" src="fancybox/jquery.fancybox-1.3.4.pack.js"></script>
		
		<script type="text/javascript">
			$(document).ready(function() {
				$("a[rel=fancybox]").fancybox({
					'titlePosition'		: 'outside',
					'overlayColor'		: '#000',
					'overlayOpacity'	: 0.85
				});
			});
		</script>
		
	</head>

	<body>
    
    	<div id="header">
        	
        	<ul>
        		<li>
        			<img src="images/lynxpress_header.png" alt="">
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
        	</ul>
            
        </div>
        
        <div id="corps">
        
        	<ul id="menu">
        		<li id="search">
        			<form class="form" method="get" action="index.php">
        				<input type="hidden" name="ctl" value="search" />
        				<input type="text" name="q" value="  Search..."  onfocus="if ( this.value == this.defaultValue ) this.value = '';" onblur="if ( this.value == '' ) this.value = this.defaultValue" />
        			</form>
        		</li>
        		<?php
        		
        			foreach($menu as $item){
        			
        				echo '<li class="menut">'.$item.'</li>';
        			
        			}
        		
        		?>
        	</ul>
        	                        
        	                        
        	<div id="content">
        	                        
        		