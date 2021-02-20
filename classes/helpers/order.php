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
 * Helper class for managing order data.
 */
class FavizoneOrderManager
{
    /**
     * @var int the amount of items to fetch.
     */
    private $limit = 100;

    /**
     * @var int the offset of items to fetch.
     */
    private $offset = 0;

    /**
     *  Constructor
     */
    public function __construct()
    {
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/sender.php');
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/common.php');
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/configuration.php');
        require_once(_PS_MODULE_DIR_.'favizone/classes/model/order.php');
        require_once(_PS_MODULE_DIR_.'favizone/classes/constants/api.php');
    }

    /**
     * Sending Order's data .
     *
     * @param $context
     * @param $params
     */
    public function sendToOrderData($context, $params)
    {
        $helper =  new FavizoneCommonHelper();
        $version = $helper->getTestingVersion();
        if ($params['cookie']->__get('favizone_connection_identifier_'.$context->shop->id.'_'.$context->language->id)) {
            $session_identifier = $params['cookie']->__get(
                'favizone_connection_identifier_'.$context->shop->id.'_'
                .$context->language->id
            );
        }

        //get the Order object
        $order_data = $params['order'];
        $id_order = (int)$order_data->id;
        $order= new Order($id_order);

        if (!isset($session_identifier)) {
            $session_identifier = $order->id_customer;
        }

        //Event Tracker
        $events = array();
        $event = $version;
        $event .= " ".$session_identifier;
        $event .= " confirm";
        $cms_version = FavizoneCommonHelper::prestaShopVersion();
        $products = $order->getProducts();
        $cart = $params['cart'] ;
        
        foreach ($products as $product) {
            if ($cms_version == '1.4') {
                $unit_price = $product['product_price_wt'] ;
            } else {
                $unit_price = $product['unit_price_tax_incl'] ;
            }
            array_push(
                $events,
                $event." ".$product['product_id']." ".$unit_price." ". $product['product_quantity']
            );
        }
        try {
            $sender = new FavizoneSender();
            $api_data = new FavizoneApi();
            $favizone_customer = new FavizoneCustomer();
            $custom_data = array_merge(
                $favizone_customer->loadCustomerDataById($context, $order->id_customer, $params),
                array('id_cart'=> $cart->id,'id_order' => $id_order)
            );
            $data_to_send =  array(
                "key" => FavizoneConfiguration::get('FAVIZONE_AUTH_KEY', $context->language->id),
                "events" => $events,
                "session" => $session_identifier,
                "custom_event_value"=>$custom_data
            );
            $sender->postRequest($api_data->getHost(), $api_data->getSendEventPath(), $data_to_send);
        } catch (Exception $e) {
        }
    }

    /**
     * Prepares the old orders data , this method is called when initializing data in Favizone .
     *
     * @param $context
     * @param int $id_lang language identifier
     */
    public function initOrders($context, $id_lang, $auth_key = null)
    {
        $count_orders = $this->getCountOrders($context, $id_lang);
        $end = false;
        /** Sending paginated products data **/
        while ($this->offset <= $count_orders && !$end) {
            $cms_orders= $this->getOrderIds($context, $id_lang);
            $favizone_orders = array();
            foreach ($cms_orders as $id_order) {
                $order = new Order($id_order);
                if (!Validate::isLoadedObject($order)) {
                    continue;
                }
                $favizone_order = new FavizoneOrder();
                $favizone_order_data = $favizone_order->loadOrderData($order);
                $favizone_orders = array_merge($favizone_orders, $favizone_order_data);
                $order = null;
            }
            $this->offset = $this->offset + $this->limit;
            if ($this->offset>$count_orders && $end == false) {
                $this->offset = $count_orders;
                $end = true;
            }
            if (count($favizone_orders)) {
                $this -> sendInitOrders($favizone_orders, $id_lang, $auth_key);
            }
        }
    }

    /**
     *  Sends the old orders data , this method is called when initializing data in Favizone .
     *
     * @param Array $orders
     * @param int $id_lang language identifier
     */
    private function sendInitOrders($orders, $id_lang, $auth_key = null)
    {
        $sender = new FavizoneSender();
        $api_data = new FavizoneApi();
        if ($auth_key == null) {
            $auth_key = FavizoneConfiguration::get('FAVIZONE_AUTH_KEY', $id_lang) ;
        }
        $data_to_send =  array("key" => $auth_key, "orders" => $orders);
        $sender->postRequest($api_data->getHost(), $api_data->getInitOrderPath(), $data_to_send);
    }

    /**
     * Returns a list of all order ids with limit and offset applied.
     *
     * @param  $context
     * @param  int $id_lang language identifier
     * @return array the order id list.
     */
    protected function getOrderIds($context, $id_lang)
    {
        $version = FavizoneCommonHelper::prestaShopVersion();
        if ($version > '1.5') {
            $where = strtr(
                '`id_shop_group` = {g} AND `id_shop` = {s} AND `id_lang` = {l}',
                array(
                    '{g}' => pSQL($context->shop->id_shop_group),
                    '{s}' => pSQL($context->shop->id),
                    '{l}' => (int)$id_lang,
                )
            );
        } else {
            $where = strtr(
                '`id_lang` = {l}',
                array(
                    '{l}' => (int)$id_lang,
                )
            );
        }

        $sql = sprintf(
            '
                SELECT id_order
                FROM %sorders
                WHERE %s
                LIMIT %d
                OFFSET %d
            ',
            _DB_PREFIX_,
            $where,
            $this->limit,
            $this->offset
        );

        $rows = Db::getInstance()->executeS($sql);
        $order_ids = array();
        foreach ($rows as &$row) {
            $order_ids[] = (int)$row['id_order'];
        }
        return $order_ids;
    }

    /**
     * Returns the number of current orders created.
     *
     * @param  $context
     * @param  int $id_lang language identifier
     * @return array the order id list.
     */
    protected function getCountOrders($context, $id_lang)
    {
        $version = FavizoneCommonHelper::prestaShopVersion();
        if ($version > '1.5') {
            $where = strtr(
                '`id_shop_group` = {g} AND `id_shop` = {s} AND `id_lang` = {l}',
                array(
                    '{g}' => pSQL($context->shop->id_shop_group),
                    '{s}' => pSQL($context->shop->id),
                    '{l}' => (int)$id_lang,
                )
            );
        } else {
            $where = strtr(
                '`id_lang` = {l}',
                array(
                    '{l}' => (int)$id_lang,
                )
            );
        }
        $sql = sprintf(
            '
                SELECT id_order
                FROM %sorders
                WHERE %s
            ',
            _DB_PREFIX_,
            $where
        );

        $rows = Db::getInstance()->getRow($sql);

        return count($rows);
    }
    public function getOrderStates($id_Lang)
    {

        $sql = "SELECT name
			    FROM "._DB_PREFIX_."order_state_lang
			    WHERE id_lang = ".(int)$id_Lang;

        $rows = Db::getInstance()->executeS($sql);
        foreach ($rows as $row) {
            $order_states[] = $row['name'];
        }
        return $order_states;
    }
    public function getProductIds()
    {
        $product_ids = array();
        $sql = "
            SELECT id_product
			FROM "._DB_PREFIX_."product
            WHERE active = 1 AND available_for_order = 1
            LIMIT ".$this->limit.
            " OFFSET ".$this->offset ;

        $rows = Db::getInstance()->executeS($sql);
        foreach ($rows as $row) {
            $product_ids[] = (int)$row['id_product'];
        }
        return $product_ids;
    }

    /**
     * @return int
     */
    public function getConversellOrderstate()
    {
        $states=["Failed","Delivred","Attemted Delivery","In Transit",
                 "Out for delivery","Shipped","Ready for shipment","Canceled","Confirmed"];
        return $states;
    }
}
