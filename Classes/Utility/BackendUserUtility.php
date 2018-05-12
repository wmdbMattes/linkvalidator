<?php

declare(strict_types = 1);
namespace TYPO3\CMS\Linkvalidator\Utility;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Type\Bitmask\Permission;

class BackendUserUtility
{

    /**
     * Returns the current BE user.
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    public static function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }

    public static function getPermsClause() : string
    {
        return self::getBackendUser()->getPagePermsClause(
            Permission::PAGE_SHOW|Permission::PAGE_EDIT | Permission::CONTENT_EDIT);
    }

}