<?php
declare(strict_types = 1);
namespace TYPO3\CMS\Linkvalidator\Controller;

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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Controller\Page\TreeController;
use TYPO3\CMS\Backend\Controller\UserSettingsController;
use TYPO3\CMS\Backend\Tree\Repository\PageTreeRepository;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Linkvalidator\Repository\LinkResultRepository;

class AjaxTreeController extends TreeController
{
    /**
     * @var array
     */
    protected $pagesForTree = [];

    /**
     * Returns page tree configuration in JSON
     *
     * @return ResponseInterface
     */
    public function fetchConfigurationAction(): ResponseInterface
    {
        $configuration = [
            'allowRecursiveDelete' => false,
            'doktypes' => [],
            'displayDeleteConfirmation' => true,
            'temporaryMountPoint' => '/',
        ];

        return new JsonResponse($configuration);
    }

    /**
     * Returns JSON representing page tree
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function fetchDataAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->hiddenRecords = GeneralUtility::intExplode(',', $this->getBackendUser()->getTSConfigVal('options.hideRecords.pages'), true);
        $this->backgroundColors = $this->getBackendUser()->getTSConfigProp('options.pageTree.backgroundColor');
        $this->addIdAsPrefix = (bool)$this->getBackendUser()->getTSConfigVal('options.pageTree.showPageIdWithTitle');
        $this->addDomainName = (bool)$this->getBackendUser()->getTSConfigVal('options.pageTree.showDomainNameWithTitle');
        $this->showMountPathAboveMounts = (bool)$this->getBackendUser()->getTSConfigVal('options.pageTree.showPathAboveMounts');
        $userSettingsController = GeneralUtility::makeInstance(UserSettingsController::class);
        $this->expandedState = $userSettingsController->process('get', 'BackendComponents.States.Pagetree');
        if (is_object($this->expandedState->stateHash)) {
            $this->expandedState = (array)$this->expandedState->stateHash;
        } else {
            $this->expandedState = $this->expandedState['stateHash'] ?: [];
        }

        // Fetching a part of a pagetree
        $page = $this->getPagesWithLinkErrors();
        $items = [];
        $finalItems = [];
        $items = array_merge($items, $this->pagesToFlatArray($page, (int)$page['uid']));

        foreach ($items as $key => $item) {
            if (isset($this->pagesForTree[$item['identifier']])) {
                $finalItems[] = $item;
            }
        }

        return new JsonResponse($finalItems);
    }

    /**
     * Converts nested tree structure produced by PageTreeRepository to a flat, one level array
     * and also adds visual representation information to the data.
     *
     * @param array $page
     * @param int $entryPoint
     * @param int $depth
     * @param array $inheritedData
     * @return array
     */
    protected function pagesToFlatArray(array $page, int $entryPoint, int $depth = 0, array $inheritedData = []): array
    {
        $pageId = (int)$page['uid'];
        $identifier = $entryPoint . '_' . $pageId;
        $expanded = $page['expanded'] || (isset($this->expandedState[$identifier]) && $this->expandedState[$identifier]);
        $backgroundColor = ($this->backgroundColors[$pageId] ?? '');

        $suffix = '';
        $prefix = '';
        $nameSourceField = 'title';
        $visibleText = $page['title'];
        $tooltip = BackendUtility::titleAttribForPages($page, '', false);
        if ($pageId !== 0) {
            $icon = $this->iconFactory->getIconForRecord('pages', $page, Icon::SIZE_SMALL);
        } else {
            $icon = $this->iconFactory->getIcon('apps-pagetree-root', Icon::SIZE_SMALL);
        }

        if ($this->useNavTitle && trim($page['nav_title'] ?? '') !== '') {
            $nameSourceField = 'nav_title';
            $visibleText = $page['nav_title'];
        }
        if (trim($visibleText) === '') {
            $visibleText = htmlspecialchars('[' . $GLOBALS['LANG']->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.no_title') . ']');
        }
        $visibleText = GeneralUtility::fixed_lgd_cs($visibleText, (int)$this->getBackendUser()->uc['titleLen'] ?: 40);

        $items = [];
        $items[] = [
            // Used to track if the tree item is collapsed or not
            'stateIdentifier' => $identifier,
            'identifier' => $pageId,
            'depth' => $depth,
            'tip' => htmlspecialchars($tooltip),
            'hasChildren' => !empty($page['_children']),
            'icon' => $icon->getIdentifier(),
            'name' => $visibleText,
            'nameSourceField' => $nameSourceField,
            'alias' => htmlspecialchars($page['alias'] ?: ''),
            'prefix' => htmlspecialchars($prefix),
            'suffix' => htmlspecialchars(($this->pagesForTree[$pageId] ? ' (' . $this->pagesForTree[$pageId] . ')' : '')),
            'overlayIcon' => $icon->getOverlayIcon() ? $icon->getOverlayIcon()->getIdentifier() : '',
            'selectable' => true,
            'expanded' => (bool)$expanded,
            'checked' => false,
            'backgroundColor' => htmlspecialchars($backgroundColor),
            'stopPageTree' => $stopPageTree,
            'class' => $this->resolvePageCssClassNames($page),
            'readableRootline' => ($depth === 0 && $this->showMountPathAboveMounts ? $this->getMountPointPath($pageId) : ''),
            'isMountPoint' => $depth === 0,
            'mountPoint' => $entryPoint,
            'workspaceId' => $page['t3ver_oid'] ?: $pageId,
        ];
        if (!$stopPageTree) {
            foreach ($page['_children'] as $child) {
                $items = array_merge($items, $this->pagesToFlatArray($child, $entryPoint, $depth + 1, ['backgroundColor' => $backgroundColor]));
            }
        }
        return $items;
    }

    protected function getPagesWithLinkErrors(): array
    {
        $this->pagesForTree = [];
        $pageTreeRepository = new PageTreeRepository();
        $linkResultRepository = new LinkResultRepository();
        $pagesWithErrors = $linkResultRepository->getPageIdsWithLinkErrors();
        foreach ($pagesWithErrors as $pageWithError) {
            $rootLine = BackendUtility::BEgetRootLine($pageWithError['uid']);
            foreach ($rootLine as $item) {
                if (!isset($this->pagesForTree[$item['uid']])) {
                    $this->pagesForTree[$item['uid']] = 0;
                }
                $this->pagesForTree[$pageWithError['uid']] = $pageWithError['_amountOfBrokenLinks'];
            }
            // set background colors
            $this->backgroundColors[$pageWithError['uid']] = '#ff9926';
            unset($this->pagesForTree[0]);
        }
        return $pageTreeRepository->getTree(0);
    }
}