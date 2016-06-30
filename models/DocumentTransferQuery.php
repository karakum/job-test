<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[DocumentTransfer]].
 *
 * @see DocumentTransfer
 */
class DocumentTransferQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DocumentTransfer[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DocumentTransfer|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
