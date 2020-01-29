<?php
/*
Autor:  Thiago Goulart.
Data de criação: 19/07/2019
*/

namespace SolvesDAO;

class SolvesDAO {

    const SYSTEM_DB_TYPE_MYSQL = 'MYSQL';
    const SYSTEM_DB_TYPE_POSTGRESQL = 'POSTGRESQL';
    const DEFAULT_CONNECTION_NAME = 'DEFAULT';

    private static $DEBUG = false;

    private static $BDS_CONF_CONNECTIONS = [];

    private static $MODEL_CLASSES= array();


    public static function isDebug(){return SolvesDAO::$DEBUG;}
    public static function config(string $systemDbType, string $bdHost, string $bdPort, string $bdUrl, string $bdUser, string $bdPassword, string $bdDatabase){
        self::configBd(self::DEFAULT_CONNECTION_NAME, $systemDbType, $bdHost, $bdPort, $bdUrl, $bdUser, $bdPassword, $bdDatabase);
        SolvesDAO::$DEBUG = \Solves\Solves::isDebugMode();
    }
    public static function configBd(string $connectionName, string $systemDbType, string $bdHost, string $bdPort, string $bdUrl, string $bdUser, string $bdPassword, string $bdDatabase){
        self::$BDS_CONF_CONNECTIONS[$connectionName] = new SolvesDAOConfigConnection($connectionName, $systemDbType, $bdHost, $bdPort, $bdUrl, $bdUser, $bdPassword, $bdDatabase);
    }

    public static function setModelClasses($p){SolvesDAO::$MODEL_CLASSES = $p;}
    public static function getModelClasses(){return SolvesDAO::$MODEL_CLASSES;}

    public static function openConnection(?string $connectionName=null) {
    	$confBdCon = self::getConfBdConnection($connectionName);
	    return new SolvesDAOConnection($confBdCon);
	}
    public static function openConnectionMock(?string $connectionName=null) {
    	$confBdCon = self::getConfBdConnection($connectionName);
        return new SolvesDAOConnection($confBdCon, true);
    }
    public static function getConfBdConnection(?string $connectionName=null) {
    	if(null==$connectionName){
    		$connectionName = self::DEFAULT_CONNECTION_NAME;
    	}
        return (array_key_exists($connectionName, self::$BDS_CONF_CONNECTIONS) ? self::$BDS_CONF_CONNECTIONS[$connectionName] : null);
    }
    public static function closeConnection(SolvesDAOConnection $con) {
        if (isset($con)) {
            return $con->close();
        }
        return null;
    }

}
class SolvesDAOConfigConnection {
    protected $connectionName;
    protected $systemDbType;
    protected $bdHost;
    protected $bdPort;
    protected $bdUrl;
    protected $bdUser;
    protected $bdPassword;
    protected $bdDatabase;
    protected $isMysql=false;
    protected $isPostgres=false;

    public function __construct(string $connectionName, string $systemDbType, string $bdHost, string $bdPort, string $bdUrl, string $bdUser, string $bdPassword, string $bdDatabase){
        $this->isPostgres = (SolvesDAO::SYSTEM_DB_TYPE_POSTGRESQL==$systemDbType);
        $this->isMysql = (SolvesDAO::SYSTEM_DB_TYPE_MYSQL==$systemDbType);
        if(!$this->isPostgres && !$this->isMysql){
            throw new \Exception("Error Processing SolvesDAO. System DB Type not configured to ".$connectionName, 1);
        }
        $this->connectionName = $connectionName;
        $this->systemDbType = $systemDbType;
        $this->bdHost = $bdHost;
        $this->bdPort = $bdPort;
        $this->bdUrl = $bdUrl;
        $this->bdUser = $bdUser;
        $this->bdPassword = $bdPassword;
        $this->bdDatabase = $bdDatabase;
    }

    public function isSystemDbTypeMySql(): bool{return $this->isMysql;}
    public function isSystemDbTypePostgresql(): bool{return $this->isPostgres;}
    public function getBdConnectionName(): string {return $this->connectionName;}
    public function getBdHost(): string {return $this->bdHost;}
    public function getBdPort(): string {return $this->bdPort;}
    public function getBdUrl(): string {return $this->bdUrl;}
    public function getBdUser(): string {return $this->bdUser;}
    public function getBdPassword(): string {return $this->bdPassword;}
    public function getBdDatabase(): string {return $this->bdDatabase;}
}
?>