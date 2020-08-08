<?php


namespace app\commands;


use app\models\console\Telegram;
use yii\console\Controller;

class TelegramController extends Controller
{
    public function run($route, $params = [])
    {

    }

    public function actionBuyBot($number)
    {
        $telegram = new Telegram($number);
        $telegram->runBuyBot();
    }

    public function actionSendHello($number)
    {
        $telegram = new Telegram($number);
        $telegram->sendHello();
    }

    public function actionSellBot($number)
    {
        $telegram = new Telegram($number);
        $telegram->runSellBot();
    }
}