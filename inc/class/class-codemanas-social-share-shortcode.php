<?php

class codemanasSocialShareShortcode {
	public static $instance;
	public static $counter = 0;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function selected_styles() {
		$options       = get_option( 'codemanas_social_sharing_options' );
		$allowed_posts = $options['cm_allowed'];
		$size          = ! empty( $options['cm_icon_size'] ) ? $options['cm_icon_size'] : '';
		if ( ! empty( $allowed_posts ) && is_singular( $allowed_posts ) ):
			$color = '';
			if ( $options['cm_choose_color'] == 'other-color' ) {
				$color = ! empty( $options['cm_icon_color'] ) ? $options['cm_icon_color'] : '';
			}
			$color_style = ! empty( $color ) ? 'color:' . $color . ';' : '';

			if ( $size == 'small' ) {
				$font_size = '16px';
			} else if ( $size == 'medium' ) {
				$font_size = '24px';
			} else if ( $size == 'large' ) {
				$font_size = '32px';
			}
			$font_style = ! empty( $font_size ) ? 'font-size:' . $font_size . ';' : '';

			?>
            <style>
                ul.cm-share li a,
                ul.cm-share li a:hover {
                <?php
                echo $color_style;
                echo $font_style;
                ?>
                }
            </style>
			<?php

		endif;
	}

	public function __construct() {
		add_shortcode( 'codemanas_social_share', array( $this, 'codemanas_social_share' ) );
		add_action( 'wp_head', array( $this, 'selected_styles' ) );
	}

	public function codemanas_social_share( $atts = array() ) {
		$output = '';
		//don't display on archive pages
		//need to display preview on admin page
		if ( is_admin() || is_singular() ):
			wp_enqueue_style( 'codemanas-social-share' );
			wp_enqueue_script( 'codemanas-social-sharer' );
			$no_of_shortcodes        = self::$counter;
			$options                 = get_option( 'codemanas_social_sharing_options' );
			$cm_all_social_networks  = codemanasSocialShareAdmin::get_instance();
			$social_networks_to_show = ! empty( $options['social_networks_to_show'] ) ? $options['social_networks_to_show']
				: $cm_all_social_networks->social_links;
			$atts                    = shortcode_atts( array(
				'size'  => '',
				'color' => '',
			), $atts, 'codemanas_social_share' );

			ob_start();

			$color_style = ! empty( $atts['color'] ) ? 'color:' . $atts['color'] . ';' : '';
			$font_size   = '';
			if ( $atts['size'] == 'small' ) {
				$font_size = '16px';
			} else if ( $atts['size'] == 'medium' ) {
				$font_size = '24px';
			} else if ( $atts['size'] == 'large' ) {
				$font_size = '32px';
			}

			$font_style = ! empty( $font_size ) ? 'font-size:' . $font_size . ';' : '';
			if ( $font_style != '' || $color_style != '' ) {
				?>
                <style>
                    ul.cm-share.cm-share-<?php echo $no_of_shortcodes; ?> li a,
                    ul.cm-share.cm-share-<?php echo $no_of_shortcodes; ?> li a:hover {
                    <?php
					   echo $color_style;
					   echo $font_style;
					   ?>
                    }
                </style>
				<?php
			}
			?>
            <ul class="cm-share cm-share-<?php echo $no_of_shortcodes; ?>">
				<?php
				foreach ( $social_networks_to_show as $social_link ) {
					if ( $social_link['visible'] == false ) {
						continue;
					}
					if ( ! is_admin() && ! wp_is_mobile() && $social_link['name'] == 'whatsapp' ) {
						continue;
					}

					if ( $social_link['name'] == 'whatsapp' ) {
						$social_link['href_link'] = 'https://api.whatsapp.com/send?text=' . urlencode( get_permalink() );
					}
					?>
                    <li class="cm-<?php echo $social_link['name']; ?>">
                        <a href="<?php echo $social_link['href_link']; ?>"
                           title="<?php echo $social_link['title_attr']; ?>"
                           class="<?php echo $social_link['link_class']; ?>">
                            <i class="<?php echo $social_link['icon_class']; ?>"></i>
                        </a>
                    </li>
					<?php
				}
				?>
            </ul>
			<?php
			$output        = ob_get_clean();
			self::$counter = $no_of_shortcodes + 1;
		endif;

		return $output;
	}
}

add_action( 'plugins_loaded', array( 'codemanasSocialShareShortcode', 'get_instance' ) );