<?php
/**
 * Plugin main class.
 * The developper-specific functionalities of the plugin.
 *
 * @package myRelatedPostsPlugin
 * @author Remi Angenieux <remi@angenieux.info>
 *
 */
class MyRelatedPostsPlugin {
    private static $_singleton=null;
    
    protected $_pluginLoader;
	protected $_pluginAdmin;
	protected $_pluginPublic;
	protected $_pluginI18n;
	protected $_pluginConfig;
	
	private function __construct() {
		$this->_config();
		$this->_loader();
		$this->_setLocale();
		$this->_defineAdminHooks();
		$this->_definePublicHooks();
	}
	
	/**
	 * Static function which allow to use this object.
	 * 
	 * @return MyRelatedPostsPlugin
	 */
	public static function getInstance(){
	    if (is_null(self::$_singleton))
	        return self::$_singleton = new self;
	    return self::$_singleton;
	}
	
	/**
	 * Loads configuration manager.
	 */
	protected function _config(){
	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/myRelatedPostsPlugin-config.php';
	    $this->_pluginConfig = MyRelatedPostsPlugin_Config::getInstance();
	}
	
	/**
	 * Loads hooks manager (loader).
	 */
	protected function _loader(){
	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/myRelatedPostsPlugin-loader.php';
	    $this->_pluginLoader = new MyRelatedPostsPlugin_Loader();
	}
	/**
	 * Loads internationalization manager.
	 */
	protected function _setLocale() {
	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/myRelatedPostsPlugin-i18n.php';
		$this->_pluginI18n = new MyRelatedPostsPlugin_i18n();
		$this->_pluginLoader->add_action( 'init', $this->_pluginI18n, 'load_plugin_textdomain' );
	}
	/**
	 * Registers all hooks related to the admin area 
	 */
	protected function _defineAdminHooks() {
		if (!is_admin())
			return;
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/myRelatedPostsPlugin-admin.php';
		$this->_pluginAdmin = new MyRelatedPostsPlugin_Admin();
		$this->_pluginLoader->add_action( 'admin_enqueue_scripts', $this->_pluginAdmin, 'loadStyleSelect2',99 );
		$this->_pluginLoader->add_action( 'admin_enqueue_scripts', $this->_pluginAdmin, 'loadScriptSelect2',99 );
		$this->_pluginLoader->add_action( 'add_meta_boxes', $this->_pluginAdmin, 'add_meta_boxes' );
		$this->_pluginLoader->add_action( 'save_post', $this->_pluginAdmin, 'saveMetaBoxes', 10, 3 );
		$this->_pluginLoader->add_action( 'admin_notices', $this->_pluginAdmin, 'printErrors' );
		$this->_pluginLoader->add_action( 'admin_footer', $this->_pluginAdmin, 'printJavascript' );
		$this->_pluginLoader->add_action( 'wp_ajax_'.$this->_pluginConfig->getAdminAjaxAction(), $this->_pluginAdmin, 'ajax_'.$this->_pluginConfig->getAdminAjaxAction());
	}
	/**
	 * Registers all hooks related to the user area
	 */
	protected function _definePublicHooks() {
	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/myRelatedPostsPlugin-public.php';
		$this->_pluginPublic = new MyRelatedPostsPlugin_Public();
		//$this->loader->add_action( 'wp_enqueue_scripts', $this->_pluginPublic, 'enqueue_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $this->_pluginPublic, 'enqueue_scripts' );
	}
	
	/**
	 * Get a WP_Query that contains related posts.
	 *
	 * @return NULL|WP_Query WP_Query on succes, NULL if miss used (not in the loop).
	 */
	public function getRelatedPosts(){
	    return $this->_pluginPublic->getRelatedPosts();
	}
	/**
	 * Runs the loader to execute all hooks.
	 */
	public function run() {
		$this->_pluginLoader->run();
	}
	/**
	 * Retrivies the name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public function getPluginName() {
		return $this->_pluginConfig->getPluginName();
	}
	/**
	 * Retrieves version number of the plugin.
	 */
	public function getVersion() {
		return $this->_pluginConfig->getVersion();
	}
}