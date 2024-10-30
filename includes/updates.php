<?php
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

$last_version = get_option('melipayamak_last_version');
$update_version = false;

if (version_compare($last_version, '1.8') < 0 && $wpdb) {
    ignore_user_abort(true);

    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_messages` MODIFY `date` varchar(50) NOT NULL;");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_messages` MODIFY `message` text CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL;");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_messages` MODIFY `sender` varchar(50) NOT NULL;");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_messages` MODIFY `recipient` text NOT NULL;");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_messages` MODIFY `date` varchar(50) NOT NULL;");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_messages` ADD KEY `date` (`date`)");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_messages` ADD KEY `sender` (`sender`)");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_messages` ADD KEY `mode` (`mode`)");

    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_members` MODIFY `date` varchar(50) NOT NULL;");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_members` MODIFY `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL;");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_members` MODIFY `lname` varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL;");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_members` MODIFY `gid` bigint(20) NOT NULL;");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_members` ADD KEY `mobile` (`mobile`)");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_members` ADD KEY `gid` (`gid`)");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_members` ADD KEY `status` (`status`)");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_members` ADD KEY `sync` (`sync`)");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_members` ADD KEY `date` (`date`)");

    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_groups` MODIFY `gdate` varchar(50) NOT NULL;");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_groups` MODIFY `gname` varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL;");
    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_groups` ADD KEY `gshow` (`gshow`)");

    $last_version = '1.8';
    $update_version = true;
}

if (version_compare($last_version, '2.0') < 0 && $wpdb) {
    ignore_user_abort(true);

    $wpdb->query("ALTER TABLE `{$table_prefix}melipayamak_messages` ADD `is_voice` SMALLINT(1) NOT NULL DEFAULT '0' AFTER `delivery`;");

    $last_version = '2.0';
    $update_version = true;
}

if (version_compare($last_version, '2.2') < 0 && $wpdb) {
    ignore_user_abort(true);

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE IF NOT EXISTS `{$table_prefix}melipayamak_GFVerification` (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			form_id mediumint(8) unsigned not null,
            lead_id mediumint(10) unsigned not null,	
            try_num mediumint(10) unsigned not null,
            sent_num mediumint(10) unsigned not null,		
			mobile VARCHAR(12) NOT NULL,
			code VARCHAR(250),
			status tinyint(1),
			PRIMARY KEY  (id),
			KEY form_id (form_id)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

    $last_version = '2.2';
    $update_version = true;
}

if ($update_version)
    update_option('melipayamak_last_version', $last_version);
