<?php
/**
 * ChangelogPanel.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace rmrevin\yii\changelog\debug\panels;

use rmrevin\yii\changelog\debug\models\search\ChangelogSearch;

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

    /** @var string */
    public $request = 'request';

    /** @var string */
    public $view = 'view';

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
        /** @var \yii\web\Request $request */
        $request = \Yii::$app->get($this->request);

        /** @var \yii\web\View $view */
        $view = \Yii::$app->get($this->view);

        /** @var ChangelogSearch $searchModel */
        $searchModel = \Yii::createObject(ChangelogSearch::className());

        $dataProvider = $searchModel
            ->search($request->getQueryParams());

        return $view->render($this->detailViewAlias, [
            'panel' => $this,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}