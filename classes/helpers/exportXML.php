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
class FavizoneExportXML
{
    /**
     *  Constructor
     */
    public function __construct()
    {
        //require_once('../../classes/Tools.php');

        require_once(_PS_MODULE_DIR_.'favizone/classes/model/productupdate.php');
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/sender.php');
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/common.php');
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/configuration.php');
        require_once(_PS_MODULE_DIR_.'favizone/classes/constants/api.php');
    }


    /**
     * export XML product data .
     *
     */
    public function exportXML($products, $idShop, $iso_code, $fileXml)
    {
         $dom = new DomDocument('1.0', 'utf-8');
        if (file_exists($fileXml)) {
            $dom->load($fileXml);
            $catalog = $dom->getElementById("catalog");
        } else {
            $catalog = $dom->appendChild($dom->createElement('catalog'));
        }
        try {
            $country = $dom->createAttribute('country');
            $country->appendChild($dom->createTextNode($iso_code));
            $catalog->appendChild($country);
            $id_shop = $dom->createAttribute('idShop');
            $id_shop->appendChild($dom->createTextNode($idShop));
            $catalog->appendChild($id_shop);
            $catalog->setAttribute('xml:id', 'catalog');
            foreach ($products as &$product) {
                $productElement = $dom->createElement('product');
                //add id_product
                if (isset($product['identifier'])) {
                    $identifier = $product['identifier'];
                    $idProduct = $dom->createElement('id_product');
                    $cdataIdProduct = $idProduct->ownerDocument->createCDATASection($identifier);
                    $idProduct->appendChild($cdataIdProduct);
                    $productElement->appendChild($idProduct);
                }
                //add name_product
                if (isset($product['title'])) {
                    $title = $product['title'];
                    $nameProduct = $dom->createElement('name_product');
                    $cdataNameProduct = $nameProduct->ownerDocument->createCDATASection($title);
                    $nameProduct->appendChild($cdataNameProduct);
                    $productElement->appendChild($nameProduct);
                }
                //add reference_product
                if (isset($product['reference'])) {
                    $reference = $product['reference'];
                    $referenceProduct = $dom->createElement('reference_product');
                    $cdataReferenceProduct = $referenceProduct->ownerDocument->createCDATASection($reference);
                    $referenceProduct->appendChild($cdataReferenceProduct);
                    $productElement->appendChild($referenceProduct);
                }
                //add lang
                if (isset($product['lang'])) {
                    $lang = $product['lang'];
                    $manufacturer = $dom->createElement('lang');
                    $cdataManufacturer = $manufacturer->ownerDocument->createCDATASection($lang);
                    $manufacturer->appendChild($cdataManufacturer);
                    $productElement->appendChild($manufacturer);
                }
                //categories Names
                if (isset($product['categoriesNames'])) {
                    $categoriesNames = $product['categoriesNames'];
                    if (is_array($categoriesNames) && !empty($categoriesNames)) {
                        $categoriesNamesElement = $dom->createElement('categoriesNames');
                        foreach ($categoriesNames as $index => $categoryName) {
                            $subCategory = $dom->createElement('category');
                            $cdataSubCategory=$subCategory->ownerDocument->createCDATASection($categoryName);
                            $subCategory->appendChild($cdataSubCategory);
                            $categoriesNamesElement->appendChild($subCategory);
                        }
                        $productElement->appendChild($categoriesNamesElement);
                    }
                }
                //categories
                if (isset($product['categories'])) {
                    $categories = $product['categories'];
                    if (is_array($categories) && !empty($categories)) {
                        $categoriesElement = $dom->createElement('categories');
                        foreach ($categories as $index => $category) {
                            $subCategory = $dom->createElement('category');
                            $cdataSubCategory=$subCategory->ownerDocument->createCDATASection($category);
                            $subCategory->appendChild($cdataSubCategory);
                            $categoriesElement->appendChild($subCategory);
                        }
                        $productElement->appendChild($categoriesElement);
                    }
                }
                //tags
                if (isset($product['tags'])) {
                    $tags = $product['tags'];
                    if (is_array($tags) && !empty($tags)) {
                        $tagsElement = $dom->createElement('tags');
                        foreach ($tags as $index => $tag) {
                            $subTag = $dom->createElement('tag');
                            $cdataSubTag=$subTag->ownerDocument->createCDATASection($tag);
                            $subTag->appendChild($cdataSubTag);
                            $tagsElement->appendChild($subTag);
                        }
                        $productElement->appendChild($tagsElement);
                    }
                }
                //hasDeclination
                if (isset($product['hasDeclination'])) {
                    $hasDeclination = $product['hasDeclination'];
                    $hasDeclinationElement = $dom->createElement('hasDeclination');
                    $cdataHasDeclination=$hasDeclinationElement->ownerDocument->createCDATASection($hasDeclination);
                    $hasDeclinationElement->appendChild($cdataHasDeclination);
                    $productElement->appendChild($hasDeclinationElement);
                }
                //facets
                if (isset($product['facets'])) {
                    $facets = $product['facets'];
                    if (is_array($facets) && !empty($facets)) {
                        $facetsElement = $dom->createElement('facets');
                        foreach ($facets as $index => $facet) {
                            if (is_array($facet) && !empty($facet)) {
                                $subFacetsElement = $dom->createElement($this->removeSpecialCharacters($index));
                                foreach ($facet as $subFacet) {
                                    $subFacetElement = $dom->
                                    createElement('sub_'.$this->removeSpecialCharacters($index));
                                    $cdataSubFacet=$subFacetElement->ownerDocument->createCDATASection($subFacet);
                                    $subFacetElement->appendChild($cdataSubFacet);
                                    $subFacetsElement->appendChild($subFacetElement);
                                }
                                $facetsElement->appendChild($subFacetsElement);
                            } else {
                                /*$subFacet = $dom->createElement($this->removeSpecialCharacters($index));
                                $cdataSubFacet=$subFacet->ownerDocument->createCDATASection($facet);
                                $subFacet->appendChild($cdataSubFacet);
                                $facetsElement->appendChild($subFacet);*/
                                $subFacetsElement = $dom->createElement($this->removeSpecialCharacters($index));
                                $subFacetElement = $dom->
                                    createElement('sub_'.$this->removeSpecialCharacters($index));
                                $subFacet = $dom->createElement($this->removeSpecialCharacters($index));
                                $cdataSubFacet=$subFacetElement->ownerDocument->createCDATASection($facet);
                                $subFacetElement->appendChild($cdataSubFacet);
                                $subFacetsElement->appendChild($subFacetElement);
                                $facetsElement->appendChild($subFacetsElement);
                            }
                        }
                        $productElement->appendChild($facetsElement);
                    }
                }
                //id attribut
                if (isset($product['id_product_attribute'])) {
                    $attribute = $product['id_product_attribute'];
                    $attributeElement = $dom->createElement('attribute');
                    $cdataAttribute=$hasDeclinationElement->ownerDocument->createCDATASection($attribute);
                    $attributeElement->appendChild($cdataAttribute);
                    $productElement->appendChild($attributeElement);

                }
                if (isset($product['isNew'])) {
                    $isNew = $product['isNew'];
                    $isNewElement = $dom->createElement('isNew');
                    $cdataisNew=$hasDeclinationElement->ownerDocument->createCDATASection($isNew);
                    $isNewElement->appendChild($cdataisNew);
                    $productElement->appendChild($isNewElement);

                }

                //stock
                if (isset($product['stock'])) {
                    $stock = $product['stock'];
                    $stockElement = $dom->createElement('stock');
                    $cdataStock=$hasDeclinationElement->ownerDocument->createCDATASection($stock);
                    $stockElement->appendChild($cdataStock);
                    $productElement->appendChild($stockElement);
                }
                //quantity
                if (isset($product['quantity'])) {
                    $quantity = $product['quantity'];
                    $quantityElement = $dom->createElement('quantity');
                    $cdataQuantity=$hasDeclinationElement->ownerDocument->createCDATASection($quantity);
                    $quantityElement->appendChild($cdataQuantity);
                    $productElement->appendChild($quantityElement);
                }
                //available_for_order
                if (isset($product['available_for_order'])) {
                    $available_for_order = $product['available_for_order'];
                    $available_for_orderElement = $dom->createElement('available_for_order');
                    $cdataAvailable_for_order=$available_for_orderElement->ownerDocument
                    ->createCDATASection($available_for_order);
                    $available_for_orderElement->appendChild($cdataAvailable_for_order);
                    $productElement->appendChild($available_for_orderElement);
                }
                //active
                if (isset($product['active'])) {
                    $active = $product['active'];
                    $activeElement = $dom->createElement('active');
                    $cdataActive=$activeElement->ownerDocument->createCDATASection($active);
                    $activeElement->appendChild($cdataActive);
                    $productElement->appendChild($activeElement);
                }
                //brand
                if (isset($product['brand'])) {
                    $brand = $product['brand'];
                    $brandElement = $dom->createElement('brand');
                    $cdataBrand=$brandElement->ownerDocument->createCDATASection($brand);
                    $brandElement->appendChild($cdataBrand);
                    $productElement->appendChild($brandElement);
                }
                //price
                if (isset($product['price'])) {
                    $price = $product['price'];
                    $priceElement = $dom->createElement('price');
                    $cdataPrice=$priceElement->ownerDocument->createCDATASection($price);
                    $priceElement->appendChild($cdataPrice);
                    $productElement->appendChild($priceElement);
                }
                //wholesale_price
                if (isset($product['wholesale_price'])) {
                    $wholesale_price = $product['wholesale_price'];
                    $wholesale_priceElement = $dom->createElement('wholesale_price');
                    $cdataWholesale_price=$wholesale_priceElement->ownerDocument->createCDATASection($wholesale_price);
                    $wholesale_priceElement->appendChild($cdataWholesale_price);
                    $productElement->appendChild($wholesale_priceElement);
                }
                //currency
                if (isset($product['currency'])) {
                    $currency = $product['currency'];
                    $currencyElement = $dom->createElement('currency');
                    $cdataCurrency=$currencyElement->ownerDocument->createCDATASection($currency);
                    $currencyElement->appendChild($cdataCurrency);
                    $productElement->appendChild($currencyElement);
                }
                //id_shop
                if (isset($product['id_shop'])) {
                    $id_shop = $product['id_shop'];
                    $id_shopElement = $dom->createElement('id_shop');
                    $cdataId_shop=$id_shopElement->ownerDocument->createCDATASection($id_shop);
                    $id_shopElement->appendChild($cdataId_shop);
                    $productElement->appendChild($id_shopElement);
                }
                if (isset($product['url'])) {
                    $url = $product['url'];
                    //add url
                    $urlProduct = $dom->createElement('url');
                    $cdataUrlProduct = $urlProduct->ownerDocument->createCDATASection($url);
                    $urlProduct->appendChild($cdataUrlProduct);
                    $productElement->appendChild($urlProduct);
                }
                if (isset($product['cover'])) {
                    $cover = $product['cover'];
                    //add cover
                    $imageProduct = $dom->createElement('cover');
                    $cdataImageProduct=$imageProduct->ownerDocument->createCDATASection($cover);
                    $imageProduct->appendChild($cdataImageProduct);
                    $productElement->appendChild($imageProduct);
                }
                //add home cover
                if (isset($product['home_cover'])) {
                    $homeCover = $product['home_cover'];
                    $homeCoverProduct = $dom->createElement('home_cover');
                    $cdataHomeCoverProduct=$homeCoverProduct->ownerDocument->createCDATASection($homeCover);
                    $homeCoverProduct->appendChild($cdataHomeCoverProduct);
                    $productElement->appendChild($homeCoverProduct);
                }
                //published_date
                if (isset($product['published_date'])) {
                    $published_date = $product['published_date'];
                    $published_dateElement = $dom->createElement('published_date');
                    $cdataPublished_date=$published_dateElement->ownerDocument->createCDATASection($published_date);
                    $published_dateElement->appendChild($cdataPublished_date);
                    $productElement->appendChild($published_dateElement);
                }
                //isReduced
                if (isset($product['isReduced'])) {
                    $isReduced = $product['isReduced'];
                    $isReducedElement = $dom->createElement('isReduced');
                    $cdataIsReduced=$isReducedElement->ownerDocument->createCDATASection($isReduced);
                    $isReducedElement->appendChild($cdataIsReduced);
                    $productElement->appendChild($isReducedElement);
                }
                //reduction
                if (isset($product['reduction'])) {
                    $reduction = $product['reduction'];
                    $reductionElement = $dom->createElement('reduction');
                    $cdataReduction=$reductionElement->ownerDocument->createCDATASection($reduction);
                    $reductionElement->appendChild($cdataReduction);
                    $productElement->appendChild($reductionElement);
                }

                //reduction_type
                if (isset($product['reduction_type'])) {
                    $reduction_type = $product['reduction_type'];
                    $reduction_typeElement = $dom->createElement('reduction_type');
                    $cdataReduction_type=$reduction_typeElement->ownerDocument->createCDATASection($reduction_type);
                    $reduction_typeElement->appendChild($cdataReduction_type);
                    $productElement->appendChild($reduction_typeElement);
                }

                //reduction_tax
                if (isset($product['reduction_tax'])) {
                    $reduction_tax = $product['reduction_tax'];
                    $reduction_taxElement = $dom->createElement('reduction_tax');
                    $cdataReduction_tax=$reduction_taxElement->ownerDocument->createCDATASection($reduction_tax);
                    $reduction_taxElement->appendChild($cdataReduction_tax);
                    $productElement->appendChild($reduction_taxElement);
                }

                  //reduction_tax
                if (isset($product['price_without_reduction'])) {
                    $price_without_reduction = $product['price_without_reduction'];
                    $price_without_reductionElement = $dom->createElement('price_without_reduction');
                    $cdataPrice_without_reduction=$price_without_reductionElement->ownerDocument
                    ->createCDATASection($price_without_reduction);
                    $price_without_reductionElement->appendChild($cdataPrice_without_reduction);
                    $productElement->appendChild($price_without_reductionElement);
                }
                //description
                if (isset($product['description'])) {
                    $description = $product['description'];
                    $descriptionElement = $dom->createElement('description');
                    $cdataDescription=$descriptionElement->ownerDocument->createCDATASection($description);
                    $descriptionElement->appendChild($cdataDescription);
                    $productElement->appendChild($descriptionElement);
                }

                 //shortDescription
                if (isset($product['shortDescription'])) {
                    $shortDescription = $product['shortDescription'];
                    $shortDescriptionElement = $dom->createElement('shortDescription');
                    $cdataShortDescription=$shortDescriptionElement
                    ->ownerDocument->createCDATASection($shortDescription);
                    $shortDescriptionElement->appendChild($cdataShortDescription);
                    $productElement->appendChild($shortDescriptionElement);
                }

                $catalog->appendChild($productElement);
            }
            $dom->formatOutput = true; // set the formatOutput attribute of domDocument to true
            // save XML as string or file
            $dom->save($fileXml);
            return $dom ->save($fileXml);// save as file
        } catch (Exception $e) {
            echo "$e";
        }
    }
    
    /**
     * Init products data in Favizone.
     *
     * @param $token
     * @return boolean
     */
    public function validateTokenWithTstamp($access_token)
    {
        $where = strtr(
            '`token` = "{l}"',
            array(
                '{l}' => $access_token,
            )
        );
        $sql = sprintf(
            '
            SELECT *
            FROM %sfz_export_xml_config
            WHERE %s
            ',
            _DB_PREFIX_,
            $where
        );
        $rows = Db::getInstance()->executeS($sql);
        if (count($rows)>0 && $rows[0]["tstamp"]) {
            if ($rows[0]["tstamp"] > time()) {
                //valide token
                return true;
            } else {
                //expired token
                return false;
            }
        } else {
            return false;
        }
        return false;
    }
 
    /**
     * Init products data in Favizone.
     *
     * @param $token
     * @return boolean
     */
    public function validateToken($access_token)
    {
        $where = strtr(
            '`token` = "{l}"',
            array(
                '{l}' => $access_token,
            )
        );
        $sql = sprintf(
            '
            SELECT *
            FROM %sfz_export_xml_config
            WHERE %s
            ',
            'ps_',
            $where
        );
        $rows = Db::getInstance()->executeS($sql);
        if (count($rows)>0 && $rows[0]["tstamp"]) {
            //valide token
            return true;
        } else {
            return false;
        }
        return false;
    }


     /**
     * Init products data in Favizone.
     *
     * @param $token
     * @return boolean
     */
    public function validateTokenAndShop($access_token, $id_shop)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'fz_export_xml_config WHERE id_shop = '
        .$id_shop.' AND token = "'.$access_token.'"';
        if (Db::getInstance()->getRow($sql)) {
            return true;
        }
        return false;
    }
    
    /**
     * exportXmlProductsData
     *
     * @param $context
     * @param int $id_language
     * @param int $idShop
     * @param int $iso_code
     * @return string
     */
    public function exportXmlProductsData($context, $id_language, $idShop, $iso_code)
    {
        $product_sender = new FavizoneProductManager();
        $number_products= $product_sender->getCountAvailableProducts();
        $favizone_product_tagger = new FavizoneTaggingProduct();
        $fileXml = _PS_MODULE_DIR_.'favizone/favizone-export-cataloge-'.$iso_code.'-'.$idShop.'.xml';
        if (file_exists($fileXml)) {
            unlink($fileXml);
        }
        /** Export paginated products data **/
        while ($product_sender->offset <= $number_products/*250*/) {
            $products_collection = array();
            foreach ($product_sender->getProductIds() as &$id_product) {
                $product = new Product($id_product, true, $id_language, $idShop);
                if (Validate::isLoadedObject($product)) {
                    $favizone_product= $favizone_product_tagger
                    ->loadProductData($context, $product, $id_language, $idShop);
                    array_push($products_collection, $favizone_product);
                }
                $product = null;
            }
            $product_sender->offset = $product_sender->offset + $product_sender->limit;
            if (count($products_collection)>0) {
                $this -> exportXML($products_collection, $idShop, $iso_code, $fileXml);
            }
        }
        return "done";
    }
     function sendtofavizone()
     {
         $sender = new FavizoneSender() ;
       $api_data = new FavizoneApi() ;
       $urlxml = _PS_BASE_URL_.__PS_BASE_URI__.'modules/favizone/favizone-export-cataloge-'.$iso_code.'-'.$idShop.'.xml';
       $link_integration=str_replace('\\', '/',   $urlxml);
       $key =Tools::getValue(
           'auth_key',
           FavizoneConfiguration::get(
               'FAVIZONE_AUTH_KEY',
               $id_language
           ));
       $data_to_send =  array(
           "key"=> $key,
           "product_file_url"=>$link_integration,
           "product_file_platform"=>"Favizone"
       );
       $sender->postRequest($api_data->getHost(), $api_data->getIntegrationUrl(), $data_to_send);
        
         
     }
    /**
     *
     * @param int $id_shop
     * @return string
     */
    public function createToken($id_shop)
    {
        $token = md5(uniqid(rand(), true));
        $tstamp = time() + (24 * 60 * 60);
        Db::getInstance()->insert(
            'fz_export_xml_config',
            array(
                'token' => pSQL($token),
                'id_shop' => pSQL($id_shop),
                'tstamp' => pSQL($tstamp),
            )
        );
        return $token;
    }

     /**
     *
     * @param int $id_shop
     * @return string
     */
    public function createToken14($id_shop)
    {
        $token = md5(uniqid(rand(), true));
        $tstamp = time() + (24 * 60 * 60);
        Db::getInstance()->autoExecute(
            _DB_PREFIX_.'fz_export_xml_config',
            array(
                'token' => pSQL($token),
                'id_shop'     => pSQL($id_shop),
                'tstamp'     => pSQL($tstamp)
            ),
            'INSERT'
        );
        return $token;
    }

    /**
     * Init products data in Favizone.
     *
     * @param $id_shop
     * @return string
     */
    public function getToken($id_shop)
    {
        $where = strtr(
            '`id_shop` = "{l}"',
            array(
                '{l}' => $id_shop,
            )
        );
        $sql = sprintf(
            '
            SELECT *
            FROM %sfz_export_xml_config
            WHERE %s
            ',
            'ps_',
            $where
        );
        $rows = Db::getInstance()->executeS($sql);
        if (count($rows)>0 && $rows[0]["tstamp"]) {
            //valide token
            return $rows[0]["token"];
        } else {
            return null;
        }
        return null;
    }

    public function removeSpecialCharacters($str)
    {
        $charset='utf-8';
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        $str = preg_replace('#&([A-Za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-Za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
        $str = str_replace(' ', '_', $str); // supprime les autres caractères
        $str = preg_replace("(^[1-9]*)", '', $str); // supprime les autres caractères
        $str = preg_replace("([\' \" ? / . \ + * ? \[ ^ \] $ ( ) { } = ! < > | : -])", '', $str);
        return $str;
    }
}
