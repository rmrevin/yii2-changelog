<?php
/**
 * Log.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace rmrevin\yii\changelog\resources;

use rmrevin\yii\changelog\interfaces\LoggableInterface;
use yii\base\Model;
use yii\base\Request;
use yii\db\BaseActiveRecord;
use yii\di\Instance;
use yii\helpers\Json;

/**
 * Class Log
 * @package rmrevin\yii\changelog\resources
 */
class Log extends Model
{

    public $action;
    public $entity_type;
    public $entity_id;
    public $present;
    public $changes;
    public $env;

    /**
     * @return array
     */
    public function export()
    {
        return [
            $this->action,
            $this->entity_type,
            $this->entity_id,
            $this->present,
            $this->changes,
            $this->getEnvData(),
        ];
    }

    /**
     * @return array
     */
    public static function schema()
    {
        return [
            'action',
            'entity_type',
            'entity_id',
            'present',
            'changes',
            'env',
        ];
    }

    /**
     * @param integer $action
     * @return static
     */
    public function action($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param BaseActiveRecord $Model
     * @return static
     */
    public function entity($Model)
    {
        $this->entity_type = get_class($Model);
        $this->entity_id = Json::encode($Model->primaryKey);

        $this->present = $Model instanceof LoggableInterface
            ? (string)$Model
            : null;

        return $this;
    }

    /**
     * @param array $changes
     * @return static
     */
    public function changes(array $changes)
    {
        $this->changes = serialize($changes);

        return $this;
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
        return serialize($result);
    }
}
