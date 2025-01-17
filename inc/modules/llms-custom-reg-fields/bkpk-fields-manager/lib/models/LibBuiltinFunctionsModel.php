<?php
namespace BPKPFieldManager;

/**
 * This class contain several WP functions, those are already built in with wordpress core
 */
class LibBuiltinFunctionsModel
{

    /**
     * Handles sending password retrieval email to user.
     * Function function found on wp-login.php
     *
     * @return bool|WP_Error True: when finish. WP_Error on error
     */
    function retrieve_password($customLink = null)
    {
        global $wpdb, $wp_hasher, $bkpkFM;
        
        $errors = new \WP_Error();
        
        if (empty($_POST['user_login'])) {
            $errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.', $bkpkFM->name));
        } else {
            /**
             * We can not distinguish email only by '@', username could also contains '@'
             */
            $login = trim($_POST['user_login']);
            $user_data = get_user_by('login', $login);
            if (! $user_data)
                $user_data = get_user_by('email', $login);
        }
        
        if ($bkpkFM->isHookEnable('lostpassword_post')) {
            do_action('lostpassword_post', $errors);
        }
        
        if ($errors->get_error_code()) {
            return $errors;
        }
        
        if (! $user_data) {
            $errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.', $bkpkFM->name));
            return $errors;
        }
        
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;
        
        $key = get_password_reset_key($user_data);
        
        if (is_wp_error($key)) {
            return $key;
        }
        
        $resetLink = $customLink ? $customLink : network_site_url('wp-login.php', 'login');
        $resetLink = add_query_arg(array(
            'action' => 'rp',
            'key' => $key,
            'login' => rawurlencode($user_login)
        ), $resetLink);
        
        $bkpkFM->prepareEmail('lostpassword', $user_data, array(
            'reset_password_link' => $resetLink,
            'key' => $key
        ));
        
        return true;
    /**
     * Codes for wp default email is not required.
     */
    }

    /**
     * Handles resetting the user's password.
     * Function found on wp-includes/user.php
     * Diff: commenting wp_password_change_notification before WP-4.4
     *
     * @param object $user
     *            The user
     * @param string $new_pass
     *            New password for the user in plaintext
     */
    function reset_password($user, $new_pass)
    {
        global $bkpkFM;
        
        if (version_compare(get_bloginfo('version'), '4.4.0', '>='))
            return reset_password($user, $new_pass);
        
        if ($bkpkFM->isHookEnable('password_reset'))
            do_action('password_reset', $user, $new_pass);
        
        wp_set_password($new_pass, $user->ID);
        update_user_option($user->ID, 'default_password_nag', false, true);
        // wp_password_change_notification( $user ); // commented wp default email
    }

    /**
     * Retrieve or display nonce hidden field for forms.
     *
     * Function found in wp-includes/functions.php
     * Diff: remove id attribute from hidden input.
     *
     * @return string Nonce field.
     */
    function wp_nonce_field($action = -1, $name = "_wpnonce", $referer = true, $echo = true)
    {
        $name = esc_attr($name);
        $nonce_field = '<input type="hidden" name="' . $name . '" value="' . wp_create_nonce($action) . '" />';
        
        if ($referer)
            $nonce_field .= wp_referer_field(false);
        
        if ($echo)
            echo $nonce_field;
        
        return $nonce_field;
    }

    /**
     * Commented since 1.1.8rc1
     */
    function retrieve_password_Old($resetPassCustomLink = null)
    {
        global $wpdb, $wp_hasher, $pfInstance;
        
        $errors = new \WP_Error();
        
        if (empty($_POST['user_login'])) {
            $errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.', $pfInstance->name));
        } else {
            $login = trim($_POST['user_login']);
            $user_data = get_user_by('login', $login);
            if (! $user_data)
                $user_data = get_user_by('email', $login);
        }
        
        if ($pfInstance->isHookEnable('lostpassword_post')) {
            // do_action( 'lostpassword_post' );
            /**
             * Fires before errors are returned from a password reset request.
             *
             * @since WP-4.4 Added the `$errors` parameter.
             */
            do_action('lostpassword_post', $errors);
        }
        
        if ($errors->get_error_code())
            return $errors;
        
        if (! $user_data) {
            $errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.', $pfInstance->name));
            return $errors;
        }
        
        // redefining user_login ensures we return the right case in the email
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;
        
        if ($pfInstance->isHookEnable('retrieve_password'))
            do_action('retrieve_password', $user_login);
        
        if ($pfInstance->isHookEnable('allow_password_reset')) {
            $allow = apply_filters('allow_password_reset', true, $user_data->ID);
            
            if (! $allow)
                return new \WP_Error('no_password_reset', __('Password reset is not allowed for this user', $pfInstance->name));
            elseif (is_wp_error($allow))
                return $allow;
        }
        
        $key = wp_generate_password(20, false);
        
        if ($pfInstance->isHookEnable('retrieve_password_key'))
            do_action('retrieve_password_key', $user_login, $key);
            
            // Now insert the key, hashed, into the DB.
        if (empty($wp_hasher)) {
            require_once ABSPATH . 'wp-includes/class-phpass.php';
            $wp_hasher = new \PasswordHash(8, true);
        }
        $hashed = $wp_hasher->HashPassword($key);
        $wpdb->update($wpdb->users, array(
            'user_activation_key' => $hashed
        ), array(
            'user_login' => $user_login
        ));
        
        /**
         * *
         */
        
        $resetLink = $resetPassCustomLink ? $resetPassCustomLink : network_site_url('wp-login.php', 'login');
        $resetLink = add_query_arg(array(
            'action' => 'rp',
            'key' => $key,
            'login' => rawurlencode($user_login)
        ), $resetLink);
        
        /*
         * do_action( 'pf_lostpassword_email', $resetLink, $key, $user_data->ID );
         * if ( did_action( 'pf_lostpassword_email' ) )
         * return true;
         */
        
        $pfInstance->prepareEmail('lostpassword', $user_data, array(
            'reset_password_link' => $resetLink,
            'key' => $key
        ));
        
        return true;
        
        $message = __('Someone requested to reset the password for the following account:', $pfInstance->name) . "\r\n\r\n";
        $message .= network_site_url() . "\r\n\r\n";
        $message .= sprintf(__('Username: %s', $pfInstance->name), $user_login) . "\r\n\r\n";
        $message .= __('If you did not authorize this then please ignore this email and nothing will happen.', $pfInstance->name) . "\r\n\r\n";
        $message .= __('To reset your password, visit the following address:', $pfInstance->name) . "\r\n\r\n";
        $message .= '<' . $resetLink . ">\r\n";
        // $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";
        
        if (is_multisite())
            $blogname = $GLOBALS['current_site']->site_name;
        else
            // The blogname option is escaped with esc_html on the way into the database in sanitize_option
            // we want to reverse this for the plain text arena of emails.
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        
        $title = sprintf(__('[%s] Password Reset', $pfInstance->name), $blogname);
        
        if ($pfInstance->isHookEnable('retrieve_password_title'))
            $title = apply_filters('retrieve_password_title', $title);
        
        if ($pfInstance->isHookEnable('retrieve_password_message'))
            $message = apply_filters('retrieve_password_message', $message, $key);
        
        if ($message && ! wp_mail($user_email, $title, $message)) {
            $errors->add('email_not_sent', __('The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...', $pfInstance->name));
            return $errors;
        }
        
        // wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') );
        
        return true;
    }

    /**
     * Commented since 1.1.8rc1
     */
    function check_password_reset_key_Old($key, $login)
    {
        global $wpdb, $wp_hasher, $pfInstance;
        
        $key = preg_replace('/[^a-z0-9]/i', '', $key);
        
        if (empty($key) || ! is_string($key))
            return new \WP_Error('invalid_key', __('Invalid key', $pfInstance->name));
        
        if (empty($login) || ! is_string($login))
            return new \WP_Error('invalid_key', __('Invalid key', $pfInstance->name));
        
        $row = $wpdb->get_row($wpdb->prepare("SELECT ID, user_activation_key FROM $wpdb->users WHERE user_login = %s", $login));
        if (! $row)
            return new \WP_Error('invalid_key', __('Invalid key'));
        
        if (empty($wp_hasher)) {
            require_once ABSPATH . 'wp-includes/class-phpass.php';
            $wp_hasher = new \PasswordHash(8, true);
        }
        
        if ($wp_hasher->CheckPassword($key, $row->user_activation_key))
            return get_userdata($row->ID);
        
        if ($key === $row->user_activation_key) {
            $return = new \WP_Error('expired_key', __('Invalid key'));
            $user_id = $row->ID;
            
            /**
             * Filter the return value of check_password_reset_key() when an
             * old-style key is used (plain-text key was stored in the database).
             *
             * @since 3.7.0
             *       
             * @param WP_Error $return
             *            A WP_Error object denoting an expired key.
             *            Return a WP_User object to validate the key.
             * @param int $user_id
             *            The matched user ID.
             */
            if ($pfInstance->isHookEnable('password_reset_key_expired'))
                return apply_filters('password_reset_key_expired', $return, $user_id);
            else
                return $return;
        }
        
        return new \WP_Error('invalid_key', __('Invalid key'));
    }

    /**
     * Commented since 1.1.8rc1
     */
    function reset_password_Old($user, $new_pass)
    {
        global $pfInstance;
        
        if ($pfInstance->isHookEnable('password_reset'))
            do_action('password_reset', $user, $new_pass);
        
        wp_set_password($new_pass, $user->ID);
        
        // wp_password_change_notification( $user ); // commented wp default email
    }

    /**
     * Commented since 1.1.8rc1
     */
    function wp_nonce_field_Old($action = -1, $name = "_wpnonce", $referer = true, $echo = true)
    {
        $name = esc_attr($name);
        $nonce_field = '<input type="hidden" name="' . $name . '" value="' . wp_create_nonce($action) . '" />';
        
        if ($referer)
            $nonce_field .= wp_referer_field(false);
        
        if ($echo)
            echo $nonce_field;
        
        return $nonce_field;
    }
}