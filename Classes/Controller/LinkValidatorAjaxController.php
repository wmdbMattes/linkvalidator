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
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Linkvalidator\Repository\LinkResultRepository;

/**
 * Main script class for rendering of the folder tree
 */
class LinkValidatorAjaxController
{
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function listAction(ServerRequestInterface $request): ResponseInterface
    {
        $page = $request->getQueryParams()['page'];
        $repository = new LinkResultRepository();
        #$results = $repository->getResults([], [], 0);
        return new HtmlResponse('<h1>huhu</h1>');
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function scanAllAction(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(['kommt noch']);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function scanSingleAction(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(['kommt noch']);
    }

    protected function accessGuard()
    {
        //
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * Returns an instance of LanguageService
     *
     * @return \TYPO3\CMS\Core\Localization\LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
