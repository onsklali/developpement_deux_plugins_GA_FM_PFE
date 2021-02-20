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
 * Model for tagging order data.
 */
class FavizoneOrder
{
    public function loadOrderData($order)
    {
        $orders_events = array();
        $products = $order->getProducts();
        foreach ($products as $product) {
            $order_event= strtotime($order->date_add)." favizone_xxx ".$order->id_customer." confirm ".
                $product['product_id']." ".$product['unit_price_tax_incl']." ".$product['product_quantity'];
            array_push($orders_events, $order_event);
        }
        return $orders_events;
    }
}
