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

    private static $MODEL_CLASSES= array();


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
    public static function setModelClasses($p){SolvesDAO::$MODEL_CLASSES = $p;}
    
    public static function getBdDevHost(){return SolvesDAO::$BD_DEV_HOST;}
    public static function getBdDevPort(){return SolvesDAO::$BD_DEV_PORT;}
    public static function getBdDevUrl(){return SolvesDAO::$BD_DEV_URL;}
    public static function getBdDevUser(){return SolvesDAO::$BD_DEV_USER;}
    public static function getBdDevPassword(){return SolvesDAO::$BD_DEV_PASSWORD;}
    public static function getBdDevDatabase(){return SolvesDAO::$BD_DEV_DATABASE;}
    public static function getModelClasses(){return SolvesDAO::$MODEL_CLASSES;}

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
	    return new SolvesDAOConnection();
	}

	public static function closeConnection(SolvesDAOConnection $con) {
	    if (isset($con)) {
	        return $con->close();
	    }
	    return null;
	}

}
?>