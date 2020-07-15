<?php
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\KunaDealsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Deals';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

$js = <<<JS
var reloadT
	$('#autorefresh').click(function(){
	        if(reloadT)clearInterval(reloadT);
	        if($(this).children('i').hasClass('glyphicon-play')){
	            $(this).children('i').addClass('glyphicon-pause');
	            $(this).children('i').removeClass('glyphicon-play');
	            reloadT=setInterval(function(){
                    $('.glyphicon-repeat').trigger('click');
                    }, 5000);
	        } else {
	            $(this).children('i').addClass('glyphicon-play');
	            $(this).children('i').removeClass('glyphicon-pause')
	        }
	        })
JS;

$this->registerJs($js, $this::POS_LOAD);

?>
<?=Html::tag('div', '<i class="glyphicon glyphicon-play"></i>',
['id'=>'autorefresh', 'data-pjax'=>1,'class'=>'btn btn-default', 'title'=>'Start Autoupdate Grid'])?>
<div class="kuna-deals-index">

    <div id="ajaxCrudDatatable">
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax'=>true,
            'columns' => require(__DIR__.'/_columns.php'),
            'toolbar'=> [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                    ['data-pjax'=>1, 'class'=>'btn btn-default', 'title'=>'Reset Grid']).
                    '{toggleData}'.
                    '{export}'
                ],
            ],          
            'striped' => true,
            'condensed' => true,
            'responsive' => true,          
            'panel' => [
                'type' => 'primary', 
                'heading' => '<i class="glyphicon glyphicon-list"></i> Kuna Deals listing',
                'after'=>BulkButtonWidget::widget([
                            'buttons'=>Html::a('<i class="glyphicon glyphicon-trash"></i>&nbsp; Delete All',
                                ["bulkdelete"] ,
                                [
                                    "class"=>"btn btn-danger btn-xs",
                                    'role'=>'modal-remote-bulk',
                                    'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                                    'data-request-method'=>'post',
                                    'data-confirm-title'=>'Are you sure?',
                                    'data-confirm-message'=>'Are you sure want to delete this item'
                                ]),
                        ]).                        
                        '<div class="clearfix"></div>',
            ]
        ])?>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
