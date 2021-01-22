<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class RBACController extends Controller
{
    /**
     * @throws yii\base\Exception
     * @throws \Exception
     */
    public function actionInitRoles(): void
    {
        $auth = Yii::$app->authManager;
        $user = $auth->createRole('user');
        $auth->add($user);
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $user);
    }
}