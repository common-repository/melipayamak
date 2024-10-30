<?php
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

class Two_Factor_Melipayamak extends Two_Factor_Provider
{
    /**
     * The user meta token key.
     *
     * @type string
     */
    const TOKEN_META_KEY = '_two_factor_melipayamak_token';

    /**
     * Ensures only one instance of this class exists in memory at any one time.
     *
     * @since 0.1-dev
     */
    static function get_instance()
    {
        static $instance;
        $class = __CLASS__;
        if (!is_a($instance, $class)) {
            $instance = new $class;
        }
        return $instance;
    }

    /**
     * Class constructor.
     *
     * @since 0.1-dev
     */
    protected function __construct()
    {
        add_action('two-factor-user-options-' . __CLASS__, array($this, 'user_options'));
        return parent::__construct();
    }

    /**
     * Returns the name of the provider.
     *
     * @since 0.1-dev
     */
    public function get_label()
    {
        return 'ملی‌پیامک';
    }

    /**
     * Generate the user token.
     *
     * @since 0.1-dev
     *
     * @param int $user_id User ID.
     * @return string
     */
    public function generate_token($user_id)
    {
        $token = rand(10000, 99999);
        update_user_meta($user_id, self::TOKEN_META_KEY, wp_hash($token));
        return $token;
    }

    /**
     * Check if user has a valid token already.
     *
     * @param  int $user_id User ID.
     * @return boolean      If user has a valid SMS token.
     */
    public function user_has_token($user_id)
    {
        $hashed_token = get_user_meta($user_id, self::TOKEN_META_KEY, true);
        if (!empty($hashed_token)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validate the user token.
     *
     * @since 0.1-dev
     *
     * @param int $user_id User ID.
     * @param string $token User token.
     * @return boolean
     */
    public function validate_token($user_id, $token)
    {
        $hashed_token = get_user_meta($user_id, self::TOKEN_META_KEY, true);
        // Bail if token is empty or it doesn't match.
        if (empty($hashed_token) || (wp_hash($token) !== $hashed_token)) {
            return false;
        }
        // Ensure that the token can't be re-used.
        $this->delete_token($user_id);
        return true;
    }

    /**
     * Delete the user token.
     *
     * @since 0.1-dev
     *
     * @param int $user_id User ID.
     */
    public function delete_token($user_id)
    {
        delete_user_meta($user_id, self::TOKEN_META_KEY);
    }

    /**
     * Generate and SMS the user token.
     *
     * @since 0.1-dev
     *
     * @param WP_User $user WP_User object of the logged-in user.
     */
    public function generate_and_sms_token($user)
    {
        global $melipayamak;

        $token = $this->generate_token($user->ID);

        $message = get_option('melipayamak_2fa_text');
        $message = str_replace(array(
            '{token}',
            '{date}'
        ), array(
            $token,
            $melipayamak->date()
        ), $message);
        $melipayamak->send(get_user_meta($user->ID, 'mpmobile'), $message);

    }

    /**
     * Prints the form that prompts the user to authenticate.
     *
     * @since 0.1-dev
     *
     * @param WP_User $user WP_User object of the logged-in user.
     */
    public function authentication_page($user)
    {
        if (!$user) {
            return;
        }
        // if (!$this->user_has_token($user->ID)) {
        $this->generate_and_sms_token($user);
        // }
        require_once(ABSPATH . '/wp-admin/includes/template.php');
        ?>
        <p>کد تایید به موبایل شما ارسال شد.</p>
        <p>
            <label for="authcode">کد تایید:</label>
            <input type="tel" name="two-factor-melipayamak-code" id="authcode" class="input" value="" size="20"
                   pattern="[0-9]*"/>
        </p>
        <script type="text/javascript">
            setTimeout(function () {
                var d;
                try {
                    d = document.getElementById('authcode');
                    d.value = '';
                    d.focus();
                } catch (e) {
                }
            }, 200);
        </script>
        <?php
        submit_button(__('Log In', 'two-factor'));
    }

    /**
     * Validates the users input token.
     *
     * @since 0.1-dev
     *
     * @param WP_User $user WP_User object of the logged-in user.
     * @return boolean
     */
    public function validate_authentication($user)
    {
        if (!isset($user->ID) || !isset($_REQUEST['two-factor-melipayamak-code'])) {
            return false;
        }
        return $this->validate_token($user->ID, $_REQUEST['two-factor-melipayamak-code']);
    }

    /**
     * Whether this Two Factor provider is configured and available for the user specified.
     *
     * @since 0.1-dev
     *
     * @param WP_User $user WP_User object of the logged-in user.
     * @return boolean
     */
    public function is_available_for_user($user)
    {
        return !!get_user_meta($user->ID, 'mpmobile')[0];
    }

    /**
     * Inserts markup at the end of the user profile field for this provider.
     *
     * @since 0.1-dev
     *
     * @param WP_User $user WP_User object of the logged-in user.
     */
    public function user_options($user)
    {
        $mobile = get_user_meta($user->ID, 'mpmobile')[0];
        if (!$mobile)
            $mobile = 'موبایل شما';
        ?>
        <div>
            <?php
            echo "کد تایید به {$mobile} ارسال خواهد شد.";
            ?>
        </div>
        <?php
    }
}
