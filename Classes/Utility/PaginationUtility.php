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

class PaginationUtility
{

    /**
     * Get number of next page
     *
     * @param $totalCount
     * @param $currentPage
     * @param $numberOfLinksPerPage
     * @return int -1 if no nextPage
     */
    public static function getNextPage($totalCount, $currentPage, $numberOfLinksPerPage) : int
    {

        $nextPage = $currentPage + 1;
        if (($nextPage * $numberOfLinksPerPage) > $totalCount) {
            return -1;
        }
        return $nextPage;
    }

    /**
     * Get number of previous page
     *
     * @param $currentPage
     * @return int -1 if no previous page
     */
    public static function getPreviousPage($currentPage) : int
    {
        return $currentPage - 1;
    }


    /**
     * @param int $totalHits
     * @param int $currentPage
     * @param int $perPage
     * @return array
     *
     * @deprecated
     */
    public static function getPages(int $totalHits, int $currentPage = 0, int $perPage = 10) : array
    {
        $numPages = ceil($totalHits / $perPage);
        $i = 0;

        $maxPages = $numPages;
        if ($numPages > 15 && $currentPage <= 7) {
            $numPages = 15;
        }
        if ($currentPage > 7) {
            $i = $currentPage - 7;
            $numPages = $currentPage + 6;
        }
        if ($numPages > $maxPages) {
            $numPages = $maxPages;
            $i = $maxPages - 15;
        }

        if ($i < 0) {
            $i = 0;
        }

        $out = [];
        while ($i < $numPages) {
            $out[] = ($i + 1);
            ++$i;
        }

        return $out;
    }
}