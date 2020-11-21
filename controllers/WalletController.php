<?php

namespace app\controllers;

use app\models\ReplenishForm;
use Yii;
use app\models\Wallet;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WalletController implements the CRUD actions for Wallet model.
 */
class WalletController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [

                ],
            ],
        ];
    }

    /**
     * Lists all Wallet models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Wallet::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Wallet model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Wallet model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Wallet();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->id_user = Yii::$app->getUser()->id;

            if ($model->save()) {
                return json_encode(['result' => 'success']);
            }
        }
    }

    /**
     * Finds the Wallet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Wallet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Wallet::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetReplenishForm() {

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $idWallet = Yii::$app->request->post('id');

            $model = $this->findModel($idWallet);
            $replenishForm = new ReplenishForm();

            $replenishForm->id_wallet = $model->id;

            return $this->renderAjax('_replenish_form', [
                'model' => $replenishForm,
                'title' => $model->title,
            ]);
        }

        $this->render('error', [
            'message' => 'Page not found',
        ]);
    }

    public function actionReplenish() {
        $replenishForm = new ReplenishForm();

        if ($replenishForm->load(Yii::$app->request->post()) && $replenishForm->validate()) {
            $model = $this->findModel($replenishForm->id_wallet);
            $model->amount += $replenishForm->amount;

            if ($model->save()) {
                return json_encode(['result' => 'success']);
            }
        }
    }
}
