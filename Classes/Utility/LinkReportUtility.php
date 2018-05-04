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

class LinkReportUtility
{

    /**
     * Shorten element header in link report, if necessary
     *
     * @param string $header
     * @return string
     */
    public static function processElementHeader(string $header) : string
    {
        if (mb_strlen($header) > 35) {
            return substr($header, 0, 32) . '...';
        }
        return $header;
    }

    /**
     * Shorten length of path in link report, if necessary
     *
     * @param string $path
     * @return string
     */
    public static function processPath(string $path) : string
    {
        $segments = explode('/', trim($path, '/'));
        if (!$segments || (count($segments) < 4 && strlen($path) < 40)) {
            return $path;
        }
        return '/' . $segments[0] . '/.../' . end($segments) . '/';

    }

}