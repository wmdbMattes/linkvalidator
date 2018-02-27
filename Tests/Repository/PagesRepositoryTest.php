<?php

namespace TYPO3\CMS\Linkvalidator\Tests\Unit\Repository;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Linkvalidator\Repository\PagesRepository;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class PagesRepositoryTest extends UnitTestCase
{

    /**
     * @dataProvider getPagesRecursiveDataProvider
     * @param $startPage
     * @param $depth
     * @param $checkHidden
     * @param $perms
     * @param $expectedResult
     */
    public function testGetPagesRecursive($startPage, $depth, $checkHidden, $perms, $expectedResult)
    {
        $pagesRepository = GeneralUtility::makeInstance(PagesRepository::class);

        // todo
        //$mockQueryBuilder = ...;

        $pagesRepository->setQueryBuilder($mockQueryBuilder);

        $actualResult = $pagesRepository->getPagesRecursive($startPage, $depth, $checkHidden, $perms);
        $this->assertSame($expectedResult, $actualResult);

    }

    public function getPagesRecursiveDataProvider(): array
    {
        return [
            'depth=0' => [
                1,
                0,
                false,
                1,
                ['uid' => '1']
            ],
        ];
    }
}
