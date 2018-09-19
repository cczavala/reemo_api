<?php
/**
* Controlador del proceso de movilización a rastro * 
*
* @version 1.0.0 Jul-18
*/

require_once '../init.php';

class MovilizacionRastroController extends EndPointController
{

    private $_cveEdo;
    private $_tipoPredioOrigen;

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
     * Realiza la validación del rastro y solicita la información a la BD *
     *
     * @access public
     * @param array $request
     * @return array $rsrc
     */
    public function evaluateDestino($request)
    {   
        $rsrc = [];
        try {
            if (isset( $request['rastro'] )) {
                if (!empty( $request['rastro'] )) {
                    $objRastro = new Rastro($this->_cveEdo);
                    $rsrc      = $objRastro->getRastroInfo($request['rastro']);
                } else {
                    throw new Exception("Clave rastro es inválida o vacía");
                }
            } else {
                throw new Exception("No se cuenta con el parámetro rastro");
            }
        } catch (Exception $e) {
            $rsrc = [
                "clave"        => $request['rastro'],
                "nombre"       => '',
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
            $result['origen'] = $this->evaluateOrigen($request);
            $result['rastro'] = $this->evaluateDestino($request);
            if ($result['origen']['estatus'] == 1) {
                if ($result['rastro']['estatus'] == 1) {
                    $result['identificadores'] = $this->evaluateIdentificadores($request,$result['origen']);
                    $result['movilizacion'] = [
                        "success" => true
                    ];
                } else {
                    throw new Exception($result['rastro']['calificacion']);
                }
            } else {
                throw new Exception("Origen - " . $result['origen']['calificacion']);
            }
        } catch (Exception $e) {
            $result['movilizacion'] = [
                "success"      => false,
                "calificacion" => "No es posible la movilización",
                "motivo"       => $e->getMessage()
            ];
        }
        return $result;
    }

    /**
     * Realiza la evaluación de los identificadores *
     *
     * @access public
     * @param array $request
     * @return array $rsrc
     */
    public function evaluateIdentificadores($request,$resultOrigen)
    {   
        $calificacion           = '';
        $movilizar              = false;
        $flagRepetido           = false;
        $result                 = [];
        $identificadoresValid   = [];
        $identificadoresIndices = [];
        try {
            if (isset( $request['identificadores'] )) {
                if (!empty( $request['identificadores'] )) {
                    $objIdentificador = new Identificador($this->_cveEdo);
                    $identificadores  = explode(",",$request['identificadores']);
                    if (!empty( $identificadores )) {
                        foreach ($identificadores as $item) {
                            if (strlen(trim( $item )) == 10) {
                                if (ctype_digit( $item )) {
                                    $rsrc = $objIdentificador->evaluaIdentificador($resultOrigen,$item);
                                    switch ($rsrc['pertenencia']) {
                                        case 0: // No pertenece al origen el identificador *
                                            if ($rsrc['estatus'] == 1) {
                                                $calificacion = 'No se puede mover - identificador no pertenece al origen';
                                                $movilizar    = false;
                                            } else {
                                                $calificacion = 'No se puede mover - identificador baja';
                                                $movilizar    = false;
                                            }
                                            break;
                                        case 1: // Pertenece al origen el identificador *
                                            if ($rsrc['estatus'] == 1) {
                                                $calificacion = 'Se puede mover';
                                                $movilizar    = true;
                                            } else {
                                                $calificacion = 'No se puede mover - identificador baja';
                                                $movilizar    = false;
                                            }
                                            break;
                                        case 2:
                                            $calificacion = 'No se puede mover - identificador no existe';
                                            $movilizar    = false;
                                            break;
                                    }
                                    $identificadoresValid[] = $item;
                                } else {
                                    $calificacion = 'No se puede mover - identificador inválido';
                                    $movilizar    = false;
                                }
                            } else {
                                $calificacion = 'No se puede mover - identificador no cumple con la longitud de 10 caractéres';
                                $movilizar    = false;
                            }
                            $result[] = [
                                "identificador" => $item,
                                "tipo"          => $rsrc['tipo'],
                                "calificacion"  => $calificacion,
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
                                        "calificacion"  => 'No se puede mover - identificador repetido',
                                        "movilizar"     => false 
                                    ];
                                } else {
                                    $tmpIdentificadores[] = [
                                        "identificador" => $item['identificador'],
                                        "calificacion"  => $item['calificacion'],
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
        $calificacionNoMoviliza   = '';
        $flagContinuaMovilizacion = true;
        try {
            $rsrc = $this->evaluateOrigenDestinoIdentificadores($request);
            if (isset( $rsrc['movilizacion']['success'] ) && $rsrc['movilizacion']['success'] == true) {
                if (is_array( $rsrc['identificadores'] )) {
                    foreach ($rsrc['identificadores'] as $item) {
                        if ($item['movilizar'] == false) {
                            $flagContinuaMovilizacion = false;
                            $calificacionNoMoviliza   = $item['identificador'] . " con problemas: " . $item['calificacion'];
                        }
                        $identificador = [
                            "identificador" => $item['identificador'],
                            "calificacion"  => $item['calificacion']
                        ];
                        $identificadores[] = $identificador;
                    }
                    if ($flagContinuaMovilizacion) {
                        $rsrc['movilizacion'] = [
                            "calificacion" => 'movilizacionOK'
                        ];
                    } else {
                        throw new Exception("Identificador $calificacionNoMoviliza");
                    }
                } else {
                    throw new Exception($rsrc['identificadores']);
                }
            } else {
                throw new Exception($rsrc['movilizacion']['motivo']);
            }
        } catch (Exception $e) {
            $rsrc["movilizacion"] = [
                "calificacion" => "No es posible la movilización",
                "motivo"       => $e->getMessage()
            ];
        }
        if (!DEBUG_MODE) {
            if (isset( $rsrc['origen'] )) {
                $result['origen']['predio']      = $rsrc['origen']['predio'];
                $result['origen']['propietario'] = $rsrc['origen']['propietario'];
                $result['origen']['califcacion'] = $rsrc['origen']['calificacion'];
            }
            if (isset( $rsrc['rastro'] )) {
                $result['rastro']['clave']        = $rsrc['rastro']['clave'];
                $result['rastro']['nombre']       = $rsrc['rastro']['nombre'];
                $result['rastro']['calificacion'] = $rsrc['rastro']['calificacion'];
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
$objMov = new MovilizacionRastroController();
$objMov->index();