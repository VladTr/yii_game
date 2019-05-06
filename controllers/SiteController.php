<?php

namespace app\controllers;

use app\models\Result;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\User;
use yii\web\HttpException;

class SiteController extends Controller
{

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
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

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
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

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();


        if ($model->load(Yii::$app->request->post())) {
            $token = $model->login();
            if($token) {
                $cookies = Yii::$app->response->cookies;
                $cookies->add(new \yii\web\Cookie([
                    'name' => 'token',
                    'value' => $token
                ]));

                return $this->render('game');
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Main game controller.
     *
     * @return Response
     */
    public function actionGame()
    {
        $cookies = Yii::$app->request->cookies;
        $token = $cookies->getValue('token');
        $user = User::find()
            ->where(['token' => $token])
            ->one();

        if (!$user) {
            throw new HttpException(404 ,'User not found');
        }

        $awardTypeId = rand(1, 3);
        $resultModel = new Result();

        switch ($awardTypeId) {
            case 1:  //ToDo move to constant file
                $result = $resultModel->handleAward($user->id);
                $awardType = 'award';
                break;
            case 2:
                $result = $resultModel->handleScores($user->id);
                $awardType = 'score';
                break;
            case 3:
                $result = $resultModel->handleMoney($user->id);
                $awardType = 'money';
                break;
        }

        return $this->asJson(['type'=>$awardType, 'value'=>$result]);
    }

}
