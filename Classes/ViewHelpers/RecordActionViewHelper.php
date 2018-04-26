<?php
declare(strict_types = 1);
namespace TYPO3\CMS\Linkvalidator\ViewHelpers;

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

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ViewHelper for providing an icon with an action.
 *
 * This ViewHelper is similar to ViewHelper in EXT:redirects. Check there
 * to see if generic solution can be used. Currently a generic solution
 * is not available yet.
 *
 * @todo remove once general edit view helper exists
 */
class RecordActionViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initializes the arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('command', 'string', 'Edit, View or Refresh', true);
        $this->registerArgument('uid', 'int', 'UID of the Record', false);
        $this->registerArgument('pid', 'int', 'PID of the Record', false);
        $this->registerArgument('table', 'string', 'table', false);
    }

    /**
     * Render link
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        switch ($arguments['command']) {
            case 'edit':
                $urlParameters = [
                    'edit[' . $arguments['table'] . '][' . $arguments['uid'] . ']' => 'edit',
                    'returnUrl' => (string)$uriBuilder->buildUriFromRoute('site_linkvalidator'),
                ];
                $route = 'record_edit';
                return (string)$uriBuilder->buildUriFromRoute($route, $urlParameters);

            case 'view':

                // TODO: ok, there is probably a better way to do this but I am not in an Extbase
                // controller and the UriBuilder we're using here is for the Backend.
                return GeneralUtility::getIndpEnv('TYPO3_SITE_URL')
                    . 'index.php?id=' . $arguments['pid']
                    . '#c' . $arguments['uid'];
                /*
                $urlParameters = [
                    'article' = $arguments['uid'];
                ];
                return (string)$uriBuilder->reset()
                    ->setTargetPageUid($arguments['pid'])
                    ->build();
                break;
                */
            default:
                // todo get new exception id?
                throw new \InvalidArgumentException('Invalid command given to RecordActionViewhelper.', 1516708789);
        }



    }
}
