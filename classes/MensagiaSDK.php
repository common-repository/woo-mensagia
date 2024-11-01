<?php

class MensagiaSDK
{
    private $email;
    private $password;

    private $base_uri;
    private $api_version;
    private $url_access_token;

    private $token;
    private $token_expires_in;
    private $token_expires_at;

    private $curl_ssl_verifier;


    public function __construct()
    {
        $this->base_uri             = "https://api.mensagia.com/";
        $this->api_version          = "v1";

        $this->email                = null;
        $this->password             = null;

        $this->token                = null;
        $this->token_expires_in     = null;
        $this->token_expires_at     = null;
    }

    public function authenticate($email, $password)
    {
        $this->email        = $email;
        $this->password     = $password;

        $data = array(
            'email'             =>   $this->email,
            'password'          =>   $this->password,
        );

        $result = $this->postRequest('login', $data);

        // CHECK THE REQUEST
        if (isset($result['data'])) {
            $this->token            = $result['data']['token'];
            $this->token_expires_in = $result['data']['expires_in'];
            $this->token_expires_at = $result['data']['expires_at'];

            return array(
                'result'    => 'success',
                'data'      => $result['data']
            );
        } else {
            return array(
                'result'        => 'error',
                'error_code'    => 'AUTHENTICATION_FAILS',
                'errors'        => $result['error']
            );
        }
    }

    private function getAuthorizationHeader()
    {
        return array(
            'Authorization: Bearer '.$this->token,
            'API-source: WooCommerce'
        );
    }


    public function getBalance()
    {
        $url = "balance";

        $balance = $this->request('GET', $url);

        return $balance;
    }

    public function getApiConfigurations()
    {
        $url = "api_configurations";

        $api_configurations = $this->request('GET', $url);

        return $api_configurations;
    }

    public function sendSMS($number, $message, $configuration_name)
    {
        // Post variables to add to the request.
        $data = array(
            'configuration_name'  =>  $configuration_name,
            'message'             =>  $message,
            'numbers'             =>  $number,
        );

        $result = $this->postRequest('push/simple', $data);

        // CHECK THE REQUEST
        if (isset($result['data'])) {
            return array(
                'result'    => 'success',
                'data'      => $result['data']
            );
        } elseif (isset($result['error'])) {
            return array(
                'result'        => 'error',
                'error_code'    => 'AUTHENTICATION_FAILS',
                'errors'        => $result['error']
            );
        }
    }

    public function getSummaryImport($process_id)
    {
        $url = "processes/".$process_id."/import_summary";

        $summary = $this->request('GET', $url);

        return $summary;
    }

    public function getImportErrors($process_id)
    {
        $url = "processes/".$process_id."/import_errors/?per_page=500";

        $summary = $this->request('GET', $url);

        return $summary;
    }
    
    public function cleanNumber($number)
    {
        return preg_replace('/[^0-9]/', '', $number);
    }

    public function checkAndSetPrefix($number, $country_prefixes)
    {
        // miramos que tenga un formato correcto
        if (! ctype_digit($number)) {
            return false;
        }

        // buscamos si el número tiene el prefijo del país
        $found          = false;
        $first_prefix   = null;

        if ($country_prefixes) {
            //get the Prefixes and check it
            $prefixes = explode(',', $country_prefixes);

            if ($prefixes) {
                $i = 1;

                foreach ($prefixes as $prefix) {
                    if ($i == 1) {
                        $first_prefix = $prefix;
                    }

                    //count prefix lenght
                    $length = strlen($prefix);

                    //get the $lenght chars of the number
                    $prefix_number = $this->getCharsFromNumber($number, $length);

                    if ($prefix_number == $prefix) {
                        $found =  $prefix;
                    }

                    $i++;
                }
            }
        }

        if ($found) {
            return $number;
        } else {
            // si no lo encuentra, de momento añade, aunque deberia comprobar si tiene algun prefijo.
            return $first_prefix.$number;
        }
    }

    private function getCharsFromNumber($number, $chars)
    {
        return substr($number, 0, $chars);
    }

    public function getProcesses($processes_list, $type = null)
    {
        $params = array(
            'processes_ids' => $processes_list,
            'per_page'      => 500
        );

        if ($type) {
            $params['type'] = $type;
        }

        $url = "processes/?" . http_build_query($params);

        $check = $this->request('GET', $url);

        return $check;
    }

    public function importByJSON($json)
    {
        $params = array(
            'contacts_json' => $json,
            'callback_url'  => 'http://195.10.21.174/pass/mensagia_test_callback.php'
        );

        $url = "contacts/import/json";

        $import = $this->request('POST', $url, $params);

        return $import;
    }


    public function existsExtraField($name)
    {
        $params = array(
            'name'        => $name,
            'search_type' => 'equals'
        );

        // Check if exists
        $url = "extrafields/?" . http_build_query($params);
        $result = $this->request('GET', $url);

        // Check result
        if (isset($result['meta']['pagination']['total'])) {
            if ($result['meta']['pagination']['total'] > 0) {
                return $result;
            }
        }

        return false;
    }


    public function createExtraField($parameters)
    {
        $url = "extrafields";

        $contact = $this->request('POST', $url, $parameters);

        return $contact;
    }


    public function existsContact($parameters)
    {
        $parameters['search_type']  = 'equals';

        // Check if exists
        $url = "contacts/?" . http_build_query($parameters);
        $result = $this->request('GET', $url);

        // Check result
        if (isset($result['meta']['pagination']['total'])) {
            if ($result['meta']['pagination']['total'] > 0) {
                return $result;
            }
        }

        return false;
    }

    public function createContact($parameters)
    {
        $url = "contacts";

        $contact = $this->request('POST', $url, $parameters);

        return $contact;
    }


    public function updateContact($id, $parameters)
    {
        $url = "contacts/".$id;

        $contact = $this->request('POST', $url, $parameters);

        return $contact;
    }


    public function existsAgendaByExactName($name)
    {
        $params = array(
            'name' => $name,
            'search_type' => 'equals'
        );

        // Check if exists
        $url = "agendas/?" . http_build_query($params);
        $result = $this->request('GET', $url);

        // Check result
        if (isset($result['meta']['pagination']['total'])) {
            if ($result['meta']['pagination']['total'] > 0) {
                return $result;
            }
        }

        return false;
    }

    public function deleteAgendaByID($id)
    {
        $url = "agendas/".$id;

        $deleted = $this->request('DELETE', $url);

        if (isset($deleted['data']['result'])) {
            return $deleted['data']['result'];
        } else {
            return false;
        }
    }

    public function createAgenda($name)
    {
        $params = array(
            'name'  =>  $name,
        );

        $url = "agendas";

        $agenda = $this->request('POST', $url, $params);

        return $agenda;
    }


    private function request($type, $url, $params = false)
    {
        $headers = $this->getAuthorizationHeader();

        $url = $this->base_uri.$this->api_version."/".$url;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (in_array($type, array('POST'))) {
            curl_setopt($ch, CURLOPT_POST, 1);
        }


        if (in_array($type, array('DELETE'))) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        if ($params) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }


    private function getRequest($relative_url)
    {
        $headers = $this->getAuthorizationHeader();

        $url = $this->base_uri.$this->api_version."/".$relative_url;


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }


    private function postRequest($relative_url, $params)
    {
        $headers = $this->getAuthorizationHeader();

        $url = $this->base_uri.$this->api_version."/".$relative_url;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    private function deleteRequest($relative_url, $params)
    {
        $headers = $this->getAuthorizationHeader();

        $url = $this->base_uri.$this->api_version."/".$relative_url;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}
