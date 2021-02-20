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
class FavizoneCategoryManager
{
    /**
     *  Constructor
     */
    public function __construct()
    {
        require_once(_PS_MODULE_DIR_ . 'favizone/classes/helpers/sender.php') ;
        require_once(_PS_MODULE_DIR_ . 'favizone/classes/helpers/common.php') ;
        require_once(_PS_MODULE_DIR_ . 'favizone/classes/helpers/configuration.php') ;
        require_once(_PS_MODULE_DIR_ . 'favizone/classes/constants/api.php') ;
    }
    /**
     * Returns a list of all  categories data.
     *
     * @param int $id_shop shop's selected shop identifier
     * @param int $id_shop_group current group identifier
     * @param array $language the selected language
     * @return Array the categories list
     */
    public function getAllCategories($id_shop, $id_shop_group, $language)
    {
        $helper = new FavizoneCommonHelper() ;
        $ps_version = $helper->prestaShopVersion() ;
        $db = Db::getInstance() ;
        if ($ps_version == '1.4') {
            $sql = 'SELECT 
              c.id_category as idCategory FROM ' . _DB_PREFIX_ . 'category c  
              JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (c.id_category = cl.id_category and cl.id_lang = '
              . (int)($language["id_lang"]) . ')
              group by c.id_category
              ' ;
        } else {
            $sql = 'SELECT

                    c.id_category as idCategory FROM ' . _DB_PREFIX_ . 'category c  
                     JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (c.id_category = cl.id_category and cl.id_lang = '
                    . (int)$language["id_lang"] . ')
                     JOIN ' . _DB_PREFIX_ . 'category_shop cs ON (c.id_category = cs.id_category and cs.id_shop = '
                    . (int)$id_shop . ')
                     JOIN ' . _DB_PREFIX_ . 'category_group cg ON (c.id_category = cs.id_category and cg.id_group = '
                    . (int)$id_shop_group . ')
                    group by c.id_category
                    ' ;
        }

        $categoryList = $db->ExecuteS($sql) ;
        $categories_data = array() ;
        foreach ($categoryList as $identifier) {
            $category = $this->getCategoryData($identifier["idCategory"], $id_shop, $language["id_lang"]) ;
            array_push($categories_data, $category) ;
        }
        return $categories_data ;
    }

    /**
     * Returns a specified category data .
     *
     * @param int $id_category category identifier
     * @param int $id_shop store's identifier
     * @param int $id_shop_group group's identifier
     * @param int $id_language selected language identifier
     * @param String $language_iso_code selected language iso code
     * @return array the category data
     */
    public function getSingleCategory($id_category, $id_shop, $id_language = null)
    {
        return $this->getCategoryData($id_category, $id_shop, $id_language) ;
    }

    /**
     * Returns a specified category data .
     *
     * @param int $id_category category identifier
     * @param Shop shop
     * @return array the category data
     */
    public function getCategoryData($id_category, $id_shop, $id_language)
    {
        $language = new Language($id_language) ;
        $language_iso_code = $language->iso_code ;
        
        $helper = new FavizoneCommonHelper() ;
        $ps_version = $helper->prestaShopVersion() ;
        $category_data = array() ;
       
        if ($ps_version == '1.4') {
            $category = new Category((int)$id_category, $id_language) ;
        } else {
            $shop = new Shop($id_shop) ;
            $id_shop = $shop->id ;
            $category = new Category($id_category, $id_language, $id_shop) ;
        }
        
        $category_data["idCategory"] = $this->buildCategoryPath($category, $id_language) ;

        return $category_data ;
    }

    /**
     * Sending categories data .
     *
     * @param int $id_shop selected shop shop identifier.
     * @param int $id_group selected selected shop .
     * @param array $language the selected language object
     */
    public function sendCategoriesData($id_shop, $id_group, $language, $auth_key = null)
    {
        $sender = new FavizoneSender() ;
        $api_data = new FavizoneApi() ;
        if ($auth_key == null) {
            $auth_key = FavizoneConfiguration::get('FAVIZONE_AUTH_KEY', $language["id_lang"]) ;
        }
        $data_to_send = array("key" => $auth_key,
                              "categories" => $this->getAllCategories($id_shop, $id_group, $language)) ;
        $sender->postRequest($api_data->getHost(), $api_data->getCategoryUrl(), $data_to_send) ;
    }

    /**
     * Getting category Link .
     *
     * @param int $id_category selected shop shop identifier.
     * @param int $id_shop selected selected shop .
     * @param array $id_language the selected language object
     */
    public function getCategoryLink($id_category, $id_shop, $id_language)
    {
        $helper = new FavizoneCommonHelper() ;
        $ps_version = $helper->prestaShopVersion() ;
        $link= new Link();
        switch ($ps_version) {
            case '1.7':
                return  $link->getCategoryLink($id_category, null, $id_language, null, $id_shop, false) ;
            case '1.6':
                return  $link->getCategoryLink($id_category, null, $id_language, null, $id_shop, false) ;
            default:
                return $link->getCategoryLink($id_category, null, $id_language) ;
        }
    }

    /**
     * Getting category Image Link .
     *
     * @param int $name .
     * @param int $id_category .
     */
    public function getCategoryImageLink($name, $id_category)
    {
        $helper = new FavizoneCommonHelper() ;
        $ps_version = $helper->prestaShopVersion() ;
        $link = new Link() ;
        $image_link = $link->getCatImageLink($name, $id_category) ;
        if ($image_link != null) {
            if ($ps_version == '1.4') {
                $image_link = Tools::getMediaServer($image_link).$image_link ;
            }
            if (strpos($image_link, 'http') !== true) {
                $protocol = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://' ;
                $image_link = $protocol . $image_link ;
            }
        }
        return $image_link ;
    }

    /**
     * Getting category path .
     *
     * @param int $id_shop selected shop  identifier.
     * @param array $id_lang
     */
    public static function buildCategoryPath($category, $id_lang)
    {
        try {
            $parents = $category->getParentsCategories($id_lang) ;
            $pahtByName = '' ;
            foreach ($parents as $parent) {
                $pahtByName = $parent['name'] .'/' .$pahtByName ;
            }
            if (Tools::substr($pahtByName, -1)==='/') {
                $pahtByName = Tools::substr($pahtByName, 0, -1);
            }
            return $pahtByName ;
        } catch (Exception $e) {
            return '' ;
        }
    }
}
