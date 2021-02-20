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
 * Helper class for configuration.
 */
class FavizoneConfiguration
{
    /**
     *  Constructor
     */
    public function __construct()
    {
        require_once(_PS_MODULE_DIR_.'favizone/classes/helpers/common.php');
    }
    /**
     * Updates a configuration attribute.
     *
     * @param   String $key the the attribute of the configuration data.
     * @param   String $value the value of attribute to update.
     * @param   int $id_lang language identifier.
     * @return mixed
     */
    public static function updateValue($key, $value, $id_lang = null)
    {
        $context = FavizoneCommonHelper::loadContext();
        if (is_null($id_lang)) {
            $id_lang = $context->language->id;
        }

        if (FavizoneCommonHelper::prestaShopVersion() == '1.4') {
            $values = array($id_lang=>$value);
            return Configuration::updateValue($key, $values, false);
        } else {
            $id_shop_group = $context->shop->id_shop_group;
            $id_shop = $context->shop->id;
            if (is_null($id_lang)) {
                $id_lang = $context->language->id;
            }
            $values = array($id_lang=>$value);
            return Configuration::updateValue($key, $values, false, $id_shop_group, $id_shop);
        }
    }

    /**
     * Returns a configuration value based on its key.
     *
     * @param String $key configuration  name.
     * @param int $id_lang language identifier.
     * @return mixed
     */
    public static function get($key, $id_lang = null)
    {
        $context = FavizoneCommonHelper::loadContext();
        if (is_null($id_lang)) {
            $id_lang = $context->language->id;
        }
        if (FavizoneCommonHelper::prestaShopVersion() == '1.4') {
            return  Configuration::get($key, $id_lang);
        }
        $id_shop_group = $context->shop->id_shop_group;
        $id_shop = $context->shop->id;
        return Configuration::get($key, $id_lang, $id_shop_group, $id_shop);
    }

    /**
     * Returns a configuration value based on its key.
     *
     * @param String $key configuration  name.
     * @param int $id_lang language identifier.
     * @param int $id_shop
     * @param int $id_shop_group
     * @return mixed
     */
    public static function getValue($key, $id_lang, $id_shop, $id_shop_group)
    {
        if (FavizoneCommonHelper::prestaShopVersion() == '1.4') {
            return  Configuration::get($key, $id_lang);
        }
        return Configuration::get($key, $id_lang, $id_shop_group, $id_shop);
    }

    /**
     * Removes a  configuration specified by name. .
     *
     * @param String $key configuration  name.
     * @return bool
     */
    public static function deleteValue($key)
    {
        if (FavizoneCommonHelper::prestaShopVersion() == '1.4') {
            return Configuration::deleteByName($key);
        }
        Configuration::deleteFromContext($key);
        return true;
    }

    /**
     * Removes all favizone config entries.
     *
     * @return bool always true.
     */
    public static function removeConfigData()
    {
        $config_table = _DB_PREFIX_.'configuration';
        $config_lang_table = $config_table.'_lang';

        Db::getInstance()->execute('
            DELETE `'.$config_lang_table.'` FROM `'.$config_lang_table.'`
            LEFT JOIN `'.$config_table.'`
            ON `'.$config_lang_table.'`.`id_configuration` = `'.$config_table.'`.`id_configuration`
            WHERE `'.$config_table.'`.`name` LIKE "FAVIZONE_%"');
        Db::getInstance()->execute('
            DELETE FROM `'.$config_table.'`
            WHERE `'.$config_table.'`.`name` LIKE "FAVIZONE_%"');
        Configuration::loadConfiguration();
        return true;
    }
}
