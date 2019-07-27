<?php
/**
* init.php
*
* @version 1.0.0 Oct-18
*/

require '../../config/autoload.php';
require '../../config/app.php';

// Define las constantes del sistema *
foreach ($C as $name => $val) {
	define( $name,$val );
}