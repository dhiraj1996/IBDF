<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <?php
        $options    =   AE_Options::get_instance();
        $favicon = $options->mobile_icon['thumbnail'][0];
    ?>
    <link rel="shortcut icon" href="<?php echo $favicon ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href='//fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='<?php echo TEMPLATEURL ?>/css/intro.css' rel='stylesheet' type='text/css'>
    <?php
        //loads comment reply JS on single posts and pages
        if ( is_single()) wp_enqueue_script( 'comment-reply' );
    ?>
    <?php wp_head(); ?>
</head>
<body <?php echo body_class('intro-wrapper'); ?>>