<?php
/**
 * @var $this \yii\web\View
 * @var $game \common\models\game\Game
 * @var $gameHistorySearch \common\models\game\GameHistorySearch,
 * @var $dataProvider \yii\data\ActiveDataProvider,
 */
use yii\helpers\Html;

$this->title = 'Результат последней игры';
?>
<div class="game">
    <div class="row">
        <div class="col-md-12">
            <h1><?= $this->title; ?></h1>

            <?php if ($game): ?>
                <div class="img-responsive text-center">
                    <?= Html::img($game->meme->image, ['alt' => $game->meme->title]) ?>
                </div>
                <br/>

                <?= \yii\widgets\DetailView::widget([
                    'model' => $game,
                    'attributes' => [
                        [
                            'label' => 'Статус',
                            'value' => $game->isWin() ? 'Победа' : 'Поражение',
                        ],
                        [
                            'attribute' => 'score',
                            'value' => Html::tag('span', $game->score),
                            'format' => 'html'
                        ],
                        'meme.title',
                        'meme.origin_year',
                        'meme.about:html',
                        [
                            'attribute' => 'meme.url',
                            'value' => Html::a($game->meme->url, $game->meme->url),
                            'format' => 'html'
                        ],
                    ],
                ]) ?>

            <?php else: ?>
                <p>Не найдено завершенных игр</p>

            <?php endif; ?>

            <p class="text-center">
                <?= Html::a('Новая игра', ['/game/start'], [
                    'class' => 'btn btn-success btn-lg',
                    'data' => [
                        'method' => 'post'
                    ]
                ]) ?>
            </p>

            <h3>История</h3>

            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '_history',
                'summary' => '',
            ]) ?>
        </div>
    </div>
</div>
