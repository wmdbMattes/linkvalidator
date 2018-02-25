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
define(["require","exports","jquery"],function(a,b,c){"use strict";var d=function(){function a(){var a=this;c(function(){c(".refresh").on("click",a.getList)})}return a.prototype.getList=function(a){c.ajax({url:TYPO3.settings.ajaxUrls.linkvalidator_listresults,dataType:"html",success:function(a){c("#t3js-linkvalidator-ajaxresults").html(a)}})},a}();return new d});