<?php

namespace IVN\OneStepCheckout\Plugin\Model\ConfigProvider;

use Aheadworks\OneStepCheckout\Model\ConfigProvider\PaymentMethodList as PaymentList;

class PaymentMethodList
{
    /**
     * @param PaymentList $subgect
     * @param $result
     * @return mixed
     */
    public function afterGetPaymentMethods(PaymentList $subgect, $result)
    {
        foreach ($result as $key => $method) {
            if($method['code'] == 'checkmo') {
                unset($result[$key]);
            }
        }

        return $result;
    }
}