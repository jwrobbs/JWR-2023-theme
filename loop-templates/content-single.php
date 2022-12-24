<?php
/**
 * Single post partial template
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">

		<?php the_title( '<h1 class="entry-title text-center pb-3">', '</h1>' ); ?>

	</header><!-- .entry-header -->

	<div class='row'>
		<div class='meta-container col-md-3'>
			<div class="entry-meta">
				<?php 
					jwr_post_meta();
					jwr_page_toc();
				?>
			</div><!-- .entry-meta -->
		</div><!-- .meta-container -->
		<div class='content-container col-md-9 p-4'>
			<?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
			<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
			<div class="entry-content">

				<?php
				the_content();
				jwr_post_topics();
				echo jwr_related_content();
				understrap_link_pages();
				?>

			</div><!-- .entry-content -->


		</div><!-- .content-container -->
	</div>
	<footer class="entry-footer">

		<?php understrap_entry_footer(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->
