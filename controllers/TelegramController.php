<?php


namespace app\controllers;


use danog\MadelineProto\API;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class TelegramController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'new-tel', 'reg-telegram'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id == 'reg-telegram') {
            Yii::$app->request->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionNewTel()
    {
        return $this->render('new-tel');
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionRegTelegram()
    {   $request1 = Yii::$app->request->post();
        print_r($request1);
        if (Yii::$app->request->isPost) {
            $request = Yii::$app->request->post();
            if (!empty($request['number'])) {
                Yii::$app->session->set('number', $request['number']);
            }
            $settings = [
                'logger' =>
                    [
                        'logger_param' => '../runtime/logs/Madeline.log'
                    ]
            ];
            $path = '../runtime/' . Yii::$app->session->get('number') . '.madeline';
            $MadelineProto = new API($path, $settings);
            $MadelineProto->start();
            if ( $request['phone_code'] )
                $this->redirect(['/telegram']);
        }
    }


}