<?php
/**
* index.php
*
* @version 1.0.0 Oct-18
*/

require 'config/autoload.php';
require 'config/app.php';

// Define las constantes del sistema *
foreach ($C as $name => $val) {
	define( $name,$val );
}
session_start();
$tpl = SysTemplate::renderPage('resources/views/index.tpl');
$tpl->assign("systemName",SYSTEM_FULL_NAME);
$tpl->printToScreen();
SysTemplate::renderSimpleFooterPage();