<?php
/**
* Controlador del proceso de almacenaje de una movilización del SIGE (Puebla-21-PUE) al REEMO * 
*
* @version 1.0.0 May-18
*/

require_once '../init.php';

class MovilizacionPostController extends EndPointController
{

    private $_cveEdo;

    public function setCveEdo($value)
    {
        $this->_cveEdo = $value;
    }

	public function index()
	{
		return self::processRequest();
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
        $params  = [];
        $tipoMov = (isset( $request['tipoMov'] ) ? $request['tipoMov'] : 0);
        try {
            $objMovilizacion = new Movilizacion($this->_cveEdo);
            foreach ($objMovilizacion->_fillable as $key => $value) {
                if (!isset( $request[$value] )) {
                    if (!empty( $request[$value] )) {
                        throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " esta vacío o es inválido");
                    }
                    throw new Exception("No se cuenta con el parámetro " . $objMovilizacion->_fillable[$key]);
                }
                switch ($value) {
                    case 'tipoMov':
                        if (!ctype_digit( $request[$value] )) {
                            throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " es un número inválido");
                        }
                        if ($request[$value] < 1 || $request[$value] > 2) {
                            throw new Exception("El rango debe ser 1 ó 2 para el parámetro " . $objMovilizacion->_fillable[$key]);
                        }
                        $params['data'][] = $request[$value];
                        break;
                    case 'motivo':
                        switch ($tipoMov) {
                            case 1:

                                if (!ctype_digit( $request[$value] )) {
                                    throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " es un número inválido");
                                }
                                if ($request[$value] < 1 || $request[$value] > 3) {
                                    throw new Exception("El rango debe ser de 1 a 3 para el parámetro " . 
                                    $objMovilizacion->_fillable[$key]);
                                }
                                $params['data'][] = $request[$value];
                                break;
                            case 2:
                                if (!ctype_digit( $request[$value] )) {
                                    throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " es un número inválido");
                                }
                                if ($request[$value] != 1) {
                                    throw new Exception("El valor del parámetro " . $objMovilizacion->_fillable[$key] . 
                                    " sólo puede ser 1 cuando tipoMov es 2");
                                }
                                $params['data'][] = $request[$value];
                                break;
                            default:
                                throw new Exception("Tipo de movilización desconocido");
                                break;
                        }
                        break;
                    case 'folio':
                    case 'responsable':
                    case 'centroExpedidor':
                        if (!ctype_digit( $request[$value] )) {
                            throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " es un número inválido");
                        }
                        if ($request[$value] <= 0) {
                            throw new Exception("El rango debe ser un número mayor a 0 para el parámetro " . 
                            $objMovilizacion->_fillable[$key]);
                        }
                        $params['data'][] = $request[$value];
                        break;
                    case 'estatusMov':
                        if (!ctype_digit( $request[$value] )) {
                            throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " es un número inválido");
                        }
                        if ($request[$value] != 1 && $request[$value] != 2 && $request[$value] != 9) {
                            throw new Exception("El rango debe ser 1,2 ó 9 para el parámetro " . $objMovilizacion->_fillable[$key]);
                        }
                        $params['data'][] = $request[$value];
                        break;
                    case 'identificadores':
                        $params['identificadores'] = explode(",",$request['identificadores']);
                        if (!empty( $params['identificadores'] )) {
                            foreach ($params['identificadores'] as $item) {
                                if (strlen(trim( $item )) != 10) {
                                    throw new Exception("Identificador $item no cumple con la longitud de 10 caractéres");
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
                    case 'fechaHoraMov':
                        $fechaHora = explode("%20",$request['fechaHoraMov']);
                        if (sizeof( $fechaHora ) == 2) {
                            $params['data'][] = $fechaHora[0] . " " . $fechaHora[1];
                        } else {
                            throw new Exception("No se cuenta con información de la fecha y hora");
                        }
                        break;
                    case 'origen':
                        if (strlen(trim( $request[$value] )) == 12) {
                            if (substr(strtoupper( $request[$value] ),0,2) != $this->_cveEdo) {
                                throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no pertenece a su estado");
                            }
                            $params['data'][] = $request[$value];
                        } else {
                            throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no cumple con la " . 
                            "longitud de 12 caractéres");
                        }
                        break;
                    case 'destino':
                        switch ($tipoMov) { 
                            case 1:
                                if (strlen(trim( $request[$value] )) == 12) {
                                    $params['data'][] = $request[$value];
                                } else {
                                    throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no cumple con la " . 
                                    "longitud de 12 caractéres");
                                }
                                break;
                            case 2:
                                if (strlen(trim( $request[$value] )) >= 4) {
                                    // Para el campo upp_destino *
                                    $params['data'][] = $request[$value];
                                    // Para el campo cve_rastro *
                                    $params['data'][] = $request[$value];
                                } else {
                                    throw new Exception("El parámetro " . $objMovilizacion->_fillable[$key] . " no cumple con la " . 
                                    "longitud de 4 caractéres o más");
                                }
                                break;
                            default:
                                throw new Exception("Tipo de movilización desconocido");
                                break;
                        }
                        break;
                    default:
                        $params['data'][] = $request[$value];
                        break;
                }
            }
            $rsrc['movilizacion'] = $objMovilizacion->saveMovilizacion($params);
        } catch (Exception $e) {
            $rsrc['movilizacion'] = [
                "calificacion" => "No es posible almacenar la movilización",
                "motivo"       => $e->getMessage()
            ];
        }
        return $this->successResponse(200,$rsrc);
    }
}
$objMov = new MovilizacionPostController();
$objMov->index();