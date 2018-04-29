<?php
declare(strict_types = 1);
namespace TYPO3\CMS\Linkvalidator\LinkChecker;

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

use TYPO3\CMS\Linkvalidator\LinkChecker\LinkAnalyzer;
use TYPO3\CMS\Linkvalidator\Repository\ContentRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LinkChecker
{
    /**
     * @var LinkCheckProperties
     */
    protected $properties;

    /**
     * @var LinkResultRepository
     */
    protected $linkResultRepository;

    /**
     * @var ContentRepository
     */
    protected $contentRepository;

    /**
     * @var LinkAnalyzer
     */
    protected $linkAnalyzer;

    public function __construct()
    {
        //$this->getLanguageService()->includeLLFile('EXT:linkvalidator/Resources/Private/Language/locallang_module_linkvalidator.xlf');
        $this->contentRepository = GeneralUtility::makeInstance(ContentRepository::class);
        $this->linkAnalyzer = GeneralUtility::makeInstance(LinkAnalyzer::class);
    }

    /**
     * @param LinkProperties $linkProperties
     */
    public function setProperties(LinkProperties $properties)
    {
        $this->properties = $properties;
    }

    public function getProperties(LinkProperties $properties)
    {
        return $this->properties;
    }

    /**
     * Remove broken links
     */
    public function flushBrokenLinks()
    {
        $this->linkResultRepository->removeAll();
    }

    /**
     * Recheck for broken links using current $properties.
     * Stores in LinkResultRepository.
     * * Iterate through tables-> fields
     * * use list of pids ?
     * * consider hooks
     *
     * @param int $startPage
     * @param int $
     *
     *
     * @todo delete / update existing links
     * @todo read configuration
     */
    public function findBrokenLinks()
    {
        //$pagesRepository = GeneralUtility::makeInstance(PagesRepository::class);
        //$pages = $pagesRepository->getPagesRecursive($startPage, $depth, $properties->isInHiddenPages(), $properties->getPerms());

        // todo: read config
        $this->searchFields = [
            'tt_content' => 'bodytext',
            //'pages'      => 'url'
        ];
        $analyzeResults = [];

        // Traverse all configured tables and fields
        foreach ($this->searchFields as $table => $field) {
            // If table is not configured, assume the extension is not installed
            // and therefore no need to check it
            if (!is_array($GLOBALS['TCA'][$table])) {
                continue;
            }

            $rows = $this->findContentForField($table, $field);

            $fields = [$field];

            foreach ($rows as $row) {
                $this->linkAnalyzer->analyzeRecord($analyzeResults, $table, $fields, $row);
            }
        }
    }

    protected function findContentForField($tablename, $field, $constraints = [])
    {

        $results = $this->contentRepository->findAllContent('tt_content', 'bodytext');
        return $results;
    }


    /**
     * Get list of pids to check.
     *
     * @deprecated
     */
    protected function getPidList()
    {

    }



}