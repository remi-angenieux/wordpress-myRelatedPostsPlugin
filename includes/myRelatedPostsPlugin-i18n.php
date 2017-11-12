<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package myRelatedPostsPlugin
 * @author Remi Angenieux <remi@angenieux.info>
 *
 */
class MyRelatedPostsPlugin_i18n {
    protected $_config;
    
    public function __construct(){
        // Load configuration
        $this->_config = MyRelatedPostsPlugin_Config::getInstance();
    }
    
	/**
	 * Sets gettext textdomain
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			$this->_config->getPluginName(),
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages'
		);
	}
}