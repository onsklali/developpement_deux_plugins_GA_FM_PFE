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
class FavizoneProductManager
{
    /**
     *  Constructor
     */
    public function __construct()
    {
        require_once(_PS_MODULE_DIR_.'favizone/classes/model/productupdate.php');
        require_once(_PS_MODULE_DIR_.'favizone/classes/model/product.php');
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/sender.php');
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/common.php');
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/configuration.php');
        require_once(_PS_MODULE_DIR_.'favizone/classes/constants/api.php');
    }
    /**
     * @var int the amount of items to fetch.
     */
    public $limit = 100;

    /**
     * @var int the offset of items to fetch.
     */
    public $offset = 0;

    /**
     * Init products data in Favizone.
     *
     * @param $context
     * @param int $id_language
     * @return string
     */
    public function initTaggingProductData($context, $id_language, $auth_key = null)
    {
        $init_done = false;
        $number_products= $this->getCountAvailableProducts();
        $favizone_product_tagger = new FavizoneTaggingUpdateProduct();
       
        /** Sending paginated products data **/
        while ($this->offset <= $number_products && $init_done == false) {
            $products_collection = array();
            foreach ($this->getProductIds() as $id_product) {
                $product = new Product($id_product, true, $id_language, $context->shop->id);
                if (Validate::isLoadedObject($product)) {
                    $favizone_product= $favizone_product_tagger->loadProductData($context, $product, $id_language);
                    array_push($products_collection, $favizone_product);
                }
                $product = null;
            }
            $this->offset = $this->offset + $this->limit;
            if ($this->offset > $number_products) {
                $init_done = true;
            }
            if (count($products_collection)>0) {
                $this -> sendInitProductData($products_collection, $init_done, $id_language, $auth_key);
            }
        }

        return "done";
    }

    /**
     * Prepares updated product data to be sent to Favizone.
     *
     * @param $context
     * @param $id_product
     * @param $operation_key
     */
    public function updateTaggingProductData($context, $id_product, $operation_key)
    {
        //updates product for every language of the current context
        $shops = FavizoneCommonHelper::getContextShops();
        foreach ($shops as $shop) {
            $id_shop =  (int)$shop['id_shop'];
            $id_shop_group = (int)$shop['id_shop_group'];
            $languages = LanguageCore::getLanguages(true, $id_shop);
            $favizone_product_tagger1 = new FavizoneTaggingUpdateProduct();

            foreach ($languages as $language) {
                $id_language = (int)$language['id_lang'];
                $auth_key = FavizoneConfiguration::getValue(
                    'FAVIZONE_AUTH_KEY',
                    $id_language,
                    $id_shop,
                    $id_shop_group
                );
                if ($auth_key) {
                    switch ($operation_key) {
                        case "delete":
                            $this -> sendDeleteProductData($id_product, $auth_key);
                            break;
                    }
                }
            }
        }
    }

    /**
     * Returns a list of all active product ids with limit and offset applied.
     *
     * @return array the product id list.
     */
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
     * Returns the number of available products.
     *
     * @return integer of available product's number.
     */
    public function getCountAvailableProducts()
    {
        $sql = "SELECT COUNT(id_product) as total
			    FROM "._DB_PREFIX_."product
			    WHERE active = 1 AND available_for_order = 1";

        $count = Db::getInstance()->executeS($sql);
        return (int)$count[0]['total'];
    }

    /**
     * Returns the product id list.
     *
     * @param int $id_product
     * @return array
     */
    public function getProductById($id_product)
    {
        $sql = "SELECT id_product
			    FROM "._DB_PREFIX_."product
			    WHERE id_product = ".(int)$id_product;

        $data = Db::getInstance()->executeS($sql);
        return $data;
    }

    /**
     * sends products data to Favizone.
     *
     * @param $products
     * @param bool|false $init_done
     * @param null $id_language
     */
//    public function sendInitProductData($products, $init_done = false, $id_language = null, $auth_key = null)
//    {
//        $sender = new FavizoneSender();
//        $api_data = new FavizoneApi();
//        if ($auth_key == null) {
//            $auth_key = FavizoneConfiguration::get('FAVIZONE_AUTH_KEY', $id_language) ;
//        }
//        $data_to_send =  array("key" => $auth_key,
//                                "init_done" => $init_done,
//                                "products" => json_encode($products)
//                                );
//        $sender->postRequest($api_data->getHost(), $api_data->getInitProductUrl(), $data_to_send);
//    }

    /**
     * sends updated product data to Favizone.
     *
     * @param $product
     * @param $auth_key
     */
    public function sendUpdateProductData($product, $auth_key)
    {
        $sender = new FavizoneSender();
        $api_data = new FavizoneApi();
        $data_to_send =  array("key" => $auth_key, "product" => json_encode($product));
        $sender->postRequest($api_data->getHost(), $api_data->getUpdateProductUrl(), $data_to_send);
    }

    /**
     * Sends the new product data to Favizone.
     *
     * @param $product
     * @param $auth_key
     */
    public function sendAddProductData($product, $auth_key)
    {
        $sender = new FavizoneSender();
        $api_data = new FavizoneApi();
        $data_to_send =  array("key" => $auth_key, "product" => json_encode($product));
        $sender->postRequest($api_data->getHost(), $api_data->getAddProductUrl(), $data_to_send);
    }

    /**
     * Send the product identifier to remove .
     *
     * @param int $id_product
     * @param String $auth_key
     */
    public function sendDeleteProductData($id_product, $auth_key)
    {
        $sender = new FavizoneSender();
        $api_data = new FavizoneApi();
        $data_to_send =  array("key" => $auth_key, "product" => $id_product);
        $sender->postRequest($api_data->getHost(), $api_data->getDeleteProductUrl(), $data_to_send);
    }

    /**
     * Checks if products data are already initialized in Favizone with the given account.
     *
     * @param int $id_language
     * @return array
     */
    public function sendCheckInitProduct($id_language = null)
    {
        $sender = new FavizoneSender();
        $api_data = new FavizoneApi();
        $favizone_helper = new FavizoneCommonHelper();
        $data_to_send =  array("key" => FavizoneConfiguration::get('FAVIZONE_AUTH_KEY', $id_language)
            ,"cms_version"=>$favizone_helper->prestaShopVersion(),"cms_name"=>"prestashop");
        $result = $sender->postRequest($api_data->getHost(), $api_data->getCheckInitUrl(), $data_to_send);
        return $result ;
    }
}
