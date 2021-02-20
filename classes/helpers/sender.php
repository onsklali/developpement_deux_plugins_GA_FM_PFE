<?php
/**
 * 2016 Favizone Solutions Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Favizone Solutions Ltd <contact@favizone.com>
 * @copyright 2015-2016 Favizone Solutions Ltd
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Helper class for sending requests operations.
 */
class FavizoneSender
{
    /**
     * POST data .
     *
     * @param String $host.
     * @param String $path.
     * @param array()
     * @return array() the response data
     */
    public function postRequest($host, $path, $body = array())
    {
        $body = json_encode($body) ;
        if (function_exists('curl_exec')) {
            $return = $this->postByCURL($host . $path, $body) ;
        }
        if (isset($return) and $return) {
            return $return ;
        }
        $return = $this->postByFileGetContents($host, $path, $body) ;
        if (isset($return) and $return) {
            return $return ;
        }
        $tmp = $this->postByFSOCK($host, $path, $body) ;
        return $tmp ;
    }

    /**
     * POST using Curl extension
     *
     * @param String $url .
     * @param $body
     * @return bool|string () the response data
     */
    public function postByCURL($url, $body)
    {
        $ch = curl_init($url) ;
        if ($ch) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST") ;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body) ;
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ;
            curl_setopt($ch, CURLINFO_HEADER_OUT, true) ;
            curl_setopt($ch, CURLOPT_HTTPHEADER, //array(
                'Content-Type: application/json'
               //, 'Origin: '.$_SERVER['SERVER_NAME']//,
               // 'Content-Length: ' . Tools::strlen($body)
        //    )
        ) ;
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6) ;
           curl_setopt($ch, CURLOPT_TIMEOUT, 5) ;
            $result = curl_exec($ch) ;
            @curl_close($ch) ;
            return $result;
        }
        return false ;
    }

    /**
     * POST using fsocketopen
     *
     * @param String $url .
     * @param $path
     * @param array() $body the data to send.
     * @return array the response data
     */
    private function postByFSOCK($url, $path, $body)
    {
        $url1 = parse_url($url) ;
        $host = $url1['host'] ;
        $port = $url1['port'] ;
        $fp = @fsockopen(gethostbyname($host), $port, $errno, $errstr, 1000) ;
        if ($fp) {
            socket_set_timeout($fp, 6) ;
            $out = "POST ".$path." HTTP/1.0\r\n" ;
            $out .= "Host: ".$host."\r\n" ;
            //$out .= 'Content-Length: '.Tools::strlen($body)."\r\n" ;
            $out .= 'Accept : application/json'."\r\n" ;
            $out .= 'Origin : '.$_SERVER['SERVER_NAME']."\r\n" ;
            $out .= 'Content-Type : application/json'."\r\n" ;
            $out .= "Connection: Close\r\n\r\n" ;
            @fwrite($fp, $out.$body) ;
            $tmp = '' ;
            while (!feof($fp)) {
                $tmp .= trim(fgets($fp, 1024)) ;
            }
            $result = $tmp ;
            fclose($fp) ;
        }
        return (isset($result) ? $result : false) ;
    }
    /**
     * POST using file_get_contents
     *
     * @param array() $body he data to send.
     * @param String $host.
     * @return  array() the response data
     */
    private function postByFileGetContents($host, $path, $body)
    {
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => $body,
                'timeout' => 6,
                'header'=>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n".
                   // 'Content-Length: ' . Tools::strlen($body)."\r\n".
                    'Origin : '.$_SERVER['SERVER_NAME']."\r\n"
            )
        ) ;
        $context = stream_context_create($options) ;
        $result = @Tools::file_get_contents($host.$path, false, $context) ;
        if (!$result) {
            return false ;
        }
        return $result ;
    }
    private function favizone_post_by_curl($favizone_url, $favizone_body)
    {

        try {
            $favizone_response = wp_remote_post( $favizone_url, array(
                    'method' => 'POST',
                    'timeout' => 100,
                    'redirection' => 100,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' =>  array( 'Content-Type' => 'application/json' ),
                    'body' => $favizone_body,
                    'cookies' => array()
                )
            );
            if ( is_wp_error( $favizone_response ) ) {
                $favizone_error_message = $favizone_response->get_error_message();
                return "Something went wrong: $favizone_error_message";
            } else {
                return  wp_remote_retrieve_body( $favizone_response ) ;
            }
        } catch(Exception $e) {
            return sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage());
        }
    }
}
