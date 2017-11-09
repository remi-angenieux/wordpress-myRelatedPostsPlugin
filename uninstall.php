<?php
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once 'includes/myRelatedPostsPlugin-config.php';
$myRelatedPostsPlugin_pluginConfig = MyRelatedPostsPlugin_Config::getInstance();

// Remove all post_meta value added by this plugin
delete_metadata('post', 0, $myRelatedPostsPlugin_pluginConfig->getPostMetaIncludePosts(), '', true);