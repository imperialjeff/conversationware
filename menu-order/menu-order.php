<?php
/**
 * Add "Menu Order" column toggle to Screen Options
 */
add_filter( 'manage_pages_columns', function ( $columns ) {
	$columns['menu_order'] = __( 'Menu Order' );
	return $columns;
});

/**
 * Make column removable via Screen Options
 */
add_filter( 'manage_pages_columns', function ( $columns ) {
	if ( ! get_user_meta( get_current_user_id(), 'show_menu_order_column', true ) ) {
		unset( $columns['menu_order'] );
	}
	return $columns;
});

add_action( 'load-edit.php', function () {
	$screen = get_current_screen();

	if ( $screen->post_type !== 'page' ) {
		return;
	}

	add_filter( 'screen_settings', function ( $settings ) {
		$checked = get_user_meta( get_current_user_id(), 'show_menu_order_column', true ) ? 'checked' : '';

		$settings .= '
			<fieldset class="metabox-prefs">
				<label>
					<input type="checkbox" id="toggle-menu-order-column" ' . $checked . '>
					' . __( 'Menu Order' ) . '
				</label>
			</fieldset>';

		return $settings;
	});
});

add_action( 'admin_init', function () {
	if ( isset( $_POST['show_menu_order_column'] ) ) {
		update_user_meta(
			get_current_user_id(),
			'show_menu_order_column',
			(int) $_POST['show_menu_order_column']
		);
	}
});

add_action( 'manage_pages_custom_column', function ( $column, $post_id ) {
	if ( $column !== 'menu_order' ) {
		return;
	}

	$post = get_post( $post_id );

	echo '<input
		type="number"
		class="menu-order-input"
		data-post-id="' . esc_attr( $post_id ) . '"
		value="' . esc_attr( $post->menu_order ) . '"
		style="width:70px;"
	/>';
}, 10, 2 );

add_action( 'wp_ajax_update_menu_order', function () {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_send_json_error();
	}

	$post_id    = intval( $_POST['post_id'] );
	$menu_order = intval( $_POST['menu_order'] );

	wp_update_post( [
		'ID'         => $post_id,
		'menu_order' => $menu_order,
	] );

	wp_send_json_success();
});

add_action( 'admin_footer-edit.php', function () {
	$screen = get_current_screen();
	if ( $screen->post_type !== 'page' ) return;
	?>
	<script>
		jQuery(document).on('change', '.menu-order-input', function () {
			jQuery.post(ajaxurl, {
				action: 'update_menu_order',
				post_id: jQuery(this).data('post-id'),
				menu_order: jQuery(this).val()
			});
		});

		jQuery('#toggle-menu-order-column').on('change', function () {
			jQuery.post(ajaxurl, {
				show_menu_order_column: this.checked ? 1 : 0
			}, function () {
				location.reload();
			});
		});
	</script>
	<?php
});
