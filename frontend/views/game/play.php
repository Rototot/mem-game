<?php
/**
 * @var $this \yii\web\View
 * @var $game \common\models\game\Game
 * @var $gameFormAnswer \frontend\models\game\GameForm
 * @var $prepareActiveGameSections \common\models\game\GameMemeSection[]
 * @var $gameHistorySearch \common\models\game\GameHistorySearch,
 * @var $dataProvider \yii\data\ActiveDataProvider,
 */

use common\models\game\GameHistory;
use common\services\meme\MemeService;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Угадай мемасик!';

//todo вынести и дополнительно в кеш
$hintOriginIsUser = $game->getGameHistories()->byType(GameHistory::TYPE_HINT_YEAR_ORIGIN)->exists();

?>

<div class="game">

    <div class="row">
        <div class="col-md-7">
            <div class="row">
                <div class="col-md-12">
                    <h3><?= $this->title; ?></h3>
                    <table class="memo-sections">
                        <?php
                        for ($x = 0; $x < MemeService::DIVIDE_QTY_BLOCK_X; $x++):
                            echo '<tr>';
                            for ($y = 0; $y < MemeService::DIVIDE_QTY_BLOCK_Y; $y++):
                                echo '<td class="memo-section">';
                                if (isset($prepareActiveGameSections[$y][$x])) {
                                    /**
                                     * @var $gameMemeSection \common\models\game\GameMemeSection
                                     */
                                    $gameMemeSection = $prepareActiveGameSections[$y][$x];
                                    echo Html::img($gameMemeSection->memeSection->getImageWebPath(), ['class' => 'img-responsive']);
                                } else {
                                    echo '&nbsp;';
                                }
                                echo '</td>';
                            endfor;
                            echo '</tr>';
                        endfor;
                        ?>
                    </table>
                </div>
                <div class="col-md-12">
                    <h3>Подсказки</h3>

                    <div class="list-group">
                        <a href="<?= $hintOriginIsUser ? '#' : Url::to(['hint', 'type' => GameHistory::TYPE_HINT_YEAR_ORIGIN]) ?>"
                           class="list-group-item <?= $hintOriginIsUser ? 'disabled' : '' ?>"
                           title="<?= $hintOriginIsUser ? 'Подсказка уже использована' : '' ?>"
                        >
                            <h4 class="list-group-item-heading">Год возникновения</h4>
                            <p class="list-group-item-text">
                                - 10 баллов. Можно использовать 1 раз
                            </p>
                        </a>
                        <a href="<?= Url::to(['hint', 'type' => GameHistory::TYPE_HINT_RANDOM_TAG]) ?>"
                           class="list-group-item">
                            <h4 class="list-group-item-heading">Случайное ключевое слово</h4>
                            <p class="list-group-item-text">
                                - 10 баллов. Можно использовать несколько раз
                            </p>
                        </a>

                        <a href="<?= Url::to(['hint', 'type' => GameHistory::TYPE_HINT_RANDOM_ABOUT]) ?>"
                           class="list-group-item">
                            <h4 class="list-group-item-heading">Случайное слово из раздела about</h4>
                            <p class="list-group-item-text">
                                - 5 баллов. Можно использовать несколько раз
                            </p>
                        </a>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="game-btn-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <?php $form = \yii\widgets\ActiveForm::begin([
                                    'action' => ['skip-move'],
                                    'method' => 'post',
                                ]); ?>
                                    <?= Html::submitButton('Пропустить ход и получить следующий блок. (-1 балл)', [
                                        'class' => 'btn btn-primary btn-block',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                <?php $form::end(); ?>
                            </div>
                            <div class="col-sm-12">
                                <?php $form = \yii\widgets\ActiveForm::begin([
                                    'action' => ['check-answer'],
                                    'method' => 'post',
                                ]); ?>
                                <?= $form->field($gameFormAnswer, 'answer')->textInput() ?>

                                <p>
                                    <?= Html::submitButton('Ответить (+10 баллов)', ['class' => 'btn btn-success']) ?>
                                    <?= Html::a('Сдаться!', ['/game/surrender'], [
                                        'class' => 'btn btn-danger',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                </p>

                                <?php $form::end(); ?>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-5">
            <h4>История игры</h4>
            <div class="history">
                <div class="list-group">
                    <?= \yii\widgets\ListView::widget([
                        'dataProvider' => $dataProvider,
                        'itemView' => '_history',
                        'summary' => '',
                    ]) ?>
                </div>
            </div>

        </div>
    </div>
</div>