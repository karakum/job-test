<?php

namespace app\controllers;

use Yii;
use app\models\Flows;
use app\models\FlowsSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BalanceController implements the CRUD actions for Flows model.
 */
class BalanceController extends Controller
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
                            'index',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Flows models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FlowsSearch();
        $params = Yii::$app->request->queryParams;
        $params['FlowsSearch']['user_id'] = Yii::$app->user->id;
        $dataProvider = $searchModel->search($params);

        $begin = Flows::getBeginTotal(Yii::$app->user->identity);
        $end = Flows::getEndTotal(Yii::$app->user->identity);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'begin' => $begin,
            'end' => $end,
        ]);
    }

}
