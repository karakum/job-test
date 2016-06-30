<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%operation}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $document_id
 * @property integer $document_type
 * @property string $value
 * @property string $datetime
 *
 * @property Flows[] $flows
 * @property Users $user
 */
class Operation extends ActiveRecord
{
    const DOCUMENT_TYPE_TRANSFER = 1;
    const DOCUMENT_TYPE_INVOICE = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%operation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'document_id', 'document_type', 'value'], 'required'],
            [['user_id', 'document_id', 'document_type'], 'integer'],
            [['value'], 'number'],
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
            'document_id' => 'Document ID',
            'document_type' => 'Document Type',
            'value' => 'Value',
            'datetime' => 'Datetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlows()
    {
        return $this->hasMany(Flows::className(), ['operation_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return OperationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OperationQuery(get_called_class());
    }
}
