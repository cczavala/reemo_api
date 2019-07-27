<?php
/**
* Clase Sanidad
*
* @version 1.0.0 Oct-18
*/

class Sanidad extends Dbconnection
{

	private $_cveEdo;

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
	 * Verifica en la base de datos si un predio cuenta con una cuarentena activa *
	 *
	 * @access public
	 * @param string $cvePredio cadena con la clave del predio UPP/PSG/PG. 
	 * @return array $dato
	 */
	public function verificarCuarentenaPredio($cvePredio)
	{
		$dato = ["cuarentena" => false];
		try {
			$cveEdo = (int) substr( $cvePredio,0,2 );
			if ($cveEdo >= 1 && $cveEdo <= 32) {
		        $sql = "SELECT COUNT(*) AS existe 
		                FROM cuarentena 
		                WHERE id_upp = ? AND (f_fin >= CURDATE() OR f_fin = '0000-00-00' OR f_fin IS NULL)";
		        $query = $this->executeStmt($this->connectionToReemo(str_pad( $cveEdo,2,'0',STR_PAD_LEFT )),$sql,[$cvePredio]);
				if (!empty( $query )) {
			    	if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			    		if ($row['existe'] > 0) {
			    			$dato = ["cuarentena" => true];
			    		}
					}
				} else {
					throw new ErrorException($this->getMsgErrorConnection());
				}
		    }
		} catch (ErrorException $e) {
			error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$this->setErrorMsg($e->getMessage() . "|" . __METHOD__ . "|");
		}
		return $dato;
	}

	/**
	 * Verifica en la base de datos si un predio UPP/PSG/PG es un corral autorizado de engorda *
	 *
	 * @access public
	 * @param string $cvePredio cadena con la clave del predio UPP/PSG/PG. 
	 * @return boolean
	 */
	public function verificaCorralAutorizado($cvePredio)
	{
		try {
	        $sql = "SELECT COUNT(*) AS corralAutorizado 
	                FROM corrales_autorizados 
	                WHERE id_upp = ? AND estatus = 1";
	        $query = $this->executeStmt($this->connectionToMx(),$sql,[$cvePredio]);
			if (!empty( $query )) {
		    	if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
		    		if ($row['corralAutorizado'] >= 1) {
		                return true;
		            } else {
		                return false;
		            }
		        } else {
		        	return false;
		        }
	        } else {
				throw new ErrorException($this->getMsgErrorConnection());
		    }
		} catch (ErrorException $e) {
			error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$this->setErrorMsg($e->getMessage() . "|" . __METHOD__ . "|");
			return false;
		}
	}

	/**
	 * Verifica en las zonas sanitarias de un predio UPP/PSG/PG y las zonas sanitarias municipales *
	 *
	 * @access public
	 * @param string $cvePredio cadena con la clave del predio UPP/PSG/PG. 
	 * @return array
	 */
	public function verificaZonaSanitaria($cvePredio)
	{
		$zonaPredio = null;
		$zonaMun    = null;
		try {
			if (ENABLE_ZONA_SANITARIA_PREDIO) {
				$cveEdo = (int) substr( $cvePredio,0,2 );
				if ($cveEdo >= 1 && $cveEdo <= 32) {
					// Zona sanitaria del predio *
			        $sql = "SELECT zona
			        		FROM upp_zona 
			        		WHERE id_upp = ?";
			        $query = $this->executeStmt($this->connectionToReemo(str_pad( $cveEdo,2,'0',STR_PAD_LEFT )),$sql,[$cvePredio]);
					if (!empty( $query )) {
				    	if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				    		$zonaPredio = $row['zona'];	
						} else {
							$zonaPredio = null;
						}
					} else {
						throw new ErrorException($this->getMsgErrorConnection());
					}
				}
			}
			// Zona sanitaria municipal del predio *
			$sql = "SELECT zona
	        		FROM estados 
	        		WHERE cve_edo = SUBSTR(?,1,2) AND cve_mun = SUBSTR(?,3,3)";
	        $query = $this->executeStmt($this->connectionToMx(),$sql,[$cvePredio,$cvePredio]);
			if (!empty( $query )) {
		    	if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
		    		$zonaMun = $row['zona'];	
				} else {
					$zonaMun = null;
				}
			} else {
				throw new ErrorException($this->getMsgErrorConnection());
			}
			if (!is_null( $zonaPredio ) && strlen(trim( $zonaPredio )) > 0) {
				return ["zonaSanitaria" => $zonaPredio];
			} else if (!is_null( $zonaMun ) && strlen(trim( $zonaMun )) > 0) {
				return ["zonaSanitaria" => $zonaMun];
			} else {
				return ["zonaSanitaria" => null];
			}
		} catch (ErrorException $e) {
			error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$this->setErrorMsg($e->getMessage() . "|" . __METHOD__ . "|");
			return null;
		}
	}

	/**
	 * Valida si es posible movilizar el identificador en base a las zonas sanitarias del origen o del identificador y destino *
	 *
	 * @access public
	 * @param string $zonaOrigen cadena con la zona sanitaria del predio origen.
	 * @param string $zonaDestino cadena con la zona sanitaria del predio destino.
	 * @param string $cvePredioIdentificador cadena con la clave del predio actual del identificador.
	 * @param integer $perteneceAreteUppOrigen valor numérico de la situación de pertenencia del identificador.
	 * @return boolean
	 */
	public function validaZonaSanitaria($zonaOrigen,$zonaDestino,$cvePredioIdentificador = null,$perteneceAreteUppOrigen = 1)
	{
	    switch ($perteneceAreteUppOrigen) {
	    	// CASO 2 El identificador NO pertenece al predio origen y se toma el predio del identificador *
	        // CASO 3 Se tomo el predio del identificador pero el predio es null, vacío ó '000000000000' *
	        case 0:
	            // Se descrimina el predio de la zona origen y se toma el predio del identificador *
	            if (!empty( $cvePredioIdentificador ) && $cvePredioIdentificador != '000000000000') {
	            	if ( ENABLE_ZONA_SANITARIA_PREDIO ) {
		                // Se consulta la zona sanitaria del predio del identificador en la tabla upp_zona *
		                // En caso de no encontrarse en la tabla de zonas se toma la del municipio del predio 
		                // del identificador *
		                $cveEdoArete = substr( $cvePredioIdentificador,0,2 );
	                	$sql = "SELECT zona 
	                            FROM upp_zona 
	                            WHERE id_upp = ?";
	                	$query = $this->executeStmt($this->connectionToReemo($cveEdoArete),$sql,[$cvePredioIdentificador]);
						if (!empty( $query )) {
						   	if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
						   		if (!empty( $row['zona'] )) {
						   			$zonaIdentificador = $row['zona'];
		                        } else {
		                            $zonaIdentificador = null;
		                        }
						   	} else {
						   		$zonaIdentificador = null;
						   	}
						} else {
							//throw new Exception();
						}
					}
                    // Se consulta la zona sanitaria municipal del predio del identificador *
                    $sql = "SELECT zona 
                            FROM estados 
                            WHERE cve_edo = substr(?,1,2) AND cve_mun = substr(?,3,3)";
                    $query = $this->executeStmt($this->connectionToMx(),$sql,[$cvePredioIdentificador,$cvePredioIdentificador]);
					if (!empty( $query )) {
   						if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
   							if (!empty( $row['zona'] )) {
	                            $zonaIdentificador = $row['zona'];
	                        } else {
	                            $zonaIdentificador = null;
	                        }
   						} else {
   							$zonaIdentificador = null;
   						}
   					} else {
   						$zonaIdentificador = null;
   					}
	            } else {
	                $zonaIdentificador = null;
	            }
	            if ($zonaIdentificador == 'A' && $zonaDestino == 'A') {
		            return true;
		        } else if ($zonaIdentificador == 'A' && $zonaDestino == 'B') {
		            return true;
		        } else if ($zonaIdentificador == 'B' && $zonaDestino == 'A') {
		            return false;
		        } else if ($zonaIdentificador == 'B' && $zonaDestino == 'B') {
		            return true;
		        } else if ($zonaIdentificador == 'B' && $zonaDestino == 'C') {
		            return false;
		        } else if ($zonaIdentificador == 'C' && $zonaDestino == 'A') {
		            return false;
		        } else if ($zonaIdentificador == 'C' && $zonaDestino == 'B') {
		            return true;
		        } else if ($zonaIdentificador == 'A' && $zonaDestino == 'C') {
		            return true;
		        } else if ($zonaIdentificador == 'C' && $zonaDestino == 'C') {
		            return true;
		        } else {
		            return false;
		        }
	            break;
	        default:
	        	if ($zonaOrigen == 'A' && $zonaDestino == 'A') {
		            return true;
		        } else if ($zonaOrigen == 'A' && $zonaDestino == 'B') {
		            return true;
		        } else if ($zonaOrigen == 'B' && $zonaDestino == 'A') {
		            return false;
		        } else if ($zonaOrigen == 'B' && $zonaDestino == 'B') {
		            return true;
		        } else if ($zonaOrigen == 'B' && $zonaDestino == 'C') {
		            return false;
		        } else if ($zonaOrigen == 'C' && $zonaDestino == 'A') {
		            return false;
		        } else if ($zonaOrigen == 'C' && $zonaDestino == 'B') {
		            return true;
		        } else if ($zonaOrigen == 'A' && $zonaDestino == 'C') {
		            return true;
		        } else if ($zonaOrigen == 'C' && $zonaDestino == 'C') {
		            return true;
		        } else {
		            return false;
		        }
	        	break;
	    }
	}
}