<?php


namespace app\models\console;

use danog\MadelineProto\API;
use danog\MadelineProto\Logger;
use yii\base\BaseObject;

class Telegram extends BaseObject
{
    public $madelin;

    protected $session;

    public function __construct($number, $config = [])
    {
        $settings = [
            'logger' => [
                'logger_level' => Logger::NOTICE,
                'logger_param' => __DIR__ . '/../../runtime/logs/Madeline.log'
            ]
        ];
        $path = __DIR__ . '/../../runtime/' . $number . '.madeline';
        $this->madelin = new API($path, $settings);
        parent::__construct($config);
    }

    public function runKunaCodeBot(){

        $this->madelin->messages->sendMessage(['peer' => '@kunacodebot', 'message' => "🔎 Orderbook UAH"]);
        $this->madelin->startAndLoop(KunaCodeTelegramHandler::class);

    }

    public function sendHello(){

        $this->madelin->messages->sendMessage(['peer' => '@diver90_deep', 'message' => "🔎 Orderbook UAH"]);
        $this->madelin->startAndLoop(TestHandler::class);
        //$this->madelin->startAndLoop(KunaCodeTelegramHandler::class);

    }
}