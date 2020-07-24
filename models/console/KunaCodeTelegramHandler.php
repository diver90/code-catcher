<?php


namespace app\models\console;

use app\models\KunaCodeBotModel;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\Exception;
use danog\MadelineProto\RPCErrorException;

class KunaCodeTelegramHandler extends EventHandler
{
    /**
     * @var int|string Username or ID of bot admin
     */
    const ADMIN = "diver90_deep";

    const BOT = "kunacodebot";

    public $status;

    public $update;

    public $warnAdmin;

    public $activeDeal;

    /**
     * Get peer(s) where to report errors.
     *
     * @return int|string|array
     */
    public function getReportPeers()
    {
        return [self::ADMIN];
    }

    /**
     * Handle updates from users.
     *
     * @param array $update Update
     *
     * @return \Generator
     */
    public function onUpdateNewMessage(array $update): \Generator
    {

        $this->warnAdmin = false;

        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }

        $this->update = $update;

       // $this->logger($update['message']['message'], 'tech', '../../../runtime/logs/tech.log');

        $message = $update['message']['message'];

        /*dump($message);

        dump( $this->status);

        dump( $this->activeDeal);*/

        if (preg_match("/\bStop\b/i", $message)) {
            $this->stop();
        }

        if (preg_match("/\bAnother deal has already been made based on this order\b/i", $message)) {
            $this->warnAdmin = true;
            //$this->sendMessage('Сделка провалена');
            $this->warnAdmin = false;
            yield $this->sendMessage('🔎 Orderbook UAH', 5);
        }

        if (preg_match("/\bCommand not recognized\b/i", $message)) {
            return;
        }

        if (preg_match("/\bOrder is not found\b/i", $message)) {
            yield $this->sendMessage('🔎 Orderbook UAH', 5);
        }

        if (preg_match("/\bin a deal creation\b/i", $message)) {
            $this->warnAdmin = true;
            yield $this->sendMessage('Создание сделки '  . $this->activeDeal['order_id']);
        }

        if (preg_match("/\bPlease pay\b/i", $message)) {
            $this->warnAdmin = true;

            yield $this->sendMessage('Сделка создана успешно, сумма к оплате ' . $this->activeDeal['price']);
        }

        if (preg_match("/\bThe validity period for Deal\b/i", $message)) {
            $this->warnAdmin = true;
            yield $this->sendMessage('Сделка в ожидании!');
        }
/*
 Please pay 694.89 UAH to the card and report the payment within 15 minutes\n
\n
Send only the specified amount! Any amount sent from above can be lost
"""
^ "5375414101185857"

Waiting for seller's confirmation\n
\n
Congradulations! Your deal №D6EA41.C24 is succesfully concluded.\n
  Your code is:\n
\n
v7v7C-PxZJj-DNdJ5-nWJyZ-QRQRL-bVbgw-6yHZi-JuNW3-Kha8P-UAH-KCode

Press “1” to cancel the deal
Press “0” if you you changed your mind and don’t want to cancel the deal.

Dear User, your deal №A91250.94C has been deleted

Use the menu under the input field:


*/


        if (preg_match("/\byou are going to accept order\b/i", $message) && $this->status !== 'Pay sended') {
            $this->status = 'Pay sended';
            //dump('Pay send');
            yield $this->sendMessage('📥 Pay');
        }

        if (preg_match("/\bBuy this code\b/im", $message)) {
            $model = new KunaCodeBotModel($message);
            $answer = $model->countOrders();

            /*dump($answer);
            dump( $this->status);*/

            if (!$answer){
                yield $this->sendMessage('🔎 Orderbook UAH', 5);
            } elseif ( (is_null($this->activeDeal)) || $this->activeDeal['order_id'] != $answer['order_id']) {
                $this->activeDeal = $answer;
                $this->status = 'Deal sended';
                /*dump('Deal '.$answer['order_id'].' send');*/
                yield $this->sendMessage('/deal' . $answer['order_id']);
            } else {
                var_dump($answer);
            }
        }

        return;

    }

    /**
     * @param $message
     * @param int $sleep
     * @return \Generator
     */
    public function sendMessage($message, $sleep = 0.1): \Generator
    {
        $peer = $this->warnAdmin ? self::ADMIN : self::BOT;
        try {
            sleep($sleep);
            yield $this->messages->sendMessage(['peer' => '@' . $peer, 'message' => $message, 'parse_mode' => 'HTML']);
        } catch (RPCErrorException $e) {
            $this->report("Surfaced: $e");
        } catch (Exception $e) {
            if (\stripos($e->getMessage(), 'invalid constructor given') === false) {
                $this->report("Surfaced: $e");
            }
        }
    }

}