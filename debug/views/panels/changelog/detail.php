<?php
/**
 * ChangelogPanel.php
 * @author Revin Roman
 * @link https://rmrevin.com
 *
 * @var rmrevin\yii\changelog\debug\panels\ChangelogPanel $panel
 * @var rmrevin\yii\changelog\debug\models\search\ChangelogSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use rmrevin\yii\changelog\resources\Changelog;
use yii\bootstrap\Modal;
use yii\di\Instance;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/** @var Changelog $Model */
$Model = Instance::ensure([
    'class' => Changelog::className(),
]);

echo Html::tag('h1', $panel->getName());

Pjax::begin();
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'db-panel-detailed-grid',
    'options' => ['class' => 'detail-grid-view table-responsive'],
    'filterModel' => $searchModel,
    'filterUrl' => $panel->getUrl(),
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'action',
            'filter' => $Model::getActions(),
            'value' => function ($data) use($Model) {
                return empty($data['action'])
                    ? \Yii::t('app', 'Unknown')
                    : $Model::getActions()[$data['action']];
            },
            'options' => [
                'width' => '10%',
            ],
        ],
        [
            'format' => 'raw',
            'attribute' => 'entity_type',
            'filter' => $Model::getAllEntityTypes(),
            'value' => function ($data) {
                $result = empty($data['entity_type']) ? \Yii::t('app', 'Unknown type') : $data['entity_type'];

                if (!empty($data['present'])) {
                    $result .= '<br>' . Html::tag('small', sprintf(' > %s', $data['present']));
                }

                return $result;
            },
        ],
        [
            'attribute' => 'entity_id',
            'options' => [
                'width' => '10%',
            ],
        ],
        [
            'format' => 'raw',
            'attribute' => 'changes',
            'value' => function ($data) {
                $content = Html::tag('div', VarDumper::dumpAsString(unserialize($data['changes']), 10, true), [
                    'class' => 'content changes hidden',
                    'data-id' => $data['id'],
                ]);

                return empty($data['changes']) ? null : Html::a(\Yii::t('app', 'show'), '#', [
                        'data-role' => 'show-changes',
                        'data-id' => $data['id'],
                    ]) . $content;
            },
            'options' => [
                'width' => '7%',
            ],
        ],
        [
            'format' => 'raw',
            'attribute' => 'env',
            'value' => function ($data) {
                $content = Html::tag('div', VarDumper::dumpAsString(unserialize($data['env']), 10, true), [
                    'class' => 'content env hidden',
                    'data-id' => $data['id'],
                ]);

                return empty($data['env']) ? null : Html::a(\Yii::t('app', 'show'), '#', [
                        'data-role' => 'show-env',
                        'data-id' => $data['id'],
                    ]) . $content;
            },
            'options' => [
                'width' => '7%',
            ],
        ],
        [
            'attribute' => 'created_at',
            'value' => function ($data) {
                return Yii::$app->get('formatter')->asDatetime($data['created_at']);
            },
            'options' => [
                'width' => '20%',
            ],
        ],
    ],
]);
Pjax::end();

$this->registerJs('initializePopUps();');

echo Modal::widget([
    'id' => 'detail-content',
    'header' => 'Detail information',
]);

?>

<script type="text/javascript">
    function initializePopUps() {
        var $modal = jQuery('#detail-content');

        jQuery('body')
            .on('click', '[data-role="show-changes"]', function (e) {
                var $link = $(this);

                $modal.find('.modal-body').html(jQuery('.content.changes[data-id="' + $link.data('id') + '"]').html());
                $modal.modal('show');

                e.preventDefault();
            })
            .on('click', '[data-role="show-env"]', function (e) {
                var $link = $(this);

                $modal.find('.modal-body').html(jQuery('.content.env[data-id="' + $link.data('id') + '"]').html());
                $modal.modal('show');

                e.preventDefault();
            });
    }
</script>
