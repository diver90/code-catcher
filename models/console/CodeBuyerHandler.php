<?php


namespace app\models\console;

use app\models\KunaCodeBotModel;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\Exception;
use danog\MadelineProto\RPCErrorException;

class CodeBuyerHandler extends EventHandler
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
    private $buttonPaid;
    /**
     * @var KunaCodeBotModel|mixed
     */
    private $model;
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

        print_r($update);

        if (($update['message']['from_id'] == '734324493' || $update['message']['from_id'] == '786805975') && $update['message']['date'] >= time()-10) {

            $this->update = $update;
//buttons
            print_r('1');

            if (isset($update['message']['reply_markup']['rows'])) {
                foreach ($update['message']['reply_markup']['rows'] as $row) {
                    foreach ($row['buttons'] as $button) {
                        if ($button['text'] === '🤝 I have paid') {
                            $this->buttonPaid = $button;
                            print_r('2');
                        }
                        print_r('3');
                    }
                    print_r('4');
                }
                print_r('5');
            }

            $message = $update['message']['message'];

            /*dump($message);

            dump( $this->status);

            dump( $this->activeDeal);*/

            if (preg_match("/^Stop$/i", $message)) {
                $this->stop();
            }
            print_r('6');
            if (preg_match("/^Start$/i", $message)) {
                $this->status = self::NORMAL_CYCLE;
                yield $this->sendMessage('🔎 Orderbook UAH', 5);
            }
            print_r('7');
            if (preg_match("/^Paid$/i", $message)) {
                yield $this->buttonPaid->click();
            }
            print_r('8');
            if (preg_match("/^Cancel$/i", $message)) {
                $this->status = self::NORMAL_CYCLE;
                $this->activeDeal = [];
                yield $this->sendMessage('❌ Cancel deal', 0);
            }
            print_r('9');
            if (preg_match("/\bPress “1” to cancel the deal\b/i", $message)) {
                $this->activeDeal = [];
                yield $this->sendMessage('1', 0);
            }
            print_r('10');

            if (preg_match("/\bAnother deal has already been made based on this order\b/i", $message)) {
                $this->activeDeal = [];
                $this->warnAdmin = false;
                $this->model->setActiveDealStatus('failure');
                $this->model->saveActiveDeal();
                $this->status = self::NORMAL_CYCLE;
                yield $this->sendMessage('🔎 Orderbook UAH', 5);
            }
            print_r('11');
            if (preg_match("/\bCommand not recognized\b/i", $message)) {
                return;
            }
            print_r('12');
            if (preg_match("/\bOrder is not found\b/i", $message)) {
                $this->activeDeal = [];
                $this->model->setActiveDealStatus('failure');
                $this->model->saveActiveDeal();
                $this->status = self::NORMAL_CYCLE;
                yield $this->sendMessage('🔎 Orderbook UAH', 5);
            }
            print_r('13');
            if (preg_match("/\bin a deal creation\b/i", $message)) {
                $this->warnAdmin = true;
                $this->status = self::NORMAL_CYCLE;
                yield $this->sendMessage('Создание сделки ' . $this->activeDeal['order_id']);
            }
            print_r('14');
            if (preg_match("/\bPlease pay\b/i", $message)) {
                $this->warnAdmin = true;
                $this->status = self::NORMAL_CYCLE;
                yield $this->sendMessage('Сделка создана успешно, сумма к оплате ' . $this->activeDeal['price']);
            }
            print_r('15');
            if (preg_match("/^\d{16}$/i", $message)) {
                $this->warnAdmin = true;
                yield $this->sendMessage($message);
            }
            print_r('16');
            if (preg_match("/^[\d\w-]{53}-UAH-KCode$/", $message)) {
                $this->warnAdmin = true;
                $this->model->setActiveDealStatus('successful');
                $this->model->saveActiveDeal();
                $this->activeDeal = [];
                yield $this->sendMessage($message);
            }
            print_r('17');
            if (preg_match("/\bThe validity period for Deal\b/i", $message)) {
                $this->warnAdmin = true;
                yield $this->sendMessage('Сделка в ожидании!');
            }
            print_r('18');
            /*if (preg_match("/\byou are going to accept order\b/i", $message) && $this->status !== self::PAY_SENDED) {
                $this->status = self::PAY_SENDED;
                //dump('Pay send');
                yield $this->sendMessage('📥 Pay');
            }*/

            if (preg_match("/\bBuy this code\b/im", $message)) {
                $this->model->setMessage($message);
                $answer = $this->model->countOrders();
                print_r('18');
                if (!$answer) {
                    yield $this->sendMessage('🔎 Orderbook UAH', 5);
                    print_r('19');
                } elseif (empty($this->activeDeal) || $this->activeDeal['order_id'] === $answer['order_id']) {
                    $this->activeDeal = $answer;
                    $this->status = self::DEAL_SENDED;
                    yield $this->sendPayMessage([ '/deal' . $answer['order_id'], '📥 Pay']);
                    //yield $this->sendMessage('/deal' . $answer['order_id']);
                    print_r('20');
                } else {
                    var_dump($answer);
                    var_dump($this->status);
                }
            }
        }
        print_r('21');
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
    public function sendPayMessage(array $messages): \Generator
    {
        $peer = $this->warnAdmin ? self::ADMIN : self::BOT;
        try {
            $this->messages->sendMessage(['peer' => '@' . $peer, 'message' => $messages[0], 'parse_mode' => 'HTML']);
            sleep(0.01);
            yield $this->messages->sendMessage(['peer' => '@' . $peer, 'message' => $messages[1], 'parse_mode' => 'HTML']);
           // yield $this->messages->sendMessage(['multiple' => true, ['peer' => '@' . $peer, 'message' => $messages[0], 'parse_mode' => 'HTML'], ['peer' => '@' . $peer, 'message' => $messages[1], 'parse_mode' => 'HTML']]);
        } catch (RPCErrorException $e) {
            $this->report("Surfaced: $e");
        } catch (Exception $e) {
            if (\stripos($e->getMessage(), 'invalid constructor given') === false) {
                $this->report("Surfaced: $e");
            }
        }
    }

}