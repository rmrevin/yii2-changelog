<?php
/**
 * LogsStorage.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace rmrevin\yii\changelog\components;

use rmrevin\yii\changelog\interfaces\LogsStorageInterface;
use rmrevin\yii\changelog\resources\Changelog;
use rmrevin\yii\changelog\resources\Log;
use yii\base\Component;
use yii\di\Instance;

/**
 * Class LogsStorage
 * @package rmrevin\yii\changelog\components
 */
class LogsStorage extends Component implements LogsStorageInterface
{

    /** @var Log[] */
    public static $storage = [];

    /**
     * @inheritdoc
     */
    public static function store(Log $log)
    {
        static::$storage[] = $log;
    }

    /**
     * @inheritdoc
     */
    public static function save()
    {
        /** @var Changelog $Model */
        $Model = Instance::ensure([
            'class' => Changelog::className(),
        ]);

        /** @var Log $Log */
        $Log = Instance::ensure([
            'class' => Log::className(),
        ]);

        $columns = $Log::schema();

        $rows = [];

        $Logs = static::$storage;
        if (!empty($Logs)) {
            foreach ($Logs as $Log) {
                $rows[] = $Log->export();
            }
        }

        if (!empty($rows)) {
            /** @var \yii\db\Connection $DB */
            $DB = \Yii::$app->get('db');
            $DB->createCommand()
                ->batchInsert($Model::tableName(), $columns, $rows)
                ->execute();
        }
    }
}
