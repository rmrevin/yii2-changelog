<?php
/**
 * ChangelogBehavior.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace rmrevin\yii\changelog\behaviors;

use rmrevin\yii\changelog\interfaces\ChangelogModelInterface;
use yii\db\BaseActiveRecord;

/**
 * Class ChangelogBehavior
 * @package rmrevin\yii\changelog\behaviors
 */
class ChangelogBehavior extends \yii\base\Behavior
{

    /**
     * @var array exclude attributes
     */
    public $ignoreAttributes = [];

    /**
     * @var string
     */
    public $modelClass = 'rmrevin\yii\changelog\resources\Changelog';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'insert',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'update',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'delete',
        ];
    }

    /**
     * @param \yii\db\AfterSaveEvent $Event
     */
    public function insert($Event)
    {
        /** @var \yii\db\ActiveRecord $Model */
        $Model = $Event->sender;

        /** @var ChangelogModelInterface $ChangelogModel */
        $ChangelogModel = $this->modelClass;

        $ChangelogModel::logInsert($Model, $this->ignoreAttributes);
    }

    /**
     * @param \yii\db\AfterSaveEvent $Event
     */
    public function update($Event)
    {
        /** @var \yii\db\ActiveRecord $Model */
        $Model = $Event->sender;

        /** @var ChangelogModelInterface $ChangelogModel */
        $ChangelogModel = $this->modelClass;

        $ChangelogModel::logUpdate($Model, $this->ignoreAttributes, $Event->changedAttributes);
    }

    /**
     * @param \yii\base\Event $Event
     */
    public function delete($Event)
    {
        /** @var \yii\db\ActiveRecord $Model */
        $Model = $Event->sender;

        /** @var ChangelogModelInterface $ChangelogModel */
        $ChangelogModel = $this->modelClass;

        $ChangelogModel::logDelete($Model, $this->ignoreAttributes);
    }
}