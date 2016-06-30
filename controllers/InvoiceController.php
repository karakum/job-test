<?php

namespace app\controllers;

use Yii;
use app\models\DocumentInvoice;
use app\models\DocumentInvoiceSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InvoiceController implements the CRUD actions for DocumentInvoice model.
 */
class InvoiceController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'accept' => ['POST'],
                    'reject' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index', 'view', 'create', 'accept', 'reject',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all DocumentInvoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DocumentInvoiceSearch();
        $params = Yii::$app->request->queryParams;
        $params['DocumentInvoiceSearch']['includeIncome'] = true;
        $params['DocumentInvoiceSearch']['user_id'] = Yii::$app->user->id;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DocumentInvoice model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DocumentInvoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DocumentInvoice();
        $model->scenario = DocumentInvoice::SCENARIO_USER;
        $model->user_id = Yii::$app->user->id;
        $model->status = DocumentInvoice::STATUS_NOT_ACTIVE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Оплатить счет
     * @param $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionAccept($id)
    {
        $model = $this->findModel($id);
        if ($model->payer_id != Yii::$app->user->id || $model->status != DocumentInvoice::STATUS_NOT_ACTIVE) {
            throw new ForbiddenHttpException('Не разрешено выполнять операцию');
        }
        $model->status = DocumentInvoice::STATUS_ACTIVE;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * Отказаться от оплаты счета
     * @param $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionReject($id)
    {
        $model = $this->findModel($id);
        if ($model->payer_id != Yii::$app->user->id || $model->status != DocumentInvoice::STATUS_NOT_ACTIVE) {
            throw new ForbiddenHttpException('Не разрешено выполнять операцию');
        }
        $model->status = DocumentInvoice::STATUS_REJECT;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing DocumentInvoice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DocumentInvoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DocumentInvoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DocumentInvoice::find()
                ->andWhere(['id' => $id])
                ->andWhere([
                    'or',
                    ['user_id' => Yii::$app->user->id],
                    ['payer_id' => Yii::$app->user->id],
                ])
                ->one()) !== null
        ) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
