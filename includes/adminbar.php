<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
//admin bar
if ($melipayamak -> access()) {
	add_action('admin_bar_menu', 'melipayamak_adminbar', 15);
	function melipayamak_adminbar() {
		global $wp_admin_bar, $melipayamak;
		if (!is_super_admin() || !is_admin_bar_showing())
			return;
		$wp_admin_bar -> add_menu(array('id' => 'melipayamak', 'title' => '<img style="padding-top: 8px" src="' . plugin_dir_url(__FILE__) . '/images/logo.png"/>', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=melipayamak'));
		$balance = $melipayamak -> credit;
		if ($balance && $melipayamak -> is_ready) {
			$balance = number_format($balance);
			$wp_admin_bar -> add_menu(array('parent' => 'melipayamak', 'title' => 'موجودی حساب: ' . $balance . ' پیامک', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=melipayamak_setting'));
		}
		$t = 'اعضای خبرنامه: ' . number_format(intval($melipayamak -> count)) . ' نفر';
		$wp_admin_bar -> add_menu(array('parent' => 'melipayamak', 'title' => $t, 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=melipayamak_phonebook'));
		$wp_admin_bar -> add_menu(array('parent' => 'melipayamak', 'title' => 'مشاهده پیام ها', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=melipayamak_smessages'));
		$wp_admin_bar -> add_menu(array('parent' => 'melipayamak', 'title' => 'ملی پیامک', 'href' => 'http://melipayamak.com'));
	}

}
