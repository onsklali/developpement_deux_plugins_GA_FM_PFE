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
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Favizone extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        /**
         * Loads class files.
         */
        require_once(_PS_MODULE_DIR_ . '/favizone/classes/helpers/category.php');
        require_once(_PS_MODULE_DIR_ . '/favizone/classes/helpers/searcher.php');
        require_once(_PS_MODULE_DIR_ . '/favizone/classes/helpers/product.php');
        require_once(_PS_MODULE_DIR_ . '/favizone/classes/helpers/common.php');
        require_once(_PS_MODULE_DIR_ . '/favizone/classes/helpers/order.php');
        require_once(_PS_MODULE_DIR_ . '/favizone/classes/constants/api.php');
        require_once(_PS_MODULE_DIR_ . '/favizone/classes/helpers/cart.php');
        require_once(_PS_MODULE_DIR_ . '/favizone/classes/helpers/configuration.php');
        require_once(_PS_MODULE_DIR_ . '/favizone/classes/helpers/accounts.php');
        require_once(_PS_MODULE_DIR_ . '/favizone/classes/model/product.php');
        require_once(_PS_MODULE_DIR_ . '/favizone/classes/model/productupdate.php');
        require_once(_PS_MODULE_DIR_ . 'favizone/classes/helpers/exportXML.php');

        $this->name = 'favizone';
        $this->tab = 'advertising_marketing';
        $this->version = '1.1.6';
        $this->author = 'Favizone Inc';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.4', 'max' => '1.7');
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Personalization for prestashop');
        $this->description = $this->l(
            'Favizone allows e-retailers to increase their sales by recommending relevant products for each clients.'
        );
        $this->warning = $this->l('A Favizone account must be installed for each store and each language.');
        /* Backward compatibility */
        if (_PS_VERSION_ < '1.5') {
            require(_PS_MODULE_DIR_ . $this->name . '/backward_compatibility/backward.php');
        }
        $this->module_key = 'e21d5794842d0be0d7d7789f8b155ae4';
    }

    /**
     * Install Favizone Recommender module.
     *
     * Initializes config, adds custom hooks and registers used hooks.
     * The hook names for PS 1.4 are used here as all superior versions have an hook alias table which they use as a
     * lookup to check which PS 1.4 names correspond to the newer names.
     *
     * @return bool
     * @see Module::install()
     */
    public function install()
    {

        Configuration::updateValue('FAVIZONE_LIVE_MODE', false);
        if (FavizoneCommonHelper::prestaShopVersion() == '1.4') {
            return parent::install()
                //admin hooks
                && $this->registerHook('updateproduct')
                && $this->registerHook('deleteproduct')
                && $this->registerHook('addproduct')
                && $this->registerHook('categoryAddition')
                && $this->registerHook('categoryUpdate')
                && $this->registerHook('categoryDeletion')
                && $this->createExportXmlConfigTbl()
                //front hooks

                && $this->registerHook('createAccount')
                && $this->registerHook('authentication')
                && $this->registerHook('home')
                && $this->registerHook('productfooter')
                && $this->registerHook('top')
                && $this->registerHook('footer')
                && $this->registerHook('extraLeft')
                && $this->registerHook('extraRight')
                && $this->registerHook('shoppingcart')
                && $this->registerHook('cart')
                && $this->registerHook('newOrder');
        } else {
            return parent::install()
                //admin  hooks
                && $this->registerHook('actionObjectUpdateAfter')
                && $this->registerHook('actionObjectDeleteAfter')
                && $this->registerHook('actionObjectAddAfter')
                && $this->createExportXmlConfigTbl()
                //front  hooks
                && $this->registerHook('actionAuthenticationBefore')
                && $this->registerHook('actionCustomerAccountAdd')
                && $this->registerHook('actionValidateOrder')
                && $this->registerHook('actionAuthentication')
                && $this->registerHook('actionCartSave')
                && $this->registerHook('actionSearch')
                && $this->registerHook('displayHome')
                && $this->registerHook('displayShoppingCartFooter')
                && $this->registerHook('displayTop')
                && $this->registerHook('displayFooter')
                && $this->registerHook('displayFooterProduct')
                && $this->registerHook('displayLeftColumnProduct')
                && $this->registerHook('displayRightColumnProduct')
                && $this->registerHook('displayHeader');
        }
    }


    /**
     * Create Export Xml Config table | Favizone Recommender module.
     * @return bool
     */
    public function createExportXmlConfigTbl()
    {
        try {
            $db = Db::getInstance();
            $query = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fz_export_xml_config (
                `token` VARCHAR(255) NOT NULL,
                `id_shop` VARCHAR(256) NOT NULL,
                `tstamp`  INTEGER UNSIGNED NOT NULL,
                PRIMARY KEY(token)
                )';
            $db->Execute($query);
            return true;
        } catch (Exception $e) {
            echo $e;
            return false;
        }
    }

    /**
     * Uninstalls  Favizone Recommender module.
     *
     * Removes used config values. No need to un-register any hooks,
     * as that is handled by the parent class.
     *
     * @return bool
     * @see Module::uninstall()
     */
    public function uninstall()
    {
        Configuration::deleteByName('FAVIZONE_LIVE_MODE');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'fz_export_xml_config');
        return parent::uninstall()
            && FavizoneConfiguration::removeConfigData();
        //&& !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'fz_export_xml_config');
    }

    /**
     * Content included in PrestaShop backoffice for module configuration.
     *
     * @return string The HTML to output.
     * @internal param array $params
     */
    public function getContent()
    {
        $smarty = $this->getSmarty();
        if (is_null($smarty)) {
            return "";
        }
        $helper = new FavizoneCommonHelper();
        $context = $helper->loadContext();
        $languages = Language::getLanguages(true, $context->shop->id);
        $language_id = (int)Tools::getValue('language_id', 0);
        $current_language = FavizoneCommonHelper::getCurrentLanguage($languages, $language_id);
        $post_fields = array(
            $this->name . '_update' => 'undefined'
        );
        $post_fields['favizone_install_message'] = sprintf(
            $this->l('You will manage Favizone for your shop : %s'),
            $current_language['name']
        );
        $favizone_helper = new FavizoneCommonHelper();
        $context = $favizone_helper->loadContext();
        $language_id = (int)Tools::getValue('language_id', 0);
        $languages = Language::getLanguages(true, $context->shop->id);
        $current_language = FavizoneCommonHelper::getCurrentLanguage($languages, $language_id);

        /*Export Order States */
        $favizone_order_manager=new FavizoneOrderManager();
        $orders_status=$favizone_order_manager->getOrderStates($current_language[id_lang]);
        $smarty->assign("favizone_orders_statues", $orders_status);

        $id_shop = (int)Context::getContext()->shop->id;

        $export_xml_manager = new FavizoneExportXML();
        $token = $export_xml_manager->getToken($id_shop);
        $res = $export_xml_manager->exportXmlProductsData(
            $context,
            $current_language['id_lang'],
            $context->shop->id,
            $current_language['iso_code']
        );
        /*Export XML URLS*/
        $export_xml_manager = new FavizoneExportXML();
        $token = $export_xml_manager->getToken($context->shop->id);
        $cat = $this->getProductsCatalog();
        $post_fields['catalog'] = $cat;
        $listOfPhpUrl = array();
        if (!is_null($token)) {
            $lg = $context->shop->id;
            if (_PS_VERSION_ < '1.5') {
                $lg = (int)$this->context->shop->id;
                $isoCode = Currency::getCurrent()->iso_code;
            }
            $isoCode = $current_language['iso_code'];
            $openFile = _PS_BASE_URL_ . __PS_BASE_URI__ .
                'modules/favizone/favizone-export-cataloge-' . $isoCode . '-' . $lg . '.xml';
            $downloadFile = _PS_BASE_URL_ . __PS_BASE_URI__ .
                'modules/favizone/download.php?lang=' . $isoCode . '&idShop=' . $lg;
            $sendtofavizone = _PS_BASE_URL_ . __PS_BASE_URI__ .
                'modules/favizone/sendtofavizone.php';
            if ($this->urlExists($openFile)) {
                array_push($listOfPhpUrl, array('url' => _PS_BASE_URL_ . __PS_BASE_URI__ .
                    'modules/favizone/generateXMLProducts.php?token=' . $token . '&lang=' . $isoCode . '&idShop=' . $lg,
                    'country' => $current_language['language_code'],
                    'urlDownload' => $downloadFile,
                    'urlOpen' => $openFile,
                    'sendtofavizone' => $sendtofavizone
                ));
            } else {
                array_push($listOfPhpUrl, array('url' => $openFile,
                        'country' => $current_language['language_code'],
                        'urlDownload' => 'no_file_found',
                        'urlOpen' => 'no_file_found',
                        'sendtofavizone' => 'no_file_found')
                );
            }
        }
        $post_fields[$this->name . '_urlsPhp'] = $listOfPhpUrl;
        /*End Export XML URLS*/
        $post_fields[$this->name . '_ab_test'] = Tools::getValue(
            'ab_test',
            FavizoneConfiguration::get(
                'FAVIZONE_AB_TEST',
                $current_language['id_lang']
            )
        );
        if (_PS_VERSION_ >= '1.5' && Shop::getContext() !== Shop::CONTEXT_SHOP) {
            $post_fields[$this->name . '_disable_submit'] = true;
            $post_fields[$this->name . '_error_message'] = $this->l('Please choose a shop to install Favizone for.');
        } else {
            $post_fields[$this->name . '_auth_key'] = Tools::getValue(
                'auth_key',
                FavizoneConfiguration::get(
                    'FAVIZONE_AUTH_KEY',
                    $current_language['id_lang']
                )
            );
        }
        $post_fields['modle_path'] = $this->_path;
        $post_fields['ShopId'] = (int)Context::getContext()->shop->id;
        $post_fields['lang'] = $current_language['name'];
        /** Adding data to smarty **/
        $smarty->assign($post_fields);
        /** POST calls **/
        if (Tools::getValue('ajax')) {
            switch (Tools::getValue("indicator")) {
                case "analyse-data":
                    $application_key = Tools::getValue('application_key');
                    $reference = Tools::getValue('reference');
                    //insert application_key && reference in firestore
                    $name_shop = (String)Context::getContext()->shop->name;
                    $fs = new FavizoneCommonHelper();
                    $fs->oldDocument($name_shop,['fz_application_key' => $application_key, 'fz_reference' => $reference]);
                    FavizoneConfiguration::updateValue(
                        'FAVIZONE_AUTH_KEY',
                        $application_key,
                        $current_language['id_lang']
                    );
                    FavizoneConfiguration::updateValue('FAVIZONE_AB_DIFF', 0, $current_language['id_lang']);
                    FavizoneConfiguration::updateValue('FAVIZONE_AB_TEST', 'false', $current_language['id_lang']);
                    $check_result = $helper->sendCheckInitDone($current_language['id_lang'], $application_key);
                    $check_data = FavizoneCommonHelper::decodeData($check_result);
                    if (($check_data['response'] == 'authorized') && ($check_data['result'] == 'Zy,]Jm9QkJ')) {
                        //synchronizing orders
                        $order_sender = new FavizoneOrderManager();
                        $order_sender->initOrders(
                            $context,
                            $current_language['id_lang'],
                            $application_key
                        );
                    }
                    echo json_encode(array("status" => "success"));
                    die();
                case "category-sync":
                    $category_sender = new FavizoneCategoryManager();
                    $category_sender->sendCategoriesData(
                        $context->shop->id,
                        $context->shop->id_shop_group,
                        $current_language
                    );
                    echo json_encode(array("status" => "success"));
                    die();
            }
        }
        if (Tools::getValue($this->name . '_account_email')) {
            header('Content-Type: application/json');
            $result = array();
            $email = Tools::getValue($this->name . '_account_email');
            $password = Tools::getValue($this->name . '_account_password');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $favizone_account = new FavizoneAccount();
                $result = $favizone_account->createAccount($email, $password, $current_language);
                $smarty->assign("result", $result);
                $id_shop = (int)Context::getContext()->shop->id;

                $export_xml_manager = new FavizoneExportXML();
                $token = $export_xml_manager->getToken($id_shop);
                if (is_null($token)) {
                    if (_PS_VERSION_ > '1.6' && Shop::getContext() == Shop::CONTEXT_SHOP) {
                        $token = $export_xml_manager->createToken($id_shop);
                    } elseif (_PS_VERSION_ <= '1.6' && _PS_VERSION_ >= '1.5' && Shop::getContext() !== Shop::CONTEXT_SHOP) {
                        $token = $export_xml_manager->createToken($id_shop);
                    } else {
                        $token = $export_xml_manager->createToken14($id_shop);
                    }
                }
                $fs = new FavizoneCommonHelper();
                $fs->newDocument($result["shop_name"], ['fz_shop_id' => $id_shop,'export_xml_token' => $token,'user_id' => "",'shop_url' => $result["shop_url"]]);
            } else {
                $result["status"] = "error";
                $result["message"] = $this->l('Please check your information ');
            }
            echo json_encode($result);
            die();
        }
        /** End POST calls **/

        if (Tools::isSubmit('favizone_submit_Bot_state')) {
            $name_shop = (String)Context::getContext()->shop->name;
            $fs = new FavizoneCommonHelper();
            unset($_POST[array_search($_POST["favizone_submit_Bot_state"], $_POST)]);
            unset($_POST[array_search($_POST["tab"], $_POST)]);
            $fs->oldDocument($name_shop,['mapping_Order_states' => $_POST]);
        }

        if (Tools::isSubmit($this->name . '_submit_recommendor')) {

            $favizone_helper = new FavizoneCommonHelper();
            $comparison = strcmp(Tools::getValue($this->name . '_ab_test'), 'true');
            if ($comparison == 0) {
                if (0 != strcmp(
                        Tools::getValue(
                            $this->name . '_ab_test'
                        ),
                        FavizoneConfiguration::get(
                            'FAVIZONE_AB_TEST',
                            $current_language['id_lang']
                        )
                    )) {
                    $favizone_helper->sendABTestStatus("init", $current_language['id_lang']);
                }
                FavizoneConfiguration::updateValue('FAVIZONE_AB_TEST', 'true', $current_language['id_lang']);
            } else {
                if (0 != strcmp(
                        Tools::getValue($this->name . 'ab_test'),
                        FavizoneConfiguration::get(
                            'FAVIZONE_AB_TEST',
                            $current_language['id_lang']
                        )
                    )) {
                    $favizone_helper->sendABTestStatus("end", $current_language['id_lang']);
                    FavizoneConfiguration::updateValue('FAVIZONE_AB_DIFF', 0, $current_language['id_lang']);
                }
                FavizoneConfiguration::updateValue(
                    'FAVIZONE_AB_TEST',
                    'false',
                    $current_language['id_lang']
                );
            }
            $result_data = array();
            $result_data[$this->name . '_ab_test'] = Tools::getValue($this->name . '_ab_test');
            $result_data['favizone_success_message'] = $this->l("A/B test status was updated successfully");
            return $this->getDispalyView();
        }
        /**End Form submit**/


        return $this->getDispalyView();
    }

    /**
     * Getting smarty object
     *
     * @return Smarty|Smarty_Data
     */
    protected function getSmarty()
    {
        if (!empty($this->smarty)
            && method_exists($this->smarty, 'assign')
        ) {
            return $this->smarty;
        } elseif (!empty($this->context->smarty)
            && method_exists($this->context->smarty, 'assign')
        ) {
            return $this->context->smarty;
        }

        return null;
    }

    public function getProductsCatalog()
    {
        $favizone_helper = new FavizoneCommonHelper();
        $context = $favizone_helper->loadContext();
        $language_id = (int)Tools::getValue('language_id', 0);
        $languages = Language::getLanguages(true, $context->shop->id);
        $current_language = FavizoneCommonHelper::getCurrentLanguage($languages, $language_id);
        // $orders_status=$favizone_order_manager->getOrderStates($current_language[id_lang]);
        //$smarty->assign("favizone_order_state", $orders_status);
        $id_shop = (int)Context::getContext()->shop->id;

        $export_xml_manager = new FavizoneExportXML();
        $token = $export_xml_manager->getToken($id_shop);
        $res = $export_xml_manager->exportXmlProductsData(
            $context,
            $current_language['id_lang'],
            $context->shop->id,
            $current_language['iso_code']
        );
        $export_xml_manager->exportXML($res,
            $context->shop->id,
            $current_language['iso_code'],
            'Catalog.xml');
    }

    protected function urlExists($url)
    {
        $headers = get_headers($url);
        return stripos($headers[0], "200 OK") ? true : false;
    }

    /**
     * Getting display view
     *
     */
    protected function getDispalyView()
    {
        if (_PS_VERSION_ < '1.6') {
            $smarty = $this->getSmarty();
            $smarty->assign($this->getConfigFormValue());
            $output = '';
            if (_PS_VERSION_ < '1.5') {
                $output .= $this->display(__FILE__, 'views/templates/admin/favizone-admin-ps14.tpl');
            } else {
                $this->context->controller->addJS($this->_path . 'views/js/internal-sender.js');
                $this->context->controller->addJS($this->_path . 'views/js/Authentification.js');
                $this->context->controller->addCss($this->_path . 'views/css/favizone-admin-custom-style.css');
            }
            $output .= $this->display(__FILE__, 'views/templates/admin/favizone-admin-config-custom.tpl');
            $output .= $this->renderExportTable();

            return $output;
        }
        $this->context->controller->addJS($this->_path . 'views/js/internal-sender.js');
        $this->context->controller->addJS($this->_path . 'views/js/creating-proc.js');
        $this->context->controller->addJS($this->_path . 'views/js/creation.js');
        $this->context->controller->addJS($this->_path . 'views/js/den.js');
        $this->context->controller->addJS($this->_path . 'views/js/ax.js');
        $this->context->controller->addJS($this->_path . 'views/js/senders-process.js');
        $output = $this->display(__FILE__, 'views/templates/admin/favizone-admin-config.tpl');
        $output .= $this->renderForm();
        $output .= $this->renderMatchingTable();
        $output .= $this->renderExportTable();
        return $output;
    }

    /**
     * Getting the values required for the admin form
     *
     */
    protected function getConfigFormValue($current_language = null, $languages = null, $favizone_auth_key = null)
    {
        if ($current_language == null || $languages == null) {
            $favizone_helper = new FavizoneCommonHelper();
            $context = $favizone_helper->loadContext();
            $language_id = (int)Tools::getValue('language_id', 0);
            $languages = Language::getLanguages(true, $context->shop->id);
            $current_language = FavizoneCommonHelper::getCurrentLanguage($languages, $language_id);
        }
        $helper = new FavizoneCommonHelper();
        $context = $helper->loadContext();
        $post_fields = array();
        $post_fields[$this->name . '_current_language'] = $current_language;
        $post_fields[$this->name . '_languages'] = $languages;
        $post_fields[$this->name . '_current_language_identifier'] = $current_language['id_lang'];
        $post_fields[$this->name . '_current_language'] = $current_language['id_lang'];
        if ((!isset($favizone_auth_key)) || ((isset($favizone_auth_key)) && $favizone_auth_key == '')) {
            $post_fields[$this->name . '_account_email'] = "";
            //if (FavizoneCommonHelper::prestaShopVersion() != '1.4') {
            $post_fields[$this->name . '_account_email'] = $context->employee->email;
            $post_fields[$this->name . '_account_password']=$context->employee->passwd;
            //}
        } else {
            $ab_test = Tools::getValue(
                'ab_test',
                FavizoneConfiguration::get(
                    'FAVIZONE_AB_TEST',
                    $current_language['id_lang']
                )
            );
            $post_fields[$this->name . '_ab_test'] = $ab_test;
        }

        $post_fields[$this->name . '_access_token'] = "";
        $post_fields[$this->name . '_link'] = "";
        // $post_fields[$this->name.'urlsPhp'] = "" ;


        return $post_fields;
    }

    /**
     * Creating the structure of the form.
     */
    public function renderExportTable()
    {
        $favizone_helper = new FavizoneCommonHelper();
        $context = $favizone_helper->loadContext();
        $language_id = (int)Tools::getValue('language_id', 0);
        $languages = Language::getLanguages(true, $context->shop->id);
        $current_language = FavizoneCommonHelper::getCurrentLanguage($languages, $language_id);
        $favizone_auth_key = FavizoneConfiguration::get('FAVIZONE_AUTH_KEY', $current_language['id_lang']);
        if ($favizone_auth_key || $favizone_auth_key != '') {
            if (_PS_VERSION_ < '1.6') {
                if (_PS_VERSION_ < '1.5') {
                    return $this->display(__FILE__, 'views/templates/admin/xml.tpl');
                }
                return $this->display(__FILE__, 'views/templates/admin/xml.tpl');
            }
            return $this->display(__FILE__, 'views/templates/admin/xml16.tpl');
        }
    }

    /**
     * Creating the structure of the form.
     */
    public function renderForm()
    {
        $favizone_helper = new FavizoneCommonHelper();
        $context = $favizone_helper->loadContext();
        $language_id = (int)Tools::getValue('language_id', 0);
        $languages = Language::getLanguages(true, $context->shop->id);
        $current_language = FavizoneCommonHelper::getCurrentLanguage($languages, $language_id);
        $favizone_auth_key = FavizoneConfiguration::get('FAVIZONE_AUTH_KEY', $current_language['id_lang']);
        $helper = new HelperForm();
        if ((!isset($favizone_auth_key)) || ((isset($favizone_auth_key)) && $favizone_auth_key == '')) {
            $fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Settings'),
                        'icon' => 'icon-cogs'
                    ),
                    'name' => 'submit-form',
                    'input' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Manage accounts'),
                            'name' => 'favizone_current_language',
                            'required' => true,
                            'desc' => $this->l('Select the account that you want to configure'),
                            'options' => array(
                                'query' => $this->context->controller->getLanguages(),
                                'id' => 'id_lang',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Please add your email'),
                            'name' => 'favizone_account_email',
                            'required' => true,
                            'class' => 'fixed-width-xxl',
                            'desc' => $this->l('With this email, you will manage your account in favizone.')
                        ), array(
                            'type' => 'password',
                            'label' => $this->l('Please add your password'),
                            'name' => 'favizone_account_password',
                            'required' => true,
                            'class' => 'fixed-width-xxl',
                            'desc' => $this->l('With this password, you will manage your account in favizone.')
                        )
                    ),
                    'submit' => array(
                        'name' => $this->name . '_submit_recommendor',
                        'title' => $this->l('Submit'),
                        'icon' => 'process-icon-save',
                    ),'buttons' => array(
                        array(
                            'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite($this->name),
                            'title' => $this->l('Cancle'),
                            'icon' => 'process-icon-cancel',
                            'name' => 'favizone_btn'
                        )
                    )
                ),
            );
        } else {


            $fields_form = array(
                'form' =>
                    array(
                        'legend' => array(
                            'title' => $this->l('Settings'),
                            'icon' => 'icon-cogs'
                        ),
                        'input' => array(
                            array(
                                'type' => 'select',
                                'label' => $this->l('Manage accounts'),
                                'name' => 'favizone_current_language',
                                'required' => true,
                                'desc' => $this->l('Select the account that you want to configure'),
                                'options' => array(
                                    'query' => $this->context->controller->getLanguages(),
                                    'id' => 'id_lang',
                                    'name' => 'name'
                                )
                            ),
                            array(
                                'type' => 'text',
                                'label' => $this->l('Please add a bot name '),
                                'name' => 'favizone_bot_name',
                                'required' => true,
                                'class' => 'fixed-width-xxl',
                                'desc' => $this->l('With this name, you will manage your Bot in Conversell.'),
                                'value' => $this->l('Marry')
                            ),
                        ),
                        'submit' => array(
                            'name' => $this->name . '_submit_Bot',
                            'title' => $this->l('Submit'),
                            'icon' => 'process-icon-save',
                        )

                    )


            );
        }


        $helper->table = $this->table;
        $helper->identifier = $this->identifier;
        $helper->currentIndex = $_SERVER['REQUEST_URI'];
        //$helper->token = Tools::getAdminTokenLite('AdminModules') ;
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValue($current_language, $languages, $favizone_auth_key),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function renderMatchingTable()
    {
        $smarty = $this->getSmarty();
        $favizone_order_manager = new FavizoneOrderManager();

        $favizone_helper = new FavizoneCommonHelper();
        $context = $favizone_helper->loadContext();
        $language_id = (int)Tools::getValue('language_id', 0);
        $languages = Language::getLanguages(true, $context->shop->id);
        $current_language = FavizoneCommonHelper::getCurrentLanguage($languages, $language_id);
        // $orders_status=$favizone_order_manager->getOrderStates($current_language[id_lang]);
        //$smarty->assign("favizone_order_state", $orders_status);
        $id_shop = (int)Context::getContext()->shop->id;

        $export_xml_manager = new FavizoneExportXML();
        $token = $export_xml_manager->getToken($id_shop);
        $favizone = $this->exportXmlProductsData($token, $current_language, $id_shop);
        $favizone_auth_key = FavizoneConfiguration::get('FAVIZONE_AUTH_KEY', $current_language['id_lang']);
        if ($favizone_auth_key || $favizone_auth_key != '') {
            if (_PS_VERSION_ < '1.6') {
                if (_PS_VERSION_ < '1.5') {
                    return $this->display(__FILE__, 'views/templates/admin/match.tpl');
                }
                return $this->display(__FILE__, 'views/templates/admin/match.tpl');
            }
            return $this->display(__FILE__, 'views/templates/admin/match16.tpl');
        }
    }

    public function exportXmlProductsData($access_token, $lang, $idShop)
    {
        $helper = new FavizoneCommonHelper();
        $context = $helper->loadContext();
        $languages = Language::getLanguages(true, $idShop);
        $current_language = FavizoneCommonHelper::getCurrentLanguageIsoCode($languages, $lang);
        $export_xml_manager = new FavizoneExportXML();
        $validateTokenAndShop = $export_xml_manager->validateTokenAndShop($access_token, $idShop);

        if ($validateTokenAndShop) {
            if (isset($current_language)) {
                $res = $export_xml_manager->exportXmlProductsData(
                    $context,
                    $current_language['id_lang'],
                    $idShop,
                    $current_language['iso_code']
                );
                return $res;
                //echo "Exporting catalogue \"" . $lang . "\" is successful";
            } else {
                echo "Error : Lang \"" . $lang . "\" not found ";
            }
        } else {
            echo "error : Invalid access token '" . $access_token . "' OR Invalid Shop '" . $idShop . "'";
        }
    }

    /**
     * Hook called when a product is deleted (Prestashop 1.4).
     ** @param array $params
     */
    public function hookDeleteProduct($params)
    {
        if (isset($params['product']) && FavizoneCommonHelper::prestaShopVersion() == "1.4") {
            $this->hookActionObjectDeleteAfter(array('object' => $params['product']));
        }
    }

    /**
     * Hook for adding content to the top of  pages (prestashop 1.4) .
     * @param $params
     * @return string The HTML to output
     */
    public function hookTop($params)
    {
        return $this->hookDisplayTop($params);
    }

    /**
     * Hook for adding content to the top of  pages.
     * @param array $params
     * @return string The HTML to output
     */
    public function hookDisplayTop($params)
    {
        $smarty = $this->getSmarty();
        if (!$smarty) {
            return "";
        }
        if (FavizoneCommonHelper::validateContext()) {
            $cookie = $params['cookie'];
            $html_content = "";
            $js_content = "";
            $events = array();
            $should_send = false;
            $helper = new FavizoneCommonHelper();
            $favizone_api = new FavizoneApi();
            $this->context = $helper->loadContext();
            $tagging_product = new FavizoneTaggingUpdateProduct();
            $session_identifier = $helper->generateSessionIdentifier($params);
            $testing_version = $helper->getTestingVersion();
            //initializing  data in smarty
            $smarty->assign("favizone_module_path", $this->_path);
            $smarty->assign("click", $helper->generateTrackEvent($params, "click"));
            $smarty->assign('post_api', $favizone_api->getSendEventUrl());
            $smarty->assign('cart_layer_identifier', $helper->getStandardElement("product"));
            $cart_layer_identifier = $helper->getStandardElement("product");
            /*initializing currency params in smarty*/
            $smarty->assign('favizone_currency_default', (int)Configuration::get('PS_CURRENCY_DEFAULT'));
            $smarty->assign('favizone_current_currency', $this->context->currency->id);

            if (Tools::getValue('favizone_preview') && Tools::getValue('favizone_preview') == "true") {
                $cookie->__set('favizone_preview', true);
            }
            if (Tools::getValue('favizone_preview') && Tools::getValue('favizone_preview') == "false") {
                $cookie->__unset('favizone_preview');
            }
            if ($helper->isController('order')) {
                if ($params['cookie']->__get('id_customer')
                    && !$params['cookie']->__get(
                        'favizone_custom_logged_' . $this->context->shop->id . '_' . $this->context->language->id
                    )) {
                    $params['cookie']->__set(
                        'favizone_custom_logged_' . $this->context->shop->id . '_' . $this->context->language->id,
                        true
                    );
                }
            }
            if ($this->context->language
                && $params['cookie']->__get(
                    'favizone_custom_logged_' . $this->context->shop->id . '_' . $this->context->language->id
                )
                && !is_null($session_identifier)) {
                $customer = new Customer($params['cookie']->id_customer);
                if (Validate::isLoadedObject($customer)) {
                    $should_send = true;
                    $event = $helper->generateTrackEvent($params, "loginUser");
                    array_push($events, $event);
                    $favizone_customer = new FavizoneCustomer();
                    $custom_event_value = $favizone_customer->loadCustomerData($this->context, $customer, $params);
                    $smarty->assign('custom_event_value', $custom_event_value);
                    $params['cookie']->__unset(
                        'favizone_custom_logged_' . $this->context->shop->id . '_' . $this->context->language->id
                    );
                }
            }
            if (isset($params['cookie']->favizone_visit) && !is_null($session_identifier)) {
                $should_send = true;
                $event = $helper->generateTrackEvent($params, "visit");
                array_push($events, $event);
                $params['cookie']->__unset("favizone_visit");
            }
            if ($helper->isController('product')) {
                $favizone_product_manager = new FavizoneProductManager();
                if ($favizone_product_manager->getProductById(Tools::getValue('id_product'))) {
                    $should_send = true;
                    $id_product = (int)Tools::getValue('id_product');
                    $product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);
                    if (Validate::isLoadedObject($product)) {
                        $product = $tagging_product->loadProductData($this->context, $product);
                        unset($product['wholesale_price']);
                        $smarty->assign('favizone_product', $product);
                        $event = $helper->generateTrackEvent($params, "viewProduct");
                        $smarty->assign('gender', 'product_cart');
                        $smarty->assign('element_identifier', $helper->getStandardElement("product"));
                        array_push($events, $event);
                    }
                }

                /**
                 * Searching for favizone_rec
                 */
                if (Tools::getValue('favizone_rec')) {
                    $should_send = true;
                    //preparing events data
                    $event = $helper->generateTrackEvent($params, "clickBot", Tools::getValue('favizone_rec'));
                    array_push($events, $event);
                }
            }
            if ($helper->isController('category')) {
                $should_send = true;
                //preparing to get recs
                $favizone_category_manager = new FavizoneCategoryManager();
                $favizone_common_helper = new FavizoneCommonHelper();
                $category = $favizone_category_manager->getCategoryData(
                    Tools::getValue('id_category'),
                    $this->context->shop->id,
                    $this->context->language->id
                );
                $product_data = $favizone_common_helper->getRenderingCanal($params, "category", $category);
                $product_data['post_data']['category'] = $category['idCategory'];
                $smarty->assign('url', $product_data['url']);
                $smarty->assign('post_data', $product_data['post_data']);
                $smarty->assign('gender', 'category');
                $smarty->assign('element_identifier', $helper->getStandardElement("category"));
                $smarty->assign('favizone_category', $category);
                $smarty->assign("favizone_module_path", $this->_path);
                if ($testing_version == "B" || $testing_version == "N") {
                    $html_content .= $this->display(__FILE__, 'views/templates/hook/favizone-hidden-element.tpl');
                }
                //preparing events data
                $event = $helper->generateTrackEvent($params, "viewCategory", '', $category['idCategory']);
                array_push($events, $event);
                $smarty->assign('custom_event_key', 'viewCategory');
                $smarty->assign('custom_event_value', $category['idCategory']);
            }
            if ($helper->isController('search')) {
                $should_send = true;
                $favizone_common_helper = new FavizoneCommonHelper();
                $product_data = $favizone_common_helper->getRenderingCanal($params, "search");
                $smarty->assign('url', $product_data['url']);
                $smarty->assign('post_data', $product_data['post_data']);
                $smarty->assign('gender', 'search');
                $smarty->assign('element_identifier', $helper->getStandardElement("search"));
                $smarty->assign("favizone_module_path", $this->_path);
                if ($testing_version == "B" || $testing_version == "N") {
                    $html_content .= $this->display(__FILE__, 'views/templates/hook/favizone-hidden-element.tpl');
                }
                $event = $helper->generateTrackEvent($params, "doSearch", Tools::getValue('search_query'));
                array_push($events, $event);
                $smarty->assign('custom_event_key', 'doSearch');
                $smarty->assign('custom_event_value', Tools::getValue('search_query'));
            }
            /**
             * Searching for widget  tag
             */
            if (Tools::getValue('favizone_widget_email')) {
                $should_send = true;
                //preparing events data
                $event = $helper->generateTrackEvent($params, "clickWidget", Tools::getValue('favizone_widget_email'));
                array_push($events, $event);
            }
            //Searching for search engine keywords
            if (isset($_SERVER['HTTP_REFERER']) && Tools::strlen($_SERVER['HTTP_REFERER']) > 0) {
                $keywords = FavizoneKeywordSearcher::getKeywords($_SERVER['HTTP_REFERER']);
                if ($keywords && Tools::strlen($keywords) > 0) {
                    $should_send = true;
                    //preparing events data
                    $event = $helper->generateTrackEvent($params, "searchEngine", $keywords);
                    array_push($events, $event);
                    $smarty->assign('search_engine', true);
                    $smarty->assign('search_engine_value', $keywords);
                }
            }
            /**
             * Searching for campaign  tag
             */
            if (Tools::getValue('favizone')) {
                $should_send = true;
                //preparing events data
                $event = $helper->generateTrackEvent($params, "searchCampaign", urldecode(Tools::getValue('favizone')));
                array_push($events, $event);
                $smarty->assign('search_campaign', 'campaign');
                $smarty->assign('search_campaign_value', Tools::getValue('favizone'));
            }
            /**
             * Searching for facebook user id
             */
            if (Tools::getValue('fz_p')) {
                $smarty->assign('favizone_facebook_profile', Tools::getValue('fz_p'));
            }
            /** Adding  js files section **/
            if (FavizoneCommonHelper::prestaShopVersion() == '1.4') {
                $smarty->assign('favizone_backward', true);
            } else {
                $this->context->controller->addJS($this->_path . 'views/js/favizone-helper.js');
                $this->context->controller->addJS($this->_path . 'views/js/favizone-auto-appender.js');
                $this->context->controller->addJS($this->_path . 'views/js/favizone-tracker.js');
            }
            /** End adding  js files section **/

            /**Post request is not always called**/
            $html_content .= $this->display(__FILE__, 'views/templates/hook/favizone-top-files.tpl');
            if ($should_send == true) {
                $smarty->assign('key', FavizoneConfiguration::get('FAVIZONE_AUTH_KEY'));
                $smarty->assign('events', $events);
                $smarty->assign('session', $session_identifier);
                $html_content .= $this->display(__FILE__, 'views/templates/hook/favizone-top-element.tpl');
            }

            return $js_content . $html_content;
        }
        return "";
    }

    /**
     * Hook called before a customer successfully signs in
     * @param array $params
     */
    public function hookActionAuthenticationBefore($params)
    {
        var_dump("It worked before aut");
        Hook::exec('actionAuthentication');
    }

    /**
     * Hook called in case of Successful customer authentication
     * @param array $params
     */
    public function hookActionAuthentication($params)
    {
        if (FavizoneCommonHelper::validateContext()) {
            $context = FavizoneCommonHelper::loadContext();
            //to be removed once login event has been sent
            $params['cookie']->__set(
                'favizone_custom_logged_' . $this->context->shop->id . '_' . $this->context->language->id,
                true
            );
            $params['cookie']->__set('favizone_logged_event_' . $context->shop->id . '_' . $context->language->id, true);
        }

        var_dump("It worked" . $context->shop->id . '_' . $context->language->id);
    }

    /**
     * Hook called in case of customer registration (prestashop 1.4) .
     * @param array $params
     */
    public function hookCreateAccount($params)
    {
        $this->hookActionCustomerAccountAdd($params);
    }

    /**
     * Hook called in case of customer registration
     * @param array $params
     */
    public function hookActionCustomerAccountAdd($params)
    {
        if (FavizoneCommonHelper::validateContext()) {
            $context = FavizoneCommonHelper::loadContext();
            //to be removed once login event has been sent
            $params['cookie']->__set('favizone_custom_logged_' . $context->shop->id . '_' . $context->language->id, true);
        }
    }

    /**
     * Hook called in case of Successful customer authentication (prestashop 1.4) .
     * @param array $params
     */
    public function hookAuthentication($params)
    {
        if (FavizoneCommonHelper::validateContext()) {
            $context = FavizoneCommonHelper::loadContext();
            //to be removed once login event has been sent
            $params['cookie']->__set(
                'favizone_custom_logged_' . $this->context->shop->id . '_' . $this->context->language->id,
                true
            );
            $params['cookie']->__set('favizone_logged_event_' . $context->shop->id . '_' . $context->language->id, true);
        }
    }

    /**
     * Hook called right after a cart creation or update.
     * @param array $params
     * @return Boolean
     */
    public function hookActionCartSave($params)
    {
        if (FavizoneCommonHelper::validateContext()) {
            $favizone_cart_manager = new FavizoneCartManager();
            $favizone_cart_manager->sendCartData($params);
        }
        return true;
    }

    /**
     * Hook called  after a cart creation or update (prestashop 1.4) .
     * @param array $params
     * @return Boolean
     */
    public function hookCart($params)
    {
        if (FavizoneCommonHelper::validateContext()) {
            $favizone_cart_manager = new FavizoneCartManager();
            $favizone_cart_manager->sendCartData($params);
        }
        return true;
    }

    /**
     * Hook called during the new order creation process, right after it has been created. (prestashop 1.4)
     * @param array $params
     * @return Boolean
     */
    public function hookNewOrder($params)
    {
        return $this->hookActionValidateOrder($params);
    }

    /**
     * Hook called during the new order creation process.
     * @param array $params
     * @return Boolean
     */
    public function hookActionValidateOrder($params)
    {
        if (FavizoneCommonHelper::validateContext()) {
            $helper = new FavizoneCommonHelper();
            $this->context = $helper->loadContext();
            $favizone_order_manager = new FavizoneOrderManager();
            $favizone_order_manager->sendToOrderData($this->context, $params);
        }
        return true;
    }

    /**
     * Hook for appending content to the home page (prestashop 1.4).
     * @param array $params
     * @return string The HTML to output
     */
    public function hookHome($params)
    {
        return $this->hookDisplayHome($params);
    }

    /**
     * Hook for appending content to the home page.
     * @param array $params
     * @return string The HTML to output
     */
    public function hookDisplayHome($params)
    {
        if (FavizoneCommonHelper::validateContext()) {
            $smarty = $this->getSmarty();
            if (is_null($smarty)) {
                return "";
            }
            $favizone_common_helper = new FavizoneCommonHelper();
            $test_version = $favizone_common_helper->getTestingVersion();
            if ($test_version == "B" || $test_version == 'N') {
                $home_data = $favizone_common_helper->getRenderingCanal($params, "home");
                $smarty->assign('url', $home_data['url']);
                $smarty->assign('post_data', $home_data['post_data']);
                /*initializing currency params in smarty*/
                $smarty->assign("click", "");
                $smarty->assign('favizone_currency_default', (int)Configuration::get('PS_CURRENCY_DEFAULT'));
                $smarty->assign('favizone_current_currency', $this->context->currency->id);
                $smarty->assign('cart_layer_identifier', "");
                $smarty->assign("favizone_module_path", $this->_path);
                $html = $this->display(__FILE__, 'views/templates/hook/favizone-hidden-element.tpl');
                $html .= $this->display(__FILE__, 'views/templates/hook/favizone-home-element.tpl');
                return $html;
            }
        }
        return "";
    }

    /**
     *
     * @param array $params
     * @return string The HTML to output
     */
    public function hookProductFooter($params)
    {
        if (FavizoneCommonHelper::validateContext()) {
            $smarty = $this->getSmarty();
            if (is_null($smarty)) {
                return "";
            }
            $html = "";
            $favizone_common_helper = new FavizoneCommonHelper();
            $test_version = $favizone_common_helper->getTestingVersion();
            if ($test_version == "B" || $test_version == 'N') {
                $product_data = $favizone_common_helper->getRenderingCanal($params, "product");
                $smarty->assign('url', $product_data['url']);
                $smarty->assign('post_data', $product_data['post_data']);
                /*initializing currency params in smarty*/
                $smarty->assign('favizone_currency_default', (int)Configuration::get('PS_CURRENCY_DEFAULT'));
                $smarty->assign('favizone_current_currency', $this->context->currency->id);
                $smarty->assign("favizone_module_path", $this->_path);
                $html .= $this->display(__FILE__, 'views/templates/hook/favizone-hidden-element.tpl');
                $html .= $this->display(__FILE__, 'views/templates/hook/favizone-product-element.tpl');
            }
            $html .= $this->display(__FILE__, 'views/templates/hook/favizone-product-hidden-element.tpl');

            return $html;
        }

        return "";
    }

    /**
     * Hook Called  before the "Print" link, under the picture.
     * @param array $params
     * @return string The HTML to output
     */
    public function hookDisplayRightColumnProduct($params)
    {
        if (FavizoneCommonHelper::validateContext()) {
            $favizone_common_helper = new FavizoneCommonHelper();
            $test_version = $favizone_common_helper->getTestingVersion();
            if ($test_version == "B" || $test_version == 'N') {
                return $this->display(__FILE__, 'views/templates/hook/favizone-product-right-element.tpl');
            }
        }
        return "";
    }

    /**
     * Hook Called  after the block for the "Add to Cart" button.
     * @param array $params
     * @return string The HTML to output
     */
    public function hookDisplayLeftColumnProduct($params)
    {
        if (FavizoneCommonHelper::validateContext()) {
            $favizone_common_helper = new FavizoneCommonHelper();
            $test_version = $favizone_common_helper->getTestingVersion();
            if ($test_version == "B" || $test_version == 'N') {
                return $this->display(__FILE__, 'views/templates/hook/favizone-product-left-element.tpl');
            }
        }
        return "";
    }

    /**
     * Hook to display some specific information on the shopping cart page (1.4)
     * @param array $params
     * @return string The HTML to output
     */
    public function hookShoppingCart($params)
    {
        return $this->hookDisplayShoppingCartFooter($params);
    }

    /**
     * * Hook to display some specific information on the shopping cart page
     * @param array $params
     * @return string The HTML to output
     */
    public function hookDisplayShoppingCartFooter($params)
    {
        $html = "";
        if (FavizoneCommonHelper::validateContext()) {
            $smarty = $this->getSmarty();
            if (is_null($smarty)) {
                return "";
            }
            $favizone_common_helper = new FavizoneCommonHelper();
            $test_version = $favizone_common_helper->getTestingVersion();
            if ($test_version == 'B' || $test_version == 'N') {
                $product_data = $favizone_common_helper->getRenderingCanal($params, "cart");
                $smarty->assign('url', $product_data['url']);
                $smarty->assign('post_data', $product_data['post_data']);
                $smarty->assign("click", "");
                /*initializing currency params in smarty*/
                $smarty->assign('favizone_currency_default', (int)Configuration::get('PS_CURRENCY_DEFAULT'));
                $smarty->assign('favizone_current_currency', $this->context->currency->id);
                $smarty->assign("favizone_module_path", $this->_path);
                $html .= $this->display(__FILE__, 'views/templates/hook/favizone-hidden-element.tpl');
                $html .= $this->display(__FILE__, 'views/templates/hook/favizone-cart-element.tpl');
            }
        }
        return $html;
    }

    /**
     * * Hook which allow you to do things in the header of each pages.
     * @param array $params
     * @return string The HTML to output
     */
    public function hookDisplayHeader($params)
    {
        if (FavizoneCommonHelper::validateContext()) {
            $smarty = $this->getSmarty();
            if (is_null($smarty)) {
                return "";
            }
            $helper = new FavizoneCommonHelper();
            $test_version = $helper->getTestingVersion();
            if ($test_version == "B" || $test_version == 'N') {
                if ($helper->isController('search')) {
                    $product_data = $helper->getRenderingCanal($params, "search");
                    $smarty->assign('url', $product_data['url']);
                    $smarty->assign('post_data', $product_data['post_data']);
                }
                if ($helper->isController('category')) {
                    $product_data = $helper->getRenderingCanal($params, "category");
                    $smarty->assign('url', $product_data['url']);
                    $smarty->assign('post_data', $product_data['post_data']);
                }
            }
            return "";
        }

        return "";
    }

    /**
     * * Hook which allow you to add block in footer (1.4)
     * @param array $params
     * @return string The HTML to output
     **/
    public function hookFooter($params)
    {
        return $this->hookDisplayFooter($params);
    }

    /**
     * * Hook which allow you to add block in footer
     * @param array $params
     * @return string The HTML to output
     **/
    public function hookDisplayFooter($params)
    {
        $smarty = $this->getSmarty();
        if (is_null($smarty)) {
            return "";
        }
        if (FavizoneCommonHelper::validateContext()) {
            $cookie = $params['cookie'];
            $helper = new FavizoneCommonHelper();
            $session_identifier = $helper->generateSessionIdentifier($params);
            $smarty->assign('favizone_s_id', $session_identifier);
            $html = '';
            /*initializing currency params in smarty*/
            $smarty->assign('favizone_currency_default', (int)Configuration::get('PS_CURRENCY_DEFAULT'));
            $smarty->assign('favizone_current_currency', $this->context->currency->id);
            if (isset($cookie->favizone_preview)) {
                $smarty->assign('favizone_preview_mode', true);
                $smarty->assign('modules_dir', $this->_path);
                $html .= $this->display(__FILE__, 'views/templates/front/preview.tpl');
            }
            $html .= $this->display(__FILE__, 'views/templates/hook/favizone-footer-files.tpl');
            $test_version = $helper->getTestingVersion();
            if ($test_version == "B" || $test_version == 'N') {
                if ($helper->isErrorReturn()) {
                    $data = $helper->getRenderingCanal($params, "error");
                    $smarty->assign('url', $data['url']);
                    $smarty->assign('post_data', $data['post_data']);
                    $smarty->assign('gender', 'error');
                    $smarty->assign('element_identifier', $helper->getStandardElement("error"));
                    $smarty->assign("favizone_module_path", $this->_path);
                    $html .= $this->display(__FILE__, 'views/templates/hook/favizone-hidden-element.tpl');
                    $html .= $this->display(__FILE__, 'views/templates/hook/favizone-footer-element.tpl');
                    return $html;
                }
                if ($helper->validateReturn()) {
                    $data = $helper->getRenderingCanal($params, "others");
                    $smarty->assign('url', $data['url']);
                    $smarty->assign('post_data', $data['post_data']);
                    $smarty->assign('gender', 'other');
                    $smarty->assign('element_identifier', $helper->getStandardElement("other"));
                    $smarty->assign("favizone_module_path", $this->_path);
                    $html .= $this->display(__FILE__, 'views/templates/hook/favizone-hidden-element.tpl');
                    $html .= $this->display(__FILE__, 'views/templates/hook/favizone-footer-element.tpl');
                    return $html;
                }
            }
            return $html;
        }
        return "";
    }
}
