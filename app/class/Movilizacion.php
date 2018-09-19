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
		"fechaHora",
		"responsable",
		"centroExpedidor",
		"origen",
		"destino",
		"identificadores",
		"estatusMov",
		"tipoTransporte",
		"dictamen"
	];

	public function __construct($cveEdo,$dbSiniiga = null,$dbReemo = null,$dbMx = null)
	{
		parent::__construct($dbSiniiga,$dbReemo,$dbMx,$cveEdo);
		$this->_cveEdo = $cveEdo;
	}

	public function setErrorMsg($value)
	{
		$this->_errorMsg = $value;
	}
	
	public function getErrorMsg()
	{
		return $this->_errorMsg;
	}

	/**
     * Almacena el registro de una movilización a procesar *
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
		        			  upp_destino,no_animales,especie,estatus,tipo_transporte,dictamenes,f_alta) 
		        			VALUES (?,?,?,?,?,?,?,%s,'b',?,?,?,NOW())";
		        	$noAnimales = sizeof( $request['identificadores']);
		        	if ($noAnimales > 0) {
			        	$sql = sprintf( $sql,$noAnimales );
						$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$request['data']);
						if ($query) {
							$fields          = [];
							$values          = [];
							$identificadores = [];
							foreach ($request['identificadores'] as $item) {
								$fields[] = "(?,?,?)";
								$values[] = $request['data'][0];
								$values[] = $item;
								$values[] = 1;
								$identificadores[] = [
									"numIdentificador" => $item,
									"edad"             => 0,
									"sexo"             => 0,
									"tablaTarjetas"    => ''
								];
							}
							$sql = "INSERT INTO movilizacion_api_arete (folio_sige,no_arete,estatus) 
									VALUES %s ";
							$sql = sprintf( $sql,implode( ",",$fields ));
							$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
							if ($query) {
								$dataMovReemo['data'] = [
									$request['data'][0],
									$request['data'][1],
									$request['data'][5],
									$request['data'][6],
									substr($request['data'][6],0,2),
									substr($request['data'][6],2,3),
									$request['data'][8],
									$request['data'][4],
									$request['data'][7],
									$request['data'][3],
									$request['data'][9]
								];
								$dataMovReemo['identificadores'] = $identificadores;
								$rsrc = $this->saveMovilizacionReemo($request['data'][0],$dataMovReemo);
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
					} else {
						throw new Exception("La movilización no cuenta con animales para almacenar.");
					}
					break;
				case 2:
					$sql = "INSERT INTO movilizacion_api (folio_sige,tipo_movil,fecha_hora_sige,q_alta,centro_expedidor,upp_origen,
		        			  upp_destino,cve_rastro,no_animales,especie,estatus,tipo_transporte,dictamenes,f_alta) 
		        			VALUES (?,?,?,?,?,?,?,?,%s,'b',?,?,?,NOW())";
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
								"calificacion" => 1,
								"mensaje"      => "Movilización con folio SIGE " . $request['data'][0] . " se asigno el folio REEMO " . 
								"PUE00000 y fue almacenada correctamente."
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
				case 3:
					$sql = "INSERT INTO movilizacion_api (folio_sige,tipo_movil,fecha_hora_sige,q_alta,centro_expedidor,upp_origen,
		        			  upp_destino,cve_rastro,no_animales,especie,estatus,tipo_transporte,dictamenes,f_alta) 
		        			VALUES (?,?,?,?,?,?,?,?,%s,'b',?,?,?,NOW())";
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
								"calificacion" => 1,
								"mensaje"      => "Movilización con folio SIGE " . $request['data'][0] . " se asigno el folio REEMO " . 
								"PUE00000 y fue almacenada correctamente."
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
				"calificacion" => 0,
				"mensaje"      => "No es posible almacenar la movilización",
                "motivo"       => $e->getMessage()
			];
        }
        return $rsrc;
	}

	/**
     * Procesa una movilización para su registro en REEMO *
     *
     * @access public
     * @param string $folioSige cadena con el valor del folio sige.
     * @param array $request arreglo con la información de la movilización.
     * @return array $rsrc
     */
    public function saveMovilizacionReemo($folioSige,$request)
    {  
		try {
			switch ($request['data'][1]) {
				case 1:
					$sql = "INSERT INTO movilizacion (folio_sige,fecha_mov,tipo_movil,upp_origen,upp_destino,cve_edo_dest,cve_mun_dest,
							  no_animales,motivo,motivo_uso,%stipo_transporte,asociacion_gan,estatus,f_alta,q_alta) 
							VALUES (?,CURDATE(),?,?,?,?,?,%s,1,1,%s?,?,?,NOW(),?)";
		        	$noAnimales = sizeof( $request['identificadores']);
		        	if ($request['data'][10] != "" || !is_null( $request['data'][10] )) {
		        		$dictamenes = explode( ",",$request['data'][10] );
		        		if (sizeof( $dictamenes ) > 1) {
		        			foreach ($dictamenes as $itemDictamen) {
		        				$dictamen       = explode( "|",$itemDictamen );
		        				$dictamenFields = $dictamenFields . $this->getValueNameDictamen($dictamen[0]) . ",";
		        				$dictamenVals   = $dictamenVals . "'" . $dictamen[1] . "',";
		        			}
		        		} else {
		        			$dictamen       = explode( "|",$request['data'][10] );
		        			$dictamenFields = $this->getValueNameDictamen($dictamen[0]) . ",";
		        			$dictamenVals   = "'" . $dictamen[1] . "',";
		        		}
		        	}
		        	array_pop( $request['data'] );
		        	$sql   = sprintf( $sql,$dictamenFields,$noAnimales,$dictamenVals );
					$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$request['data']);
					if ($query) {
						$valuesAretes = [];
						$idFolio      = $this->dbReemo->lastInsertId();
						$folioReemo   = str_pad( $idFolio,6,"0",STR_PAD_LEFT );
						$queryIdentificador = "INSERT INTO movilizacion_desc (folio,no_arete,sexo,edad) 
										 	   VALUES %s";
						foreach ($request['identificadores'] as $itemIdentificador) {
							$valuesIdentificadores[] = "(?,?,?,?)";
							$valuesAretes[] = $idFolio;
							$valuesAretes[] = $itemIdentificador['numIdentificador'];
							$valuesAretes[] = $itemIdentificador['sexo'];
							$valuesAretes[] = $itemIdentificador['edad'];
						}
						$values           = implode( ",",$valuesIdentificadores );
						$sqlIdentificador = sprintf( $queryIdentificador,$values );
						$query            = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sqlIdentificador,$valuesAretes);
						if ($query) {
							$rsrc = $this->saveMovilizacionSiniiga($folioSige,$folioReemo,$request);
						} else {
							throw new Exception("No fue posible registrar el identificador de la movilización en REEMO. " . 
							$this->getMsgErrorConnection());
						}
					} else {
						throw new Exception($this->getMsgErrorConnection());
					}
					break;
				case 2:
					break;
				default:
					throw new Exception('Tipo de movilización desconocida');
					break;
			}
        } catch (Exception $e) {
            error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$rsrc = [
				"calificacion" => 0,
				"mensaje"      => "No es posible registrar la movilización en REEMO",
                "motivo"       => $e->getMessage()
			];
        }
        return $rsrc;
	}

	/**
     * Procesa una movilización para su registro en SINIIGA *
     *
     * @access public
     * @param string $folioSige cadena con el valor del folio sige.
     * @param string $folioReemo cadena con el valor del no. REEMO.
     * @param array $request arreglo con la información de la movilización.
     * @return array $rsrc
     */
    public function saveMovilizacionSiniiga($folioSige,$folioReemo,$request)
    {  
		try {
			if (SYSTEM_ENVIRONMENT == "DEV") {
				switch ($request['data'][1]) {
					case 1:
						$valuesAretes          = [];
						$flagErrorAreteMov     = false;
						$valuesIdentificadores = [];
						// Se inserta en el histórico de donde está ubicado actualmente *
						$sql = "INSERT INTO tarjetas_hist (folio,no_arete,id_upporg,id_uppdno,motivo,especie,f_alta,q_alta) 
								VALUES (?,?,?,?,1,0,'0000-00-00',?)";
						$query = $this->executeStmt($this->connectionToSiniiga(),$sql,$request['data']);
						if ($queryHist) {
							$folioReemo = str_pad( $folioReemo,6,"0",STR_PAD_LEFT );
							$rsrc = [
								"calificacion" => 1,
								"mensaje"      => "Movilización con folio SIGE " . $folioSige . " se le ha asociado el No. REEMO " . 
								"PUE " . $folioReemo . " y se ha actualizado el REEMO y SINIIGA correctamente."
							];
						} else {
							throw new Exception("No fue posible registrar el identificador de la movilización en REEMO. " . 
							$this->getMsgErrorConnection());
						}
			            //
			            // Se inserta en el histórico de la movilización realizada *
			            $sql = "INSERT INTO tarjetas_hist (folio,no_arete,id_upporg,id_uppdno,motivo,especie,f_alta,q_alta) 
								VALUES %s";
						foreach ($request['identificadores'] as $item) {
							$valuesIdentif[] = "(?,?,?,?,1,0,NOW(),?)";
							$valuesAretes[]  = $folioReemo;
							$valuesAretes[]  = $item['numIdentificador'];
							$valuesAretes[]  = $request['data'][2];
							$valuesAretes[]  = $request['data'][3];
							$valuesAretes[]  = $request['data'][9];
						}
						$sqlHist   = sprintf( $sql,implode( ",",$valuesIdentif ) );
						$queryHist = $this->executeStmt($this->connectionToSiniiga(),$sqlHist,$valuesAretes);
						if ($queryHist) {
							//if ($motivo_mov == 11) {
							if (false) {
								$sql = "UPDATE %s 
										SET id_upp = ?,f_baja = now(),q_baja = ?,motivo_baja = ?,rastro_tif = ?,estatus = ?,confirmar = 1,
										  f_modi = now(),q_modi = ? 
										WHERE no_arete = ?";
								foreach ($request['identificadores'] as $item) {
									$sql    = sprintf( $sql,$item['tablaTarjetas'] );
									$params = [$request['data'][3],$request['data'][9],8,$request['data'][3],0,$request['data'][9],
									$item['numIdentificador']];
									$queryArete = $this->executeStmt($this->connectionToSiniiga(),$sql,$params);
									if (!$queryArete) {
										$flagErrorAreteMov = true;
									}
								}
							} else {
								$sql = "UPDATE %s 
										SET id_upp = ?,f_modi = now(),q_modi = ? 
										WHERE no_arete = ?";
								foreach ($request['identificadores'] as $item) {
									$sql    = sprintf( $sql,$item['tablaTarjetas'] );
									$params = [$request['data'][3],$request['data'][9],$item['numIdentificador']];
									$queryArete = $this->executeStmt($this->connectionToSiniiga(),$sql,$params);
									if (!$queryArete) {
										$flagErrorAreteMov = true;
									}
								}
							}
							if (!$flagErrorAreteMov) {
								$rsrc = [
									"calificacion" => 1,
									"mensaje"      => "Movilización con folio SIGE " . $folioSige . " se le ha asociado el No. REEMO " . 
									"PUE " . $folioReemo . " y se ha actualizado el REEMO y SINIIGA correctamente."
								];
							} else {
								throw new Exception($this->getMsgErrorConnection());
							}
						} else {
							throw new Exception($this->getMsgErrorConnection());
						}
						if (true) {
							$rsrc = [
								"calificacion" => 1,
								"mensaje"      => "Movilización con folio SIGE " . $folioSige . " se le ha asociado el No. REEMO " . 
								"PUE " . $folioReemo . " y se ha actualizado el REEMO y SINIIGA correctamente."
							];
						}
						break;
					case 2:
						break;
					default:
						throw new Exception('Tipo de movilización desconocida');
						break;
				}
			} else {
				$rsrc = [
					"calificacion" => 1,
					"mensaje"      => "Movilización con folio SIGE " . $folioSige . " se le ha asociado el No. REEMO " . 
					"PUE " . $folioReemo . " y se ha actualizado el REEMO y SINIIGA correctamente."
				];
			}
        } catch (Exception $e) {
            error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$rsrc = [
				"calificacion" => 0,
				"mensaje"      => "No es posible registrar la movilización en SINIIGA",
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