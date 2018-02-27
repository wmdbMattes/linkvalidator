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

    public function __construct()
    {
        $this->getLanguageService()->includeLLFile('EXT:linkvalidator/Resources/Private/Language/locallang_module_linkvalidator.xlf');
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

    }

    /**
     * Recheck for broken links using current $properties.
     * Stores in LinkResultRepository.
     */
    // was: LinkAnalyzer->getLinkStatistics()
    public function checkBrokenLinks()
    {
        // iterate through tables-> fields
        // use list of pids
        // consider hooks
    }

    /**
     * Get list of pids to check.
     */
    protected function getPidList()
    {

    }



}