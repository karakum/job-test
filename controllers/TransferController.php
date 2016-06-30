<?php

namespace app\controllers;

use Yii;
use app\models\DocumentTransfer;
use app\models\DocumentTransferSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TransferController implements the CRUD actions for DocumentTransfer model.
 */
class TransferController extends Controller
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
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index', 'view', 'create',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all DocumentTransfer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DocumentTransferSearch();
        $params = Yii::$app->request->queryParams;
        $params['DocumentTransferSearch']['includeIncome'] = true;
        $params['DocumentTransferSearch']['user_id'] = Yii::$app->user->id;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DocumentTransfer model.
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
     * Creates a new DocumentTransfer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DocumentTransfer();
        $model->scenario = DocumentTransfer::SCENARIO_USER;
        $model->user_id = Yii::$app->user->id;
        $model->status = DocumentTransfer::STATUS_NOT_ACTIVE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the DocumentTransfer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DocumentTransfer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DocumentTransfer::find()
                ->andWhere(['id' => $id])
                ->andWhere([
                    'or',
                    ['user_id' => Yii::$app->user->id],
                    ['recipient_id' => Yii::$app->user->id],
                ])
                ->one()) !== null
        ) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
