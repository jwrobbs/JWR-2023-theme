<?php
/**
 * Navbar branding
 *
 * @package Understrap
 * @since 1.2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

echo "<div class='navbar-logo-container'>";
if ( ! has_custom_logo() ) { ?>

	<?php if ( is_front_page() && is_home() ) : ?>

		<h1 class="navbar-brand mb-0 p-0">
			<a rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" itemprop="url">
				<?php bloginfo( 'name' ); ?>
			</a>
		</h1>

	<?php else : ?>

		<a class="navbar-brand" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" itemprop="url">
			<?php bloginfo( 'name' ); ?>
		</a>

	<?php endif; ?>

	<?php
} else {
	the_custom_logo();
}
echo "<div class='motto'>Write code. Speak human.</div>";
echo "</div>";
