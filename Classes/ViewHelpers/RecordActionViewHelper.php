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
        $this->registerArgument('field', 'string', 'field', false);
        $this->registerArgument('sys_language_uid', 'int', 'language', false);
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
        extract($arguments, 0);

        switch ($command) {
            case 'edit':
                $urlParameters = [
                    //'edit[' . $table . '][' . $uid . ']' => 'edit',
                    // see https://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/Examples/EditLinks/Index.html#editing-only-a-few-fields-from-a-record
                    'edit[' . $table . '][' . $uid . ']' => 'edit',
                    'columnsOnly'                        => $field,
                    'returnUrl'                          => (string)$uriBuilder->buildUriFromRoute('site_linkvalidator'),
  /*                  'curUrl' => [
                        'url'       => 'http://www.linkedin.com/groups?gid=70999',
                        'target'    => '_blank',
                        'title'     => 'Link to LinkedIn'
                    ],
  */
                ];
                $route = 'record_edit';
                return (string)$uriBuilder->buildUriFromRoute($route, $urlParameters);

            /**
             * Open Link Wizard. This does not work, because link
             * wizard must be opened from rte, otherwise RteLinkBrowser.js
             * finalizeFunction fails on
             * RteLinkBrowser.CKEditor.document
             *
             * todo: either remove this or find another way
             */
            /*
            case 'editlink':
                $parameters = [
                    'table'     => $table,
                    'fieldName' => $field,
                    'pid'       => $pid,
                    'uid'       => $uid,
                    'recordType' => ''
                ];
                $urlParameters = [
                    'contentsLanguage' => 'en',
                    // 'route'
                    // 'token*
                    'P' => $parameters,
                    'curUrl' => [
                        'url' => 'http://abc.de'
                    ],
                    'editorId' => 'cke_1'
                ];
                $route = 'rteckeditor_wizard_browse_links';
                return (string)$uriBuilder->buildUriFromRoute($route, $urlParameters);
            */

            case 'editlink':
                $parameters = [
                    'table'     => $table,
                    'fieldName' => $field,
                    'pid'       => $pid,
                    'uid'       => $uid,
                    'recordType' => ''
                ];
                $urlParameters = [
                    'contentsLanguage' => 'en',
                    // 'route'
                    // 'token*
                    'P' => $parameters,
                    'curUrl' => [
                        'url' => 'http://abc.de'
                    ],
                    'linkAttributes' => [
                        'target' => 'http://abcsfsf.de',
                        'title'  => '',
                        'class'  => '',
                        'params' => ''
                    ],
                    'editorId' => 'cke_1'
                ];
                $route = 'wizard_link';
                return (string)$uriBuilder->buildUriFromRoute($route, $urlParameters);


            case 'view':
                $pageId = $arguments['pid'];
                if ($arguments['table'] == 'pages') {
                    $pageId = $arguments['uid'];
                }

                // TODO: ok, there is probably a better way to do this but I am not in an Extbase
                // controller and the UriBuilder we're using here is for the Backend.
                $pageUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL')
                    . 'index.php?id=' . $pageId;
                if ($arguments['sys_language_uid'] ?? false) {
                    $pageUrl .= '&L=' . $arguments['sys_language_uid'];
                }
                if ($arguments['table'] == 'tt_content') {
                    $pageUrl .= '#c' . $arguments['uid'];
                }
                return $pageUrl;

            default:
                // todo get new exception id?
                throw new \InvalidArgumentException('Invalid command given to RecordActionViewhelper.', 1516708789);
        }
    }

    public static function experimentalLinkWizard(): array
    {
        $options = $this->data['renderData']['fieldControlOptions'];

        $title = $options['title'] ?? 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.link';

        $parameterArray = $this->data['parameterArray'];
        $itemName = $parameterArray['itemFormElName'];
        $windowOpenParameters = $options['windowOpenParameters'] ?? 'height=800,width=1000,status=0,menubar=0,scrollbars=1';

        $linkBrowserArguments = [];
        if (isset($options['blindLinkOptions'])) {
            $linkBrowserArguments['blindLinkOptions'] = $options['blindLinkOptions'];
        }
        if (isset($options['blindLinkFields'])) {
            $linkBrowserArguments['blindLinkFields'] = $options['blindLinkFields'];
        }
        if (isset($options['allowedExtensions'])) {
            $linkBrowserArguments['allowedExtensions'] = $options['allowedExtensions'];
        }
        $urlParameters = [
            'P' => [
                'params' => $linkBrowserArguments,
                'table' => $this->data['tableName'],
                'uid' => $this->data['databaseRow']['uid'],
                'pid' => $this->data['databaseRow']['pid'],
                'field' => $this->data['fieldName'],
                'formName' => 'editform',
                'itemName' => $itemName,
                'hmac' => GeneralUtility::hmac('editform' . $itemName, 'wizard_js'),
                'fieldChangeFunc' => $parameterArray['fieldChangeFunc'],
                'fieldChangeFuncHash' => GeneralUtility::hmac(serialize($parameterArray['fieldChangeFunc'])),
            ],
        ];
        /** @var \TYPO3\CMS\Backend\Routing\UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Backend\Routing\UriBuilder::class);
        $url = (string)$uriBuilder->buildUriFromRoute('wizard_link', $urlParameters);
        $onClick = [];
        $onClick[] = 'this.blur();';
        $onClick[] = 'vHWin=window.open(';
        $onClick[] =    GeneralUtility::quoteJSvalue($url);
        $onClick[] =    '+\'&P[currentValue]=\'+TBE_EDITOR.rawurlencode(';
        $onClick[] =        'document.editform[' . GeneralUtility::quoteJSvalue($itemName) . '].value,300';
        $onClick[] =    ')';
        $onClick[] =    '+\'&P[currentSelectedValues]=\'+TBE_EDITOR.curSelected(';
        $onClick[] =        GeneralUtility::quoteJSvalue($itemName);
        $onClick[] =    '),';
        $onClick[] =    '\'\',';
        $onClick[] =    GeneralUtility::quoteJSvalue($windowOpenParameters);
        $onClick[] = ');';
        $onClick[] = 'vHWin.focus();';
        $onClick[] = 'return false;';

        return [
            'iconIdentifier' => 'actions-wizard-link',
            'title' => $title,
            'linkAttributes' => [
                'onClick' => implode('', $onClick),
            ],
        ];
    }

}
