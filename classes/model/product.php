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
 * Model for tagging product data.
 */
class FavizoneTaggingProduct
{
    const  DATE_FORMAT = "Y-m-d H:i:s" ;

    /**
     *  Constructor
     */
    public function __construct()
    {
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/configuration.php') ;
    }

    /**
     * Getting product's data.
     *
     * @param Context $context
     * @param Product $product
     * @param null $id_language
     * @param null $id_shop
     * @return array
     */
    public function loadProductData($context, Product $product, $id_language = null, $id_shop = null)
    {
        try {
            $ps_version = FavizoneCommonHelper::prestaShopVersion() ;
            if ($ps_version == '1.4') {
                return $this->loadProductDataPS14($context, $product, $id_language, $id_shop) ;
            } else {
                return $this->loadProductDataPSNewer($context, $product, $id_language, $id_shop) ;
            }
        } catch (Exception $e) {
            return  array() ;
        }
    }

    /**
     * @param $context
     * @param Product $product
     * @param null $id_language
     * @param null $id_shop
     * @return array
     */
    public function loadProductDataPS14($context, Product $product, $id_language = null, $id_shop = null)
    {
        try {
            $product_data = array() ;
            $id_product =(int)$product->id ;
            if (!is_null($id_language)) {
                $id_lang = $id_language ;
            } else {
                $id_lang = $context->language->id ;
            }
            if (is_null($id_shop)) {
                $id_shop =  (int) $context->shop->id ;
            }
            /** Default Language **/
            $language = Language::getLanguage($id_lang) ;
            $iso_lang_code = "fr" ;
            if ($language) {
                $iso_lang_code = $language["iso_code"] ;
            }
            $product_data["lang"] = $iso_lang_code ;
            /** Product's description **/
            $product_data["description"] = strip_tags($product->description) ;
            /** Product's short description **/
            $product_data["shortDescription"] = strip_tags($product->description_short) ;

            /** Product's identifier **/
            $product_data["identifier"] = ''.$id_product ;
            /** Product's reference **/
            $product_data["reference"] = $product->reference ;
            /** Product's title **/
            $product_data["title"] =  $this->getProductName($id_product, $id_lang) ;
            /**  Product's categories **/
            $categoriesData = $this->buildCategoriesStringFromObjects($product->getWsCategories(), $id_lang);
            $product_data['categoriesNames'] = $categoriesData['categoriesNames'];
            $product_data['categories'] = $categoriesData['categories'] ;

            /**  Product's tags **/
            $product_data['tags'] = array() ;
            $product_keywords = Tag::getProductTags($id_product) ;
            if (is_array($product_keywords)) {
                $keywords_array = array() ;
                foreach ($product_keywords as $keyword) {
                    $keywords_array = array_merge($keywords_array, $keyword) ;
                }
                $product_data['tags'] = $keywords_array ;
            }
            /**  Product's facets **/
            $product_features = Product::getFrontFeaturesStatic($id_lang, $id_product) ;
            $features_array = array() ;
            foreach ($product_features as $feature) {
                $features_array[$feature['name']] = array($feature['value']) ;
            }
            $product_attributes = $this->getAttributesInformationsByProduct($id_product, $id_lang) ;
            $product_data['hasDeclination'] = false;
            if (count($product_attributes)) {
                $product_data['hasDeclination'] = true;
            }
            foreach ($product_attributes as $attribute) {
                if (array_key_exists($attribute['group'], $features_array)) {
                    array_push($features_array[$attribute['group']], $attribute['attribute']) ;
                } else {
                    if (gettype($attribute['attribute']) == "string") {
                        $features_array[$attribute['group']] = array($attribute['attribute']) ;
                    }
                }
            }
            $facets = array() ;
            foreach ($features_array as $key => $value) {
                if (sizeof($value) == 1) {
                    $facets[$key] = $value[0] ;
                } else {
                    $facets[$key] = $value ;
                }
            }
            if (!empty($facets)) {
                $product_data['facets'] = $facets ;
            }
            /**  If product is in stock **/
            $product_data['stock'] = false ;
            if ($product->quantity > 0) {
                $product_data['stock'] = true ;
            }
            $product_data['quantity'] = (int)$product->quantity;
            /**  If product is available_for_order **/
            $product_data["available_for_order"] = $product->available_for_order ;
            $product_data["active"] = $product->active == 1 ? true : false ;
            if (gettype($product_data["available_for_order"]) == "string") {
                $product_data["available_for_order"] = $product_data["available_for_order"] == '1' ? true : false ;
            }
            /**  Product's brand **/
            if ($product->manufacturer_name !=null
                && $product->manufacturer_name != "false"
                && $product->manufacturer_name != false) {
                $product_data["brand"] = $product->manufacturer_name ;
            }
            /**  Product's price **/
            if ($context->currency === null) {
                $product_data["price"] = Tools::ps_round($product->getPrice(true, null, 2), 2);
            } else {
                $product_data["price"] = Tools::ps_round(Tools::convertPrice(
                    $product->getPrice(true, null, 2),
                    $context->currency->id,
                    false
                ), 2);
            }
            /**  Product's purchase price **/
            $product_data["wholesale_price"] = Tools::ps_round($product->wholesale_price, 2) ;
            /**  Product's  Currency **/
            $product_data["currency"] = Currency::getCurrencyInstance(
                Configuration::get('PS_CURRENCY_DEFAULT')
            )->iso_code ;
            /**  Product's  url **/
            $linkInstance = new Link() ;
            $rewrite= (int)(Configuration::get('PS_REWRITING_SETTINGS')) ;
            $product_data["id_shop"] =  $id_shop ;
            $url = (string) $linkInstance->getProductLink(
                $product,
                null,
                $product->id_category_default,
                null,
                $id_language
            ) ;
            if ($rewrite == 0) {
                if (strpos($url, 'id_lang') == false) {
                    $url = $url."&id_lang=".$id_language ;
                }
            }
            $product_data["url"] = $url ;
            /**  Product's  cover image  **/
            $productCoverIdentifier = Product::getCover($product->id) ;
            $productCoverIdentifier = $productCoverIdentifier['id_image'] ;
            if ($productCoverIdentifier) {
                $image_id = $id_product . '-' . $productCoverIdentifier ;
                $image_size = "large" ;
                $productCoverLink = $linkInstance->getImageLink($product->link_rewrite, $image_id, $image_size) ;
                $product_data['cover'] = $productCoverLink ;
            }

            /**  Product's Home cover image  **/
            $productHomeCoverIdentifier = Product::getCover($product->id) ;
            $productHomeCoverIdentifier = $productHomeCoverIdentifier['id_image'] ;
            if ($productHomeCoverIdentifier) {
                $image_id = $id_product . '-' . $productHomeCoverIdentifier ;
                $image_size = "home" ;
                $productHomeCoverLink = $linkInstance->getImageLink($product->link_rewrite, $image_id, $image_size) ;
                $product_data['home_cover'] = $productHomeCoverLink ;
            }
            /**  Product's published date **/
            $tz = new DateTimeZone(Configuration::get('PS_TIMEZONE')) ;
            $utcTz = new DateTimeZone("UTC") ;
            $new_from_date = new DateTime($product->date_add, $utcTz) ;
            $product_data['published_date'] = $new_from_date->format(self::DATE_FORMAT) ;
            $new_to_date = new DateTime($product->date_add, $tz) ;
            $new_to_date->add(new DateInterval('P'. Configuration::get('PS_NB_DAYS_NEW_PRODUCT') .'D')) ;
            /**  Is new product **/
            if ($new_to_date > new DateTime()) {
                // Still new product
                $product_data['isNew'] = true ;
                $new_to_date->setTimeZone($utcTz) ;
                $product_data['isNew_from_date'] = $new_from_date->format(self::DATE_FORMAT) ;
                $product_data['isNew_to_date'] = $new_to_date->format(self::DATE_FORMAT) ;
            }
            /**  Product's Reduction **/
            if (isset($product->specificPrice) && $product->specificPrice) {
                $specificPrice = $product->specificPrice ;
                $from_date = new DateTime($specificPrice['from'], $tz) ;
                $to_date = new DateTime($specificPrice['to'], $tz) ;
                $verif_date = false ;
                $now_date = new DateTime() ;
                if (($to_date == new DateTime("0000-00-00 00:00:00", $tz)
                     && $from_date == new DateTime("0000-00-00 00:00:00", $tz))
                     || $now_date >= new DateTime($specificPrice['from'], $tz)
                     && $now_date <= new DateTime($specificPrice['to'], $tz)
                     ||( $now_date >= new DateTime($specificPrice['from'], $tz)
                     && $to_date == new DateTime("0000-00-00 00:00:00", $tz))
                ) {
                    $verif_date = true ;
                }
                if ($verif_date) {
                    $product_data["isReduced"] = true ;
                    $product_data['reduction'] = Tools::ps_round($specificPrice['reduction'], 2) ;
                    $product_data['reduction_type'] = $specificPrice['reduction_type'] ;
                    if ($to_date != new DateTime("0000-00-00 00:00:00", $tz)) {
                        $to_date->setTimeZone($utcTz) ;
                        $product_data['reduction_expiry_date'] = $to_date->format(self::DATE_FORMAT) ;
                    }
                    if (isset($specificPrice['reduction_tax']) && (int)$specificPrice['reduction_tax'] == 1) {
                        $product_data['reduction_tax'] = true ; // tax includes*d
                    } else {
                        $product_data['reduction_tax'] = false ; // tax xcluded
                    }
                    if ($context->currency === null) {
                        $product_data["price_without_reduction"] = Tools::ps_round($product->getPrice(
                            true,
                            null,
                            2,
                            null,
                            false,
                            false,
                            1
                        ), 2);
                    } else {
                        $product_data['price_without_reduction'] =
                        Tools::ps_round(
                            Tools::convertPrice(
                                $product->getPrice(
                                    true,
                                    null,
                                    2,
                                    null,
                                    false,
                                    false,
                                    1
                                ),
                                $context->currency->id,
                                false
                            ),
                            2
                        );
                    }
                }
            }
            return $product_data ;
        } catch (Exception $e) {
            return  array() ;
        }
    }

    /**
     * @param $context
     * @param Product $product
     * @param null $id_language
     * @param null $id_shop
     * @return array
     */
    public function loadProductDataPSNewer($context, Product $product, $id_language = null, $id_shop = null)
    {
        try {
            $ps_version = FavizoneCommonHelper::prestaShopVersion() ;
            $product_data = array() ;
            $id_product =(int)$product->id ;
            if (!is_null($id_language)) {
                $id_lang = $id_language ;
            } else {
                $id_lang = $context->language->id ;
            }
            if (is_null($id_shop)) {
                $id_shop = (int) $context->shop->id ;
            }
            /** Default Language **/
            $language = Language::getLanguage($id_lang) ;
            $iso_lang_code = "fr" ;
            if ($language) {
                $iso_lang_code = $language["iso_code"] ;
            }
            /** Product's description **/
            $product_data["description"] = strip_tags($product->description) ;
            /** Product's short description **/
            $product_data["shortDescription"] = strip_tags($product->description_short) ;


            $product_data["lang"] = $iso_lang_code ;
            /** Product's identifier **/
            $product_data["identifier"] = ''.$id_product ;
            /** Product's reference **/
            $product_data["reference"] = $product->reference ;
            /** Product's name **/
            $product_data["title"] =  $product->name ;
            /**  Product's categories **/
            $categoriesData = $this->buildCategoriesString($product->getCategories(), $id_lang);
            $product_data['categoriesNames'] = $categoriesData['categoriesNames'];
            $product_data['categories'] = $categoriesData['categories'] ;
            /**  Product's tags **/
            $product_data['tags'] = array() ;
            $product_keywords = Tag::getProductTags($id_product) ;
            if (is_array($product_keywords)) {
                $keywords_array = array() ;
                foreach ($product_keywords as $keyword) {
                    $keywords_array = array_merge($keywords_array, $keyword) ;
                }
                $product_data['tags'] = $keywords_array ;
            }
            /**  Product's facets **/
            $product_features = Product::getFrontFeaturesStatic($id_lang, $id_product) ;
            $features_array = array() ;
            foreach ($product_features as $feature) {
                $features_array[$feature['name']] = array($feature['value']) ;
            }
            $product_attributes = Product::getAttributesInformationsByProduct($id_product) ;
            $product_data['hasDeclination'] = false;
            if (count($product_attributes)) {
                $product_data['hasDeclination'] = true;
            }
            foreach ($product_attributes as $attribute) {
                if (array_key_exists($attribute['group'], $features_array)) {
                    array_push($features_array[$attribute['group']], $attribute['attribute']) ;
                } else {
                    if (gettype($attribute['attribute']) == "string") {
                        $features_array[$attribute['group']] = array($attribute['attribute']) ;
                    }
                }
            }
            $facets = array() ;
            foreach ($features_array as $key => $value) {
                if (sizeof($value) == 1) {
                    $facets[$key] = $value[0] ;
                } else {
                    $facets[$key] = $value ;
                }
            }
            if (!empty($facets)) {
                $product_data['facets'] = $facets ;
            }
               $product_data['id_product_attribute'] = Product::getDefaultAttribute($id_product,0 ,false);
            /**  If product is in stock **/
            $product_data['stock'] = false ;
            if ($product->quantity > 0) {
                $product_data['stock'] = true ;
            }
            $product_data['quantity'] = (int)$product->quantity;
            /**  If product is available_for_order **/
            $product_data["available_for_order"] = $product->available_for_order ;
            $product_data["active"] = $product->active == 1 ? true : false ;
            if (gettype($product_data["available_for_order"]) == "string") {
                $product_data["available_for_order"] = $product_data["available_for_order"] == '1' ? true : false ;
            }
            /**  Product's brand **/
            if ($product->manufacturer_name !=null
                && $product->manufacturer_name != "false"
                && $product->manufacturer_name != false) {
                $product_data["brand"] = $product->manufacturer_name ;
            }
            /**  Product's price **/
            if ($context->currency === null) {
                $product_data["price"] = Tools::ps_round($product->getPrice(true, null, 2), 2);
            } else {
                $product_data["price"] = Tools::ps_round(Tools::convertPrice(
                    $product->getPrice(true, null, 2),
                    $context->currency->id,
                    false
                ), 2);
            }
            /**  Product's  Currency **/
            $product_data["currency"] = Currency::getDefaultCurrency()->iso_code ;
            
            
            /**  Product's purchase price **/
            $product_data["wholesale_price"] = Tools::ps_round($product->wholesale_price, 2) ;
            /**  Product's  url **/
            $product_data["id_shop"] =  $id_shop ;
            $product_data["url"] = (string)  $context->link->getProductLink(
                $product,
                null,
                null,
                null,
                $id_language,
                $id_shop,
                0,
                false,
                false
            ) ;
            /**  Product's  cover image  **/
            $linkInstance = new Link() ;
            $productCoverIdentifier = Product::getCover($product->id, $context) ;
            $productCoverIdentifier = $productCoverIdentifier['id_image'] ;
            if ($productCoverIdentifier) {
                switch ($ps_version) {
                    case "1.5":
                        $image_id = $id_product . '-' . $productCoverIdentifier ;
                        $image_size = "large" ;
                        break ;
                    case "1.6":
                        $image_id = $id_product . '-' . $productCoverIdentifier ;
                        $image_size = ImageType::getFormatedName('large') ;
                        break;
                    case "1.7":
                        $image_id = $id_product . '-' . $productCoverIdentifier ;
                        $image_size = ImageType::getFormatedName('large') ;
                    break;
                }
                $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
                $productCoverLink =  $protocol_link.$linkInstance->getImageLink(
                    $product->link_rewrite,
                    $image_id,
                    $image_size
                ) ;
                if (strpos($productCoverLink, _PS_BASE_URL_.__PS_BASE_URI__)>=0) {
                    $productCoverLink = str_replace(
                        _PS_BASE_URL_.__PS_BASE_URI__,
                        $context->shop->getBaseURL(),
                        $productCoverLink
                    ) ;
                }
                $product_data['cover'] = $productCoverLink ;
            }

              /**  Product's home cover image  **/
            $linkInstance = new Link() ;
            $productHomeCoverIdentifier = Product::getCover($product->id, $context) ;
            $productHomeCoverIdentifier = $productHomeCoverIdentifier['id_image'] ;
            if ($productHomeCoverIdentifier) {
                switch ($ps_version) {
                    case "1.5":
                        $image_id = $id_product . '-' . $productHomeCoverIdentifier ;
                        $image_size = "home" ;
                        break ;
                    case "1.6":
                        $image_id = $id_product . '-' . $productHomeCoverIdentifier ;
                        $image_size = ImageType::getFormatedName('home') ;
                        break;
                    case "1.7":
                        $image_id = $id_product . '-' . $productHomeCoverIdentifier ;
                        $image_size = ImageType::getFormatedName('home') ;
                        break;
                }
                $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
                $productHomeCoverLink =  $protocol_link.$linkInstance->getImageLink(
                    $product->link_rewrite,
                    $image_id,
                    $image_size
                ) ;
                if (strpos($productHomeCoverLink, _PS_BASE_URL_.__PS_BASE_URI__)>=0) {
                    $productHomeCoverLink = str_replace(
                        _PS_BASE_URL_.__PS_BASE_URI__,
                        $context->shop->getBaseURL(),
                        $productHomeCoverLink
                    ) ;
                }
                $product_data['home_cover'] = $productHomeCoverLink ;
            }
            /**  Product's published date **/
            $tz = new DateTimeZone(Configuration::get('PS_TIMEZONE')) ;
            $utcTz = new DateTimeZone("UTC") ;
            $new_from_date = new DateTime($product->date_add, $utcTz) ;
            $product_data['published_date'] = $new_from_date->format(self::DATE_FORMAT) ;
            $new_to_date = new DateTime($product->date_add, $tz) ;
            $new_to_date->add(new DateInterval('P'. Configuration::get('PS_NB_DAYS_NEW_PRODUCT') .'D')) ;

            /**  Is new product **/
            if ($ps_version < 1.7) {
            if ($new_to_date > new DateTime()) {
                // Still new product
                   
                $product_data['isNew'] = true ;
                $new_to_date->setTimeZone($utcTz) ;
                $product_data['isNew_from_date'] = $new_from_date->format(self::DATE_FORMAT) ;
                $product_data['isNew_to_date'] = $new_to_date->format(self::DATE_FORMAT) ;
 
                  
                }
                
            }
            else{
                if ($product->condition=="new") {
                      $product_data['isNew']=1;
                } else
                $product_data['isNew']=0;
             
            }
            /**  Product's Reduction **/
            if (isset($product->specificPrice) && $product->specificPrice) {
                $specificPrice = $product->specificPrice ;
                $from_date = new DateTime($specificPrice['from'], $tz) ;
                $to_date = new DateTime($specificPrice['to'], $tz) ;
                $verif_date = false ;
                $now_date = new DateTime() ;
                if (($to_date == new DateTime("0000-00-00 00:00:00", $tz)
                    && $from_date == new DateTime("0000-00-00 00:00:00", $tz))
                    || $now_date >= new DateTime($specificPrice['from'], $tz)
                    && $now_date <= new DateTime($specificPrice['to'], $tz)
                    ||( $now_date >= new DateTime($specificPrice['from'], $tz)
                     && $to_date == new DateTime("0000-00-00 00:00:00", $tz))) {
                    $verif_date = true ;
                }

                if ($verif_date) {
                    $product_data["isReduced"] = true ;
                    $product_data['reduction'] = Tools::ps_round($specificPrice['reduction'], 2) ;
                    $product_data['reduction_type'] = $specificPrice['reduction_type'] ;
                    if ($to_date != new DateTime("0000-00-00 00:00:00", $tz)) {
                        $to_date->setTimeZone($utcTz) ;
                        $product_data['reduction_expiry_date'] = $to_date->format(self::DATE_FORMAT) ;
                    }
                    if (isset($specificPrice['reduction_tax']) && (int)$specificPrice['reduction_tax'] == 1) {
                        $product_data['reduction_tax'] = true ; // tax includes
                    } else {
                        $product_data['reduction_tax'] = false ; // tax excluded
                    }
                    if ($context->currency === null) {
                        $product_data["price_without_reduction"] = Tools::ps_round($product->getPrice(
                            true,
                            null,
                            2,
                            null,
                            false,
                            false,
                            1
                        ), 2);
                    } else {
                        $product_data['price_without_reduction'] =
                        Tools::ps_round(
                            Tools::convertPrice(
                                $product->getPrice(
                                    true,
                                    null,
                                    2,
                                    null,
                                    false,
                                    false,
                                    1
                                ),
                                $context->currency->id,
                                false
                            ),
                            2
                        );
                    }
                }
            }
            return $product_data ;
        } catch (Exception $e) {
            return  array() ;
        }
    }
    /**
     * Gets the name of a given product, in the given lang
     *
     * @param int $id_product
     * @param int $id_lang
     * @return string
     */
    public function getProductName($id_product, $id_lang)
    {
        $sql = "
            SELECT name
            FROM "._DB_PREFIX_."product_lang
            WHERE id_product = ".(int)$id_product." AND id_lang =".(int)$id_lang ;
        $row = Db::getInstance()->executeS($sql) ;
        if (count($row)>0  && $row[0]['name']) {
            return $row[0]['name'] ;
        }
        return "" ;
    }

    public static function getProductCategoriesFull($id_lang, $id_product = '')
    {
        $ret = array() ;
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT cp.`id_category`, cl.`name`, cl.`link_rewrite` FROM `'._DB_PREFIX_.'category_product` cp
            LEFT JOIN `'._DB_PREFIX_.'category` c ON (c.id_category = cp.id_category)
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (cp.`id_category` = cl.`id_category`'.')
            '.'WHERE cp.`id_product` = '.(int)$id_product.'AND cl.`id_lang` = '.(int)$id_lang
        ) ;

        foreach ($row as $val) {
            $ret[$val['id_category']] = $val ;
        }
        return $ret ;
    }

    /**
     * @param int $id_product
     * @param $id_lang
     * @return array
     */
    public static function getAttributesInformationsByProduct($id_product, $id_lang)
    {
        $result = array() ;
        // if blocklayered module is installed we check if user has set custom attribute name
        if (Module::isInstalled('blocklayered')) {
            $nb_custom_values = Db::getInstance()->executeS('
            SELECT DISTINCT la.`id_attribute`, la.`url_name` as `attribute`
            FROM `'._DB_PREFIX_.'attribute` a
            LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
                ON (a.`id_attribute` = pac.`id_attribute`)
            LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
                ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
            '.'
            LEFT JOIN `'._DB_PREFIX_.'layered_indexable_attribute_lang_value` la
                ON (la.`id_attribute` = a.`id_attribute` AND la.`id_lang` = '.(int)$id_lang.')
            WHERE la.`url_name` IS NOT NULL
            AND pa.`id_product` = '.(int)$id_product) ;

            if (!empty($nb_custom_values)) {
                $tab_id_attribute = array() ;
                foreach ($nb_custom_values as $attribute) {
                    $tab_id_attribute[] = $attribute['id_attribute'] ;
                    $group = Db::getInstance()->executeS('
                    SELECT g.`id_attribute_group`, g.`url_name` as `group`
                    FROM `'._DB_PREFIX_.'layered_indexable_attribute_group_lang_value` g
                    LEFT JOIN `'._DB_PREFIX_.'attribute` a
                        ON (a.`id_attribute_group` = g.`id_attribute_group`)
                    WHERE a.`id_attribute` = '.(int)$attribute['id_attribute'].'
                    AND g.`id_lang` = '.(int)$id_lang.'
                    AND g.`url_name` IS NOT NULL') ;
                    if (empty($group)) {
                        $group = Db::getInstance()->executeS('
                        SELECT g.`id_attribute_group`, g.`name` as `group`
                        FROM `'._DB_PREFIX_.'attribute_group_lang` g
                        LEFT JOIN `'._DB_PREFIX_.'attribute` a
                            ON (a.`id_attribute_group` = g.`id_attribute_group`)
                        WHERE a.`id_attribute` = '.(int)$attribute['id_attribute'].'
                        AND g.`id_lang` = '.(int)$id_lang.'
                        AND g.`name` IS NOT NULL') ;
                    }
                    $result[] = array_merge($attribute, $group[0]) ;
                }
                $values_not_custom = Db::getInstance()->executeS('
                SELECT DISTINCT a.`id_attribute`, a.`id_attribute_group`, a.`id_attribute_group`
                , al.`name` as `attribute`, agl.`name` as `group`
                FROM `'._DB_PREFIX_.'attribute` a
                LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
                    ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
                    ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
                    ON (a.`id_attribute` = pac.`id_attribute`)
                LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
                    ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                '.'
                '.'
                WHERE pa.`id_product` = '.(int)$id_product.'
                AND a.`id_attribute` NOT IN('.implode(', ', $tab_id_attribute).')') ;
                $result = array_merge($values_not_custom, $result) ;
            } else {
                $result = Db::getInstance()->executeS('
                SELECT DISTINCT a.`id_attribute`, a.`id_attribute_group`, al.`name` as `attribute`,
                agl.`name` as `group`
                FROM `'._DB_PREFIX_.'attribute` a
                LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
                    ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
                    ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
                    ON (a.`id_attribute` = pac.`id_attribute`)
                LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
                    ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                '.'
                '.'
                WHERE pa.`id_product` = '.(int)$id_product) ;
            }
        } else {
            $result = Db::getInstance()->executeS('
            SELECT DISTINCT a.`id_attribute`, a.`id_attribute_group`, al.`name` as `attribute`, agl.`name` as `group`
            FROM `'._DB_PREFIX_.'attribute` a
            LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
                ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
            LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
                ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
            LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
                ON (a.`id_attribute` = pac.`id_attribute`)
            LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
                ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
            '.'
            '.'
            WHERE pa.`id_product` = '.(int)$id_product) ;
        }
        return $result ;
    }

    /**
     * Building product's categories hierarchy
     * @param array $categories
     * @param $id_lang
     * @return array
     */
    public static function buildCategoriesString($categories, $id_lang)
    {
        try {
            $categoriesData  = array('identifiers' => array(),'categoriesNames' => array(), 'categories'=>array());
            if (!empty($categories)) {
                $pathArray = array();
                $idPathArray = array();
                foreach ($categories as $r) {
                    $pahtByName = '';
                    $pahtById = '';
                    $index = 0;
                    $category = new Category($r, $id_lang);
                    $parents = $category->getParentsCategories($id_lang);
                    foreach ($parents as $parent) {
                        $pahtByName = $parent['name'] .'/' .$pahtByName ;
                        $pahtById = $parent['id_category'] .'/' .$pahtById ;
                        if ($index == (count($parents)-1)) {
                            //identifiers
                            array_push($categoriesData['categories'], Tools::substr($pahtByName, 0, -1));
                        } else {
                            $index +=1;
                        }
                    }
                    if (Tools::substr($pahtByName, -1)==='/') {
                        $pahtByName = Tools::substr($pahtByName, 0, -1);
                    }
                    if (Tools::substr($pahtById, -1)==='/') {
                        $pahtById = Tools::substr($pahtById, 0, -1);
                    }
                    $pathArray[] = $pahtByName;
                    $idPathArray[] = $pahtById;
                }
                $unique = true;
                for ($i=0; $i <count($pathArray); $i++) {
                    for ($j=$i+1; $j <count($pathArray); $j++) {
                        if (strpos($pathArray[$j], $pathArray[$i]) !== false) {
                            $unique = false;
                            break;
                        }
                    }
                    if ($unique === true) {
                        array_push($categoriesData['categoriesNames'], $pathArray[$i]);
                    }
                    $unique = true;
                }

                $unique = true;
                for ($i=0; $i <count($idPathArray); $i++) {
                    for ($j=$i+1; $j <count($idPathArray); $j++) {
                        if (strpos($idPathArray[$j], $idPathArray[$i]) !== false) {
                            $unique = false;
                            break;
                        }
                    }
                    if ($unique === true) {
                        array_push($categoriesData['identifiers'], $idPathArray[$i]);
                    }
                    $unique = true;
                }
                return $categoriesData;
            }

            return array('identifiers' => array(), 'categoriesNames' => array(), 'categories'=>array());
        } catch (Exception $e) {
            return    array('identifiers' => array(), 'categories' => array(), 'categoriesNames' => array());
        }
    }

        /**
     * Building product's categories hierarchy
     * @param array $categories
     * @param $id_lang
     * @return array
     */
    public static function buildCategoriesStringFromObjects($categories, $id_lang)
    {
        try {
            $categoriesData  = array('identifiers' => array(),'categoriesNames' => array(), 'categories'=>array());
            if (!empty($categories)) {
                $pathArray = array();
                $idPathArray = array();
                foreach ($categories as $r) {
                    $pahtByName = '';
                    $pahtById = '';
                    $index = 0;
                    $category = new Category($r['id'], $id_lang);
                    $parents = $category->getParentsCategories($id_lang);
                    foreach ($parents as $parent) {
                        $pahtByName = $parent['name'] .'/' .$pahtByName ;
                        $pahtById = $parent['id_category'] .'/' .$pahtById ;
                        if ($index == (count($parents)-1)) {
                            //identifiers
                            array_push($categoriesData['categories'], Tools::substr($pahtByName, 0, -1));
                        } else {
                            $index +=1;
                        }
                    }
                    if (Tools::substr($pahtByName, -1)==='/') {
                        $pahtByName = Tools::substr($pahtByName, 0, -1);
                    }
                    if (Tools::substr($pahtById, -1)==='/') {
                        $pahtById = Tools::substr($pahtById, 0, -1);
                    }
                    $pathArray[] = $pahtByName;
                    $idPathArray[] = $pahtById;
                }
                $unique = true;
                for ($i=0; $i <count($pathArray); $i++) {
                    for ($j=$i+1; $j <count($pathArray); $j++) {
                        if (strpos($pathArray[$j], $pathArray[$i]) !== false) {
                            $unique = false;
                            break;
                        }
                    }
                    if ($unique === true) {
                        array_push($categoriesData['categoriesNames'], $pathArray[$i]);
                    }
                    $unique = true;
                }

                $unique = true;
                for ($i=0; $i <count($idPathArray); $i++) {
                    for ($j=$i+1; $j <count($idPathArray); $j++) {
                        if (strpos($idPathArray[$j], $idPathArray[$i]) !== false) {
                            $unique = false;
                            break;
                        }
                    }
                    if ($unique === true) {
                        array_push($categoriesData['identifiers'], $idPathArray[$i]);
                    }
                    $unique = true;
                }
                return $categoriesData;
            }

            return array('identifiers' => array(), 'categoriesNames' => array(), 'categories'=>array());
        } catch (Exception $e) {
            return    array('identifiers' => array(), 'categories' => array(), 'categoriesNames' => array());
        }
    }
}
