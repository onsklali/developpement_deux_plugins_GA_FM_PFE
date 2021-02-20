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
 * Helper class for managing cart data.
 */
class FavizoneCartManager
{
    /**
     *  Constructor
     */
    public function __construct()
    {
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/sender.php') ;
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/common.php') ;
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/configuration.php') ;
        require_once(_PS_MODULE_DIR_.'favizone/classes/constants/api.php') ;
    }
    /**
     * Sending Cart data .
     *
     * @param params.
     */
    public function sendCartData($params)
    {
        $helper =  new FavizoneCommonHelper() ;
        $sessionIdentifier = $helper->generateSessionIdentifier($params) ;
        if (!isset($sessionIdentifier)) {
            return ;
        }

        $cookie = $params['cookie'] ;
        if (isset($params['cart'])) {
            $cart = $params['cart'] ;
            $products_id = array() ;
            $events = array();
            // Semaphore to avoid recursivity
            if ($cookie->__get('xlcn_consumable_adding') == 'true') {
                return ;
            }
            $cookie->__set('xlcn_consumable_adding', 'true') ;
            // Get current products
            $products = $cart->getProducts() ;
            foreach ($products as $product) {
                array_push($products_id, $product['id_product']) ;
            }
            // Compare with previous ones
            $prev_cookie = $cookie->__get('xlcn_consumable_cart') ;
            $cookie->__set('xlcn_consumable_cart', implode(',', $products_id)) ;
            $added = array_diff($products_id, explode(',', $prev_cookie)) ;
            $cookie->__set('xlcn_consumable_adding', false) ;
            try {
                if (count($added) > 0) {
                    //Tracking  event
                    foreach ($added as $product) {
                        $params["event_product"] = $product;
                        array_push($events, $helper->generateTrackEvent($params, "addToCart")) ;
                    }
                }
                $sender = new FavizoneSender() ;
                $api_data = new FavizoneApi() ;
                if (count($events)) {
                    $data_to_send =  array(
                        "key" => FavizoneConfiguration::get('FAVIZONE_AUTH_KEY'),
                        "events" => $events,
                        "session" => $sessionIdentifier,
                        "cart" => $products_id
                    );
                    $sender->postRequest($api_data->getHost(), $api_data->getSendEventPath(), $data_to_send);
                }
            } catch (Exception $e) {
            }
        }
    }
}
