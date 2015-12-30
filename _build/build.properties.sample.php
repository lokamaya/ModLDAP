<?php
/* define some properties */
$properties['cache_path'] = MODX_CORE_PATH . '/' . (MODX_CONFIG_KEY === 'config' ? '' : MODX_CONFIG_KEY . '/') . 'cache/';
$properties['xpdo_driver']= 'mysql';
$properties['logLevel']= xPDO::LOG_LEVEL_INFO;
