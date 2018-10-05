<?php
/**
 *
 * Displays all of the <head> section and everything through <div id="main">
 *
 * @package Fixed
 * @since 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<title><?php wp_title( '|', true, 'right' ); ?></title>

	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<!-- media queries -->
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0" />

	<!--[if lte IE 8]>
		<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/includes/styles/ie.css" media="screen"/>
		<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/includes/js/IE/html5shiv.js"></script>
	<![endif]-->

	<!-- add js class -->
	<script type="text/javascript">document.documentElement.className = 'js';</script>

	<!-- load scripts -->
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'fixed-bg' ); ?>>

	<div class="header-toggle">
		<a class="toggle-menu" href="#"><i class="fa fa-bars"></i> <?php _e( 'Menu', 'fixed' ); ?></a>
		<a class="toggle-widgets" href="#"><i class="fa fa-th-list"></i> <?php _e( 'Sidebar', 'fixed' ); ?></a>
	</div>

	<div class="fixed-bar">
		<div class="fixed-bar-inside">
			<a href="#" class="menu-toggle"><i class="fa fa-bars"></i> <?php _e( 'Menu', 'fixed' ); ?></a>

			<!-- Author profile drop down -->
			<?php
				if ( get_theme_mod( 'fixed_theme_customizer_user' ) ) {

				// Check for author description
				global $current_user;
				get_currentuserinfo();

				$featured_author = get_theme_mod( 'fixed_theme_customizer_user' );

				// Prepare the author for use in the profile
				$user_info = get_userdata( $featured_author );
				$user_email = $user_info->user_email;
			?>

				<div class="navatar-badge">
					<span class="navatar-name"><?php echo $user_info->display_name; ?></span>
					<?php if ( get_avatar( $user_email ) ) { ?>
						<?php echo get_avatar( $user_email, apply_filters( 'designer_author_bio_avatar_size', 50 ) ); ?>
					<?php } ?>
				</div>

				<div class="author-profile">
					<div class="author-profile-inside">
						<?php if ( get_avatar( $user_email ) ) { ?>
							<div class="author-avatar">
								<a class="big-avatar" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" title="<?php esc_attr_e( 'Posts by ', 'designer' ); ?> <?php the_author(); ?>">
										<?php echo get_avatar( $user_email, apply_filters( 'designer_author_bio_avatar_size', 125 ) ); ?>
									</a>
							</div>
						<?php } ?>

						<div class="author-description">
							<h2><?php printf( $user_info->display_name ); ?></h2>

							<?php if ( $user_info->description ) { ?>
								<p><?php echo $user_info->description; ?></p>
							<?php } else { ?>
								<p><?php _e( 'Enter a brief biography here by', 'fixed' ); ?><a href="<?php echo get_edit_user_link( $featured_author ); ?>" title="<?php _e( 'Edit your profile', 'fixed' ); ?>"> <?php _e( 'editing your profile &rarr;', 'fixed' ); ?></a></p>

							<?php } ?>

							<?php echo fixed_author_posts(); ?>
						</div>
					</div>
				</div><!-- author-profile -->
			<?php } ?><!-- if user selected -->

			<!-- nav menu -->
			<nav role="navigation" class="header-nav">
				<?php wp_nav_menu( array( 'theme_location' => 'main', 'menu_class' => 'nav' ) ); ?>
			</nav>

		</div><!-- fixed-bar-inside -->
	</div><!-- fixed-bar -->

	<!-- Find the header/sidebar code in footer.php -->

	<div id="wrapper">
		<div id="main">