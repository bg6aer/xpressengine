<?php
/**
 * ArrayPluginCache class. This file is part of the Xpressengine package.
 *
 * PHP version 5
 *
 * @category    Plugin
 * @package     Xpressengine\Plugin
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
namespace Xpressengine\Plugin\Cache;

use Xpressengine\Plugin\PluginCollection;

/**
 * 이 클래스는 XE에 존재하는 플러그인의 목록을 array(in memory)에 캐싱하는 클래스이다.
 *
 * @category    Plugin
 * @package     Xpressengine\Plugin
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
class ArrayPluginCache implements PluginCache
{

    /**
     * plugin info 목록을 저장할 array
     *
     * @var array plugin info list
     */
    public $plugins = null;

    /**
     * 캐시에 저장된 PluginEntity 목록을 반환한다.
     *
     * @return array
     */
    public function getPluginsFromCache()
    {
        return $this->plugins;
    }

    /**
     * XE3에 존재하는 플러그인의 PluginEntity 목록을 캐시에 저장한다.
     *
     * @param array $plugins 캐시에 저장할 PluginEntity 목록
     *
     * @return void
     */
    public function setPluginsToCache(array $plugins)
    {
        $this->plugins = $plugins;
    }

    /**
     * 캐싱된 플러그인 정보가 있는지 조사한다.
     *
     * @return bool
     */
    public function hasCachedPlugins()
    {
        if ($this->plugins === null) {
            return false;
        } else {
            return true;
        }
    }
}
