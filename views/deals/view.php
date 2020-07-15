<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\KunaDeals */
?>
<div class="kuna-deals-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'order_id',
            'amount',
            'percent',
            'price',
            'bank',
            'executed:boolean',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
