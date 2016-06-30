<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Flows]].
 *
 * @see Flows
 */
class FlowsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Flows[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Flows|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
