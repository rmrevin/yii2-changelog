<?php
/**
 * ChangelogBehavior.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace rmrevin\yii\changelog\behaviors;

use rmrevin\yii\changelog\components\LogsStorage;
use rmrevin\yii\changelog\resources\Log;
use yii\base\Request;
use yii\db\BaseActiveRecord;
use yii\di\Instance;

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
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'logInsert',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'logUpdate',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'logDelete',
        ];
    }

    /**
     * @param \yii\db\AfterSaveEvent $Event
     */
    public function logInsert($Event)
    {
        $Storage = $this->getStorage();

        /** @var BaseActiveRecord $Model */
        $Model = $Event->sender;

        $attributes = $Model->attributes;

        $this->unsetIgnoreAttributes($attributes);

        /** @var Log $Log */
        $Log = Instance::ensure(['class' => Log::className()]);
        $Log->action($Storage::ACTION_INSERT)
            ->entity($Model)
            ->changes(['insert' => $attributes]);

        $Storage::store($Log);
    }

    /**
     * @param \yii\db\AfterSaveEvent $Event
     */
    public function logUpdate($Event)
    {
        $Storage = $this->getStorage();

        /** @var BaseActiveRecord $Model */
        $Model = $Event->sender;

        $attributes = $Model->attributes;

        $changedAttributes = $Event->changedAttributes;

        $this->unsetIgnoreAttributes($attributes, $changedAttributes);

        $newAttributes = [];
        foreach ($attributes as $k => $v) {
            if (isset($changedAttributes[$k])) {
                $newAttributes[$k] = $v;
            }
        }

        if (!empty($changedAttributes)) {
            /** @var Log $Log */
            $Log = Instance::ensure(['class' => Log::className()]);
            $Log->action($Storage::ACTION_UPDATE)
                ->entity($Model)
                ->changes([
                    'update' => [
                        'from' => $changedAttributes,
                        'to' => $newAttributes,
                    ],
                ]);

            $Storage::store($Log);
        }
    }

    /**
     * @param \yii\base\Event $Event
     */
    public function logDelete($Event)
    {
        $Storage = $this->getStorage();

        /** @var BaseActiveRecord $Model */
        $Model = $Event->sender;

        $attributes = $Model->attributes;

        $this->unsetIgnoreAttributes($attributes);

        /** @var Log $Log */
        $Log = Instance::ensure(['class' => Log::className()]);
        $Log->action($Storage::ACTION_DELETE)
            ->entity($Model)
            ->changes(['delete' => $attributes]);

        $Storage::store($Log);
    }

    /**
     * @return LogsStorage
     */
    protected function getStorage()
    {
        /** @var LogsStorage $Storage */
        $Storage = Instance::ensure([
            'class' => LogsStorage::className(),
        ]);

        return $Storage;
    }

    /**
     * @param array $attributes
     * @param array $changedAttributes
     */
    protected function unsetIgnoreAttributes(array &$attributes, array &$changedAttributes = [])
    {
        $ignoreAttributes = $this->ignoreAttributes;

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
    }

    /**
     * @return array
     */
    protected function getEnvData()
    {
        $result = [
            'ENV' => $_ENV,
            'SERVER' => $_SERVER,
        ];

        $Request = Instance::ensure('request', Request::className());

        if ($Request instanceof \yii\web\Request) {
            $result['COOKIE'] = $_COOKIE;
            $result['GET'] = $Request->get();
            $result['POST'] = $Request->post();
            $result['REQUEST_BODY'] = $Request->getRawBody();
        }

        // @todo add gzip compress
        return $result;
    }
}
