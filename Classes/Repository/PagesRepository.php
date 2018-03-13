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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Install\FolderStructure\Exception\InvalidArgumentException;

class PagesRepository
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

    /**
     * Returns array of page ids
     *
     * @param int $startPage
     * @param int $depth (0: current page only etc.)
     * @param bool $checkHidden
     * @param int $perms
     *   -1: do not check user access
     *   1: read access,
     *   3: read/write access.
     *
     * @return array
     *
     * @todo need to check getRootLineIsHidden?
     * @todo need to check extendToSubpages?
     *
     * (see extGetTreeList, checkPageLinks)
     *
     * @throws InvalidArgumentException
     */
    public function getPagesRecursive(int $startPage, int $depth, bool $checkHidden, int $perms): array
    {

        $results = [];
        $whereField = 'pid';

        // handle depth=0
        if ($depth === 0) {
            $whereField = 'uid';
        }

        $rows = $this->getPages($whereField,(string)$startPage,  $perms, $checkHidden);

        foreach ($rows as $row) {
            if ($depth > 0) {
                $results = array_merge($results,
                    $this->getPagesRecursive((int)$row['uid'], $depth - 1, $checkHidden, $perms));
            }
        }
        $results = array_merge($results, $rows);

        return $results;
    }



    /**
     * Constraints:
     * - uid= or pid=
     * - !hidden (if checkHidden is true)
     * - perms
     *
     * todo
     * - getRootlineIsHidden
     * - extendToSubpages
     *
     * @param string $whereValue
     * @param string $whereField
     * @param int $perms
     * @param bool $checkHidden
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function getPages(
        string $whereField, string $whereValue,
        int $perms, bool $checkHidden): array
    {
        $permsClause = '';
        if ($perms !== -1) {
            $permsClause = "$perms=$perms";
        }
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        if ($checkHidden === false) {
            $queryBuilder->getRestrictions()
                ->add(GeneralUtility::makeInstance(HiddenRestriction::class));
        }

        if ($permsClause) {
            $result = $queryBuilder
                ->select('uid', 'title', 'hidden', 'extendToSubpages')
                ->from('pages')
                ->where(
                    $queryBuilder->expr()->eq(
                        $whereField,
                        $queryBuilder->createNamedParameter($whereValue, \PDO::PARAM_INT)
                    ),
                    QueryHelper::stripLogicalOperatorPrefix($permsClause)
                )
                ->execute()->fetchAll();
        } else {
            $result = $queryBuilder
                ->select('uid', 'title', 'hidden', 'extendToSubpages')
                ->from('pages')
                ->where(
                    $queryBuilder->expr()->eq(
                        $whereField,
                        $queryBuilder->createNamedParameter($whereValue, \PDO::PARAM_INT)
                    )
                )
                ->execute()->fetchAll();
        }

        return $result;
    }

    /**
     * Check if rootline contains a hidden page
     *
     * @param array $pageInfo Array with uid, title, hidden, extendToSubpages from pages table
     * @return bool TRUE if rootline contains a hidden page, FALSE if not
     *
     * @todo Does this really make sense here? Instead of checking if page in
     * rootline is hidden, we should not traverse into hidden page subtrees
     * in the first place if checkHidden is false!
     */
    public function getRootLineIsHidden(array $pageInfo)
    {
        $hidden = false;
        if ($pageInfo['extendToSubpages'] == 1 && $pageInfo['hidden'] == 1) {
            $hidden = true;
        } else {
            if ($pageInfo['pid'] > 0) {
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
                $queryBuilder->getRestrictions()->removeAll();

                $row = $queryBuilder
                    ->select('uid', 'title', 'hidden', 'extendToSubpages')
                    ->from('pages')
                    ->where(
                        $queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter($pageInfo['pid'], \PDO::PARAM_INT)
                        )
                    )
                    ->execute()
                    ->fetch();

                if ($row !== false) {
                    $hidden = $this->getRootLineIsHidden($row);
                }
            }
        }

        return $hidden;
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