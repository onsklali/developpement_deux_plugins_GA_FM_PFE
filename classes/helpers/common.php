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
 * Helper class for common operations.
 */
use Google\Cloud\Firestore\FirestoreClient;
class FavizoneCommonHelper
{
    protected $db;
    protected $name="prestashop";
    /**
     *  Constructor
     */
    public function __construct()
    {
        if (_PS_VERSION_ < '1.5') {
            require_once(_PS_MODULE_DIR_.'favizone/classes/model/Shop.php') ;
        }
        require_once(_PS_MODULE_DIR_.'favizone/classes/model/customer.php') ;
        require_once(_PS_MODULE_DIR_.'favizone/classes/constants/api.php') ;
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/sender.php') ;
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/product.php') ;
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/configuration.php') ;
        require_once(_PS_MODULE_DIR_.'favizone/vendor/autoload.php');
        $this->db = new FirestoreClient([
            'projectId' => 'prestaproject-b0d92'
        ]);
    }
    /**
     * Create new collection
     * @param string $name
     * @param string $doc_name
     * @param array $data
     * @return bool|string
     */
    public function newDocument(string $name, array $data = [])
    {
        try {
            $this->db->collection($this->name)->document($name)->set($data);
            return true;
        } catch (Exception $exception){
            return $exception->getMessage();
        }
    }
    /**
     * Create old collection
     * @param string $name
     * @param string $doc_name
     * @param array $data
     * @return bool|string
     */
    public function oldDocument(string $name, array $data = [])
    {
        try {
            $this->db->collection($this->name)->document($name)->set($data, ['merge' => true]);
            return true;
        } catch (Exception $exception){
            return $exception->getMessage();
        }
    }
    /**
     * Checks if the given controller is the current one.
     *
     * @param string $name the controller name
     * @return bool true if the given name is the same as the controllers php_self variable, false otherwise.
     */
    public function isController($name)
    {
        if (_PS_VERSION_ >= 1.5) {
            // For prestashop 1.5 and 1.6 we can in most cases access the current controllers php_self property.
            if (!empty($this->context->controller->php_self)) {
                return $this->context->controller->php_self === $name ;
            }
            // But some prestashop 1.5 controllers are missing the php_self property.
            if (($controller = Tools::getValue('controller')) !== false) {
                return $controller === $name ;
            }
        } else {
            // For 1.4 we need to parse the current script name, as it uses different scripts per page.
            $script_name = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '' ;
            return basename($script_name) === ($name.'.php') ;
        }
        return false ;
    }

    /**
     * Update AB/Test status
     *
     * @param String $action the action name
     * @param int $id_lang the selected language identifier
     * @return array|string
     */
    public function sendABTestStatus($action, $id_lang)
    {
        $sender = new FavizoneSender() ;
        $api_data = new FavizoneApi() ;
        $data_to_send =  array("key" => FavizoneConfiguration::get('FAVIZONE_AUTH_KEY', $id_lang)) ;
        if ($action == "init") {
            return $sender->postRequest($api_data->getHost(), $api_data->getInitABTestUrl(), $data_to_send) ;
        } else {
            $sender->postRequest($api_data->getHost(), $api_data->getEndABTestUrl(), $data_to_send) ;
            return $api_data->getHost().$api_data->getEndABTestUrl() ;
        }
    }

    /**
     * Getting data related to given canal
     *
     * @param $params
     * @param String $canal canal name
     * @return Array() the categories list
     */
    public function getRenderingCanal($params, $canal)
    {
        $cookie = $params['cookie'];
        $api_data = new FavizoneApi() ;
        $url = $api_data->getRecommendationRendererUrl()."/".$canal ;
        $sessionIdentifier = $this->generateSessionIdentifier($params) ;
        $testing_version = $this ->getTestingVersion() ;

        switch ($canal) {
            case "product":
                $data_to_send =  array(
                                        "url" =>$url,
                                        "post_data"=>array( "product"=>(int)Tools::getValue('id_product'),
                                                            "key" => FavizoneConfiguration::get('FAVIZONE_AUTH_KEY'),
                                                            "session" => $sessionIdentifier,
                                                            "event_params" => array( "version" => $testing_version
                                                            ,"session" => $sessionIdentifier
                                                            ),
                                                            "cart" => $this->getCartContent($params['cart'])
                                                        )
                                    ) ;
                break ;
            case "category":
                $data_to_send =  array(
                                        "url" =>$url,
                                        "post_data"=>array( "key" => FavizoneConfiguration::get('FAVIZONE_AUTH_KEY'),
                                                            "session" => $sessionIdentifier,
                                                            "event_params" => array( "version" => $testing_version
                                                                                    ,"session" => $sessionIdentifier
                                                                                    ),
                                                            "category" =>Tools::getValue('id_category'),
                                                            "cart" => $this->getCartContent($params['cart'])
                                                            )
                                    ) ;
                break ;
            case "search":
                $data_to_send =  array(
                                        "url" =>$url,
                                        "post_data"=>array( "key" => FavizoneConfiguration::get('FAVIZONE_AUTH_KEY'),
                                                            "session" => $sessionIdentifier,
                                                            "event_params" => array( "version" => $testing_version
                                                            ,"session" => $sessionIdentifier
                                                            ),
                                                            "search" =>Tools::getValue('search_query'),
                                                            "cart" => $this->getCartContent($params['cart'])
                                                         )
                                    ) ;
                break ;
            case "demo":
                $url =  $api_data->getDemoRecommendationRendererUrl().Tools::getValue('id_recommender');
                $data_to_send =  array(
                    "url" =>$url,
                    "post_data"=>array( "key" => FavizoneConfiguration::get('FAVIZONE_AUTH_KEY'),
                        "session" => $sessionIdentifier,
                        "event_params" => array( "version" => "B","session" => $sessionIdentifier),
                        "cart" => array()
                    )
                ) ;
                break ;
            default:
                $data_to_send =  array(
                    "url" =>$url,
                    "post_data"=>array( "key" => FavizoneConfiguration::get('FAVIZONE_AUTH_KEY'),
                        "session" => $sessionIdentifier,
                        "event_params" => array( "version" => $testing_version
                                                    ,"session" => $sessionIdentifier
                                                    ),
                        "cart" => $this->getCartContent($params['cart'])
                    )
                ) ;
                if (Tools::getValue('id_product')) {
                    $product = new Product((int) Tools::getValue('id_product')) ;
                    if ($product && !$product->active) {
                        $data_to_send =  array(
                            "url" =>$url,
                            "post_data"=>array( "product"=>Tools::getValue('id_product'),
                                                "key" => FavizoneConfiguration::get('FAVIZONE_AUTH_KEY'),
                                                "session" => $sessionIdentifier,
                                                "event_params" => array( "version" => $testing_version,
                                                                        "session" => $sessionIdentifier
                                                                        ),
                                                "cart" => $this->getCartContent($params['cart']))) ;
                    }
                }
                break ;
        }
        //Searching for keywords coming from campaigns
        //if (isset( $_GET['favizone'])) {
        if (Tools::getValue('favizone')) {
            $data_to_send['post_data']['campaign'] = Tools::getValue('favizone') ;
        }

        if (isset($cookie->favizone_preview)) {
            $data_to_send['post_data']['favizone_preview'] = true ;
        }

        return $data_to_send ;
    }

    /**
     * Getting event's data
     *
     * @param  $params
     * @param  String $event_key the event name.
     * @param  string $search_query  the search query .
     * @return String the event to be sent.
     */
    public function generateTrackEvent($params, $event_key, $search_query = '', $category_path = '')
    {
        $session_identifier = $this->generateSessionIdentifier($params) ;
        $version = $this->getTestingVersion() ;
        //event structure
        $event = "" ;
        $event .= $version." " ;
        $event .= $session_identifier." " ;

        switch ($event_key) {
            case "viewProduct":
                $event .= "viewProduct " ;
                $event .= Tools::getValue('id_product')." " ;
                $event .= "1 1" ;
                break ;
            case "viewCategory":
                $category_path = str_replace(' ', "fz#", $category_path) ;
                $event .= "viewCategory " ;
                $event .= $category_path/*Tools::getValue('id_category')*/." " ;
                $event .= "1 1" ;
                break ;
            case "doSearch":
                $event .= "doSearch " ;
                $event .= urlencode($search_query)." 1 1" ;
                break ;
            case "searchEngine":
                $event .= "searchEngine " ;
                $event .= urlencode($search_query)." 1 1" ;
                break ;
            case "searchCampaign":
                $event .= "searchCampaign " ;
                $event .= urlencode($search_query)." 1 1" ;
                break ;
            case "loginUser":
                $event .= "loginUser " ;
                $event .= "1 " ;
                $event .= "1 1" ;
                break ;
            case "addToCart":
                $event .= "addToCart " ;
                $event .= $params['event_product']." " ;
                $event .= "1 1" ;
                $cart = $params['cart'] ;
                $event .=" ".$cart->id ;
                break ;
            case "click":
                $event .= "click " ;
                break ;
            case "clickBot":
                $event .= "clickBot " ;
                $event .= Tools::getValue('id_product')." " ;
                $event .= "1 1 ".Tools::getValue('favizone_rec') ;
                break ;
            case "visit":
                $event .= "visit 1 1 1" ;
                break ;
            case "clickWidget":
                $event .= "clickWidget ";
                if (Tools::getValue('id_product')) {
                    $event .= Tools::getValue('id_product')." ";
                } else {
                    $event .= "0 ";
                }
                $event .= "1 1 ".$search_query ;
                break ;
        }

        return $event ;
    }

    /**
     * Returns the session identifier
     *
     * @param  array $params the context params
     * @return String the session key of the current connexion.
     */
    public function generateSessionIdentifier($params)
    {
        $context = $this->loadContext() ;
        $sessionIdentifier = null ;
        $logout_test = !$context->customer->logged  ;
        if ($params['cookie']->__get('favizone_logged_event_'.$context->shop->id.'_'.$context->language->id)
            && $logout_test) {
            $params['cookie']->__unset('favizone_logged_event_'.$context->shop->id.'_'.$context->language->id) ;
            $params['cookie']->__unset(
                'favizone_connection_identifier_'.$context->shop->id.'_'
                .$context->language->id
            ) ;
            $params['cookie']->__unset('FAVIZONE_ABTV_'.$context->shop->id.'_'.$context->language->id) ;
        }
        if ($params['cookie']->__get('favizone_connection_identifier_'.$context->shop->id.'_'.$context->language->id)) {
            $sessionIdentifier = $params['cookie']->__get(
                'favizone_connection_identifier_'.
                $context->shop->id.'_'.$context->language->id
            ) ;
        } else {
            $sender = new FavizoneSender() ;
            $api_data = new FavizoneApi() ;
            $data_to_send = array( "key" => FavizoneConfiguration::get('FAVIZONE_AUTH_KEY'),
                                   "version"=> $this->getTestingVersion() ) ;
            $result = $sender->postRequest($api_data ->getHost(), $api_data->getRegisterProfiletUrl(), $data_to_send) ;
            $result = $this->decodeData($result) ;
            if ($result['response'] == 'authorized' || $result['response'] == 'success') {
                $params['cookie']->__set(
                    'favizone_connection_identifier_'.$context->shop->id.'_'.$context->language->id,
                    $result['identifier']
                ) ;
                $ab_test = $this->getConfigurationItem('FAVIZONE_AB_TEST') == 'true' ;
                if (!$ab_test) {
                    $params['cookie']->__set('favizone_visit', true) ;
                }
                $sessionIdentifier = $result['identifier'] ;
            }
        }

        return $sessionIdentifier ;
    }

    /**
     * Getting cart products identifiers
     *
     * @param  $cart
     * @return Array
     */
    public function getCartContent($cart)
    {
        $products = $cart->getProducts() ;
        $products_id = array()  ;
        foreach ($products as $product) {
            array_push($products_id, $product['id_product']) ;
        }
        return $products_id ;
    }

    /**
     * Loading  A/B testing version from session
     *
     * @return string {'A', 'B', 'N'}
     */
    public function getTestingVersion()
    {
        $context = $this->loadContext() ;
        $cookie = $context->cookie ;
        $session_identifier = $cookie->__get(
            "favizone_connection_identifier_".$context->shop->id."_".
            $context->language->id
        ) ;
        $ab_test = $this->getConfigurationItem('FAVIZONE_AB_TEST') ;
        $ab_test = ($ab_test == 'true') ;
        //Old user
        if ($session_identifier) {
            $current_version = $cookie->__get("FAVIZONE_ABTV_".$context->shop->id."_".$context->language->id) ;
            //A/B test is active
            if ($ab_test) {
                if ($current_version) {
                    if ($current_version == 'N') {
                        $current_version = $this->getRandomVersion() ;
                        $cookie->__set(
                            "FAVIZONE_ABTV_".$context->shop->id."_".$context->language->id,
                            $current_version
                        ) ;
                        $cookie->__set("favizone_visit", true) ;
                    }
                } else {
                    $current_version = $this->getRandomVersion() ;
                    $cookie->__set("FAVIZONE_ABTV_".$context->shop->id.'_'.$context->language->id, $current_version) ;
                }
            } elseif ($current_version != 'N') {
                    //A/B test is inactive
                    $current_version = 'N' ;
                $cookie->__set('FAVIZONE_ABTV_'.$context->shop->id.'_'.$context->language->id, $current_version) ;
            }

            return $current_version ;
        } elseif ($ab_test) {
                //New user
                //A/B test is active
                $current_version = $this->getRandomVersion() ;
            $cookie->__set('FAVIZONE_ABTV_'.$context->shop->id.'_'.$context->language->id, $current_version) ;
            $cookie->__set('favizone_visit', true) ;
        } else {
            //A/B test is inactive
            $current_version = 'N' ;
            $cookie->__set('FAVIZONE_ABTV_'.$context->shop->id.'_'.$context->language->id, 'N') ;
        }

        return $current_version ;
    }

    /**
     * Returns a random version if A/B testing is enabled.
     *
     * @return string {'A', 'B'}
     */
    private function getRandomVersion()
    {
        if (rand(1, 2) == 2) {
            $version  = 'A' ;
        } else {
            $version  = 'B' ;
        }

        return $version ;
    }

    /**
     * Get a configuration item based on its key
     *
     * @param string $key
     * @return string value
     */
    public function getConfigurationItem($key)
    {
        try {
            return FavizoneConfiguration::get($key) ;
        } catch (Exception $e) {
            return null ;
        }
    }

    /**
     * @param string $canal
     * @return string value
     */
    public function getStandardElement($canal)
    {
        switch ($canal) {
            case "product":
                $element = "layer_cart" ;
                break ;
            default:
                $element = "center_column" ;
                break ;
        }

        return $element ;
    }

    /**
     * checks if the current context is relative to a Favizone's "Other"  context or not.
     *
     * @return Boolean.
     */
    public function validateReturn()
    {
        $helper = new FavizoneProductManager() ;
        if (!$this->isController("category")
            && (!$this->isController("product")||($this->isController("product")
                    && !$helper->getProductById(Tools::getValue('id_product'))))
            && !$this->isController("search")
            && !$this->isController("index")
            && !$this->isController("order")
            && !$this->isController("order-confirmation")
            && !$this->isController("orderopc")
            && !$this->isController("validation")
            && !$this->isController("")) {
            return true ;
        }
        return false ;
    }

    /**
     * checks if the current context is relative to a Favizone's "Error"  context or not.
     *
     * @return Boolean.
     */
    public function isErrorReturn()
    {
        $helper = new FavizoneProductManager() ;
        if ($this->isController("pagenotfound") || $this->isController("404")) {
            return true ;
        }
        if ($this->isController("product")) {
            if (!$helper->getProductById(Tools::getValue('id_product'))) {
                return true ;
            } else {
                $product = new Product((int) Tools::getValue('id_product')) ;
                if (!$product->active) {
                    return true ;
                }
            }
        }
        return false ;
    }

    /**
     * Getting cms current version
     *
     * @return string
     */
    public static function prestaShopVersion()
    {
        if (version_compare(_PS_VERSION_, '1.4', '>=') && version_compare(_PS_VERSION_, '1.5', '<')) {
            return '1.4' ;
        } elseif (version_compare(_PS_VERSION_, '1.5', '>=') && version_compare(_PS_VERSION_, '1.6', '<')) {
            return '1.5' ;
        }  elseif (version_compare(_PS_VERSION_, '1.6', '>=') && version_compare(_PS_VERSION_, '1.7', '<')) {
            return '1.6' ;
        } else {
            return '1.7' ;
        }
    }

    /**
     * Getting the global context
     *
     */
    public static function loadContext()
    {
        if (class_exists('Context')) {
            return  Context::getContext() ;
        } else {
            $context = new StdClass() ;
            $context->language = new Language(1) ;
            $context->currency =  new Currency('usd') ;
            $context->shop =  new FavizoneShop(1, "default shop") ;
            return $context ;
        }
    }

    /**
     * Checks if Favizone is added to the current context .
     *
     * @return Boolean
     */
    public static function validateContext()
    {
        if (FavizoneConfiguration::get('FAVIZONE_AUTH_KEY')) {
            return true ;
        }
        return false ;
    }

    /**
     * Gets the current admin config language data.
     *
     * @param array $languages list of valid languages.
     * @param int $id_lang of a specific language .
     * @return array the language data array.
     */
    public static function getCurrentLanguage(array $languages, $id_lang)
    {
        foreach ($languages as $language) {
            if ($language['id_lang'] == $id_lang) {
                return $language ;
            }
        }
        if (isset($languages[0])) {
            return $languages[0] ;
        } else {
            return array('id_lang' => 0, 'name' => '', 'iso_code' => '') ;
        }
    }

    /**
     * Gets the current admin config language data .
     *
     * @param array $languages list of valid languages.
     * @param string $iso_code of a specific language .
     * @return array the language data array.
     */
    public static function getCurrentLanguageIsoCode(array $languages, $iso_code)
    {
        foreach ($languages as $language) {
            if ($language['iso_code'] == $iso_code) {
                return $language ;
            }
        }
        return null ;
    }

    /**
     * Checks if data are already initialized in Favizone .
     *
     * @param int $id_lang of a specific language .
     * @return array .
     */
    public function sendCheckInitDone($id_lang = null, $auth_key = null)
    {
        $sender = new FavizoneSender() ;
        $api_data = new FavizoneApi() ;
        if ($auth_key == null) {
            $auth_key = FavizoneConfiguration::get('FAVIZONE_AUTH_KEY', $id_lang);
        }
        $data_to_send =  array("key" => $auth_key) ;
        $result = $sender->postRequest($api_data->getHost(), $api_data->getCheckInitUrl(), $data_to_send) ;
        return $result  ;
    }

    /**
     * Returns the shops that are affected by the current context.
     *
     * @return array list of shop data.
     */
    public static function getContextShops()
    {
        if (_PS_VERSION_ >= '1.5' && Shop::isFeatureActive() && Shop::getContext() !== Shop::CONTEXT_SHOP) {
            if (Shop::getContext() === Shop::CONTEXT_GROUP) {
                return Shop::getShops(true, Shop::getContextShopGroupID()) ;
            } else {
                return Shop::getShops(true) ;
            }
        } else {
            return array(1 => array(
                        'id_shop' => 1,
                        'id_shop_group' => 1,
                    ),
            ) ;
        }
    }

    /**
     * Decodes response data
     *
     * @param $data
     */
    public static function decodeData($data)
    {
        if (_PS_VERSION_ >= '1.5') {
            return Tools::jsonDecode($data, true);
        }
        return json_decode($data, true) ;
    }
}
