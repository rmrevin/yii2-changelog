<?php
/**
 * ChangelogQuery.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace rmrevin\yii\changelog\resources\queries;

use yii\db\ActiveQuery;

/**
 * Class ChangelogQuery
 * @package rmrevin\yii\changelog\resources\queries
 */
class ChangelogQuery extends ActiveQuery
{

    /**
     * @param integer|array $id
     * @return static
     */
    public function byId($id)
    {
        $this->andWhere(['id' => $id]);

        return $this;
    }

    /**
     * @param integer|array $action
     * @return static
     */
    public function byAction($action)
    {
        $this->andWhere(['action' => $action]);

        return $this;
    }

    /**
     * @param string|array $entity_type
     * @param string|array $entity_id
     * @return static
     */
    public function byEntity($entity_type, $entity_id)
    {
        $this->andWhere([
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
        ]);

        return $this;
    }

    /**
     * @param string|array $entity_type
     * @return static
     */
    public function byEntityType($entity_type)
    {
        $this->andWhere(['entity_type' => $entity_type]);

        return $this;
    }

    /**
     * @param string|array $entity_id
     * @return static
     */
    public function byEntityId($entity_id)
    {
        $this->andWhere(['entity_id' => $entity_id]);

        return $this;
    }
}
