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
define(["require","exports","jquery"],function(t,n,s){"use strict";return new(function(){function t(){var t=this;s(function(){s(".refresh").on("click",t.getList),s(".scanall").on("click",t.scanAll)})}return t.prototype.getList=function(t){s.ajax({url:TYPO3.settings.ajaxUrls.linkvalidator_listresults,dataType:"html",success:function(t){s("#t3js-linkvalidator-ajaxresults").html(t)}})},t.prototype.scanAll=function(t){s.ajax({url:TYPO3.settings.ajaxUrls.linkvalidator_scan_all,dataType:"html",success:function(t){s("#t3js-linkvalidator-ajaxresults").html(t)}})},t}())});