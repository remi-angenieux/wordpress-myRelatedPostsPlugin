<?php
/**
 * The public-facing functionalities of the plugin.
 * 
 * @package myRelatedPostsPlugin
 * @author Remi Angenieux <remi@angenieux.info>
 *
 */
class MyRelatedPostsPlugin_Public {
	protected $_config;
	
	public function __construct() {
		// Load configuration
	    $this->_config = MyRelatedPostsPlugin_Config::getInstance();
	}
	
	public function enqueue_styles() {
	}
	public function enqueue_scripts() {
	}
	
	/**
	 * Decrement $i by $v but $i can not be negative.
	 * 
	 * @param integer $i
	 * @param integer $v
	 * @return number
	 */
	protected function _positiveDecrement($i, $v){
        $i -= $v;
        if ($i<=0)
            return 0;
        else
            return $i;
	}
	
	/**
	 * Get the primary category of the current post (if $catId=false).
	 * Or the
	 * 
	 * @param integer $catId
	 * @return mixed[]|boolean On sucess return an array:<br />
	 * 0 => (integer) primary category id.<br />
	 * 1 => (string) primary category name.<br />
	 * 2 => (string) primary category link.<br />
	 * 
	 * If the current post is not in any category, return FALSE.
	 */
	protected function _primaryCategory($catId=FALSE) {
        if ($catId===FALSE)
            $category = get_the_category();
        else
            $category[0] = get_category($catId);
        // If post has a category assigned.
        if ($category){
            $category_display = '';
            $category_link = '';
            $category_id = 1;
            if ( class_exists('WPSEO_Primary_Term') ) {
                // Show the post's 'Primary' category, if this Yoast feature is available, & one is set
                $wpseo_primary_term = new WPSEO_Primary_Term( 'category', get_the_id() );
                $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
                $term = get_term( $wpseo_primary_term );
                if (is_wp_error($term)) {
                    // Default to first category (not Yoast) if an error is returned
                    $category_display = $category[0]->name;
                    $category_link = get_category_link( $category[0]->term_id );
                    $category_id = $category[0]->term_id;
                } else {
                    // Yoast Primary category
                    $category_display = $term->name;
                    $category_link = get_category_link( $term->term_id );
                    $category_id = $term->term_id;
                }
            } else {
                // Default, display the first category in WP's list of assigned categories
                $category_display = $category[0]->name;
                $category_link = get_category_link( $category[0]->term_id );
                $category_id = $category[0]->term_id;
            }
	                
            return array($category_id, htmlspecialchars($category_display), $category_link);
        } // If post has categorie
        return FALSE;
	}
	
	/**
	 * Get a WP_Query that contains related posts
	 * 
	 * @return NULL|WP_Query WP_Query on succes, NULL if miss used (not in the loop)
	 */
	public function getRelatedPosts() {
	    global $post;
	    
	    if (empty($post))
	        return null;
	    
	    if ($posts = get_transient($this->_config->getTransientName().'-'.$post->ID))
	        return $posts;
	    else {
	        $countDownPosts = 4;
	        $post__in = array();
	        if ($post__in = get_post_meta($post->ID, $this->_config->getPostMetaIncludePosts(), true )) {
    	        $countDownPosts = $this->_positiveDecrement($countDownPosts, count($post__in));
	        }
	        $post__not_in = $post__in;
	        $post__not_in[] = $post->ID;
	        
	        if ($countDownPosts >= 1) {
	            list($cat, $null, $null) = $this->_primaryCategory();
	            
	            $catsQuery=array(
	                'post_status' => 'publish',
	                'post_type' => 'post',
	                'post__not_in' => $post__not_in,
	                'showposts' => $countDownPosts,
	                'fields' => 'ids',
	                'ignore_sticky_posts' => 1,
	                'category__in' => $cat
	            );
	            $cats = new WP_Query($catsQuery);
	            foreach ($cats->posts as $value){
	                $post__in[] = $value;
	                $post__not_in[] = $value;
	            }
	            $countDownPosts = $this->_positiveDecrement($countDownPosts, count($cats->posts));
	            if ($countDownPosts >= 1){
	                $tags = wp_get_post_tags($post->ID);
	                if ($tags) {
	                    $tag_ids = array();
	                    
	                    foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
	                    
	                    $tagsQuery=array(
	                        'post_status' => 'publish',
	                        'post_type' => 'post',
	                        'post__not_in' => $post__not_in,
	                        'showposts' => $countDownPosts,
	                        'fields' => 'ids',
	                        'ignore_sticky_posts' => 1,
	                        'tag__in' => $tag_ids
	                    );
	                    $tags = new WP_Query($tagsQuery);
	                    foreach ($tags->posts as $value){
	                        $post__in[] = $value;
	                        $post__not_in[] = $value;
	                    }
	                    $countDownPosts = $this->_positiveDecrement($countDownPosts, count($tags->posts));
	                }
	                
	                if ($countDownPosts >= 1){
	                    $randQuery=array(
	                        'post_status' => 'publish',
	                        'post_type' => 'post',
	                        'post__not_in' => $post__not_in,
	                        'showposts' => $countDownPosts,
	                        'fields' => 'ids',
	                        'ignore_sticky_posts' => 1,
	                        'orderby' => 'RAND(42)'
	                    );
	                    $rands = new WP_Query($randQuery);
	                    foreach ($rands->posts as $value)
	                        $post__in[] = $value;
	                }
	            }
    	    }
    	    $related_query = new WP_Query(array(
    	        'ignore_sticky_posts' => 1,
    	        'post__in' => $post__in,
    	        'orderby' => 'post__in',
    	        'order'   => 'DESC'
    	    ));
    	    
    	    set_transient($this->_config->getTransientName().'-'.$post->ID, $related_query, 4*HOUR_IN_SECONDS);
    	    
    	    return $related_query;
    	} // Transient exist
    } // Enf function
}