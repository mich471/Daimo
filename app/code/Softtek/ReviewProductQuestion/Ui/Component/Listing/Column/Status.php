<?php

namespace Softtek\ReviewProductQuestion\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item[$this->getData('name')] == 1){
                    $item[$this->getData('name')] =  __('Approved');
                }elseif($item[$this->getData('name')] == 2){
                    $item[$this->getData('name')] = __('Pending');
                }else{
                    $item[$this->getData('name')] = __('Not Approved');
                }
            }
        }
        return $dataSource;
    }
}
