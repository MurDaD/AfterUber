<?php
/**
 * Author: MurDaD
 * Author URL: https://github.com/MurDaD
 *
 * Description: APIHelper class
 */

namespace app;

use includes\Exception;

class APIHelper
{
    protected $url;
    protected $auth_key;
    protected $server_token;

    private $api_domain;
    private $client_id;
    private $client_secret;

    private $send_format = 'json';
    private $get_format = 'json';

    /**
     * APIHelper constructor.
     * @param $api_domain
     * @param $server_token
     * @param $client_id
     * @param $client_secret
     */
    public function __construct($api_domain, $server_token, $client_id, $client_secret)
    {
        $this->api_domain = $api_domain;
        $this->server_token = $server_token;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        // Init curl
        $this->ch = curl_init();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        // Close curl
        curl_close($this->ch);
    }

    /**
     * Change formats of sending and receiving data
     *
     * @param string $send_format
     * @param string $get_format
     */
    public function setFormats($send_format = 'json', $get_format = 'json')
    {
        $this->send_format = $send_format;
        $this->get_format = $get_format;
    }

    /**
     * Returns user auth key
     * @return mixed
     */
    public function getKey()
    {
        return $this->auth_key;
    }

    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Creates API url from domain and path. Returns string if $return = true
     *
     * @param $path
     * @param string $v
     * @param bool $https
     * @param string $domain
     * @param bool $return
     * @return string
     */
    protected function construct_url($path, $v = '', $https = false, $domain = '', $return = false)
    {
        $url = ($https ? 'https' : 'http') . '://' . ($domain ? $domain : $this->api_domain) .
            ( $v ? '/v'.$v : '' ) . $path;
        if($return)
            return $url;
        else $this->url = $url;
    }

    /**
     * Encodes array xml or json (can be sent via POST)
     *
     * @param array $data
     * @return mixed|string
     */
    protected function encode($data = array())
    {
        switch ($this->send_format) {
            case 'xml':
                $root = key($data);
                if (count($data) == 1 && is_string($root)) {
                    $xml = new \SimpleXMLElement('<' . key($data) . '/>');
                    $this->array_to_xml($data[key($data)], $xml);
                    $result = $xml->asXML();
                } else {
                    new Exception('Wrong XML array format. Must be 1 root element with data array inside.');
                }
                break;
            case 'json':
                $result = json_encode($data);
                break;
            default:
                new Exception('Can\'t encode to this format');
                break;
        }
        return $result;
    }

    /**
     * Converts array to XML
     *
     * @param $array
     * @param $xml
     */
    private function array_to_xml($array, &$xml)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild("$key");
                    array_to_xml($value, $subnode);
                } else {
                    array_to_xml($value, $xml);
                }
            } else {
                $xml->addChild("$key", "$value");
            }
        }
    }

    /**
     * Decodes result to assoc array
     *
     * @param $data
     * @return mixed|\SimpleXMLElement
     */
    protected function decode($data)
    {
        if ($this->get_format == 'xml') {
            $decoded = simplexml_load_string($data);
            // Check if error included
            $this->error($decoded);
            return $decoded;
        } elseif ($this->get_format == 'json') {
            $decoded = json_decode($data);

            $this->error($decoded);
            return $decoded;
        } else {
            new Exception('Result format can\'t be decoded');
        }
    }

    /**
     * Executes request on link
     *
     * @param   string $url
     * @param   string $method
     * @param   array $fields
     * @return  string
     */
    public function request($url = '', $method = 'POST', $fields = array())
    {
        $curl_array = array(
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => ($method == 'POST'),
            CURLOPT_URL => $url ? $url : $this->url
        );
        if($method == 'POST') {
            $curl_array[CURLOPT_POSTFIELDS] = $this->encode($fields);
        } else {
            $curl_array[CURLOPT_URL] .= '?'.http_build_query($fields);
        }
        curl_setopt_array($this->ch, $curl_array);
        return $this->decode(curl_exec($this->ch));
    }

    /**
     * Check if there is an error in API return
     *
     * @param $data
     * @throws Exception
     */
    protected function error($data)
    {
        if(is_object($data)) {
            $message = (string)$data->message;
            $code = (string)$data->code;
            if ($message && $code) {
                $this->showApiError($message, $code);
            }
        } else {
            throw new Exception('API returned NULL result');
        }
    }

    /**
     * Shows API error
     *
     * @param $message
     * @param $code
     * @throws Exception
     */
    protected function showApiError($message, $code)
    {
        die(json_encode([
            'result' => 'error',
            'message' => $message
        ]));
    }
}