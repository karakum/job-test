<?php

use app\models\DocumentTransfer;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DocumentTransferSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Переводы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-transfer-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Новый перевод', ['create'], ['class' => 'btn btn-success']) ?>
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
                'value' => function (DocumentTransfer $data) {
                    return $data->user_id != Yii::$app->user->id ? $data->user->username : null;
                },
                'filter' => false,
            ],
            [
                'label' => 'Получатель',
                'attribute' => 'recipient_id',
                'value' => function (DocumentTransfer $data) {
                    return $data->recipient_id != Yii::$app->user->id ? $data->recipient->username : null;
                },
                'filter' => false,
            ],
            'comment',
            [
                'label' => 'Приход',
                'attribute' => 'value',
                'format' => 'currency',
                'filter' => false,
                'value' => function (DocumentTransfer $data) {
                    return $data->recipient_id == Yii::$app->user->id ? $data->value : null;
                },
                'contentOptions' => ['style' => 'text-align: right;'],
            ],
            [
                'label' => 'Расход',
                'attribute' => 'value',
                'format' => 'currency',
                'filter' => false,
                'value' => function (DocumentTransfer $data) {
                    return $data->user_id == Yii::$app->user->id ? $data->value : null;
                },
                'contentOptions' => ['style' => 'text-align: right;'],
            ],
            [
                'attribute' => 'datetime',
                'filter' => false,
            ],
            [
                'attribute' => 'status',
                'value' => function (DocumentTransfer $data) {
                    return $data->getStatusName();
                },
                'filter' => false,
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
