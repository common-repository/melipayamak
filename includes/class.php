<?php
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

class melipayamak
{
    public $groups = array(), $is_ready = false, $hasnt = false, $update, $username, $password, $tel, $credit = false, $connection = true, $date, $ip, $count = 0;

    //getting ready
    public function __construct()
    {
        global $melipayamak_version, $wpdb, $table_prefix;
        //should force update informations?
        if (isset($_REQUEST['melipayamak_update']) && is_admin())
            $force_update = true;
        //set update period time
        $update_period = (intval(get_option('melipayamak_update_period') >= 1) && intval(get_option('melipayamak_update_period') < 13)) ? get_option('melipayamak_update_period') : 6;
        $this->username = get_option('melipayamak_username');
        $this->password = get_option('melipayamak_password');
        $this->tel = get_option('melipayamak_tel');
        $this->ip = '37.228.138.118';
        if ($this->access())
            $this->count = $wpdb->get_var("SELECT count(id) FROM {$table_prefix}melipayamak_members");
        if (empty($this->username) || empty($this->password) || empty($this->tel)) {
            return false;
        } else {
            if (class_exists('SoapClient')) {
                @ini_set("soap.wsdl_cache_enabled", "0");
                $this->set_credit();
                if ($this->credit == '') {
                    $this->connection = false;
                    return false;
                } else {
                    if (intval($this->credit) === 0) {
                        return false;
                    } else {
                        $this->is_ready = true;
                        if (is_admin()) {
                            $now = time();
                            $update = get_option('melipayamak_update');
                            $last = $update[0];
                            if ($now - ($update_period * 60 * 60) > $last || $force_update) {
                                $result = wp_remote_get('https://hamyar.org/download/dl.php?do=json&plugin=melipayamak&json=true');
                                if (!is_wp_error($result))
                                    update_option('melipayamak_update', array(
                                        $now,
                                        isset($result) ? $result['body'] : ''
                                    ));
                            }
                            $update = get_option('melipayamak_update');
                            $result = $update[1];
                            if (!empty($result)) {
                                $result = json_decode($result, true);
                                if (version_compare($result[0], $melipayamak_version) > 0) {
                                    $this->update = '<div class="error"><p>نسخه ' . $result[0] . ' پلاگین ملی پیامک هم اکنون آماده است. <a href="' . $result[1] . '">لطفا هم اکنون بروزرسانی کنید.</a></p></div>';
                                } else
                                    $this->update = false;
                            } else
                                $this->update = false;
                            return true;
                        } else {
                            $this->hasnt = 'JSON';
                            return false;
                        }
                    }
                }
            } else {
                $this->hasnt = 'SoapClient';
                return false;
            }
        }
    }

    //call to melipayamak
    public function call($action, $name, $parameters = array(), $r = true)
    {
        try {
            $url = 'http://' . $this->ip . '/post/' . $name . '.asmx?wsdl';
            $sms_client = new SoapClient($url, array('encoding' => 'UTF-8'));
            $parameters['username'] = $this->username;
            $parameters['password'] = $this->password;
            $result = $sms_client->$action($parameters);
            $action .= 'Result';
            if ($r)
                return $result = $result->$action;
            else
                return $result;
        } catch (SoapFault $ex) {
            if (defined('WP_DEBUG') AND WP_DEBUG === true)
                echo $ex;
            return false;
        }
    }

    //check premission
    public function check_premission()
    {
        if (!$this->access()) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        if (defined('MELIPAYAMAK_READY') && !$this->is_ready) {
            if (!$this->connection)
                wp_die('در برقراری ارتباط با ملی پیامک مشکلی پیش آمد، پلاگین قادر به ادامه فعالیت نیست.<br/><a href="admin.php?page=melipayamak_setting&tab=webservice">برای تلاش مجدد کلیک کنید.</a> همچنین ممکن است حساب شما منقضی شده باشد یا اعتبار شما صفر باشد.');
            else
                wp_die('<a href="admin.php?page=melipayamak_setting&tab=webservice">لطفا جهت ادامه فعالیت پلاگین، تنظیمات وب سرویس پلاگین را بررسی نمایید.</a>');
        }
    }

    //check access
    public function access()
    {
        return current_user_can('manage_options');
    }

    //fetch melipayamak groups
    public function fetch_groups($show, $selected)
    {
        $this->update_groups();
        $groups = get_option('melipayamak_groups');
        $groups = $groups[1]['GroupsList'];
        if ($groups['GroupID']) {
            $b = $groups;
            unset($groups);
            $groups[] = $b;
        }
        if ($show) {
            if ($groups[0]) {
                $return = "<select style='width: 200px;height: 34px;' name='melipayamak_group' required>";
                foreach ($groups as $key => $value) {
                    $select = '';
                    if ($value['GroupID'] == $selected)
                        $select = 'selected';
                    $return .= "<option value='{$value['GroupID']}' {$select}>{$value['GroupName']}</option>";
                }
                return $return . '</select>';
            } else {
                return "<select style='width: 200px;height: 34px;' name='melipayamak_group' required><option disabled value=''>هیچ گروهی یافت نشد.</option></select>";
            }
        } else {
            return $groups;
        }
    }

    //phonebook groups
    public function fetch_phonebook_groups($show, $selected = null, $select = true, $where = false)
    {
        global $table_prefix, $wpdb;
        if (!$GLOBALS['mpcache']) {
            if ($where)
                $where = "WHERE gshow = '1'";
            $GLOBALS['mpcache'] = $wpdb->get_results("SELECT * FROM {$table_prefix}melipayamak_groups {$where} ORDER BY gid DESC", ARRAY_A);
        }
        $groups = $GLOBALS['mpcache'];
        if ($show) {
            if ($groups[0]) {
                if ($select)
                    $return = "<select  name='group' required>";
                else
                    $return = '';
                foreach ($groups as $key => $value) {
                    $select = '';
                    if ($value['gid'] == $selected)
                        $select = 'selected';
                    $return .= "<option value='{$value['gid']}' {$select}>{$value['gname']}</option>";
                }
                if ($select)
                    return $return . '</select>';
                else
                    return $return;
            } else {
                if ($select)
                    return "<select  name='group' required><option disabled value=''>هیچ گروهی یافت نشد.</option></select>";
                else
                    return null;
            }
        } else {
            return $groups;
        }
    }

    //update groups
    public function update_groups()
    {
        $result = $this->call('GetGroups', 'contacts');
        if (!empty($result)) {
            $result = melipayamak_o2a($result);
            update_option('melipayamak_groups', array(
                time(),
                $result
            ));
        }
    }

    //shamsi date
    public function date($time = null, $format = 'Y/m/d H:i', $tz = null)
    {
        if (!$time)
            $time = time();
        if (!is_object($this->date))
            $this->date = new jDateTime(false, true, 'Asia/Tehran');

        return $this->date->date($format, $time);
    }

    //send sms
    public function send($to, $message, $is_auto = 1, $flash = false, $update = true)
    {
        if (!$message || !$to[0])
            return false;
        $message = html_entity_decode($message);
        if (count($to) == 1) {
            $to[0] = str_replace(array(
                '+98',
                '+',
                '-'
            ), '', $to[0]);
        }
        global $wpdb, $table_prefix;
        $message = ($is_auto == 1) ? $message . "\n" . get_option('melipayamak_sig') : $message;
        $parameters = array();
        $parameters['from'] = $this->tel;
        $parameters['to'] = $to;
        $parameters['text'] = $message;
        $parameters['isflash'] = $flash;
        $parameters['udh'] = "";
        if (count($to) > 10) {
            $parameters['recId'] = array(0);
            $parameters['status'] = 0x0;
            $result = $this->call('SendSms', 'send', $parameters, false);
            if ($result->SendSmsResult == 1) {
                $time = $this->date();
                $to = melipayamak_clean(implode(',', $to));
                $message = melipayamak_clean($message);
                $flash = ($flash) ? 1 : 0;
                $wpdb->insert($table_prefix . 'melipayamak_messages', array(
                    'flash' => $flash,
                    'date' => $time,
                    'message' => $message,
                    'sender' => $this->tel,
                    'recipient' => $to,
                    'mode' => $is_auto,
                    'is_voice' => 0
                ));
                if ($update)
                    $this->set_credit(true);
                return $result;
            } else {
                return false;
            }
        } else {
            if (get_option('melipayamak_use_voice')) {
                $parameters['speechBody'] = $parameters['text'];
                $parameters['smsBody'] = $parameters['text'];
                $parameters['to'] = implode(',', $parameters['to']);
                $result = $this->call('SendSMSWithSpeechText', 'Voice', $parameters);
            } else {
                $result = $this->call('SendSimpleSMS', 'send', $parameters);
                $result = $result->string;
            }
            $time = $this->date();
            $tob = $to;
            $to = melipayamak_clean(implode(',', $to));
            $message = melipayamak_clean($message);
            $de = array();
            $de['time'] = time();
            $flash = ($flash) ? 1 : 0;
            if (is_array($result)) {
                foreach ($result as $key => $value) {
                    $de[$tob[$key]] = array(
                        $value,
                        ''
                    );
                }
                $de = json_encode($de);
                $wpdb->insert($table_prefix . 'melipayamak_messages', array(
                    'flash' => $flash,
                    'date' => $time,
                    'message' => $message,
                    'sender' => $this->tel,
                    'recipient' => $to,
                    'mode' => $is_auto,
                    'delivery' => $de,
                    'is_voice' => get_option('melipayamak_use_voice') == true ? 1 : 0
                ));
                if ($update)
                    $this->set_credit(true);
                return true;
            } else {
                foreach (explode(',', $result) as $key => $value) {
                    $de[$tob[$key]] = array(
                        $value,
                        ''
                    );
                }
                $de = json_encode($de);
                $wpdb->insert($table_prefix . 'melipayamak_messages', array(
                    'flash' => $flash,
                    'date' => $time,
                    'message' => $message,
                    'sender' => $this->tel,
                    'recipient' => $to,
                    'mode' => $is_auto,
                    'delivery' => $de,
                    'is_voice' => get_option('melipayamak_use_voice') == true ? 1 : 0
                ));
                if (!in_array($result, array(
                    0,
                    2,
                    3,
                    4,
                    5,
                    6,
                    7,
                    8,
                    9,
                    10,
                    11,
                    12
                ))) {
                    if ($update)
                        $this->set_credit(true);
                    return true;
                } else
                    return false;
            }
        }
    }

    //set user credit
    public function set_credit($force = false)
    {
        global $wpdb;
        $update_period = (intval(get_option('melipayamak_update_period') >= 1)) ? get_option('melipayamak_update_period') : 6;
        if (((isset($_REQUEST['melipayamak_update']) || (isset($_REQUEST['page']) && $_REQUEST['page'] == 'melipayamak_setting' && $_REQUEST['tab'] == 'webservice')) && is_admin()) || $force)
            $force_update = true;
        if (isset($_GET['page']) && $_GET['page'] == 'melipayamak_send' && is_admin())
            $force_update = true;
        if (isset($_GET['page']) && $_GET['page'] == 'melipayamak_reports' && is_admin())
            $force_update = true;
        $now = time();
        $credit = get_option('melipayamak_credit');
        $last = $credit[0];
        if ($now - ($update_period * 60 * 60) > $last || isset($force_update)) {
            $credit = $this->call('GetCredit', 'Send');
            update_option('melipayamak_credit', array(
                time(),
                $credit
            ));
            $this->credit = $credit;
        } else {
            $this->credit = $credit[1];
        }
    }

    //create a string to show for user in correct way :)
    public function nl2br($string)
    {
        $string = str_replace(array(
            '<br/>',
            '<br />',
            '<br>',
            '</br>'
        ), '%melipayamak_br%', $string);
        $string = trim($string);
        $string = htmlspecialchars($string, ENT_QUOTES);
        $string = str_replace(array(
            '\r\n',
            '\r',
            '\n'
        ), "<br/>", $string);
        $string = stripslashes($string);
        $string = " " . $string;
        $string = preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1">$1</a>', $string);
        $string = str_replace('%melipayamak_br%', '<br/>', $string);
        $string = str_replace(array(
            '<br/>',
            '<br />',
            '<br>',
            '</br>'
        ), '<br/>', $string);
        $string = str_replace(array(
            '<br/><br/>',
            '<br/><br/><br/>',
            '<br/><br/><br/><br/>'
        ), '<br/>', $string);
        return nl2br(stripslashes($string));
    }

    public function nl2br2($string)
    {
        $string = trim($string);
        $string = str_replace(array(
            '\r\n',
            '\r',
            '\n'
        ), "<br/>", $string);
        $string = str_replace(array(
            '<br/>',
            '<br />',
            '<br>',
            '</br>'
        ), '<br/>', $string);
        $string = str_replace(array(
            '<br/><br/>',
            '<br/><br/><br/>',
            '<br/><br/><br/><br/>'
        ), '<br/>', $string);
        return nl2br($string);
    }

}
