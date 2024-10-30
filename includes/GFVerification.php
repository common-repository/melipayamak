<?php

//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

class GFVerificationMelipayamak
{

    public static function construct()
    {

        if (is_admin()) {
            add_filter('gform_add_field_buttons', array('GFVerificationMelipayamak', 'gravity_sms_fields'));
            add_filter('gform_field_type_title', array('GFVerificationMelipayamak', 'title'), 10, 2);
            add_action('gform_editor_js_set_default_values', array('GFVerificationMelipayamak', 'default_label'));
            add_action('gform_editor_js', array('GFVerificationMelipayamak', 'js'));
            add_action('gform_field_standard_settings', array('GFVerificationMelipayamak', 'standard_settings'), 10, 2);
            add_filter('gform_tooltips', array('GFVerificationMelipayamak', 'tooltips'));
        }

        add_filter('gform_field_validation', array('GFVerificationMelipayamak', 'validation'), 10, 4);
        add_filter('gform_entry_post_save', array('GFVerificationMelipayamak', 'process'), 10, 2);
        add_action('gform_field_input', array('GFVerificationMelipayamak', 'input'), 10, 5);
        add_action('gform_field_css_class', array('GFVerificationMelipayamak', 'classes'), 10, 3);
        add_filter('gform_field_content', array('GFVerificationMelipayamak', 'content'), 10, 5);
        add_filter('gform_merge_tag_filter', array('GFVerificationMelipayamak', 'all_fields'), 10, 4);
    }

    public static function gravity_sms_fields($field_groups)
    {
        $gravity_sms_fields = array(
            'name' => 'gravity_sms_fields',
            'label' => 'فیلد‌های SMS',
            'fields' => array(
                array(
                    "class" => "button",
                    "value" => "معتبر سازی",
                    "onclick" => "StartAddField('sms_verification');"
                ),
            )
        );
        array_push($field_groups, $gravity_sms_fields);
        return $field_groups;
    }

    public static function title($title, $field_type)
    {
        if ($field_type == 'sms_verification')
            return $title = 'تایید موبایل';
        return $title;
    }

    public static function default_label()
    { ?>
        case "sms_verification" :
        field.label = 'تایید موبایل';
        break;
        <?php
    }

    public static function classes($classes, $field, $form)
    {
        if (!empty($field["type"]) && $field["type"] == "sms_verification")
            $classes .= " gfield_contains_required gform_sms_verification";
        return $classes;
    }

    public static function input($input, $field, $value, $lead_id, $form_id)
    {

        $field_arr = (array)$field;

        if ($field_arr["type"] == "sms_verification") {

            $form = GFAPI::get_form($form_id);

            $field_id = $field_arr["id"];
            $form_id = is_admin() && empty($form_id) ? rgget("id") : $form_id;

            $disabled_text = (is_admin() && RG_CURRENT_VIEW != 'entry') ? "disabled='disabled'" : '';

            $size = rgar($field_arr, "size");
            $class_suffix = RG_CURRENT_VIEW == 'entry' ? '_admin' : '';
            $class = $size . $class_suffix;

            $html5_attributes = '';

            $is_form_editor = (is_admin() && RG_CURRENT_VIEW != 'entry');
            $is_entry_page = (is_admin() && RG_CURRENT_VIEW == 'entry');
            $is_frontend = !is_admin();

            $tabindex = GFCommon::get_tabindex();

            if (!is_admin() && (RGFormsModel::get_input_type($field) == 'adminonly_hidden')) {
                return '';
            }


            $text_input = '<div class="ginput_container ginput_container_text ginput_container_verfication">';
            $text_input .= '<input name="input_' . $field_id . '" id="input_' . $form_id . '_' . $field_id . '" type="text" value="' . esc_attr($value) . '" class="verify_code ' . esc_attr($size) . '" ' . $tabindex . ' ' . $html5_attributes . ' ' . $disabled_text . '/>';

            if ($is_form_editor) {
                $input = $text_input;
                $input .= '</div><br/>';
                $input .= '<div class="gf-html-container ginput_container_verfication" id="ginput_container_verfication_' . $field_id . '">';
                $input .= '<span>';
                $input .= 'با استفاده از این فیلد، می‌توانید کاربر را مجبور به تایید کردن شماره موبایل خود کنید.';
                $input .= '</span>';
                $input .= '</div>';

            } else if ($is_entry_page) {
                $input = $text_input . '</div>';
            } else {

                $mobile_field_id = rgget("field_sms_verify_mobile", $field_arr);
                $mobile_field = RGFormsModel::get_field($form, $mobile_field_id);

                $diff_page = !empty($mobile_field['pageNumber']) && !empty($field_arr['pageNumber']) && $mobile_field['pageNumber'] != $field_arr['pageNumber'] ? true : false;

                if ($diff_page && apply_filters('sms_verify_self_validation', true)) {
                    $result = self::validation(array('action' => 'self'), $value, $form, $field_arr);
                }


                if (!$diff_page && apply_filters('gform_button_verify', true) && empty($field_arr['conditionalLogic'])) {
                    $max_page_num = GFFormDisplay::get_max_page_number($form);
                    if (!empty($field_arr['pageNumber']) && $field_arr['pageNumber'] == $max_page_num || !empty($field_arr['pageNumber']))
                        add_filter('gform_submit_button', array('GFVerificationMelipayamak', 'submit_button'), 10, 2);
                    else if ($max_page_num > 1)
                        add_filter('gform_next_button', array('GFVerificationMelipayamak', 'next_button'), 10, 2);
                }


                if (apply_filters('sms_verify_display_none', true)) {
                    return '<style type="text/css">#field_' . $form_id . '_' . $field_id . '{display:none !important;}</style>';
                } else {

                    $input = '';

                    if (apply_filters('sms_verify_field', false) || ($diff_page && apply_filters('sms_verify_field', false))) {
                        $input .= $text_input;
                        if (apply_filters('sms_verify_resend', false)) {
                            $input .= '<input id="gform_resend_button" class="gform_button button" name="resend_verify_sms" type="submit" value="ارسال مجدد">';
                        }
                        $input .= '</div>';
                    }

                    if (isset($result["message_"])) {
                        $input .= '<div class="ginput_container ginput_container_text ginput_container_verfication ginput_container_verfication_"><p>';
                        $input .= $result["message_"];
                        $input .= '</p></div>';
                    }
                }

            }


        }

        return $input;
    }


    public static function verify_table()
    {
        global $wpdb;
        return $wpdb->prefix . "melipayamak_GFVerification";
    }

    public static function update_verify($id, $try_num, $sent_num, $lead_id, $status)
    {
        global $wpdb;
        $sent_verify_table = self::verify_table();
        $lead_id = !empty($lead_id) ? $lead_id : '';
        $wpdb->update($sent_verify_table,
            array(
                'lead_id' => $lead_id,
                'try_num' => $try_num,
                'sent_num' => $sent_num,
                'status' => $status
            ),
            array('id' => $id),
            array(
                '%d',
                '%d',
                '%d',
                '%d'
            ),
            array('%d')
        );

    }

    public static function insert_verify($form_id, $lead_id, $mobile, $code, $status, $try_num, $sent_num)
    {
        global $wpdb;
        $sent_verify_table = self::verify_table();
        $lead_id = !empty($lead_id) ? $lead_id : '';
        $form_id = !empty($form_id) ? $form_id : 0;
        $wpdb->insert($sent_verify_table,
            array(
                'form_id' => $form_id,
                'lead_id' => $lead_id,
                'mobile' => $mobile,
                'code' => $code,
                'try_num' => $try_num,
                'sent_num' => $sent_num,
                'status' => $status
            ),
            array(
                '%d',
                '%d',
                '%s',
                '%s',
                '%d',
                '%d',
                '%d'
            )
        );
    }


    public static function validation($result, $value, $form, $field)
    {

        $field_arr = (array)$field;

        global $melipayamak;

        if ($field_arr["type"] == "sms_verification") {

            global $wpdb;
            $verify_table = self::verify_table();
            $form_id = $form['id'];


            $mobile_field_id = rgget("field_sms_verify_mobile", $field_arr);
            $mobile_field = RGFormsModel::get_field($form, $mobile_field_id);
            $mobile_value = self::get_mobile($field_arr, false);
            if ($mobile_field->noDuplicates && RGFormsModel::is_duplicate($form_id, $mobile_field, $mobile_value)) {
                return $result;
            }


            $show_input = true;
            $mobile = self::get_mobile($field_arr);
            if (empty($mobile) || strlen($mobile) < 3) {
                $result["is_valid"] = false;
                $show_input = false;
                $result["message"] = "لطفا شماره موبایل خود را جهت تایید وارد نمایید.";
            } else {

                $white_list = self::white_list($field_arr);

                if (!in_array($mobile, $white_list)) {

                    $get_result = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$verify_table} WHERE mobile = %s AND form_id = %s AND lead_id = %s ORDER BY id DESC LIMIT 1", $mobile, $form_id, 0));
                    if (!empty($get_result) && is_object($get_result)) {
                        $ID = $get_result->id;
                        $code = $get_result->code;
                        $status = $get_result->status;
                        $try_num = $get_result->try_num;
                        $sent_num = $get_result->sent_num;
                    } else {
                        $ID = '';
                        $code = '';
                        $status = '';
                        $try_num = '';
                        $sent_num = '';
                    }
                    $try_num = (!empty($try_num) && $try_num != 0) ? $try_num : 0;
                    $sent_num = (!empty($sent_num) && $sent_num != 0) ? $sent_num : 0;

                    $new_try_num = (isset($result["action"]) && $result["action"] == 'self') ? $try_num : $try_num + 1;

                    if (empty($code) || !$code) {
                        $type = rgget('sms_verify_code_type_radio', $field_arr);
                        if ($type == 'manual') {
                            $delimator = ',';
                            $manual = explode($delimator, rgget('sms_verify_code_type_manual', $field_arr));
                            $random_keys = array_rand($manual, 1);
                            $code = isset($manual[$random_keys[0]]) ? $manual[$random_keys[0]] : (isset($manual[$random_keys]) ? $manual[$random_keys] : rand(10000, 99999));
                        } else {
                            $code = self::rand_mask(rgget('sms_verify_code_type_rand', $field_arr));
                        }
                    }

                    $allowed_try = rgget('sms_verify_try_num', $field_arr);
                    $allowed_try = $allowed_try ? ($allowed_try - 1) : 10;

                    if ($try_num <= $allowed_try && !rgempty('input_' . $field_arr["id"]) && !empty($code) && rgpost('input_' . str_replace('.', '_', $field_arr["id"])) == $code) {
                        if (!empty($ID) && $ID != 0) {
                            self::update_verify($ID, $new_try_num, $sent_num, 0, 1);
                        } else {
                            self::insert_verify($form_id, 0, $mobile, $code, 1, $new_try_num, $sent_num);
                        }
                    } else if (($status != 1 && $status != '1') || empty($status) || $status == 0) {

                        $result["is_valid"] = false;

                        if ($try_num < $allowed_try) {

                            $message = rgget('sms_verify_code_msg_body', $field_arr);
                            $message = strpos($message, '%code%') === false ? $message . '%code%' : $message;
                            $message = $message ? $message : $code;
                            $message = str_replace('%code%', $code, $message);
                            //$message = GFCommon::replace_variables($message, $form, $lead, false, true, false);

                            $result["message"] = 'لطفا کد ارسال شده به موبایل خود را وارد نمایید.';

                            $allowed_send = rgget('sms_verify_sent_num', $field_arr);
                            $allowed_send = $allowed_send ? $allowed_send : 0;

                            if ($sent_num < $allowed_send) {
                                add_filter('sms_verify_resend', array('GFVerificationMelipayamak', 'apply_true'), 99);
                            }

                            if (!empty($ID) && $ID != 0) {

                                if (!rgempty('resend_verify_sms')) {

                                    $result["message"] = 'خطا در ارسال کد تایید';

                                    if ($sent_num <= $allowed_send) {
                                        $melipayamak->send(array($mobile), $message);
                                        $sent_num = $sent_num + 1;

                                        self::update_verify($ID, $try_num, $sent_num, 0, 0);

                                        $result["message"] = 'کد تایید مجددا ارسال شد.';
                                    }
                                } else if (!rgempty('input_' . $field_arr["id"])) {

                                    self::update_verify($ID, $new_try_num, $sent_num, 0, 0);

                                    $result["message"] = 'کد تایید وارد شده صحیح نمی‌باشد.';
                                }

                            } else {
                                $melipayamak->send(array($mobile), $message);
                                $sent_num = $sent_num + 1;
                                self::insert_verify($form_id, 0, $mobile, $code, 0, $try_num, $sent_num);
                            }

                        } else {

                            if (!empty($ID) && $ID != 0) {
                                self::update_verify($ID, $new_try_num, $sent_num, 0, 0);
                            }
                            $show_input = false;
                            $result["message"] = 'محدودیت ارسال کد تایید فرارسیده است.';
                        }

                    }
                }
            }

            if ($result["is_valid"] != true) {

                add_filter('gform_validation_message', array('GFVerificationMelipayamak', 'change_message'), 10, 2);
                add_filter('sms_verify_display_none', array('GFVerificationMelipayamak', 'apply_false'), 99);


                if ($show_input == true) {
                    add_filter('sms_verify_field', array('GFVerificationMelipayamak', 'apply_true'), 99);
                }

                if (isset($result["action"]) && $result["action"] == 'self') {
                    $result["message_"] = $result["message"];
                } else {
                    add_filter('sms_verify_self_validation', array('GFVerificationMelipayamak', 'apply_false'), 99);
                }
            } else {
                add_filter('gform_button_verify', array('GFVerificationMelipayamak', 'apply_false'), 99);
            }

        }

        return $result;
    }


    public static function process($entry, $form)
    {

        $sms_verification = GFCommon::get_fields_by_type($form, array('sms_verification'));

        foreach ((array)$sms_verification as $field) {

            global $wpdb, $table_prefix;
            $verify_table = self::verify_table();

            $field = (array)$field;

            $mobile = self::get_mobile($field);

            $get_result = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$verify_table} WHERE mobile = %s AND form_id = %s AND lead_id = %s ORDER BY id DESC LIMIT 1", $mobile, $form['id'], 0));

            if (!empty($get_result) && is_object($get_result)) {

                $ID = !empty($get_result->id) ? $get_result->id : '';
                $status = !empty($get_result->status) ? $get_result->status : 0;

                if (!empty($ID) && $ID != 0 && !empty($status) && $status != 0) {

                    $verify_code = $entry[$field['id']] = $get_result->code;

                    $try_num = $get_result->try_num;
                    $sent_num = $get_result->sent_num;
                    self::update_verify($ID, $try_num, $sent_num, $entry['id'], 1);

                    GFAPI::update_entry_field($entry['id'], $field['id'], $verify_code);

                }
            }
        }

        return $entry;
    }

    public static function content($content, $field, $value, $lead_id, $form_id)
    {
        /*
        if ( $field["type"] == "sms_verification" ) {
            return $content;
        }
        */
        return $content;
    }

    public static function js()
    {
        ?>
        <script type='text/javascript'>
            fieldSettings["sms_verification"] = ".label_setting, .placeholder_setting,.label_placement_setting, .prepopulate_field_setting, .conditional_logic_field_setting, .admin_label_setting, .size_setting, .default_value_setting, .css_class_setting, .sms_verification_setting";

            function GFVerificationMelipayamak_verify_populate_select() {
                var options = ["<option value=''></option>"];
                jQuery.each(window.form.fields, function (i, field) {
                    if (field.inputs) {
                        jQuery.each(field.inputs, function (i, input) {
                            options.push("<option value='", input.id, "'>", field.label, " (", input.label, ") (ID: ", input.id, ")</option>");
                        });
                    } else {
                        options.push("<option value='", field.id, "'>", field.label, " (ID: ", field.id, ")</option>");
                    }
                });
                jQuery("select[id^=field_sms_verify_]").html(options.join(""));
            }

            jQuery(document).bind("gform_field_deleted", GFVerificationMelipayamak_verify_populate_select);
            jQuery(document).bind("gform_field_added", GFVerificationMelipayamak_verify_populate_select);
            GFVerificationMelipayamak_verify_populate_select();
            jQuery(document).bind("gform_load_field_settings", function (event, field, form) {
                if (field.sms_verify_code_type_radio == 'manual') {
                    jQuery("#sms_verify_code_type_radio_manual").prop("checked", true);
                    jQuery("#sms_verify_code_type_rand_div").hide("slow");
                    jQuery("#sms_verify_code_type_manual_div").show("slow");
                }
                else {
                    jQuery("#sms_verify_code_type_radio_rand").prop("checked", true);
                    jQuery("#sms_verify_code_type_rand_div").show("slow");
                    jQuery("#sms_verify_code_type_manual_div").hide("slow");
                }
                // show hide div when radio button changed
                jQuery('input[name="sms_verify_code_type_radio"]').on("click", function () {
                    if (jQuery('input[name="sms_verify_code_type_radio"]:checked').val() == 'manual') {
                        jQuery("#sms_verify_code_type_rand_div").hide("slow");
                        jQuery("#sms_verify_code_type_manual_div").show("slow");
                    }
                    else {
                        jQuery("#sms_verify_code_type_rand_div").show("slow");
                        jQuery("#sms_verify_code_type_manual_div").hide("slow");
                    }
                });

                jQuery("#sms_verify_code_type_rand").val(field["sms_verify_code_type_rand"]);
                jQuery("#sms_verify_code_type_manual").val(field["sms_verify_code_type_manual"]);
                jQuery("#sms_verify_try_num").val(field["sms_verify_try_num"]);
                jQuery("#sms_verify_sent_num").val(field["sms_verify_sent_num"]);
                jQuery("#sms_verify_code_msg_body").val(field["sms_verify_code_msg_body"]);
                jQuery("#sms_verify_code_white_list").val(field["sms_verify_code_white_list"]);
                jQuery("#sms_verify_code_all_fields").attr("checked", field["sms_verify_code_all_fields"] == true);

                var fields = [ <?php foreach (self::get_this_fields() as $key) {
                    echo "'{$key}',";
                } ?> ];
                fields.map(function (fname) {
                    jQuery("#field_sms_verify_" + fname).attr("value", field["field_sms_verify_" + fname]);
                });
            });
        </script>
        <?php
    }


    public static function tooltips($tooltips)
    {

        $tooltips['form_gravity_sms_fields'] = '<h6>ملی‌پیامک</h6>فیلد‌های ملی‌یپامک';
        $tooltips['sms_verify_code_type_select'] = 'می‌توانید روش تولید کد‌های تایید را انتخاب کنید. در روش دستی ممکن است هر کد به چند نفر ارسال گردد.';
        $tooltips["sms_verify_mobile"] = "<h6>فیلد موبایل</h6>فیلد شماره موبایل را جهت تایید انتخاب کنید.";
        $tooltips["sms_verify_code_msg_body"] = 'متن پیامک را وارد کنید.';
        $tooltips["sms_verify_try_num"] = 'هر شماره تا چند مرتبه قادر به وارد کردن کد تایید اشتباه باشد؟';
        $tooltips["sms_verify_sent_num"] = 'هر شماره تا چند مرتبه قادر به دریافت کد تایید باشد؟';
        $tooltips["sms_verify_all_fields"] = 'با انتخاب کردن این گزینه، این فیلد از تگ all_fields حذف خواهد شد.';
        $tooltips["sms_verify_code_white_list"] = "<h6>لیست سفید</h6>شماره‌هایی که نیاز به تایید ندارند را می‌توانید اینجا وارد کنید. ";

        return $tooltips;
    }


    public static function standard_settings($position, $form_id)
    {

        if ($position == 50) { ?>

            <li class="sms_verification_setting field_setting">

                <div class="field_sms_verify_mobile">
                    <br/>
                    <label for="field_sms_verify_mobile">
                        فیلد شماره موبایل
                        <?php gform_tooltip('sms_verify_mobile') ?>
                    </label>
                    <select id="field_sms_verify_mobile"
                            onchange="SetFieldProperty('field_sms_verify_mobile', this.value);"></select>
                </div>


                <div class="sms_verify_type_div">
                    <br/>
                    <label>
                        نحوه وارد کردن کد تایید
                        <?php gform_tooltip("sms_verify_code_type_select"); ?>
                    </label>
                    <div>
                        <input type="radio" name="sms_verify_code_type_radio" id="sms_verify_code_type_radio_rand"
                               size="10" value="rand"
                               onclick="SetFieldProperty('sms_verify_code_type_radio', this.value);"/>
                        <label for="sms_verify_code_type_radio_rand" class="inline">
                            تصادفی
                        </label>

                        <input type="radio" name="sms_verify_code_type_radio" id="sms_verify_code_type_radio_manual"
                               size="10" value="manual"
                               onclick="SetFieldProperty('sms_verify_code_type_radio', this.value);"/>
                        <label for="sms_verify_code_type_radio_manual" class="inline">
                            دستی
                        </label>
                    </div>

                    <div id="sms_verify_code_type_rand_div">
                        <input id="sms_verify_code_type_rand" name="sms_verify_code_type_rand" type="text" size="35"
                               style="direction:ltr !important;text-align:left;"
                               onkeyup="SetFieldProperty('sms_verify_code_type_rand', this.value);">
                        <p class="mask_text_description_" style="margin: 5px 0px 0px;">
                            <?php _e('Enter a custom mask', 'gravityforms') ?>.
                            <a onclick="tb_show('<?php echo __('Custom Mask Instructions', 'gravityforms') ?>', '#TB_inline?width=350&inlineId=custom_mask_instructions', '');"
                               href="javascript:void(0);"><?php _e('Help', 'gravityforms') ?></a>
                        </p>
                    </div>

                    <div id="sms_verify_code_type_manual_div">
                        <textarea id="sms_verify_code_type_manual"
                                  style="text-align:left !important; direction:ltr !important;"
                                  class="fieldwidth-1 fieldheight-1"
                                  onkeyup="SetFieldProperty('sms_verify_code_type_manual', this.value);"></textarea>
                        <span
                                class="description">کدها را با , از هم جدا کنید.</span>
                    </div>
                </div>

                <div id="sms_verify_code_msg_body_div">
                    <br/>
                    <label for="sms_verify_code_msg_body">
                        متن پیامک
                        <?php gform_tooltip("sms_verify_code_msg_body"); ?>
                    </label>
                    <textarea id="sms_verify_code_msg_body" class="fieldwidth-1"
                              onkeyup="SetFieldProperty('sms_verify_code_msg_body', this.value);"></textarea>
                    <span class="description">کد تایید = <code>%code%</code></span>
                </div>

                <div class="sms_verify_try_num_div">
                    <br/>
                    <label for="sms_verify_try_num">
                        حداکثر دفعات تلاش مجدد
                        <?php gform_tooltip("sms_verify_try_num"); ?>
                    </label>
                    <input type="text" size="35" id="sms_verify_try_num"
                           onkeyup="SetFieldProperty('sms_verify_try_num', this.value);"/>
                </div>

                <div class="sms_verify_sent_num_div">
                    <br/>
                    <label for="sms_verify_sent_num">
                        حداکثر دفعات ارسال مجدد
                        <?php gform_tooltip("sms_verify_sent_num"); ?>
                    </label>
                    <input type="text" size="35" id="sms_verify_sent_num"
                           onkeyup="SetFieldProperty('sms_verify_sent_num', this.value);"/>
                </div>

                <div class="sms_verify_code_all_fields_div">
                    <br/>
                    <input type="checkbox" id="sms_verify_code_all_fields"
                           onclick="SetFieldProperty('sms_verify_code_all_fields', this.checked);"/>
                    <label for="sms_verify_code_all_fields" class="inline">
                        پنهان کردن از تگ all_fields
                        <?php gform_tooltip("sms_verify_all_fields"); ?>
                    </label>
                </div>

                <div id="sms_verify_code_white_list_div">
                    <br/>
                    <label for="sms_verify_code_white_list">
                        لیست سفید
                        <?php gform_tooltip("sms_verify_code_white_list"); ?>
                    </label>
                    <textarea id="sms_verify_code_white_list" style="text-align:left;direction:ltr !important;"
                              class="fieldwidth-1"
                              onkeyup="SetFieldProperty('sms_verify_code_white_list', this.value);"></textarea>
                    <span class="description">شماره‌ها را با , از هم جدا کنید.</span>
                </div>

            </li>
            <?php
        }
    }

    public static function get_this_fields()
    {
        return array('mobile');
    }

    public static function rand_str($type = 2)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = $type == 1 ? '0123456789' : '';
        $rand = str_split(str_shuffle($alphabet . $numbers));
        return $rand[rand(0, 51)];
    }

    public static function rand_mask($mask)
    {
        if (empty($mask))
            return rand(10000, 99999);
        $all_str = array();
        $all_str = str_split($mask);
        $code = '';
        foreach ((array)$all_str as $str) {
            if ($str == '*')
                $code .= str_replace($str, self::rand_str(1), $str);
            else if ($str == '9' || $str == 9)
                $code .= str_replace($str, rand(0, 9), $str);
            else if ($str == 'a')
                $code .= str_replace($str, self::rand_str(2), $str);
            else
                $code .= $str;
        }
        return $code;
    }

    public static function get_mobile($field, $change = true)
    {
        $field = (array)$field;
        $mobile = rgget("field_sms_verify_mobile", $field);
        $mobile = str_replace('.', '_', $mobile);
        $mobile = "input_{$mobile}";
        $mobile = !rgempty($mobile) ? sanitize_text_field(rgpost($mobile)) : '';
        return $mobile;
    }

    public static function white_list($field)
    {
        $field = (array)$field;
        $numbers = rgget("sms_verify_code_white_list", $field);
        return !empty($numbers) ? explode(',', $numbers) : array();
    }

    public static function apply_false()
    {
        return false;
    }

    public static function apply_true()
    {
        return true;
    }

    public static function submit_button($button, $form)
    {
        unset($form['button']['text']);
        $text = apply_filters('sms_verification_button', 'تایید شماره موبایل', $button, $form);
        if (is_callable(array('GFFormDisplay', 'get_form_button')))
            return GFFormDisplay::get_form_button($form['id'], "gform_submit_button_{$form['id']}", $form['button'], $text, 'gform_button', $text, 0);
        else
            return self::get_form_button($form['id'], "gform_submit_button_{$form['id']}", $form['button'], $text, 'gform_button', $text, 0);
    }

    public static function next_button($button, $form)
    {
        unset($form['button']['text']);
        $text = apply_filters('sms_verification_button', 'تایید شماره موبایل', $button, $form);
        $field = GFCommon::get_fields_by_type($form, array('page'));
        if (is_callable(array('GFFormDisplay', 'get_form_button')))
            return GFFormDisplay::get_form_button($form['id'], "gform_next_button_{$form['id']}_{$field->id}", $field->nextButton, $text, 'gform_next_button', $text, $field->pageNumber);
        else
            return self::get_form_button($form['id'], "gform_next_button_{$form['id']}_{$field->id}", $field->nextButton, $text, 'gform_next_button', $text, $field->pageNumber);
    }

    public static function change_message($message, $form)
    {
        return "<div class='validation_error'>لطفا جهت ادامه دادن، ابتدا شماره موبایل خود را تایید کنید.</div>";
    }

    public static function all_fields($value, $merge_tag, $modifier, $field)
    {
        if ($merge_tag == 'all_fields' && $field->type == 'sms_verification') {
            if (rgget("sms_verify_code_all_fields", $field))
                return false;
        }
        return $value;
    }

    public static function get_form_button($form_id, $button_input_id, $button, $default_text, $class, $alt, $target_page_number, $onclick = '')
    {

        $tabindex = GFCommon::get_tabindex();

        $input_type = 'submit';

        if (!empty($target_page_number)) {
            $onclick = "onclick='jQuery(\"#gform_target_page_number_{$form_id}\").val(\"{$target_page_number}\"); {$onclick} jQuery(\"#gform_{$form_id}\").trigger(\"submit\",[true]); '";
            $input_type = 'button';
        } else {
            // prevent multiple form submissions when button is pressed multiple times
            if (GFFormsModel::is_html5_enabled()) {
                $set_submitting = "if( !jQuery(\"#gform_{$form_id}\")[0].checkValidity || jQuery(\"#gform_{$form_id}\")[0].checkValidity()){window[\"gf_submitting_{$form_id}\"]=true;}";
            } else {
                $set_submitting = "window[\"gf_submitting_{$form_id}\"]=true;";
            }

            $onclick_submit = $button['type'] == 'link' ? "jQuery(\"#gform_{$form_id}\").trigger(\"submit\",[true]);" : '';

            $onclick = "onclick='if(window[\"gf_submitting_{$form_id}\"]){return false;}  {$set_submitting} {$onclick} {$onclick_submit}'";
        }

        if (rgar($button, 'type') == 'text' || rgar($button, 'type') == 'link' || empty($button['imageUrl'])) {
            $button_text = !empty($button['text']) ? $button['text'] : $default_text;
            if (rgar($button, 'type') == 'link') {
                $button_input = "<a href='javascript:void(0);' id='{$button_input_id}_link' class='{$class}' {$tabindex} {$onclick}>{$button_text}</a>";
            } else {
                $class .= ' button';
                $button_input = "<input type='{$input_type}' id='{$button_input_id}' class='{$class}' value='" . esc_attr($button_text) . "' {$tabindex} {$onclick} />";
            }
        } else {
            $imageUrl = $button['imageUrl'];
            $class .= ' gform_image_button';
            $button_input = "<input type='image' src='{$imageUrl}' id='{$button_input_id}' class='{$class}' alt='{$alt}' {$tabindex} {$onclick} />";
        }

        return $button_input;
    }

}

GFVerificationMelipayamak::construct();