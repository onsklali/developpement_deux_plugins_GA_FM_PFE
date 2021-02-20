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
 * Helper class for search operations .
 */
class FavizoneKeywordSearcher
{
    /**
     * Getting  keywords from the requested url
     *
     * @param  String $url
     * @return String
     */
    public static function getKeywords($url)
    {
        if (!Validate::isAbsoluteUrl($url)) {
            return false ;
        }
        $parsed_url = parse_url($url) ;
        if (!isset($parsed_url['query']) && isset($parsed_url['fragment'])) {
            $parsed_url['query'] = $parsed_url['fragment'] ;
        }
        if (!isset($parsed_url['query'])) {
            return false ;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT `server`, `getvar` FROM `'
            ._DB_PREFIX_.'search_engine`'
        ) ;
        foreach ($result as $row) {
            $host =& $row['server'] ;
            $varname =& $row['getvar'] ;
            if (strstr($parsed_url['host'], $host)) {
                $k_array = array() ;
                preg_match('/[^a-zA-Z&]?'.$varname.'=.*\&'.'/U', $parsed_url['query'], $k_array) ;
                if (!isset($k_array[0]) || empty($k_array[0])) {
                    preg_match('/[^a-zA-Z&]?'.$varname.'=.*$'.'/', $parsed_url['query'], $k_array) ;
                }
                if (!isset($k_array[0]) || empty($k_array[0])) {
                    return false ;
                }
                if ($k_array[0][0] == '&' && Tools::strlen($k_array[0]) == 1) {
                    return false ;
                }
                return urldecode(
                    str_replace(
                        '+',
                        ' ',
                        ltrim(
                            Tools::substr(
                                rtrim(
                                    $k_array[0],
                                    '&'
                                ),
                                Tools::strlen($varname) + 1
                            ),
                            '='
                        )
                    )
                ) ;
            }
        }
        return false ;
    }
}
