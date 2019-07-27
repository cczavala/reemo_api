<?php
/**
* Configuración de la aplicación *
*
<<<<<<< HEAD
* @version 1.0.0 Sep-18
=======
* @version 1.0.1 Mar-19
>>>>>>> development
*/
date_default_timezone_set( 'America/Mexico_City' );
define( 'SYSTEM_ENVIRONMENT','PROD' );
$C = [
	'DEBUG_MODE'            => false,
    'SYSTEM_FULL_NAME'      => 'REEMO - API - Sistema de Movilización Pecuaria',
    'SYSTEM_SHORT_NAME'     => 'REEMO - API',
    'SYSTEM_YEAR'           => date( 'Y' ),
    'SYSTEM_NAME_CLIENT'    => 'CNOG - SINIIGA',
    'SYSTEM_DOCUMENT_PATH'  => realpath( dirname($_SERVER['DOCUMENT_ROOT']) ),
	'NUMBER_ATTEMPTS_LOGIN' => 5,
	'WAIT_TIME_LOGIN'       => 15
];
switch (SYSTEM_ENVIRONMENT) {
	case 'PROD':
<<<<<<< HEAD
		$C['SYSTEM_VERSION'] = '1.0.0.07 re-api-prod';
		// Datos de Conexión a la Base de Datos de SINIIGA *
		$C['DB_HOST_PGN'] = '207.249.77.77';
		$C['DB_USER_PGN'] = 'siniigao_reemo';
		$C['DB_PASS_PGN'] = 'TeZ/2!3sp+p0';
		$C['DB_NAME_PGN'] = 'siniigao_db';    // -> PRODUCCIÓN
=======
		$C['SYSTEM_VERSION'] = '1.0.1.03 re-api-prod';
		// Datos de Conexión a la Base de Datos de SINIIGA *
		$C['DB_HOST_PGN'] = '201.150.33.6';
		$C['DB_USER_PGN'] = 'siniigam_reemo';
		$C['DB_PASS_PGN'] = 'TeZ/2!3sp+p0';
		$C['DB_NAME_PGN'] = 'siniigam_db';    // -> PRODUCCIÓN
>>>>>>> development
		//
		// Datos de Conexión a la Base de Datos de REEMO *
		$C['DB_HOST_REEMO'] = 'localhost';
		$C['DB_USER_REEMO'] = 'reemoorg_usuario';
		$C['DB_PASS_REEMO'] = 're3m0U5er';
		$C['DB_NAME_REEMO'] = 'reemoorg_movilizacion'; // -> PRODUCCIÓN
		//
		// Datos de Conexión a la Base de Datos de REEMO MEX *
		$C['DB_HOST_REEMOMX'] = 'localhost';
		$C['DB_USER_REEMOMX'] = 'reemoorg_usuario';
		$C['DB_PASS_REEMOMX'] = 're3m0U5er';
		$C['DB_NAME_REEMOMX'] = 'reemoorg_mex';
		break;
	case 'DEV':
<<<<<<< HEAD
		$C['SYSTEM_VERSION'] = '1.0.0.07 re-api-dev';
=======
		$C['SYSTEM_VERSION'] = '1.0.1.03 re-api-dev';
>>>>>>> development
		// Datos de Conexión a la Base de Datos de SINIIGA *
		$C['DB_HOST_PGN'] = '201.150.33.6';
		$C['DB_USER_PGN'] = 'siniigam_reemo';
		$C['DB_PASS_PGN'] = 'TeZ/2!3sp+p0';
		$C['DB_NAME_PGN'] = 'siniigao_curso'; // -> DESARROLLO
		//
		// Datos de Conexión a la Base de Datos de REEMO *
		$C['DB_HOST_REEMO'] = 'localhost';
		$C['DB_USER_REEMO'] = 'reemoorg_usuario';
		$C['DB_PASS_REEMO'] = 're3m0U5er';
		$C['DB_NAME_REEMO'] = 'reemoorg_test_movilizacion'; // -> DESAROLLO
		// DB REEMO_MX
		$C['DB_HOST_REEMOMX'] = 'localhost';
		$C['DB_USER_REEMOMX'] = 'reemoorg_usuario';
		$C['DB_PASS_REEMOMX'] = 're3m0U5er';
		$C['DB_NAME_REEMOMX'] = 'reemoorg_mex';
		break;
	case 'CAP':
		$C['SYSTEM_VERSION'] = '1.0.1.03 dm-api-prod';
		// Datos de Conexión a la Base de Datos de SINIIGA *
		$C['DB_HOST_PGN'] = 'localhost';
		$C['DB_USER_PGN'] = 'dashmx_usuario';
		$C['DB_PASS_PGN'] = 'u5u4r10';
		$C['DB_NAME_PGN'] = 'dashmx_siniiga';
		//
		// Datos de Conexión a la Base de Datos de REEMO *
		$C['DB_HOST_REEMO'] = 'localhost';
		$C['DB_USER_REEMO'] = 'dashmx_usuario';
		$C['DB_PASS_REEMO'] = 'u5u4r10';
		$C['DB_NAME_REEMO'] = 'dashmx_movilizacion';
		//
		//
		// Datos de Conexión a la Base de Datos de REEMO MEX *
		$C['DB_HOST_REEMOMX'] = 'localhost';
		$C['DB_USER_REEMOMX'] = 'dashmx_usuario';
		$C['DB_PASS_REEMOMX'] = 'u5u4r10';
		$C['DB_NAME_REEMOMX'] = 'dashmx_mex';
		break;
	case 'LOCAL':
		$C['SYSTEM_VERSION'] = '1.0.1.03 local-api-prod';
		// Datos de Conexión a la Base de Datos de SINIIGA *
		$C['DB_HOST_PGN'] = 'localhost';
		$C['DB_USER_PGN'] = 'dashmx_usuario';
		$C['DB_PASS_PGN'] = 'u5u4r10';
		$C['DB_NAME_PGN'] = 'siniigam_db';
		//
		// Datos de Conexión a la Base de Datos de REEMO *
		$C['DB_HOST_REEMO'] = 'localhost';
		$C['DB_USER_REEMO'] = 'dashmx_usuario';
		$C['DB_PASS_REEMO'] = 'u5u4r10';
		$C['DB_NAME_REEMO'] = 'dashmx_movilizacion';
		//
		//
		// Datos de Conexión a la Base de Datos de REEMO MEX *
		$C['DB_HOST_REEMOMX'] = 'localhost';
		$C['DB_USER_REEMOMX'] = 'dashmx_usuario';
		$C['DB_PASS_REEMOMX'] = 'u5u4r10';
		$C['DB_NAME_REEMOMX'] = 'dashmx_mex';
		break;
}