<?php
declare(strict_types = 1);
namespace TYPO3\CMS\Linkvalidator\Tests\Unit\Utility;

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

use TYPO3\CMS\Linkvalidator\Utility\PaginationUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class PaginationUtilityTest extends UnitTestCase
{

    /**
     * @dataProvider paginationDataProvider
     * @param $totalHits
     * @param $currentPage
     * @param $perPage
     * @param $expectedResult
     */
    public function testGetPages($totalHits, $currentPage, $perPage, $expectedResult): void
    {
        $actualResult = PaginationUtility::getPages($totalHits, $currentPage, $perPage);
        $this->assertSame($expectedResult, $actualResult);
    }

    public function paginationDataProvider(): array
    {
        return [
            'standard Pagination' => [
                100,
                0,
                10,
                [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10
                ]
            ],
            'upper Bound' => [
                1000,
                0,
                10,
                [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15
                ]
            ],
            'rolling count Bound' => [
                1000,
                13,
                10,
                [
                    7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19
                ]
            ],
        ];
    }
}
