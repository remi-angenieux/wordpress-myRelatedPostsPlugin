<?php
/**
 * The admin-specific functionalities of the plugin.
 *
 * @package myRelatedPostsPlugin
 * @author Remi Angenieux <remi@angenieux.info>
 *
 */
class MyRelatedPostsPlugin_Admin {
	
	protected $_config;
	protected $_errors;
	const ER_BAD_INPUT = 1;
	const ER_UNKNOWN = 10;
	
	public function __construct() {
	    // Load configuration
	    $this->_config = MyRelatedPostsPlugin_Config::getInstance();
	}

	/**
	 * Retrives error message.
	 * 
	 * @param integer $code Error code.
	 * @return NULL
	 */
	protected function _getErrorMessage($code){
	    // Create here this array, because we can't use l18n in a construct (l18n not yet init).
	    if (empty($this->_errors)){
	        $this->_errors = array(
	            self::ER_BAD_INPUT => _x('MyRelatedPostsPlugin: Bad input!', 'admin', (string)'myRelatedPostsPlugin'),
	            self::ER_UNKNOWN => _x('MyRelatedPostsPlugin: Unknown error!', 'admin', 'myRelatedPostsPlugin')
	        );
	    }
	    
	    switch($code) {
	        case self::ER_BAD_INPUT:
	            $result = $this->_errors[self::ER_BAD_INPUT];
	            break;
	            
	        default:
	            $result = $this->_errors[self::ER_UNKNOWN];
	            break;
	    }
	    
	    return $result;
	}
	
	/**
	 * Prints error if something went wrong.
	 */
	public function printErrors(){
	    if (!empty($_GET[$this->_config->getMetaBoxErrorsParamName()])) { ?>
		    <div class="notice notice-error">
		        <p>
		            <?php
		            /*switch($_GET[$this->_config->getMetaBoxErrorsParamName()]) {
		                case self::ER_BAD_INPUT:
		                    echo esc_html($this->_errors[self::ER_BAD_INPUT]);
		                break;

		                default:
		                    echo esc_html($this->_errors[self::ER_UNKNOWN]);
		                break;
		            }*/
		            echo esc_html($this->_getErrorMessage($_GET[$this->_config->getMetaBoxErrorsParamName()]));
		            ?>
		        </p>
		    </div><?php
		}
	}
	
	/**
	 * Loads styles for select2 lib
	 * 
	 * @param string $hook
	 */
	public function loadStyleSelect2($hook){
	    if ( 'post.php' != $hook ) { // Only on edit post page
			return;
		}
		
		// If yoast seo had already loaded select2 style, we don't load it twice
		if( !wp_style_is( 'yoast-seo-select2', 'enqueued' ) AND !wp_style_is( 'select2', 'enqueued' ) ) {
		    wp_enqueue_style('select2', plugins_url('/css/select2.min.css', __FILE__), array(), '4.0.5');
		}
		
		wp_enqueue_style($this->_config->getPluginName().'-select2Custom', plugins_url('/css/select2Custom.min.css', __FILE__), array(), $this->_version);
	}
	
	/**
	 * Loads js for select2 lib
	 * 
	 * @param string $hook
	 */
	public function loadScriptSelect2($hook){
	    if ( 'post.php' != $hook ) { // Only on edit post page
			return;
		}
		
		// If yoast seo had already loaded select2 script, we don't load it twice
		if( wp_script_is( 'yoast-seo-select2', 'enqueued' ) OR wp_script_is( 'select2', 'enqueued' ) ) {
		    return;
		}
		
		wp_enqueue_script('select2', plugins_url('/js/select2.min.js', __FILE__), array('jquery'), '4.0.5');
		$lang = strtok(get_locale(), '_');
		wp_enqueue_script('select2-lang', plugins_url('/js/i18n/'.$lang.'.js', __FILE__), array('select2'), '4.0.5');
	}
	
	/**
	 * Stores related posts added manually
	 * 
	 * @param integer $post_id
	 * @param WP_Screen $post
	 * @param unknown $update
	 * @return integer|WP_Error integer if nonce failed or if this function is triggered in a case it should not be.<br />
	 * WP_error in case of error during input verifications
	 */
	protected function _saveMetaBox($post_id, $post, $update)
	{
	    if (!isset($_POST[$this->_config->getMetaBoxNonce()]) || !wp_verify_nonce($_POST[$this->_config->getMetaBoxNonce()], basename(__FILE__)))
			return $post_id;
	
		if(!current_user_can('edit_post', $post_id))
			return $post_id;
	
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return $post_id;
	
		if('post' != $post->post_type)
			return $post_id;
				
		$error = null;
		if (!empty($_POST[$this->_config->getMetaBoxInputIncludePosts()])){
		    // Force to be an array, even if just one value is choosen
		    $inputIncludePosts = (is_array($_POST[$this->_config->getMetaBoxInputIncludePosts()]) ) ? $_POST[$this->_config->getMetaBoxInputIncludePosts()] : array($_POST[$this->_config->getMetaBoxInputIncludePosts()]);
		    
		    $args = array(
                'post_type'      => 'post',
                'post_status'    => array('publish','pending','draft','future'), // Usefull if we want to prepare a post and want to link to draft posts
                'fields' => 'ids',
		        'post__in' => $inputIncludePosts
            );
            $postsMatches = get_posts($args);
    		    
            // Verify each IDs, they need to be valid
            if( array_diff($inputIncludePosts, $postsMatches) != array()) {
                $error = new WP_Error(self::ER_BAD_INPUT, $this->_getErrorMessage(self::ER_BAD_INPUT));
            }
		}
		
        if ($error) {
            // Print error
            add_filter('redirect_post_location', function( $location ) use ( $error ) {
                return add_query_arg($this->_config->getMetaBoxErrorsParamName(), $error->get_error_code(), $location );
            });
            return $error;
        } else {
            // store the list in database
            update_post_meta($post_id, $this->_config->getPostMetaIncludePosts(), $inputIncludePosts);
            // Remove the transient linked to this related post to be sure to have fresh data
            delete_transient($this->_config->getTransientName().'-'.$post_id);
        }
	}
	
	/**
	 * Stores related posts added manually
	 *
	 * @param integer $post_id
	 * @param WP_Screen $post
	 * @param unknown $update
	 * @return integer|WP_Error integer if nonce failed or if this function is triggered in a case it should not be.<br />
	 * WP_error in case of error during input verifications
	 */
	public function saveMetaBoxes($post_id, $post, $update){
		return $this->_saveMetaBox($post_id,$post, $update);
	}
	
	/**
	 * Prints the metBox used to ask which posts have to be prints to the related posts section.
	 * 
	 * @param unknown $object
	 */
	public function showMetaBox($object) {
	    wp_nonce_field (basename ( __FILE__ ), $this->_config->getMetaBoxNonce());
		
		?>
<div>
	<p>
		<?php _ex('Choose each posts you want to see on the related posts section. You can choose only 3 posts.', 'admin', 'myRelatedPostsPlugin'); ?>
	</p>
	<p>
		<select id="<?php echo $this->_config->getMetaBoxInputIncludePosts(); ?>" name="<?php echo $this->_config->getMetaBoxInputIncludePosts(); ?>[]" multiple="multiple">
		<?php 
		$values = get_post_meta($object->ID, $this->_config->getPostMetaIncludePosts(), true);
		if (!empty($values)){
		    foreach ($values as $v){
		        if (!empty($thumb = get_post_thumbnail_id($v)) AND $img = wp_get_attachment_image_src($thumb) ) {
		            $img = $img[0];
				} else {
				    $img = $this->_config->getImageDefault();
				}
				// Store url in label, it will be used by JS
				echo '<option value="'.$v.'" selected="selected" label="' . htmlspecialchars($img) . '">'.substr(get_the_title($v), 0, 60).'</option>';
			}
		}
		?>
		</select>
	</p>	
</div>
<?php
	}
	
	/**
	 * Adds the metaBox
	 */
	public function add_meta_boxes() {
		add_meta_box ( 'myRelatedPostsPlugin-metaBox', _x('Related Posts', 'admin', 'myRelatedPostsPlugin' ), array ($this, 'showMetaBox'), 'post', 'advanced', 'low', null);
	}
	
	/**
	 * Prints JS scripts used by our metaBox
	 */
	public function printJavascript(){
		global $post;
		$hook = get_current_screen();
		
		// I can't rely on $hook->parent_base, it's set to post, same for parent_file
		if ( 'post' != $hook->id ) {
		    return;
		}
		
		$lang = strtok(get_locale(), '_');
		?>
<script type="text/javascript">
	jQuery( document ).ready( function( $ ) {
		/* Thumb followed by its name */
		function formatPost(post){
			if (post.loading) {
				return post.text;
			}
			var $htmlPost = $($.parseHTML("<div class='select2ResultPosts'><div class='select2ResultPostLeft'><img height='<?php echo $this->_config->getImgHeight(); ?>' src='" + post.img.url + "' /></div><div class='select2ResultPostRight'><div class='select2ResultPostVCenterDiv'><div class='select2ResultPostVCenterElem'>" + post.text + "</div></div></div></div>"));
			return $htmlPost;
		}
		/* Thumb followed by its name */
		function formatPostSelection(post){
			if (!post.id) {
				return post.text;
			}
			if (!post.img){
				var imgUrl = $('#<?php echo $this->_config->getMetaBoxInputIncludePosts(); ?> option[value="' + post.id + '"]');
				if (imgUrl){
					imgUrl = imgUrl.first().attr('label');
					if (imgUrl){
						post.img = {url: imgUrl};
					} else {
						return post.text;
					}
				} else {
					return post.text;
				}
			}
			var $htmlPost = $($.parseHTML("<img class='select2Selection' height='<?php echo $this->_config->getImgHeight(); ?>' src='" + post.img.url + "' />" + post.text ));
			
			return $htmlPost;
		}
		$('#<?php echo $this->_config->getMetaBoxInputIncludePosts(); ?>').select2({
			width: '100%',
			placeholder: '<?php _ex('Search post...', 'admin', 'myRelatedPostsPlugin'); ?>',
			minimumInputLength : 3,
			templateResult: formatPost,
			templateSelection: formatPostSelection,
			cache: true,
			language: '<?php echo $lang; ?>',
			ajax: {
				method: 'POST',
				url: '<?php echo admin_url('admin-ajax.php?action='.$this->_config->getAdminAjaxAction()); ?>',
				dataType: 'json',
				action: '<?php echo $this->_config->getAdminAjaxAction(); ?>',
				quietMillis: 250,
				maximumSelectionLength: 4,
				data: function (params) {
					return {
						term: params.term, //search term
						exclude: <?php echo $post->ID; ?>,
						nonce: '<?php echo wp_create_nonce($this->_config->getAdminAjaxNonce()); ?>'
					};
				},
				processResults: function (data) {
					return {
						results: data
					};
				}
			}
		});
		/* To keep order set by user */
		$('#<?php echo $this->_config->getMetaBoxInputIncludePosts(); ?>').on('select2:select', function(e){
			var id = e.params.data.id;
			var option = $(e.target).children('[value='+id+']');
			option.detach();
			$(e.target).append(option).change();
		});
	});
</script>
		<?php
		
	}
	
	/**
	 * Function called by Ajax, in input it's expects query seach. And ouput potentials posts based on input query.
	 */
	public function ajax_getPostIdByName(){
	    check_ajax_referer($this->_config->getAdminAjaxNonce(), 'nonce');
		
		$term = urldecode(stripslashes(strip_tags(trim($_REQUEST['term']))));
		$exclude = intval(trim($_REQUEST['exclude']));
		$args = array(
				'post_type'      => 'post',
		        'post_status'    => array('publish','pending','draft','future'), // Usefull if we want to prepare a post and want to link to draft posts
				'posts_per_page' => 10, // to reduce server load
				's'				 => $term,
				'fields' => 'ids',
				'post__not_in' => array($exclude) // To exclude posts, by default in the request, the current post is excluded
		);
		
		$postsMatches = get_posts($args);
		
		$result=array();
		foreach ($postsMatches as $post){
		    if (!empty($img = get_post_thumbnail_id($post)) AND $wpImg = wp_get_attachment_image_src($img) ) {
				$img = array(
					'url' => $wpImg[0],
					'width' => $wpImg[1],
					'height' => $wpImg[2]
				);
			} else { // If post don't have any thumb
				$img = array(
				    'url' => $this->_config->getImageDefault(),
					'width' => 150,
					'height' => 150
				);
			}
			
			$result[] = array(
				'id' => $post,
				'text' => substr(get_the_title($post), 0, 60),
				'img' => $img
			);
		}
		
		wp_send_json($result);
		wp_die();
	}
}