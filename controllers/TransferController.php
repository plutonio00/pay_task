<?php

namespace app\controllers;

use app\exceptions\TransferStatusNotFoundException;
use app\models\Constants;
use app\models\search\TransferSearch;
use app\models\TransferStatus;
use app\models\User;
use app\models\Wallet;
use app\utils\ArrayUtils;
use DateTime;
use JsonException;
use Throwable;
use Yii;
use app\models\Transfer;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TransferController implements the CRUD actions for Transfer model.
 */
class TransferController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [],
            ],
        ];
    }

    /**
     * Creates a new Transfer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws JsonException
     * @throws TransferStatusNotFoundException
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new Transfer();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            /**
             * Due to the fact that ajax validation is used, the model has to be partially validated.
             * Otherwise, you would either have to create a separate class for the form,
             * which would lead to duplicate code,
             * or make additional queries to the database with each attempt to validate.
            */
            $model->validate(Transfer::FIELDS_FOR_FORM_VALIDATION);

            if ($model->errors) {
                /**
                 * Converting an array of model errors to an array of errors of ActiveForm
                 * to display errors on the form
                */
                return ArrayUtils::addPrefixToAllKeys(Transfer::tableName() . '-', $model->errors);
            }

            $formWasSubmit = Yii::$app->request->post('was_submit');

            if (isset($formWasSubmit)) {

                $model->id_status = TransferStatus::getIdByTitle(TransferStatus::IN_PROGRESS);
                $model->id_sender = Yii::$app->user->getId();

                /** @var Wallet $recipientWallet */
                $recipientWallet = Wallet::findOne([
                    'id' => $model->id_recipient_wallet,
                ]);

                $model->id_recipient = $recipientWallet->id_user;

                $saveResult = $model->save();
                if (!$saveResult) {
                    Yii::error(sprintf(
                        Constants::SAVE_MODEL_ERROR,
                        Transfer::tableName(),
                        json_encode($model->errors, JSON_THROW_ON_ERROR)
                    ));
                }

                return [
                    'success' => $saveResult,
                ];
            }

            return ['result' => 'success'];
        }

        return $this->render('/site/error', [
            'message' => 'Page not found',
        ]);
    }

    /**
     * Finds the Transfer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transfer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Transfer
    {
        if (($model = Transfer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return string
     * @throws TransferStatusNotFoundException
     */
    public function actionGetUserTransfers(): string
    {
        if (Yii::$app->request->isAjax) {
            $this->layout = false;

            $dataProvider = new ActiveDataProvider([
               'query' =>  Transfer::getTransfersForUser(Yii::$app->user->getId()),
            ]);

            $dataProvider->prepare();

            return $this->renderAjax('_transfers_list', [
                'dataProvider' => $dataProvider,
            ]);
        }

        return $this->render('/site/error', [
            'message' => 'Page not found',
        ]);
    }

    /**
     * @return array|string
     * @throws NotFoundHttpException
     * @throws TransferStatusNotFoundException
     * @throws JsonException
     */
    public function actionChangeStatus() {

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel(Yii::$app->request->post('id'));
            $changeType = Yii::$app->request->post('changeType');

            if ($changeType === 'cancel') {
                $idStatus = TransferStatus::getIdByTitle(TransferStatus::CANCELLED);
            }
            else {
                $idStatus = TransferStatus::getIdByTitle(TransferStatus::IN_PROGRESS);
                $execTime = new DateTime();
                $execTime->modify('+1 hour');
                $model->exec_time = $execTime->format(Transfer::TIMESTAMP_FORMAT);
            }

            $model->id_status = $idStatus;

            $saveResult = $model->save();
            if (!$saveResult) {
                Yii::error(sprintf(
                    Constants::SAVE_MODEL_ERROR,
                    Transfer::tableName(),
                    json_encode($model->errors, JSON_THROW_ON_ERROR)
                ));
            }

            return [
                'success' => $saveResult,
            ];
        }

        return $this->render('/site/error', [
            'message' => 'Page not found',
        ]);
    }
}
