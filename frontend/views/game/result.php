<?php
/**
 * @var $this \yii\web\View
 * @var $game \common\models\game\Game
 */
use yii\helpers\Html;

$this->title = 'Результат последней игры';
?>
<div class="game">
    <div class="row">
        <div class="col-md-12">
            <h1><?= $this->title;?></h1>

            <?php if($game):?>
                <div class="img-responsive">
                    <?= Html::img($game->meme->image, ['alt' => $game->meme->title])?>
                </div>

                <?= \yii\widgets\DetailView::widget([
                    'model' => $game,
                    'attributes' => [
                        'score',
                        'created_at:datetime',
                        'meme.title',
                        'meme.about:html',
                        'meme.url:html',
                    ],
                ])?>
                <p class="text-center">
                    <?= Html::a('Повторить игру?', ['/game/start'], ['class' => 'btn btn-primary'])?>
                </p>
            <?php else:?>
                <p>Не найдено завершенных игр</p>
                <?= Html::a('Начать новую игру?', ['/game/start'], ['class' => 'btn btn-primary'])?>

            <?php endif;?>

        </div>
    </div>
</div>
