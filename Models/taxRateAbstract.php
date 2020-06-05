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

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Shop;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;

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
        list($from, $to) = $this->execPeriod;

        return (time() > strtotime($from)) && (time() < strtotime($to));
    }

    /**
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public function run()
    {
        if (false === $this->isInExecutableTimeRange()) {
            trigger_error("script shouldn't run outside the defined time range", E_USER_WARNING);
            die();
        };

        $this->changeDefaultTaxRates();
    }

    /**
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public function changeDefaultTaxRates() {
        $shop = new Shop();

        $q = "SELECT oxid FROM " . $shop->getCoreTableName() . " WHERE 1";
        foreach ( DatabaseProvider::getDb( DatabaseProvider::FETCH_MODE_ASSOC )->getAll( $q ) as $record ) {
            $shopId = (int) $record['oxid'];
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

    /**
     * @param int $shopId
     */
    public function changeDefaultTaxRate($shopId)
    {
        $oCurrConfig = new Config();
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
     *
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public function changeArticlesTaxRate($shopId)
    {
        $article = oxNew(Article::class);
        $q = "SELECT oxid FROM ".$article->getCoreTableName()." 
            WHERE oxvat IN (".implode(', ', array_keys($this->rateChanges)).") 
            AND oxshopid = ".DatabaseProvider::getDb()->quote($shopId);

        $counter = 0;
        foreach (DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getAll($q) as $articleRecord) {
            $articleId = $articleRecord['oxid'];
            $article = oxNew(Article::class);
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
            AND oxshopid = ".DatabaseProvider::getDb()->quote($shopId);

        if ($counter = DatabaseProvider::getDb()->getOne($q)) {
            echo "the tax rate update for " . $counter . " article(s) was failed in shop " . $shopId . PHP_EOL;
        }
    }
}