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

class ContentRepository
{

    /**
     * Returns array of content elements
     *
     * @throws InvalidArgumentException
     *
     * @todo handle pages table differently
     */
    public function findAllContent(string $table, string $field): array
    {
        // Always add some fields to returned fields: uid, pid, ...
        $selectFields = array_merge(
            [
                'uid',
                'pid',
                $GLOBALS['TCA'][$table]['ctrl']['label'],
                'sys_language_uid'
            ],
            [$field]);

        // prepend 'table.' because of join with pages
        array_walk($selectFields, function (&$value, $key) use(&$table) {
            $value = $table . '.' . $value;
        });





        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryBuilder
            ->select(... $selectFields)
            ->from($table)
            ->join(
                $table,
                'pages',
                'pages',
                $queryBuilder->expr()->eq('pages.uid', $queryBuilder->quoteIdentifier($table . '.pid'))
            );

        $considerHidden = true;

        if ($considerHidden) {
            $queryBuilder->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        }

        $queryBuilder->expr()->isNotNull($table . '.' . $field);


        $results = $queryBuilder
            ->execute()
            ->fetchAll();

        return $results;
    }

}