<?php
/**
 * ChangelogPanel.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace rmrevin\yii\changelog\debug\panels;

/**
 * Class ChangelogPanel
 * @package rmrevin\yii\changelog\debug\panels
 */
class ChangelogPanel extends \yii\debug\Panel
{

    /** @var string */
    public $summaryViewAlias = '@rmrevin/yii/changelog/debug/views/panels/changelog/summary';

    /** @var string */
    public $detailViewAlias = '@rmrevin/yii/changelog/debug/views/panels/changelog/detail';

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return \Yii::t('app', 'Changelog');
    }

    /**
     * @return string short name of the panel, which will be use in summary.
     */
    public function getSummaryName()
    {
        return \Yii::t('app', 'Changelog');
    }

    /**
     * @inheritdoc
     */
    public function getSummary()
    {
        return \Yii::$app->view->render($this->summaryViewAlias, [
            'panel' => $this,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getDetail()
    {
        $searchModel = new \rmrevin\yii\changelog\debug\models\search\Changelog;
        $dataProvider = $searchModel->search(\Yii::$app->get('request')->getQueryParams());

        return \Yii::$app->view->render($this->detailViewAlias, [
            'panel' => $this,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}