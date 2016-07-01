<?php

namespace app\models;

use app\components\StatusActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%document_transfer}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $recipient_id
 * @property string $comment
 * @property string $value
 * @property string $datetime
 * @property integer $status
 *
 * @property Users $recipient
 * @property Users $user
 * @property Operation $operation
 */
class DocumentTransfer extends StatusActiveRecord
{
    const SCENARIO_USER = 'user';

    public $recipient_name;

    public static function statusList()
    {
        return [
            self::STATUS_ACTIVE => 'Выполнен',
            self::STATUS_NOT_ACTIVE => 'Не выполнен',
        ] + parent::statusList();
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if ($this->recipient_name && is_null($this->recipient_id)) {
                $user = Users::findByUsername($this->recipient_name);
                if ($user) {
                    $this->recipient_id = $user->id;
                }
            }
            return true;
        }
        return false;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->status = self::STATUS_NOT_ACTIVE;
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
            $tr = $this->getDb()->beginTransaction();
            try {

                $operation = new Operation([
                    'user_id' => $this->user_id,
                    'document_id' => $this->id,
                    'document_type' => Operation::DOCUMENT_TYPE_TRANSFER,
                    'value' => $this->value,
                ]);
                if ($operation->save()) {

                    $flow1 = new Flows([
                        'user_id' => $this->recipient_id,
                        'operation_id' => $operation->id,
                        'debit' => $this->value,
                        'credit' => 0,
                    ]);
                    $flow2 = new Flows([
                        'user_id' => $this->user_id,
                        'operation_id' => $operation->id,
                        'debit' => 0,
                        'credit' => $this->value,
                    ]);

                    if ($flow1->save() && $flow2->save()) {
                        $this->status = self::STATUS_ACTIVE;
                        $this->updateAttributes([
                            'status' => $this->status,
                        ]);
                        $tr->commit();
                    }
                }
            } catch (\Exception $e) {
                $tr->rollBack();
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%document_transfer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'recipient_id', 'value', 'recipient_name'], 'required'],
            [['user_id', 'recipient_id', 'status'], 'integer'],
            [['value'], 'number'],
            ['value', 'compare', 'compareValue' => 0, 'operator' => '>'],
            ['recipient_id', function ($attribute, $params) {
                if ($this->$attribute == $this->user_id) {
                    $this->addError($attribute, 'Нельзя делать перевод самому себе');
                }
            }],
            ['recipient_name', function ($attribute, $params) {
                if ($this->$attribute == $this->user->username) {
                    $this->addError($attribute, 'Нельзя делать перевод самому себе');
                }
            }],
            [['recipient_name'], 'string', 'max' => 50],
            [['comment'], 'string', 'max' => 255],
            [['recipient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['recipient_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['status', 'default', 'value' => self::STATUS_NOT_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::statusList())],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_USER] = ['recipient_name', 'comment', 'value'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'recipient_id' => 'Получатель',
            'recipient_name' => 'Получатель',
            'comment' => 'Комментарий',
            'value' => 'Сумма',
            'datetime' => 'Создан',
            'status' => 'Статус',
            'statusName' => 'Статус',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipient()
    {
        return $this->hasOne(Users::className(), ['id' => 'recipient_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperation()
    {
        return $this->hasOne(Operation::className(), ['document_id' => 'id'])
            ->andWhere(['document_type' => Operation::DOCUMENT_TYPE_TRANSFER]);
    }

    /**
     * @inheritdoc
     * @return DocumentTransferQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DocumentTransferQuery(get_called_class());
    }
}
