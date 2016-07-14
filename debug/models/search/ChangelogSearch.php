<?php
/**
 * ChangelogSearch.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace rmrevin\yii\changelog\debug\models\search;

use rmrevin\yii\changelog\resources\Changelog;
use rmrevin\yii\changelog\resources\queries\ChangelogQuery;
use yii\data\ActiveDataProvider;
use yii\di\Instance;

/**
 * Class ChangelogSearch
 * @package common\debug\models\search
 */
class ChangelogSearch extends \yii\debug\models\search\Base
{

    /**
     * @var string type of the input search value
     */
    public $action;

    /**
     * @var string type of the input search value
     */
    public $entity_type;

    /**
     * @var integer query attribute input search value
     */
    public $entity_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['action', 'entity_type', 'entity_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'action' => \Yii::t('app', 'Action'),
            'entity_type' => \Yii::t('app', 'Type'),
            'entity_id' => \Yii::t('app', 'ID'),
        ];
    }

    /**
     * Returns data provider with filled models. Filter applied if needed.
     *
     * @param array $params an array of parameter values indexed by parameter names
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params) && $this->validate();

        /** @var \yii\db\BaseActiveRecord $Model */
        $Model = Instance::ensure([
            'class' => Changelog::className(),
        ]);

        /** @var ChangelogQuery $ChangelogQuery */
        $ChangelogQuery = $Model::find();

        if (!empty($this->action)) {
            $ChangelogQuery->byAction($this->action);
        }

        if (!empty($this->entity_type)) {
            $ChangelogQuery->byEntityType($this->entity_type);
        }

        if (!empty($this->entity_id)) {
            $ChangelogQuery->byEntityId($this->entity_id);
        }

        /** @var ActiveDataProvider $DataProvider */
        $DataProvider = \Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $ChangelogQuery,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        return $DataProvider;
    }
}
