<?php
/**
* Controlador de los procesos de peticiones a través de la red *
*
* @version 1.0.0 Jul-18
*/

require_once '../init.php';

class EndPointController extends SysForm
{
    private $_sysParametros;

    public function getParametrosApp()
    {
        return $this->_sysParametros;
    }

    /**
     * Procesa una solicitud enviada por el usuario validando permisos y token *
     *
     * @access public
     * @return json
     */
    public function processRequest()
    {
        header( "Access-Control-Allow-Origin: *" );
        header( "Access-Control-Allow-Headers: usuario,token,Origin,X-Requested-With,Content-Type,Accept,Access-Control-Request-Method" );
        header( "Access-Control-Allow-Methods: GET" );
        header( "Allow: GET" );
        $httpUser      = apache_request_headers()['Usuario'];
        $httpUserToken = apache_request_headers()['Token'];
        if (!empty( $httpUserToken ) && !empty( $httpUser )) {
            $token = self::validateToken(['idUsuario' => $httpUser,'token' => $httpUserToken]);
        } else {
            $token = false;
        }
        if (isset( $token->success ) && $token->success == true) {
            $this->_sysParametros = new SysParametros($token->result['usuario']['tipo'],$token->result['usuario']['cveEdo']);
            if( $this->_sysParametros->validateOpenCloseSystemTime() ) {
                $datos = self::parseString();
                $this->setCveEdo($token->result['usuario']['cveEdo']);
                $this->setUserId($token->result['usuario']['id']);
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'GET':
                        return $this->evaluateMovilizacion($datos);
                        break;
                    case 'DELETE':
                        return $this->destroyMovilizacion($datos);
                        break;
                    case 'POST':
                        return $this->createMovilizacion($datos);
                        break;
                }
            } else {
                return $this->showError(6,0);
            }
        }
        return $this->showError(1,$token);
    }

    /**
     * Valida el usuario y el token *
     *
     * @access protected
     * @param array $data
     * @return json
     */
    protected function validateToken($data)
    {
        $objAuth = new SysAuthentication;
        return $objAuth->validateToken($data);
    }

    /**
     * Función que muestra el mensaje de success y envía los datos obtenidos si es el caso *
     *
     * @access protected
     * @param array $data [description]
     * @return json
     */
    protected function successResponse($code = "",$data = [])
    {
        header("Content-Type:application/json;charset=utf-8");
        $datos = [
            "success" => true,
            "message" => "Respuesta exitosa.",
            "code"    => "REEMO-" . ($code ? $code : 200) . "-" . $this->setHeader($code),
            "result"  => $data
        ];
        echo json_encode( $datos );
    }

    /**
     * Regresa el texto del código de error de una petición *
     *
     * @access private 
     * @param integer $codigo [description]
     * @return string
     */
    private function getStatusCodeMessage($codigo = null)
    {
        $estado = [
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            204 => 'No Content',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error'
        ];
        return (!is_null( $estado[$codigo] ) ? $estado[$codigo] : $estado[500]);
    }

    /**
     * Muestra el mensaje de error en base a una acción realizada *
     *
     * @access protected
     * @param integer $id []
     * @param array $datos []
     * @return json
     */
    protected function showError($id = false,$datos = [])
    {
        $errors = [
            #0
            [
                'success' => false,
                'message' => "Acceso Denegado",
                'error'   => [
                    'description' => "No tiene permisos para realizar esta accion",
                    'result'      => $datos,
                    'code'        => "REEMO-401-" . $this->setHeader(401)
                 ]

            ],
            #1
            [
                'success' => false,
                'message' => "Acceso Denegado",
                'error'   => [
                    'description' => "Usuario o Token incorrecto, favor de verificar",
                    'result'      => $datos,
                    'code'        => "REEMO-401-" . $this->setHeader(401)
                 ]

            ],
            #2
            [
                'success' => false,
                'message' => "Peticion Incorrecta",
                'error'   => [
                    'description' => "El Servicio de Internet es Incorrecto",
                    'result'      => $datos,
                    'code'        => "REEMO-500-" . $this->setHeader(500)
                 ]

            ],
            #3
            [
                'success' => false,
                'message' => $msg,
                'error'   => [
                    'description' => "Verificar los campos solicitados.",
                    'result'      => $datos,
                    'code'        => "REEMO-204-" . $this->setHeader(204)
                 ]

            ],
            #4
            [
                'success' => false,
                'message' => "Sin Registros",
                'error'   => [
                    'description' => "No se encontró ningun registro",
                    'result'      => $datos,
                    'code'        => "REEMO-204-" . $this->setHeader(204)
                 ]
            ],
            #5
            [
                'success' => false,
                'message' => "Sin Registros",
                'error'   => [
                    'description' => "Ingrese datos para poder realizar la acción",
                    'result'      => $datos,
                    'code'        => "REEMO-204-" . $this->setHeader(204)
                 ]
            ],
            #6
            [
                'success' => false,
                'message' => "Sistema cerrado - Fuera de horario",
                'error'   => [
                    'description' => "Fuera de servicio",
                    'result'      => $datos,
                    'code'        => "REEMO-204-" . $this->setHeader(204)
                 ]
            ]
        ];
        echo json_encode( ($errors[$id]) );
    }

    /**
     * Establece el formato del encabezado de una petición REST *
     *
     * @access protected
     * @param integer $codigo [description]
     * @return string
     */
    protected function setHeader($codigo)
    {
        header($_SERVER['SERVER_PROTOCOL']);
        header("Content-Type:application/json;charset=utf-8");
        return $this->getStatusCodeMessage($codigo);
    }

    /**
     * Verifica si están correctos los valores ingresados *
     *
     * @access public 
     * @param array $data [description]
     * @param object $clase [description]
     * @param array $valoresFecha []
     * @return array
     */
    public function parseRegister($data = [],$clase,$valoresFecha = [])
    {
        $datos          = [];
        $errorFields    = [];
        $flagErrorField = false;
        # Verifica que no vayan nulos los campos y si no están nulos los regresa en un arreglo *
        if (count( $data ) > 0) {
            for ($i = 0; $i < count( $data ); $i++) { 
                foreach ($data[$i] as $key => $value) {
                    if ($data[$i][$key] == null || $data[$i][$key] == "") {
                        $flagErrorField = true;
                        $errorFields[]  = [
                            "parameter" => $key,
                            "message"   => "Campo nulo o vacío"
                        ];
                    }
                    if ($value != false) {
                        $datos[$key] = $value;
                    }
                }
                # Se valida la fecha *
                if (!empty( $valoresFecha )) {
                    for ($j = 0; $j < count( $valoresFecha ); $j++) { 
                        $validateFecha = isset( $data[$i][$valoresFecha[$j]] ) ? $data[$i][$valoresFecha[$j]] : false;
                        if (!empty( $validateFecha )) {
                            //$fecha = self::checkFecha( $validateFecha );
                            if (isset( $fecha['success'] ) && $fecha['success'] == false) {
                                $flagErrorField = true;
                                $errorFields[]  = [
                                    "parameter" => $valoresFecha[$j],
                                    "message"   => $fecha['message']
                                ];
                            }
                        }
                    }
                }
            }
            # Se valida si existe un error en algún campo y devuelve los campos con problema *
            if ($flagErrorField) {
                return ['success' => false,'result' => $errorFields];
            }
        }
        # Se realizan validaciones de diferentes datos *
        if ( array_diff( array_keys( $datos ),$clase->fillable )) {
            return ['success' => false,'result' => $errorFields];
            return ['success' => false,'result' => array_values( array_diff( array_keys( $datos ),$clase->fillable ) )];
        }
        return $datos;
    }

    /**
     * Verifica si están correctamente los valores ingresados de la fecha *
     *
     * @access public 
     * @param string $fecha [description]
     * @return array
     */
    public function checkFecha($fecha = false)
    {    
        if ($fecha) {
            $fechas = explode( "-",$fecha );
            if (count( $fechas ) == 3) {
                if (checkdate( $fechas[1],$fechas[2],$fechas[0] ) != false) {
                    return ['success' => true,'message' => "Fecha correcta"];
                }
            }
            return ['success' => false,'message' => "Fecha incorrecta"];   
        } else {
            return ['success' => false,'message' => "Valor fecha inválido"];  
        }
    }
}