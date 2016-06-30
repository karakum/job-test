<?php

use app\models\DocumentInvoice;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\DocumentInvoice */

$this->title = 'Счет #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-invoice-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($model->payer_id == Yii::$app->user->id && $model->status == DocumentInvoice::STATUS_NOT_ACTIVE) { ?>
        <p>
            <?= Html::a('Оплатить', ['accept', 'id' => $model->id], [
                'class' => 'btn btn-primary',
                'data' => [
                    'confirm' => 'Вы действительно хотите оплатить счет?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Отказаться', ['reject', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы действительно хотите отказаться от оплаты счета?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user.username:text:Отправитель',
            'payer.username:text:Плательщик',
            'comment',
            'value:currency',
            'datetime',
            [
                'attribute' => 'status',
                'value' => $model->payer_id == Yii::$app->user->id && $model->status == DocumentInvoice::STATUS_NOT_ACTIVE ?
                    'Входящий' : $model->getStatusName(),
            ],

        ],
    ]) ?>

</div>
