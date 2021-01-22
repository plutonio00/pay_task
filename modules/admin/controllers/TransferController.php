<?php


namespace app\modules\admin\controllers;

use app\models\Constants;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class TransferController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => AccessControl::class,
                'only' => ['statistic'],
                'rules' => [
                    [
                        'actions' => ['statistic'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],

            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [],
            ],
        ];
    }

    public function actionStatistic(): string
    {
        $transferStatistic = Yii::$app->cache->get(Constants::CACHE_KEY_TRANSFER_DONE_STATISTIC);

        if (!$transferStatistic) {
            $transferStatistic = User::getLastDoneTransferForSender();
            Yii::$app->cache->set(Constants::CACHE_KEY_TRANSFER_DONE_STATISTIC, $transferStatistic);
        }

        return $this->render('statistic', [
            'transfer_statistic' => $transferStatistic,
        ]);
    }
}