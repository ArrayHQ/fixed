<?php
/*
Template Name: Custom Archive
*/
get_header(); ?>

		<div id="content">
			<div class="posts">

				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<article <?php post_class( 'post' ); ?>>

					<?php if ( has_post_thumbnail() ) { ?>
						<a class="featured-image" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail( 'large-image' ); ?></a>
					<?php } ?>

					<div class="box-wrap">
						<div class="box">
							<header>
								<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
							</header>

							<!-- post content -->
							<div class="post-content">
								<?php the_content( __( 'Read More', 'fixed' ) ); ?>

								<div id="archive">
									<div class="archive-col">
										<div class="archive-box">
											<h4><?php _e( 'Pages', 'fixed' ); ?></h4>
											<ul>
												<?php wp_list_pages( 'sort_column=menu_order&title_li=' ); ?>
											</ul>
										</div>

										<div class="archive-box">
											<h4><?php _e( 'Categories', 'fixed' ); ?></h4>
											<ul>
												<?php wp_list_categories( 'orderby=name&title_li=' ); ?>
											</ul>
										</div>
									</div><!-- column -->

									<div class="archive-col">
										<div class="archive-box">
											<h4><?php _e( 'Latest Posts', 'fixed' ); ?></h4>
											<ul>
												<?php wp_get_archives( 'type=postbypost&limit=15' ); ?>
											</ul>
										</div>
									</div><!-- column -->
								</div><!-- archive -->
							</div><!-- post content -->
						</div><!-- box -->
					</div><!-- box wrap -->
				</article><!-- post-->

				<?php endwhile; ?>
				<?php endif; ?>
			</div>

		</div><!-- content -->

		<!-- footer -->
		<?php get_footer(); ?>