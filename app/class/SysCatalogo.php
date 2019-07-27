<?php
/**
* Clase SysCatalogo
*
* @version 1.0.0 Oct-18
*/

require_once '../init.php';

class SysCatalogo
{
    /**
     * Catálogo de los motivos de movilización *
     *
     * @access public
     * @param integer $motivo valor numérico del motivo de movilización.
     * @return string
     */
    public function getMotivoMovilizacion($motivo = null)
    {
        $motivosMov = [
            "ENGORDA",                  // 1
            "EVENTO DEPORTIVO",         // 2 
            "EXPORTACIÓN",              // 3
            "EXPOSICIÓN",               // 4
            "REPASTO",                  // 5
            "REPRODUCCIÓN PIE DE CRÍA", // 6
            "TRABAJO",                  // 7
            "ACOPIO",                   // 8
            "SUBASTA",                  // 9
            "REGRESO DE FERIA O EXPO",  // 10
            "SACRIFICIO",               // 11
            "ESPECTÁCULO IDA-VUELTA",   // 12
            "IDA-VUELTA BÁSCULA",       // 13
            "REGRESO A ORIGEN"          // 14
        ];
        if ($motivo != "" || $motivo != null) {
            return $motivosMov[$motivo - 1];
        } else {
            return $motivosMov;
        }
    }

    /**
     * Catálogo de los tipos de transporte *
     *
     * @access public
     * @param integer $cveTransporte valor numérico del tipo de transporte.
     * @return string
     */
    public function getTipoTransporte($cveTransporte = null)
    {
        $transportes = [
            "Ferroviario", // 1
            "Marítimo",    // 2 
            "Terrestre",   // 3
            "Aéreo",       // 4
            "Arreo",       // 5
        ];
        if ($cveTransporte != "" || $cveTransporte != null) {
            return $transportes[$cveTransporte - 1];
        } else {
            return $transportes;
        }
    }

    /**
     * Verifica el tipo de transporte en catálogo *
     *
     * @access public
     * @param integer $cveTransporte valor numérico del tipo de transporte.
     * @return boolean
     */
    public function verificaTipoTransporte($cveTransporte = null)
    {
        switch ($cveTransporte) {
            case 1:
                return true;
                break;
            case 2:
                return true;
                break;
            case 3:
                return true;
                break;
            case 4:
                return true;
                break;
            case 5:
                return true;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * Catálogo de los dictamenes *
     *
     * @access public
     * @param integer $cveDictamen valor numérico de la clave del dictamen.
     * @return string $datos
     */
    public function getDictamenes($cveDictamen = null)
    {
        $datos = [
            "Certificado Zoosanitario",          // 1
            "Const. Tratamiento Garrapa.",       // 2
            "Facturas o Cert. de propiedad",     // 3
            "Const. Negativa de Tb",             // 4
            "Const. Negativa de Br",             // 5
            "Const. Hato Libre de Tb",           // 6
            "Const. Hato Libre de Br",           // 7
            "Const. Hato Libre Cert. Tb",        // 8
            "Const. de Vacunación Br",           // 9
            "Const. de Rebaño Libre (Borregos)", // 10
        ];
        if (!is_null( $cveDictamen )) {
            return $datos[$cveDictamen - 1];
        } else {
            return $datos;
        }
    }

    /**
     * Catálogo de los nombres de los valores de los dictamenes en la base de datos *
     *
     * @access public
     * @param integer $cveDictamen valor numérico de la clave del dictamen.
     * @return string $datos
     */
    public function getValueNameDictamen($cveDictamen = null)
    {
        $datos = [
            "dicta_certifica_zoo",      // 1
            "dicta_const_garrapata",    // 2
            "dicta_facturas",           // 3
            "dicta_const_neg_tuber",    // 4
            "dicta_const_neg_bruce",    // 5
            "dicta_const_tuberculosis", // 6
            "dicta_const_brucelosis",   // 7
            "dicta_const_cert_tuber",   // 8
            "dicta_const_vacunacion",   // 9
            "dicta_const_rebano",       // 10
        ];
        if (!is_null( $cveDictamen )) {
            return $datos[$cveDictamen - 1];
        } else {
            return $datos;
        }
    }

    /**
     * Verifica el tipo de dictamen en catálogo *
     *
     * @access public
     * @param integer $cveDictamen valor numérico del dictamen.
     * @return boolean
     */
    public function verificaDictamen($cveDictamen = null)
    {
        if (!is_null( $cveDictamen )) {
            if (ctype_digit( $cveDictamen )) {
                if ($cveDictamen > 0 && $cveDictamen < 11) {
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

    /**
     * Catálogo de estatus de movilización *
     *
     * @access public
     * @param integer $cveEstatus
     * @return boolean
     */
    public function getEstatusMov($cveEstatus = null)
    {
        switch ($cveEstatus) {
            case 0:
                return true;
                break;
            case 2:
                return true;
                break;
            case 3:
                return true;
                break;
            case 9:
                return true;
                break;
            default:
                return false;
                break;
        }
    }
}