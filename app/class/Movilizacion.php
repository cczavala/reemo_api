<?php
/**
* Clase Movilización
*
* @version 1.0.0 Jul-18
*/

class Movilizacion extends Dbconnection
{

	private $_cveEdo;
	public $_fillable = [
		"folio",
		"tipoMov",
		"fechaHoraMov",
		"responsable",
		"centroExpedidor",
		"origen",
		"destino",
		"motivo",
		"identificadores",
		"estatusMov"
	];

	public function __construct($cveEdo,$dbSiniiga = null,$dbReemo = null,$dbMx = null)
	{
		parent::__construct($dbSiniiga,$dbReemo,$dbMx,$cveEdo);
		$this->_cveEdo = $cveEdo;
	}

	public function setErrorMsg($value){
		$this->_errorMsg = $value;
	}
	
	public function getErrorMsg(){
		return $this->_errorMsg;
	}

	/**
     * Almacena un registro de una movilización *
     *
     * @access public
     * @param array $request
     * @return array $rsrc
     */
    public function saveMovilizacion($request)
    {  
		try {
			switch ($request['data'][1]) {
				case 1:
		        	$sql = "INSERT INTO movilizacion_api (folio_sige,tipo_movil,fecha_hora_sige,q_alta,centro_expedidor,upp_origen,
		        			  upp_destino,no_animales,especie,motivo_uso,estatus,f_alta) 
		        			VALUES (?,?,?,?,?,?,?,%s,'b',?,?,NOW())";
		        	$noAnimales = sizeof( $request['identificadores']);
		        	$sql = sprintf( $sql,$noAnimales );
					$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$request['data']);
					if ($query) {
						$fields = [];
						$values = [];
						foreach ($request['identificadores'] as $item) {
							$fields[] = "(?,?,?)";
							$values[] = $request['data'][0];
							$values[] = $item;
							$values[] = 1; 
						}
						$sql = "INSERT INTO movilizacion_api_arete (folio_sige,no_arete,estatus) 
								VALUES %s ";
						$sql = sprintf( $sql,implode( ",",$fields ));
						$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
						if ($query) {
							$rsrc = [
								"calificacion" => "Movilización con folios SIGE " . $request['data'][0] . " y REEMO " . $folioReemo . 
								" almacenada correctamente"
							];
						} else {
							throw new Exception($this->getMsgErrorConnection());
						}
					} else {
						if ($this->getErrorCode() == "23000") {
							throw new Exception("La movilización folio SIGE " . $request['data'][0] . " ya se encuentra registrada");
						} else {
							throw new Exception($this->getMsgErrorConnection());
						}
					}
					break;
				case 2:
					$sql = "INSERT INTO movilizacion_api (folio_sige,tipo_movil,fecha_hora_sige,q_alta,centro_expedidor,upp_origen,
		        			  upp_destino,cve_rastro,no_animales,especie,motivo_uso,estatus,f_alta) 
		        			VALUES (?,?,?,?,?,?,?,?,%s,'b',?,?,NOW())";
		        	$noAnimales = sizeof( $request['identificadores']);
		        	$sql = sprintf( $sql,$noAnimales );
					$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$request['data']);
					if ($query) {
						$fields = [];
						$values = [];
						foreach ($request['identificadores'] as $item) {
							$fields[] = "(?,?,?)";
							$values[] = $request['data'][0];
							$values[] = $item;
							$values[] = 1; 
						}
						$sql = "INSERT INTO movilizacion_api_arete (folio_sige,no_arete,estatus) 
								VALUES %s ";
						$sql = sprintf( $sql,implode( ",",$fields ));
						$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
						if ($query) {
							$rsrc = [
								"calificacion" => "Movilización folio SIGE " . $request['data'][0] . " almacenada correctamente"
							];
						} else {
							throw new Exception($this->getMsgErrorConnection());
						}
					} else {
						if ($this->getErrorCode() == "23000") {
							throw new Exception("La movilización folio SIGE " . $request['data'][0] . " ya se encuentra registrada");
						} else if ($this->getErrorCode() == "22001") {
							throw new Exception("El campo destino debe ser menor a 12 caractéres y mayor o igual a 4");
						} else {
							throw new Exception($this->getMsgErrorConnection());
						}
					}
					break;
				default:
					throw new Exception('Tipo de movilización desconocida');
					break;
			}
        } catch (Exception $e) {
            error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$rsrc = [
				"calificacion" => "No es posible almacenar la movilización",
                "motivo"       => $e->getMessage()
			];
        }
        return $rsrc;
	}

	/**
     * Almacena un registro de una movilización *
     *
     * @access public
     * @param array $request
     * @return array $rsrc
     */
    public function saveMovilizacionSiniiga($request)
    {  
		try {
			switch ($request['data'][1]) {
				case 1:
		        	$sql = "INSERT INTO movilizacion_api (folio_sige,tipo_movil,fecha_hora_sige,q_alta,centro_expedidor,upp_origen,
		        			  upp_destino,no_animales,especie,motivo_uso,estatus,f_alta) 
		        			VALUES (?,?,?,?,?,?,?,%s,'b',?,?,NOW())";
		        	$noAnimales = sizeof( $request['identificadores']);
		        	$sql = sprintf( $sql,$noAnimales );
					$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$request['data']);
					if ($query) {
						$fields = [];
						$values = [];
						foreach ($request['identificadores'] as $item) {
							$fields[] = "(?,?,?)";
							$values[] = $request['data'][0];
							$values[] = $item;
							$values[] = 1; 
						}
						$sql = "INSERT INTO movilizacion_api_arete (folio_sige,no_arete,estatus) 
								VALUES %s ";
						$sql = sprintf( $sql,implode( ",",$fields ));
						$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
						if ($query) {
							$rsrc = [
								"calificacion" => "Movilización folio SIGE " . $request['data'][0] . " almacenada correctamente"
							];
						} else {
							throw new Exception($this->getMsgErrorConnection());
						}
					} else {
						if ($this->getErrorCode() == "23000") {
							throw new Exception("La movilización folio SIGE " . $request['data'][0] . " ya se encuentra registrada");
						} else {
							throw new Exception($this->getMsgErrorConnection());
						}
					}
					break;
				case 2:
					$sql = "INSERT INTO movilizacion_api (folio_sige,tipo_movil,fecha_hora_sige,q_alta,centro_expedidor,upp_origen,
		        			  upp_destino,cve_rastro,no_animales,especie,motivo_uso,estatus,f_alta) 
		        			VALUES (?,?,?,?,?,?,?,?,%s,'b',?,?,NOW())";
		        	$noAnimales = sizeof( $request['identificadores']);
		        	$sql = sprintf( $sql,$noAnimales );
					$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$request['data']);
					if ($query) {
						$fields = [];
						$values = [];
						foreach ($request['identificadores'] as $item) {
							$fields[] = "(?,?,?)";
							$values[] = $request['data'][0];
							$values[] = $item;
							$values[] = 1; 
						}
						$sql = "INSERT INTO movilizacion_api_arete (folio_sige,no_arete,estatus) 
								VALUES %s ";
						$sql = sprintf( $sql,implode( ",",$fields ));
						$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
						if ($query) {
							$rsrc = [
								"calificacion" => "Movilización folio SIGE " . $request['data'][0] . " almacenada correctamente"
							];
						} else {
							throw new Exception($this->getMsgErrorConnection());
						}
					} else {
						if ($this->getErrorCode() == "23000") {
							throw new Exception("La movilización folio SIGE " . $request['data'][0] . " ya se encuentra registrada");
						} else if ($this->getErrorCode() == "22001") {
							throw new Exception("El campo destino debe ser menor a 12 caractéres y mayor o igual a 4");
						} else {
							throw new Exception($this->getMsgErrorConnection());
						}
					}
					break;
				default:
					throw new Exception('Tipo de movilización desconocida');
					break;
			}
        } catch (Exception $e) {
            error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$rsrc = [
				"calificacion" => "No es posible almacenar la movilización",
                "motivo"       => $e->getMessage()
			];
        }
        return $rsrc;
	}
	
	/**
     * Almacena un registro de una petición de cancelación de una movilización *
     *
     * @access public
     * @param array $request
     * @return array $rsrc
     */
    public function cancelaMovilizacion($request)
    {  
		try {
			$sql = "INSERT INTO movilizacion_api_cancela (folio_sige,motivo_cancela,f_alta,q_alta) 
        			VALUES (?,?,NOW(),1)";
			$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$request);
			if ($query) {
				$rsrc = [
					"calificacion" => "Movilización folio SIGE " . $request[0] . " para cancelación almacenado correctamente"
				];
			} else {
				if ($this->getErrorCode() == "23000") {
					throw new Exception("La movilización folio SIGE " . $request[0] . " ya se encuentra registrada");
				} else {
					throw new Exception($this->getMsgErrorConnection());
				}
			}
        } catch (Exception $e) {
            error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$rsrc = [
				"calificacion" => "No es posible almacenar la cancelación",
                "motivo"       => $e->getMessage()
			];
        }
        return $rsrc;
	}
}