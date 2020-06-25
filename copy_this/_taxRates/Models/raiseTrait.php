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

trait raiseTrait
{
    public $execPeriod = [
        '2020-12-28',
        '2021-01-03',
    ];

    public function __construct()
    {
        $this->rateChanges = array_flip($this->rateChanges);
    }
}