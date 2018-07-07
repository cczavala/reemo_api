<?php
/**
* Auto cargado de clases *
*
* @version 1.0.0 May-18
*/
// Autoload classes *
function __autoload($class)
{
	$fileName = "app/class/" . $class . ".php";
	if (file_exists( $fileName )) {
		include_once $fileName;
	} else {
		$fileName = "../app/class/" . $class . ".php";
		if (file_exists( $fileName )) {
			include_once $fileName;
		} else {
			$fileName = "../../app/class/" . $class . ".php";
			if (file_exists( $fileName )) {
				include_once $fileName;
			} else {
				$fileName = "../../../app/class/" . $class . ".php";
				if (file_exists( $fileName )) {
					include_once $fileName;
				}
			}
		}
	}
	$fileName = "app/controllers/" . $class . ".php";
	if (file_exists( $fileName )) {
		include_once $fileName;
	} else {
		$fileName = "../app/controllers/" . $class . ".php";
		if (file_exists( $fileName )) {
			include_once $fileName;
		} else {
			$fileName = "../../app/controllers/" . $class . ".php";
			if (file_exists( $fileName )) {
				include_once $fileName;
			} else {
				$fileName = "../../../app/controllers/" . $class . ".php";
				if (file_exists( $fileName )) {
					include_once $fileName;
				}
			}
		}
	}
}