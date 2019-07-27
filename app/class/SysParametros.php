<?php
/**
* Clase SysParametros
*
* @version 1.0.0 Oct-18
*/

class SysParametros extends Dbconnection
{

	private $_cveEdo;
	private $_userProfile;
	private $_parameters;
	private $_errorMsg;
	private $_responseMsg;

	public function __construct($userProfile,$cveEdo,$dbSiniiga = null,$dbReemo = null,$dbMx = null)
	{
		parent::__construct($dbSiniiga,$dbReemo,$dbMx,$cveEdo);
		if (is_int( $userProfile )) {
			$this->_userProfile = $userProfile;
		} else {
			$this->_userProfile = null;
		}
		$this->_cveEdo = $cveEdo;
		$this->getGlobalParameters();
		$this->getLocalParameters();
	}

	public function setErrorMsg($valor)
	{
		$this->_errorMsg = $valor;
	}

	public function getErrorMsg()
	{
		return $this->_errorMsg;
	}

	public function setResponseMsg($valor)
	{
		$this->_responseMsg = $valor;
	}
	
	public function getResponseMsg()
	{
		return $this->_responseMsg;
	}

	/**
	 * Consulta los parámetros globales del sistema *
	 *
	 * @access public
	 * @param string $returnType cadena con el tipo de regreso de datos (array o definir constantes).
	 * @return array $datos
	 */
	public function getGlobalParameters($returnType = null)
	{
		$datos = [];
		try {
			$sql = "SELECT limit_mov_file_earrings,limit_mov_man_earrings,enable_edit_values 
					FROM parametros_globales ";
			$query = $this->executeStmt($this->connectionToMx(),$sql);
			if (!empty( $query )) {
				$row = $query->fetch(PDO::FETCH_ASSOC);
				if (!empty( $row )) {
					switch ($returnType) {
						case 'array':
							$datos = [
								"limitMovFileEarrings" => $row['limit_mov_file_earrings'],
								"limitMovManEarrings"  => $row['limit_mov_man_earrings'],
								"enableEditParameters" => $row['enable_edit_values']
							];
							$cveEdo = str_pad( $this->_cveEdo,2,"0",STR_PAD_LEFT );
							if (strlen( trim($row['enable_edit_values']) ) > 0) {
								$arrValues = explode( "|",$row['enable_edit_values'] );
								if (in_array( $cveEdo,$arrValues )) {
									@define(ENABLE_EDIT_PARAMETERS,true);
								} else {
									@define(ENABLE_EDIT_PARAMETERS,false);
								}
							} else {
								define(ENABLE_EDIT_PARAMETERS,false);
							}
							return $datos;
							break;
						default:
							@define(LIMIT_MOV_FILE_EARRINGS,$row['limit_mov_file_earrings']);
							@define(LIMIT_MOV_MAN_EARRINGS,$row['limit_mov_man_earrings']);
							@define(ENABLE_EDIT_PARAMETERS,$row['enable_edit_values']);
							break;
					}
				} else {
					throw new ErrorException($this->getMsgErrorConnection());
				}
			} else {
				throw new ErrorException($this->getMsgErrorConnection());
			}
		} catch (ErrorException $e) {
			error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$this->setErrorMsg($e->getMessage() . "|" . __METHOD__ . "|");
		}
	}

	/**
	 * Consulta los parámetros del sistema en el estado *
	 *
	 * @access public
	 * @return array $datos
	 */
	public function getLocalParameters()
	{
		$datos = [];
		try {
			$sql = "SELECT email1,email2,vigencia,upp_individual,pruebas_sanitarias_oblig,solicitante,
					  editar_identificadores,moviliza_sin_upp,moviliza_fuera_sin_upp,moviliza_rastro,
					  moviliza_otro_predio,enable_cancelacion_admin,cancelaciones_guias 
					FROM parametros ";
			$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql);
			if (!empty( $query )) {
				$row = $query->fetch(PDO::FETCH_ASSOC);
				if (!empty( $row )) {
					$datos = [
						"emailUno"             => $row['email1'],
						"emailDos"             => $row['email2'], 
						"vigencia"             => $row['vigencia'],
						"uppIndividual"        => $row['upp_individual'],
						"solicitante"          => $row['solicitante'],
						"editIdentificadores"  => $row['editar_identificadores'],
						"movilizaSinUpp"       => $row['moviliza_sin_upp'],
						"movilizaFueraSinUpp"  => $row['moviliza_fuera_sin_upp'],
						"movilizaRastro"       => $row['moviliza_rastro'],
						"movilizaOtroPredio"   => $row['moviliza_otro_predio'],
						"pruebasSanitarias"    => $row['pruebas_sanitarias_oblig'],
						"enableCancelaAdmin"   => $row['enable_cancelacion_admin'],
						"cancelaciones"        => $row['cancelaciones_guias'],
						"identificadorEngorda" => true
					];
					define(EDIT_IDENTIFICADORES_FEATURE,($row['editar_identificadores'] == 1 ? true : false));
					define(ENABLE_ZONA_SANITARIA_PREDIO,($row['upp_individual'] == 1) ? true : false);
					define(MOVILIZA_SIN_UPP_FEATURE,($row['moviliza_sin_upp'] == 1) ? true : false);
					define(MOVILIZA_FUERA_SIN_UPP_FEATURE,($row['moviliza_fuera_sin_upp'] == 1 ? true : false));
					define(MOVILIZA_RASTRO_FEATURE,($row['moviliza_rastro'] == 1) ? true : false);
					define(MOVILIZA_OTRO_PREDIO_FEATURE,($row['moviliza_otro_predio'] == 1) ? true : false);
					define(ENABLE_CANCELACIONES_GUIAS_ADMIN,($row['enable_cancelacion_admin'] == 1) ? true : false);
					define(ENABLE_CANCELACIONES_GUIAS,($row['cancelaciones_guias'] == 1 ? true : false));
					define(MOVILIZA_IDENTIFICADOR_ENGORDA,($row['cancelaciones_guias'] == 1 ? true : false));
				} else {
					throw new ErrorException($this->getMsgErrorConnection());
				}
			} else {
				throw new ErrorException($this->getMsgErrorConnection());
			}
		} catch (ErrorException $e) {
			error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$this->setErrorMsg($e->getMessage() . "|" . __METHOD__ . "|");
		}
		return $datos;
	}

	/**
	 * Consulta los horarios de servicio del estado *
	 *
	 * @access public
	 * @return array $datos
	 */
	public function getSchedulesTime()
	{
		$datos = [];
		try {
			$sql = "SELECT horario_lunes,horario_martes,horario_miercoles,horario_jueves,horario_viernes,
					  horario_sabado,horario_domingo 
					FROM parametros
					LIMIT 1";
			$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql);
			if (!empty( $query )) {
				$row = $query->fetch(PDO::FETCH_ASSOC);
				if (!empty( $row )) {
					$datos = [
						"horaD"  => $row['horario_domingo'],
						"0"      => $row['horario_domingo'],
						"horaL"  => $row['horario_lunes'],
						"1"      => $row['horario_lunes'],
						"horaM"  => $row['horario_martes'],
						"2"      => $row['horario_martes'],
						"horaMi" => $row['horario_miercoles'],
						"3"      => $row['horario_miercoles'],
						"horaJ"  => $row['horario_jueves'],
						"4"      => $row['horario_jueves'],
						"horaV"  => $row['horario_viernes'],
						"5"      => $row['horario_viernes'],
						"horaS"  => $row['horario_sabado'],
						"6"      => $row['horario_sabado'],
					];
				} else {
					throw new ErrorException($this->getMsgErrorConnection());
				}
			} else {
				throw new ErrorException($this->getMsgErrorConnection());
			}
		} catch (ErrorException $e) {
			error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$this->setErrorMsg($e->getMessage() . "|" . __METHOD__ . "|");
		}
		return $datos;
	}

	/**
	 * Valida si el sistema se encuentra abierto o cerrado *
	 *
	 * @access public
	 * @return boolean
	 */
	public function validateOpenCloseSystemTime()
	{	
		$parametrosHora = $this->getSchedulesTime();
		$systemTime     = explode("|",$parametrosHora[date( 'w' )]);
		$openTime       = $systemTime[0];
		$closeTime      = $systemTime[1];
		// Hora Actual *
    	$currentTime    = date( 'His' );
    	//
		$openTime       = explode( ":",$openTime );
    	$openTime       = $openTime[0] . $openTime[1] . $openTime[2];
		$closeTime      = explode( ":",$closeTime );
    	$closeTime      = $closeTime[0] . $closeTime[1] . $closeTime[2];
    	if ($currentTime > $openTime && $currentTime < $closeTime) {
    		return true;
    	} else {
    		return false;
    	}
	}
}