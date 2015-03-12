<?php
/*
ini_set ( "display_errors", 1 );
$dir = dirname ( __FILE__ );
$webroot = $_SERVER ['DOCUMENT_ROOT'];
$configfile = "$dir/amf_config.ini";
$servicesdir = $dir . '/../services';
$librarydir = $dir . '/../library';
//default zend install directory
$zenddir = $webroot . '/ZendFramework/library';

date_default_timezone_set( 'Brazil/East' );  


//Load ini file and locate zend directory
if (file_exists ( $configfile )) {
	$arr = parse_ini_file ( $configfile, true );
	if (isset ( $arr ['zend'] ['webroot'] )) {
		$webroot = $arr ['zend'] ['webroot'];
		$zenddir = $webroot . '/ZendFramework/library';
	}
	if (isset ( $arr ['zend'] ['zend_path'] )) {
		$zenddir = $arr ['zend'] ['zend_path'];
	}
	if (isset ( $arr ['zend'] ['library'] )) {
		$librarydir = $arr ['zend'] ['library'];
	}
	if (isset ( $arr ['zend'] ['services'] )) {
		$servicesdir = $arr ['zend'] ['services'];
	}
}
// Setup include path
// add zend directory, library and services to include path
set_include_path ( get_include_path () . PATH_SEPARATOR . $zenddir . PATH_SEPARATOR . $librarydir . PATH_SEPARATOR . $servicesdir );
// Initialize Zend Framework loader
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance ()->setFallbackAutoloader ( true )->suppressNotFoundWarnings ( false );
// Load configuration
$default_config = new Zend_Config ( array ("production" => false ), true );
$default_config->merge ( new Zend_Config_Ini ( $configfile, 'zendamf' ) );

$default_config->setReadOnly ();

$amf = $default_config->amf;

// Store configuration in the registry
Zend_Registry::set ( "amf-config", $amf );
// Initialize AMF Server
$server = new Zend_Amf_Server ();
$server->setProduction ( $amf->production );
$server->encoding = $amf->version;
if (isset ( $amf->directories )) {
	$dirs = $amf->directories->toArray ();
	foreach ( $dirs as $dir ) {
		if ($dir == "./") {
			$server->addDirectory ( $webroot );
		} else if (realpath ( "{$webroot}/{$dir}" )) {
			$server->addDirectory ( "{$webroot}/{$dir}" );
		} else if (realpath ( $dir )) {
			$server->addDirectory ( realpath ( $dir ) );
		}
	}
}


// Initialize introspector for non-production
if (! $amf->production) {
	$server->setClass ( 'Zend_Amf_Adobe_Introspector', '', array ("config" => $default_config, "server" => $server ) );
	$server->setClass ( 'Zend_Amf_Adobe_DbInspector', '', array ("config" => $default_config, "server" => $server ) );
}

*/

//=============inicio nova config=========
error_reporting(E_ALL); 
ini_set( 'display_errors','1');
$dir = dirname ( __FILE__ );
$webroot = $_SERVER ['DOCUMENT_ROOT'];
$configfile = "$dir/amf_config.ini";
$servicesdir = $dir . '/../services';
$librarydir = $dir . '/../library';
//default zend install directory
$zenddir = $webroot . '/ZendFramework/library';

date_default_timezone_set( 'Brazil/East' );

set_include_path ( get_include_path () . PATH_SEPARATOR . $zenddir . PATH_SEPARATOR . $librarydir . PATH_SEPARATOR . $servicesdir );
// Initialize Zend Framework loader
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance ()->setFallbackAutoloader ( true )->suppressNotFoundWarnings ( false );

// Instantiate server
$server = new Zend_Amf_Server();
$server->encoding = 'amf3';

$server->addDirectory("$webroot/com/followMoney/domain/services");
$server->setProduction(false);

// Handle request
echo $server->handle ();
?>

