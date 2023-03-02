<?php

namespace Softtek\Tax\Plugins\Model;

use Magento\Checkout\Model\TotalsInformation;

class TotalsInformationManagement
{
    public function afterCalculate(TotalsInformation $totalsInformation, $result)
    {
        var_dump($result);
    }
}
