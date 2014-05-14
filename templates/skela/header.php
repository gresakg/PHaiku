<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="<?php echo $site->language; ?>"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="<?php echo $site->language; ?>"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="<?php echo $site->language; ?>"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="<?php echo $site->language; ?>"> <!--<![endif]-->
<head>

	<!-- Basic Page Needs
  ================================================== -->
	<meta charset="utf-8">
	<title><?php echo $site->title; ?></title>

	<!-- Mobile Specific Metas
  ================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- CSS
  ================================================== -->
	<link rel="stylesheet" href="<?php echo $site->template_url; ?>/css/ske18.min.css">
	<link rel="stylesheet" href="<?php echo $site->template_url; ?>/css/dropdowns-skin-discrete.css">
	<link rel="stylesheet" href="<?php echo $site->template_url; ?>/css/custom.css">
	<!-- if you want to use Dropdowns menu you shold also include a dropdowns skin -->

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script src="<?php echo $site->template_url; ?>/js/jquery-1.10.2.min.js"></script>
	<script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>
	<script type="text/javascript">
		var RecaptchaOptions = {
			theme : 'clean'
		};
	</script>
	
	<!-- Favicons
	================================================== -->
	<link rel="shortcut icon" href="<?php echo $site->template_url; ?>/img/favicon.ico">
	<link rel="apple-touch-icon-precomposed" href="<?php echo $site->template_url; ?>/img/apple-touch-icon.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $site->template_url; ?>/img/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $site->template_url; ?>/img/apple-touch-icon-114x114.png">

	<!-- Windows 8 start screen specific meta
  ================================================== -->
	<meta name="application-name" content=""/>
	<meta name="msapplication-TileColor" content=""/>
	<meta name="msapplication-TileImage" content="<?php echo $site->template_url; ?>/img/winows-8-start-screen-icon.png"/>
</head>
<body>
	<div class="container">
		<header>
			<div class="row">
				<div class="nine columns"><h1><?php echo $site->title; ?></h1></div>
				<div class="nine columns"><?php echo $widgets->langmenu; ?></div>
			</div>
			<?php echo $widgets->menu; ?>
		</header>