<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Fixed
 * @since 1.0
 */
get_header(); ?>

		<div id="content">
			<div class="posts">

				<!-- titles -->
				<?php if( is_search() ) { ?>
					<h2 class="archive-title"><i class="fa fa-search"></i> <?php printf( __( 'Search Results for: %s', 'fixed' ), '<span>' . get_search_query() . '</span>' ); ?></h2>
				<?php } else if( is_tag() ) { ?>
					<h2 class="archive-title"><i class="fa fa-tag"></i> <?php single_tag_title(); ?></h2>
				<?php } else if( is_day() ) { ?>
					<h2 class="archive-title"><i class="fa fa-time"></i> <?php _e( 'Archive:', 'fixed' ); ?> <?php echo get_the_date(); ?></h2>
				<?php } else if( is_month() ) { ?>
					<h2 class="archive-title"><i class="fa fa-clock-o"></i> <?php echo get_the_date( 'F Y' ); ?></h2>
				<?php } else if( is_year() ) { ?>
					<h2 class="archive-title"><i class="fa fa-clock-o"></i> <?php echo get_the_date( 'Y' ); ?></h2>
				<?php } else if( is_category() ) { ?>
					<h2 class="archive-title"><i class="fa fa-folder-open"></i> <?php single_cat_title(); ?></h2>
				<?php } else if( is_author() ) { ?>
					<h2 class="archive-title"><i class="fa fa-pencil"></i> <?php the_post(); printf( __( 'Author: %s', 'publisher' ), '' . get_the_author() . '' ); rewind_posts(); ?></h2>
				<?php } ?>

				<!-- grab the posts -->
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<article <?php post_class( 'post clearfix' ); ?>>
					<!-- grab the post format template -->
					<?php
						if( 'gallery' == get_post_format() ) {
							get_template_part( 'format', 'gallery' );
						} else if( 'status' == get_post_format() ) {
							get_template_part( 'format', 'status' );
						} else if( 'quote' == get_post_format() ) {
							get_template_part( 'format', 'quote' );
						} else {
							get_template_part( 'format', 'standard' );
						}
					?>

					<!-- Post meta -->
					<?php if( ! is_page() ) { ?>
						<ul class="meta">
							<li><span><?php _e( 'Date', 'fixed' ); ?></span> <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo get_the_date(); ?></a></li>
							<li><span><?php _e( 'Author', 'fixed' ); ?></span> <?php the_author_posts_link(); ?></li>

							<!-- Show categories and tags on single -->
							<?php if( is_single() ) { ?>
								<li><span><?php _e( 'Category', 'fixed' ); ?></span> <?php the_category(' '); ?></li>
								<?php $posttags = get_the_tags();
								if ( $posttags ) { ?>
									<li><span><?php _e( 'Tag', 'fixed' ); ?></span> <?php the_tags('', ' ', ''); ?></li>
								<?php } ?>
							<?php } ?>

							<li><span><?php _e( 'Comments', 'fixed' ); ?></span> <a href="<?php the_permalink(); ?>#comments-title" title="comments"><?php comments_number( __( 'No Comments', 'fixed' ), __( '1 Comment', 'fixed' ), __( '% Comments', 'fixed' ) );?></a></li>
						</ul>
					<?php } ?>
				</article><!-- post-->

				<?php endwhile; ?>
				<?php endif; ?>
			</div>

			<?php fixed_page_nav(); ?>

			<!-- comments -->
			<?php if( is_single () ) { ?>
				<?php if ( 'open' == $post->comment_status ) { ?>
				<div id="comment-jump" class="comments">
					<?php comments_template(); ?>
				</div>
				<?php } ?>
			<?php } ?>
		</div><!-- content -->

		<!-- footer -->
		<?php get_footer(); ?>