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
use SolvesNotification\SolvesNotification;
use SolvesUi\SolvesUi;

class Solves {
	const VENDOR_INSIDE_NAVS = '../../../../';
	const VENDOR_PATH = 'vendor/thiagogoulart/solves/';
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
	private static $APP_URL = 'http://localhost/';
	private static $SITE_DESCR = '';
	private static $SITE_KEYS = '';
	private static $REST_URL = 'http://localhost/';
	private static $SITE_CONTEXT = '/';
	private static $SITE_DIR = '/';

	private static $PATH_ROOT = '';
	private static $PATH_RAIZ= '';
	private static $ROOT_PATH_OR_MODULE ='/';

	private static $SYSTEM_NAME = '';
	private static $SYSTEM_VERSION = '';
	private static $SYSTEM_MODE = 'DEV';
	private static $DEFAULT_CHARSET = '';
	private static $MODO_SOON_ATIVADO = false;
	private static $MODO_DEBUG = false;
	private static $MODO_TEST = false;

	private static $APP_GOOGLE_PLAY_STORE_LINK = '';
	private static $APP_APPLE_STORE_LINK = '';
	private static $APP_WINDOWS_STORE_LINK = '';

	private static $IMG_PATH;
	private static $IMG_PATH_LOGO;

	private static $WEBSOCKET_SERVER = null;

    public static function config($systemName, $systemVersion, $siteTitulo, $siteUrl, $siteDescr, $sitekeys, $imgPath, $imgPathLogo, $appUrl=null){
    	Solves::setSystemName($systemName);
    	Solves::setSystemVersion($systemVersion);
    	Solves::setSiteTitulo($siteTitulo);
    	Solves::setSiteUrl($siteUrl);
    	Solves::setAppUrl($appUrl);
    	Solves::setSiteDescr($siteDescr);
    	Solves::setSiteKeys($sitekeys);
    	Solves::setImgPath($imgPath);
    	Solves::setImgPathLogo($imgPathLogo);
    	Solves::setRestUrl($siteUrl);
    }
    public static function configAuth($firebaseJsonFile, $firebaseUser){
    	SolvesAuth::config($firebaseJsonFile, $firebaseUser);
    }
    public static function configMail($emailHost, $emailPort, $emailRemetente, $emailRemetentePasswd, $emailRemetenteFromLabel){
    	SolvesMail::config($emailHost, $emailPort, $emailRemetente, $emailRemetentePasswd, $emailRemetenteFromLabel);
    }
    public static function configDAO(string $systemDbType, string $bdHost, string $bdPort, string $bdUrl, string $bdUser, string $bdPassword, string $bdDatabase){
    	SolvesDAO::config($systemDbType, $bdHost, $bdPort, $bdUrl, $bdUser, $bdPassword, $bdDatabase);
    }
    public static function configDAOAdicional(string $connectionName, string $systemDbType, string $bdHost, string $bdPort, string $bdUrl, string $bdUser, string $bdPassword, string $bdDatabase){
    	SolvesDAO::configBd($connectionName,$systemDbType, $bdHost, $bdPort, $bdUrl, $bdUser, $bdPassword, $bdDatabase);
    }
    public static function configNotifications($serverSubscriptionsUrl, $publicKey, $privateKey, $senderId){
    	SolvesNotification::config($serverSubscriptionsUrl, $publicKey, $privateKey, $senderId);
    }
    public static function configUi(array $uiCssList,array $jsFilePaths,string $themeBgColor,string $themeColor,string $uiVersion){
    	SolvesUi::config($uiCssList, $jsFilePaths, $themeBgColor, $themeColor,$uiVersion);
    }

    public static function setSystemName($p){Solves::$SYSTEM_NAME = $p;}
    public static function setSystemVersion($p){Solves::$SYSTEM_VERSION = $p;}
    public static function setSiteTitulo($p){Solves::$SITE_TITULO = $p;}
    public static function setSiteUrl($p){Solves::$SITE_URL = $p;}
    public static function setRestUrl($p){Solves::$REST_URL = $p;}
    public static function setAppUrl($p){Solves::$APP_URL = (Solves::isNotBlank($p)?$p:Solves::$SITE_URL.'app/');}
    public static function setSiteDescr($p){Solves::$SITE_DESCR = $p;}
    public static function setSiteKeys($p){Solves::$SITE_KEYS = $p;}
    public static function setImgPath($p){Solves::$IMG_PATH = $p;}
    public static function setImgPathLogo($p){Solves::$IMG_PATH_LOGO = $p;}
    public static function setSiteContext($p){Solves::$SITE_CONTEXT = $p;}
    public static function setSiteDir($p){Solves::$SITE_DIR = $p;}

    public static function getSystemName(){return Solves::$SYSTEM_NAME;}
    public static function getSystemVersion(){return Solves::$SYSTEM_VERSION;}
    public static function getSiteTitulo(){return Solves::$SITE_TITULO;}
    public static function getSiteUrl(){return Solves::$SITE_URL;}
    public static function getRestUrl(){return Solves::$REST_URL;}
    public static function getAppUrl(){return Solves::$APP_URL;}
    public static function getSiteDescr(){return Solves::$SITE_DESCR;}
    public static function getSiteKeys(){return Solves::$SITE_KEYS;}
    public static function getImgPath(){return Solves::$IMG_PATH;}
    public static function getImgPathLogo(){return Solves::$IMG_PATH_LOGO;}
    public static function getSiteIcone(){return Solves::$IMG_PATH_LOGO.'favicon-32x32.png';}
    public static function getCompleteSiteIcone(){return self::getCompleteUrlPath(Solves::$IMG_PATH_LOGO.'favicon-32x32.png');}
    public static function getSiteContext(){return Solves::$SITE_CONTEXT;}
    public static function getSiteDir(){return Solves::$SITE_DIR;}


    public static function setWebsocketServer(\SolvesWebsocket\SolvesWebSocketServer $s){Solves::$WEBSOCKET_SERVER = $s;}
    public static function getWebsocketServer(){return Solves::$WEBSOCKET_SERVER;}

    public static function getCompleteImgPath(){
        return self::getCompleteUrlPath(Solves::getImgPath());
    }
    public static function getCompleteImgPathLogo(){
        return self::getCompleteUrlPath(Solves::getImgPathLogo());
    }
    public static function getCompleteUrlPath($p){
        return Solves::getSiteUrl().Solves::removeBarraInicial($p);
    }
    public static function getRelativePath($path){
    	if(\Solves\Solves::stringComecaCom($path, 'http')){
            return $path;
        }else{
            return (\Solves\Solves::getRootPathOrModule()).Solves::removeBarraInicial($path);
        }
    }

    public static function getVendorInsideNavs(){
    	return self::VENDOR_INSIDE_NAVS;
    }
    public static function getVendorPath(){
    	return Solves::$PATH_RAIZ.self::VENDOR_PATH;
    }

    public static function getPathRoot(){return Solves::$PATH_ROOT;}
    public static function setPathRoot($p){Solves::$PATH_ROOT = $p;}

    public static function getPathRaiz(){return Solves::$PATH_RAIZ;}
    public static function setPathRaiz($p){Solves::$PATH_RAIZ = $p;}

    public static function getGooglePlayStoreLink(){return Solves::$APP_GOOGLE_PLAY_STORE_LINK;}
    public static function setGooglePlayStoreLink($p){Solves::$APP_GOOGLE_PLAY_STORE_LINK = $p;}
    public static function getAppleStoreLink(){return Solves::$APP_APPLE_STORE_LINK;}
    public static function setAppleStoreLink($p){Solves::$APP_APPLE_STORE_LINK = $p;}
    public static function getWindowsStoreLink(){return Solves::$APP_WINDOWS_STORE_LINK;}
    public static function setWindowsStoreLink($p){Solves::$APP_WINDOWS_STORE_LINK = $p;}

	public static function isDevMode() :bool {return (self::SYSTEM_MODE_DEV==Solves::$SYSTEM_MODE);}
	public static function isProdMode() :bool {return (self::SYSTEM_MODE_PROD==Solves::$SYSTEM_MODE);}
	public static function setDevMode() {Solves::$SYSTEM_MODE=self::SYSTEM_MODE_DEV;}
	public static function setProdMode() {Solves::$SYSTEM_MODE=self::SYSTEM_MODE_PROD;}
	public static function isModoSoon() :bool {return Solves::$MODO_SOON_ATIVADO;}
	public static function setModoSoon() {Solves::$MODO_SOON_ATIVADO=true;}

	public static function isDebugMode() {return Solves::$MODO_DEBUG;}
	public static function setDebugMode() {Solves::$MODO_DEBUG=true;}

	public static function isTestMode() {return Solves::$MODO_TEST;}
	public static function setTestMode() {Solves::$MODO_TEST=true;}
	public static function setTestModeOff() {Solves::$MODO_TEST=false;}

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
	public static function removeEspacoesExcedentes(string $str){
		// First remove the leading/trailing whitespace
		$str = trim($str);
		// Now remove any doubled-up whitespace
		$str = preg_replace('/\s(?=\s)/', '', $str);
		// Finally, replace any non-space whitespace, with a space
		$str = preg_replace('/[\n\r\t]/', ' ', $str);
		return $str;
	}
	public static function getClientIp() {
	    return self::getClientIpByServer(@$_SERVER);
	}
	public static function getClientIpByServer($SERVER) {
	     $ipaddress = '';
	     if (@$SERVER['HTTP_CLIENT_IP'])
	         $ipaddress = $SERVER['HTTP_CLIENT_IP'];
	     else if(@$SERVER['HTTP_X_FORWARDED_FOR'])
	         $ipaddress = $SERVER['HTTP_X_FORWARDED_FOR'];
	     else if(@$SERVER['HTTP_X_FORWARDED'])
	         $ipaddress = $SERVER['HTTP_X_FORWARDED'];
	     else if(@$SERVER['HTTP_FORWARDED_FOR'])
	         $ipaddress = $SERVER['HTTP_FORWARDED_FOR'];
	     else if(@$SERVER['HTTP_FORWARDED'])
	         $ipaddress = $SERVER['HTTP_FORWARDED'];
	     else if(@$SERVER['REMOTE_ADDR'])
	         $ipaddress = $SERVER['REMOTE_ADDR'];
	     else
	         $ipaddress = 'UNKNOWN';

	     return $ipaddress; 
	}
    public static function getServerNameByServer($SERVER){
        return (@$SERVER["SERVER_NAME"]);
    }
    public static function getHttpUserAgentByServer($SERVER){
        return (@$SERVER['HTTP_USER_AGENT']);
    }
    public static function getRequestUriByServer($SERVER){
        return (@$SERVER["REQUEST_URI"]);
    }
    public static function getRequestTimeByServer($SERVER){
        return (@$SERVER['REQUEST_TIME']);
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
    /**
     * @param string $str 
     * @return string
     * Procura por trechos de scripts e também por palavrões na string, e ao encontrar substitui todas as ocorrências por "****"
     */
	public static function removerConteudoMalicioso(string $str): string{
		$scripts = array('<script','location.href');
		$palavroes = array('arrombad','bucet','bocet','blowjob','caralh','c*',' cu ',' cú ','cacete','cacetinho','cacetao','cacetaum','pênis','penis','foder','f****','fodase','fodasi','fodassi','fodassa','fodinha','fodao','fodaum','foda1','fodona','f***','fodeu','f****','fudeu','fodasse','fuckoff','fuckyou','filho da puta','filha da puta','filhodaputa','filhadaputa','gozo','goza','gozar','gozada','gozadanacara','m****','merdao','merdaum','merdinha','vadia','vasefoder','venhasefoder','voufoder','vasefuder','venhasefuder','voufuder','vaisefoder','vaisefuder','venhasefuder','vaisifude','v****','vaisifuder','vasifuder','vasefuder','vasefoder','pirigueti','piriguete','p****','porraloca','porraloka','porranacara','#@?$%~','putinha','putona','putassa','putao','punheta','putamerda','putaquepariu','putaquemepariu','#@?$%~','putavadia','putaqpariu','putaqpario','putaqparil','peido','peidar','xoxota','xota','xoxotinha','xerequinha','xereqinha','xerekinha','xoxotona','toma no c','toma no cú','toma no toba','tomar no cu','tomar no cú','tomar no toba','xvideos','porn','xana','caralho','kralho','pau no cu','pau no cú','pau no','f.o.d.a.s.e','porra ',' viado ','prostitut','sexo','boquete'
        );
        $qtdArray = count($scripts);
        for ($n = 0; $n != $qtdArray; $n++) {
            $str = str_ireplace($scripts[$n], "****", $str);
        }
        $qtdArray = count($palavroes);
        for ($n = 0; $n != $qtdArray; $n++) {
            $str = str_ireplace($palavroes[$n], "****", $str);
        }
        $str = strip_tags($str);

        return $str;
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
	public static function getNomeClasse($string) {
		$string = (ucwords($string, " _|\n\r\t\f\v"));
	    $string = Solves::removeEspacos($string);
	    $string = preg_replace("/_+/", '', $string);
	    return $string;
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
	public static function parsePutRequest(){
	    global $_PUT;

	    /* PUT data comes in on the stdin stream */
	    $putdata = fopen("php://input", "r");

	    /* Open a file for writing */
	    // $fp = fopen("myputfile.ext", "w");

	    $raw_data = '';

	    /* Read the data 1 KB at a time
	       and write to the file */
	    while ($chunk = fread($putdata, 1024))
	        $raw_data .= $chunk;

	    /* Close the streams */
	    fclose($putdata);

	    // Fetch content and determine boundary
	    $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

	    if(empty($boundary)){
	        parse_str($raw_data,$data);
	        $GLOBALS[ '_PUT' ] = $data;
	        return $GLOBALS[ '_PUT' ];
	    }

	    // Fetch each part
	    $parts = array_slice(explode($boundary, $raw_data), 1);
	    $data = array();

	    foreach ($parts as $part) {
	        // If this is the last part, break
	        if ($part == "--\r\n") break;

	        // Separate content from headers
	        $part = ltrim($part, "\r\n");
	        list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

	        // Parse the headers list
	        $raw_headers = explode("\r\n", $raw_headers);
	        $headers = array();
	        foreach ($raw_headers as $header) {
	            list($name, $value) = explode(':', $header);
	            $headers[strtolower($name)] = ltrim($value, ' ');
	        }

	        // Parse the Content-Disposition to get the field name, etc.
	        if (isset($headers['content-disposition'])) {
	            $filename = null;
	            $tmp_name = null;
	            preg_match(
	                '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
	                $headers['content-disposition'],
	                $matches
	            );
	            list(, $type, $name) = $matches;

	            //Parse File
	            if( isset($matches[4]) )
	            {
	                //if labeled the same as previous, skip
	                if( isset( $_FILES[ $matches[ 2 ] ] ) )
	                {
	                    continue;
	                }

	                //get filename
	                $filename = $matches[4];

	                //get tmp name
	                $filename_parts = pathinfo( $filename );
	                $tmp_name = tempnam( ini_get('upload_tmp_dir'), $filename_parts['filename']);

	                //populate $_FILES with information, size may be off in multibyte situation
	                $_FILES[ $matches[ 2 ] ] = array(
	                    'error'=>0,
	                    'name'=>$filename,
	                    'tmp_name'=>$tmp_name,
	                    'size'=>strlen( $body ),
	                    'type'=>$value
	                );

	                //place in temporary directory
	                file_put_contents($tmp_name, $body);
	            }
	            //Parse Field
	            else
	            {
	                $data[$name] = substr($body, 0, strlen($body) - 2);
	            }
	        }

	    }
	    $GLOBALS[ '_PUT' ] = $data;
	    return $GLOBALS[ '_PUT' ];
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
	    	if (!is_float($valor)) {
		        $valor = str_replace("R$", "", $valor);
		        $valor = str_replace(" ", "", $valor);
	        	$valor = preg_replace("/[^0-9,.]/", "", $valor);
				if(strpos($valor, '.')>0 && strpos($valor, ',')>0){
		        	$valor = str_replace(".", "", $valor);
				}
		        $valor = str_replace(",", ".", $valor);
		    }
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

	public static function stringComecaCom(string $string, string $startPart): bool{
	    return (preg_match("/^".$startPart."/", $string));
	}
	public static function stringTerminaCom(string $string,string  $startPart): bool{
	    return (preg_match("/".$startPart."^/", $string));
	}
	public static function validaEmail($mail){
	    //if(preg_match("/^([[:alnum:]_.-]){3,}@([[:lower:][:digit:]_.-]{3,})(.[[:lower:]]{2,3})(.[[:lower:]]{2})?$/", $mail)) 
		if(filter_var($mail, FILTER_VALIDATE_EMAIL)){
			return true;
		}
		return false;
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
	    return Solves::getCompleteUrl(true, $IS_APP, $url);
	}
	public static function getRootPathOrModule(){
	    return Solves::$ROOT_PATH_OR_MODULE;
	}
	public static function setRootPathOrModule($p){
	    Solves::$ROOT_PATH_OR_MODULE = $p;
	}
	public static function getCompleteUrl($root, $IS_APP, $url){
	    return (Solves::checkBoolean($root) ? self::getRootPathOrModule() : Solves::getSiteUrl()).(Solves::checkBoolean($IS_APP) ? 'app/' :'').$url;
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
	public static function removeBarraInicial($url){
		if($url[0]=='/'){$url = substr($url,1,strlen($url));}
		return $url;
	}
	public static function getNomeNormalizadoComUnderline(string $str, $lower=true){
		$pieces = preg_split('/(?=[A-Z])/',$str);
		$first = true;
		$result ='';
		foreach($pieces as $pc){			
			$result .= (!$first?'_':'').strtolower($pc);
			$first = false;
		}
		return $result;
	}
	public static function getUrlName($root,$url,$primeiraLetraMaiuscula=true){
	    if(Solves::isNotBlank($root)){
	        $url = substr($url, strlen($root), strlen($url));
	    }
	    $url = Solves::removeBarraInicial($url);
	    if(strpos($url, '?') !== false){$url = strstr($url, '?', true);}
	    if(strpos($url, '#') !== false){$url = strstr($url, '#', true);}
	    if(strpos($url, '.') !== false){$url = strstr($url, '.', true);}
	    return ($primeiraLetraMaiuscula ? ucfirst($url) : $url);
	}
	public static function getUrlNameArray($urlName){
		return explode('/',$urlName);
	}
	public static function getUrlNameViewPath($urlName, $navInside=''){
		$arrUrl = \Solves\Solves::getUrlNameArray($urlName);  
		$u = '/'.('app'==$arrUrl[0] ? 'app'.(count($arrUrl)>1 && Solves::isNotBlank($arrUrl[1]) ?'/'.$arrUrl[1].'':'') : $arrUrl[0]);  
	    $acumulado='';
	    foreach($arrUrl as $arrItem){
	      $acumulado .= '/'.$arrItem;
	      $pTemp = $navInside.'views'.$acumulado;
	      if(file_exists($pTemp.'.php')) {
	        $u = $acumulado;
	      }else if(!is_dir($pTemp)) {
	        break;
	      }else{
	      	$pTemp = str_replace('app', '', $pTemp);
	      	if(file_exists($pTemp.'.php')) {
		        $u = $acumulado;
		     }else if(!is_dir($pTemp)) {
		        break;
		    }
	      }
	    }
	    if('/app'==$u){
	    	$u = $u.'/home';
	    }
	    return $navInside.'views'.$u.'.php';
	}
}