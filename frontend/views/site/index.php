<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">&nbsp;</div>
    <div class="jumbotron">
        <p>
            <?= \yii\helpers\Html::a('Начать игру', ['/game/start'], [
                    'class' => 'btn btn-success btn-lg',
                    'data' => [
                            'method' => 'POST'
                    ],
            ])?>
        </p>
    </div>

</div>
