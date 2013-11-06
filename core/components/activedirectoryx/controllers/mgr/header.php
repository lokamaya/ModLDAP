<?php
/**
 * ActiveDirectoryX
 *
 * Copyright 2010 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of ActiveDirectoryX, which integrates Active Directory
 * authentication into MODx Revolution.
 *
 * ActiveDirectoryX is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * ActiveDirectoryX is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ActiveDirectoryX; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package activedirectoryx
 */
/**
 * Loads the header for mgr pages.
 *
 * @package activedirectoryx
 * @subpackage controllers
 */
$modx->regClientCSS($adirx->config['cssUrl'] . 'mgr.css');
$modx->regClientStartupScript($adirx->config['jsUrl'] . 'mgr/activedirectoryx.js');
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
    ActiveDirectoryX.config = ' . $modx->toJSON($activedirectoryx->config) . ';
    ActiveDirectoryX.config.connector_url = "' . $activedirectoryx->config['connectorUrl'] . '";
    ActiveDirectoryX.action = "' . (!empty($_REQUEST['a']) ? $_REQUEST['a'] : 0) . '";
});
</script>');

return '';