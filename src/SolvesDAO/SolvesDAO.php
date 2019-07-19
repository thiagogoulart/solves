<?php
/*
Autor:  Thiago Goulart.
Data de criação: 19/07/2019
*/

namespace SolvesDAO;

class SolvesDAO {

	const SYSTEM_DB_TYPE_MYSQL = 'MYSQL';
	const SYSTEM_DB_TYPE_POSTGRESQL = 'POSTGRESQL';

	private static $DEBUG = false;
	private static $SYSTEM_DB_TYPE = 'MYSQL';

	private static $BD_DEV_HOST;
	private static $BD_DEV_PORT;
	private static $BD_DEV_URL;
	private static $BD_DEV_USER;
	private static $BD_DEV_PASSWORD;
	private static $BD_DEV_DATABASE;

	private static $BD_PROD_HOST;
	private static $BD_PROD_PORT;
	private static $BD_PROD_URL;
	private static $BD_PROD_USER;
	private static $BD_PROD_PASSWORD;
	private static $BD_PROD_DATABASE;


	public static function isDebug(){return SolvesDAO::$DEBUG;}
	public static function config($bdDevHost, $bdDevPort, $bdDevUrl, $bdDevUser, $bdDevPassword, $bdDevDatabase,
	$bdProdHost, $bdProdPort, $bdProdUrl, $bdProdUser, $bdProdPassword, $bdProdDatabase){
		SolvesDAO::configDev($bdDevHost, $bdDevPort, $bdDevUrl, $bdDevUser, $bdDevPassword, $bdDevDatabase);
		SolvesDAO::configProd($bdProdHost, $bdProdPort, $bdProdUrl, $bdProdUser, $bdProdPassword, $bdProdDatabase);
	}

    public static function configDev($bdDevHost, $bdDevPort, $bdDevUrl, $bdDevUser, $bdDevPassword, $bdDevDatabase){
    	SolvesDAO::setBdDevHost($bdDevHost);
	    SolvesDAO::setBdDevPort($bdDevPort);
	    SolvesDAO::setBdDevUrl($bdDevUrl);
	    SolvesDAO::setBdDevUser($bdDevUser);
	    SolvesDAO::setBdDevPassword($bdDevPassword);
	    SolvesDAO::setBdDevDatabase($bdDevDatabase);
    }
    public static function configProd($bdProdHost, $bdProdPort, $bdProdUrl, $bdProdUser, $bdProdPassword, $bdProdDatabase){
        SolvesDAO::setBdProdHost($bdProdHost);
        SolvesDAO::setBdProdPort($bdProdPort);
        SolvesDAO::setBdProdUrl($bdProdUrl);
        SolvesDAO::setBdProdUser($bdProdUser);
        SolvesDAO::setBdProdPassword($bdProdPassword);
        SolvesDAO::setBdProdDatabase($bdProdDatabase);
    }

    public static function setSystemDbTypeMySql(){SolvesDAO::$SYSTEM_DB_TYPE = SYSTEM_DB_TYPE_MYSQL;}
    public static function setSystemDbTypePostgresql(){SolvesDAO::$SYSTEM_DB_TYPE = SYSTEM_DB_TYPE_POSTGRESQL;}
    public static function isSystemDbTypeMySql(){return SYSTEM_DB_TYPE_MYSQL==SolvesDAO::$SYSTEM_DB_TYPE;}
    public static function isSystemDbTypePostgresql(){return SYSTEM_DB_TYPE_POSTGRESQL==SolvesDAO::$SYSTEM_DB_TYPE;}

    public static function setBdDevHost($p){SolvesDAO::$BD_DEV_HOST = $p;}
    public static function setBdDevPort($p){SolvesDAO::$BD_DEV_PORT = $p;}
    public static function setBdDevUrl($p){SolvesDAO::$BD_DEV_URL = $p;}
    public static function setBdDevUser($p){SolvesDAO::$BD_DEV_USER = $p;}
    public static function setBdDevPassword($p){SolvesDAO::$BD_DEV_PASSWORD = $p;}
    public static function setBdDevDatabase($p){SolvesDAO::$BD_DEV_DATABASE = $p;}
    
    public static function getBdDevHost(){return SolvesDAO::$BD_DEV_HOST;}
    public static function getBdDevPort(){return SolvesDAO::$BD_DEV_PORT;}
    public static function getBdDevUrl(){return SolvesDAO::$BD_DEV_URL;}
    public static function getBdDevUser(){return SolvesDAO::$BD_DEV_USER;}
    public static function getBdDevPassword(){return SolvesDAO::$BD_DEV_PASSWORD;}
    public static function getBdDevDatabase(){return SolvesDAO::$BD_DEV_DATABASE;}

    public static function setBdProdHost($p){SolvesDAO::$BD_PROD_HOST = $p;}
    public static function setBdProdPort($p){SolvesDAO::$BD_PROD_PORT = $p;}
    public static function setBdProdUrl($p){SolvesDAO::$BD_PROD_URL = $p;}
    public static function setBdProdUser($p){SolvesDAO::$BD_PROD_USER = $p;}
    public static function setBdProdPassword($p){SolvesDAO::$BD_PROD_PASSWORD = $p;}
    public static function setBdProdDatabase($p){SolvesDAO::$BD_PROD_DATABASE = $p;}
    
    public static function getBdProdHost(){return SolvesDAO::$BD_PROD_HOST;}
    public static function getBdProdPort(){return SolvesDAO::$BD_PROD_PORT;}
    public static function getBdProdUrl(){return SolvesDAO::$BD_PROD_URL;}
    public static function getBdProdUser(){return SolvesDAO::$BD_PROD_USER;}
    public static function getBdProdPassword(){return SolvesDAO::$BD_PROD_PASSWORD;}
    public static function getBdProdDatabase(){return SolvesDAO::$BD_PROD_DATABASE;}
		
	public static function openConnection() {
	    $CONNECTION = null;
	    $bd_host = '';
	    $bd_port = '';
	    $bd_url = '';
	    $bd_user = '';
	    $bd_passwd = '';
	    $bd_database = '';
	    if (\Solves\Solves::isDevMode()) {
	        $bd_host = SolvesDAO::getBdDevHost();
	        $bd_port = SolvesDAO::getBdDevPort();
	        $bd_url = SolvesDAO::getBdDevUrl();
	        $bd_user = SolvesDAO::getBdDevUser();
	        $bd_passwd = SolvesDAO::getBdDevPassword();
	        $bd_database = SolvesDAO::getBdDevDatabase();
	    } else if (\Solves\Solves::isProdMode()) {
            $bd_host = SolvesDAO::getBdProdHost();
            $bd_port = SolvesDAO::getBdProdPort();
            $bd_url = SolvesDAO::getBdProdUrl();
            $bd_user = SolvesDAO::getBdProdUser();
            $bd_passwd = SolvesDAO::getBdProdPassword();
            $bd_database = SolvesDAO::getBdProdDatabase();
	    }
	    $CONNECTION = SolvesDAO::connectDb($bd_host, $bd_port, $bd_url, $bd_user, $bd_passwd, $bd_database);
	    if(SolvesDAO::isDebug()){
	    	echo '[$bd_host:'.$bd_host.'], [$bd_port:'.$bd_port.'], [$bd_url:'.$bd_url.'], [$bd_user:'.$bd_user.'], [$bd_passwd:'.$bd_passwd.'], [$bd_database:'.$bd_database.']';
	    	var_dump( $CONNECTION);
	    }
	    return $CONNECTION;
	}

	private static function connectDb($bd_host, $bd_port, $bd_url, $bd_user, $bd_passwd, $bd_database) {
	    $CONNECTION = null;
	    if (SolvesDAO::isSystemDbTypeMySql()) {
	        $CONNECTION = new \mysqli($bd_host, $bd_user, $bd_passwd, $bd_database);
	        /* check connection */
	        if (mysqli_connect_errno()) {
	            printf("Erro na conexão: %s\n", mysqli_connect_error());
	            $CONNECTION = null;
	            return $CONNECTION;
	        }

	        /* change character set to utf8 */
	        if (!$CONNECTION->set_charset("utf8")) {
	            printf("Erro ao alterar charset para UTF-8: %s\n", $CONNECTION->error);
	            $CONNECTION = null;
	            return $CONNECTION;
	        } 

	    } else if (SolvesDAO::isSystemDbTypePostgresql()) {
	        $CONNECTION = pg_connect('host=' . $bd_host . ' port=' . $bd_port . ' dbname=' . $bd_database . ' user=' . $bd_user . ' password=' . $bd_passwd) or die("Erro na conexão com o Database PostgreSQL --> " . pg_last_error($CONNECTION));
	        pg_set_client_encoding($CONNECTION, 'utf8');
	    }
	    return $CONNECTION;
	}

	public static function closeConnection($con) {
	    if (isset($con)) {
	        if (SolvesDAO::isSystemDbTypeMySql()) {
	            @$con->close();
	            @mysqli_close($con);
	        } else if (SolvesDAO::isSystemDbTypePostgresql()) {
	            @pg_close($con);
	        }
	        $con = null;
	        return $con;
	    }
	}

}
?>