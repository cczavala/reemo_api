<?php
/**
* Clase Movilización
*
* @version 1.0.0 Oct-18
*/

class Movilizacion extends Dbconnection
{

	private $_cveEdo;
	private $_objEstado;
	private $_objIdentificador;
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
		$this->_objEstado        = new Estado($cveEdo,$dbMx);
		$this->_objIdentificador = new Identificador($cveEdo,$dbSiniiga,$dbReemo,$dbMx);
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
     * @param array $request arreglo con la información de la movilización.
     * @param string $especie cadena con el valor de la especie animal a movilizar.
     * @return array $rsrc
     */
    public function saveMovilizacion($request,$especie)
    {  
		try {
			switch ($request['data'][1]) {
				case 1: // Movilización a otro predio UPP/PSG/PG *
		        	$sql = "INSERT INTO movilizacion_api (folio_sige,tipo_movil,fecha_hora_sige,q_alta,centro_expedidor,upp_origen,
<<<<<<< HEAD
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
=======
		        			  upp_destino,no_animales,especie,estatus,tipo_transporte,dictamenes,transaccion,f_alta) 
		        			VALUES (?,?,?,?,?,?,?,%s,'b',?,?,?,1,NOW())";
		        	$noAnimales = count( $request['identificadores']);
		        	if ($noAnimales > 0) {
			        	$sql = sprintf( $sql,$noAnimales );
						$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$request['data']);
						if ($query) {
							$idFolio         = $this->dbReemo->lastInsertId();
							$motivo          = 1;
							$fields          = [];
							$values          = [];
							$identificadores = [];
							$centroOrigen    = $this->_objEstado->getCentro($request['data'][5]);
							$centroDestino   = $this->_objEstado->getCentro($request['data'][6]);
							if (substr($request['data'][6],9,1) == "P" || substr($request['data'][6],9,1) == "p") {
								if (substr($request['data'][6],10,2) == "03") {
									$motivo = 11; // Movilización a Sacrificio *
								}
							}
							foreach ($request['identificadores'] as $item) {
								$fields[] = "(?,?,?)";
								$values[] = $request['data'][0];
								$values[] = $item;
								$values[] = 1;
								$identificadorInfo = $this->_objIdentificador->getIdentificadorInfo($item,$especie);
								if ($identificadorInfo['info'] != null) {
									$identificadores[] = [
										"numIdentificador" => $item,
										"edad"             => $identificadorInfo['info']['edad'],
										"sexo"             => $identificadorInfo['info']['sexo'],
										"tarjetasOrigen"   => $centroOrigen . $especie,
										"tarjetasDestino"  => $centroDestino . $especie,
										"tablaTarjetas"    => $identificadorInfo['info']['tablaTarjetas'],
									];
								} else {
									$identificadores[] = [
										"numIdentificador" => $item,
										"edad"             => null,
										"sexo"             => null,
										"tarjetasOrigen"   => $centroOrigen . $especie,
										"tarjetasDestino"  => $centroDestino . $especie,
										"tablaTarjetas"    => null
									];
								}
							}
							$sql = "INSERT INTO movilizacion_api_arete (folio_sige,no_arete,estatus) 
									VALUES %s ";
							$sql = sprintf( $sql,implode(",",$fields) );
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
								$rsrc = $this->saveMovilizacionSiniiga($request['data'][0],$idFolio,$motivo,$dataMovReemo);
							} else {
								throw new Exception($this->getMsgErrorConnection());
							}
>>>>>>> development
						} else {
							if ($this->getErrorCode() == "23000") {
								throw new Exception("La movilización folio SIGE " . $request['data'][0] . " ya se encuentra registrada.");
							} else {
								throw new ErrorException($this->getMsgErrorConnection());
							}
						}
					} else {
<<<<<<< HEAD
						if ($this->getErrorCode() == "23000") {
							throw new Exception("La movilización folio SIGE " . $request['data'][0] . " ya se encuentra registrada");
						} else if ($this->getErrorCode() == "22001") {
							throw new Exception("El campo destino debe ser menor a 12 caractéres y mayor o igual a 4");
						} else {
							throw new Exception($this->getMsgErrorConnection());
						}
=======
						throw new Exception("La movilización no cuenta con animales para almacenar.");
>>>>>>> development
					}
					break;
				case 3:
					$sql = "INSERT INTO movilizacion_api (folio_sige,tipo_movil,fecha_hora_sige,q_alta,centro_expedidor,upp_origen,
<<<<<<< HEAD
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
=======
		        			  upp_destino,cve_rastro,no_animales,especie,estatus,tipo_transporte,dictamenes,transaccion,f_alta) 
		        			VALUES (?,?,?,?,?,?,?,?,%s,'b',?,?,?,1,NOW())";
		        	$noAnimales = count( $request['identificadores']);
		        	if ($noAnimales > 0) {
			        	$sql   = sprintf( $sql,$noAnimales );
						$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$request['data']);
						if ($query) {
							$idFolio         = $this->dbReemo->lastInsertId();
							$motivo          = 1;
							$fields          = [];
							$values          = [];
							$identificadores = [];
							$centroOrigen    = $this->_objEstado->getCentro($request['data'][5]);
							foreach ($request['identificadores'] as $item) {
								$fields[] = "(?,?,?)";
								$values[] = $request['data'][0];
								$values[] = $item;
								$values[] = 1;
								$identificadorInfo = $this->_objIdentificador->getIdentificadorInfo($item,$especie);
								if ($identificadorInfo['info'] != null) {
									$identificadores[] = [
										"numIdentificador" => $item,
										"edad"             => $identificadorInfo['info']['edad'],
										"sexo"             => $identificadorInfo['info']['sexo'],
										"tarjetasOrigen"   => $centroOrigen . $especie,
										"tablaTarjetas"    => $identificadorInfo['info']['tablaTarjetas'],
									];
								} else {
									$identificadores[] = [
										"numIdentificador" => $item,
										"edad"             => null,
										"sexo"             => null,
										"tarjetasOrigen"   => $centroOrigen . $especie,
										"tablaTarjetas"    => null
									];
								}
							}
							$sql = "INSERT INTO movilizacion_api_arete (folio_sige,no_arete,estatus) 
									VALUES %s ";
							$sql = sprintf( $sql,implode(",",$fields) );
							$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
							if ($query) {
								$dataMovReemo['data'] = [
									$request['data'][0],
									$request['data'][1],
									$request['data'][5],
									$request['data'][6],
									$request['data'][8],
									$request['data'][4],
									$request['data'][7],
									$request['data'][3],
									$request['data'][9]
								];
								$dataMovReemo['identificadores'] = $identificadores;
								$rsrc = $this->saveMovilizacionSiniiga($request['data'][0],$idFolio,0,$dataMovReemo);
							} else {
								throw new Exception($this->getMsgErrorConnection());
							}
>>>>>>> development
						}
					} else {
						throw new Exception("La movilización no cuenta con animales para almacenar.");
					}
					break;
				case 3:
					throw new Exception('No se permite realizar este tipo de movilización.');
					break;
				default:
					throw new Exception('Tipo de movilización desconocida');
					break;
			}
        } catch (ErrorException $e) {
            error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$rsrc = [
				"calificacion" => 0,
<<<<<<< HEAD
				"mensaje"      => "No es posible almacenar la movilización",
=======
				"mensaje"      => "No fue posible almacenar la movilización.",
                "motivo"       => $e->getMessage()
			];
        } catch (Exception $e) {
			$rsrc = [
				"calificacion" => 0,
				"mensaje"      => "La movilización presenta inconvenientes para ser almacenada.",
>>>>>>> development
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
<<<<<<< HEAD
							throw new Exception("No fue posible registrar el identificador de la movilización en REEMO. " . 
							$this->getMsgErrorConnection());
						}
					} else {
						throw new Exception($this->getMsgErrorConnection());
=======
							throw new ErrorException("No fue posible registrar los identificadores de la movilización en REEMO. " . 
							$this->getMsgErrorConnection());
						}
					} else {
						throw new ErrorException($this->getMsgErrorConnection());
>>>>>>> development
					}
					break;
				case 2:
					break;
				default:
					throw new Exception('Tipo de movilización desconocida');
					break;
			}
<<<<<<< HEAD
        } catch (Exception $e) {
=======
        } catch (ErrorException $e) {
>>>>>>> development
            error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$rsrc = [
				"calificacion" => 0,
				"mensaje"      => "No es posible registrar la movilización en REEMO",
                "motivo"       => $e->getMessage()
			];
<<<<<<< HEAD
=======
        } catch (Exception $e) {
			$rsrc = [
				"calificacion" => 0,
				"mensaje"      => "No es posible registrar la movilización en REEMO",
                "motivo"       => $e->getMessage()
			];
>>>>>>> development
        }
        return $rsrc;
	}

	/**
     * Procesa una movilización para su registro en SINIIGA *
     *
     * @access public
     * @param string $folioSige cadena con el valor del folio sige.
     * @param string $folioReemo cadena con el valor del no. REEMO.
<<<<<<< HEAD
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
=======
     * @param integer $motivo valor numérico con el motivo de la movilización.
     * @param array $request arreglo con la información de la movilización.
     * @return array $rsrc
     */
    public function saveMovilizacionSiniiga($folioSige,$folioReemo,$motivo,$request)
    {  
		try {
			switch ($request['data'][1]) {
				case 1: // Movilización a otro predio UPP/PSG/PG *
					$valuesAretes          = [];
					$flagErrorAreteMov     = false;
					$valuesIdentificadores = [];
					foreach ($request['identificadores'] as $item) {
						$valuesHist = [];
						// Se inserta en el histórico donde se encuentra actualmente el identificador *
		            	$sqlHist = "INSERT INTO tarjetas_hist (folio,no_arete,id_upporg,id_uppdno,observa,motivo,especie,f_alta,q_alta) 
							        VALUES (?,?,?,?,?,1,0,NOW(),?)";
						$valuesHist = [
							$folioReemo,
							$item['numIdentificador'],
							$request['data'][2], //=> Predio origen
							$request['data'][3], //=> Predio destino
							"PUE-" . $folioSige, //=> Folio SIGE - Puebla
							$request['data'][9]  //=> ID Usuario REEMO
						];
						$queryHist = $this->executeStmt($this->connectionToSiniiga(),$sqlHist,$valuesHist);
						if ($queryHist) {
							if ($motivo == 11) { // Movilización a sacrificio *
								if ($item['tablaTarjetas'] == $item['tarjetasDestino']) {
									$sql = "UPDATE %s 
											SET id_upp = ?,f_baja = now(),q_baja = ?,motivo_baja = ?,rastro_tif = ?,estatus = ?,
											  confirmar = 1,f_modi = now(),q_modi = ? 
											WHERE no_arete = ?";
									$sql    = sprintf( $sql,$item['tarjetasDestino'] );
									$params = [$request['data'][3],$request['data'][9],2,$request['data'][3],0,$request['data'][9],
									$item['numIdentificador']];
									$query  = $this->executeStmt($this->connectionToSiniiga(),$sql,$params);
									if (!$query) {
										$values = [];
										$sql = "UPDATE movilizacion_api_arete SET estatus = 3 
												WHERE folio_sige = ? AND no_arete = ?";
										$values = [
											$folioSige,
											$item['numIdentificador']
										];
										$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
										$flagErrorAreteMov = true;
										$this->setMsgErrorConnection("El identificador " . $item['numIdentificador'] . 
										" no fue actualizado en la BD de SINIIGA por un error, contacte al administrador de SINIIGA.");
										if (!$query) {
											throw new ErrorException("Ocurrió un error al actualizar el identificador " . 
											$item['numIdentificador'] . " en la BD de REEMO.");
										}
									}
								} else {
									$sql = "INSERT INTO %s 
											  SELECT * FROM %s 
											  WHERE no_arete = ?";
									$sql   = sprintf( $sql,$item['tarjetasDestino'],$item['tablaTarjetas'] );
									$query = $this->executeStmt($this->connectionToSiniiga(),$sql,[$item['numIdentificador']]);
									if ($query) {
										$sql = "UPDATE %s 
												SET id_upp = ?,f_baja = now(),q_baja = ?,motivo_baja = ?,rastro_tif = ?,estatus = ?,
												  confirmar = 1,f_modi = now(),q_modi = ? 
												WHERE no_arete = ?";
										$sql    = sprintf( $sql,$item['tarjetasDestino'] );
										$params = [$request['data'][3],$request['data'][9],2,$request['data'][3],0,$request['data'][9],
										$item['numIdentificador']];
										$query  = $this->executeStmt($this->connectionToSiniiga(),$sql,$params);
										if ($query) {
											$sql = "DELETE FROM %s 
													WHERE no_arete = ?";
											$sql   = sprintf( $sql,$item['tablaTarjetas'] );
											$query = $this->executeStmt($this->connectionToSiniiga(),$sql,[$item['numIdentificador']]);
											if (!$query) {
												$values = [];
												$sql = "UPDATE movilizacion_api_arete SET estatus = 3 
														WHERE folio_sige = ? AND no_arete = ?";
												$values = [
													$folioSige,
													$item['numIdentificador']
												];
												$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
												$flagErrorAreteMov = true;
												$this->setMsgErrorConnection("Un identificador o identificadores no se pudieron " . 
												"actualizar en la BD de SINIIGA contacte al administrador de SINIIGA.");
												if (!$query) {
													throw new ErrorException("Ocurrió un error al actualizar el identificador " . 
													$item['numIdentificador'] . " en la BD de REEMO.");
												}
											}
										} else {
											$values = [];
											$sql = "UPDATE movilizacion_api_arete SET estatus = 3 
													WHERE folio_sige = ? AND no_arete = ?";
											$values = [
												$folioSige,
												$item['numIdentificador']
											];
											$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
											$flagErrorAreteMov = true;
											$this->setMsgErrorConnection("Un identificador o identificadores no se pudieron " . 
											"actualizar en la BD de SINIIGA contacte al administrador de SINIIGA.");
											if (!$query) {
												throw new ErrorException("Ocurrió un error al actualizar el identificador " . 
												$item['numIdentificador'] . " en la BD de REEMO.");
											}
										}
									} else {
										$values = [];
										$sql = "UPDATE movilizacion_api_arete SET estatus = 3 
												WHERE folio_sige = ? AND no_arete = ?";
										$values = [
											$folioSige,
											$item['numIdentificador']
										];
										$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
										$flagErrorAreteMov = true;
										$this->setMsgErrorConnection("Un identificador o identificadores no se pudieron actualizar " . 
										"en la BD de SINIIGA contacte al administrador de SINIIGA.");
										if (!$query) {
											throw new ErrorException("Ocurrió un error al actualizar el identificador " . 
											$item['numIdentificador'] . " en la BD de REEMO.");
										}
									}
								}
							} else {
								if ($item['tablaTarjetas'] == $item['tarjetasDestino']) {
									$sql = "UPDATE %s 
											SET id_upp = ?,f_modi = now(),q_modi = ? 
											WHERE no_arete = ?";
									$sql    = sprintf( $sql,$item['tablaTarjetas'] );
									$params = [$request['data'][3],$request['data'][9],$item['numIdentificador']];
									$query  = $this->executeStmt($this->connectionToSiniiga(),$sql,$params);
									if (!$query) {
										$values = [];
										$sql = "UPDATE movilizacion_api_arete SET estatus = 3 
												WHERE folio_sige = ? AND no_arete = ?";
										$values = [
											$folioSige,
											$item['numIdentificador']
										];
										$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
										$flagErrorAreteMov = true;
										$this->setMsgErrorConnection("El identificador " . $item['numIdentificador'] . 
										" no fue actualizado en la BD de SINIIGA por un error, contacte al administrador de SINIIGA.");
										if (!$query) {
											throw new ErrorException("Ocurrió un error al actualizar el identificador " . 
											$item['numIdentificador'] . " en la BD de REEMO.");
										}
									}
								} else {
									$sql = "INSERT INTO %s 
											  SELECT * FROM %s 
											  WHERE no_arete = ?";
									$sql   = sprintf( $sql,$item['tarjetasDestino'],$item['tablaTarjetas'] );
									$query = $this->executeStmt($this->connectionToSiniiga(),$sql,[$item['numIdentificador']]);
									if ($query) {
										$sql = "UPDATE %s 
												SET id_upp = ?,f_modi = now(),q_modi = ? 
												WHERE no_arete = ?";
										$sql    = sprintf( $sql,$item['tarjetasDestino'] );
										$params = [$request['data'][3],$request['data'][9],$item['numIdentificador']];
										$query  = $this->executeStmt($this->connectionToSiniiga(),$sql,$params);
										if ($query) {
											$sql = "DELETE FROM %s 
													WHERE no_arete = ?";
											$sql   = sprintf( $sql,$item['tablaTarjetas'] );
											$query = $this->executeStmt($this->connectionToSiniiga(),$sql,[$item['numIdentificador']]);
											if (!$query) {
												$values = [];
												$sql = "UPDATE movilizacion_api_arete SET estatus = 3 
														WHERE folio_sige = ? AND no_arete = ?";
												$values = [
													$folioSige,
													$item['numIdentificador']
												];
												$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
												$flagErrorAreteMov = true;
												$this->setMsgErrorConnection("Un identificador o identificadores no se pudieron " . 
												"actualizar en la BD de SINIIGA contacte al administrador de SINIIGA.");
												if (!$query) {
													throw new ErrorException("Ocurrió un error al actualizar el identificador " . 
													$item['numIdentificador'] . " en la BD de REEMO.");
												}
											}
										} else {
											$values = [];
											$sql = "UPDATE movilizacion_api_arete SET estatus = 3 
													WHERE folio_sige = ? AND no_arete = ?";
											$values = [
												$folioSige,
												$item['numIdentificador']
											];
											$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
											$flagErrorAreteMov = true;
											$this->setMsgErrorConnection("Un identificador o identificadores no se pudieron " . 
											"actualizar en la BD de SINIIGA contacte al administrador de SINIIGA.");
											if (!$query) {
												throw new ErrorException("Ocurrió un error al actualizar el identificador " . 
												$item['numIdentificador'] . " en la BD de REEMO.");
											}
										}
									} else {
										$values = [];
										$sql = "UPDATE movilizacion_api_arete SET estatus = 3 
												WHERE folio_sige = ? AND no_arete = ?";
										$values = [
											$folioSige,
											$item['numIdentificador']
										];
										$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
										$flagErrorAreteMov = true;
										$this->setMsgErrorConnection("Un identificador o identificadores no se pudieron actualizar " . 
										"en la BD de SINIIGA contacte al administrador de SINIIGA.");
										if (!$query) {
											throw new ErrorException("Ocurrió un error al actualizar el identificador " . 
											$item['numIdentificador'] . " en la BD de REEMO.");
										}
									}
								}
							}
						} else {
							throw new ErrorException("No se pudo agregar al histórico el identificador " . $item['numIdentificador']);
						}
					}
					break;
				case 2: // Movilización a rastro *
					$flagErrorAreteMov = false;
					foreach ($request['identificadores'] as $item) {
						$valuesHist = [];
						// Se inserta en el histórico donde se encuentra actualmente el identificador *
		            	$sqlHist = "INSERT INTO tarjetas_hist (folio,no_arete,id_upporg,id_uppdno,observa,motivo,especie,f_alta,q_alta) 
							        VALUES (?,?,?,?,?,1,0,NOW(),?)";
						$valuesHist = [
							$folioReemo,
							$item['numIdentificador'],
							$request['data'][2], //=> Predio origen
							$request['data'][3], //=> Rastro destino
							"PUE-" . $folioSige, //=> Folio SIGE - Puebla
							$request['data'][7]  //=> ID Usuario REEMO
						];
						$queryHist = $this->executeStmt($this->connectionToSiniiga(),$sqlHist,$valuesHist);
						if ($queryHist) {
							$sql = "UPDATE %s 
									SET f_baja = now(),q_baja = ?,motivo_baja = ?,rastro_tif = ?,estatus = ?,
									  confirmar = 1,f_modi = now(),q_modi = ? 
									WHERE no_arete = ?";
							$sql    = sprintf( $sql,$item['tablaTarjetas'] );
							$params = [$request['data'][9],2,$request['data'][3],0,$request['data'][9],$item['numIdentificador']];
							$query  = $this->executeStmt($this->connectionToSiniiga(),$sql,$params);
							if (!$query) {
								$values = [];
								$sql = "UPDATE movilizacion_api_arete SET estatus = 3 
										WHERE folio_sige = ? AND no_arete = ?";
								$values = [
									$folioSige,
									$item['numIdentificador']
								];
								$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,$values);
								$flagErrorAreteMov = true;
								$this->setMsgErrorConnection("El identificador " . $item['numIdentificador'] . 
								" no fue actualizado en la BD de SINIIGA por un error, contacte al administrador de SINIIGA.");
								if (!$query) {
									throw new ErrorException("Ocurrió un error al actualizar el identificador " . 
									$item['numIdentificador'] . " en la BD de REEMO.");
								}
>>>>>>> development
							}
						} else {
							throw new ErrorException("No se pudo agregar al histórico el identificador " . $item['numIdentificador']);
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
			if (!$flagErrorAreteMov) {
				$rsrc = [
					"calificacion" => 1,
					"mensaje"      => "Movilización con folio SIGE " . $folioSige . " ha actualizado SINIIGA correctamente.",
					"folio"        => $folioReemo,
				];
			} else {
				throw new ErrorException($this->getMsgErrorConnection());
			}
        } catch (ErrorException $e) {
            error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$rsrc = [
				"calificacion" => 0,
<<<<<<< HEAD
				"mensaje"      => "No es posible registrar la movilización en SINIIGA",
=======
				"mensaje"      => "No es posible registrar la movilización en SINIIGA.",
                "motivo"       => $e->getMessage()
			];
        } catch (Exception $e) {
			$rsrc = [
				"calificacion" => 0,
				"mensaje"      => "No es posible registrar la movilización en SINIIGA.",
>>>>>>> development
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
					throw new ErrorException($this->getMsgErrorConnection());
				}
			}
        } catch (ErrorException $e) {
            error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$rsrc = [
				"calificacion" => 0,
				"mensaje"      => "No es posible almacenar la cancelación",
                "motivo"       => $e->getMessage()
			];
        } catch (Exception $e) {
			$rsrc = [
				"calificacion" => 0,
				"mensaje"      => "No es posible almacenar la cancelación",
                "motivo"       => $e->getMessage()
			];
        }
        return $rsrc;
	}
}