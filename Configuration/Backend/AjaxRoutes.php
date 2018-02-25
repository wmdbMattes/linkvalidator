<?php

/**
 * Definitions for routes provided by EXT:linkvalidator
 * Contains all AJAX-based routes for entry points
 *
 * Currently the "access" property is only used so no token creation + validation is made
 * but will be extended further.
 */
return [
    'linkvalidator_listresults' => [
        'path' => '/linkvalidator/list',
        'target' => \TYPO3\CMS\Linkvalidator\Controller\LinkValidatorAjaxController::class . '::listAction'
    ],
    'linkvalidator_scan_all' => [
        'path' => '/linkvalidator/scan/all',
        'target' => \TYPO3\CMS\Linkvalidator\Controller\LinkValidatorAjaxController::class . '::scanAllAction'
    ],
    'linkvalidator_scan_single' => [
        'path' => '/linkvalidator/scan/single',
        'target' => \TYPO3\CMS\Linkvalidator\Controller\LinkValidatorAjaxController::class . '::scanSingleAction'
    ],
];
