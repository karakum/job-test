<?php

use app\models\DocumentInvoice;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DocumentInvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Счета';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-invoice-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Новый счет', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'options' => ['style' => 'width: 100px;'],
            ],
            [
                'label' => 'Отправитель',
                'attribute' => 'user_id',
                'value' => function (DocumentInvoice $data) {
                    return $data->user_id != Yii::$app->user->id ? $data->user->username : null;
                },
                'filter' => false,
            ],
            [
                'label' => 'Плательщик',
                'attribute' => 'payer_id',
                'value' => function (DocumentInvoice $data) {
                    return $data->payer_id != Yii::$app->user->id ? $data->payer->username : null;
                },
                'filter' => false,
            ],
            'comment',
            [
                'label' => 'Сумма',
                'attribute' => 'value',
                'format' => 'currency',
                'filter' => false,
                'value' => function (DocumentInvoice $data) {
                    return $data->value;
                },
                'contentOptions' => ['style' => 'text-align: right;'],
            ],
            [
                'attribute' => 'datetime',
                'filter' => false,
            ],
            [
                'attribute' => 'status',
                'value' => function (DocumentInvoice $data) {
                    return $data->payer_id == Yii::$app->user->id && $data->status == DocumentInvoice::STATUS_NOT_ACTIVE ?
                        'Входящий' : $data->getStatusName();
                },
                'filter' => false,
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {accept} {reject}',
                'buttons' => [
                    'accept' => function ($url, $data, $key) {
                        return $data->payer_id == Yii::$app->user->id && $data->status == DocumentInvoice::STATUS_NOT_ACTIVE ?
                            Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, [
                                'data' => [
                                    'method' => 'post',
                                    'confirm' => 'Оплатить счет?',
                                ],
                            ]) : '';
                    },
                    'reject' => function ($url, $data, $key) {
                        return $data->payer_id == Yii::$app->user->id && $data->status == DocumentInvoice::STATUS_NOT_ACTIVE ?
                            Html::a('<span class="glyphicon glyphicon-remove"></span>', $url, [
                                'data' => [
                                    'method' => 'post',
                                    'confirm' => 'Отказаться от оплаты?',
                                ],
                            ]) : '';
                    },
                ],
            ],
        ],
    ]); ?>
</div>
