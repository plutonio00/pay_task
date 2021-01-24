<?php

use app\models\Transfer;
use app\models\User;

/* @var User $user */
/* @var Transfer $model */


echo $this->render('/transfer/_create_form', [
    'model' => $model,
    'user' => $user
]);
?>

<div id="transfers-wrapper"></div>
