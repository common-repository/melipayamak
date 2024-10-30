<?php
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
//widget
add_action("widgets_init", array(
    'melipayamak_widget',
    'register'
));
register_activation_hook(__FILE__, array(
    'melipayamak_widget',
    'activate'
));
register_deactivation_hook(__FILE__, array(
    'melipayamak_widget',
    'deactivate'
));
class melipayamak_widget {
    function activate() {
        $data = array(
            'width' => '300',
            'before' => '',
            'after' => '',
            'title' => 'عضویت در خبرنامه پیامکی'
        );
        update_option('melipayamak_widget', $data);
    }

    function deactivate() {
        delete_option('melipayamak_widget');
    }

    function control() {
        $data = get_option('melipayamak_widget');
        if (isset($_POST['melipayamak'])) {
            $data['width'] = esc_html($_POST['melipayamak']['width']);
            $data['before'] = esc_html($_POST['melipayamak']['before']);
            $data['after'] = esc_html($_POST['melipayamak']['after']);
            $data['title'] = esc_html($_POST['melipayamak']['title']);
            update_option('melipayamak_widget', $data);
        }
        echo "<p><label>عنوان ابزارک</label><br/><input name='melipayamak[title]' type='text' value='{$data['title']}' /></p>";
        echo "<p><label>عرض فرم(استفاده از CSS مجاز است.)</label><br/><input name='melipayamak[width]' type='text' value='{$data['width']}' /></p>";
        echo "<p><label>متن قبل از فرم</label><br/><textarea name='melipayamak[before]'>{$data['before']}</textarea></p>";
        echo "<p><label>متن بعد از فرم</label><br/><textarea name='melipayamak[after]'>{$data['after']}</textarea></p>";
    }

    function widget($args) {
        $data = get_option('melipayamak_widget');
        echo $args['before_widget'];
        echo $args['before_title'] . $data['title'] . $args['after_title'] . $data['before'];
        if (is_numeric($data['width']))
            $data['width'] .= 'px';
        if (empty($data['width']))
            $data['width'] = '100%';
        echo "<iframe src=\"" . get_bloginfo('url') . "/?melipayamak_mini=1\" onload=\"this.style.height=this.contentWindow.document.body.scrollHeight+'px';this.style.display='inline'\" width=\"" . $data['width'] . "\" allowtransparency=\"yes\"  scrolling=\"no\" frameborder=\"0\"></iframe>";
        echo $data['after'] . $args['after_widget'];
    }

    function register() {
        register_sidebar_widget('ملی پیامک', array(
            'melipayamak_widget',
            'widget'
        ));
        register_widget_control('ملی پیامک', array(
            'melipayamak_widget',
            'control'
        ));
    }

}
