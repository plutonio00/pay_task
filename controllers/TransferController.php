<?php

namespace app\controllers;

use app\models\TransferStatus;
use app\models\User;
use app\models\Wallet;
use app\utils\ArrayUtils;
use DateTime;
use Yii;
use app\models\Transfer;
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
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Creates a new Transfer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
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

                if ($model->save()) {
                    return ['result' => 'success'];
                }
            }

            return ['result' => 'success'];
        }

        return $this->render('/site/error', [
            'message' => 'Page not found',
        ]);
    }

    /**
     * Updates an existing Transfer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Transfer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Transfer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transfer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transfer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetTabContent() {

        if (Yii::$app->request->isAjax) {
            $model = new Transfer();
            $this->layout = false;
            $idUser = Yii::$app->user->getId();


            return $this->renderAjax('_transfers_tab_content', [
                'model' => $model,
                'transfers' => Transfer::getTransfersForUser($idUser),
                'user' => User::findOne(['id' => $idUser]),
            ]);
        }

        return $this->render('/site/error', [
            'message' => 'Page not found',
        ]);
    }

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

            return [
                'success' => $model->save(),
                'errors' => $model->errors,
            ];
        }

        return $this->render('/site/error', [
            'message' => 'Page not found',
        ]);
    }
}
