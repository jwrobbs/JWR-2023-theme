<?php
/**
 * Post rendering content according to caller of get_template_part
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class('archive-card'); ?> id="post-<?php the_ID(); ?>">
	<?php
		$permalink = get_permalink();
		$thumb = get_the_post_thumbnail( $post->ID, 'large' ); 
		if( !isset($thumb) ){
			$thumb = get_jwr_alt_fi();
		} 
		
		echo "<div class='style-excerpt-container'><a href='$permalink'>$thumb</a></div>";
	?>
	<header class="entry-header">
		<?php
		//[] verify post type in header
		//
		the_title(
			sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ),
			'</a></h2>'
		);
		?>
	</header><!-- .entry-header -->

	<div class="entry-content">

		<?php
		// the_excerpt();
		echo wp_trim_excerpt();
		?>

	</div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->
