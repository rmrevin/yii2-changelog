<?php
/**
 * ChangelogPanel.php
 * @author Revin Roman
 * @link https://rmrevin.com
 *
 * @var rmrevin\yii\changelog\debug\panels\ChangelogPanel $panel
 */

use yii\helpers\Html;

?>
<div class="yii-debug-toolbar__block">
    <?= Html::a($panel->getSummaryName(), $panel->getUrl(), [
        'title' => Yii::t('app', 'Show changelog.'),
    ]) ?>
</div>
