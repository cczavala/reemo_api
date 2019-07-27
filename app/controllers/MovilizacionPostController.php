<?php
/**
* Controlador del proceso de almacenaje de una movilización del SIGE (Puebla-21-PUE) al REEMO * 
*
* @version 1.0.0 Oct-18
*/

require_once '../init.php';

class MovilizacionPostController extends EndPointController
{

    private $_cveEdo;
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

    /**
     *  Verifica si es válida la petición del servidor *
     *
     * @access public
     * @return mixed
     */
	public function index()
	{
        try {
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                return self::processRequest();
            } else {
                throw new ErrorException("Método no permitido. Acceso denegado.");
            }
        } catch (ErrorException $e) {
            error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
            $rsrc = [
                "calificacion" => 0,
                "mensaje"      => "No es posible almacenar la movilización",
                "motivo"       => $e->getMessage()
            ];
        }
		echo json_encode( $rsrc );
	}

    /**
     *  Verifica y valida la información que recibe de la petición para almacenar una movilización *
     *
     * @access public
     * @param array $request
     * @return json
     */
    public function createMovilizacion($request)
    {   
        $params = [];
        try {
            $objMovilizacion = new Movilizacion($this->_cveEdo);
            foreach ($objMovilizacion->_fillable as $key => $value) {
                switch ($value) {
                    case 'folio':
                        if (empty( $request[$value] ) || $request[$value] == "") {
                            throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " esta vacío o nulo");
                        }
                        $params['data'][] = $request[$value];
                        break;
                    case 'tipoMov':
                        if (!ctype_digit( $request[$value] )) {
                            throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " es un número inválido");
                        }
                        if ($request[$value] < 1 || $request[$value] > 3) {
                            throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no se encuentra en el catálogo.");
                        }
                        $params['data'][] = $request[$value];
                        break;
                    case 'fechaHora':
                        $fechaHora = explode("%20",$request['fechaHora']);
                        if (sizeof( $fechaHora ) == 2) {
                            if ($fechaHora[0] == "") {
                                throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no cuenta con el " . 
                                "valor de fecha.");
                            }
                            if ($fechaHora[1] != "") {
                                $params['data'][] = $fechaHora[0] . " " . $fechaHora[1];
                            } else {
                                throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no cuenta con el " . 
                                "valor de hora.");
                            }
                        } else {
                            throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no cuenta con el " . 
                            "valor de fecha y hora");
                        }
                        break;
                    case 'responsable':
                    case 'centroExpedidor':
                        if (!ctype_digit( $request[$value] )) {
                            throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " es un número inválido");
                        }
                        if ($request[$value] < 0) {
                            throw new Exception("El valor debe ser mayor a 0 para el parámetro " . $objMovilizacion->_fillable[$key]);
                        }
                        $params['data'][] = $request[$value];
                        break;
                    case 'origen':
                        if (strlen(trim( $request[$value] )) == 12) {
                            if (substr(strtoupper( $request[$value] ),0,2) == $this->_cveEdo) {
                                switch (substr(strtoupper( $request[$value] ),9,2)) {
                                    case 'PG':
                                        $objPg      = new Pg($this->_cveEdo);
                                        $rsrcPredio = $objPg->getPgInfo($request[$value]);
                                        break;
                                    default:
                                        switch (substr(strtoupper( $request[$value] ),9,1)) {
                                            case 'P':
                                                $objPsg     = new Psg($this->_cveEdo);
                                                $rsrcPredio = $objPsg->getPsgInfo($request[$value]);
                                                break;
                                            default:
                                                $objUpp     = new Upp($this->_cveEdo);
                                                $rsrcPredio = $objUpp->getUppInfo($request[$value]);
                                                break;
                                        }
                                        break;
                                }
                                if ($rsrcPredio['estatus'] == 1) {
                                    $params['data'][] = $request[$value];
                                } else {
                                    throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . 
                                    " presenta el siguiente error: " . $rsrcPredio['calificacion']);
                                }
                            } else {
                                throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no pertenece a su estado");
                            }
                        } else {
                            throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no cumple con la " . 
                            "longitud de 12 caracteres");
                        }
                        break;
                    case 'destino':
                        $tipoMov = (isset( $request['tipoMov'] ) ? $request['tipoMov'] : 0);
                        switch ($tipoMov) { 
                            case 1:
                                if (strlen(trim( $request[$value] )) == 12) {
                                    switch (substr(strtoupper( $request[$value] ),9,2)) {
                                        case 'PG':
                                            $objPg      = new Pg($this->_cveEdo);
                                            $rsrcPredio = $objPg->getPgInfo($request[$value]);
                                            break;
                                        default:
                                            switch (substr(strtoupper( $request[$value] ),9,1)) {
                                                case 'P':
                                                    $objPsg     = new Psg($this->_cveEdo);
                                                    $rsrcPredio = $objPsg->getPsgInfo($request[$value]);
                                                    break;
                                                default:
                                                    $objUpp     = new Upp($this->_cveEdo);
                                                    $rsrcPredio = $objUpp->getUppInfo($request[$value]);
                                                    break;
                                            }
                                            break;
                                    }
                                    if ($rsrcPredio['estatus'] == 1) {
                                        $params['data'][] = $request[$value];
                                    } else {
                                        throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . 
                                        " presenta el siguiente error: " . $rsrcPredio['calificacion']);
                                    }
                                } else {
                                    throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no cumple con la " . 
                                    "longitud de 12 caracteres.");
                                }
                                break;
                            case 2:
                                if (strlen(trim( $request[$value] )) >= 1 && strlen(trim( $request[$value] )) <= 6) {
                                    $objRastro  = new Rastro($this->_cveEdo);
                                    $rsrcRastro = $objRastro->getRastroInfo($request[$value]);
                                    if ($rsrcRastro['estatus'] == 1) {
                                        // Para el campo upp_destino *
                                        $params['data'][] = strtoupper( $request[$value] );
                                        // Para el campo cve_rastro *
                                        $params['data'][] = strtoupper( $request[$value] );
                                    } else {
                                        throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . 
                                        " presenta el siguiente error: " . $rsrcRastro['calificacion']);
                                    }
                                } else {
                                    throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no cumple con la " . 
                                    "longitud mínima de 1 caracter y máxima de 6 caracteres.");
                                }
                                break;
                            default:
                                throw new Exception("Tipo de movilización desconocido.");
                                break;
                        }
                        break;
                    case 'identificadores':
                        $params['identificadores'] = explode(",",$request['identificadores']);
                        if (!empty( $params['identificadores'] )) {
                            foreach ($params['identificadores'] as $item) {
                                if (strlen(trim( $item )) != 10) {
                                    throw new Exception("Identificador $item no cumple con la longitud de 10 caracteres.");
                                } else {
                                    if (!ctype_digit( $item )) {
                                        throw new Exception("Identificador $item inválido");
                                    }
                                }
                            }
                        } else {
                            throw new Exception("No se cuenta con información de identificadores");
                        }
                        break;
                    case 'estatusMov':
                        if (!ctype_digit( $request[$value] )) {
                            throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " es un número inválido");
                        }
                        if (!$this->getEstatusMov($request[$value])) {
                            throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no se encuentra en el catálogo.");
                        }
                        $params['data'][] = $request[$value];
                        break;
                    case 'tipoTransporte':
                        if (!ctype_digit( $request[$value] )) {
                            throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " es un número inválido");
                        }
                        if (!$this->verificaTipoTransporte($request[$value])) {
                            throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no se encuentra en el catálogo.");
                        }
                        $params['data'][] = $request[$value];
                        break;
                    case 'dictamen':
                        $dictamen = explode(",",$request['dictamen']);
                        if (sizeof( $dictamen ) > 1) {
                            $dictamenValues = [];
                            foreach ($dictamen as $itemDictamen) {
                                $requestDictamen = str_replace("%20"," ",$itemDictamen);
                                $dictamenValue   = explode("%7C",$requestDictamen);
                                if ($this->verificaDictamen(trim( $dictamenValue[0] ))) {
                                    if (trim( $dictamenValue[1] ) != "") {
                                        $dictamenValues[] = $dictamenValue[0] . "|" . $dictamenValue[1];
                                    } else {
                                        throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " con el dictamen " . 
                                        trim( $dictamenValue[0] ) . " se encuentra vacío o nulo.");
                                    }
                                } else {
                                    throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no se encuentra en " . 
                                    "el catálogo.");
                                }
                            }
                            $params['data'][] = (!empty( $dictamenValues ) ? implode( ",",$dictamenValues ) : $dictamenValues);
                        } else {
                            $requestDictamen = str_replace("%20"," ",$request['dictamen']);
                            $dictamenValue   = explode("%7C",$requestDictamen);
                            if ($this->verificaDictamen(trim( $dictamenValue[0] ))) { 
                                if (trim( $dictamenValue[1] ) != "") {
                                    $params['data'][] = $dictamenValue[0] . "|" . $dictamenValue[1];
                                } else {
                                    throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " con el dictamen " . 
                                    trim( $dictamenValue[0] ) . " se encuentra vacío o nulo.");
                                }
                            } else {
                                throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no se encuentra en " . 
                                "el catálogo.");
                            }
                        }
                        break;
                    default:
                        $params['data'][] = $request[$value];
                        break;
                }
            }
            if ($params['data'][5] != $params['data'][6]) {
                $rsrc['movilizacion'] = $objMovilizacion->saveMovilizacion($params,'b');
            } else {
                throw new Exception("Los parámetros origen y destino no pueden ser iguales.");
            }
        } catch (Exception $e) {
            $rsrc['movilizacion'] = [
                "calificacion" => 0,
                "mensaje"      => "No es posible almacenar la movilización.",
                "motivo"       => $e->getMessage()
            ];
        }
        return $this->successResponse(200,$rsrc);
    }
}
$objMov = new MovilizacionPostController();
$objMov->index();