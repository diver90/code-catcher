<?php


namespace app\commands;


use app\models\console\Telegram;
use yii\console\Controller;

class TelegramController extends Controller
{
    public function run($route, $params = [])
    {

    }

    public function actionKunaBot($number)
    {
        $telegram = new Telegram($number);
        $telegram->runKunaCodeBot();
    }

    public function actionSendHello($number)
    {
        $telegram = new Telegram($number);
        $telegram->sendHello();
    }
}