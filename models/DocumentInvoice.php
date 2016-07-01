<?php

namespace app\models;

use app\components\StatusActiveRecord;
use Yii;

/**
 * This is the model class for table "document_invoice".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $payer_id
 * @property string $comment
 * @property string $value
 * @property string $datetime
 * @property integer $status
 *
 * @property Users $payer
 * @property Users $user
 * @property Operation $operation
 */
class DocumentInvoice extends StatusActiveRecord
{
    const STATUS_REJECT = 2;
    const SCENARIO_USER = 'user';

    public $payer_name;

    public static function statusList()
    {
        return [
            self::STATUS_ACTIVE => 'Оплачен',
            self::STATUS_NOT_ACTIVE => 'Отправлен',
            self::STATUS_REJECT => 'Отвергнут',
        ] + parent::statusList();
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if ($this->payer_name && is_null($this->payer_id)) {
                $user = Users::findByUsername($this->payer_name);
                if ($user) {
                    $this->payer_id = $user->id;
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
        if (!$insert && isset($changedAttributes['status'])
            && $changedAttributes['status'] == self::STATUS_NOT_ACTIVE
            && $this->status == self::STATUS_ACTIVE
        ) {
            $tr = $this->getDb()->beginTransaction();
            try {

                $operation = new Operation([
                    'user_id' => $this->user_id,
                    'document_id' => $this->id,
                    'document_type' => Operation::DOCUMENT_TYPE_INVOICE,
                    'value' => $this->value,
                ]);
                if ($operation->save()) {

                    $flow1 = new Flows([
                        'user_id' => $this->user_id,
                        'operation_id' => $operation->id,
                        'debit' => $this->value,
                        'credit' => 0,
                    ]);
                    $flow2 = new Flows([
                        'user_id' => $this->payer_id,
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
        return 'document_invoice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'payer_id', 'value'], 'required'],
            [['payer_name'], 'required', 'on' => self::SCENARIO_USER],
            [['user_id', 'payer_id', 'status'], 'integer'],
            [['value'], 'number'],
            ['value', 'compare', 'compareValue' => 0, 'operator' => '>'],
            ['payer_id', function ($attribute, $params) {
                if ($this->$attribute == $this->user_id) {
                    $this->addError($attribute, 'Нельзя выставлять счет самому себе');
                }
            }],
            ['payer_name', function ($attribute, $params) {
                if ($this->$attribute == $this->user->username) {
                    $this->addError($attribute, 'Нельзя выставлять счет самому себе');
                }
            }],
            [['payer_name'], 'string', 'max' => 50],
            [['comment'], 'string', 'max' => 255],
            [['payer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['payer_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['status', 'default', 'value' => self::STATUS_NOT_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::statusList())],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_USER] = ['payer_name', 'comment', 'value'];
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
            'payer_id' => 'Плательщик',
            'payer_name' => 'Плательщик',
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
    public function getPayer()
    {
        return $this->hasOne(Users::className(), ['id' => 'payer_id']);
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
            ->andWhere(['document_type' => Operation::DOCUMENT_TYPE_INVOICE]);
    }

    /**
     * @inheritdoc
     * @return DocumentInvoiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DocumentInvoiceQuery(get_called_class());
    }
}
