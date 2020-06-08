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

use oxArticle;
use oxConfig;
use oxDb;
use oxRegistry;
use oxShop;

abstract class taxRateAbstract
{
    public $execPeriod = [
        '2020-01-01',
        '2019-12-31',
    ];

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

    public function run()
    {
        if (false === $this->isInExecutableTimeRange()) {
            trigger_error("script shouldn't run outside the defined time range", E_USER_WARNING);
            die();
        };

        $this->changeTaxRates();
    }

    public function changeTaxRates()
    {
        $shop = new oxShop();

        // use shop list, when parameter -d is set
        $opts = getopt("s:");

        $where = isset($opts['s']) ?
            "oxid IN (".implode(', ', array_map(
                    function ($a) {return oxDb::getDb()->quote(trim($a));},
                    explode(',', $opts['s']))
            ).")" :
            "1";

        $q = "SELECT oxid FROM " . $shop->getCoreTableName() . " WHERE ".$where ;

        foreach (oxDb::getDb(oxDb::FETCH_MODE_ASSOC )->getAll( $q ) as $record ) {
            $shopId = (int) $record["oxid"];
            $this->switchToShop($shopId);
            $this->changeDefaultTaxRate( $shopId );
            $this->changeArticlesTaxRate( $shopId );
        }
    }

    /**
     * @param int $id
     *
     * @throws \oxConnectionException
     */
    public function switchToShop($id)
    {
        if (oxRegistry::getConfig()->isMall()
            && $id != oxRegistry::getConfig()->getActiveShop()->getId()
        ) {
            /** @var oxConfig $oNewConf */
            $oNewConf = new oxConfig();
            $oNewConf->setShopId($id);
            $oNewConf->init();

            oxRegistry::getConfig()->onShopChange();
            oxRegistry::getSession()->setVariable('actshop', $id);
            oxRegistry::getSession()->setVariable('currentadminshop', $id);
            oxRegistry::getConfig()->setShopId($id);
        }
    }

    /**
     * @param int $shopId
     */
    public function changeDefaultTaxRate($shopId)
    {
        $oCurrConfig = new oxConfig();
        $newVat = $this->rateChanges[(int) $oCurrConfig->getConfigParam('dDefaultVAT')];

        if ($newVat) {
            $oCurrConfig->saveShopConfVar('num', 'dDefaultVAT', $newVat);
            echo "default tax rate was sucessfully changed in shop ".$shopId.PHP_EOL;
        } else {
            echo "no changeable default tax rate found in shop ".$shopId.PHP_EOL;
        }
    }

    /**
     * @param int $shopId
     */
    public function changeArticlesTaxRate($shopId)
    {
        $article = oxNew(oxArticle::class);
        $q = "SELECT oxid FROM ".$article->getCoreTableName()." 
            WHERE oxvat IN (".implode(', ', array_keys($this->rateChanges)).") 
            AND oxshopid = ". oxDb::getDb()->quote($shopId);

        $counter = 0;
        foreach (oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getAll($q) as $articleRecord) {
            $articleId = $articleRecord['oxid'];
            $article = oxNew(oxArticle::class);
            $article->load($articleId);
            $article->assign(
                [
                    'oxvat' => $this->rateChanges[(int) $article->getFieldData('oxvat')]
                ]
            );
            $article->save();
            $counter++;
        }

        if ($counter) {
            echo "the tax rate for " . $counter . " article(s) was changed in shop " . $shopId . PHP_EOL;
        }

        $q = "SELECT count(*) FROM ".$article->getCoreTableName()." 
            WHERE oxvat IN (".implode(', ', array_keys($this->rateChanges)).") 
            AND oxshopid = ". oxDb::getDb()->quote($shopId);

        if ($counter = oxDb::getDb()->getOne($q)) {
            echo "the tax rate update for " . $counter . " article(s) was failed in shop " . $shopId . PHP_EOL;
        }
    }
}
