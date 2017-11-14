<?php
use yii\widgets\ListView;

/**
 * @var \yii\web\View
 * @var \common\models\game\GameHistory  $model , the data model
 * @var int $key , the key value associated with the data item
 * @var integer $index , the zero-based index of the data item in the items array returned by [[dataProvider]].
 * @var ListView $widget , this widget instance
 */
?>
<divwidthwidth class="list-group-item">
    <div class="history-item-header">
        <div class="row">
            <div class="col-sm-8">
                <b class="list-group-item-heading"><?= $model->title ?></b>
            </div>
            <div class="col-sm-4 text-right">
                <?= \yii\helpers\Html::tag('span', $model->score_cost, [
                    'class' => 'pull-right btn btn-xs ' . ($model->score_cost > 0
                            ? 'btn-success'
                            : ($model->score_cost < 0 ? 'btn-danger' : 'btn-primary'))
                ])?>
            </div>
        </div>

    </div>


</divwidthwidth>