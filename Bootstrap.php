<?php
/**
 * Bootstrap.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace rmrevin\yii\changelog;

use rmrevin\yii\changelog\components\LogsStorage;
use yii\base\Application;
use yii\di\Instance;

/**
 * Class Bootstrap
 * @package rmrevin\yii\changelog
 */
class Bootstrap implements \yii\base\BootstrapInterface
{

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->on(Application::EVENT_AFTER_REQUEST, function () {
            /** @var LogsStorage $Storage */
            $Storage = Instance::ensure([
                'class' => LogsStorage::className(),
            ]);

            $Storage::save();
        });
    }
}
