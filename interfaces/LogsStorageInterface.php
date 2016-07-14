<?php
/**
 * LogsStorageInterface.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace rmrevin\yii\changelog\interfaces;

use rmrevin\yii\changelog\resources\Log;

/**
 * Interface LogsStorageInterface
 * @package rmrevin\yii\changelog\interfaces
 */
interface LogsStorageInterface
{

    const ACTION_INSERT = 1;
    const ACTION_UPDATE = 2;
    const ACTION_DELETE = 3;

    /**
     * @param Log $log
     */
    public static function store(Log $log);

    /**
     *
     */
    public static function save();
}
