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
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Linkvalidator\Repository\LinkResultRepository;

class AjaxTreeController extends TreeController
{
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
        $entryPoints = $this->getPagesWithLinkErrors();
        $items = [];
        foreach ($entryPoints as $page) {
//            $page['_children'] = [];
            $items = array_merge($items, $this->pagesToFlatArray($page, (int)$page['uid']));
        }

        return new JsonResponse($items);
    }

    protected function getPagesWithLinkErrors(): array
    {
        $linkResultRepository = new LinkResultRepository();
        $pagesWithErrors = $linkResultRepository->getPageIdsWithLinkErrors();
        foreach ($pagesWithErrors as $key => $pageRow) {
            $rootLine = BackendUtility::BEgetRootLine($pageRow['uid']);
            foreach ($rootLine as $item) {
                $pagesWithErrors[] = $item;
            }
        }
        return $pagesWithErrors;
    }
}