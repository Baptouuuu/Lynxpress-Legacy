<?php use \Library\Variable\Session as Session; ?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php echo $page->title.' | '.WS_NAME ?></title>
		<link rel="index" href="<?php echo WS_URL ?>" title="<?php echo WS_NAME ?>" />
		<link rel="icon" type="image/png" href="<?php echo PATH ?>images/lynxpress-mini.png" />
		<link rel="stylesheet" type="text/css" href="<?php echo PATH ?>css/admin.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo PATH ?>shadowbox/shadowbox.css" />
		<script type="text/javascript" src="<?php echo PATH ?>shadowbox/shadowbox.js"></script>
		
		<script type="text/javascript">
			Shadowbox.init();
		</script>

	</head>

	<body>

		<header>
		
			<nav>
				
					<a href="../" target="_blank" title="Back to your website"><?php echo WS_NAME ?></a>
				
					<a href="index.php">Dashboard</a>
				
					<a href="index.php?ns=posts&ctl=manage">Posts</a>
				
					<a href="index.php?ns=media&ctl=manage">Media</a>
				
					<a href="index.php?ns=comments&ctl=manage">Comments</a>
				
					<a href="index.php?ns=users&ctl=profile">Profile</a>
				
				<?php
				
					if($page->settings)
						echo '<a href="index.php?ns=settings&ctl=manage">Settings</a>';
				
				?>
				<span id="right">
					Hi <?php echo Session::username() ?> | 
					<a href="logout.php">Logout</a>
				</span>
			</nav>
			
		</header>
		
		<section id="wrapper">