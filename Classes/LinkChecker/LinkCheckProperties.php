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
     * Permissions of records to process.
     *
     * 1 - read permission
     * 3 - read + write permission
     *
     * @var int
     */
    protected $perms = 3;

    /**
     * LinkCheckProperties constructor.
     *
     * @param array $props
     * @param array $tsConfig
     *
     * @todo handle TSconfig
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
        if (isset($props['perms'])) {
            $this->perms = $props['perms'];
        }
    }

    /**
     * @return array
     */
    public function getLinkTypes(): array
    {
        return $this->linkTypes;
    }

    /**
     * @param array $linkTypes
     */
    public function setLinkTypes(array $linkTypes)
    {
        $this->linkTypes = $linkTypes;
    }

    /**
     * @return int
     */
    public function getSearchLevels(): int
    {
        return $this->searchLevels;
    }

    /**
     * @param int $searchLevels
     */
    public function setSearchLevels(int $searchLevels)
    {
        $this->searchLevels = $searchLevels;
    }

    /**
     * @return int
     */
    public function getStartPage(): int
    {
        return $this->startPage;
    }

    /**
     * @param int $startPage
     */
    public function setStartPage(int $startPage)
    {
        $this->startPage = $startPage;
    }

    /**
     * @return bool
     */
    public function isInHiddenPages(): bool
    {
        return $this->inHiddenPages;
    }

    /**
     * @param bool $inHiddenPages
     */
    public function setInHiddenPages(bool $inHiddenPages)
    {
        $this->inHiddenPages = $inHiddenPages;
    }

    /**
     * @return array
     */
    public function getSearchFields(): array
    {
        return $this->searchFields;
    }

    /**
     * @param array $searchFields
     */
    public function setSearchFields(array $searchFields)
    {
        $this->searchFields = $searchFields;
    }

    /**
     * @return int
     */
    public function getPerms(): int
    {
        return $this->perms;
    }

    /**
     * @param int $perms
     */
    public function setPerms(int $perms): void
    {
        $this->perms = $perms;
    }



}