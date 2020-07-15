<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "kuna_deals".
 *
 * @property string|null $order_id
 * @property int|null $amount
 * @property int|null $percent
 * @property int|null $price
 * @property string|null $bank
 * @property bool|null $executed
 * @property int $created_at
 * @property int $updated_at
 */
class KunaDeals extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kuna_deals';
    }


    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'percent', 'price', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['amount', 'percent', 'price', 'order_id', 'bank', 'created_at', 'updated_at', 'executed'], 'safe'],
            [['created_at', 'updated_at'], 'integer'],
            [['executed'], 'boolean'],
            [['created_at', 'updated_at'], 'required'],
            [['order_id', 'bank'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'amount' => 'Amount',
            'percent' => 'Percent',
            'price' => 'Price',
            'bank' => 'Bank',
            'executed' => 'Executed',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
