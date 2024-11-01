<?php

class MensagiaSMSNotification
{
    public function sendNotification($params, $hook, $type, WC_Order $order = null)
    {
        $customer_question     = null;
        $id_country_prestashop = null;

        switch ($hook['hook']) {

            default:
                $id_order         = $order->get_id();
                $address          = $order->get_address();
                $order            = $order;
                $currency         = $order->get_currency();
                $phone_mobile     = $order->get_billing_phone();
                $full_name        = $order->get_billing_first_name() . " " . $order->get_billing_last_name();
                $customer_id_lang = substr(get_bloginfo('language'), 0, 2);

                // $carrier    = $order->
                // $product    = ;
                // $employee   = null;
                // $customer   = new Customer($params['cart']->id_customer);
                // $id_country_prestashop = ;
                $carrier               = null;
                $product               = null;
                $employee              = null;
                $customer              = null;
                $id_country_prestashop = null;

                break;
        }

        $ps_data = $this->getDataActionOrderStatusPostUpdate(
            $id_order,
            $order,
            $customer,
            $address,
            $currency,
            $carrier,
            $product,
            $employee,
            $customer_question
        );

        $login       = (string) get_option('MENSAGIA_LOGIN_EMAIL');
        $password    = (string) get_option('MENSAGIA_LOGIN_PASSWORD');
        $connected   = (bool) get_option('MENSAGIA_AUTHENTICATED');
        $prefix_mode = (string) get_option('MENSAGIA_PREFIX_MODE');

        if ($connected) {
            $authentication = $this->mensagiaSDK->authenticate($login, $password);

            // miramos si se autentifica y puede enviar SMS
            if ($authentication['result'] != 'error') {
                if ($type == 'customer') {
                    if ($phone_mobile) {
                        // limpiamos el número de caracteres extraños
                        $phone_mobile = $this->mensagiaSDK->cleanNumber($phone_mobile);

                        if ($prefix_mode == 'check_prefixs') {
                            // Buscamos el pais de la compra
                            $country_iso_code_address = Mensagia::getIsoById($id_country_prestashop);

                            // Buscamos el país en Mensagia
                            $mensagia_country = MensagiaCountry::getMensagiaCountryByISO($country_iso_code_address);

                            // comprobamos prefijo del número
                            $phone_mobile = $this->mensagiaSDK->checkAndSetPrefix(
                                $phone_mobile,
                                $mensagia_country['phone']
                            );
                        }

                        // comprobamos que $phone_mobile no sea null despues de comprobaciones anteriores
                        if ($phone_mobile) {
                            // buscamos si hay un mensaje
                            $message = MensagiaSMSNotification::getMessagesNotification(
                                (int)$hook['id'],
                                $customer_id_lang
                            );

                            if (isset($message[0]['message'])) {
                                $sms = $message[0]['message'];

                                //$sms_transformed = $this->transformSMSToSend($sms, $ps_data);

                                $sendSMS = $this->mensagiaSDK->sendSMS(
                                    $phone_mobile,
                                    //$sms_transformed,
                                    $sms,
                                    (string)get_option('MENSAGIA_API_CONFIGURATION')
                                );

                                if ($sendSMS['result'] == 'error') {
                                    return $this->returnResponseErrors($sendSMS);
                                } else {
                                    return array(
                                        'result' => 'success'
                                    );
                                }
                            }
                        } else {
                            return array(
                                'result'    => 'error-whithout-number',
                                'full_name' => $full_name,
                            );
                        }
                    } else {
                        return array(
                            'result'    => 'error-whithout-number',
                            'full_name' => $full_name,
                        );
                    }
                }

                $phone_mobile = null;
                $message      = null;

                if ($type == 'admin') {
                    // buscamos los admins
                    $admins  = MensagiaAdmin::getAdmins();
                    $numbers = array();

                    if (count($admins)) {
                        foreach ($admins as $admin) {
                            array_push($numbers, $admin['number']);
                        }

                        $phone_mobile = implode(',', $numbers);
                    }

                    if ($phone_mobile) {
                        // limpiamos el número de caracteres extraños
                        $phone_mobile = $this->mensagiaSDK->cleanNumber($phone_mobile);

                        // buscamos si hay un mensaje
                        $message = MensagiaSMSNotification::getMessagesNotification((int)$hook['id'], 0);

                        if (isset($message[0]['message'])) {
                            $sms = $message[0]['message'];

                            $sms_transformed = $this->transformSMSToSend($sms, $ps_data);

                            $sendSMS = $this->mensagiaSDK->sendSMS(
                                $phone_mobile,
                                $sms_transformed,
                                (string)Configuration::get('MENSAGIA_API_CONFIGURATION')
                            );

                            if ($sendSMS['result'] == 'error') {
                                return $this->returnResponseErrors($sendSMS);
                            } else {
                                return array(
                                    'result' => 'success'
                                );
                            }
                        }
                    } else {
                        return array(
                            'result'    => 'error-whithout-number',
                            'full_name' => $full_name,
                        );
                    }
                }
            } else {
                return array(
                    'result'  => 'error-authentification-failed',
                    'message' => 'No se ha enviado ninguna notificación SMS porque no estás conectado con ' .
                        ' Mensagia. Vuelve a introducir tus datos de acceso.',
                );
            }
        } else {
            return array(
                'result'  => 'error-no-connected',
                'message' => 'No se ha enviado ninguna notificación SMS porque no estás conectado con ' .
                    'Mensagia. Vuelve a introducir tus datos de acceso.',
            );
        }
    }


    public static function getNotifications($type = null)
    {
        global $wpdb;

        if ($type !== null) {
            $where = " WHERE type = '".sanitize_text_field($type)."' ";
        } else {
            $where = "";
        }

        $results = $wpdb->get_results('SELECT *
            FROM '.$wpdb->prefix.'mensagia_sms_notifications'." ".$where." ORDER BY `id` ASC", ARRAY_A);

        return $results;
    }


    public static function getHooks($type, $hook, $option_name = null)
    {
        global $wpdb;

        if ($option_name !== null) {
            $where = " AND option_name = ".sanitize_text_field($option_name)." ";
        } else {
            $where = "";
        }

        $results = $wpdb->get_results("SELECT *
            FROM ".$wpdb->prefix."mensagia_sms_notifications WHERE hook = '".$hook."' AND type = '".$type."'  ".$where." 
            ORDER BY `id` ASC", ARRAY_A);

        return $results;
    }


    public static function getMessagesNotification($mensagia_sms_notification_id, $lang_code)
    {
        global $wpdb;

        $results = $wpdb->get_results("SELECT *
            FROM ".$wpdb->prefix."mensagia_sms_notifications_lang WHERE mensagia_sms_notification_id = '".$mensagia_sms_notification_id."' 
            ORDER BY `id` ASC", ARRAY_A);

        return $results;
    }


    public static function getAllMessagesNotifications()
    {
        global $wpdb;

        $results = $wpdb->get_results("SELECT *
            FROM ".$wpdb->prefix."mensagia_sms_notifications_lang  
            ORDER BY `id` ASC", ARRAY_A);

        return $results;
    }


    public static function transformNotificationsToCheck($notifications)
    {
        $new_notifications = array();

        if ($notifications) {
            foreach ($notifications as $notification) {
                if ($notification['hook'] == 'orderStatusChanged') {
                    $new_notifications[$notification['type'].'_orderStatusChanged_'.$notification['option_name']]
                        = (int) $notification['active'];
                } else {
                    $new_notifications[$notification['type'].'_'.$notification['hook']] = (int) $notification['active'];
                }
            }
        }

        return $new_notifications;
    }


    public static function updateNotification($data)
    {
        global $wpdb;

        $hook                        = $data['hook'];
        $htype                       = $data['htype'];
        $hid                         = isset($data['hid']) ? $data['hid'] : null;
        $active                      = $data['active'];

        if ($hid) {
            $query = '
            UPDATE `'.$wpdb->prefix.'mensagia_sms_notifications`
            SET active = '.$active.'
            WHERE hook = "'.$hook.'" AND option_name = "'.$hid.'" AND type = "'.$htype.'" ;';
        } else {
            $query = '
            UPDATE `'.$wpdb->prefix.'mensagia_sms_notifications`
            SET active = '.$active.'
            WHERE hook = "'.$hook.'" AND type = "'.$htype.'" ;';
        }

        $result = $wpdb->query($query);

        if ($result) {
            echo "true";
        } else {
            echo "false";
        }
    }


    public static function updateTextNotifications($data)
    {
        global $wpdb;

        $notifications = json_decode(stripslashes($data['json']));

        if (!json_last_error())
        {
            $errors = array();

            if ($notifications) {
                foreach ($notifications as $notification) {
                    $query = '
                    UPDATE `'.$wpdb->prefix.'mensagia_sms_notifications_lang`
                    SET message = "'.sanitize_textarea_field($notification->text).'" 
                    WHERE id = '.(int) $notification->id.';';

                    $result = $wpdb->query($query);

                    if (!$result) {
                        array_push($errors, $notification->text);
                    }
                }
            }

            if (empty($errors)) {
                echo "true";
            } else {
                echo "false";
            }
        } else {
            echo "false";
        }


    }

    public static function isHookEnabled($hook, $type, $option_name = null)
    {
        global $wpdb;

        if ($option_name !== null) {
            $where = " AND option_name = '".$option_name."' ";
        } else {
            $where = "";
        }

        $query = 'SELECT * FROM `'.$wpdb->prefix.'mensagia_sms_notifications` 
            WHERE hook = "'.$hook.'" AND active=1 AND type = "'.$type.'" '.$where .";";

        $result = $wpdb->get_results($query, ARRAY_A);

        if (count($result)) {
            if (isset($result[0])) {
                return $result[0];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
