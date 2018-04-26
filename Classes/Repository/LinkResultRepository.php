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
    /**
     * @param int $limit
     * @todo  returns \Doctrine\DBAL\Driver\Statement|int or array?
     */
    public function findAllResults(int $limit = 0) : array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_linkvalidator_link');
        $queryBuilder
            ->select('*')
            ->from('tx_linkvalidator_link')
            ->orderBy('record_pid')
            ->addOrderBy('record_uid');

        if ($limit) {
            $queryBuilder->setMaxResult($limit);
        }

        return $queryBuilder->execute()
            ->fetchAll();
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