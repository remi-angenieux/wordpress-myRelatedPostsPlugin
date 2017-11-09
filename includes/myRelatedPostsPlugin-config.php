<?php
/**
 * Configuration manager
 *
 * @package myRelatedPostsPlugin
 * @author Remi Angenieux <remi@angenieux.info>
 *
 */
class MyRelatedPostsPlugin_Config
{
    private static $_singleton=null;
    
    protected $_pluginName = 'myRelatedPostsPlugin';
    protected $_version = '0.0.1';
    protected $_postMetaIncludePosts;
    protected $_metaBoxInputIncludePosts;
    protected $_metaBoxNonce;
    protected $_adminAjaxNonce;
    protected $_metaBoxErrorsParamName;
    protected $_adminAjaxAction = 'getPostIdByName';
    protected $_imgHeight = '55px';
    protected $_imageDefault;
    protected $_transientName;
    
    private function __construct(){
        $this->_postMetaIncludePosts = '_'.$this->_pluginName.'-includePosts';
        $this->_metaBoxInputIncludePosts = $this->_pluginName.'-includePosts';
        $this->_metaBoxNonce = $this->_pluginName.'-nonce';
        $this->_adminAjaxNonce = $this->_pluginName.'-ajaxNonce';
        $this->_metaBoxErrorsParamName = $this->_pluginName.'-error';
        $this->_imageDefault = plugins_url('/public/img/empty_thumb.gif', dirname(__FILE__));
        $this->_transientName = $this->_pluginName.'-transient';
    }
    
    public static function getInstance(){
        if (is_null(self::$_singleton))
            return self::$_singleton = new self;
        return self::$_singleton;
    }
    
    /**
     * Retrieves plugin name.
     * 
     * @return string
     */
    public function getPluginName()
    {
        return $this->_pluginName;
    }
    
    /**
     * Retrieves version number.
     * 
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }
    
    /**
     * Retrieves name used as field in postmeta table.
     * 
     * @return string
     */
    public function getPostMetaIncludePosts()
    {
        return $this->_postMetaIncludePosts;
    }

    /**
     * Retrieves input name used to send related posts selected by user.
     * 
     * @return string
     */
    public function getMetaBoxInputIncludePosts()
    {
        return $this->_metaBoxInputIncludePosts;
    }

    /**
     * Retrieves nonce name used by our metaBox
     * 
     * @return string
     */
    public function getMetaBoxNonce()
    {
        return $this->_metaBoxNonce;
    }

    /**
     * Retrieves nonce used by admin-ajax
     * 
     * @return string
     */
    public function getAdminAjaxNonce()
    {
        return $this->_adminAjaxNonce;
    }

    /**
     * Retrieves URL param used to specify an error has occurred.
     * 
     * @return string
     */
    public function getMetaBoxErrorsParamName()
    {
        return $this->_metaBoxErrorsParamName;
    }

    /**
     * Retrieves admin-ajax action used by our plugin. 
     * 
     * @return string
     */
    public function getAdminAjaxAction()
    {
        return $this->_adminAjaxAction;
    }

    /**
     * Retrieves maximum height for thumbs in metaBox.
     * 
     * @return string
     */
    public function getImgHeight()
    {
        return $this->_imgHeight;
    }

    /**
     * Retrieves default thumb image.
     * 
     * @return string
     */
    public function getImageDefault()
    {
        return $this->_imageDefault;
    }
    
    /**
     * Retrieves transient name used to store related posts of each posts.
     * 
     * @return string
     */
    public function getTransientName()
    {
        return $this->_transientName;
    }
    
}

