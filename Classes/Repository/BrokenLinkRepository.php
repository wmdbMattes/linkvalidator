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
use TYPO3\CMS\Linkvalidator\Utility\BackendUserUtility;

/**
 * Repository for broken links.
 */
class BrokenLinkRepository
{

    /**
     * @param int $startPage
     * @param int $resultsPerPage
     * @param bool $forCurrentBeUser : only results accessible for current BE-Usre
     * @return array
     */
    public function findResults(int $currentPage=0, int $resultsPerPage=10, bool $forCurrentBeUser=true) : array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_linkvalidator_link');
        $queryBuilder
            ->select('*')
            ->from('tx_linkvalidator_link')
            ->join(
                'tx_linkvalidator_link',
              'pages',
               'pages',
                    $queryBuilder->expr()->eq('pages.uid', $queryBuilder->quoteIdentifier('tx_linkvalidator_link.record_pid'))
            );

        if ($forCurrentBeUser) {
            $permsClause = BackendUserUtility::getPermsClause();
            $queryBuilder
                ->where($permsClause);
        }

        $queryBuilder
            ->orderBy('record_pid')
            ->addOrderBy('record_uid');
        if ($resultsPerPage) {
            $queryBuilder->setMaxResults($resultsPerPage);
        }
        if ($currentPage) {
            $startResult = $currentPage * $resultsPerPage;
            $queryBuilder->setFirstResult($startResult);
        }
        return $queryBuilder->execute()
            ->fetchAll();

    }

    /**
     * @param int $startPage
     * @param int $resultsPerPage
     * @return array
     */
    public function findResultsForCurrentBeUser(int $currentPage=0, int $resultsPerPage=10) : array
    {
       return $this->findResults($currentPage, $resultsPerPage, true);
    }

    /**
     * @param bool $forCurrentBeUser If true, only results for current BE-User, if false: all results
     * @return int
     */
    public function countResults(bool $forCurrentBeUser = true) : int
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_linkvalidator_link');

        $queryBuilder
            ->count('tx_linkvalidator_link.uid')
            ->from('tx_linkvalidator_link')
            ->join(
                'tx_linkvalidator_link',
                'pages',
                'pages',
                $queryBuilder->expr()->eq('pages.uid', $queryBuilder->quoteIdentifier('tx_linkvalidator_link.record_pid'))
            );
        if ($forCurrentBeUser) {
            $permsClause = BackendUserUtility::getPermsClause();
            $queryBuilder->
            where($permsClause);
        }
        return (int)
            $queryBuilder
            ->orderBy('record_pid')
            ->addOrderBy('record_uid')
            ->execute()
            ->fetchColumn(0);

    }

    public function countResultsForCurrentBeUser() : int
    {
        return $this->countResults(true);
    }

    public function removeAll()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_linkvalidator_link');
        $queryBuilder->delete('tx_linkvalidator_link')
            ->execute();
    }

    public function insertRecord(array $record)
    {
        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_linkvalidator_link')
            ->insert('tx_linkvalidator_link', $record);
    }

    /**
     * @param array $linkTypes
     * @param array $pageList
     * @param int $page
     * @return array
     *
     * @deprecated this function is currently not used, check if it can be removed!
     */
    public function getResults(array $linkTypes, array $pageList, int $page = 0): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_linkvalidator_link');
        $queryBuilder
            ->select('*')
            ->from('tx_linkvalidator_link')
            ->where(
                $queryBuilder->expr()->in(
                    'record_pid',
                    $queryBuilder->createNamedParameter($pageList, Connection::PARAM_INT_ARRAY)
                )
            )
            ->orderBy('record_pid')
            ->addOrderBy('record_uid');

        if (!empty($linkTypes)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(
                    'link_type',
                    $queryBuilder->createNamedParameter($linkTypes, Connection::PARAM_STR_ARRAY)
                )
            );
        }
        return $queryBuilder->execute();
    }
}