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

class FavizoneShop
{
    public $id;
    /** @var string name  of the shop*/
    public $name;
    /** @var int ID of shop group */
    public $id_shop_group;


    /**
     * Constructor
     *
     * @param $id_shop
     * @param int $name_shop
     * @param int $id_shop_group
     */
    public function __construct($id_shop, $name_shop = 1, $id_shop_group = 1)
    {
        $this->id = (int)$id_shop;
        $this->name = $name_shop;
        $this->id_shop_group = (int)$id_shop_group;
    }

    /**
     * @return array
     */
    public function getShops()
    {
        return array(
            array('id_shop' =>  $this->id, 'name' => $this->name, 'id_shop_group' =>$this->id_shop_group)
        );
    }

    /**
     * @return int
     */
    public function getCurrentShop()
    {
        return  $this->id;
    }
}
