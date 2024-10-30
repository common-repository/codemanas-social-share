<?php
$options                     = get_option( 'codemanas_social_sharing_options' );
$cm_allowed_options          = ! empty( $options['cm_allowed'] ) ? $options['cm_allowed'] : '';
$cm_where_to_show            = ! empty( $options['cm_where_to_show'] ) ? $options['cm_where_to_show'] : '';
$cm_choose_size              = ! empty( $options['cm_icon_size'] ) ? $options['cm_icon_size'] : '';
$cm_selected_social_networks = ! empty( $options['cm_selected_social_networks'] )
	? $options['cm_selected_social_networks'] : '';
$social_networks_to_show     = ! empty( $options['social_networks_to_show'] ) ? $options['social_networks_to_show']
	: $this->social_links;
$cm_icon_color               = ! empty( $options['cm_icon_color'] )
	? $options['cm_icon_color'] : '';
$cm_shortcode                = codemanasSocialShareShortcode::get_instance();
$color_style                 = '';

if ( $options['cm_choose_color'] == 'other-color' ) {
	$color_style = ! empty( $cm_icon_color ) ? 'color:' . $cm_icon_color . ';' : '';
}

if ( $cm_choose_size == 'small' ) {
	$font_size = '16px';
} else if ( $cm_choose_size == 'medium' ) {
	$font_size = '24px';
} else if ( $cm_choose_size == 'large' ) {
	$font_size = '32px';
}
$font_style = ! empty( $font_size ) ? 'font-size:' . $font_size . ';' : '';
?>
<?php /*will not move style to it's own file unless it's necessary*/ ?>
<style>
    section {
        clear: both
    }

    .cm-show-option {
        width: 33.33%;
        float: left;
    }

    @media (max-width: 767px) {
        .cm-show-option {
            width: 100%;
            float: left;
        }
    }

    .cm-full-width {
        width: 100%;
    }

    ul.cm-share li a,
    ul.cm-share li a:hover {
    <?php
	echo $color_style;
	echo $font_style;
	?>
    }

    li.ui-sortable-handle:nth-child(odd) {
        background: #ccc;
    }

    li.ui-sortable-handle {
        padding: 5px;
        cursor: move;
    }
</style>
<div class="cm-full-width">
    <h2>Shortcode:</h2>
    <p class="description">
        You can use this Shortcode to show sharing options anywhere on your post / pages / or wherever you want to show
        it.
    </p>
    <label for="cm-shortcode">
        Shortcode:
        <input readonly="readonly" type="text" id="cm-shortcode" name="cm-shortcode" onfocus="this.select()" value="[codemanas_social_share]">
    </label>
</div>

<div class="codemanas-social-share-form-wrap">
    <form id="cm-share-form" class="form" action="" method="POST">
		<?php wp_nonce_field( 'verify_cm_settings_nonce', 'cm_settings_nonce' ); ?>
        <section>
            <div class="cm-show-option">
                <h2>Preview:</h2>
                <span class="description">Preview changes after you save the settings</span>
				<?php echo $cm_shortcode->codemanas_social_share(); ?>
            </div>
            <div class="cm-show-option">
                <h2>Select Where to Show:</h2>
                <span class="description">Select where to show the share icons</span>
                <ul>
					<?php
					$args = array(
						'public' => true
					);

					$post_types = get_post_types( $args, 'object' );
					foreach ( $post_types as $post_type ) {
						$checked = '';
						if ( ! empty( $cm_allowed_options ) && in_array( $post_type->name, $cm_allowed_options ) ) {
							$checked = 'checked="checked"';
						}
						?>
                        <li>
                            <label>
                                <input id="<?php echo $post_type->name ?>" type="checkbox" name="cm_allowed[]"
                                       value="<?php echo $post_type->name ?>" <?php echo $checked; ?> />
								<?php echo $post_type->label; ?>
                            </label>
                        </li>
						<?php
					}
					?>
                </ul>
            </div>
            <div class="cm-show-option">
                <h2>Select What Social Networks to Show:</h2>
                <span class="description">The list can be re-ordered by dragging to determine the order of the icons</span>
                <ul id="sortable">
					<?php
					foreach ( $social_networks_to_show as $social_link ) {
						$checked = ( $social_link['visible'] == false ) ? '' : 'checked="checked"';
						?>
                        <li>
                            <label for="<?php echo $social_link['name']; ?>">
                                <input id="<?php echo $social_link['name']; ?>" type="checkbox" name="cm_selected_social_networks[<?php echo $social_link['name']; ?>]" value="visible" <?php echo $checked; ?>/>
								<?php echo $social_link['label']; ?>
                            </label>
                            <input type="hidden" name="cm_social_link_order[]" value="<?php echo $social_link['name']; ?>">
                        </li>
						<?php
					}
					?>
                </ul>
            </div>
        </section>
        <section>
            <div class="cm-show-option">
				<?php
				$where_to_show_default = array(
					'before-the-content' => 'Before the Content',
					'after-the-content'  => 'After the Content',
					'float-left'         => 'Floating Left',
					'featured-image'     => 'Inside Featured Image'
				);
				?>
                <h2>Select How to Show:</h2>
                <ul>
					<?php
					foreach ( $where_to_show_default as $key => $value ) {
						$checked = '';
						if ( ! empty( $cm_where_to_show ) && in_array( $key, $cm_where_to_show ) ) {
							$checked = 'checked="checked"';
						}
						?>
                        <li>
                            <label>
                                <input type="checkbox" name="cm_where_to_show[]" value="<?php echo $key; ?>" <?php echo $checked; ?>>
								<?php echo $value; ?>
                            </label>
                        </li>
						<?php
					}
					?>
                </ul>
            </div>
            <div class="cm-show-option">
                <h3>Choose Color for Social Networks:</h3>
                <ul>
                    <li>
                        <label><input type="radio" name="cm_choose_color" value="original-color" <?php checked( 'original-color', $options['cm_choose_color'] ); ?>>Original Color</label>
                    </li>
                    <li>
                        <label><input type="radio" name="cm_choose_color" value="other-color" <?php checked( 'other-color', $options['cm_choose_color'] ); ?>>Alternate Color</label>
                    </li>
                </ul>
				<?php
				if ( empty( $options['cm_choose_color'] ) || $options['cm_choose_color'] != 'other-color' ) {
					$style = 'display:none';
				} else {
					$style = '';
				}
				?>

                <div class="cm-other-color" style="<?php echo $style; ?>">
                    <input title="Pick your color" type="text" class="cm-color-picker" name="cm_icon_color" value="<?php echo $cm_icon_color; ?>" data-default-color=""/>
                </div>
            </div>
            <div class="cm-show-option">
                <h2>Choose Size for Social Icon:</h2>
                <ul>
                    <li>
                        <label><input type="radio" name="cm_icon_size" value="small" <?php checked( 'small', $cm_choose_size ); ?>>Small ( 16px )</label>
                    </li>
                    <li>
                        <label><input type="radio" name="cm_icon_size" value="medium" <?php checked( 'medium', $cm_choose_size ); ?>>Medium ( 24px )</label>
                    </li>
                    <li>
                        <label><input type="radio" name="cm_icon_size" value="large" <?php checked( 'large', $cm_choose_size ); ?>>Large ( 32px )</label>
                    </li>
                </ul>
            </div>
        </section>
        <section>
            <div class="cm-show-option cm-full-width">
                <input title="Save form changes" type="submit" value="Save"
                       class="button button-primary">
            </div>
            <div style="clear: both;">
            </div>
        </section>
    </form>
</div>