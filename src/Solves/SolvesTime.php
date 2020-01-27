<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 18/07/2019
 */ 
namespace Solves;

use \Datetime;
use \DateInterval;

class SolvesTime {
    

    public static function getPeriodoLabel($dtInicio, $dtFim) {
        if (\Solves\Solves::isNotBlank($dtInicio) && \Solves\Solves::isNotBlank($dtFim)) {
            return 'De ' . $dtInicio . ' até ' . $dtFim;
        } else if (\Solves\Solves::isNotBlank($dtInicio)) {
            return 'A partir de ' . $dtInicio;
        } else if (\Solves\Solves::isNotBlank($dtFim)) {
            return 'Até ' . $dtFim;
        }
    }
    public static function diffDatasEmDias($dataInicial, $dataFinal) {
        $diff = strtotime($dataFinal) - strtotime($dataInicial);
        return floor($diff / (60*60*24));
    }

    public static function subtraiMes($dataBR, $qtdMeses) {
        $dia = substr($dataBR, 0, 2);
        $mes = substr($dataBR, 3, 2);
        $ano = substr($dataBR, 6, 4);

        $newAno = $ano;
        $newMes = $mes;
        if ($qtdMeses > $mes) {
            $newAno --;
            $newMes = 13 - ($qtdMeses - $mes);
        } else if ($qtdMeses == $mes) {
            $newAno --;
            $newMes = 12;
        } else {
            $newMes = $newMes - $qtdMeses;
        }

        if ($newMes < 9) {
            $newMes = '0' . $newMes;
        }
        return $dia . '/' . $newMes . '/' . $newAno;
    }
    public static function getDiaSemanaInt($data) {
        $dia = substr($data, 0, 2);
        $mes = substr($data, 3, 2);
        $ano = substr($data, 6, 4);

        return jddayofweek(cal_to_jd(CAL_GREGORIAN, $mes, $dia, $ano), 0) + 1;
    }

    public static function dataPorExtenso($data) {
        $dia = substr($data, 0, 2);
        $mes = substr($data, 3, 2);
        $ano = substr($data, 6, 4);

        $meses = array("01" => "Janeiro",
            "02" => "Fevereiro",
            "03" => "Março",
            "04" => "Abril",
            "05" => "Maio",
            "06" => "Junho",
            "07" => "Julho",
            "08" => "Agosto",
            "09" => "Setembro",
            "10" => "Outubro",
            "11" => "Novembro",
            "12" => "Dezembro");


        $str = $dia . " de " . $meses[$mes] . " de " . $ano;
        return $str;
    }

    public static function converterHoraEmMinuto($horas) {
        $hora = substr($horas, 0, 2);
        $min = substr($horas, 3, 2);

        $qtdMin = ($hora * 60) + $min;

        return $qtdMin;
    }
    public static function getAnoAtual() {
        return date("Y");
    }
    public static function getMesAtual() {
        return date("m");
    }
    public static function getMesNumericAtual() {
        return date("n");
    }
    public static function getDataAtual() {
        return date("d/m/Y");
    }

    public static function getHoraAtual() {
        return date("H:i:s");
    }

    public static function getDataHoraAtual() {
        return date("d/m/Y H:i:s");
    }

    public static function getTimestampAtual() {
        return date("Y-m-d H:i:s");
    }

    public static function getDateAtual() {
        return date("Y-m-d");
    }

    public static function converterMinutoEmHora($minutos) {
        if ($minutos) {
            $horas = $minutos / 60;
            $horas2 = floor($horas);
            $horas3 = ceil($horas);

            $min = ($horas3 - $horas) * 60;

            if ($min < 10) {
                $min .= "0";
            }
            if ($horas2 < 10) {
                $horas2 = "0" . $horas2;
            }

            $horas = $horas2 . ":" . $min;

            return $horas;
        } else {
            return "";
        }
    }
    public static function addDiasToTimestamp($timestamp, $dias){
        $newTimestampStr = "";
        if (\Solves\Solves::isNotBlank($timestamp)) {
            $format = "Y-m-d H:i:s";
            if(strlen($timestamp)==10){
                $format = "Y-m-d";
            }
            $data = DateTime::createFromFormat($format, $timestamp);
            $data->add(new DateInterval('P'.$dias.'D')); // QTD dias
            $newTimestampStr = $data->format($format);
        }
        return $newTimestampStr;
    }
    public static function addHorasToTimestamp($timestamp, $horas){
        $newTimestampStr = "";
        if (\Solves\Solves::isNotBlank($timestamp)) {
            $format = "Y-m-d H:i:s";
            if(strlen($timestamp)==10){
                $format = "Y-m-d";
            }
            $data = DateTime::createFromFormat($format, $timestamp);
            $data->add(new DateInterval('PT'.$horas.'H')); // QTD horas
            $newTimestampStr = $data->format($format);
        }
        return $newTimestampStr;
    }
    public static function getDataFormatada($timestamp) {
        $dia = substr($timestamp, 8, 2);
        $mes = substr($timestamp, 5, 2);
        $ano = substr($timestamp, 0, 4);
        $horas = substr($timestamp, 11, 8);
        $str = $dia . "/" . $mes . "/" . $ano . " às " . $horas;
        return $str;
    }
    public static function getDataFormatadaSemHoras($timestamp) {
        $dia = substr($timestamp, 8, 2);
        $mes = substr($timestamp, 5, 2);
        $ano = substr($timestamp, 0, 4);

        $str = $dia . "/" . $mes . "/" . $ano;

        return $str;
    }
    public static function getHoraFormatada($time) {
        $format = "Y-m-d H:i:s";
        $data = DateTime::createFromFormat($format, $time);
        return $data->format($format);
    }
    public static function getDateFormated($data) {
        if (\Solves\Solves::isNotBlank($data)) {
            $dia = substr($data, 0, 2);
            $mes = substr($data, 3, 2);
            $ano = substr($data, 6, 4);

            $str = $ano . "-" . $mes . "-" . $dia;
        } else {
            $str = "";
        }
        return $str;
    }
    public static function getTimestampFormated($data) {
        if (\Solves\Solves::isNotBlank($data)) {
            $padraoUsa = (\Solves\Solves::isNumerico($data[2]));
            if($padraoUsa){
                $dia = substr($data, 8, 2);
                $mes = substr($data, 5, 2);
                $ano = substr($data, 0, 4);
                $horas = (strlen($data)>10 ? ' '.substr($data, 11, 8) : '');
            }else{
                $dia = substr($data, 0, 2);
                $mes = substr($data, 3, 2);
                $ano = substr($data, 6, 4);
                $horas = (strlen($data)>10 ? ' '.substr($data, 11, 8) : '');
            }

            $str = $ano . "-" . $mes . "-" . $dia.$horas;
        } else {
            $str = "";
        }
        return $str;
    }
    public static function getTimeFormated($data) {
        if (\Solves\Solves::isNotBlank($data)) {
            $str = $data;
        } else {
            $str = "";
        }
        return $str;
    }

    public static function getAno($timestamp) {
        return substr($timestamp, 0, 4);
    }
    
}