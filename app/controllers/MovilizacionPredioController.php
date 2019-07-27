<?php
/**
* Controlador del proceso de movilización a otro predio UPP/PSG/PG * 
*
* @version 1.0.0 Oct-18
*/

require_once '../init.php';

class MovilizacionPredioController extends EndPointController
{

    private $_cveEdo;
    private $_userId;
    private $_tipoPredioOrigen;
    private $_tipoPredioDestino;

    public function setCveEdo($value)
    {
        $this->_cveEdo = $value;
    }

    public function setUserId($value)
    {
        $this->_userId = $value;
    }

	public function index()
	{
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                return self::processRequest();
            } else {
                throw new Exception("Método no permitido. Acceso denegado.");
            }
        } catch (Exception $e) {
            error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
            $rsrc = [
                "calificacion" => 0,
                "motivo"       => $e->getMessage()
            ];
        }
        echo json_encode( $rsrc );
	}

	/**
     * Realiza la validación del predio origen y solicita la información a la BD *
     *
     * @access public
     * @param array $request
     * @return array $rsrc
     */
    public function evaluateOrigen($request)
    {   
        $rsrc = [];
    	try {
            if (isset( $request['origen'] )) {
                if (strlen(trim( $request['origen'] )) == 12) {
                    if (substr(strtoupper( $request['origen'] ),0,2) == $this->_cveEdo) {
                        switch (substr(strtoupper( $request['origen'] ),9,2)) {
                            case 'PG':
                                $this->_tipoPredioOrigen = "PG";
                                $objPg      = new Pg($this->_cveEdo);
                                $objSanidad = new Sanidad($this->_cveEdo);
                                $rsrcPredio = $objPg->getPgInfo($request['origen']);
                                $rsrcCuaren = $objSanidad->verificarCuarentenaPredio($request['origen']);
                                $rsrcZona   = $objSanidad->verificaZonaSanitaria($request['origen']);
                                $rsrc       = array_merge($rsrcPredio,$rsrcZona,$rsrcCuaren);
                                break;
                            default:
                                switch (substr(strtoupper( $request['origen'] ),9,1)) {
                                    case 'P':
                                        $this->_tipoPredioOrigen = "PSG";
                                        $objPsg     = new Psg($this->_cveEdo);
                                        $objSanidad = new Sanidad($this->_cveEdo);
                                        $rsrcPredio = $objPsg->getPsgInfo($request['origen']);
                                        $rsrcCuaren = $objSanidad->verificarCuarentenaPredio($request['origen']);
                                        $rsrcZona   = $objSanidad->verificaZonaSanitaria($request['origen']);
                                        $rsrc       = array_merge($rsrcPredio,$rsrcZona,$rsrcCuaren);
                                        break;
                                    default:
                                        $this->_tipoPredioOrigen = "UPP";
                                        $objUpp     = new Upp($this->_cveEdo);
                                        $objSanidad = new Sanidad($this->_cveEdo);
                                        $rsrcPredio = $objUpp->getUppInfo($request['origen']);
                                        $rsrcCuaren = $objSanidad->verificarCuarentenaPredio($request['origen']);
                                        $rsrcZona   = $objSanidad->verificaZonaSanitaria($request['origen']);
                                        $rsrc       = array_merge($rsrcPredio,$rsrcZona,$rsrcCuaren);
                                        break;
                                }
                                break;
                        }
                    } else {
                        throw new Exception("Clave predio no pertenece a su estado.");
                    }
                } else {
                    throw new Exception("Clave predio no cumple la longitud de 12 caractéres.");
                }
            } else {
                throw new Exception("No se cuenta con el parámetro origen.");
            }
        } catch (Exception $e) {
            $rsrc = [
                "predio"       => $request['origen'],
                "propietario"  => '',
                "estatus"      => 3,
                "calificacion" => $e->getMessage()
            ];
        }
        return $rsrc;
    }

    /**
     * Realiza la validación del predio destino y solicita la información a la BD *
     *
     * @access public
     * @param array $request
     * @return array $rsrc
     */
    public function evaluateDestino($request)
    {   
        $rsrc = [];
        try {
            if (isset( $request['destino'] )) {
                if (strlen(trim( $request['destino'] )) == 12) {
                    switch (substr(strtoupper( $request['destino'] ),9,2)) {
                        case 'PG':
                            $this->_tipoPredioDestino = "PG";
                            $objPg      = new Pg($this->_cveEdo);
                            $objSanidad = new Sanidad($this->_cveEdo);
                            $rsrcPredio = $objPg->getPgInfo($request['destino']);
                            $rsrcCuaren = $objSanidad->verificarCuarentenaPredio($request['destino']);
                            $rsrcZona   = $objSanidad->verificaZonaSanitaria($request['destino']);
                            $rsrc       = array_merge($rsrcPredio,$rsrcZona,$rsrcCuaren);
                            break;
                        default:
                            switch (substr(strtoupper( $request['destino'] ),9,1)) {
                                case 'P':
                                    $this->_tipoPredioDestino = "PSG";
                                    $objPsg     = new Psg($this->_cveEdo);
                                    $objSanidad = new Sanidad($this->_cveEdo);
                                    $rsrcPredio = $objPsg->getPsgInfo($request['destino']);
                                    $rsrcCuaren = $objSanidad->verificarCuarentenaPredio($request['destino']);
                                    $rsrcZona   = $objSanidad->verificaZonaSanitaria($request['destino']);
                                    $rsrc       = array_merge($rsrcPredio,$rsrcZona,$rsrcCuaren);
                                    break;
                                default:
                                    $this->_tipoPredioDestino = "UPP";
                                    $objUpp     = new Upp($this->_cveEdo);
                                    $objSanidad = new Sanidad($this->_cveEdo);
                                    $rsrcPredio = $objUpp->getUppInfo($request['destino']);
                                    $rsrcCuaren = $objSanidad->verificarCuarentenaPredio($request['destino']);
                                    $rsrcZona   = $objSanidad->verificaZonaSanitaria($request['destino']);
                                    $rsrc       = array_merge($rsrcPredio,$rsrcZona,$rsrcCuaren);
                                    break;
                            }
                            break;
                    }
                } else {
                    throw new Exception("Clave predio no cumple la longitud de 12 caractéres.");
                }
            } else {
                throw new Exception("No se cuenta con el parámetro destino.");
            }
        } catch (Exception $e) {
            $rsrc = [
                "predio"       => $request['destino'],
                "propietario"  => '',
                "estatus"      => 3,
                "calificacion" => $e->getMessage()
            ];
        }
        return $rsrc;
    }

    /**
     * Realiza la evaluación del origen vs destino vs identificadores *
     *
     * @access public
     * @param array $request
     * @return array $rsrc
     */
    public function evaluateOrigenDestinoIdentificadores($request)
    {   
        $result = [];
        try {
            $result['origen']  = $this->evaluateOrigen($request);
            $result['destino'] = $this->evaluateDestino($request);
            if ($result['origen']['predio'] != $result['destino']['predio']) {
                if ($result['origen']['estatus'] == 1) {
                    if ($result['destino']['estatus'] == 1) {
                        $result['identificadores'] = $this->evaluateIdentificadores($request,$result['origen'],$result['destino']);
                        $result['movilizacion'] = [
                            "success" => true
                        ];
                    } else {
                        throw new Exception("Destino - " . $result['destino']['calificacion']);
                    }
                } else {
                    throw new Exception("Origen - " . $result['origen']['calificacion']);
                }              
            } else {
                throw new Exception("Predios origen y destino iguales.");
            }
        } catch (Exception $e) {
            $result['movilizacion'] = [
                "success"      => false,
                "calificacion" => 0,
                "motivo"       => $e->getMessage()
            ];
        }
        return $result;
    }

    /**
     * Realiza la evaluación de los predios si cuentan con cuarentena activa *
     *
     * @access public
     * @param string $cvePredioOrigen
     * @param string $cvePredioDestino
     * @return boolean
     */
    public function evaluateCuarentenasPredios($predioOrigen,$predioDestino)
    {
        if ($predioOrigen == true && $predioDestino == false) {
            $objSanidad = new Sanidad($this->_cveEdo);
            if ($objSanidad->verificaCorralAutorizado($predioDestino)) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Realiza la evaluación de los identificadores *
     *
     * @access public
     * @param array $request
     * @return array $rsrc
     */
    public function evaluateIdentificadores($request,$resultOrigen,$resultDestino)
    {   
        $flagRepetido           = false;
        $result                 = [];
        $identificadoresValid   = [];
        $identificadoresIndices = [];
        try {
            if (isset( $request['identificadores'] )) {
                if (!empty( $request['identificadores'] )) {
                    $identificadores = explode(",",$request['identificadores']);
                    if (!empty( $identificadores )) {
                        $objIdentificador = new Identificador($this->_cveEdo);
                        $objSanidad       = new Sanidad($this->_cveEdo);
                        foreach ($identificadores as $item) {
                            $calificacion = null;
                            $codigoMotivo = null;
                            $movilizar    = false;
                            if (!$this->evaluateCuarentenasPredios($resultOrigen['cuarentena'],$resultDestino['cuarentena'])) {
                                if ($objSanidad->validaZonaSanitaria($resultOrigen['zonaSanitaria'],$resultDestino['zonaSanitaria'])) {
                                    $rsrc = $objIdentificador->evaluaIdentificador($resultOrigen,$item);
                                    if ($rsrc['error'] == false) {
                                        switch ($rsrc['pertenencia']) {
                                            case 0: // No pertenece al origen el identificador *
                                                if (MOVILIZA_OTRO_PREDIO_FEATURE) {
                                                    $predioActual = $objSanidad->verificarCuarentenaPredio($rsrc['predio']);
                                                    if (!$this->evaluateCuarentenasPredios($predioActual['cuarentena'],
                                                        $resultDestino['cuarentena'])) {
                                                        if ($objSanidad->validaZonaSanitaria($resultOrigen['zonaSanitaria'],
                                                            $resultDestino['zonaSanitaria'],$rsrc['predio'],$rsrc['pertenencia'])) {
                                                            if (!$objIdentificador->validaIdentificadorSistema($item)) {
                                                                $resultItem   = $objIdentificador->validaEstatusIdentificador($rsrc);
                                                                $calificacion = $resultItem['calificacion'];
                                                                $codigoMotivo = ($resultItem['codigoMotivo'] == 0 ? 2 : 
                                                                $resultItem['codigoMotivo']);
                                                                $movilizar    = $resultItem['movilizar'];
                                                            } else {
                                                                $calificacion = 0; // No se puede mover
                                                                $codigoMotivo = 19;
                                                                $movilizar    = false;
                                                            }
                                                        } else {
                                                            $calificacion = 0; // No se puede mover
                                                            $codigoMotivo = 26;
                                                            $movilizar    = false;
                                                        }
                                                    } else {
                                                        $calificacion = 0; // No se puede mover
                                                        $codigoMotivo = 5;
                                                        $movilizar    = false;
                                                    }
                                                } else {
                                                    $calificacion = 0; // No se puede mover
                                                    $codigoMotivo = 27;
                                                    $movilizar    = false;
                                                }
                                                break;
                                            case 1: // Pertenece al origen el identificador *
                                                /*if (isset( $request['foliosFlejes'] )) {
                                                    if (!empty( $request['foliosFlejes'] )) {
                                                        $flejes;
                                                    } else {
                                                        $calificacion = 'No se puede mover - identificador requiere folios de flejes';
                                                        $movilizar    = false;
                                                    }
                                                } else {
                                                    $flejes;
                                                }*/
                                                $resultItem   = $objIdentificador->validaEstatusIdentificador($rsrc);
                                                $calificacion = $resultItem['calificacion'];
                                                $codigoMotivo = $resultItem['codigoMotivo'];
                                                $movilizar    = $resultItem['movilizar'];
                                                break;
                                            case 2: // No registrado en la base de datos *
                                                $calificacion = 0; // No se puede mover
                                                $codigoMotivo = 1;
                                                $movilizar    = false;
                                                break;
                                        }
                                        $identificadoresValid[] = $item;
                                    } else {
                                        $calificacion = 0; // No se puede mover
                                        $codigoMotivo = $rsrc['codigoMotivo'];
                                        $movilizar    = false;
                                    }
                                } else {
                                    $calificacion = 0; // No se puede mover
                                    $codigoMotivo = 10;
                                    $movilizar    = false;
                                }
                            } else {
                                $calificacion = 0; // No se puede mover
                                $codigoMotivo = 5;
                                $movilizar    = false;
                            }
                            $result[] = [
                                "identificador" => $item,
                                "info"          => $rsrc['info'],
                                "tipo"          => $rsrc['tipo'],
                                "calificacion"  => $calificacion,
                                "codigoMotivo"  => $codigoMotivo,
                                "movilizar"     => $movilizar 
                            ];
                        }
                    } else {
                        throw new Exception("No se cuenta con información de identificadores");
                    }
                    if (!empty( $result )) {
                        // Rutina para buscar identificadores repetidos *
                        if (is_array( $identificadoresValid )) {
                            $arrVecesRepetidos = @array_count_values( $identificadoresValid );
                        }
                        $indice = 0;
                        while (list($llave,$valor) = each($arrVecesRepetidos)) {
                            if ($valor > 1) {
                                $flagRepetido  = true;
                                // Recorre arreglo de identificadores *
                                for ($k = 1; $k <= sizeof( $identificadoresValid ); $k++) {
                                    if ($identificadoresValid[$k] == $llave) {
                                        $indice++;
                                        $identificadoresIndices[$indice] = $k;
                                    }
                                }                      
                            }
                        }
                        if ($flagRepetido) {
                            $tmpIdentificadores = [];
                            foreach ($result as $key => $item) {
                                if (in_array( $key,$identificadoresIndices )) {
                                    $tmpIdentificadores[] = [
                                        "identificador" => $item['identificador'],
                                        "calificacion"  => 0,
                                        "codigoMotivo"  => 6,
                                        "movilizar"     => false 
                                    ];
                                } else {
                                    $tmpIdentificadores[] = [
                                        "identificador" => $item['identificador'],
                                        "info"          => $item['info'],
                                        "calificacion"  => $item['calificacion'],
                                        "codigoMotivo"  => $item['codigoMotivo'],
                                        "movilizar"     => $item['movilizar'] 
                                    ];                                
                                }
                            }
                            $result = [];
                            $result = $tmpIdentificadores;
                        }
                    }
                } else {
                    throw new Exception("Identificadores inválidos o vacíos");
                }
            } else {
                throw new Exception("No se cuenta con el parámetro de identificadores");
            }
        } catch (Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }

    /**
     * Realiza la evaluación de la movilización *
     *
     * @access public
     * @param array $request
     * @return array $rsrc
     */
    public function evaluateMovilizacion($request)
    {   
        $rsrc            = [];
        $identificadores = [];
        $formato         = '';
        $numIdentificadorRechazo  = 0;
        $calificacionNoMoviliza   = '';
        $flagContinuaMovilizacion = true;
        try {
            if (isset( $request['motivo'] )) {
                if ($request['motivo'] != "") {
                    if (ctype_digit( $request['motivo'] )) {
                        if ( $request['motivo'] > 0 && $request['motivo'] <= sizeof( $this->getMotivoMovilizacion() )) {
                            switch (substr( $result['destino']['predio'],9,3 )) {
                                case 'P03':
                                    if ($request['motivo'] == 11) {
                                        $formato = 4;
                                    }
                                    break;
                                case 'P04':
                                    if ($request['motivo'] == 3) {
                                        $formato = 5;
                                    }
                                    break;
                                case 'P08':
                                    if ($request['motivo'] == 12) {
                                        $formato = 6;
                                    }
                                    break;
                                default:
                                    $formato = 1;
                                    break;
                            }
                        } else {
                            throw new Exception("Motivo no se encuentra en catálogo");
                        }
                    } else {
                        throw new Exception("Motivo inválido");
                    }
                } else {
                    throw new Exception("No se cuenta con información del motivo");
                }
            } else {
                throw new Exception("No se cuenta con el parámetro de motivo");
            }
            $rsrc = $this->evaluateOrigenDestinoIdentificadores($request);
            if (isset( $rsrc['movilizacion']['success'] ) && $rsrc['movilizacion']['success'] == true) {
                if (is_array( $rsrc['identificadores'] )) {
                    foreach ($rsrc['identificadores'] as $item) {
                        if ($item['movilizar'] == false) {
                            $flagContinuaMovilizacion = false;
                            $numIdentificadorRechazo++;
                        }
                        if ($formato == 5) {
                            if ($item['tipo'] == 21) {
                                $calificacionNoMoviliza   = $item['identificador'] . " con problemas: Identficador de consumo nacional";
                                $flagContinuaMovilizacion = false;
                            }
                        }
                        $identificador = [
                            "identificador" => $item['identificador'],
                            "info"          => $item['info'],
                            "calificacion"  => $item['calificacion'],
                            "codigoMotivo"  => $item['codigoMotivo']
                        ];
                        $identificadores[] = $identificador;
                    }
                    if ($flagContinuaMovilizacion) {
                        $rsrc['movilizacion'] = [
                            "calificacion" => 1
                        ];
                    } else {
                        throw new Exception($numIdentificadorRechazo . " " . ($numIdentificadorRechazo > 1 ? "Identificadores rechazados":
                        "Identificador rechazado"));
                    }
                } else {
                    throw new Exception($rsrc['identificadores']);
                }
            } else {
                throw new Exception($rsrc['movilizacion']['motivo']);
            }
        } catch (Exception $e) {
            $rsrc["movilizacion"] = [
                "calificacion" => 0,
                "motivo"       => $e->getMessage()
            ];
        }
        $objLog = new SysLog($this->_cveEdo);
        $dataMov = [
            "tipo_movil"      => 1,
            "motivo"          => intval( $request['motivo'] ),
            "upp_origen"      => $request['origen'],
            "upp_destino"     => $request['destino'],
            "cve_edo_destino" => substr( $request['destino'],0,2 ),
            "cve_mun_destino" => substr( $request['destino'],2,3 ),
            "id_rastro"       => null,
            "q_alta"          => $this->_userId
        ];
        //$objLog->procesaLogValidacionesIdentificadores($dataMov,$identificadores);
        if (!DEBUG_MODE) {
            if (isset( $rsrc['origen'] )) {
                $result['origen']['predio']      = $rsrc['origen']['predio'];
                $result['origen']['propietario'] = $rsrc['origen']['propietario'];
                $result['origen']['califcacion'] = $rsrc['origen']['calificacion'];
            }
            if (isset( $rsrc['destino'] )) {
                $result['destino']['predio']       = $rsrc['destino']['predio'];
                $result['destino']['propietario']  = $rsrc['destino']['propietario'];
                $result['destino']['calificacion'] = $rsrc['destino']['calificacion'];
            }
            if (isset( $rsrc['identificadores'] )) {
                $result['identificadores'] = $identificadores;
            }
            if (isset( $rsrc['movilizacion'] )) {
                $result['movilizacion'] = $rsrc['movilizacion'];
            }
        } else {
            $result = $rsrc;
        }
        return $this->successResponse(200,$result);
    }
}
$objMov = new MovilizacionPredioController();
$objMov->index();