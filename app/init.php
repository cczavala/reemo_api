<?php
/**
* init.php
*
* @version 1.0.0 May-18
*/
require '../../config/autoload.php';
require '../../config/app.php';
// Definir las constantes del Sistema *
foreach ($C as $name => $val) {
	define($name,$val);
}
//