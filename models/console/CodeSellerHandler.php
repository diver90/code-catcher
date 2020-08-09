<?php


namespace app\models\console;

use app\models\KunaCodeBotModel;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\Exception;
use danog\MadelineProto\RPCErrorException;

class CodeSellerHandler extends EventHandler
{
    /**
     * @var int|string Username or ID of bot admin
     */
    const ADMIN = "diver90_deep";

    const BOT = "kunacodebot";

    const DEAL_SENDED = "deal_sended";

    const NORMAL_CYCLE = "normal";

    public $status = self::NORMAL_CYCLE;

    public $update;

    public $warnAdmin;

    public $activeDeal;
    /**
     * @var mixed
     */
    private $buttonSendInfo;
    /**
     * @var mixed
     */
    private $buttonDiscardOrder;
    /**
     * @var KunaCodeBotModel|mixed
     */
    private $model;
    /**
     * @var mixed
     */
    private $buttonConfirmOrder;
    /**
     * @var mixed
     */

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

        if ($update['message']['from_id'] == '734324493' && $update['message']['date'] > time()-10) {

            $message = $update['message']['message'];

            if (preg_match("/^Stop$/i", $message)) {
                $this->stop();
            }

            if (preg_match("/^Send$/i", $message) && !empty($this->buttonConfirmOrder)) {
                yield $this->buttonConfirmOrder->click();
            }

            if (preg_match("/^Cancel$/i", $message) ) {
                $this->status = self::NORMAL_CYCLE;
                $this->activeDeal = [];
                yield $this->sendMessage('❌ Cancel deal', 0);
            }
        }

        if ($update['message']['from_id'] == '786805975' /*&& $update['message']['date'] > time()-10*/) {

            var_dump($update);

            $this->update = $update;
//buttons
            if (isset($update['message']['reply_markup']['rows'])) {
                foreach ($update['message']['reply_markup']['rows'] as $row) {
                    foreach ($row['buttons'] as $button) {
                        if ($button['text'] === '💳 Send card info') {
                            $this->buttonSendInfo = $button;
                        }
                        if ($button['text'] === '❌ Cancel') {
                            $this->buttonDiscardOrder = $button;
                        }
                        if ($button['text'] === '✅ Yes') {
                            $this->buttonConfirmOrder = $button;
                        }
                        if ($button['text'] === '⌚️ Prolong') {
                            $button->click();
                        }
                    }
                }
            }

            $message = $update['message']['message'];

            if (preg_match("/\bPress “1” to cancel the deal\b/i", $message)) {
                $this->activeDeal = [];
                yield $this->sendMessage('1', 0);
            }

            if (preg_match("/\bA buyer has been found\b/i", $message)) {
                yield $this->buttonSendInfo->click();
            }

            if (preg_match("/\bCommand not recognized\b/i", $message)) {
                return;
            }

           /* if (preg_match("/^[\d\w-]{53}-UAH-KCode$/", $message)) {
                $this->warnAdmin = true;
                $this->model->setActiveDealStatus('successful');
                $this->model->saveActiveDeal();
                yield $this->sendMessage($message);
            }*/

            if (preg_match("/\bThe validity period for Deal\b/i", $message)) {
                $this->warnAdmin = true;
                yield $this->sendMessage('Сделка в ожидании!');
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

    /**
     * @param array $messages
     * @param float $sleep
     * @return \Generator
     */
    public function sendMultyMessage(array $messages, $sleep = 0.1): \Generator
    {
        $peer = $this->warnAdmin ? self::ADMIN : self::BOT;
        try {
            sleep($sleep);
            yield $this->messages->sendMessage(['multiple' => true, ['peer' => '@' . $peer, 'message' => $messages[0], 'parse_mode' => 'HTML'], ['peer' => '@' . $peer, 'message' => $messages[1], 'parse_mode' => 'HTML']]);
        } catch (RPCErrorException $e) {
            $this->report("Surfaced: $e");
        } catch (Exception $e) {
            if (\stripos($e->getMessage(), 'invalid constructor given') === false) {
                $this->report("Surfaced: $e");
            }
        }
    }

}