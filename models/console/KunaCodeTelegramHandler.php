<?php


namespace app\models\console;

use app\models\KunaCodeBotModel;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\Exception;
use danog\MadelineProto\Logger;
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
     * @var mixed
     */
    private $buttonPaid;
    /**
     * @var KunaCodeBotModel|mixed
     */
    private $model;
    /**
     * @var mixed
     */
    //private $buttonCancel;

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
        if (empty($this->model)) $this->model = new KunaCodeBotModel();

        $this->warnAdmin = false;

        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }

        $this->update = $update;

//buttons
        if (isset($update['message']['reply_markup']['rows'])) {
            foreach ($update['message']['reply_markup']['rows'] as $row) {
                foreach ($row['buttons'] as $button) {
                    if ($button['text'] === '🤝 I have paid') {
                        $this->buttonPaid = $button;
                    }
                    /*if ($button['text'] === '❌ Cancel deal') {
                        $this->buttonCancel = $button;
                    }*/
                }
            }
        }

        $message = $update['message']['message'];

        /*dump($message);

        dump( $this->status);

        dump( $this->activeDeal);*/

        if (preg_match("/\bStop\b/i", $message)) {
            $this->stop();
        }

        if (preg_match("/\bStart\b/i", $message)) {
            yield $this->sendMessage('🔎 Orderbook UAH', 5);
        }

        if (preg_match("/\bPaid\b/i", $message)) {
            yield $this->buttonPaid->click();
        }

        if (preg_match("/\bCancel\b/i", $message)) {
            yield $this->sendMessage('❌ Cancel deal', 0);
        }

        if (preg_match("/\bPress “1” to cancel the deal\b/i", $message)) {
            $this->activeDeal = [];
            yield $this->sendMessage('1', 0);
        }

        if (preg_match("/\bAnother deal has already been made based on this order\b/i", $message)) {
            $this->activeDeal = [];
            $this->warnAdmin = false;
            $this->model->setActiveDealStatus('failure');
            $this->model->saveActiveDeal();
            yield $this->sendMessage('🔎 Orderbook UAH', 5);
        }

        if (preg_match("/\bCommand not recognized\b/i", $message)) {
            return;
        }

        if (preg_match("/\bOrder is not found\b/i", $message)) {
            $this->activeDeal = [];
            $this->model->setActiveDealStatus('failure');
            $this->model->saveActiveDeal();
            yield $this->sendMessage('🔎 Orderbook UAH', 5);
        }

        if (preg_match("/\bin a deal creation\b/i", $message)) {
            $this->warnAdmin = true;
            yield $this->sendMessage('Создание сделки '  . $this->activeDeal['order_id']);
        }

        if (preg_match("/\bPlease pay\b/i", $message)) {
            $this->warnAdmin = true;
            $this->model->setActiveDealStatus('successful');
            $this->model->saveActiveDeal();
            yield $this->sendMessage('Сделка создана успешно, сумма к оплате ' . $this->activeDeal['price']);
        }

        if (preg_match("/^\d{16}$/i", $message)) {
            $this->warnAdmin = true;
            yield $this->sendMessage($message);
        }

        if (preg_match("/^[\d\w-]{53}-UAH-KCode$/", $message)) {
            $this->warnAdmin = true;
            yield $this->sendMessage($message);
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

danog\MadelineProto\TL\Types\Button Object
(
    [button] => Array
        (
            [_] => keyboardButtonCallback
            [text] => With markup (123)
            [data] => danog\MadelineProto\TL\Types\Bytes Object
                (
                    [bytes:danog\MadelineProto\TL\Types\Bytes:private] => next_list_page::0::extra::5
                )

        )

    [id] => 204990
    [peer] => 786805975
)
danog\MadelineProto\TL\Types\Button Object
(
    [button] => Array
        (
            [_] => keyboardButtonCallback
            [text] => Refresh
            [data] => danog\MadelineProto\TL\Types\Bytes Object
                (
                    [bytes:danog\MadelineProto\TL\Types\Bytes:private] => next_list_page::0::all::5
                )

        )

    [id] => 204990
    [peer] => 786805975
)
danog\MadelineProto\TL\Types\Button Object
(
    [button] => Array
        (
            [_] => keyboardButtonCallback
            [text] => Next 20
            [data] => danog\MadelineProto\TL\Types\Bytes Object
                (
                    [bytes:danog\MadelineProto\TL\Types\Bytes:private] => next_list_page::20::all::5
                )

        )

    [id] => 204990
    [peer] => 786805975
)
danog\MadelineProto\TL\Types\Button Object
(
    [button] => Array
        (
            [_] => keyboardButton
            [text] => ❌ Cancel deal
        )

    [id] => 204992
    [peer] => 786805975
)
danog\MadelineProto\TL\Types\Button Object
(
    [button] => Array
        (
            [_] => keyboardButton
            [text] => 📥 Pay
        )

    [id] => 204992
    [peer] => 786805975
)
danog\MadelineProto\TL\Types\Button Object
(
    [button] => Array
        (
            [_] => keyboardButton
            [text] => ❌ Cancel deal
        )

    [id] => 204994
    [peer] => 786805975
)
danog\MadelineProto\TL\Types\Button Object
(
    [button] => Array
        (
            [_] => keyboardButtonCallback
            [text] => 🤝 I have paid
            [data] => danog\MadelineProto\TL\Types\Bytes Object
                (
                    [bytes:danog\MadelineProto\TL\Types\Bytes:private] => send_dealpay_message::98974
                )

        )

    [id] => 204998
    [peer] => 786805975
)

*/


        if (preg_match("/\byou are going to accept order\b/i", $message) && $this->status !== 'Pay sended') {
            $this->status = 'Pay sended';
            //dump('Pay send');
            yield $this->sendMessage('📥 Pay');
        }

        if (preg_match("/\bBuy this code\b/im", $message)) {
            $this->model->setMessage($message);
            $answer = $this->model->countOrders();

            /*dump($answer);
            dump( $this->status);*/

            if (!$answer){
                yield $this->sendMessage('🔎 Orderbook UAH', 5);
            } elseif ( (empty($this->activeDeal)) || $this->activeDeal['order_id'] != $answer['order_id']) {
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