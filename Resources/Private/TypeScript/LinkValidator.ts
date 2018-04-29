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

import * as $ from 'jquery';

class LinkValidator {

    constructor() {
        $(
            (): void => {
                $('.refresh').on('click', this.getList);
                $('.scanall').on('click', this.scanAll);
            }
        );
    }

    public getList(event: JQueryEventObject): void {
        $.ajax({
            url: TYPO3.settings.ajaxUrls.linkvalidator_listresults,
            dataType: 'html',
            success: (markup: string): void => {
                $('#t3js-linkvalidator-ajaxresults').html(markup);
            }
        });
    }

    public scanAll(event: JQueryEventObject): void {
        $.ajax({
            url: TYPO3.settings.ajaxUrls.linkvalidator_scan_all,
            dataType: 'html',
            success: (markup: string): void => {
                $('#t3js-linkvalidator-ajaxresults').html(markup);
            }
        });
    }
}

export = new LinkValidator();
