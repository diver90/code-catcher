<?php

namespace app\models;

/**
 * This is the model class for table "kuna_code_bot".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $bank
 * @property float|null $max_percent
 * @property float|null $available_sum
 * @property float|null $min_sum
 */
class KunaCodeBot extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kuna_code_bot';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['max_percent', 'available_sum', 'min_sum'], 'number'],
            [['name', 'bank'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'bank' => 'Bank',
            'max_percent' => 'Max Percent',
            'available_sum' => 'Available Sum',
            'min_sum' => 'Min Sum',
        ];
    }
}
