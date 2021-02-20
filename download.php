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
 require_once(dirname(__FILE__).'/../../config/config.inc.php');
 require_once('../../config/settings.inc.php');
 require_once('../../init.php');
 include_once(dirname(__FILE__).'/favizone.php');
//date actuelle
$date = gmdate('D, d M Y H:i:s');
$id_language = Tools::getValue('lang');
$idShop = Tools::getValue('idShop');

if (isset($id_language) && isset($idShop)) {
    $local_file = _PS_MODULE_DIR_.'favizone/favizone-export-cataloge-'.$id_language.'-'.$idShop.'.xml';
    if (file_exists($local_file) && is_file($local_file)) {
        chmod($local_file, 0644);
        // Vous voulez afficher un xml
        header('Content-Type: text/xml');
        // Il sera nommé favizone-export-cataloge.xml
        header('Content-Disposition: attachment; filename=favizone-export-cataloge-'
            .$id_language.'-'.$idShop.'.xml'/*.$local_file*/);
        header('Last-Modified: '. $date . ' GMT');
        header('Expires: ' . $date);
        // Le source du xml original.xml
        readfile($local_file);
        //exit();
    }
}
