<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\KunaCodeBot */
?>
<div class="kuna-code-bot-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'bank',
            'max_percent',
            'available_sum',
            'min_sum',
        ],
    ]) ?>

</div>
