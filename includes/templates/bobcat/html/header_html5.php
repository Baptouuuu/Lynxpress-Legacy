<?php use \Site\Html as Html; ?>
<!DOCTYPE html >

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta charset="utf-8" />
		<meta name="author" content="Langlade Baptiste" />
		<meta name="generator" content="Lynxpress" />
		<title><?php echo $title.' | '.WS_NAME ?></title>
		<link rel="index" href="<?php echo WS_URL ?>" title="<?php echo WS_NAME ?>" />
		<link rel="icon" type="image/png" href="images/lynxpress-mini.png" />
		<link rel="alternate" type="application/rss+xml" title="<?php echo WS_NAME ?>" href="feed.php" />
		<link rel="alternate" type="application/xml" title="Sitemap" href="sitemap.php" />
		<link rel="stylesheet" type="text/css" href="includes/templates/bobcat/css/html5.css" />
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
    
    	<header>
        	
        	<a href="<?php echo WS_URL ?>">
        		<figure>
        			<img src="images/lynxpress_install.png" alt="lynxpress" />
        			<figcaption>
        				<?php echo WS_NAME ?>
        			</figcaption>
        		</figure>
        	</a>
        	
        	<nav>
        		
        		<a href="<?php echo WS_URL ?>?ctl=posts">Blog</a>
        		
        		<a href="<?php echo WS_URL ?>?ctl=albums">Albums</a>
        		
        		<a href="<?php echo WS_URL ?>?ctl=video">Videos</a>
        		
        		<a href="<?php echo WS_URL ?>?ctl=links">Links</a>
        		
        	</nav>
        	
        	<form id="search" class="form" method="get" action="index.php">
        		<input type="hidden" name="ctl" value="search" />
        		<input type="text" name="q" placeholder="  Search..." list="titles" />
        		<?php Html::datalist('titles'); ?>
        	</form>
            
        </header>
        
        <section id="corps">
        
        	<section id="content">
        	                        
        		