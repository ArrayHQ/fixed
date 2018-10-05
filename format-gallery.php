<?php
/**
 * Template for gallery posts.
 *
 * @package Fixed
 * @since 1.0
 */

					if( function_exists( 'array_gallery' ) ) {
						array_gallery();
					} ?>

					<div class="box-wrap">
						<div class="box clearfix">
							<!-- post content -->
							<div class="post-content">
								<header>
									<?php if( is_single() || is_page() ) { ?>
										<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
									<?php } else { ?>
										<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
									<?php } ?>
								</header>

								<?php if( is_search() || is_archive() ) { ?>
									<div class="excerpt-more">
										<?php the_excerpt( __( 'Read More', 'fixed' ) ); ?>
									</div>
								<?php } else { ?>
									<?php the_content( __( 'Read More', 'fixed' ) ); ?>

									<?php if( is_single() || is_page() ) { ?>
										<div class="pagelink">
											<?php wp_link_pages(); ?>
										</div>
									<?php } ?>
								<?php } ?>
							</div><!-- post content -->
						</div><!-- box -->
					</div><!-- box wrap -->
