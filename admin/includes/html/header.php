<?php 
	use \Library\Variable\Session as Session;
	use \Admin\Helper\Helper as Helper;
	use \Library\Variable\Get as VGet;
?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>

		<meta charset="utf-8" />
		<title><?php echo $page->title.' | '.WS_NAME ?></title>
		<link rel="index" href="<?php echo WS_URL ?>" title="<?php echo WS_NAME ?>" />
		<link rel="icon" type="image/png" href="<?php echo PATH ?>images/lynxpress-mini.png" />
		<link rel="stylesheet" type="text/css" href="<?php echo PATH ?>css/admin.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo PATH ?>fancybox/jquery.fancybox.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="<?php echo PATH ?>fancybox/helpers/jquery.fancybox-buttons.css?v=2.0.3" />
		<?php
		
			if(file_exists(PATH.'css/'.VGet::ns().'.css'))
				echo '<link rel="stylesheet" type="text/css" href="'.PATH.'css/'.VGet::ns().'.css" />';
		
		?>
		
		<script type="text/javascript" src="<?php echo PATH ?>js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="<?php echo PATH ?>js/jquery.mousewheel-3.0.6.pack.js"></script>
		<script type="text/javascript" src="<?php echo PATH ?>fancybox/jquery.fancybox.js"></script>
		<script type="text/javascript" src="<?php echo PATH ?>fancybox/helpers/jquery.fancybox-buttons.js?v=2.0.3"></script>
		
		<script type="text/javascript">
			$(document).ready(function() {
				$("a.fancybox").fancybox({
					helpers: {
						title : {
							type : 'outside'
						},
						overlay : {
							speedIn : 500,
							opacity : 0.85
						}
					}
				});
			});
		</script>

	</head>

	<body>

		<header>
		
			<ul id="nav">
				
				<li>
					<a href="../" target="_blank" title="Back to your website"><?php echo WS_NAME ?></a>
				</li>
				
				<li>
					<a href="index.php?ns=timeline&ctl=manage">Timeline</a>
					
					<ul>
						<li><a href="index.php?ns=timeline&ctl=settings">Settings</a></li>
					</ul>
				</li>
			
				<li>
					<a href="index.php?ns=dashboard&ctl=manage">Dashboard</a>
				</li>
			
				<li>
					<a href="index.php?ns=posts&ctl=manage">Posts</a>
					
					<ul>
						<li><a href="index.php?ns=posts&ctl=add">Add</a></li>
						<li>
							<a href="index.php?ns=posts&ctl=manage">Posts</a>
							
							<ul>
								<li><a href="index.php?ns=posts&ctl=manage&post_status=publish">Published</a></li>
								<li><a href="index.php?ns=posts&ctl=manage&post_status=draft">Draft</a></li>
								<li><a href="index.php?ns=posts&ctl=manage&post_status=trash">Trash</a></li>
							</ul>
						</li>
					</ul>
				</li>
			
				<li>
					<a href="index.php?ns=media&ctl=manage">Media</a>
					
					<ul>
						<li>
							<a href="index.php?ns=media&ctl=add">Add</a>
							
							<ul>
								<li><a href="index.php?ns=media&ctl=add&view=album">Album</a></li>
								<li><a href="index.php?ns=media&ctl=add&view=linkage">Linkage</a></li>
								<li><a href="index.php?ns=media&ctl=add&view=video">Video</a></li>
							</ul>
						</li>
						<li>
							<a href="index.php?ns=media&ctl=manage">Media</a>
							
							<ul>
								<li><a href="index.php?ns=media&ctl=manage">Images</a></li>
								<li><a href="index.php?ns=media&ctl=manage&type=video">Videos</a></li>
								<li><a href="index.php?ns=media&ctl=manage&type=alien">External Videos</a></li>
							</ul>
						</li>
						<li><a href="index.php?ns=media&ctl=albums">Albums</a></li>
					</ul>
				</li>
			
				<li>
					<a href="index.php?ns=comments&ctl=manage">Comments</a>
					
					<ul>
						<li><a href="index.php?ns=comments&ctl=manage">Pending</a></li>
						<li><a href="index.php?ns=comments&ctl=manage&comment_status=approved">Approved</a></li>
						<li><a href="index.php?ns=comments&ctl=manage&comment_status=spam">Spam</a></li>
						<li><a href="index.php?ns=comments&ctl=manage&comment_status=trash">Trash</a></li>
					</ul>
				</li>
			
				<li>
					<a href="index.php?ns=users&ctl=profile">Profile</a>
				</li>
				
				<li>
					<a href="index.php?ns=plugins&ctl=bridge">Plugins</a>
					
					<?php 
					
						$plugins = Helper::plugins_infos();
						
						if(!empty($plugins)){
						
							echo '<ul id="mplg">';
							
							foreach($plugins as $plg)
								echo '<li><a href="index.php?ns='.$plg['namespace'].'&ctl='.$plg['entry_point'].'">'.$plg['name'].'</a></li>';
							
							echo '</ul>';
						
						}
					
					?>
					
				</li>
				
				<?php
				
					if($page->settings){
					
						echo '<li>'.
							 	'<a href="index.php?ns=settings&ctl=manage">Settings</a>'.
							 	'<ul>'.
							 		'<li><a href="index.php?ns=categories&ctl=manage">Categories</a></li>'.
							 		'<li><a href="index.php?ns=posts&ctl=settingpage">Posts</a></li>'.
							 		'<li>'.
							 			'<a href="index.php?ns=users&ctl=manage">Users</a>'.
							 			'<ul>'.
							 				'<li><a href="index.php?ns=users&ctl=add">Add</a></li>'.
							 			'</ul>'.
							 		'</li>'.
							 		'<li><a href="index.php?ns=roles&ctl=manage">Roles</a></li>'.
							 		'<li><a href="index.php?ns=social&ctl=manage">Social Buttons</a></li>'.
							 		'<li><a href="index.php?ns=defaultpage&ctl=manage">Default Page</a></li>'.
							 		'<li>'.
							 			'<a href="index.php?ns=templates&ctl=manage">Templates</a>'.
							 			'<ul>'.
							 				'<li><a href="index.php?ns=templates&ctl=add">Add</a></li>'.
							 				'<li><a href="index.php?ns=templates&ctl=library">Library</a></li>'.
							 			'</ul>'.
							 		'</li>'.
							 		'<li>'.
							 			'<a href="index.php?ns=plugins&ctl=manage">Plugins</a>'.
							 			'<ul>'.
							 				'<li><a href="index.php?ns=plugins&ctl=add">Add</a></li>'.
							 				'<li><a href="index.php?ns=plugins&ctl=library">Library</a></li>'.
							 			'</ul>'.
							 		'</li>'.
							 		'<li>'.
							 			'<a href="index.php?ns=links&ctl=manage">Links</a>'.
							 			'<ul>'.
							 				'<li><a href="index.php?ns=links&ctl=add">Add</a></li>'.
							 			'</ul>'.
							 		'</li>'.
							 		'<li><a href="index.php?ns=activity&ctl=manage">Activity</a></li>'.
							 		'<li><a href="index.php?ns=update&ctl=manage">Update</a></li>'.
							 	'</ul>'.
							 '<li>';
					
					}
				
				?>
				<li id="right">
					Hi <?php echo Session::username() ?> | 
					<a href="index.php?ns=session&ctl=logout">Logout</a>
				</li>
			</ul>
			
		</header>
		
		<section id="wrapper">