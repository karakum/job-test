<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\DocumentTransfer */

$this->title = 'Перевод #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Переводы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-transfer-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user.username:text:Отправитель',
            'recipient.username:text:Получатель',
            'comment',
            'value:currency',
            'datetime',
            'statusName',
        ],
    ]) ?>

</div>
