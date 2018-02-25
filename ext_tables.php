<?php
defined('TYPO3_MODE') or die();

// Add module
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::insertModuleFunction(
    'web_info',
    \TYPO3\CMS\Linkvalidator\Report\LinkValidatorReport::class,
    null,
    'LLL:EXT:linkvalidator/Resources/Private/Language/locallang.xlf:mod_linkvalidator'
);

// Initialize Context Sensitive Help (CSH)
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'linkvalidator',
    'EXT:linkvalidator/Resources/Private/Language/Module/locallang_csh.xlf'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
    'site',
    'linkvalidator',
    '',
    '',
    [
        'routeTarget' => \TYPO3\CMS\Linkvalidator\Controller\LinkValidatorController::class . '::handleRequest',
        'access' => 'group,user',
        'name' => 'site_linkvalidator',
        'icon' => 'EXT:linkvalidator/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:linkvalidator/Resources/Private/Language/locallang_module_linkvalidator.xlf'
    ]
);
