# wordpress-myRelatedPostsPlugin
# English
## Description
With this wordpress plugin you can print a "Related Posts" section.
It was design to be *simple*, *fast* and *secure*.

By default the "Related Posts" section is based on :
* Manual list of posts
* Posts in same category
* Posts with same tags
* Random posts

4 posts are retrievied.
Priority is given first to "manual list", then "posts in same category", then "posts with same tags" finally random posts.

This plugin adds a section "Related Posts" in edit post panels, where you can add easily related posts with a search function.

This plugin use transient to cache each SQL requests to be fast.

## Installation
Download and extract plugin files to a wp-content/myRelatedPostsPlugin.
("myRelatedPostsPlugin.php" needs to located be at wp-content/myRelatedPostsPlugin/myRelatedPostsPlugin.php)
Activate the plugin through the WordPress admin interface.

## Configuration
This plugin don't print anything, it give you possibility to print related posts. So you can stylized as you want !

To get related posts list, you juste need to add this line:

    $related_query = MyRelatedPostsPlugin::getInstance()->getRelatedPosts();
    
Then you can create a loop:

    while ($related_query->have_posts()) : $related_query->the_post();
    
### Full example
    if (class_exists(MyRelatedPostsPlugin)){
        $related_query = MyRelatedPostsPlugin::getInstance()->getRelatedPosts();
        if ($related_query->have_posts()) {	?>
		    <div class="related-posts">
			<?php
			$related_title = esc_attr( of_get_option('blog_related') );
			?>
				<h2 class="related-posts_h"><?php if ( '' != $related_title ) { echo $related_title; } else { _e('Related Posts','bueno'); }?></h2>
				<ul class="related-posts_list clearfix">
				<?php
				while ($related_query->have_posts()) : $related_query->the_post();
				?>
					<li class="related-posts_item">
						<?php
						if(has_post_thumbnail()) {
							$thumb = get_post_thumbnail_id();
							$img_url = wp_get_attachment_image_src( $thumb,'related-thumb'); //get img URL
						?>
						<figure class="thumbnail featured-thumbnail">
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img src="<?php echo esc_url( $img_url[0] ); ?>" alt="<?php the_title(); ?>" /></a>
						</figure>
						<?php
						} else {
						?>
						<figure class="thumbnail featured-thumbnail">
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/empty_thumb.gif" alt="<?php the_title(); ?>" /></a>
						</figure>
						<?php
						}
						?>
						<a href="<?php the_permalink() ?>" > <?php the_title();?> </a>
					</li>
				<?php
				endwhile;
				?>
				</ul>
		    </div><!-- .related-posts -->
	    <?php 
	    }
	    wp_reset_query(); // to reset the loop : http://codex.wordpress.org/Function_Reference/wp_reset_query
    }
# Français
## Description
Avec ce plugin wordpress vous pouvez afficher une section "Articles en relation".
Il a été dévéloppé pour être *simple*, *rapide* et *sécurisé*.

Par défaut la section "Articles en relation" se base sur :
* Liste d'article configuré manuellement
* Articles dans la même catégorie
* Articles avec les mêmes tag
* Articles aléatoires

4 articles sont récupérés.
La priorité est donnée en premieux a la "liste manuelle", puis "articles dans la même catégorie", puis "articles avec les mêmes tag" et dernièrement des articles aléatoires.

Ce plugin ajoute une section "Articles en relation" dans le panneau d'édition d'articles, où vous pouvez simplement ajouter des articles en relation grâce à une fonction recherche.

Ce plugin utiliser des transient pour mettre en cache toutes les requêtes SQL, dans le but d'être rapide.

## Installation
Téléchargez and dézippez les fcihiers dans wp-content/myRelatedPostsPlugin.
("myRelatedPostsPlugin.php" doit se retrouve à wp-content/myRelatedPostsPlugin/myRelatedPostsPlugin.php)
Activez le plugin a l'aide du panneau d'administration de Wordpress.

## Configuration
Ce plugin n'affiche rien, il vous donne la possibilité d'afficher les articles en relation. Donc c'est à vous de styliser comme bon vous semble !

Pour avoir la liste des articles en relation, vous avez juste à ajouter cette ligne :

    $related_query = MyRelatedPostsPlugin::getInstance()->getRelatedPosts();
    
Puis créer la boucle :

    while ($related_query->have_posts()) : $related_query->the_post();
    
### Exemple complet
    if (class_exists(MyRelatedPostsPlugin)){
        $related_query = MyRelatedPostsPlugin::getInstance()->getRelatedPosts();
        if ($related_query->have_posts()) {	?>
		    <div class="related-posts">
			<?php
			$related_title = esc_attr( of_get_option('blog_related') );
			?>
				<h2 class="related-posts_h"><?php if ( '' != $related_title ) { echo $related_title; } else { _e('Related Posts','bueno'); }?></h2>
				<ul class="related-posts_list clearfix">
				<?php
				while ($related_query->have_posts()) : $related_query->the_post();
				?>
					<li class="related-posts_item">
						<?php
						if(has_post_thumbnail()) {
							$thumb = get_post_thumbnail_id();
							$img_url = wp_get_attachment_image_src( $thumb,'related-thumb'); //get img URL
						?>
						<figure class="thumbnail featured-thumbnail">
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img src="<?php echo esc_url( $img_url[0] ); ?>" alt="<?php the_title(); ?>" /></a>
						</figure>
						<?php
						} else {
						?>
						<figure class="thumbnail featured-thumbnail">
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/empty_thumb.gif" alt="<?php the_title(); ?>" /></a>
						</figure>
						<?php
						}
						?>
						<a href="<?php the_permalink() ?>" > <?php the_title();?> </a>
					</li>
				<?php
				endwhile;
				?>
				</ul>
		    </div><!-- .related-posts -->
	    <?php 
	    }
	    wp_reset_query(); // to reset the loop : http://codex.wordpress.org/Function_Reference/wp_reset_query
    }
