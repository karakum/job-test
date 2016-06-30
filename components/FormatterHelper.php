<?php
/**
 * Created by PhpStorm.
 * User: Андрейка
 * Date: 01.07.2016
 * Time: 0:54
 */

namespace app\components;


use app\models\Operation;
use yii\helpers\Html;
use yii\helpers\Url;

class FormatterHelper
{

    /**
     * @param $op Operation
     * @return string
     */
    public static function operationLink($op)
    {
        switch ($op->document_type) {
            case Operation::DOCUMENT_TYPE_TRANSFER:
                return Html::a('Перевод #' . $op->document_id, Url::to(['/transfer/view', 'id' => $op->document_id]));
            case Operation::DOCUMENT_TYPE_INVOICE:
                return Html::a('Счет на оплату #' . $op->document_id, Url::to(['/invoice/view', 'id' => $op->document_id]));

        }
        return 'Операция #' . $op->id;
    }
}