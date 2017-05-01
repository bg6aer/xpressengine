<?php
/**
 * EloquentRepositoryTrait.php
 *
 * PHP version 5
 *
 * @category    Support
 * @package     Xpressengine\Support
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link        https://xpressengine.io
 */

namespace Xpressengine\Support;

use Xpressengine\Database\Eloquent\DynamicModel as Model;

/**
 * @category    Support
 * @package     Xpressengine\Support
 */
trait EloquentRepositoryTrait
{
    /**
     * Xpressengine\Database\Eloquent\DynamicModel를 상속받은 class의 이름이어야 한다
     *
     * @var string model name.
     */
    protected static $model;

    /**
     * update
     *
     * @param Model $item item
     * @param array $data data
     *
     * @return Model
     */
    public function update(Model $item, array $data = [])
    {
        $item->update($data);
        return $item;
    }

    /**
     * delete
     *
     * @param Model $item item
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Model $item)
    {
        return $item->delete();
    }

    /**
     * Increment a column's value by a given amount.
     *
     * @param Model  $item   item
     * @param string $column column
     * @param int    $amount amount
     * @return int
     */
    public function increment(Model $item, $column, $amount = 1)
    {
        return $item->increment($column, $amount);
    }

    /**
     * Decrement a column's value by a given amount.
     *
     * @param Model  $item   item
     * @param string $column column
     * @param int    $amount amount
     * @return int
     */
    public function decrement(Model $item, $column, $amount = 1)
    {
        return $item->decrement($column, $amount);
    }

    /**
     * The name of Category model class
     *
     * @return string
     */
    public static function getModel()
    {
        return static::$model;
    }

    /**
     * Set the name of Category model
     *
     * @param string $model model class
     * @return void
     */
    public static function setModel($model)
    {
        static::$model = '\\' . ltrim($model, '\\');
    }

    /**
     * Create model instance
     *
     * @return Model
     */
    public function createModel()
    {
        $class = $this->getModel();

        return new $class;
    }

    /**
     * query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return $this->createModel()->newQuery();
    }

    /**
     * __call
     *
     * @param string $name      method name
     * @param array  $arguments arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $model = $this->createModel();

        return call_user_func_array([$model, $name], $arguments);
    }
}