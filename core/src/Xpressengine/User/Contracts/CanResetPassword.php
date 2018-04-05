<?php

/**
 * CanResetPassword.php
 *
 * PHP version 7
 *
 * @category    User
 * @package     Xpressengine\User
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link        https://xpressengine.io
 */

namespace Xpressengine\User\Contracts;

use Illuminate\Contracts\Auth\CanResetPassword as BaseCanResetPassword;

/**
 * Interface CanResetPassword
 *
 * @category    User
 * @package     Xpressengine\User
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link        https://xpressengine.io
 */
interface CanResetPassword extends BaseCanResetPassword
{
    /**
     * setEmailForPasswordReset() 메소드에서 반환할 email 정보를 지정한다.
     *
     * @param string $email 지정할 email주소
     *
     * @return void
     */
    public function setEmailForPasswordReset($email);
}
