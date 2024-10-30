<?php

//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

GFForms::include_feed_addon_framework();


class GFMelipayamak extends GFFeedAddOn
{

    /**
     * Contains an instance of this class, if available.
     *
     * @since  Unknown
     * @access private
     * @var    object $_instance If available, contains an instance of this class.
     */
    private static $_instance = null;

    /**
     * Defines the version of the  Add-On.
     *
     * @since  Unknown
     * @access protected
     * @var    string $_version Contains the version, defined from melipayamak.php
     */
    protected $_version = MELIPAYAMAK_VERSION;

    /**
     * Defines the minimum Gravity Forms version required.
     *
     * @since  Unknown
     * @access protected
     * @var    string $_min_gravityforms_version The minimum version required.
     */
    protected $_min_gravityforms_version = '1.9.11';

    /**
     * Defines the plugin slug.
     *
     * @since  Unknown
     * @access protected
     * @var    string $_slug The slug used for this plugin.
     */
    protected $_slug = 'gravityformsmelipayamak';


    /**
     * Defines the full path to this class file.
     *
     * @since  Unknown
     * @access protected
     * @var    string $_full_path The full path.
     */
    protected $_full_path = __FILE__;

    /**
     * Defines the URL where this Add-On can be found.
     *
     * @since  Unknown
     * @access protected
     * @var    string The URL of the Add-On.
     */
    protected $_url = 'http://melipayamak.com';

    /**
     * Defines the title of this Add-On.
     *
     * @since  Unknown
     * @access protected
     * @var    string $_title The title of the Add-On.
     */
    protected $_title = 'ملی‌پیامک';

    /**
     * Defines the short title of the Add-On.
     *
     * @since  Unknown
     * @access protected
     * @var    string $_short_title The short title.
     */
    protected $_short_title = 'ملی‌پیامک';

    /**
     * Defines if Add-On should use Gravity Forms servers for update data.
     *
     * @since  Unknown
     * @access protected
     * @var    bool
     */
    protected $_enable_rg_autoupgrade = false;


    /**
     * Get instance of this class.
     *
     * @access public
     * @static
     *
     */
    public static function get_instance()
    {

        if (null === self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;

    }

    /**
     * Plugin starting point. Handles hooks, loading of language files and PayPal delayed payment support.
     *
     * @since  Unknown
     * @access public
     */
    public function init()
    {
        parent::init();
    }




    // # FEED SETTINGS -------------------------------------------------------------------------------------------------

    /**
     * Setup fields for feed settings.
     *
     * @since  Unknown
     * @access public
     *
     *
     * @return array
     */
    public function feed_settings_fields()
    {

        return array(
            array(
                'title' => 'تنظیمات پیامک',
                'description' => '',
                'fields' => array(
                    array(
                        'name' => 'feedName',
                        'label' => 'نام',
                        'type' => 'text',
                        'required' => true,
                        'class' => 'medium',
                        'tooltip' => sprintf(
                            '<h6>%s</h6>%s',
                            'نام',
                            'یک نام برای این پیامک انتخاب کنید'
                        ),
                    ),
                    array(
                        'name' => 'toNumber',
                        'label' => 'گیرنده',
                        'type' => 'select_custom',
                        'choices' => $this->get_phone_numbers_as_choices(),
                        'required' => true,
                        'input_class' => 'merge-tag-support mt-position-right',
                        'tooltip' => sprintf(
                            '<h6>%s</h6>%s',
                            'شماره گیرنده',
                            'گیرنده پیامک را انتخاب کنید.'
                        ),
                    ),
                    array(
                        'name' => 'smsMessage',
                        'label' => 'متن پیامک',
                        'type' => 'textarea',
                        'class' => 'medium merge-tag-support mt-position-right',
                        'tooltip' => sprintf(
                            '<h6>%s</h6>%s',
                            'متن پیامک',
                            'متن پیامک را وارد کنید.'
                        ),
                    ),
                    array(
                        'name' => 'feed_condition',
                        'label' => 'منطق شرطی',
                        'type' => 'feed_condition',
                        'checkbox_label' => 'فعال',
                        'instructions' => 'ارسال به ملی‌پیامک اگر',
                        'tooltip' => sprintf(
                            '<h6>%s</h6>%s',
                            'منطق شرطی',
                            'شرط‌های ارسال پیامک'
                        ),
                    ),
                ),
            ),
        );

    }

    /**
     * Retrieve the from/to numbers for use on the feed settings page.
     *
     * @since  2.4
     * @access public
     *
     * @param string $type The phone number type. Either incoming_numbers or outgoing_numbers.
     *
     * @uses GFAddOn::get_current_form()
     * @uses GFAddOn::log_debug()
     * @uses GFAddOn::log_error()
     * @uses GFAPI::get_fields_by_type()
     *
     * @return array
     */
    public function get_phone_numbers_as_choices()
    {


        // Initialize phone numbers array.
        $phone_numbers = array(
            array(
                'label' => 'فیلد‌های شماره موبایل',
                'choices' => array(),
            ),
            array(
                'label' => 'شماره‌های از قبل وارد شده',
                'choices' => array(array(
                    'label' => 'مدیرسایت',
                    'value' => 'admin'
                )),
            ),
            array(
                'label' => 'شماره دلخواه',
                'value' => 'gf_custom',
            ),
        );

        // Get current form.
        $form = $this->get_current_form();

        // Get Phone fields.
        $phone_fields = GFAPI::get_fields_by_type($form, array('phone'));

        // Add Phone fields to choices.
        if (!empty($phone_fields)) {

            // Loop through Phone fields.
            foreach ($phone_fields as $phone_field) {

                // Add Phone field as choice.
                $phone_numbers[0]['choices'][] = array(
                    'label' => esc_html($phone_field->label),
                    'value' => 'field_' . esc_attr($phone_field->id),
                );

            }
        }

        return $phone_numbers;

    }


    /**
     * Renders and initializes a drop down field with a input field for custom input based on the $field array.
     * (Forked to add support for merge tags in input field.)
     *
     * @since  2.4
     * @access public
     *
     * @param array $field Field array containing the configuration options of this field
     * @param bool $echo True to echo the output to the screen, false to simply return the contents as a string
     *
     * @return string The HTML for the field
     */
    public function settings_select_custom($field, $echo = true)
    {

        // Prepare select field.
        $select_field = $field;
        $select_field_value = $this->get_setting($select_field['name'], rgar($select_field, 'default_value'));
        $select_field['onchange'] = '';
        $select_field['class'] = (isset($select_field['class'])) ? $select_field['class'] . 'gaddon-setting-select-custom' : 'gaddon-setting-select-custom';

        // Prepare input field.
        $input_field = $field;
        $input_field['name'] .= '_custom';
        $input_field['style'] = 'width:200px;max-width:90%;';
        $input_field['class'] = rgar($field, 'input_class');
        $input_field_display = '';

        // Loop through select choices and make sure option for custom exists.
        $has_gf_custom = false;
        foreach ($select_field['choices'] as $choice) {

            if (rgar($choice, 'name') == 'gf_custom' || rgar($choice, 'value') == 'gf_custom') {
                $has_gf_custom = true;
            }

            // If choice has choices, check inside those choices..
            if (rgar($choice, 'choices')) {
                foreach ($choice['choices'] as $subchoice) {
                    if (rgar($subchoice, 'name') == 'gf_custom' || rgar($subchoice, 'value') == 'gf_custom') {
                        $has_gf_custom = true;
                    }
                }
            }

        }
        if (!$has_gf_custom) {
            $select_field['choices'][] = array(
                'label' => esc_html__('Add Custom', 'gravityforms') . ' ' . $select_field['label'],
                'value' => 'gf_custom'
            );
        }

        // If select value is "gf_custom", hide the select field and display the input field.
        if ($select_field_value == 'gf_custom' || (count($select_field['choices']) == 1 && $select_field['choices'][0]['value'] == 'gf_custom')) {
            $select_field['style'] = 'display:none;';
        } else {
            $input_field_display = ' style="display:none;"';
        }

        // Add select field.
        $html = $this->settings_select($select_field, false);

        // Add input field.
        $html .= '<div class="gaddon-setting-select-custom-container"' . $input_field_display . '>';
        $html .= count($select_field['choices']) > 1 ? '<a href="#" class="select-custom-reset">Reset</a>' : '';
        $html .= $this->settings_text($input_field, false);
        $html .= '</div>';

        if ($echo) {
            echo $html;
        }

        return $html;

    }




    // # FEED LIST -----------------------------------------------------------------------------------------------------

    /**
     * Setup columns for feed list table.
     *
     * @since  Unknown
     * @access public
     *
     * @return array
     */
    public function feed_list_columns()
    {

        return array(
            'feedName' => 'نام',
            'toNumber' => 'گیرنده',
        );

    }


    /**
     * Returns the value to be displayed in the From column.
     *
     * @since  Unknown
     * @access public
     *
     * @param array $feed Feed object.
     *
     * @uses GFAddOn::get_current_form()
     * @uses GFFormsModel::get_field()
     *
     * @return string
     */
    public function get_column_value_toNumber($feed)
    {

        // If a custom value is set, return it.
        if ('gf_custom' === rgars($feed, 'meta/toNumber')) {
            return rgars($feed, 'meta/toNumber_custom');
        }

        // Get To Number value.
        $to_number = rgars($feed, 'meta/toNumber');

        if ($to_number == 'admin') {
            return 'مدیر‌سایت';
        }

        // If a field is not selected, return number.
        if ('field_' !== substr($to_number, 0, 6)) {
            return $to_number;
        }

        // Get field ID.
        $phone_field = str_replace('field_', '', $to_number);

        // Get current form.
        $form = $this->get_current_form();

        // Get field.
        $phone_field = GFFormsModel::get_field($form, $phone_field);

        return esc_html($phone_field->label);

    }





    // # FEED PROCESSING -----------------------------------------------------------------------------------------------

    /**
     * Initiate processing the feed.
     *
     * @since  2.0
     * @access public
     *
     * @param array $feed The Feed object to be processed.
     * @param array $entry The Entry object currently being processed.
     * @param array $form The Form object currently being processed.
     *
     * @uses GFAddOn::get_plugin_settings()
     * @uses GFAddOn::log_debug()
     * @uses GFCommon::replace_variables()
     * @uses GFFeedAddOn::add_feed_error()
     */
    public function process_feed($feed, $entry, $form)
    {

        global $melipayamak;

        // Prepare message arguments.
        $args = array(
            'to' => $this->get_message_to($feed, $entry, $form),
            'body' => rgars($feed, 'meta/smsMessage'),
        );

        $args['to'] = apply_filters('gform_melipayamak_set_to_phone_number', $args['to'], $entry, $feed['id']);


        $args = gf_apply_filters(array('gform_melipayamak_message', $form['id'], $feed['id']), $args, $feed, $entry, $form);


        $args['body'] = GFCommon::replace_variables($args['body'], $form, $entry, false, true, false, 'text');


        $melipayamak->send($args['to'], $args['body']);
    }


    /**
     * Return the To Number.
     *
     * @since  2.4
     * @access public
     *
     * @param array $feed The Feed object.
     * @param array $entry The Entry object.
     * @param array $form The Form object.
     *
     * @uses GFCommon::replace_variables()
     *
     * @return string
     */
    public function get_message_to($feed, $entry, $form)
    {

        // If a custom value is set, return it.
        if ('gf_custom' === rgars($feed, 'meta/toNumber')) {
            return array(GFCommon::replace_variables($feed['meta']['toNumber_custom'], $form, $entry, false, true, false, 'text'));
        }

        // Get To Number value.
        $to_number = rgars($feed, 'meta/toNumber');

        if ($to_number == 'admin') {
            return explode(';', get_option('melipayamak_admin'));
        }

        // If a field is not selected, return number.
        if ('field_' !== substr($to_number, 0, 6)) {
            return array($to_number);
        }

        // Get field ID.
        $phone_field = str_replace('field_', '', $to_number);

        // Get field value.
        $to_number = rgar($entry, $phone_field);

        return array($to_number);

    }
}