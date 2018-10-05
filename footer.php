<?php
/**
 * Template for displaying the footer.
 *
 * @package Fixed
 * @since 1.0
 */
?>
		</div><!-- main -->

		<header class="header">
			<!-- Logo and site title -->
			<div class="logo-wrap">
				<?php
					$logo = get_theme_mod( 'fixed_theme_customizer_logo' );
					if ( ! empty( $logo ) ) {
				?>
					<h1 class="logo-image">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" /></a>
					</h1>
				<?php } else { ?>
					<hgroup>
						<h1 class="logo-text"><a href="<?php echo home_url( '/' ); ?>" title="<?php bloginfo( 'name' ); ?>"><?php bloginfo( 'name' ) ?></a></h1>
						<h2 class="logo-subtitle"><?php bloginfo( 'description' ) ?></h2>
					</hgroup>
				<?php } ?>
			</div>

			<div class="widgets">
				<?php dynamic_sidebar( 'sidebar' ); ?>

				<div class="widget">
					<div class="copyright">&copy; <?php echo date( 'Y' ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a> | <?php bloginfo( 'description' ); ?></div>
				</div>
			</div>

		</header>
	</div><!-- wrapper -->

	<?php wp_footer(); ?>
</body>
</html>