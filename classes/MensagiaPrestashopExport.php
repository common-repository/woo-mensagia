<?php

class MensagiaPrestashopExport
{
    private $roles;
    private $exporting;
    private $prefix_agenda;
    private $delete_agendas;
    private $groups;
    private $limit;
    private $jobs;
    private $jobs_done;
    private $next_index_group_to_export;
    private $export_started;
    private $processes;
    private $extrafields;
    private $default_language_id;
    private $date_format_lite;
    private $date_format_full;
    private $waiting_finish_processes;
    private $finished;
    private $processes_checked_errors;
    private $prefix_mode;
    private $total_rows_processed;
    private $total_created_contacts;
    private $total_imported_contacts;
    private $total_errors;
    private $mensagiaSDK;

    private $icon_separator;

    public function __construct($email, $password, $options)
    {
        $options = json_decode(str_replace("\\", '', $options), true);

        $this->roles                      = $options['roles'];
        $this->exporting                  = (bool) $options['exporting'];
        $this->prefix_agenda              = sanitize_text_field($options['prefix_agenda']);
        $this->delete_agendas             = (bool) $options['delete_agendas'];
        $this->limit                      = (int) $options['limit'];
        $this->groups                     = $options['groups'];
        $this->jobs                       = (int) $options['jobs'];
        $this->jobs_done                  = (int) $options['jobs_done'];
        $this->woo_total_users            = (int) $options['woo_total_users'];
        $this->next_index_group_to_export = $options['next_index_group_to_export'];
        $this->export_started             = (bool) $options['export_started'];
        $this->processes                  = $options['processes'];
        $this->extrafields                = $options['extrafields'];
        $this->default_language_id        = $options['default_language_id'];
        $this->date_format_lite           = (string) sanitize_text_field($options['date_format_lite']);
        $this->date_format_full           = (string) sanitize_text_field($options['date_format_full']);
        $this->waiting_finish_processes   = (bool) $options['waiting_finish_processes'];
        $this->finished                   = (bool) $options['finished'];
        $this->processes_checked_errors   = $options['processes_checked_errors'];
        $this->prefix_mode                = sanitize_text_field($options['prefix_mode']);
        $this->total_rows_processed       = (int) $options['total_rows_processed'];
        $this->total_created_contacts     = (int) $options['total_created_contacts'];
        $this->total_imported_contacts    = (int) $options['total_imported_contacts'];
        $this->total_errors               = (int) $options['total_errors'];

        $this->mensagiaSDK        = new MensagiaSDK();
        $this->mensagiaSDK->authenticate(
            $email,
            $password
        );

        $this->icon_separator = "<i class='icon-angle-double-right' aria-hidden='true'></i>";
    }

    public function exportFinished()
    {
        $html       = "";
        $errors     = "";

        $html .= "<span style='font-weight:bold;font-size:14px;display:block;margin-top:15px;".
            "text-decoration: underline;margin-bottom:5px;'>".
            __('Import completed', 'woo-mensagia')."</span>";
        $html .= $this->icon_separator." ".__('Total contacts processed', 'woo-mensagia').": ".
            $this->total_rows_processed.". <br>";
        $html .= $this->icon_separator." ".__('Total contacts created', 'woo-mensagia').": ".
            $this->total_created_contacts.". <br>";
        $html .= $this->icon_separator." ".__('Total contacts imported', 'woo-mensagia').": ".
            $this->total_imported_contacts.". <br>";
        $html .= $this->icon_separator." ".__('Total errors', 'woo-mensagia').": ".
            $this->total_errors.".<br>";

        return json_encode(array(
            'html'      => $html,
            'options'   => get_object_vars($this),
            'errors'    => $errors,
        ));
    }

    public function waitingFinishProcesses()
    {
        $html       = "";
        $errors     = "";
        $continue   = true;

        $count_processes = count($this->processes);
        $count_processes_checked = count($this->processes_checked_errors);

        $counters               = array();

        $counters['finished']   = 0;
        $counters['error']      = 0;
        $counters['waiting']    = 0;

        if ($count_processes &&  $count_processes_checked < $count_processes) {
            $processes_list = implode(',', $this->processes);

            if (!$this->waiting_finish_processes) {
                $html .= "<span style='font-weight:bold;font-size:14px;display:block;margin-top:15px;".
                    "text-decoration: underline;margin-bottom:5px;'>".
                    __('Import groups to Mensagia', 'woo-mensagia')."</span>";
                $this->waiting_finish_processes = true;
            }

            $processes = $this->mensagiaSDK->getProcesses($processes_list, 'ContactsMassiveJson');

            if (isset($processes['data'])) {
                foreach ($processes['data'] as $process) {
                    if ($process['state'] == 'finished') {
                        if (!in_array($process['id'], $this->processes_checked_errors)) {

                            // Buscamos el resumen
                            $summary = $this->mensagiaSDK->getSummaryImport($process['id']);

                            if (isset($summary['data'])) {
                                $html .= " ".$this->icon_separator." ".
                                    __('Request processed correctly.', 'woo-mensagia');
                                $html .= __('Processed contacts', 'woo-mensagia').": ".
                                    $summary['data']['total_rows_processed'].". ";
                                $html .= __('Created contacts', 'woo-mensagia').": ".
                                    $summary['data']['total_created'].". ";
                                $html .= __('Imported contacts', 'woo-mensagia').": ".
                                    $summary['data']['total_imported'].". ";
                                $html .= __('Errors', 'woo-mensagia').": ".
                                    $summary['data']['total_errors'].".<br>";

                                // actualizamos contadores
                                $this->total_rows_processed       =
                                    $this->total_rows_processed + $summary['data']['total_rows_processed'];
                                $this->total_created_contacts     =
                                    $this->total_created_contacts + $summary['data']['total_created'];
                                $this->total_imported_contacts    =
                                    $this->total_imported_contacts + $summary['data']['total_imported'];
                                $this->total_errors               =
                                    $this->total_errors + $summary['data']['total_errors'];

                                $counters['finished']++;
                                array_push($this->processes_checked_errors, $process['id']);
                            }

                            // buscamos los errores

                            $import_errors = $this->mensagiaSDK->getImportErrors($process['id']);

                            if (isset($import_errors['data'])) {
                                foreach ($import_errors['data'] as $errorList) {
                                    $errors .= $this->icon_separator." <b>[".$errorList['error_code']."]</b> Name: ".
                                        $errorList['json_user_object']['contact']['name'] .
                                        " (".$errorList['json_user_object']['contact']['email']."). 
                                        Phone Mobile: ".$errorList['json_user_object']['contact']['number']."<br>";
                                }
                            }
                        }
                    } elseif ($process['state'] == 'error') {
                        $counters['error']++;
                        array_push($this->processes_checked_errors, $process['id']);

                        $errors.= __('There was an error importing in Mensagia.', 'woo-mensagia');
                        $continue = false;
                    } else {
                        $counters['waiting']++;
                    }
                }

                $total_processes_done = $counters['finished'] + $counters['error'];
                $count_processes_checked = count($this->processes_checked_errors);

                if ($total_processes_done == $count_processes and $total_processes_done != 0) {
                    $this->finished = true;
                    $this->jobs_done++;
                    $html .= $this->icon_separator." ".
                        __('Import finished. Completed requests: ', 'woo-mensagia')." ".
                        $count_processes_checked."/".$count_processes."<br>";
                } else {
                    $html .= $this->icon_separator." ".
                        __('Making request to Mensagia. Completed requests: ', 'woo-mensagia')." ".
                        $count_processes_checked."/".$count_processes."<br>";
                }
            } else {
                $errors.= __('There was an unexpected error checking the processes in Mensagia.', 'woo-mensagia');
                $continue = false;
            }
        } else {
            $this->finished = true;
            $this->jobs_done++;
        }

        return json_encode(array(
            'html'      => $html,
            'options'   => get_object_vars($this),
            'errors'    => $errors,
            'continue'  => $continue,
        ));
    }

    public function exportGroups()
    {
        $count_groups = count($this->groups);

        $html   = "";
        $errors = "";

        if (!$this->export_started) {
            $html .= "<span style='font-weight:bold;font-size:14px;display:block;margin-top:15px;".
                "text-decoration: underline;margin-bottom:5px;'>".
                __('Import requests to Mensagia', 'woo-mensagia')."</span>";
            $this->export_started = true;
        }

        if ($this->next_index_group_to_export !== null) {
            if ($this->groups[$this->next_index_group_to_export]['jobs']) {
                if (! $this->groups[$this->next_index_group_to_export]['export_started']) {
                    $html .= "<b>".__('Users import requests to the agenda: ', 'woo-mensagia').
                        $this->groups[$this->next_index_group_to_export]['agenda_name_mensagia']."</b><br>";
                    $this->groups[$this->next_index_group_to_export]['export_started'] = true;
                }

                $html .= $this->icon_separator." ".
                    __('Request successfully made. Request number ', 'woo-mensagia').
                    ($this->groups[$this->next_index_group_to_export]['jobs_done'] + 1).".<br>";

                // exportamos los usuarios
                $errors_exports = $this->processExportUsers(
                    $this->groups[$this->next_index_group_to_export],
                    $this->next_index_group_to_export
                );

                // controlamos los errores de sin número de movil antes de exportar al usuario
                if (count($errors_exports['no_phone_mobile'])) {
                    foreach ($errors_exports['no_phone_mobile'] as $errorList) {
                        $errors .= $this->icon_separator." <b>[WITHOUT_NUMBER]</b> Name: ".$errorList['display_name']." ".
                            " (".$errorList['email'].")<br>";
                        $this->total_rows_processed++;
                        $this->total_errors++;
                    }
                }

                // controlamos los errores de no address antes de exportar al usuario
                if (count($errors_exports['no_address'])) {
                    foreach ($errors_exports['no_address'] as $errorList) {
                        $errors .= $this->icon_separator." <b>[WITHOUT_ADDRESS]</b> Name: ".$errorList['display_name']." ".
                            " (".$errorList['email'].")<br>";
                        $this->total_rows_processed++;
                        $this->total_errors++;
                    }
                }

                // Actualizamos contadores de trabajos
                $this->groups[(int) $this->next_index_group_to_export]['jobs_done']++;
                $this->jobs_done++;

                // miramos si ha hecho todos los trabajos para este grupo
                $actual_jobs_done = (int) $this->groups[$this->next_index_group_to_export]['jobs_done'];

                if ($actual_jobs_done == (int) $this->groups[$this->next_index_group_to_export]['jobs']) {
                    if ((int) $this->next_index_group_to_export + 1 < (int) $count_groups) {
                        $this->next_index_group_to_export++;
                    } else {
                        $this->next_index_group_to_export = null;

                        $html .= "<b>".
                            __('Import requests from groups to Mensagia finished', 'woo-mensagia')."</b><br>";
                        $html .= $this->icon_separator." ".
                            __('Import request from groups to Mensagia finished', 'woo-mensagia')."<br>";
                    }
                }
            } else {
                if ((int) $this->next_index_group_to_export + 1 < (int) $count_groups) {
                    $this->next_index_group_to_export++;
                } else {
                    $this->next_index_group_to_export = null;

                    $html .= "<b>".__('Import request from groups to Mensagia finished', 'woo-mensagia').
                        "</b><br>";

                    $html .= $this->icon_separator." ".
                        __('Import requests to Mensagia have completed successfully.', 'woo-mensagia')."<br>";
                }
            }
        } else {
            $html .= $this->icon_separator." ".__(
                'Groups to export do not have valid users.',
                'woo-mensagia'
                );
        }

        $continue = true;

        return json_encode(array(
            'html'      => $html,
            'options'   => get_object_vars($this),
            'errors'    => $errors,
            'continue'  => $continue,
        ));
    }

    private function getCustomerLastOrderDate($id_customer)
    {
        $customer_orders = get_posts(array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $id_customer,
            'post_type'   => 'shop_order',
            'post_status' => 'wc-completed',
            'orderby'     => 'date',
            'order'       => 'DESC'
        ));


        if (isset($customer_orders[0]->post_date)) {
            if ($customer_orders[0]->post_date) {
                return $customer_orders[0]->post_date;
            } else {
                return null;
            }
        }

        return null;
    }

    private function setFormatToDate($valueExtrafield, $extrafield)
    {
        if ($valueExtrafield != '0000-00-00' and $valueExtrafield != '0000-00-00 00:00:00'
            and $valueExtrafield != null and $valueExtrafield != '') {
            if ($extrafield['date_format_type'] == 'date_format_lite') {
                $date_format = $this->date_format_lite;
            } else {
                $date_format = $this->date_format_full;
            }

            $date = new DateTime($valueExtrafield);
            return $date->format($date_format);
        }

        return null;
    }


    private function getCustomerSpent($id_customer)
    {
        $customer = new WC_Customer($id_customer);

        return $customer->get_total_spent();
    }

    private function getCustomerSpentCurrentYear($id_customer)
    {
        $customer_orders = get_posts(array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $id_customer,
            'post_type'   => array( 'shop_order' ),
            'post_status' => array( 'wc-completed' ),
            'date_query' => array(
                'after' => date('Y-01-01 00:00:00', strtotime('now')),
                'before' => date('Y-m-d 23:59:59', strtotime('now'))
            )
        ));

        $total = 0;
        foreach ($customer_orders as $customer_order) {
            $order = wc_get_order($customer_order);
            $total += $order->get_total();
        }

        return $total;
    }

    private function getCustomerSpentLastYear($id_customer)
    {
        $lastYear = date('Y') - 1;

        $customer_orders = get_posts(array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $id_customer,
            'post_type'   => array( 'shop_order' ),
            'post_status' => array( 'wc-completed' ),
            'date_query' => array(
                'after' => date($lastYear.'-01-01 00:00:00'),
                'before' => date($lastYear.'-12-31 23:59:59')
            )
        ));

        $total = 0;
        foreach ($customer_orders as $customer_order) {
            $order = wc_get_order($customer_order);
            $total += $order->get_total();
        }

        return $total;
    }

    private function getCustomerSpentCurrentMonth($id_customer)
    {
        $customer_orders = get_posts(array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $id_customer,
            'post_type'   => array( 'shop_order' ),
            'post_status' => array( 'wc-completed' ),
            'date_query' => array(
                'after' => date('Y-m-01 00:00:00'),
                'before' => date('Y-m-31 23:59:59')
            )
        ));

        $total = 0;
        foreach ($customer_orders as $customer_order) {
            $order = wc_get_order($customer_order);
            $total += $order->get_total();
        }

        return $total;
    }



    private function getCustomerSpentLastMonth($id_customer)
    {
        $customer_orders = get_posts(array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $id_customer,
            'post_type'   => array( 'shop_order' ),
            'post_status' => array( 'wc-completed' ),
            'date_query' => array(
                'after' => date('Y-m-01 00:00:00', strtotime('-1 month')),
                'before' => date('Y-m-31 23:59:59', strtotime('-1 month'))
            )
        ));

        $total = 0;
        foreach ($customer_orders as $customer_order) {
            $order = wc_get_order($customer_order);
            $total += $order->get_total();
        }

        return $total;
    }


    private function getCustomerNumberOrdersValids($id_customer)
    {
        $customer_orders = get_posts(array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $id_customer,
            'post_type'   => 'shop_order',
            'post_status' => 'wc-completed',
            //'post_status' => array_keys( wc_get_order_statuses() ),
        ));

        return count($customer_orders);
    }

    private function processExportUsers($group, $actual_index_group)
    {
        global $wpdb;

        $no_phone_mobile    = array();
        $no_address         = array();
        $valids             = array();

        // sanitize roles
        foreach($this->roles as $role)
        {
            if (! key_exists($role, wp_roles()->roles) )
                exit('Roles modified');
        }

        $args = array(
            'role__in'  => $this->roles,
            'number'  => $this->limit,
            'offset'  => $this->limit * $group['jobs_done'],
        );

        $users = get_users($args);

        if (empty($this->roles)) {
            $users = null;
        }

        if ($users) {
            foreach ($users as $user) {
                $wc_customer = new WC_Customer($user->ID);

                $phone_mobile = $wc_customer->get_billing_phone();

                // limpiamos el número de caracteres extraños
                $phone_mobile = $this->mensagiaSDK->cleanNumber($phone_mobile);

                if ($this->prefix_mode == 'check_prefixs') {

                    // Buscamos el pais de la compra
                    $country_iso_code_address = $wc_customer->get_billing_country();

                    // Buscamos el país en Mensagia
                    $mensagia_country = $this->getMensagiaCountryByISO($country_iso_code_address);

                    // comprobamos prefijo del número
                    $phone_mobile = $this->mensagiaSDK->checkAndSetPrefix(
                        $phone_mobile,
                        $mensagia_country->phone
                    );
                }

                if (!$phone_mobile)
                    $phone_mobile = null;

                // comprobamos que $phone_mobile no sea null despues de comprobaciones anteriores
                if ($phone_mobile or $user->user_email) {

                    // buscamos los valores de los extrafields que nos han enviado
                    $extrafields_users = array();

                    $valueExtrafield = "";

                    foreach ($this->extrafields as $extrafield) {
                        if ($extrafield['woo_group'] == 'customer') {
                            if ($extrafield['name'] == 'woo_id_customer') {
                                $valueExtrafield = $user->ID;
                            } elseif ($extrafield['name'] == 'woo_date_add') {
                                $valueExtrafield = $user->user_registered;
                            } elseif ($extrafield['name'] == 'woo_user_role') {
                                $valueExtrafield = $wc_customer->get_role();
                            }

                            if ($extrafield['extra_field_type'] != 'date') {
                                if ($valueExtrafield === null) {
                                    $valueExtrafield = "";
                                }
                                $extrafields_users[$extrafield['name']] = $valueExtrafield;
                            } else {
                                if ($this->setFormatToDate($valueExtrafield, $extrafield)) {
                                    $extrafields_users[$extrafield['name']] = $this->setFormatToDate(
                                        $valueExtrafield,
                                        $extrafield
                                    );
                                }
                            }
                        } elseif ($extrafield['woo_group'] == 'order') {
                            switch ($extrafield['name']) {
                                case 'woo_date_last_order':
                                    $last_order_date = $this->getCustomerLastOrderDate($user->ID);

                                    if ($last_order_date) {
                                        if ($this->setFormatToDate($last_order_date, $extrafield)) {
                                            $extrafields_users[$extrafield['name']] = $this->setFormatToDate(
                                                $last_order_date,
                                                $extrafield
                                            );
                                        }
                                    }
                                    break;
                                case 'woo_orders_quantity':
                                    $num_orders_valids = $this->getCustomerNumberOrdersValids(
                                        $user->ID
                                    );
                                    $extrafields_users[$extrafield['name']] = $num_orders_valids;
                                    break;

                                case 'woo_orders_total_spent':
                                    $total_spent = $this->getCustomerSpent(
                                        $user->ID
                                    );
                                    $extrafields_users[$extrafield['name']] = $total_spent;
                                    break;

                                case 'woo_orders_total_spent_current_year':
                                    $total_spent = $this->getCustomerSpentCurrentYear(
                                        $user->ID
                                    );
                                    $extrafields_users[$extrafield['name']] = $total_spent;
                                    break;

                                case 'woo_orders_total_spent_last_year':
                                    $total_spent = $this->getCustomerSpentLastYear(
                                        $user->ID
                                    );
                                    $extrafields_users[$extrafield['name']] = $total_spent;
                                    break;

                                case 'woo_orders_total_spent_current_month':
                                    $total_spent = $this->getCustomerSpentCurrentMonth(
                                        $user->ID
                                    );
                                    $extrafields_users[$extrafield['name']] = $total_spent;
                                    break;

                                case 'woo_orders_total_spent_last_month':
                                    $total_spent = $this->getCustomerSpentLastMonth(
                                        $user->ID
                                    );
                                    $extrafields_users[$extrafield['name']] = $total_spent;
                                    break;
                            }
                        }
                    }


                    // creamos el array con los datos del usuario
                    $to_export = array(
                        'contact'   =>  array(
                            'number'    => $phone_mobile,
                            'name'      => $user->display_name,
                            'email'     => $user->user_email,
                        ),
                        'groups'    =>  $group['mensagia_agenda_id']
                    );


                    // añadimos extrafields si existen
                    if (count($extrafields_users)) {
                        $to_export['extra_fields'] = $extrafields_users;
                    }

                    array_push($valids, $to_export);
                }
            }

            // transformamos los validos en JSON
            $valids = json_encode($valids);

            $result = $this->mensagiaSDK->importByJSON($valids);

            if (isset($result['data'])) {
                array_push($this->groups[$actual_index_group]['processes'], $result['data']['process_id']);
                array_push($this->processes, $result['data']['process_id']);
            }
        }

        return array(
            'no_phone_mobile'   => $no_phone_mobile,
            'no_address'        => $no_address
        );
    }

    public function checkExtrafields()
    {
        $continue   = true;
        $errors     = "";
        $html       = "";

        if ($this->extrafields) {
            $html .= "<span style='font-weight:bold;font-size:14px;display:block;margin-top:15px;".
                "text-decoration: underline;margin-bottom:5px;'>".
                __('Extra fields', 'woo-mensagia')."</span>";

            foreach ($this->extrafields as $extrafield) {
                $html .= "<b>".__('Checking extra fields: ', 'woo-mensagia').": ".
                    $extrafield['nameToShow']." (".$extrafield['name'].")</b><br>";

                // comprobamos si existe el campo personalizado, si no existe, lo creamos.
                if (! $this->mensagiaSDK->existsExtraField($extrafield['name'])) {
                    $html.= $this->icon_separator." ".
                        __("The extra field doesn't exist on Mensagia.", 'woo-mensagia')."<br>";

                    // creamos el campo personalizado en Mensagia

                    $parameters = array(
                        'name'  =>  $extrafield['name'],
                        'type'  =>  $extrafield['extra_field_type']
                    );

                    if ($extrafield['extra_field_type'] == 'date') {
                        if ($extrafield['date_format_type'] == 'date_format_lite') {
                            $parameters['date_format'] = $this->date_format_lite;
                        } elseif ($extrafield['date_format_type'] == 'date_format_full') {
                            $parameters['date_format'] = $this->date_format_full;
                        }
                        
                        // El campo lo creamos con dia y mes con fechas con dos dígitos
                        $parameters['date_format'] = str_replace('d', 'D', $parameters['date_format']);
                        $parameters['date_format'] = str_replace('m', 'M', $parameters['date_format']);
                    }

                    $newExtrafield = $this->mensagiaSDK->createExtraField($parameters);

                    if (isset($newExtrafield['data'])) {
                        $html.= $this->icon_separator." ".
                            __('Extra field created succesfully.', 'woo-mensagia')."<br>";
                    } else {
                        $html.= $this->icon_separator." ".
                            __('Error when creating an extra field', 'woo-mensagia').".<br>";
                        $errors.= __('Error when creating an extra field', 'woo-mensagia').": ".
                            $extrafield['nameToShow']." (".$extrafield['name'].")<br>";
                        $continue = false;
                    }
                } else {
                    $html.= $this->icon_separator." ".
                        __('The extra field already exists in Mensagia.', 'woo-mensagia')."<br>";
                }

                $this->jobs_done++;
            }
        }

        return json_encode(array(
            'html'      => $html,
            'options'   => get_object_vars($this),
            'errors'    => $errors,
            'continue'  => $continue,
        ));
    }

    public function deleteAgendas()
    {
        $html       = "";

        if ($this->groups) {
            $html .= "<span style='font-weight:bold;font-size:14px;display:block;margin-top:15px;".
                "text-decoration: underline;margin-bottom:5px;'>".
                __('Delete agendas', 'woo-mensagia')."</span>";

            foreach ($this->groups as $group) {
                $html .= "<b>".__('Deleting agenda: ', 'woo-mensagia').
                    $group['agenda_name_mensagia']."</b><br>";

                $agenda = $this->mensagiaSDK->existsAgendaByExactName($group['agenda_name_mensagia']);

                if ($agenda) {
                    $deleted = $this->mensagiaSDK->deleteAgendaByID($agenda['data'][0]['id']);

                    if ($deleted) {
                        $html.= $this->icon_separator." ".
                            __('Agenda deleted succesfully.', 'woo-mensagia')."<br>";
                    } else {
                        $html.= $this->icon_separator." ".
                            __('The agenda could not be deleted.', 'woo-mensagia')."<br>";
                    }
                } else {
                    $html.= $this->icon_separator." ".
                        __("The agenda doesn't exist in Mensagia.", 'woo-mensagia')."<br>";
                }

                $this->jobs_done++;
            }
        }

        $continue = true;
        $errors   = "";

        return json_encode(array(
            'html'      => $html,
            'options'   => get_object_vars($this),
            'errors'    => $errors,
            'continue'  => $continue,
        ));
    }

    public function createAgendas()
    {
        $html       = "";
        $errors     = "";
        $continue   = true;

        if (count($this->groups)) {
            $html .= "<span style='font-weight:bold;font-size:14px;display:block;margin-top:15px;".
                "text-decoration: underline;margin-bottom:5px;'>".
                __('Creating agenda in Mensagia', 'woo-mensagia')."</span>";

            foreach ($this->groups as $index => $group) {
                $html .= "<b>".__('Creating agenda: ', 'woo-mensagia').
                    $group['agenda_name_mensagia']."</b><br>";

                $agenda_exists = $this->mensagiaSDK->existsAgendaByExactName($group['agenda_name_mensagia']);

                if ($agenda_exists) {
                    $this->groups[$index]['mensagia_agenda_id'] = $agenda_exists['data'][0]['id'];
                    $html.= $this->icon_separator." ".
                        __("The agenda exists in Mensagia.", 'woo-mensagia')."<br>";
                } else {
                    $agenda = $this->mensagiaSDK->createAgenda($group['agenda_name_mensagia']);

                    if (isset($agenda['data'])) {
                        $this->groups[$index]['mensagia_agenda_id'] = $agenda['data']['id'];
                        $html.= $this->icon_separator." ".
                            __('Agenda created succesfully.', 'woo-mensagia')."<br>";
                    } else {
                        $html.= $this->icon_separator." ".
                            __('Error creating agenda', 'woo-mensagia').".<br>";
                        $errors.= __('Error creating agenda', 'woo-mensagia').": ".
                            $group['agenda_name_mensagia']."<br>";
                        $continue = false;
                    }
                }

                $this->jobs_done++;
            }
        }

        return json_encode(array(
            'html'      => $html,
            'options'   => get_object_vars($this),
            'errors'    => $errors,
            'continue'  => $continue,
        ));
    }

    public function exportInit()
    {
        $html = "<span style='font-weight:bold;font-size:14px;display:block;text-decoration: underline;margin-bottom:".
            "5px;'>".__('Preparing export groups to Mensagia', 'woo-mensagia')."</span>";

        $html.= $this->icon_separator." ". __('Collecting information for export to Mensagia.', 'woo-mensagia')."<br>";

        // contamos cuantos usuarios tenemos por grupos
        $this->woo_total_users = $this->countWordpressUsers();

        // variable para controlar si hay trabajos de exportación de usuarios
        $thereAreGroupsToExport = false;

        //Contamos el numero de trabajos a realizar
        if ($this->woo_total_users) {
            $thereAreGroupsToExport = true;

            $jobs_to_do = 0;

                // trabajos eliminar agendas
                if ($this->delete_agendas) {
                    $this->jobs++;
                }

                // trabajos crear agenda
                $this->jobs++;

                // contamos cuantos
                $jobs_to_do = $this->woo_total_users / $this->limit;

            if (($this->woo_total_users % $this->limit) > 0) {
                $jobs_to_do++;
            }

            $this->jobs = $this->jobs + (int) $jobs_to_do;

                // nombre de la agenda en mensagia
                $this->groups[0]['agenda_name_mensagia'] = $this->prefix_agenda;
            $this->groups[0]['jobs']                 = (int) $jobs_to_do;
            $this->groups[0]['jobs_done']            = (int) 0;
            $this->groups[0]['export_started']       = false;
            $this->groups[0]['processes']            = array();
        }

        // trabajos para campos personalizados
        $this->jobs = $this->jobs + count($this->extrafields);

        // trabajos esperar que finalice de importart
        $this->jobs++;

        if ($thereAreGroupsToExport) {
            $this->next_index_group_to_export = 0;
        } else {
            $thereAreGroupsToExport = null;
        }

        $this->jobs_done = 0;

        $continue = true;
        $errors   = "";

        return json_encode(array(
            'html'      => $html,
            'options'   => get_object_vars($this),
            'errors'    => $errors,
            'continue'  => $continue,
        ));
    }

    private function countWordpressUsers()
    {
        global $wpdb;

        $results = $wpdb->get_results('SELECT count(*) as count FROM '.$wpdb->prefix.'users', OBJECT);

        if (isset($results[0]->count)) {
            return $results[0]->count;
        } else {
            return 0;
        }
    }

    private function getValueFromMetaID($umeta_id, $user_id)
    {
        global $wpdb;

        $results = $wpdb->get_results('SELECT meta_value FROM '.$wpdb->prefix.'usermeta WHERE umeta_id='.$umeta_id.' and user_id='.$user_id, OBJECT);

        if (isset($results[0]->meta_value)) {
            return $results[0]->meta_value;
        } else {
            return null;
        }
    }

    private static function getMensagiaCountryByISO($code)
    {
        global $wpdb;

        $results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."mensagia_countries WHERE code='".strtoupper($code)."'", OBJECT);

        if (isset($results[0])) {
            return $results[0];
        } else {
            return null;
        }
    }
}
