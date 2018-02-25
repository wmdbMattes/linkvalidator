<?php
declare(strict_types = 1);
namespace TYPO3\CMS\Linkvalidator\Properties;

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

/**
 * Class LinkCheckProperties
 *
 * Wrapper class for properties:
 * Stores information about how to check broken links
 *
 * @package TYPO3\CMS\Linkvalidator\Properties
 */
class LinkCheckProperties
{
    /**
     * Which type of broken links to check
     *
     * @var array
     */
    protected $linkTypes;

    /**
     * How many levels to crawl
     *
     * @var int
     */
    protected $searchLevels;

    /**
     * @var int
     */
    protected $startPage;

    /**
     * Check links in hidden pages too?
     *
     * @var bool
     */
    protected $inHiddenPages = false;

    /**
     * Array of tables => fields to search through
     *
     * @var array
     */
    protected $searchFields;

    /**
     * LinkCheckProperties constructor.
     *
     * @param array $props
     * @param array $tsConfig
     */
    public function __construct(array $props, $tsConfig = [])
    {
        if (isset($props['$linkTypes'])) {
            $this->linkTypes = $props['linkTypes'];
        }
        if (isset($props['searchLevels'])) {
            $this->searchLevels = $props['searchLevels'];
        }
        if (isset($props['startPage'])) {
            $this->startPage = $props['startPage'];
        }
        if (isset($props['inHiddenPages'])) {
            $this->inHiddenPages = $props['inHiddenPages'];
        }
    }

}