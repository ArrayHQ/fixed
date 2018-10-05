<?php
/**
 * Template for status posts.
 *
 * @package Fixed
 * @since 1.0
 */
 ?>
					<div class="box-wrap status-wrap">
						<div class="box clearfix">
							<div class="format-status">
								<header>
									<?php if( is_single() || is_page() ) { ?>
										<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
									<?php } else { ?>
										<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
									<?php } ?>
								</header>

								<?php the_content( __( 'Read More', 'fixed' ) ); ?>
							</div>
						</div><!-- box -->
					</div><!-- box wrap -->
