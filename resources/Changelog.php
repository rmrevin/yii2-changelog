<?php
/**
 * Changelog.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace rmrevin\yii\changelog\resources;

use rmrevin\yii\changelog\components\LogsStorage;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\di\Instance;

/**
 * Class Changelog
 * @package resources
 *
 * @property integer $id
 * @property integer $action
 * @property string $entity_type
 * @property string $entity_id
 * @property string $present
 * @property string $changes
 * @property string $env
 * @property integer $created_at
 * @property integer $updated_at
 */
class Changelog extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            /** type validators */
            [['action', 'created_at', 'updated_at'], 'integer'],
            [['entity_type', 'entity_id', 'present', 'changes', 'env'], 'string'],

            /** semantic validators */
            [['entity_type', 'entity_id', 'present', 'changes', 'env'], 'required'],
            [['entity_type', 'entity_id'], 'filter', 'filter' => 'trim'],

            /** default values */
        ];
    }

    /**
     * @return array
     */
    public static function getActions()
    {
        /** @var LogsStorage $Storage */
        $Storage = Instance::ensure([
            'class' => LogsStorage::className(),
        ]);

        return [
            $Storage::ACTION_INSERT => \Yii::t('app', 'Insert'),
            $Storage::ACTION_UPDATE => \Yii::t('app', 'Update'),
            $Storage::ACTION_DELETE => \Yii::t('app', 'Delete'),
        ];
    }

    /**
     * @return array
     */
    public static function getAllEntityTypes()
    {
        $types = static::find()
            ->select('entity_type')
            ->groupBy(['entity_type'])
            ->asArray()
            ->all();

        $types = array_column($types, 'entity_type');

        return empty($types) ? [] : array_combine($types, $types);
    }

    /**
     * @param string $period
     * @return int
     */
    public static function prune($period = '-90 days')
    {
        $threshold = strtotime($period);

        return static::deleteAll(['<', 'created_at', $threshold]);
    }

    /**
     * @return queries\ChangelogQuery
     */
    public static function find()
    {
        /** @var queries\ChangelogQuery $Query */
        $Query = \Yii::$container->get(queries\ChangelogQuery::className(), [get_called_class()]);

        return $Query;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%changelog}}';
    }
}
