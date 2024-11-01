<?php

class MensagiaHooks
{
    private $mensagiaSDK;

    public function __construct()
    {
        $this->mensagiaSDK = new MensagiaSDK();
    }


    public function getDataToTransformMessage($notification)
    {
        $data = array();

        // customer basic
        $data['{customer_firstname}'] = isset($notification['customer_firstname']) ? $notification['customer_firstname'] : "";
        $data['{customer_lastname}']  = isset($notification['customer_lastname']) ? $notification['customer_lastname'] : "";
        $data['{customer_email}']     = isset($notification['customer_email']) ? $notification['customer_email'] : "";

        // customer all
        $data['{customer_company}']  = isset($notification['customer_company']) ? $notification['customer_company'] : "";
        $data['{customer_address1}'] = isset($notification['customer_address1']) ? $notification['customer_address1'] : "";
        $data['{customer_address2}'] = isset($notification['customer_address2']) ? $notification['customer_address2'] : "";
        $data['{customer_postcode}'] = isset($notification['customer_postcode']) ? $notification['customer_postcode'] : "";
        $data['{customer_city}']     = isset($notification['customer_city']) ? $notification['customer_city'] : "";
        $data['{customer_country}']  = isset($notification['customer_country']) ? $notification['customer_country'] : "";
        $data['{customer_state}']    = isset($notification['customer_state']) ? $notification['customer_state'] : "";
        $data['{customer_phone}']    = isset($notification['customer_phone']) ? $notification['customer_phone'] : "";

        // order variables
        $data['{order_id}']             = isset($notification['order_id']) ? $notification['order_id'] : "";
        $data['{order_total_paid}']     = isset($notification['order_total_paid']) ? $notification['order_total_paid'] : "";
        $data['{order_currency}']       = isset($notification['order_currency']) ? $notification['order_currency'] : "";
        $data['{order_data_es}']        = isset($notification['order_data_es']) ? $notification['order_data_es'] : "";
        $data['{order_data_en}']        = isset($notification['order_data_en']) ? $notification['order_data_en'] : "";
        $data['{order_payment_method}'] = isset($notification['order_payment_method']) ? $notification['order_payment_method'] : "";

        // refunds
        $data['{total_refunded}']          = isset($notification['total_refunded']) ? str_replace('-', '', $notification['total_refunded']) : "";
        $data['{order_currency_refunded}'] = isset($notification['order_currency_refunded']) ? $notification['order_currency_refunded'] : "";

        //shop vars
        $data['{shop_name}']   = $notification['shop_name'];
        $data['{shop_domain}'] = $notification['shop_domain'];
        $data['{shop_email}']  = $notification['shop_email'];

        //product
        $data['{product_id}']              = isset($notification['product_id']) ? $notification['product_id'] : "";
        $data['{product_name}']            = isset($notification['product_name']) ? $notification['product_name'] : "";
        $data['{product_created_date_es}'] = isset($notification['product_created_date_es']) ? $notification['product_created_date_es'] : "";
        $data['{product_created_date_en}'] = isset($notification['product_created_date_en']) ? $notification['product_created_date_en'] : "";

        return $data;
    }


    public function sendNotification($params, $hook, $type, WC_Order $order = null, WC_Order_Refund $refund = null, WC_Product $product = null)
    {
        $customer_question     = null;
        $id_country_prestashop = null;

        //shop vars
        $notification['shop_name']   = get_bloginfo('name');
        $notification['shop_domain'] = get_site_url();
        $notification['shop_email']  = get_bloginfo('admin_email');

        // wp lang
        $notification['wp_code_lang'] = substr(get_bloginfo('language'), 0, 2);

        switch ($hook['hook']) {

            case 'deletedProduct':
                $notification['product_id']              = $product->get_id();
                $notification['product_name']            = $product->get_name();
                $notification['product_created_date_es'] = $product->get_date_created()->format('d-m-Y H:i:s');
                $notification['product_created_date_en'] = $product->get_date_created()->format('Y-m-d H:i:s');
                break;
            case 'orderRefunded':
                $notification['total_refunded']          = $refund->get_total();
                $notification['order_currency_refunded'] = $refund->get_currency();
            default:
                $notification['full_name']             = $order->get_billing_first_name() . " " . $order->get_billing_last_name();
                $notification['customer_code_country'] = $order->get_billing_country();

                // customer basic
                $notification['customer_firstname'] = $order->get_billing_first_name();
                $notification['customer_lastname']  = $order->get_billing_last_name();
                $notification['customer_email']     = $order->get_billing_email();

                // customer all
                $notification['customer_company']      = $order->get_billing_company();
                $notification['customer_address1']     = $order->get_billing_address_1();
                $notification['customer_address2']     = $order->get_billing_address_2();
                $notification['customer_postcode']     = $order->get_billing_postcode();
                $notification['customer_city']         = $order->get_billing_city();
                $notification['customer_country']      = $order->get_billing_country();
                $notification['customer_state']        = $order->get_shipping_state();
                $notification['customer_phone']        = $order->get_billing_phone();

                // order variables
                $notification['order_id']             = $order->get_order_number();
                $notification['order_total_paid']     = $order->get_total();
                $notification['order_currency']       = $order->get_currency();
                $notification['order_data_es']        = $order->get_date_created()->format('d-m-Y H:i:s');
                $notification['order_data_en']        = $order->get_date_created()->format('Y-m-d H:i:s');
                $notification['order_payment_method'] = $order->get_payment_method_title();

                //var_dump($notification);die();
                break;
        }

        $ps_data = $this->getDataToTransformMessage($notification);

        //var_dump($ps_data);die();

        $login          = (string) get_option('MENSAGIA_LOGIN_EMAIL');
        $password       = (string) get_option('MENSAGIA_LOGIN_PASSWORD');
        $connected      = (bool) get_option('MENSAGIA_AUTHENTICATED');
        $prefix_mode    = (string) get_option('MENSAGIA_PREFIX_MODE');

        if ($connected) {
            $authentication = $this->mensagiaSDK->authenticate($login, $password);

            // miramos si se autentifica y puede enviar SMS
            if ($authentication['result'] != 'error') {
                if ($type == 'customer') {
                    if ($notification['customer_phone']) {

                        // limpiamos el número de caracteres extraños
                        $phone_mobile = $this->mensagiaSDK->cleanNumber($notification['customer_phone']);

                        if ($prefix_mode == 'check_prefixs') {
                            // Buscamos el país en Mensagia
                            $mensagia_country = MensagiaCountry::getMensagiaCountryByISO($notification['customer_code_country']);

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
                                (int) $hook['id'],
                                $notification['wp_code_lang']
                            );


                            if (isset($message[0]['message'])) {
                                $sms = $message[0]['message'];
                                $sms_transformed = $this->transformSMSToSend($sms, $ps_data);

                                //var_dump($sms_transformed);die();

                                $sendSMS = $this->mensagiaSDK->sendSMS(
                                    $phone_mobile,
                                    $sms_transformed,
                                    (string) get_option('MENSAGIA_API_CONFIGURATION')
                                );

                                if ($sendSMS['result'] == 'error') {
                                    return $this->returnResponseErrors($sendSMS, $phone_mobile, $sms_transformed);
                                } else {
                                    new MensagiaWPAdminNotices('updated', 'SMS sent to customer. Number: '.$phone_mobile.". Message: ".$sms_transformed);
                                }
                            }
                        } else {
                            new MensagiaWPAdminNotices('error', 'It was not possible to send the SMS notification to the contact '.$notification['full_name'].' because he does not have a mobile number.');
                            return false;
                        }
                    } else {
                        new MensagiaWPAdminNotices('error', 'It was not possible to send the SMS notification to the contact '.$notification['full_name'].' because he does not have a mobile number.');
                        return false;
                    }
                }

                $phone_mobile   = null;
                $message        = null;

                if ($type == 'admin') {
                    // buscamos los admins
                    $admins = MensagiaAdmin::getAdmins();
                    $numbers = array();

                    if (count($admins)) {
                        foreach ($admins as $admin) {
                            // limpiamos el número de caracteres extraños
                            $phone_mobile = $this->mensagiaSDK->cleanNumber($admin->number);

                            if ($phone_mobile)
                                array_push($numbers, $phone_mobile);
                        }

                        $phone_mobile = implode(',', $numbers);
                    }


                    if ($phone_mobile) {
                        // buscamos si hay un mensaje
                        $message = MensagiaSMSNotification::getMessagesNotification((int) $hook['id'], $notification['wp_code_lang']);

                        if (isset($message[0]['message'])) {
                            $sms = $message[0]['message'];

                            $sms_transformed = $this->transformSMSToSend($sms, $ps_data);

                            $sendSMS = $this->mensagiaSDK->sendSMS(
                                $phone_mobile,
                                $sms_transformed,
                                (string) get_option('MENSAGIA_API_CONFIGURATION')
                            );

                            if ($sendSMS['result'] == 'error') {
                                return $this->returnResponseErrors($sendSMS, $phone_mobile, $sms_transformed);
                            } else {
                                new MensagiaWPAdminNotices('updated', 'SMS sent to admins. Numbers: '.$phone_mobile.". Message: ".$sms_transformed);
                                return true;
                            }
                        }
                    } else {
                        new MensagiaWPAdminNotices('error', "It was not possible to send the SMS notification because there aren't admins.");
                        return false;
                    }
                }
            } else {
                new MensagiaWPAdminNotices('error', 'No SMS notification was sent to '.$type.' because your Mensagia login details are wrong.');
                return false;
            }
        } else {
            new MensagiaWPAdminNotices('error', 'No SMS notification was sent to '.$type.' because you are not connected to Mensagia. '.
                'Re-enter your login details.');
            return false;
        }
    }


    private function transformSMSToSend($sms, $ps_data)
    {
        preg_match_all('/\{([^}]+)\}/', $sms, $matches);

        if (isset($matches[1])) {
            $mergeTagsFound = $matches[1];

            foreach ($mergeTagsFound as $tag) {
                if (isset($ps_data['{'.$tag.'}'])) {
                    $sms = str_replace('{'.$tag.'}', $ps_data['{'.$tag.'}'], $sms);
                } else {
                    $sms = str_replace('{'.$tag.'}', '', $sms);
                }
            }
        }

        return $sms;
    }


    public function returnResponseErrors($response, $phone_mobile, $message)
    {
        $prefix = "[Mensagia SMS] SMS not sent to ".$phone_mobile." (Message: ".$message."). ";

        if (isset($response['errors']['validation_errors'])) {
            $validations_errors = array();

            foreach ($response['errors']['validation_errors'] as $error_code => $error_message) {
                if (!is_array($error_message)) {
                    new MensagiaWPAdminNotices('error', $prefix.$error_code.": ".$error_message);
                } else {
                    foreach ($error_message as $index => $value) {
                        if (!is_array($value)) {
                            new MensagiaWPAdminNotices('error', $prefix.$error_code.": ".$index." ".$value);
                        } else {
                            foreach ($value as $msg) {
                                new MensagiaWPAdminNotices('error', $prefix.$error_code.": ".$index." ".$msg);
                            }
                        }
                    }
                }
            }
        } else {
            $message_error = $prefix.$response['errors']['code'];

            $message_error .= ": ".$response['errors']['message'];

            new MensagiaWPAdminNotices('error', $message_error);
        }

        return false;
    }
}
