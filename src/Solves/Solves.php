<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 18/07/2019
 */ 
namespace Solves;

use SolvesAuth\SolvesAuth;
use SolvesMail\SolvesMail;
use SolvesDAO\SolvesDAO;
use SolvesPay\SolvesPay;

class Solves {
	const SYSTEM_MODE_DEV = 'DEV';
	const SYSTEM_MODE_PROD = 'PROD';
	const SOLVES_CDN = 'https://cdn.solves.com.br/';
	const SOLVES_CDN_JS = 'https://cdn.solves.com.br/js/';
	const SOLVES_CDN_CSS =  'https://cdn.solves.com.br/css/';
	const SOLVES_CDN_IMG =  'https://cdn.solves.com.br/img/';
	const SOLVES_CDN_LIB =  'https://cdn.solves.com.br/lib/';

	private static $BLACKLIST = array("'", "/", "|", "-", " OR ", " AND ", " XOR ", " NOR ", ";", "(", ")", "SELECT", "UNION ", " FROM ");
	private static $CORINGA_CRIPTOGRAFIA = "_1aZ7_";

	private static $SITE_TITULO = 'Solves';
	private static $SITE_URL = 'http://localhost/';
	private static $SITE_DESCR = '';
	private static $SITE_KEYS = '';

	private static $SYSTEM_NAME = '';
	private static $SYSTEM_VERSION = '';
	private static $SYSTEM_MODE = 'DEV';
	private static $DEFAULT_CHARSET = '';
	private static $MODO_SOON_ATIVADO = false;

	private static $IMG_PATH;
	private static $IMG_PATH_LOGO;

    public static function config($systemName, $systemVersion, $siteTitulo, $siteUrl, $siteDescr, $sitekeys, $imgPath, $imgPathLogo){
    	Solves::setSystemName($systemName);
    	Solves::setSystemVersion($systemVersion);
    	Solves::setSiteTitulo($siteTitulo);
    	Solves::setSiteUrl($siteUrl);
    	Solves::setSiteDescr($siteDescr);
    	Solves::setSiteKeys($siteKeys);
    	Solves::setImgPath($imgPath);
    	Solves::setImgPathLogo($imgPathLogo);
    }
    public static function configAuth($firebaseJsonFile, $firebaseUser){
    	SolvesAuth::config($firebaseJsonFile, $firebaseUser);
    }
    public static function configMail($emailHost, $emailPort, $emailRemetente, $emailRemetentePasswd, $emailRemetenteFromLabel){
    	SolvesMail::config($emailHost, $emailPort, $emailRemetente, $emailRemetentePasswd, $emailRemetenteFromLabel);
    }
    public static function configDAO($bdDevHost, $bdDevPort, $bdDevUrl, $bdDevUser, $bdDevPassword, $bdDevDatabase, $bdProdHost, $bdProdPort, $bdProdUrl, $bdProdUser, $bdProdPassword, $bdProdDatabase){
    	SolvesDAO::config($bdDevHost, $bdDevPort, $bdDevUrl, $bdDevUser, $bdDevPassword, $bdDevDatabase,$bdProdHost, $bdProdPort, $bdProdUrl, $bdProdUser, $bdProdPassword, $bdProdDatabase);
    }

    public static function setSystemName($p){Solves::$SYSTEM_NAME = $p;}
    public static function setSystemVersion($p){Solves::$SYSTEM_VERSION = $p;}
    public static function setSiteTitulo($p){Solves::$SITE_TITULO = $p;}
    public static function setSiteUrl($p){Solves::$SITE_URL = $p;}
    public static function setSiteDescr($p){Solves::$SITE_DESCR = $p;}
    public static function setSiteKeys($p){Solves::$SITE_KEYS = $p;}
    public static function setImgPath($p){Solves::$IMG_PATH = $p;}
    public static function setImgPathLogo($p){Solves::$IMG_PATH_LOGO = $p;}

    public static function getSystemName(){return Solves::$SYSTEM_NAME;}
    public static function getSystemVersion(){return Solves::$SYSTEM_VERSION;}
    public static function getSiteTitulo(){return Solves::$SITE_TITULO;}
    public static function getSiteUrl(){return Solves::$SITE_URL;}
    public static function getSiteDescr(){return Solves::$SITE_DESCR;}
    public static function getSiteKeys(){return Solves::$SITE_KEYS;}
    public static function getImgPath(){return Solves::$IMG_PATH;}
    public static function getImgPathLogo(){return Solves::$IMG_PATH_LOGO;}

	public static function isDevMode() {return (SYSTEM_MODE_DEV==Solves::$SYSTEM_MODE);}
	public static function isProdMode() {return (SYSTEM_MODE_PROD==Solves::$SYSTEM_MODE);}
	public static function setDevMode() {Solves::$SYSTEM_MODE=SYSTEM_MODE_DEV;}
	public static function setProdMode() {Solves::$SYSTEM_MODE=SYSTEM_MODE_PROD;}
	public static function isModoSoon() {return Solves::$MODO_SOON_ATIVADO;}
	public static function setModoSoon() {Solves::$MODO_SOON_ATIVADO=true;}

	public static function isNotBlank($n) {
	    return (isset($n) && $n !== 'null' && $n !== null && $n !== "" && $n !== " ");
	}
	public static function criptografa($p) {
	    $cod = strrev(base64_encode($p));
	    return str_replace("=", Solves::$CORINGA_CRIPTOGRAFIA, $cod);
	}

	public static function descriptografa($p) {
	    $recebe = str_replace(Solves::$CORINGA_CRIPTOGRAFIA, "=", $p);
	    return base64_decode(strrev($recebe));
	}

	public static function criptografaSenha($email, $senha) {
	    $p = $email . '.md5.' . $senha . '.md5';
	    $cod = md5($p);
	    return $cod;
	}

	public static function criptoMD5($p) {
	    $cod = md5($p);
	    return $cod;
	}
	public static function escapeTextFields($txt){
	    return str_replace('\'', '\\\'', $txt);
	}
	public static function getSoNumero($str) {
	    if(Solves::isNotBlank($str)){
	        return Solves::soNumero($str);
	    }
	    return null;
	}
	public static function soNumero($str) {
	    return preg_replace("/[^0-9]/", "", $str);
	}
	public static function getClientIp() {
	     $ipaddress = '';
	     if ($_SERVER['HTTP_CLIENT_IP'])
	         $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	     else if($_SERVER['HTTP_X_FORWARDED_FOR'])
	         $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	     else if($_SERVER['HTTP_X_FORWARDED'])
	         $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	     else if($_SERVER['HTTP_FORWARDED_FOR'])
	         $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	     else if($_SERVER['HTTP_FORWARDED'])
	         $ipaddress = $_SERVER['HTTP_FORWARDED'];
	     else if($_SERVER['REMOTE_ADDR'])
	         $ipaddress = $_SERVER['REMOTE_ADDR'];
	     else
	         $ipaddress = 'UNKNOWN';

	     return $ipaddress; 
	}

	public static function removeAcentos($var) {
    	return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"),explode(" ","a A e E i I o O u U n N c C"),$var);
	}

	public static function filtrar($p, $blacklist) {
	    if (!array_search($p, $blacklist) || !array_search($p, $blacklist)) {
	        return $p;
	    } else {
	        return "";
	    }
	}
	public static function getAnoCopyright(){
	    $anoStart = 2019;
	    $anoAtual = SolvesTime::getAnoAtual();
	    if($anoStart==$anoAtual){
	        return $anoAtual;
	    }
	    return $anoStart.' - '.$anoAtual;
	}
	public static function isNumerico($n) {
	    return is_numeric($n);
	}

	public static function removeEspacos($string) {
	    return Solves::substituiEspacos($string, "");
	}
	public static function substituiEspacos($string, $replaceFor) {
	    return preg_replace("/\\s\\s+/", $replaceFor, (isset($string)?$string:""));
	}
	public static function descriptografaNumero($p) {
	    if (isset($p)) {
	        $p = Solves::descriptografa($p);
	        if (Solves::isNumerico($p)) {
	            return $p;
	        }
	    }
	    return null;
	}
	public static function getTextWithLimit($text, $limit) {
	    $return = substr($text, 0, $limit);
	    if (strlen($text) > $limit) {
	        $return .= '... ';
	    }
	    return $return;
	}

	public static function getParamsFromUrl($url) {
	    if (strpos($url, '?') > 0) {
	        $arr = split('[?]', $url);
	        return $arr[1];
	    } else {
	        return '';
	    }
	}

	public static function getParamValueFromUrl($url, $paramName) {
	    $params = Solves::getParamsFromUrl($url);
	    $arr = array();
	    if (strpos($url, '&') > 0) {
	        $arr = split('[&]', $params);
	    } else {
	        $arr[0] = $params;
	    }
	    $arrLen = count($arr);
	    $val = '';
	    for ($i = 0; $i < $arrLen; $i++) {
	        $a = split('[=]', $arr[$i]);
	        $pName = $a[0];
	        if ($pName == $paramName) {
	            $val = $a[1];
	            break;
	        }
	    }
	    return $val;
	}

	public static function checkBoolean($b) {
	    return (isset($b) && ($b === 1 || $b === '1' || $b === true || $b === 'true' || $b === 't'));
	}

	public static function getBooleanLabel($b) {
	    return (Solves::checkBoolean($b) ? 'Sim' : 'Não');
	}

	/* * Apenas corrige o formato da informação. */

	public static function getDoubleValue($valor) {
	    if (Solves::isNotBlank($valor)) {
	        $valor = str_replace("R$", "", $valor);
	        $valor = str_replace(" ", "", $valor);
	        $valor = str_replace(".", "", $valor);
	        $valor = str_replace(",", ".", $valor);
	    } else {
	        $valor = "";
	    }
	    return $valor;
	}

	/** Formata um valor double como moeda */
	public static function formatMoney($v) {
	    $valor_real = $v * 100;
	    $i = 6;
	    $valor_moeda_formatada = Solves::getIntValue(round($valor_real, 0));

	    while (strlen($valor_moeda_formatada) < 3) {
	        $valor_moeda_formatada = '0' . $valor_moeda_formatada;
	    }
	    $lenValorMoedaFormatada = strlen($valor_moeda_formatada);
	    $valor_moeda_formatada = substr($valor_moeda_formatada, 0, $lenValorMoedaFormatada - 2) .
	            ',' . substr($valor_moeda_formatada, $lenValorMoedaFormatada - 2, $lenValorMoedaFormatada);

	    while (strlen($valor_moeda_formatada) > $i) {
	        $lenValorMoedaFormatada = strlen($valor_moeda_formatada);
	        $valor_moeda_formatada = substr($valor_moeda_formatada, 0, $lenValorMoedaFormatada - $i) . '.' .
	                substr($valor_moeda_formatada, $lenValorMoedaFormatada - ($i), $lenValorMoedaFormatada);
	        $i = $i + 4;
	    }
	    return 'R$ ' . $valor_moeda_formatada;
	}

	/* * Retorna o valor no formato correto e tipo FLOAT. */

	public static function getFloatValue($valor) {
	    $valor = Solves::getDoubleValue($valor);
	    return floatval($valor);
	}

	public static function getIntValue($valor) {
	    if (Solves::isNotBlank($valor)) {
	        $valor = str_replace("R$", "", $valor);
	        $valor = str_replace(" ", "", $valor);
	        $valor = preg_replace("/[^0-9]/", "", $valor);
	    } else {
	        $valor = "";
	    }
	    return $valor;
	}

	public static function getArraySimNao() {
	    $arr_SimNao = array();
	    $arr_SimNao[0] = array();
	    $arr_SimNao[1] = array();

	    $arr_SimNao[0]["id"] = "true";
	    $arr_SimNao[0]["label_label"] = "Sim";

	    $arr_SimNao[1]["id"] = "false";
	    $arr_SimNao[1]["label_label"] = "Não";

	    return $arr_SimNao;
	}
	public static function getArrayQtds($qtd) {
	    $arr = array();
	    for($q=0; $q!=$qtd; $q++){
	        $arr[$q] = array();
	        $arr[$q]["id"] = ($q+1);
	        $arr[$q]["label_label"] = ($q+1);
	    }
	    return $arr;
	}

	public static function getArrayBitSimNao() {
	    $arr_SimNao = array();
	    $arr_SimNao[0] = array();
	    $arr_SimNao[1] = array();

	    $arr_SimNao[0]["id"] = "1";
	    $arr_SimNao[0]["label_label"] = "Sim";

	    $arr_SimNao[1]["id"] = "0";
	    $arr_SimNao[1]["label_label"] = "Não";

	    return $arr_SimNao;
	}
	public static function getCustomSimpleArray($id1, $v1, $id2, $v2) {
	    $arr = array();
	    $arr[0] = array();
	    $arr[1] = array();

	    $arr[0]["id"] = $id1;
	    $arr[0]["label_label"] = $v1;

	    $arr[1]["id"] = $id2;
	    $arr[1]["label_label"] = $v2;

	    return $arr;
	}

	public static function getCustomSimpleArray3Options($id1, $v1, $id2, $v2, $id3, $v3) {
	    $arr = array();
	    $arr[0] = array();
	    $arr[1] = array();
	    $arr[2] = array();

	    $arr[0]["id"] = $id1;
	    $arr[0]["label_label"] = $v1;

	    $arr[1]["id"] = $id2;
	    $arr[1]["label_label"] = $v2;

	    $arr[2]["id"] = $id3;
	    $arr[2]["label_label"] = $v3;

	    return $arr;
	}
	public static function getCustomSimpleArrayDynamicOptions($arrOrigin) {
	    $arr = array();
	    $qtd = count($arrOrigin);
	    $i=0;
	    foreach($arrOrigin as $key=>$value){ 
	        $arr[$i] = array();
	        $arr[$i]["id"] = $key;
	        $arr[$i]["label_label"] = $value;
	        $i++;
	    }
	    return $arr;
	}


	public static function validaEmail($mail){
	    if(preg_match("/^([[:alnum:]_.-]){3,}@([[:lower:][:digit:]_.-]{3,})(.[[:lower:]]{2,3})(.[[:lower:]]{2})?$/", $mail)) {
	        return true;
	    }else{
	        return false;
	    }
	}
	public static function getUrlNormalizada($string) {
	    $string = Solves::removeAcentos($string);
	    $string = strtolower($string);
	    $string = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i","", $string );
	    $string = str_replace(' ','-', $string);
	    $string = str_replace('__','_', $string);
	    $string = str_replace('--','-', $string);
	    return $string;
	}
	public static function getApenasNumeros($string) {
	    return preg_replace("/[^0-9]/", "", $string);
	}
	public static function maiusculo($string){
	    return strtoupper(strtr($string ,"áéíóúâêôãõàèìòùç","ÁÉÍÓÚÂÊÔÃÕÀÈÌÒÙÇ"));
	}
	public static function getIndexUrl($IS_APP){
	    return Solves::getPublicUrl($IS_APP, 'index');
	}
	public static function getPublicUrl($IS_APP, $url){
	    return (Solves::checkBoolean($IS_APP) ? '/app_' :'/').$url;
	}
	public static function isPageActive($urlAtual, $urlMenu){
	    if(!Solves::isNotBlank($urlAtual)){
	        $urlAtual = 'index';
	    }else if($urlAtual[0]=='/'){$urlAtual = substr($urlAtual, 1, strlen($urlAtual));}
	    $urlRaiz = explode("/", $urlAtual);
	    $urlRaiz = $urlRaiz[0];
	    return ($urlRaiz==$urlMenu);
	}
	public static function getParamBaseUrl($url, $ordem){
	    if($url[0]=='/'){$url = substr($url, 1, strlen($url));}
	    $arr = explode('/',$url);
	    return $arr[$ordem];
	}
	public static function getUrlName($root,$url,$primeiraLetraMaiuscula=true){
	    if(Solves::isNotBlank($root)){
	        $url = substr($url, strlen($root), strlen($url));
	    }
	    if($url[0]=='/'){$url = substr($url,1,strlen($url));}
	    if(strpos($url, '/') !== false){$url = strstr($url, '/', true);}
	    if(strpos($url, '?') !== false){$url = strstr($url, '?', true);}
	    if(strpos($url, '#') !== false){$url = strstr($url, '#', true);}
	    if(strpos($url, '.') !== false){$url = strstr($url, '.', true);}
	    return ($primeiraLetraMaiuscula ? ucfirst($url) : $url);
	}
}