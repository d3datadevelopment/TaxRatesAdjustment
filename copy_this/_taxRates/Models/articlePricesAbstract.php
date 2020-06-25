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

require_once('genericAbstract.php');

abstract class articlePricesAbstract extends genericAbstract
{
    public $baseQueriesDefaultTax = [
        //'UPDATE oxarticles SET oxprice = (oxprice / 1.19 * 1.16) WHERE oxshopid = 'oxbaseshop' AND (oxvat IS NULL);',
        // default prices
        'UPDATE oxarticles SET oxprice = (oxprice / :oldTaxPercent * :newTaxPercent) WHERE oxshopid = :shopid AND (oxvat IS NULL)',
        // recommended retail price
        'UPDATE oxarticles SET oxtprice = (oxtprice / :oldTaxPercent * :newTaxPercent) WHERE oxshopid = :shopid AND (oxvat IS NULL)',

        // varminprices
        'UPDATE oxarticles SET oxvarminprice = (oxvarminprice / :oldTaxPercent * :newTaxPercent) WHERE oxshopid = :shopid AND (oxvat IS NULL)',
        // varmaxprices
        'UPDATE oxarticles SET oxvarmaxprice = (oxvarmaxprice / :oldTaxPercent * :newTaxPercent) WHERE oxshopid = :shopid AND (oxvat IS NULL)'
    ];

    public $baseQueriesCustomTax = [
        //'UPDATE oxarticles SET oxprice = (oxprice / 1.19 * 1.16) WHERE oxshopid = 'oxbaseshop' AND (oxvat IN(16, 19));',
        // default prices
        'UPDATE oxarticles SET oxprice = (oxprice / :oldTaxPercent * :newTaxPercent) WHERE oxshopid = :shopid AND (oxvat IN(:oldTaxRate, :newTaxRate))',
        // recommended retail price
        'UPDATE oxarticles SET oxtprice = (oxtprice / :oldTaxPercent * :newTaxPercent) WHERE oxshopid = :shopid AND (oxvat IN(:oldTaxRate, :newTaxRate))',

        // varminprices
        'UPDATE oxarticles SET oxvarminprice = (oxvarminprice / :oldTaxPercent * :newTaxPercent) WHERE oxshopid = :shopid AND (oxvat IN(:oldTaxRate, :newTaxRate))',
        // varmaxprices
        'UPDATE oxarticles SET oxvarmaxprice = (oxvarmaxprice / :oldTaxPercent * :newTaxPercent) WHERE oxshopid = :shopid AND (oxvat IN(:oldTaxRate, :newTaxRate))'
    ];

    public function run()
    {
        if (false === $this->isInExecutableTimeRange()) {
            trigger_error("script shouldn't run outside the defined time range", E_USER_WARNING);
            die();
        };

        $this->changeArticlePrices();
    }

    public function changeArticlePrices()
    {
        $shop = new \oxShop();

        // use shop list, when parameter -d is set
        $opts = getopt("s:");

        $where = isset($opts['s']) ?
            "oxid IN (".implode(', ', array_map(
                                        function ($a) {return \oxDb::getDb()->quote(trim($a));},
                                        explode(',', $opts['s']))
            ).")" :
            "1";

        $q = "SELECT oxid FROM " . $shop->getCoreTableName() . " WHERE ".$where ;

        foreach ( \oxDb::getDb(\oxDb::FETCH_MODE_ASSOC)->getAll( $q ) as $record ) {
            $shopId = (int) $record["oxid"];

            $count = 0;

            $count += $this->changeSubshopArticlePricesDefaultTax($shopId);
            $count += $this->changeSubshopArticlePricesCustomTax($shopId);

            echo "$count article prices in shop $shopId changed.".PHP_EOL;
        }
    }


    public function changeSubshopArticlePricesDefaultTax($shopId)
    {
        $count = 0;

        $oCurrConfig = new \oxConfig();

        $oldTaxRate = (int) $oCurrConfig->getConfigParam('dDefaultVAT');
        $newTaxRate = $this->rateChanges[$oldTaxRate];

        if ($newTaxRate === null) {
            $flipped = array_flip($this->rateChanges);
            $oldTaxRate = $flipped[(int) $oCurrConfig->getConfigParam('dDefaultVAT')];
            $newTaxRate = $this->rateChanges[$oldTaxRate];
        }

        foreach ($this->baseQueriesDefaultTax as $query) {
            $db = \oxDb::getDb(\oxDb::FETCH_MODE_ASSOC);

            $queryParameters = [
                'shopid'    => $shopId,
                'oldTaxRate'=> $oldTaxRate,
                'oldTaxPercent' => 1 + ($oldTaxRate / 100),
                'newTaxRate'=> $newTaxRate,
                'newTaxPercent' => 1 + ($newTaxRate / 100),
            ];

            $count += $db->execute($query, $queryParameters);
        }

        return $count;
    }

    public function changeSubshopArticlePricesCustomTax($shopId)
    {
        $count = 0;
        foreach ($this->baseQueriesCustomTax as $query) {
            foreach ($this->rateChanges as $oldTaxRate => $newTaxRate) {
                $db = \oxDb::getDb(\oxDb::FETCH_MODE_ASSOC);

                $queryParameters = [
                    'shopid'    => $shopId,
                    'oldTaxRate'=> $oldTaxRate,
                    'oldTaxPercent' => 1 + ($oldTaxRate / 100),
                    'newTaxRate'=> $newTaxRate,
                    'newTaxPercent' => 1 + ($newTaxRate / 100),
                ];

                $count += $db->execute($query, $queryParameters);
            }
        }

        return $count;
    }
}