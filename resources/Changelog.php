<?php
/**
 * Changelog.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace rmrevin\yii\changelog\resources;

use rmrevin\yii\changelog\interfaces\ChangelogModelInterface;
use rmrevin\yii\changelog\interfaces\LoggableInterface;
use yii\helpers\Json;

/**
 * Class Changelog
 * @package resources
 *
 * @property integer $id
 * @property integer $action
 * @property string $entity_type
 * @property string $entity_id
 * @property string $entity
 * @property string $changes
 * @property string $env
 * @property integer $created_at
 * @property integer $updated_at
 */
class Changelog extends \yii\db\ActiveRecord implements ChangelogModelInterface
{

    /** @var string Request component name */
    public static $request = 'request';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::class,
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
            [['entity_type', 'entity_id', 'entity', 'changes', 'env'], 'string'],

            /** semantic validators */
            [['entity_type', 'entity_id', 'entity', 'changes', 'env'], 'required'],
            [['entity_type', 'entity_id'], 'filter', 'filter' => 'trim'],

            /** default values */
        ];
    }

    /**
     * @inheritdoc
     */
    public static function logInsert($Model, $ignoreAttributes = [])
    {
        $Changelog = new static;

        $attributes = $Model->attributes;

        if (!empty($ignoreAttributes)) {
            foreach ($ignoreAttributes as $field) {
                if (isset($attributes[$field])) {
                    unset($attributes[$field]);
                }
            }
        }

        $present = $Model instanceof LoggableInterface
            ? (string)$Model
            : null;

        $Changelog->setAttributes([
            'action' => static::ACTION_INSERT,
            'entity_type' => get_class($Model),
            'entity_id' => Json::encode($Model->primaryKey),
            'entity' => $present,
            'changes' => serialize(['insert' => $attributes]),
            'env' => serialize(static::getEnvData()),
        ]);

        $Changelog->validate() && $Changelog->save();

        return $Changelog;
    }

    /**
     * @inheritdoc
     */
    public static function logUpdate($Model, $ignoreAttributes = [], $changedAttributes = [])
    {
        $attributes = $Model->attributes;

        if (!empty($ignoreAttributes)) {
            foreach ($ignoreAttributes as $field) {
                if (isset($attributes[$field])) {
                    unset($attributes[$field]);
                }
                if (isset($changedAttributes[$field])) {
                    unset($changedAttributes[$field]);
                }
            }
        }

        $newAttributes = [];
        foreach ($attributes as $k => $v) {
            if (isset($changedAttributes[$k])) {
                $newAttributes[$k] = $v;
            }
        }

        $present = $Model instanceof LoggableInterface
            ? (string)$Model
            : null;

        if (!empty($changedAttributes)) {
            $Changelog = new static;

            $Changelog->setAttributes([
                'action' => static::ACTION_UPDATE,
                'entity_type' => get_class($Model),
                'entity_id' => Json::encode($Model->primaryKey),
                'entity' => $present,
                'changes' => serialize([
                    'update' => [
                        'from' => $changedAttributes,
                        'to' => $newAttributes,
                    ],
                ]),
                'env' => serialize(static::getEnvData()),
            ]);

            $Changelog->validate() && $Changelog->save();

            return $Changelog;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public static function logDelete($Model, $ignoreAttributes = [])
    {
        $Changelog = new static;

        $attributes = $Model->attributes;

        if (!empty($ignoreAttributes)) {
            foreach ($ignoreAttributes as $field) {
                if (isset($attributes[$field])) {
                    unset($attributes[$field]);
                }
            }
        }

        $present = $Model instanceof LoggableInterface
            ? (string)$Model
            : null;

        $Changelog->setAttributes([
            'action' => static::ACTION_DELETE,
            'entity_type' => get_class($Model),
            'entity_id' => Json::encode($Model->primaryKey),
            'entity' => $present,
            'changes' => serialize(['delete' => $attributes]),
            'env' => serialize(static::getEnvData()),
        ]);

        $Changelog->validate() && $Changelog->save();

        return $Changelog;
    }

    /**
     * @return array
     */
    public static function getEnvData()
    {
        // @todo add gzip compress
        $result = [
            'ENV' => $_ENV,
            'SERVER' => $_SERVER,
        ];

        $Request = \Yii::$app->get(static::$request);

        if ($Request instanceof \yii\web\Request) {
            $result['COOKIE'] = $_COOKIE;
            $result['GET'] = $Request->get();
            $result['POST'] = $Request->post();
            $result['REQUEST_BODY'] = $Request->getRawBody();
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function getActions()
    {
        return [
            static::ACTION_INSERT => \Yii::t('app', 'Insert'),
            static::ACTION_UPDATE => \Yii::t('app', 'Update'),
            static::ACTION_DELETE => \Yii::t('app', 'Delete'),
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
     * @return \rmrevin\yii\changelog\resources\queries\ChangelogQuery
     */
    public static function find()
    {
        return new \rmrevin\yii\changelog\resources\queries\ChangelogQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%changelog}}';
    }

    const ACTION_INSERT = 1;
    const ACTION_UPDATE = 2;
    const ACTION_DELETE = 3;
}