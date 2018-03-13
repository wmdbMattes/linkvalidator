<?php
declare(strict_types = 1);
namespace TYPO3\CMS\Linkvalidator\Repository;

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

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Repository for link check runs.
 */
class LinkCheckRepository
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder = false;



    /**
     * @param QueryBuilder $queryBuilder
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getLastResult(): array
    {
        $this->queryBuilder
            ->select('*')
            ->from('tx_linkvalidator_check')
            ->setMaxResults(1)
            ->orderBy('uid', 'DESC');

        return $queryBuilder->execute()->fetchColumn(0);
    }

    public function addResult(int $startTime, int $endTime, int $numberOfLinks, int $numberOfBrokenLinks)
    {
        $this->queryBuilder
            ->insert('tx_linkvalidator_check')
            ->values([
                'starttime' => $startTime,
                'endtime'   => $endTime,
                'number_of_links' => $numberOfLinks,
                'number_of_broken_links' => $numberOfBrokenLinks
            ])
            ->execute();

    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder() : QueryBuilder
    {
        if ($this->queryBuilder === false) {
            $this->initializeQueryBuilder();
        }
        return $this->queryBuilder;
    }


    /**
     * Initialize QueryBuider to a default
     */
    protected function initializeQueryBuilder()
    {
        $this->queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');
    }

}