<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%flows}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $operation_id
 * @property string $begin
 * @property string $debit
 * @property string $credit
 * @property string $end
 * @property string $datetime
 *
 * @property Operation $operation
 * @property Users $user
 */
class Flows extends ActiveRecord
{

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $last = Flows::find()
                    ->andWhere(['user_id' => $this->user_id])
                    ->orderBy(['datetime' => SORT_DESC])
                    ->limit(1)
                    ->one();
                if ($last) {
                    $this->begin = $last->end;
                } else {
                    $this->begin = 0;
                }
                $this->end = (double)$this->begin + (double)$this->debit - (double)$this->credit;
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            Users::updateAll([
                'balance' => $this->end,
            ], [
                'id' => $this->user_id,
            ]);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%flows}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'operation_id', 'debit', 'credit'], 'required'],
            [['user_id', 'operation_id'], 'integer'],
            [['begin', 'debit', 'credit', 'end'], 'number'],
            [['operation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Operation::className(), 'targetAttribute' => ['operation_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'operation_id' => 'Операция',
            'begin' => 'Начальный баланс',
            'debit' => 'Приход',
            'credit' => 'Расход',
            'end' => 'Конечный баланс',
            'datetime' => 'Создан',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperation()
    {
        return $this->hasOne(Operation::className(), ['id' => 'operation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public static function getDebitTotal(Users $user)
    {
        return Flows::find()->andWhere(['user_id' => $user->id])->sum('debit');
    }

    public static function getCreditTotal(Users $user)
    {
        return Flows::find()->andWhere(['user_id' => $user->id])->sum('credit');
    }

    public static function getBeginTotal(Users $user)
    {
        $item = Flows::find()->andWhere(['user_id' => $user->id])->orderBy(['datetime' => SORT_ASC])->limit(1)->one();
        return is_null($item) ? 0.0 : (double)$item->begin;
    }

    public static function getEndTotal(Users $user)
    {
        $item = Flows::find()->andWhere(['user_id' => $user->id])->orderBy(['datetime' => SORT_DESC])->limit(1)->one();
        return is_null($item) ? 0.0 : (double)$item->end;
    }

    /**
     * @inheritdoc
     * @return FlowsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FlowsQuery(get_called_class());
    }
}
