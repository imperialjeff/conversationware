<?php
/**
 * Plugin Name: Contextual Menu Block
 * Description: A plugin that adds a contextual menu block to Gutenberg with customizable parent-child levels.
 * Version: 1.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function contextual_menu_block_register_block() {
    wp_register_script(
        'contextual-menu-block',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'block.js' )
    );

    wp_register_style(
        'contextual-menu-block-editor',
        plugins_url( 'editor.css', __FILE__ ),
        array( 'wp-edit-blocks' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'editor.css' )
    );

    wp_register_style(
        'contextual-menu-block',
        plugins_url( 'style.css', __FILE__ ),
        array(),
        filemtime( plugin_dir_path( __FILE__ ) . 'style.css' )
    );

    register_block_type( 'contextual-menu/block', array(
        'editor_script' => 'contextual-menu-block',
        'editor_style'  => 'contextual-menu-block-editor',
        'style'         => 'contextual-menu-block',
    ) );
}

add_action( 'init', 'contextual_menu_block_register_block' );
