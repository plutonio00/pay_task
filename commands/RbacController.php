<?php

namespace app\commands;

use app\models\User;
use Exception;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class RbacController extends Controller
{
    /**
     * @throws yii\base\Exception
     * @throws Exception
     */
    public function actionInitRoles(): int
    {
        $auth = Yii::$app->authManager;
        $user = $auth->createRole('user');
        $auth->add($user);
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $user);

        return ExitCode::OK;
    }

    /**
     * @throws Exception
     */
    public function actionAssignRolesForFixtureUsers(): int
    {
        /** @var User[] $users */
        $users = User::find()->all();
        $auth = Yii::$app->authManager;
        $userRole = $auth->getRole('user');
        $adminRole = $auth->getRole('admin');
        $adminIndexes = array_rand($users, 2);

        foreach ($adminIndexes as $adminIndex) {
            $auth->assign($adminRole, $users[$adminIndex]->id);
            unset($users[$adminIndex]);
        }

        foreach ($users as $user) {
            $auth->assign($userRole, $user['id']);
        }

        return ExitCode::OK;
    }
}