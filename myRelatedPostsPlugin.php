<?php
/**
 * Plugin Name: Fast Simple Reliable Related Post
 * Plugin URI: https://blog.angenieux.info
 * Description: Add related posts section based on post added manuallay, or categories or tags or random.
 * Version: 1.0.0
 * Author: Remi Angenieux
 * Author URI: https://remi.angenieux.info
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: myRelatedPostsPlugin
 * Domain Path: /languages
 * 
 * @package myRelatedPostsPlugin
 * @author Remi Angenieux <remi@angenieux.info>
 * @version 1.0.0
 
myRelatedPostsPlugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
myRelatedPostsPlugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with myRelatedPostsPlugin. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function activate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/myRelatedPostsPlugin-activator.php';
	myRelatedPostsPlugin_Activator::activate();
}

function deactivate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/myRelatedPostsPlugin-deactivator.php';
	myRelatedPostsPlugin_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_plugin_name' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );

require plugin_dir_path( __FILE__ ) . 'includes/myRelatedPostsPlugin-class.php';

MyRelatedPostsPlugin::getInstance()->run();
