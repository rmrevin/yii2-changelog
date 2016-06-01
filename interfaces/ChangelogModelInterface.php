<?php
/**
 * ChangelogModelInterface.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace rmrevin\yii\changelog\interfaces;

/**
 * Interface ChangelogModelInterface
 * @package rmrevin\yii\changelog\interfaces
 */
interface ChangelogModelInterface
{

    /**
     * @param \yii\db\BaseActiveRecord $Model
     * @param array $ignoreAttributes
     * @return static
     */
    public static function logInsert($Model, $ignoreAttributes = []);

    /**
     * @param \yii\db\BaseActiveRecord $Model
     * @param array $ignoreAttributes
     * @param array $changedAttributes
     * @return static
     */
    public static function logUpdate($Model, $ignoreAttributes = [], $changedAttributes = []);

    /**
     * @param \yii\db\BaseActiveRecord $Model
     * @param array $ignoreAttributes
     * @return static
     */
    public static function logDelete($Model, $ignoreAttributes = []);
}