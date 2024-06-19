<?php
function CW_Context_Menu_shortcode() {
    global $id;
    if ( !is_page() ) { return; }
    $current = get_post($id);
    $return = '';
    $top_level = ($current->post_parent) ? end(get_post_ancestors($current)) : $current->ID;
    $return .= '<div class="context-menu-container">';
    $return .= '<a href="' . get_permalink($top_level) . '" title="' . get_the_title($top_level) . '"><h4 class="menu-title">' . get_the_title($top_level) . '</h4></a>';
    $return .= '<ul class="contextmenu">';
    if(!$current->post_parent){
        $children = wp_list_pages("title_li=&sort_column=menu_order&child_of=".$current->ID."&echo=0");
    } else {
        if($current->ancestors) {
            $ancestors = end($current->ancestors);
            $children = wp_list_pages("title_li=&sort_column=menu_order&child_of=".$ancestors."&echo=0");
        }
    }
    
    if ($children) {
        $return .= $children;
    }
    
    $return .= '</ul>';
    $return .= '</div>';
    return $return;
}
add_shortcode( 'CW_Context_Menu', 'CW_Context_Menu_shortcode' );
