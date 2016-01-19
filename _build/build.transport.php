<?php
/**
 * ModLDAP Build Script
 *
 * @package modldap
 * @author Jason Coward <jason@modx.com>
**/
$tstart = microtime(true);
set_time_limit(0);

/* set package info */
define('PKG_NAME',      'ModLDAP');
define('PKG_NAME_LOWER',strtolower(PKG_NAME));
define('PKG_VERSION',   '2.4.1');
define('PKG_RELEASE',   'beta');

echo "<pre>";
/* define sources */
$root = dirname(dirname(__FILE__)) . '/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'resolvers' => $root . '_build/resolvers/',
    'data' => $root . '_build/data/',
    'snippets' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/snippets/',
    'plugins' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/plugins/',
    'lexicon' => $root . 'core/components/'.PKG_NAME_LOWER.'/lexicon/',
    'docs' => $root.'core/components/'.PKG_NAME_LOWER.'/docs/',
    'source_assets' => $root.'assets/components/'.PKG_NAME_LOWER,
    'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
);
unset($root);

/* instantiate MODx */
require_once $sources['build'].'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['build'] . '/includes/functions.php';

$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

/* load builder */
$modx->log(xPDO::LOG_LEVEL_INFO,'Creating package builder'); flush();
$modx->loadClass('transport.modPackageBuilder', '', false, true);

$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');


/* PLUGINS */
/* ------------------------------------------------------ */
$modx->log(xPDO::LOG_LEVEL_INFO,'Adding in plugin.'); flush();

$plugin= $modx->newObject('modPlugin');
$plugin->set('name', PKG_NAME);
$plugin->set('description', '<strong>'.PKG_VERSION.'-'.PKG_RELEASE.'</strong> This plugin is part of ModLDAP packages: handling LDAP-User authentication.');
$plugin->set('category', 0);
$plugin->set('plugincode', getSnippetContent($sources['plugins'] . 'plugin.modldap.php'));

//add properties to plugin
$properties = include $sources['data'].'plugin.modldap.properties.php';
$plugin->setProperties($properties);
unset($properties);

//add system events to plugin
$events = include $sources['data'].'plugin.modldap.event.php';
if (is_array($events) && !empty($events)) {
    $modx->log(modX::LOG_LEVEL_INFO,'Added '.count($events).' events to ModLDAP plugin.');
    $plugin->addMany($events);
}
unset($events);

//create vehicle for plugin
$modx->log(modX::LOG_LEVEL_INFO,'Create plugin vehicle'); flush();
$vehicle = $builder->createVehicle($plugin, array (
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'PluginEvents' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
            xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
        ),
    ),
));

$modx->log(modX::LOG_LEVEL_INFO,'Adding file resolvers to plugin...');
$vehicle->resolve('file',array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$vehicle->resolve('file',array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'] . 'resolver.modldap.php',
));
$builder->putVehicle($vehicle);
unset($vehicle,$plugin);



/* SNIPPETS */
/* ------------------------------------------------------ */
$modx->log(xPDO::LOG_LEVEL_INFO,'Adding in plugin.'); flush();

$snippets= $modx->newObject('modSnippet');
$snippets->set('name', PKG_NAME . 'Debug');
$snippets->set('description', '<strong>'.PKG_VERSION.'-'.PKG_RELEASE.'</strong> Output data entries from LDAP');
$snippets->set('category', 0);
$snippets->set('snippet', getSnippetContent($sources['snippets'].'snippet.modldapdebug.php'));

//add properties to snippet
$properties = include $sources['data'].'snippet.modldapdebug.properties.php';
$snippets->setProperties($properties);
unset($properties);

//create vehicle for plugin
$modx->log(modX::LOG_LEVEL_INFO,'Create snippet vehicle'); flush();
$vehicle = $builder->createVehicle($snippets, array (
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'name',
));

/*
$modx->log(modX::LOG_LEVEL_INFO,'Adding file resolvers to snippet...');
$vehicle->resolve('file',array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$vehicle->resolve('file',array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
*/
$builder->putVehicle($vehicle);
unset($vehicle,$snippet);



/* SYSTEM SETTING */
/* ------------------------------------------------------ */
$modx->log(xPDO::LOG_LEVEL_INFO,'Adding in system setting.'); flush();

$settings = array();
$settings = include $sources['data'].'transport.settings.php';
foreach ($settings as $setting) {
    $vehicle = $builder->createVehicle($setting, array(
        xPDOTransport::UNIQUE_KEY => 'key',
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => false,
    ));
    $builder->putVehicle($vehicle);
}


/* now pack in the license file, readme and setup options */
/* ------------------------------------------------------ */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
    //'setup-options' => array(
    //   'source' => $sources['build'].'setup.options.php',
    //),
));
$modx->log(modX::LOG_LEVEL_INFO,'Added package attributes and setup options.');

/* zip up the package */
$modx->log(modX::LOG_LEVEL_INFO,'Packing up transport package zip...');
$builder->pack();

$tend= microtime(true);
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f seconds", $totalTime);

$modx->log(xPDO::LOG_LEVEL_INFO, "Package Built.");
$modx->log(xPDO::LOG_LEVEL_INFO, "Execution time: {$totalTime}");
echo "</pre>";
exit();
