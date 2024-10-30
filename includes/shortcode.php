<?php
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
//shortcode
function melipayamak_shortcode($atts) {
    extract(shortcode_atts(array(
        'mode' => '',
        'width' => ''
    ), $atts));
    if (is_numeric($width))
        $width .= 'px';
    if (empty($width))
        $width = '100%';
    switch($mode) {
        case '1' :
            return "<iframe src=\"" . get_bloginfo('url') . "/?melipayamak_mini=1\"  onload=\"this.style.height=this.contentWindow.document.body.scrollHeight+'px';this.style.display='inline'\" width=\"" . $width . "\" allowtransparency=\"yes\"  scrolling=\"no\" frameborder=\"0\"></iframe>";
            break;
        case '2' :
            return "<iframe src=\"" . get_bloginfo('url') . "/?melipayamak_large=1\"  onload=\"this.style.height=this.contentWindow.document.body.scrollHeight+'px';this.style.display='inline'\" width=\"" . $width . "\" allowtransparency=\"yes\"  scrolling=\"no\" frameborder=\"0\"></iframe>";
            break;
    }
}

add_shortcode('melipayamak', 'melipayamak_shortcode');
//add editor button
add_action('init', 'melipayamak_buttons');
function melipayamak_buttons() {
    add_filter("mce_external_plugins", "melipayamak_add_buttons");
    add_filter('mce_buttons', 'melipayamak_register_button');
}

function melipayamak_add_buttons($plugin_array) {
    $url = plugins_url('templates/assets/', __FILE__);
    $plugin_array['melipayamak'] = $url . '/editor.js';
    return $plugin_array;
}

function melipayamak_register_button($buttons) {
    array_push($buttons, 'melipayamak');
    return $buttons;
}
