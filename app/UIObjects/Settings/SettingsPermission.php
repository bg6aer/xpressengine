<?php
/**
 * SettingsPermission.php
 *
 * PHP version 7
 *
 * @category    UIObjects
 * @package     App\UIObjects\Settings
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link        https://xpressengine.io
 */

namespace App\UIObjects\Settings;

use XeFrontend;
use Xpressengine\Permission\Grant;
use Xpressengine\Permission\Permission;
use Xpressengine\Permission\PermissionHandler;
use Xpressengine\UIObject\AbstractUIObject;

/**
 * Class SettingsPermission
 *
 * @category    UIObjects
 * @package     App\UIObjects\Settings
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link        https://xpressengine.io
 */
class SettingsPermission extends AbstractUIObject
{
    /**
     * The component id
     *
     * @var string
     */
    protected static $id = 'uiobject/xpressengine@registeredPermission';

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render()
    {
        $args = $this->arguments;

        $permissionInfo = $args['permission'];
        $title = $permissionInfo['title'];

        /** @var Permission $permission */
        $permission = $permissionInfo['permission'];

        $groups = app('xe.user.groups')->all();

        $settings = [];
        $content = uio('permission', [
            'mode' => '',
            'title' => 'access',
            'grant' => $this->getGrant($permission['access']),
            'groups' => $groups
        ]);
        $settings[] = $this->generateBox($title, $content);
        $this->template = implode(PHP_EOL, $settings);


        return parent::render();
    }

    /**
     * Get the grant
     *
     * @param array $grant grant
     * @return array
     */
    protected function getGrant($grant)
    {
        $defaultGrant = [
            Grant::RATING_TYPE => '',
            Grant::GROUP_TYPE => [],
            Grant::USER_TYPE => [],
            Grant::EXCEPT_TYPE => []
        ];

        if ($grant !== null) {
            return array_merge($defaultGrant, $grant);
        } else {
            return $defaultGrant;
        }
    }

    /**
     * Wrap the content.
     *
     * @param string                                  $title   title
     * @param \Xpressengine\UIObject\AbstractUIObject $content content
     * @return string
     */
    private function generateBox($title, $content)
    {
        return "<div class=\"form-group\">
        $content
</div>";
    }
}
