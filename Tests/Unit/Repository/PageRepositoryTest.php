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

use Doctrine\DBAL\Driver\Statement;
use Prophecy\Argument;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionContainerInterface;
use TYPO3\CMS\Linkvalidator\Repository\PagesRepository;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class PageRepositoryTest extends UnitTestCase
{

    /**
     * Test to check if number or results returned is correct
     * (tests recursion depending on depth).
     *
     * @param int $depth
     * @param array $payload
     * @param array $expectedResult
     *
     * @dataProvider getPagesRecursiveDataProvider
     */
    public function testGetPagesRecursiveDepth(int $depth, array $payload, array $expectedResult)
    {
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryRestrictionContainerProphecy = $this->prophesize(QueryRestrictionContainerInterface::class);
        $expressionBuilderProphecy = $this->prophesize(ExpressionBuilder::class);
        $queryRestrictionContainerProphecy->removeAll()->willReturn($queryRestrictionContainerProphecy);
        $queryRestrictionContainerProphecy->add(Argument::cetera())->willReturn($queryRestrictionContainerProphecy);
        $queryBuilderProphecy->createNamedParameter(Argument::cetera(), \PDO::PARAM_INT)->willReturn(Argument::cetera());
        $queryBuilderProphecy->getRestrictions()->willReturn($queryRestrictionContainerProphecy->reveal());
        $queryBuilderProphecy->select(Argument::cetera())->willReturn($queryBuilderProphecy->reveal());
        $queryBuilderProphecy->expr()->willReturn($expressionBuilderProphecy->reveal());
        $queryBuilderProphecy->from('pages')->willReturn($queryBuilderProphecy->reveal());
        $queryBuilderProphecy->where(Argument::cetera())->willReturn($queryBuilderProphecy->reveal());
        $statementProphecy = $this->prophesize(Statement::class);
        $statementProphecy->fetchAll()->willReturn($payload);
        $queryBuilderProphecy->execute()->willReturn($statementProphecy->reveal());
        $expressionBuilderProphecy->eq(Argument::cetera())->willReturn('');
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        $pageRepository->setQueryBuilder($queryBuilderProphecy->reveal());
        $result = $pageRepository->getPagesRecursive(0, $depth, false, 1);
        $this->assertSame($result, $expectedResult);
    }


    public function getPagesRecursiveDataProvider(): array
    {
        return [
            'depth=0' => [
                0,
                [0 => ['uid' => 1]],
                [0 => ['uid' => 1]]
            ],
            'depth=1' => [
                1,
                [0 => ['uid' => 1]],
                [
                    0 => ['uid' => 1],
                    1 => ['uid' => 1]
                ]
            ],
            'depth=3' => [
                3,
                [0 => ['uid' => 1]],
                [
                    0 => ['uid' => 1],
                    1 => ['uid' => 1],
                    2 => ['uid' => 1],
                    3 => ['uid' => 1],
                ]
            ],
            'depth=1, 2 results' => [
                1,
                [
                    0 => ['uid' => 1],
                    1 => ['uid' => 2],
                ],
                [
                    0 => ['uid' => 1],
                    1 => ['uid' => 2],
                    2 => ['uid' => 1],
                    3 => ['uid' => 2],
                    4 => ['uid' => 1],
                    5 => ['uid' => 2],
                ]
            ],
        ];
    }

}
