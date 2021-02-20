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
 * Helper class for managing api data.
 */
class FavizoneApi
{
   
    const HOST =  "https://dev.favizone.com" ;
    /**
     * Returns Favizone's Api Url.
     *
     * @return String
     */
    public function getHost()
    {
        return self::HOST ;
    }

    /**
     * Returns Favizone's Api path for adding account .
     *
     * @return string
     */
    public function getAddAccountUrl()
    {
        return "/v2/user/add-account" ;
    }


    /**
     * Returns  Favizone's Api path for categories .
     * @return string
     */
    public function getCategoryUrl()
    {
        return "/category/categories" ;
    }

    /**
     * Returns Favizone's Api path for updating category data .
     *
     * @return string
     */
    public function getUpdateCategoryUrl()
    {
        return "/category/update" ;
    }

    /**
     * Retruns Favizone's Api path for deleting category .
     * @return string
     */
    public function getDeleteCategoryUrl()
    {
        return "/category/delete";
    }

    /**
     * Returns Favizone's Api path for adding new category .
     *
     * @return string
     */
    public function getAddCategoryUrl()
    {
        return "/category/add" ;
    }

    /**
     * Returns Favizone's Api path for products initialisation .
     *
     * @return string
     */
    public function getInitProductUrl()
    {
        return "/product/first-init" ;
    }

    /**
     * Returns Favizone's Api path of updating product data .
     *
     * @return string
     */
    public function getUpdateProductUrl()
    {
        return "/product/update" ;
    }

    /**
     * Returns Favizone's Api path for adding new product .
     *
     * @return string
     */
    public function getAddProductUrl()
    {
        return "/product/add" ;
    }

    /**
     * Returns Favizone's Api path for deleting a product .
     *
     * @return string
     */
    public function getDeleteProductUrl()
    {
        return "/product/delete" ;
    }

    /**
     * Returns Favizone's Api path for customer data .
     *
     * @return string
     */
    public function getSendCustomUrl()
    {
        return "/api/custom-data" ;
    }

    /**
     * Return Favizone's Api url for event tracker .
     *
     * @return string
     */
    public function getSendEventUrl()
    {
        return self::HOST . "/api/addEvent" ;
    }

    /**
     * Returns Favizone's path for event tracker .
     *
     * @return string
     */
    public function getSendEventPath()
    {
        return "/api/addEvent" ;
    }

    /**
     * Returns Favizone's Api url for recommendations renderer .
     *
     * @return string
     */
    public function getRecommendationRendererUrl()
    {
        return self::HOST . "/api/allrecs" ;
    }

    /**
     * Returns Favizone's path for checking data initialisation.
     *
     * @return string
     */
    public function getCheckInitUrl()
    {
        return "/product/check-init" ;
    }

    /**
     * Returns Favizone's path for A/B test initialisation .
     *
     * @return string
     */
    public function getInitABTestUrl()
    {
        return "/ab-test/init" ;
    }

    /**
     * Returns Favizone's path for A/B test end period .
     *
     * @return string
     */
    public function getEndABTestUrl()
    {
        return "/ab-test/end" ;
    }

    /**
     * Returns Favizone's path for registering a new profile .
     *
     * @return string
     */
    public function getRegisterProfiletUrl()
    {
        return "/api/profile/register" ;
    }

    /**
     * Returns Favizone's Api url for getting a demo recommendations .
     *
     * @return string
     */
    public function getDemoRecommendationRendererUrl()
    {
        return self::HOST . "/api/demo-recs/" ;
    }

    /**
     * Returns Favizone's path orders tracker .
     *
     * @return string
     */
    public function getInitOrderPath()
    {
        return "/order/init" ;
    }

    /**
     * Send Catalog Url
     * @return mixed
     */
    public function getIntegrationUrl()
    {
        return "/api/update-import-data";
    }
}
