<?php
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

namespace TYPO3\CMS\Linkvalidator\Tests\Linktype;

use TYPO3\CMS\Linkvalidator\Linktype\ExternalLinktype;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ExternalLinktypeTest extends UnitTestCase
{

    public function fetchTypeDataProvider()
    {
        return [
            'external link' => [
                [],
                'page',
                'key',
                'page'
            ]
        ];
    }

    public function testFetchType()
    {
        $subject = new ExternalLinktype();
        $actualResult = $subject->fetchType(['tokenValue' => 'https://foo.bar'], 'blabla', 'WhatKeyIsThis?');
        $this->assertSame('blabla', $actualResult);
    }
}
