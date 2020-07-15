<?php


namespace app\models;

use yii\base\BaseObject;

class KunaCodeBotModel extends BaseObject
{
    private $activeDeal;
    private $message;

    public function __construct($message,$config = [])
    {
        parent::__construct($config);
        $this->message = $message;

    }

    public function countOrders()
    {

        $botModel = new KunaCodeBot();
        $botParams = $botModel->findOne(['id' => 1]);
        $orders = $this->parseOrders($this->message);

        foreach ($orders as $order_id => $order) {
            if ($order['percent'] <= $botParams->max_percent) {
                if ($order['price'] <= $botParams->available_sum && $order['amount'] >= $botParams->min_sum) {
                    if ($botParams->bank === 'ANY' || $order['bank'] === $botParams->bank) {
                        $this->activeDeal = $order;
                        $this->saveOrder($order);
                        return $order;
                    }
                }
            }
        }

        $this->storeOrders($orders);

        return false;
    }


    public function parseOrders($message)
    {
        $orders_raw = explode("\n\n", $message, 20);
        $orders_received = [];
        foreach ($orders_raw as $order_string) {
            $order_string = json_encode($order_string, JSON_PRETTY_PRINT);
            $order_string = rtrim($order_string, '\n');
            $str = strpos($order_string, '\n');
            $order_string = substr($order_string, 0, $str);
            $order_string = str_replace('"', '', $order_string);
            $order_string = str_replace('#', '', $order_string);
            $order_string = str_replace('%', '', $order_string);
            $order_string = str_replace(' UAH', '', $order_string);
            $order_string = str_replace('\'', '', $order_string);
            $json_new[] = $order_string;
            unset ($order_string);
        }
        foreach ($json_new as $order) {
            $order_data = explode(' | ', $order);
            if (isset($order_data[1])) {
                $order_sorted['order_id'] = $order_data[0];
                $order_sorted['amount'] = floatval($order_data[1]);
                $order_sorted['percent'] = floatval($order_data[2]);
                $order_sorted['price'] = floatval($order_data[3]);
                $order_sorted['bank'] = $order_data[4];
                $orders_received[$order_sorted['order_id']] = $order_sorted;
            }
            unset ($order_data, $order);
        }

        return $orders_received;
    }

    public function storeOrders($orders)
    {
        if (KunaDeals::find()->limit(1)->one()) {

            $old_orders = KunaDeals::find()->select('order_id')->where(['executed' => false])->asArray()->all();

            if (!empty($old_orders)) {
                foreach ($old_orders as $old_order) {
                    $old_orders_ids[] = $old_order['order_id'];
                }

                !empty($old_orders_ids) ? $old_orders_ids = array_flip($old_orders_ids) : '';

                $closed_orders = array_diff_key($old_orders_ids, $orders);
                if (!empty($closed_orders)) {
                    foreach ($closed_orders as $closed_order_id => $value) {
                        $closed_order_ids[] = $closed_order_id;
                    }
                    $closed_orders_base = KunaDeals::find()->where(['order_id' => $closed_order_ids])->all(); // создаём модель
                    foreach ($closed_orders_base as $order) {
                        $order->executed = true;
                        \Yii::info('Old order update: ' . PHP_EOL);
                        $order->update();
                    }
                }

                $new_orders = array_diff_key($orders, $old_orders_ids);

            } else {
                $new_orders = $orders;
            }
            foreach ($new_orders as $new_order) {
                if (!empty($renew_order = KunaDeals::find()->where(['order_id' => $new_order['order_id']])->one())) {
                    $this->renewOrder($renew_order);
                } else {
                    $this->saveOrder($new_order);
                }
            }

        } else {

            dump('First cycle: ' . PHP_EOL);
            foreach ($orders as $new_order) {

                $table = new KunaDeals();
                $table->attributes = $new_order; // загружаем из массива
                $table->save(false);

            }
        }
    }

    public function renewOrder($order)
    {
        $order->executed = false;
        $order->update();
    }

    public function saveOrder($order)
    {
        if (!(KunaDeals::find()->where(['order_id' => $order['order_id']])->one())) {
            $table = new KunaDeals();
            $table->attributes = $order; // загшружаем из массива
            $table->save(false);
            unset ($table);
        }
    }
}