<div class="wrap">
    <h1>CodeManas Social Share Options</h1>
    <div class="message">
		<?php
		$message = $this->get_message();
		if ( isset( $message ) && ! empty( $message ) ) {
			echo $message;
		}
		?>
    </div>
	<?php
	if ( ! isset( $_GET['tab'] ) || $_GET['tab'] == 'settings' ) {
		require_once( CODEMANAS_SS_DIR_PATH . '/inc/views/settings.php' );
	}
	?>
</div>