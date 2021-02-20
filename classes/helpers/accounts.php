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
 * Helper class for managing account data.
 */
class FavizoneAccount
{

    /**
     *  Constructor
     */
    public function __construct()
    {
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/common.php') ;
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/configuration.php') ;
    }
    /**
     * Collects the account's data to be sent to favizone
     *
     * @param String $email the email inserted in back office input
     * @param array $language the selected language .
     * @return array|mixed
     */
    public function createAccount($email,$password, $language)
    {
        try {
            $context = FavizoneCommonHelper::loadContext();
            $account_data = array();
            $account_data["email"] = $email;
            $account_data["password"]=$password;
            $account_data["cms_name"] = "prestashop";
            $account_data["cms_version"] = FavizoneCommonHelper::prestaShopVersion();
            $account_data["shop_identifier"] = $context->shop->id;
            $ssl = Configuration::get('PS_SSL_ENABLED');
            if (_PS_VERSION_ < '1.5') {
                $url = ($ssl ? _PS_BASE_URL_SSL_ : _PS_BASE_URL_).__PS_BASE_URI__ ;
            } else {
                $url = ($ssl ? 'https://'.$context->shop->domain_ssl : 'http://'.$context->shop->domain).
                        $context->shop->getBaseURI() ;
            }
            $account_data["shop_url"] = $url ;
            $account_data["shop_name"] = _PS_VERSION_ < '1.5' ? Configuration::get('PS_SHOP_NAME'):
                $context->shop->name ;
            $account_data["language_identifier"] = $language['id_lang'] ;
            $account_data["language"] = $language['iso_code'] ;
            $account_data["timezone"] = Configuration::get('PS_TIMEZONE') ;
            //country
            $shop_country_id = Configuration::get('PS_SHOP_COUNTRY_ID') ? Configuration::get('PS_SHOP_COUNTRY_ID') :
                               Configuration::get('PS_COUNTRY_DEFAULT') ;
            $account_data["country"] =  Country::getIsoById($shop_country_id) ;
            //currency
            $shop_default_currency_id = Configuration::get('PS_CURRENCY_DEFAULT') ;
            $account_data["currency"] =  Currency::getCurrencyInstance($shop_default_currency_id)->sign ;
            $api_data = new FavizoneApi() ;
            $account_data["request_url"] =  $api_data->getHost().$api_data->getAddAccountUrl() ;
            $account_data["status"] = "success";

            return $account_data;

        } catch (Exception $exception) {
            $result = array();
            $result["status"] = "error";
            $result["message"] = "account not created";
            return $result;
        }
    }

    public function createConversellAccount()
    {
        try {
            $context = FavizoneCommonHelper::loadContext();
            $sender = new FavizoneSender();
            $api_data = new ConversellApi();
            $data_to_send = array(
                "email"=>"bensalah.yosra732+2020@gmail.com",
                "password" => "med.ali123wp",
                "name" => "my_store",
                "cell" => "25475587",
                "company" => "my_store",
                "shop" => "5e660072e4b0eb71767ac996"
            );
            json_encode($data_to_send);
            $result = $sender->postByCURL("https://us-central1-conversell-258914.cloudfunctions.net/clientRegistration", $data_to_send);
            return $result;
        } catch (Exception $exception) {
        }
    }
}
