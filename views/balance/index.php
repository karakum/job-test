<?php

use app\components\FormatterHelper;
use app\models\Flows;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FlowsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Баланс';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flows-index">

    <h1><?= Html::encode($this->title . ' ' . Yii::$app->formatter->asCurrency(Yii::$app->user->identity->balance)) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'operation_id',
                'format' => 'raw',
                'value' => function (Flows $data) {
                    return FormatterHelper::operationLink($data->operation);
                },
            ],
            [
                'attribute' => 'begin',
                'contentOptions' => ['style' => 'text-align: right;'],
                'options' => ['style' => 'width: 100px;'],
            ],
            [
                'attribute' => 'debit',
                'value' => function (Flows $data) {
                    return $data->debit > 0 ? $data->debit : '';
                },
                'contentOptions' => ['style' => 'text-align: right;'],
                'options' => ['style' => 'width: 100px;'],
            ],
            [
                'attribute' => 'credit',
                'value' => function (Flows $data) {
                    return $data->credit > 0 ? $data->credit : '';
                },
                'contentOptions' => ['style' => 'text-align: right;'],
                'options' => ['style' => 'width: 100px;'],
            ],
            [
                'attribute' => 'end',
                'contentOptions' => ['style' => 'text-align: right;'],
                'options' => ['style' => 'width: 100px;'],
            ],
            [
                'attribute' => 'datetime',
                'options' => ['style' => 'width: 170px;'],
            ],
        ],
    ]); ?>
</div>
