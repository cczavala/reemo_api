<?php
/**
* Clase Identificador
*
* @version 1.0.0 Jul-18
*/

class Identificador extends Dbconnection
{

	private $_cveEdo;
	private $_centros;
	private $_confirmacion;

	public function __construct($cveEdo,$dbSiniiga = null,$dbReemo = null,$dbMx = null)
	{
		parent::__construct($dbSiniiga,$dbReemo,$dbMx,$cveEdo);
		$this->_cveEdo       = $cveEdo;
		$this->_confirmacion = '';
		$this->_centros      = $this->getCentros();
	}

	public function setErrorMsg($value){
		$this->_errorMsg = $value;
	}
	
	public function getErrorMsg(){
		return $this->_errorMsg;
	}

	/**
	* Se obtienen los identificadores de las ventanillas *
	*
	* @access public
	* @return array $datos
	*/
	public function getCentros()
	{
		$datos = [];
		$sql = "SELECT id_centro 
				FROM centros 
				WHERE id_centro > 1 AND (tipo_centro = 1 OR tipo_centro = 5) AND dep_aretes <> '' 
				ORDER BY id_centro ASC";
		$query = $this->executeStmt($this->connectionToSiniiga(),$sql);
	    if (!empty( $query )) {
			while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				$datos[] = $row['id_centro'];
			}
		}
		return $datos;
	}

	/**
	* Se obtienen los identificadores de las ventanillas del estado *
	*
	* @access public
	* @param string $cveEdo
	* @return array $datos
	*/
	public function getCentrosEdo($cveEdo)
	{
		$datos = [];
		$sql = "SELECT id_centro 
				FROM centros 
				WHERE id_centro > 1 AND (tipo_centro = 1 OR tipo_centro = 5) AND dep_aretes <> '' AND cve_edo = ?
				ORDER BY id_centro ASC";
		$query = $this->executeStmt($this->connectionToSiniiga(),$sql,[$cveEdo]);
	    if (!empty( $query )) {
			while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				$datos[] = $row['id_centro'];
			}
		}
		return $datos;
	}

	/**
	* Forma el nombre de la tabla de tarjetas al identificador del animal *
	*
	* @access public
	* @param integer $cveCentro número identificador de la ventanilla.
	* @param string $especieAnimal cadena del tipo de especie del animal.
	* @return string
	*/
	public function getNombreTablaTarjetas($cveCentro,$especieAnimal)
	{
		if (!empty( $cveCentro ) && !empty( $especieAnimal )) {
			return "tarjetas_" . str_pad( $cveCentro,3,'0',STR_PAD_LEFT ) . $especieAnimal;
		} else {
			return "tarjetas_099x";
		}
	}

	/**
	* Verifica la longitud del identificador *
	*
	* @access public
	* @param string $numIdentificador cadena del número de identificador del animal.
	* @param string $especieAnimal cadena del tipo de especie del animal.
	* @return boolean
	*/
	public function validaLongitudIdentificador($numIdentificador = null,$especieAnimal = 'b')
	{
		if (!is_null( $numIdentificador ) || trim( $numIdentificador ) != "") {
			switch ($especieAnimal) {
				case 'b':
					if (strlen(trim( $numIdentificador )) == 10) {
						return true;
					} else {
						return false;
					}
					break;
				default:
					return false;
					break;
			}
		} else {
			return false;
		}
	}

	/**
	* Verifica el identificador este compuesto por números *
	*
	* @access public
	* @param string $numIdentificador cadena del número de identificador del animal.
	* @param string $especieAnimal cadena del tipo de especie del animal.
	* @return boolean
	*/
	public function validaIdentificadorNumerico($numIdentificador = null)
	{
		if (!is_null( $numIdentificador ) || trim( $numIdentificador ) != "") {
			if (ctype_digit( $numIdentificador )) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	* Verifica el identificador este dentro del rango válido de los códigos de identificadores *
	*
	* @access public
	* @param string $numIdentificador cadena del número de identificador del animal.
	* @param string $especieAnimal cadena del tipo de especie del animal.
	* @return boolean
	*/
	public function validaCodigoIdentificador($numIdentificador = null,$especieAnimal = 'b')
	{
		if (!is_null( $numIdentificador ) || trim( $numIdentificador ) != "") {
			switch ($especieAnimal) {
				case 'b':
					if (MOVILIZA_IDENTIFICADOR_ENGORDA) {
						$codigos = [
							"00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17",
							"18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","35","50"
						];
					} else {
						$codigos = [
							"00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17",
							"18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","35"
						];
					}
					$codigoArete = substr( $numIdentificador,0,2 );
					if (in_array( $codigoArete,$codigos )) {
						return true;
					} else {
						return false;
					}
					break;
				default:
					return false;
					break;
			}
		} else {
			return false;
		}
	}

	/**
	* Obtiene el código del motivo de baja del identificador *
	*
	* @access public
	* @param integer $cveMotivoBaja número identificador del motivo de baja.
	* @return integer
	*/
	public function getMotivoBaja($cveMotivoBaja)
	{
		switch ($cveMotivoBaja) {
            case 1:
                // MUERTE EN PREDIO
                return 3;
                break;
            case 2:
                // MUERTE POR SACRIFICIO
                return 9;
                break;
            case 3:
                // EXPORTACIÓN
                return 21;
                break;
            case 4:
                // ROBO
                return 4;
                break;
            case 5:
                // CANCELADO
                return 22;
                break;
            case 6:
                // INCENDIO
                return 34;
                break;
            case 7:
                // INUNDACIÓN
                return 35;
                break;
            case 8:
                // SIN INFORMACIÓN
                return 23;
                break;
            case 9:
                // EN DESTRUCCIÓN
                return 36;
                break;
            case 10:
                // ROBO ANIMAL
                return 37;
                break;
            case 11:
                // EXTRAVIADO POR TÉCNICO
                return 38;
                break;
            case 12:
                // PROBLEMA SANITARIO
                return 33;
                break;
            case 13:
                // PROBLEMA LEGAL
                return 29;
                break;
            default:
            	// DESCONOCIDO
            	return 99;
            	break;
        }
	}

	/**
	* Calcula la edad del animal en meses *
	*
	* @access public
	* @param integer $mesNac número del mes de nacimiento del animal.
	* @param integer $yearNac número del año de nacimiento del animal.
	* @param string $fecha cadena con la fecha de baja o de movilización para calcular hasta ese día.
	* @return string $edad
	*/
	public function getEdadMeses($mesNac,$yearNac,$fecha = NULL)
	{
		if (is_null( $fecha )) {
	        date_default_timezone_set( 'America/Mexico_City' );
	        $date       = getdate();
	        $mesActual  = $date['mon'];
	        $anioActual = $date['year'];
	    } else {
	    	if ($fecha != '0000-00-00') {
		        $fechaMov   = explode('-',$fecha);
		        $mesActual  = $fechaMov[1];
		        $anioActual = $fechaMov[0];
		    } else {
		    	date_default_timezone_set( 'America/Mexico_City' );
	        	$date       = getdate();
	        	$mesActual  = $date['mon'];
	        	$anioActual = $date['year'];
		    }
	    }
	    if ($mesNac == NULL && $yearNac == NULL) {
	        $edad = "-";
	    } else {
	        if (strlen( $yearNac ) == 2) {
	            if ($yearNac >= '90' && $yearNac <= '99') {
	                $yearNac = '19' . substr( $yearNac,0,2 );
	            } else if ($yearNac >= '00' && $yearNac <= substr( $anioActual,2,2 )) {
	                $yearNac = '20' . substr( $yearNac,0,2 );
	            } else {
	                $yearNac = 'fueraRango';
	            }
	        } else {
	            if ($yearNac <= 1989) {
	                $yearNac = 'fueraRango';
	            }
	        }
	        if ($yearNac > $anioActual) {
	            $yearNac = 'fueraRango';
	        }
	        if ($yearNac != 'fueraRango') {
	            $edad = (($anioActual - $yearNac) * 12) - ($mesNac - $mesActual);
	        } else {
	          $edad = 'Inválida';
	        }
	    }
	    return $edad;
	}

	/**
	* Obtiene los datos generales de un identificador en base a la tabla de tarjetas de la(s) ventanilla(s) *
	*
	* @access public
	* @param array $request arreglo con la información del predio origen.
	* @param string $numIdentificador cadena del número de identificador del animal.
	* @param string $especieAnimal cadena del tipo de especie del animal.
	* @return array $datos
	*/
	public function evaluaIdentificador($request,$numIdentificador,$especieAnimal = 'b')
	{
		$datos = [];
		try {
			if ($this->validaLongitudIdentificador($numIdentificador,$especieAnimal)) {
				if ($this->validaIdentificadorNumerico($numIdentificador)) {
					if ($this->validaCodigoIdentificador($numIdentificador,$especieAnimal)) {
						$centrosEdo = $this->getCentrosEdo($this->_cveEdo);
						if (!empty( $centrosEdo )) {
							foreach ($centrosEdo as $idCentro) {
								$tablaTarjetas = $this->getNombreTablaTarjetas($idCentro,$especieAnimal);
								if (!empty( $tablaTarjetas )) {
									switch ($especieAnimal) {
										case 'b':
										case 'o':
										case 'c':
											$sql = "SELECT no_arete,id_upp,sexo,m_nac,a_nac,estatus,f_baja,motivo_baja,rastro_tif,
													tipo,confirmar
													FROM %s 
													WHERE no_arete = ?";
											$sql = sprintf($sql,$tablaTarjetas);
											$query = $this->executeStmt($this->connectionToSiniiga(),$sql,[$numIdentificador]);
										    if (!empty( $query )) {
												if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
													$datos = [
														"error"            => false,
														"numIdentificador" => $row['no_arete'],
														"info"             => [
		                                                    "edad" => $this->getEdadMeses($row['m_nac'],$row['a_nac']),
		                                                    "sexo" => intval( $row['sexo'] )
		                                                ],
														"predio"           => $row['id_upp'],
													    "estatus"          => $row['estatus'],
													    "fechaBaja"        => $row['f_baja'],
													    "motivoBaja"       => $row['motivo_baja'],
													    "muerte"           => $row['motivo_baja'],
													    "claveRastro"      => $row['rastro_tif'],
													    "tipo"             => $row['tipo'],
													    "confirmar"        => $row['confirmar'],
													    "tablaTarjetas"    => $tablaTarjetas,
													    "pertenencia"      => (strcmp( $row['id_upp'],$request['predio'] ) == 0 ? 1 : 0)
													];
												}
											}
											break;
										case 'a':
											$sql = "SELECT no_arete,id_upp,DATE_FORMAT(f_areta,'%d-%m-%Y') AS fechaAretado,estatus,f_baja,motivo_baja 
													FROM %s
													WHERE no_arete = ?";
											$sql = sprintf($sql,$tablaTarjetas);
											$query = $this->executeStmt($this->connectionToSiniiga(),$sql,[$numIdentificador]);
										    if (!empty( $query )) {
												if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
													$datos = [
														"error"            => false,
														"numIdentificador" => $row['no_arete'],
														"info"             => [],
														"predio"           => $row['id_upp'],
													    "estatus"          => $row['estatus'],
													    "fechaBaja"        => $row['f_baja'],
													    "motivoBaja"       => $row['motivo_baja'],
													    "tablaTarjetas"    => $tablaTarjetas,
													    "pertenencia"      => (strcmp( $row['id_upp'],$request['predio'] ) == 0 ? 1 : 0)
													];
												}
											}
											break;
									}
								} else {
									throw new Exception("Tabla de identificadores inválida.");
								}
							}
							if (empty( $datos )) {
								$datos = $this->evaluaIdentificadorSistema($request,$numIdentificador,$especieAnimal,$centrosEdo);
							}
						} else {
							throw new Exception("Ventanillas autorizadas inválidas.");
						}
					} else {
						$datos = [
							"error"        => true,
							"codigoMotivo" => 40
						];
					}
				} else {
					$datos = [
						"error"        => true,
						"codigoMotivo" => 42
					];
				}
			} else {
				$datos = [
					"error"        => true,
					"codigoMotivo" => 43
				];
			}
		} catch (Exception $e) {
			error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$this->setErrorMsg($e->getMessage() . "|" . __METHOD__);
		}
		return $datos;
	}

	/**
	* Obtiene los datos generales de un identificador buscando en todas las tablas de tarjetas *
	*
	* @access public
	* @param array $request arreglo con la información del predio origen.
	* @param string $numIdentificador cadena del número de identificador del animal.
	* @param string $especieAnimal cadena del tipo de especie del animal.
	* @param array $centrosEdo arreglo con las ventanillas del estado revisadas.
	* @return array $datos
	*/
	public function evaluaIdentificadorSistema($request,$numIdentificador,$especieAnimal,$centrosEdo)
	{
		$datos = [];
		foreach ($centrosEdo as $item) {
			unset($this->_centros[$item]);
		}
		foreach ($this->_centros as $centro) {
			switch ($especieAnimal) {
				case 'b':
				case 'o':
				case 'c':
					$sql = "SELECT no_arete,id_upp,sexo,m_nac,a_nac,estatus,f_baja,motivo_baja,rastro_tif,tipo,confirmar
							FROM %s 
							WHERE no_arete = ?";
					$sql = sprintf($sql,$this->getNombreTablaTarjetas($centro,$especieAnimal));
					$query = $this->executeStmt($this->connectionToSiniiga(),$sql,[$numIdentificador]);
				    if (!empty( $query )) {
						if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
							$datos = [
								"error"            => false,
								"numIdentificador" => $row['no_arete'],
								"info"             => [
                                    "edad" => $this->getEdadMeses($row['m_nac'],$row['a_nac']),
                                    "sexo" => intval( $row['sexo'] )
                                ],
								"predio"           => $row['id_upp'],
							    "estatus"          => $row['estatus'],
							    "fechaBaja"        => $row['f_baja'],
							    "motivoBaja"       => $row['motivo_baja'],
							    "muerte"           => $row['motivo_baja'],
							    "claveRastro"      => $row['rastro_tif'],
							    "tipo"             => $row['tipo'],
							    "confirmar"        => $row['confirmar'],
							    "tablaTarjetas"    => $tablaTarjetas,
							    "pertenencia"      => (strcmp( $row['id_upp'],$request['predio'] ) == 0 ? 1 : 0)
							];
							break;
						}
					}
					break;
				case 'a':
					$sql = "SELECT no_arete,id_upp,estatus 
							FROM %s 
							WHERE no_arete = ?";
					$sql = sprintf($sql,$this->getTablaTarjetas($centro,$especieAnimal));
					$query = $this->executeStmt($this->connectionToSiniiga(),$sql,[$numIdentificador]);
				    if (!empty( $query )) {
						if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
							$datos = [
								"error"            => false,
								"numIdentificador" => $row['no_arete'],
								"info"             => [],
								"predio"           => $row['id_upp'],
							    "estatus"          => $row['estatus'],
							    "tablaTarjetas"    => $tablaTarjetas,
							    "pertenencia"      => (strcmp( $row['id_upp'],$request['predio'] ) == 0 ? 1 : 0)
							];
						}
					}
					break;
			}
		}
		if (empty( $datos )) {
			$datos = [
				"numIdentificador" => $numIdentificador,
				"pertenencia"      => 2
			];
		}
		return $datos;
	}

	/**
	* Verifica el identificador si cuenta con una movilización dentro de REEMO *
	*
	* @access public
	* @param string $numIdentificador cadena del número de identificador del animal
	* @return boolean
	*/
	public function validaIdentificadorSistema($numIdentificador)
	{
		$movAreteEstado = false;
	    // Se verifica si el arete se ha movilizado dentro del estado firmado *
	    $sql = "SELECT md.no_arete 
	            FROM movilizacion_desc md,movilizacion mov 
	            WHERE (mov.estatus = 2 OR mov.estatus = 3) AND md.no_arete = ? 
	              AND md.folio = mov.folio 
	            LIMIT 1";
	    $query = $this->executeStmt($this->connectionToReemo(str_pad( $this->_cveEdo,2,'0',STR_PAD_LEFT )),$sql,[$numIdentificador]);
		if (!empty( $query )) {
	    	if ($query->rowCount() > 0) {
	            $movAreteEstado = true;
	        } else {
	            $movAreteEstado = false;
	        }
		} else {
			$movAreteEstado = false;
		}
	    //
	    if ($movAreteEstado) {
	        return true;
	    } else {
	        // Se verifica si el arete se ha movilizado fuera del estado firmado *
	        $cuan = 0;
	        $sql = "SELECT id_uppdno 
	                FROM tarjetas_hist 
	                WHERE no_arete = ? AND f_alta <> '0000-00-00 00:00:00'
	                ORDER BY f_alta DESC
	                LIMIT 1";
	        $query = $this->executeStmt($this->connectionToSiniiga(),$sql,[$numIdentificador]);
			if (!empty( $query )) {
				$cuan = $query->rowCount();
			}
	        if ($cuan > 0) {
	        	$row = $query->fetch(PDO::FETCH_ASSOC);
				if (!empty( $row )) {
		            $idEdo = substr( $row['id_uppdno'],0,2 );
		            $idEdo = intval( $idEdo );
		            if (strlen(trim( $row['id_uppdno'] )) == 12 && $idEdo >= 1 && $idEdo <= 32) {
	                    $sql = "SELECT md.no_arete 
	                            FROM movilizacion_desc md,movilizacion mov 
	                            WHERE (mov.estatus = 2 OR mov.estatus = 3) AND md.no_arete = ? 
	                              AND md.folio = mov.folio 
	                            LIMIT 1";
	                    $query = $this->executeStmt($this->connectionToReemo($idEdo),$sql,[$numIdentificador]);
						if (!empty( $query )) {
	                        if ($query->rowCount() > 0) {
	                            return true;
	                        } else {
	                            return false;
	                        }
	                    } else {
	                        return false;
	                    }
		            } else {
		                return false;
		            }
		        }
	        } else {
	            return false;
	        }
	    }
	}

	/**
	* Verifica el estatus de un identificador *
	*
	* @access public
	* @param array $identificador arreglo con la información del identificador del animal.
	* @param string $especieAnimal cadena del tipo de especie del animal.
	* @return array $datos
	*/
	public function validaEstatusIdentificador($identificador,$especieAnimal = 'b')
	{
		$datos = [];
		if (!empty( $identificador )) {
			switch ($especieAnimal) {
				case 'b':
					switch ($identificador['estatus']) {
						case 0: // Baja *
							$datos['calificacion'] = 0; // No se puede mover
			            	$datos['codigoMotivo'] = $this->getMotivoBaja($identificador['motivoBaja']);
			            	$datos['movilizar']    = false;
							break;
						case 1: // Vigente *
			                $datos['calificacion'] = 1; // Se puede mover
			                $datos['codigoMotivo'] = 0;
			                $datos['movilizar']    = true;
							break;
						case 2: // En Producción - almacén SINIIGA *
			                $datos['calificacion'] = 0; // No se puede mover
			                $datos['codigoMotivo'] = 12;
			                $datos['movilizar']    = false;
							break;
						case 3: // Ventanilla Autorizada SINIIGA *
			                $datos['calificacion'] = 0; // No se puede mover
			                $datos['codigoMotivo'] = 13;
			                $datos['movilizar']    = false;
							break;
						case 4: // Ventanilla Local SINIIGA *
			                $datos['calificacion'] = 0; // No se puede mover
			                $datos['codigoMotivo'] = 14;
			                $datos['movilizar']    = true;
							break;
						case 5: // Con Técnico SINIIGA para colocación *
			                $datos['calificacion'] = 1; // Se puede mover
			                $datos['codigoMotivo'] = 15;
			                $datos['movilizar']    = true;
							break;
						case 6: // Localizar o Pendiente SINIIGA *
			                $datos['calificacion'] = 1; // Se puede mover
			                $datos['codigoMotivo'] = 16;
			                $datos['movilizar']    = true;
							break;
						case 7: // Sin Información 2003 - 2007 *
			                $datos['calificacion'] = 1; // Se puede mover
			                $datos['codigoMotivo'] = 17;
			                $datos['movilizar']    = true;
							break;
						default:
							$datos['calificacion'] = 0; // No se puede mover
			                $datos['codigoMotivo'] = 99;
			                $datos['movilizar']    = false;
							break;
					}
					break;
				default:
					break;
			}
		}
		return $datos;
	}
}