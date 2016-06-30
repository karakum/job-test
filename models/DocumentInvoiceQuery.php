<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[DocumentInvoice]].
 *
 * @see DocumentInvoice
 */
class DocumentInvoiceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DocumentInvoice[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DocumentInvoice|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
