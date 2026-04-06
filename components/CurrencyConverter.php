<?php 

namespace app\components;

use yii\base\Widget;

class CurrencyConverter extends Widget
{
    public $amount = 0;
    public $defaultRate = 1.38;

    public function run()
    {
        return $this->render('currency-converter', [
            'amount' => $this->amount,
            'defaultRate' => $this->defaultRate
        ]);
    }
}
