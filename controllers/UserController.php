<?php

namespace app\controllers;

use app\models\Transfer;
use app\models\Wallet;
use Yii;
use app\models\User;
use yii\data\ArrayDataProvider;
use yii\data\Sort;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * {@inheritdoc}
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
        ];
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): User
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionProfile($login): string
    {
        /** @var User $model */
        $model = User::getAccountInfo($login);

        if(!$model) {
            return $this->render('/site/error', [
               'message' => 'User not found',
            ]);
        }

        if ($model->id !== Yii::$app->user->getId()) {
            return $this->render('/site/error', [
                'message' => 'Not allowed. You haven\'t access to this page',
            ]);
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $model->wallets,
            'sort' => new Sort([
                'attributes' => [
                    'id',
                    'title',
                    'amount',
                    'created_at',
                    'updated_at',
                ]
            ]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $dataProvider->prepare();

        return $this->render('profile', [
            'model' => $model,
            'wallet' => new Wallet(),
            'transfer' => new Transfer(),
            'dataProvider' => $dataProvider,
        ]);
    }
}
