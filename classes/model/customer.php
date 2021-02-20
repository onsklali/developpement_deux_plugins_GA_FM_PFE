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

class FavizoneCustomer
{
    /**
     * Getting customer's data.
     *
     * @param $context
     * @param Customer $customer
     * @param $params .
     * @return array
     */
    public function loadCustomerData($context, $customer, $params)
    {
        try {
            $customer_data = array();
            $current_country = Customer::getCurrentCountry($customer->id);
            $languages = Language::getLanguages(true);
            $customer_data['id'] = $customer->id;
            if ($params['cookie']->__get(
                'favizone_connection_identifier_'.$context->shop->id.'_'.
                $context->language->id
            )) {
                $customer_data['session_id'] = $params['cookie']->__get(
                    'favizone_connection_identifier_'.
                    $context->shop->id.'_'.$context->language->id
                );
            } else {
                $customer_data['session_id'] = $customer->id;
            }
            $customer_data['country'] = CountryCore::getNameById((int) $context->language->id, (int) $current_country);
            $customer_data['email'] = $customer->email;
            $customer_data['firstname'] = $customer->firstname;
            $customer_data['lastname'] = $customer->lastname;
            $customer_data['gender'] = $this->getCustomerGender($customer->id_gender, $context);
            $customer_data['languages'] = array();
            foreach ($languages as &$language) {
                array_push($customer_data['languages'], $language['iso_code']);
            }
            //Currency
            $customer_data['currency'] = $context->currency->iso_code;
            return $customer_data;
        } catch (Exception $e) {
            return  array();
        }
    }

    /**
     * Getting customer's data.
     *
     * @param $context
     * @param String $id_customer
     * @param $params
     * @return array
     */
    public function loadCustomerDataById($context, $id_customer, $params)
    {
        $customer = new Customer((int) $id_customer);
        return $this->loadCustomerData($context, $customer, $params);
    }

     /**
     * Getting customer's data.
     *
     * @param $context
     * @param String $id_customer
     * @param $params
     * @return array
     */
    private function getCustomerGender($id_gender, $context)
    {
        /* Backward compatibility */
        if (_PS_VERSION_ < '1.5') {
            if ($id_gender == 2) {
                return 'f';
            }
            return 'h';
        } else {
            $gender = new Gender($id_gender, (int) $context->language->id, (int) $context->shop->id);
            if ($gender->type == 1) {
                return 'f';
            }
            return 'h';
        }
    }
}
