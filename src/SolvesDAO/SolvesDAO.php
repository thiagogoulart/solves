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

	private static $BD_HOST;
	private static $BD_PORT;
	private static $BD_URL;
	private static $BD_USER;
	private static $BD_PASSWORD;
	private static $BD_DATABASE;

    private static $MODEL_CLASSES= array();


	public static function isDebug(){return SolvesDAO::$DEBUG;}
	public static function config($systemDbType, $bdHost, $bdPort, $bdUrl, $bdUser, $bdPassword, $bdDatabase){
		if(self::SYSTEM_DB_TYPE_POSTGRESQL==$systemDbType){
			SolvesDAO::setSystemDbTypePostgresql();
		}else if(self::SYSTEM_DB_TYPE_MYSQL==$systemDbType){
			SolvesDAO::setSystemDbTypeMySql();
		}else{
			throw new Exception("Error Processing SolvesDAO. System DB Type not configured.", 1);			
		}
    	SolvesDAO::setBdHost($bdHost);
	    SolvesDAO::setBdPort($bdPort);
	    SolvesDAO::setBdUrl($bdUrl);
	    SolvesDAO::setBdUser($bdUser);
	    SolvesDAO::setBdPassword($bdPassword);
	    SolvesDAO::setBdDatabase($bdDatabase);
	    SolvesDAO::$DEBUG = \Solves\Solves::isDebugMode();
    }
    public static function setSystemDbTypeMySql(){SolvesDAO::$SYSTEM_DB_TYPE = self::SYSTEM_DB_TYPE_MYSQL;}
    public static function setSystemDbTypePostgresql(){SolvesDAO::$SYSTEM_DB_TYPE = self::SYSTEM_DB_TYPE_POSTGRESQL;}
    public static function isSystemDbTypeMySql(){return self::SYSTEM_DB_TYPE_MYSQL==SolvesDAO::$SYSTEM_DB_TYPE;}
    public static function isSystemDbTypePostgresql(){return self::SYSTEM_DB_TYPE_POSTGRESQL==SolvesDAO::$SYSTEM_DB_TYPE;}

    public static function setBdHost($p){SolvesDAO::$BD_HOST = $p;}
    public static function setBdPort($p){SolvesDAO::$BD_PORT = $p;}
    public static function setBdUrl($p){SolvesDAO::$BD_URL = $p;}
    public static function setBdUser($p){SolvesDAO::$BD_USER = $p;}
    public static function setBdPassword($p){SolvesDAO::$BD_PASSWORD = $p;}
    public static function setBdDatabase($p){SolvesDAO::$BD_DATABASE = $p;}
    public static function setModelClasses($p){SolvesDAO::$MODEL_CLASSES = $p;}
    
    public static function getBdHost(){return SolvesDAO::$BD_HOST;}
    public static function getBdPort(){return SolvesDAO::$BD_PORT;}
    public static function getBdUrl(){return SolvesDAO::$BD_URL;}
    public static function getBdUser(){return SolvesDAO::$BD_USER;}
    public static function getBdPassword(){return SolvesDAO::$BD_PASSWORD;}
    public static function getBdDatabase(){return SolvesDAO::$BD_DATABASE;}
    public static function getModelClasses(){return SolvesDAO::$MODEL_CLASSES;}
    
	public static function openConnection() {
	    return new SolvesDAOConnection();
	}
	public static function openConnectionMock() {
	    return new SolvesDAOConnection(true);
	}

	public static function closeConnection(SolvesDAOConnection $con) {
	    if (isset($con)) {
	        return $con->close();
	    }
	    return null;
	}

}
?>