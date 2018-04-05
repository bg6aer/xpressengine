<?php
/**
 * UriValidator
 *
 * PHP version 7
 *
 * @category  Routing
 * @package   Xpressengine\Routing
 * @author    XE Developers <developers@xpressengine.com>
 * @copyright 2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license   http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link      https://xpressengine.io
 */

namespace Xpressengine\Routing;

use Illuminate\Routing\Matching\ValidatorInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

/**
 * Class UriValidator
 *
 * @category    Routing
 * @package     Xpressengine\Routing
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link        https://xpressengine.io
 */
class UriValidator implements ValidatorInterface
{

    /**
     * Validate a given rule against a route and request.
     *
     * @param  Route   $route   illuminate route
     * @param  Request $request illuminate request
     *
     * @return bool
     */
    public function matches(Route $route, Request $request)
    {
        $path = $request->path() == '/' ? '/' : '/' . $request->path();
        $firstSegment = $request->segment(1);
        $uri = $route->uri();
        if (strpos($uri, '{instanceGroup') === 0 && $firstSegment === null) {
            return true;
        } else {
            return preg_match($route->getCompiled()->getRegex(), rawurldecode($path));
        }
    }
}
