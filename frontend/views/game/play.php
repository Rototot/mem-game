<?php
/**
 * @var $this \yii\web\View
 * @var $game \common\models\game\Game
 * @var $gameFormAnswer \frontend\models\game\GameForm
 */
use yii\helpers\Html;

$this->title = 'Угадай мемасик!';
?>

<div class="game">

    <div class="row">
        <div class="col-md-7">
            <div class="row">
                <div class="col-md-12">
                    <h3><?= $this->title; ?></h3>
                    <?php foreach ($game->gameMemeSections as $gameMemeSection): ?>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-12">
                    <h3>Подсказки</h3>

                    <div class="list-group">
                        <a href="#" class="list-group-item">
                            <h4 class="list-group-item-heading">Год возникновения</h4>
                            <p class="list-group-item-text">
                                - 10 баллов. Можно использовать 1 раз
                            </p>
                        </a>
                        <a href="#" class="list-group-item">
                            <h4 class="list-group-item-heading">Случайное ключевое слово</h4>
                            <p class="list-group-item-text">
                                - 10 баллов. Можно использовать несколько раз
                            </p>
                        </a>

                        <a href="#" class="list-group-item">
                            <h4 class="list-group-item-heading">Случайное слово из раздела about</h4>
                            <p class="list-group-item-text">
                                - 5 баллов. Можно использовать несколько раз
                            </p>
                        </a>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-5">
            <h4>История игры</h4>
            <!-- todo история-->
        </div>
    </div>


    <div class="game-btn-group">
        <div class="row">
            <div class="col-md-7">
                <?php $form = \yii\widgets\ActiveForm::begin([
                        'action' => ['/game/check-answer'],
                        'method' => 'post',
                ]); ?>
                <?= $form->field($gameFormAnswer, 'answer')->textInput() ?>

                <p>
                    <?= Html::submitButton('Ответить', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('Сдаться!', ['/game/surrender'], ['class' => 'btn btn-danger']) ?>
                </p>

                <?php $form::end(); ?>
            </div>
        </div>


    </div>
</div>
</div>
