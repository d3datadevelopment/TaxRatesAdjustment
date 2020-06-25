<?php

/**
 * This Software is the property of Data Development and is protected
 * by copyright law - it is NOT Freeware.
 * Any unauthorized use of this software without a valid license
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 * http://www.shopmodule.com
 *
 * @copyright (C) D3 Data Development (Inh. Thomas Dartsch)
 * @author        D3 Data Development - Daniel Seifert <support@shopmodule.com>
 * @link          http://www.oxidmodule.com
 */

namespace D3\TaxRatesAdjustment\Models;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;

abstract class genericAbstract
{
    public $rateChanges = [
        19  => 16,
        7   => 5
    ];

    /**
     * @return bool
     */
    public function isInExecutableTimeRange()
    {
        // skip time check, when parameter -d is set
        $opts = getopt("d");
        if (is_array($opts) && isset($opts['d'])) {
            return true;
        }

        list($from, $to) = $this->execPeriod;

        return (time() > strtotime($from)) && (time() < strtotime($to));
    }

    /**
     * @param int $id
     *
     * @throws \oxConnectionException
     */
    public function switchToShop($id)
    {
        if (Registry::getConfig()->isMall()
            && $id != Registry::getConfig()->getActiveShop()->getId()
        ) {
            /** @var Config $oNewConf */
            $oNewConf = new Config();
            $oNewConf->setShopId($id);
            $oNewConf->init();

            Registry::getConfig()->onShopChange();
            Registry::getSession()->setVariable('actshop', $id);
            Registry::getSession()->setVariable('currentadminshop', $id);
            Registry::getConfig()->setShopId($id);
        }
    }
}