<?php
/**
* Clase SysCatalogo
*
* @version 1.0.0 Jul-18
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
            "ESPECTÁCULO IDA-VUELTA"    // 12
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
     * Catálogo de los dictamenes *
     *
     * @access public
     * @param integer $cveTransporte valor numérico del tipo de transporte.
     * @return string
     */
    public function getDictamenes($cveTransporte = null)
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
}