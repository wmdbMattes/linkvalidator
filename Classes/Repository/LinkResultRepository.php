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
 * Repository for broken links.
 */
class LinkResultRepository
{
    /** @var TYPO3\CMS\Core\Database\Query\QueryBuilder */
    protected $queryBuilder;


    public function __construct()
    {
        $this->queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_linkvalidator_link');
    }

    public function findAllResults(int $startResult=0, int $maxResults = 0) : array
    {
        $this->queryBuilder
            ->select('*')
            ->from('tx_linkvalidator_link')
            ->orderBy('record_pid')
            ->addOrderBy('record_uid');
        if ($maxResults) {
            $this->queryBuilder->setMaxResult($maxResults);
        }
        if ($startResult) {
            $this->queryBuilder->setFirstResult($startResult);
        }
        return $this->queryBuilder->execute()
            ->fetchAll();
    }

    public function removeAll()
    {
        $this->queryBuilder->delete('tx_linkvalidator_link')
            ->execute();
    }

    /**
     * @param array $linkTypes
     * @param array $pageList
     * @param int $page
     * @return array
     * @deprecated this function is currently not used, check if it can be removed!
     */
    public function getResults(array $linkTypes, array $pageList, int $page = 0): array
    {
        $this->queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_linkvalidator_link');
        $this->queryBuilder
            ->select('*')
            ->from('tx_linkvalidator_link')
            ->where(
                $this->queryBuilder->expr()->in(
                    'record_pid',
                    $this->queryBuilder->createNamedParameter($pageList, Connection::PARAM_INT_ARRAY)
                )
            )
            ->orderBy('record_pid')
            ->addOrderBy('record_uid');

        if (!empty($linkTypes)) {
            $this->queryBuilder->andWhere(
                $this->queryBuilder->expr()->in(
                    'link_type',
                    $this->queryBuilder->createNamedParameter($linkTypes, Connection::PARAM_STR_ARRAY)
                )
            );
        }
        return $this->queryBuilder->execute();
    }
}