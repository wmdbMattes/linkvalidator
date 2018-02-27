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

class PagesRepository
{

    /**
     * @var QueryBuilder
     */
    $protected $queryBuilder = false;


    /**
     * Initialize QueryBuider to a default
     */
    protected function initializeQueryBuilder()
    {
        $this->queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder() : QueryBuilder
    {
        if ($queryBuilder === false) {
            $this->initializeQueryBuilder();
        }
        return $this->getQueryBuilder();
    }


    /**
     * Returns array of page ids
     *
     * @param int $startPage
     * @param int $depth (0: current page only etc.)
     * @param bool $checkHidden
     * @param int $perms
     *
     * @return array
     *
     * @todo need to check getRootLineIsHidden?
     * @todo need to check extendToSubpages?
     *
     * (see extGetTreeList, checkPageLinks)
     */
    public function getPagesRecursive(int $startPage, int $depth, bool $checkHidden, int $perms = 1): array
    {

        $results = [];
        $whereField = 'pid';

        // handle depth=0
        if ($depth === 0) {
            $whereField = 'uid';
        }

        $rows = $this->getPages($fields = ['uid'], $whereField,(string)$startPage,  $perms, $checkHidden);

        while ($row = $rows->fetch()) {
            if ($depth > 0 ) {
                $results = array_merge($results,
                    $this->getPagesRecursive($row['uid'], depth -1, $checkHidden, $perms));
            }
            $results[] = $row;
        }
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
        string $fields = ['uid' , 'title', 'hidden', 'extendToSubpages'],
        string $whereField, string $whereValue,
        int $perms, bool $checkHidden)
    {
        $results = [];
        $permsClause = "$perms=$perms";
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        if ($checkHidden === false) {
            $queryBuilder->getRestrictions()
                ->add(GeneralUtility::makeInstance(HiddenRestriction::class));
        }

        $result = $queryBuilder
            //->select('uid', 'title', 'hidden', 'extendToSubpages')
            ->select('uid')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq(
                    $whereField,
                    $queryBuilder->createNamedParameter($whereValue, \PDO::PARAM_INT)
                ),
                QueryHelper::stripLogicalOperatorPrefix($permsClause)
            )
            ->execute();

        while ($row = $result->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

}