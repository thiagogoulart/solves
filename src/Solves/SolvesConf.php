<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 09/12/2019
 */
namespace Solves;

class SolvesConf {

    public static $DEFAULT_CHARSET = 'UTF-8';
    public static $SYSTEM_DEBUG_MODE=false;
    public static $LIMIT_PAGINATION = 20;
    public static $LIMIT_UPLOAD = (1024 * 1024 * 2);

    /** Configuração dados de identificação */
    /** @var \Solves\SolvesConfIdentificacao */
    private static $SOLVES_CONF_IDENTIFICACAO;
    public static function setSolvesConfIdentificacao(\Solves\SolvesConfIdentificacao $identificacao){
        self::$SOLVES_CONF_IDENTIFICACAO = $identificacao;
    }
    public static function getSolvesConfIdentificacao() :\Solves\SolvesConfIdentificacao {
        return self::$SOLVES_CONF_IDENTIFICACAO;
    }

    /** Configuração dados de UI */
    /** @var \Solves\SolvesConfUi */
    private static $SOLVES_CONF_UI;
    public static function setSolvesConfUi(\Solves\SolvesConfUi $confUi){
        self::$SOLVES_CONF_UI = $confUi;
    }
    public static function getSolvesConfUi() :\Solves\SolvesConfUi {
        return self::$SOLVES_CONF_UI;
    }

    /** Configuração dados de endereços e URLS */
    /** @var \Solves\SolvesConfUrls */
    private static $SOLVES_CONF_URLS;
    public static function setSolvesConfUrls(\Solves\SolvesConfUrls $urls){
        self::$SOLVES_CONF_URLS = $urls;
        if(null==self::$SOLVES_CONF_URLS->getSolvesConfUrlAtivo()){
            self::$SOLVES_CONF_URLS->setDevUrlEnvironment();
        }
    }
    public static function getSolvesConfUrls() :\Solves\SolvesConfUrls {
        return self::$SOLVES_CONF_URLS;
    }

    /** Configuração de acesso ao banco */
    /** @var \Solves\SolvesConfBD */
    private static $SOLVES_CONF_BD;
    public static function setSolvesConfBd(\Solves\SolvesConfBD $bdConf){
        self::$SOLVES_CONF_BD = $bdConf;
    }
    public static function getSolvesConfBd() :\Solves\SolvesConfBD {
        return self::$SOLVES_CONF_BD;
    }
    private static $SOLVES_CONF_BD_ADICIONAIS = [];
    public static function addSolvesConfBdAdicional(string $keyName, \Solves\SolvesConfBD $bdConf){
        self::$SOLVES_CONF_BD_ADICIONAIS[$keyName] = $bdConf;
    }
    public static function getSolvesConfBdAdicional(string $keyName) : ?\Solves\SolvesConfBD {
        return self::$SOLVES_CONF_BD_ADICIONAIS[$keyName];
    }

    /** Configuração de Pagamento */
    /** @var \Solves\SolvesConfPayMethod */
    private static $SOLVES_CONF_PAY;
    public static function setSolvesConfPay(\Solves\SolvesConfPayMethod $payConf){
        self::$SOLVES_CONF_PAY = $payConf;
    }
    public static function getSolvesConfPay() :\Solves\SolvesConfPayMethod {
        return self::$SOLVES_CONF_PAY;
    }

    /** Configuração dados de E-mail (Envio e recebimento) */
    /** @var \Solves\SolvesConfEmails */
    private static $SOLVES_CONF_EMAILS;
    public static function setSolvesConfEmails(\Solves\SolvesConfEmails $confEmails){
        self::$SOLVES_CONF_EMAILS = $confEmails;
    }
    public static function getSolvesConfEmails() :\Solves\SolvesConfEmails {
        return self::$SOLVES_CONF_EMAILS;
    }

    /** Configuração dados de Firebase */
    /** @var \Solves\SolvesConfFirebase */
    private static $SOLVES_CONF_FIREBASE;
    public static function setSolvesConfFirebase(\Solves\SolvesConfFirebase $confFirebase){
        self::$SOLVES_CONF_FIREBASE = $confFirebase;
    }
    public static function getSolvesConfFirebase() :\Solves\SolvesConfFirebase {
        return self::$SOLVES_CONF_FIREBASE;
    }

    /** Configuração dados de Notificações */
    /** @var \Solves\SolvesConfNotifications */
    private static $SOLVES_CONF_NOTIFICATIONS;
    public static function setSolvesConfNotifications(\Solves\SolvesConfNotifications $confNotifications){
        self::$SOLVES_CONF_NOTIFICATIONS = $confNotifications;
    }
    public static function getSolvesConfNotifications() :\Solves\SolvesConfNotifications {
        return self::$SOLVES_CONF_NOTIFICATIONS;
    }


    /** Configuração dados de ResponsiveFileManager */
    /** @var \Solves\SolvesConfResponsiveFileManager */
    private static $SOLVES_CONF_RESPONSIVE_FILEMANAGER;
    public static function setSolvesConfResponsiveFileManager(\Solves\SolvesConfResponsiveFileManager $confresponsiveFilemanager){
        self::$SOLVES_CONF_RESPONSIVE_FILEMANAGER = $confresponsiveFilemanager;
    }
    public static function getSolvesConfResponsiveFileManager() :\Solves\SolvesConfNotifications {
        return self::$SOLVES_CONF_RESPONSIVE_FILEMANAGER;
    }

    /** Configuração dados de WebSocket */
    /** @var \Solves\SolvesConfWebSocket */
    private static $SOLVES_CONF_WEBSOCKET;
    public static function setSolvesConfWebSocket(\Solves\SolvesConfWebSocket $ws){
        self::$SOLVES_CONF_WEBSOCKET = $ws;
    }
    public static function getSolvesConfWebSocket() :\Solves\SolvesConfWebSocket {
        return self::$SOLVES_CONF_WEBSOCKET;
    }

    /** Configuração dados de Telegram */
    /** @var \Solves\SolvesConfTelegram */
    private static $SOLVES_CONF_TELEGRAM;
    public static function setSolvesConfTelegram(\Solves\SolvesConfTelegram $t){
        self::$SOLVES_CONF_TELEGRAM = $t;
    }
    public static function getSolvesConfTelegram() :\Solves\SolvesConfTelegram {
        return self::$SOLVES_CONF_TELEGRAM;
    }

    public static function build(){
        \Solves\Solves::config(self::$SOLVES_CONF_IDENTIFICACAO->getSystemName(),
            self::$SOLVES_CONF_IDENTIFICACAO->getVersao(),
            self::$SOLVES_CONF_IDENTIFICACAO->getSiteTitulo(),
            self::$SOLVES_CONF_URLS->getSolvesConfUrl()->getSiteUrl(),
            self::$SOLVES_CONF_IDENTIFICACAO->getSiteDesc(),
            self::$SOLVES_CONF_IDENTIFICACAO->getSiteKeys(),
            self::$SOLVES_CONF_URLS::IMG,
            self::$SOLVES_CONF_URLS::IMG_LOGO);
        \Solves\Solves::setRootPathOrModule(self::$SOLVES_CONF_URLS->getSolvesConfUrl()->getSiteContext());
        if(\Solves\Solves::isNotBlank(self::$SOLVES_CONF_IDENTIFICACAO->getSiteAuthor())) {
            \SolvesUi\SolvesCabecalho::setAuthor(self::$SOLVES_CONF_IDENTIFICACAO->getSiteAuthor());
        }
        if(\Solves\Solves::isNotBlank(self::$SOLVES_CONF_IDENTIFICACAO->getTwitterCreator())) {
            \SolvesUi\SolvesCabecalho::setTwitterCreator(self::$SOLVES_CONF_IDENTIFICACAO->getTwitterCreator());
        }
        \Solves\Solves::setRestUrl(self::$SOLVES_CONF_URLS->getSolvesConfUrl()->getRestUrl());
        \Solves\Solves::setSiteContext(self::$SOLVES_CONF_URLS->getSolvesConfUrl()->getSiteContext());
        //App Stores
        \Solves\Solves::setGooglePlayStoreLink(self::$SOLVES_CONF_URLS->getAppGooglePlayStoreLink());
        \Solves\Solves::setAppleStoreLink(self::$SOLVES_CONF_URLS->getAppAppleStoreLink());
        \Solves\Solves::setWindowsStoreLink(self::$SOLVES_CONF_URLS->getAppWindowsStoreLink());

        if(isset(self::$SOLVES_CONF_FIREBASE)){
            \Solves\Solves::configAuth(self::$SOLVES_CONF_FIREBASE->getFirebaseConfigJsonFile(),
                self::$SOLVES_CONF_FIREBASE->getFirebaseConfigUser());
            \SolvesUi\SolvesConfigJS::setFirebaseJsonConfig(self::$SOLVES_CONF_FIREBASE->getFirebaseConfigJsonConfig());
            \SolvesUi\SolvesConfigJS::useFirebaseAuthGoogle(self::$SOLVES_CONF_FIREBASE->getFirebaseConfigAuthGoogle());
            if(self::$SOLVES_CONF_FIREBASE->isUseAuthFacebook()){
                \SolvesUi\SolvesConfigJS::useFirebaseAuthFacebook();
            }
        }
        if(isset(self::$SOLVES_CONF_EMAILS)){
            \Solves\Solves::configMail(self::$SOLVES_CONF_EMAILS->getEmailHost(),
                self::$SOLVES_CONF_EMAILS->getEmailPort(),
                self::$SOLVES_CONF_EMAILS->getEmailRemetente(),
                self::$SOLVES_CONF_EMAILS->getEmailRemetentePassword(),
                self::$SOLVES_CONF_IDENTIFICACAO->getSiteTitulo());
        }
        if(isset(self::$SOLVES_CONF_BD)){
            \Solves\Solves::configDAO(self::$SOLVES_CONF_BD->getSystemDbType(),
                self::$SOLVES_CONF_BD->getHost(),
                self::$SOLVES_CONF_BD->getPort(),
                self::$SOLVES_CONF_BD->getUrl(),
                self::$SOLVES_CONF_BD->getUser(),
                self::$SOLVES_CONF_BD->getPassword(),
                self::$SOLVES_CONF_BD->getDatabaseName());

            if(isset(self::$SOLVES_CONF_BD_ADICIONAIS)){
                foreach (self::$SOLVES_CONF_BD_ADICIONAIS as $key => $confBd) {
                    \Solves\Solves::configDAOAdicional($key, $confBd->getSystemDbType(),
                        $confBd->getHost(), $confBd->getPort(), $confBd->getUrl(), $confBd->getUser(),
                        $confBd->getPassword(), $confBd->getDatabaseName());
                }
            }
        }
        if(isset(self::$SOLVES_CONF_NOTIFICATIONS)){
            \Solves\Solves::configNotifications(
                SolvesConfNotifications::NOTIFICATIONS_SUBSCRIPTION_URL,
                self::$SOLVES_CONF_NOTIFICATIONS->getPublicKey(),
                self::$SOLVES_CONF_NOTIFICATIONS->getPrivateKey(),
                self::$SOLVES_CONF_NOTIFICATIONS->getSenderId());
        }
        if(isset(self::$SOLVES_CONF_UI)){
            \Solves\Solves::configUi(self::$SOLVES_CONF_UI->getUiCssList(),
                self::$SOLVES_CONF_UI->getScriptsFilePaths(),
                self::$SOLVES_CONF_UI->getUiThemeBackgroundColor(),
                self::$SOLVES_CONF_UI->getUiThemeColor(),
                self::$SOLVES_CONF_UI->getUiVersion());
            if(\Solves\Solves::isNotBlank(self::$SOLVES_CONF_UI->getLogoOnLoading())){
                \SolvesUi\SolvesCabecalho::setLogoOnLoading(self::$SOLVES_CONF_UI->getLogoOnLoading());
            }
            \SolvesUi\SolvesCabecalho::config(self::$SOLVES_CONF_UI->getScriptAnalytics());
        }
        if(isset(self::$SOLVES_CONF_WEBSOCKET)){
            \SolvesWebsocket\SolvesWebsocketServer::config(self::$SOLVES_CONF_WEBSOCKET->getUrl(), self::$SOLVES_CONF_WEBSOCKET->getHost(), self::$SOLVES_CONF_WEBSOCKET->getPort(), self::$SOLVES_CONF_WEBSOCKET->isBuildApp());
            $routesConf = self::$SOLVES_CONF_WEBSOCKET->getRoutes();
            foreach($routesConf as $route){
                \SolvesWebsocket\SolvesWebsocketServer::addConfigRoute($route->getName(), $route->getPath());
            }
        }
        if(isset(self::$SOLVES_CONF_PAY)){
            $paypalConf = self::$SOLVES_CONF_PAY->getConfPaypalMethod();
            if(isset($paypalConf)){
                \SolvesPay\SolvesPayPaypal::config($paypalConf->getPaypalUsuario(), $paypalConf->getPaypalSenha(),
                    $paypalConf->getPaypalAssinatura(), $paypalConf->getPaypalEmail(), $paypalConf->getPaypalBrandName(),
                    $paypalConf->getPaypalNotifyUrl(), $paypalConf->getPaypalReturnUrl(), $paypalConf->getPaypalReturnUrl(), $paypalConf->getPaypalPathLog());
            }
            $pagseguroConf = self::$SOLVES_CONF_PAY->getConfPagseguroMethod();
            if(isset($pagseguroConf)) {
                //TODO configurar PAGSEGURO
                // \SolvesPay\SolvesPayPagseguro::config
            }
        }

    }
}
class SolvesConfIdentificacao{
    private $systemName ='';
    private $systemNameCdn ='';
    private $versao = '0.0';
    private $siteTitulo='';
    private $siteDesc='';
    private $siteKeys='';
    private $siteAuthor='';
    private $twitterCreator='';
    private $defaultEmpresaId = 1;
    private $defaultSistemaUsuarioId = 0;
    /**
     * SolvesConfIdentificacao constructor.
     * @param string $systemName
     * @param string $systemNameCdn
     * @param string $versao
     * @param string $siteTitulo
     * @param string $siteDesc
     * @param string $siteKeys
     * @param int $defaultEmpresaId
     * @param int $defaultSistemaUsuarioId
     */
    public function __construct($systemName,$systemNameCdn, $versao, $siteTitulo, $siteDesc, $siteKeys,
                                int $defaultEmpresaId=null, int $defaultSistemaUsuarioId=null)   {
        $this->systemName = $systemName;
        $this->systemNameCdn = $systemNameCdn;
        $this->versao = $versao;
        $this->siteTitulo = $siteTitulo;
        $this->siteDesc = $siteDesc;
        $this->siteKeys = $siteKeys;
        if(\Solves\Solves::isNotBlank($defaultEmpresaId)){$this->defaultEmpresaId = $defaultEmpresaId;}
        if(\Solves\Solves::isNotBlank($defaultSistemaUsuarioId)){$this->defaultSistemaUsuarioId = $defaultSistemaUsuarioId;}
    }

    /**
     * @return string
     */
    public function getSystemName(): string
    {
        return $this->systemName;
    }

    /**
     * @return string
     */
    public function getSystemNameCdn(): string
    {
        return $this->systemNameCdn;
    }

    /**
     * @return string
     */
    public function getVersao(): string
    {
        return $this->versao;
    }

    /**
     * @return string
     */
    public function getSiteTitulo(): string
    {
        return $this->siteTitulo;
    }

    /**
     * @return string
     */
    public function getSiteDesc(): string
    {
        return $this->siteDesc;
    }

    /**
     * @return string
     */
    public function getSiteKeys(): string
    {
        return $this->siteKeys;
    }

    /**
     * @return int
     */
    public function getDefaultEmpresaId(): int
    {
        return $this->defaultEmpresaId;
    }

    /**
     * @return int
     */
    public function getDefaultSistemaUsuarioId(): int
    {
        return $this->defaultSistemaUsuarioId;
    }

    /**
     * @return string
     */
    public function getSiteAuthor(): string
    {
        return $this->siteAuthor;
    }

    /**
     * @param string $siteAuthor
     * @return SolvesConfIdentificacao
     */
    public function setSiteAuthor(string $siteAuthor): SolvesConfIdentificacao
    {
        $this->siteAuthor = $siteAuthor;
        return $this;
    }

    /**
     * @return string
     */
    public function getTwitterCreator(): string
    {
        return $this->twitterCreator;
    }

    /**
     * @param string $twitterCreator
     * @return SolvesConfIdentificacao
     */
    public function setTwitterCreator(string $twitterCreator): SolvesConfIdentificacao
    {
        $this->twitterCreator = $twitterCreator;
        return $this;
    }

}
class SolvesConfUi{
    private $uiVersion;
    private $uiThemeBackgroundColor = '#FFFFFF';
    private $uiThemeColor;
    private $scriptsFilePaths = array();
    private $uiCssList = array();
    private $scriptAnalytics='';
    private $googleAnalyticsIdCode=null;
    private $logoOnLoading='';

    /**
     * SolvesUi constructor.
     * @param $uiVersion
     * @param string $uiThemeBackgroundColor
     * @param $uiThemeColor
     */
    public function __construct($uiVersion, string $uiThemeBackgroundColor, $uiThemeColor)
    {
        $this->uiVersion = $uiVersion;
        $this->uiThemeBackgroundColor = $uiThemeBackgroundColor;
        $this->uiThemeColor = $uiThemeColor;
    }

    /**
     * @return mixed
     */
    public function getUiVersion()
    {
        return $this->uiVersion;
    }

    /**
     * @return string
     */
    public function getUiThemeBackgroundColor(): string
    {
        return $this->uiThemeBackgroundColor;
    }

    /**
     * @return mixed
     */
    public function getUiThemeColor()
    {
        return $this->uiThemeColor;
    }

    /**
     * @return array
     */
    public function getScriptsFilePaths(): array
    {
        return $this->scriptsFilePaths;
    }

    /**
     * @param array $scriptsFilePaths
     * @return SolvesConfUi
     */
    public function setScriptsFilePaths(array $scriptsFilePaths): SolvesConfUi
    {
        $this->scriptsFilePaths = $scriptsFilePaths;
        return $this;
    }

    /**
     * @return array
     */
    public function getUiCssList(): array
    {
        return $this->uiCssList;
    }

    /**
     * @param \SolvesUi\SolvesUiCss $uiCss
     * @return SolvesConfUi
     */
    public function addUiCss(\SolvesUi\SolvesUiCss $uiCss): SolvesConfUi
    {
        $this->uiCssList[] = $uiCss;
        return $this;
    }

    /**
     * @return string
     */
    public function getScriptAnalytics(): string
    {
        return $this->scriptAnalytics;
    }

    /**
     * @param string $scriptAnalytics
     * @return SolvesConfUi
     */
    public function setScriptAnalytics(string $scriptAnalytics): SolvesConfUi
    {
        $this->scriptAnalytics = $scriptAnalytics;
        return $this;
    }

    /**
     * @return string
     */
    public function getGoogleAnalyticsIdCode(): string
    {
        return $this->googleAnalyticsIdCode;
    }

    /**
     * @param string $scriptAnalytics
     * @return SolvesConfUi
     */
    public function setGoogleAnalyticsIdCode(string $googleAnalyticsIdCode): SolvesConfUi
    {
        $this->googleAnalyticsIdCode = $googleAnalyticsIdCode;
        $this->scriptAnalytics = "<!-- Global site tag (gtag.js) - Google Analytics -->".
            "<script async src=\"https://www.googletagmanager.com/gtag/js?id=".$this->googleAnalyticsIdCode."\"></script>".
            "<script>".
            "window.dataLayer = window.dataLayer || [];".
            "function gtag(){dataLayer.push(arguments);}".
            "gtag('js', new Date());".
            "gtag('config', '".$this->googleAnalyticsIdCode."');".
            "</script>
";
        return $this;
    }

    /**
     * @return string
     */
    public function getLogoOnLoading(){return $this->logoOnLoading;}

    /**
     * @param string $logo
     * @return SolvesConfUi
     */
    public function setLogoOnLoading($logo){
        $this->logoOnLoading = $logo;
        return $this;
    }

}
class SolvesConfUrl{
    private $siteUrl;
    private $siteDir;
    private $siteContext;
    private $useHttps=true;
    private $restUrl;
    private $restUseHttps=true;

    private $pathRaiz;
    private $urlRaiz;

    /**
     * SolvesConfUrl constructor.
     * @param $siteUrl
     * @param $siteContext
     * @param bool $useHttps
     * @param bool $restUseHttps
     * @param $restUrl
     * @param $pathRaiz
     * @param $urlRaiz
     */
    public function __construct($siteUrl, $siteContext, $useHttps, $restUseHttps, $restUrl=null, $pathRaiz=null, $urlRaiz=null)  {
        $this->siteUrl = $siteUrl;
        $this->siteContext = $siteContext;
        $this->useHttps = $useHttps;
        $this->restUseHttps = $restUseHttps;
        $this->restUrl = $restUrl;
        $this->pathRaiz = $pathRaiz;
        $this->urlRaiz = $urlRaiz;

        $this->siteDir = $this->siteUrl;
        $this->siteUrl = ($this->useHttps ?'https://':'http://').$this->siteDir.$this->siteContext;
        if(\Solves\Solves::isNotBlank($this->restUrl)){
            $this->restUrl = (($this->restUseHttps ?'https://':'http://').$this->restUrl);
        }else{
            $this->restUrl = (($this->restUseHttps ?'https://':'http://').$this->siteDir.$this->siteContext);
        }
    }

    /**
     * @return mixed
     */
    public function getSiteUrl()    {
        return $this->siteUrl;
    }

    /**
     * @return mixed
     */
    public function getSiteDir()    {
        return $this->siteDir;
    }


    /**
     * @param mixed $siteUrl
     * @return SolvesConfUrl
     */
    public function setSiteUrl($siteUrl)
    {
        $this->siteUrl = $siteUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSiteContext()
    {
        return $this->siteContext;
    }

    /**
     * @param mixed $siteContext
     * @return SolvesConfUrl
     */
    public function setSiteContext($siteContext)
    {
        $this->siteContext = $siteContext;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseHttps()
    {
        return $this->useHttps;
    }

    /**
     * @param bool $useHttps
     * @return SolvesConfUrl
     */
    public function setUseHttps($useHttps)
    {
        $this->useHttps = $useHttps;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRestUrl()
    {
        return $this->restUrl;
    }

    /**
     * @param mixed $restUrl
     * @return SolvesConfUrl
     */
    public function setRestUrl($restUrl)
    {
        $this->restUrl = $restUrl;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRestUseHttps()
    {
        return $this->restUseHttps;
    }

    /**
     * @param bool $restUseHttps
     * @return SolvesConfUrl
     */
    public function setRestUseHttps($restUseHttps)
    {
        $this->restUseHttps = $restUseHttps;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPathRaiz(){
        return $this->pathRaiz;
    }

    /**
     * @return mixed
     */
    public function getUrlRaiz(?bool $getValidOne=false){
        if($getValidOne){
            return (\Solves\Solves::isNotBlank($this->urlRaiz) ? $this->urlRaiz :$this->siteUrl);
        }
        return $this->urlRaiz;
    }

    public function checkIfIsThisEnvironment($host) :bool {
        if($host==$this->siteDir){
            return true;
        }
        $islocal = ('127.0.0.1'==$host || 'localhost'==$host);
        $islocalServer = ('127.0.0.1'==$this->siteDir || 'localhost'==$this->siteDir);
        return ($islocal && $islocalServer);
    }

}
class SolvesConfUrls{
    const DS = '/';

    const SYSTEM_MODE_DEV = 'DEV';
    const SYSTEM_MODE_HML = 'HML';
    const SYSTEM_MODE_PROD = 'PROD';

    const ASSETS ='/assets/';
    const IMG ='/assets/img/';
    const IMG_LOGO ='/assets/img/logo/';
    const CSS ='/assets/css/';
    const JS = '/assets/js/';

//Solves CDN
    const SOLVES_CDN = 'https://cdn.solves.com.br/';
    const SOLVES_CDN_JS = 'https://cdn.solves.com.br/js/';
    const SOLVES_CDN_CSS = 'https://cdn.solves.com.br/css/';
    const SOLVES_CDN_IMG = 'https://cdn.solves.com.br/img/';
    const SOLVES_CDN_LIB = 'https://cdn.solves.com.br/lib/';

//Solves LOCAL CDN
    const SOLVES_LOCAL_CDN = '/vendor/thiagogoulart/solves_cdn/';
    const SOLVES_LOCAL_CDN_JS = '/vendor/thiagogoulart/solves_cdn/js/';
    const SOLVES_LOCAL_CDN_CSS = '/vendor/thiagogoulart/solves_cdn/css/';
    const SOLVES_LOCAL_CDN_IMG = '/vendor/thiagogoulart/solves_cdn/img/';
    const SOLVES_LOCAL_CDN_LIB = '/vendor/thiagogoulart/solves_cdn/lib/';

    private $host;
    private $acessoRestritoTipo;
    private $modoSoonAtivado = false;

    private $unix = false;
    private $pathRoot;
    private $pathRootRelative;

    private $publicFolderRelative = '/public/';
    private $publicFolder;
    private $publicFolderImagesName = 'images';
    private $publicFolderImagesRelative;
    private $publicFolderImages;
    private $publicFolderThumbsName = 'thumbs';
    private $publicFolderThumbsRelative;
    private $publicFolderThumbs;
    private $publicFolderDocsName = 'docs';
    private $publicFolderDocsRelative;
    private $publicFolderDocs;

    private $isModeDev=false;
    private $isModeHml=false;
    private $isModeProd=false;

    private $solvesConfIdentificacao;

    private $solvesConfUrlAtivo;
    private $solvesConfUrlDev;
    private $solvesConfUrlHml;
    private $solvesConfUrlProd;

    private $jsRest;
    private $jsModel;
    private $jsView;
    private $cdnApp;
    private $localCdn;
    private $localCdnApp;
    private $localCdnAddress;
    private $localCdnAppAddress;

    private $appGooglePlayStoreLink = '';
    private $appAppleStoreLink = '';
    private $appWindowsStoreLink = '';

    /**
     * SolvesConfUrls constructor.
     * @param \Solves\SolvesConfIdentificacao $solvesConfIdentificacao
     * @param string $host
     * @param string $acessoRestritoTipo
     * @param bool $modoSoonAtivado
     */
    public function __construct(\Solves\SolvesConfIdentificacao $solvesConfIdentificacao,
                                $host, $acessoRestritoTipo='', bool $modoSoonAtivado=false){
        $this->solvesConfIdentificacao = $solvesConfIdentificacao;
        $this->host = $host;
        $this->acessoRestritoTipo = $acessoRestritoTipo;
        $this->modoSoonAtivado = $modoSoonAtivado;

        $this->pathRoot = str_replace('\\','/', dirname(__FILE__).self::DS);
    }

    public function getJsRest(){return $this->jsRest;}
    public function getJsModel(){return $this->jsModel;}
    public function getJsView(){return $this->jsView;}
    public function getCdnApp(){return $this->cdnApp;}
    public function getLocalCdn(){return $this->localCdn;}
    public function getLocalCdnAddress(){return $this->localCdnAddress;}
    public function getLocalCdnApp(){return $this->localCdnApp;}
    public function getLocalCdnAppAddress(){return $this->localCdnAppAddress;}

    public function getSolvesConfUrl() :\Solves\SolvesConfUrl {
        if($this->isModeProd){
            return $this->solvesConfUrlProd;
        }else if($this->isModeHml){
            return $this->solvesConfUrlHml;
        }
        return $this->solvesConfUrlDev;
    }


    public function isModeDev() :bool {return $this->isModeDev;}
    public function isModeHml() :bool {return $this->isModeHml;}
    public function isModeProd() :bool {return $this->isModeProd;}

    public function setDevUrlEnvironment(){
        $this->fillAttrs($this->solvesConfUrlDev);
    }

    public function getSolvesConfUrlDev() :\Solves\SolvesConfUrl {return $this->solvesConfUrlDev;}
    public function setSolvesConfUrlDev(\Solves\SolvesConfUrl $solvesConfUrlDev){
        $this->solvesConfUrlDev = $solvesConfUrlDev;
        $this->isModeDev = $this->solvesConfUrlDev->checkIfIsThisEnvironment($this->host);
        if($this->isModeDev){
            $this->fillAttrs($this->solvesConfUrlDev);
        }
    }

    public function getSolvesConfUrlHml() :\Solves\SolvesConfUrl {return $this->solvesConfUrlHml;}
    public function setSolvesConfUrlHml(\Solves\SolvesConfUrl $solvesConfUrlHml){
        $this->solvesConfUrlHml = $solvesConfUrlHml;
        $this->isModeHml = $this->solvesConfUrlHml->checkIfIsThisEnvironment($this->host);
        if($this->isModeHml){
            $this->fillAttrs($this->solvesConfUrlHml);
        }
    }

    public function getSolvesConfUrlProd() :\Solves\SolvesConfUrl {return $this->solvesConfUrlProd;}
    public function setSolvesConfUrlProd(\Solves\SolvesConfUrl $solvesConfUrlProd){
        $this->solvesConfUrlProd = $solvesConfUrlProd;
        $this->isModeProd = $this->solvesConfUrlProd->checkIfIsThisEnvironment($this->host);
        if($this->isModeProd){
            $this->fillAttrs($this->solvesConfUrlProd);
        }
    }

    public function fillAttrs(\Solves\SolvesConfUrl $solvesConfUrl){
        $this->solvesConfUrlAtivo = $solvesConfUrl;
        $this->jsRest = $solvesConfUrl->getSiteContext().Solves::removeBarraInicial(self::JS).'rest/';
        $this->jsModel = $solvesConfUrl->getSiteContext().Solves::removeBarraInicial(self::JS).'model/';
        $this->jsView = $solvesConfUrl->getSiteContext().Solves::removeBarraInicial(self::JS).'view/';
        $this->pathRootRelative = ('../'.$solvesConfUrl->getSiteDir().'/');

        //filling values in Public folders vars
        $this->publicFolder = $this->getPathRaiz().'public/';
        $this->publicFolderImagesRelative = $this->publicFolderRelative.$this->publicFolderImagesName.'/';
        $this->publicFolderImages = $this->publicFolder.$this->publicFolderImagesName.'/';
        $this->publicFolderThumbsRelative = '/'.$this->publicFolderThumbsName.'/';
        $this->publicFolderThumbs = $this->getPathRaiz().$this->publicFolderThumbsName.'/';
        $this->publicFolderDocsRelative =  $this->publicFolderRelative.$this->publicFolderDocsName.'/';
        $this->publicFolderDocs = $this->publicFolder.$this->publicFolderDocsName.'/';

        $dirCdnInApp = 'apps/'.$this->solvesConfIdentificacao->getSystemNameCdn().'/'.$this->solvesConfIdentificacao->getVersao().'/';
        //CDN app
        $this->cdnApp = self::SOLVES_CDN.$dirCdnInApp;
        //LOCAL CDN app
        $this->localCdn = $this->getPathRaiz().Solves::removeBarraInicial(self::SOLVES_LOCAL_CDN);
        $this->localCdnAppSemPathRaiz = Solves::removeBarraInicial(self::SOLVES_LOCAL_CDN).$dirCdnInApp;
        $this->localCdnApp = $this->getPathRaiz().$this->localCdnAppSemPathRaiz;
        //Local CDN ADDRESS (starting like http:// https://)
        $urlRaiz = $solvesConfUrl->getUrlRaiz();
        $urlRaizOuSiteUrl = $solvesConfUrl->getUrlRaiz(true);
        $this->localCdnAddress = $urlRaizOuSiteUrl.(Solves::isNotBlank($urlRaiz) ? Solves::removeBarraInicial(self::SOLVES_LOCAL_CDN) : Solves::removeBarraInicial($this->localCdn));
        $this->localCdnAppAddress = $urlRaizOuSiteUrl.(Solves::isNotBlank($urlRaiz) ? Solves::removeBarraInicial($this->localCdnAppSemPathRaiz) : Solves::removeBarraInicial($this->localCdnApp));

        //Informa lib Solves
        if($this->isModeProd){
            \Solves\Solves::setProdMode();
        }else{
            \Solves\Solves::setDevMode();
        }
    }

    /**
     * @return mixed
     */
    public function getPathRaiz(){return $this->solvesConfUrlAtivo->getPathRaiz();}

    /**
     * @return mixed
     */
    public function getUrlRaiz(){return $this->solvesConfUrlAtivo->getUrlRaiz();}

    /**
     * @return string
     */
    public function getAppGooglePlayStoreLink(): string
    {
        return $this->appGooglePlayStoreLink;
    }

    /**
     * @param string $appGooglePlayStoreLink
     * @return SolvesConfUrls
     */
    public function setAppGooglePlayStoreLink(string $appGooglePlayStoreLink): SolvesConfUrls
    {
        $this->appGooglePlayStoreLink = $appGooglePlayStoreLink;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppAppleStoreLink(): string
    {
        return $this->appAppleStoreLink;
    }

    /**
     * @param string $appAppleStoreLink
     * @return SolvesConfUrls
     */
    public function setAppAppleStoreLink(string $appAppleStoreLink): SolvesConfUrls
    {
        $this->appAppleStoreLink = $appAppleStoreLink;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppWindowsStoreLink(): string
    {
        return $this->appWindowsStoreLink;
    }

    /**
     * @param string $appWindowsStoreLink
     * @return SolvesConfUrls
     */
    public function setAppWindowsStoreLink(string $appWindowsStoreLink): SolvesConfUrls
    {
        $this->appWindowsStoreLink = $appWindowsStoreLink;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getAcessoRestritoTipo(): string
    {
        return $this->acessoRestritoTipo;
    }

    /**
     * @return bool
     */
    public function isModoSoonAtivado(): bool
    {
        return $this->modoSoonAtivado;
    }

    /**
     * @return bool
     */
    public function isUnix(): bool
    {
        return $this->unix;
    }

    /**
     * @return mixed
     */
    public function getPathRoot()
    {
        return $this->pathRoot;
    }

    /**
     * @return mixed
     */
    public function getPathRootRelative()
    {
        return $this->pathRootRelative;
    }

    /**
     * @return string
     */
    public function getPublicFolderRelative(): string
    {
        return $this->publicFolderRelative;
    }

    /**
     * @return mixed
     */
    public function getPublicFolder()
    {
        return $this->publicFolder;
    }

    /**
     * @return string
     */
    public function getPublicFolderImagesName(): string
    {
        return $this->publicFolderImagesName;
    }

    /**
     * @return mixed
     */
    public function getPublicFolderImagesRelative()
    {
        return $this->publicFolderImagesRelative;
    }

    /**
     * @return mixed
     */
    public function getPublicFolderImages()
    {
        return $this->publicFolderImages;
    }

    /**
     * @return string
     */
    public function getPublicFolderThumbsName(): string
    {
        return $this->publicFolderThumbsName;
    }

    /**
     * @return mixed
     */
    public function getPublicFolderThumbsRelative()
    {
        return $this->publicFolderThumbsRelative;
    }

    /**
     * @return mixed
     */
    public function getPublicFolderThumbs()
    {
        return $this->publicFolderThumbs;
    }

    /**
     * @return string
     */
    public function getPublicFolderDocsName(): string
    {
        return $this->publicFolderDocsName;
    }

    /**
     * @return mixed
     */
    public function getPublicFolderDocsRelative()
    {
        return $this->publicFolderDocsRelative;
    }

    /**
     * @return mixed
     */
    public function getPublicFolderDocs()
    {
        return $this->publicFolderDocs;
    }

    /**
     * @return SolvesConfIdentificacao
     */
    public function getSolvesConfIdentificacao(): SolvesConfIdentificacao
    {
        return $this->solvesConfIdentificacao;
    }

    /**
     * @return mixed
     */
    public function getSolvesConfUrlAtivo()
    {
        return $this->solvesConfUrlAtivo;
    }

}
class SolvesConfBD{

    const SYSTEM_DB_TYPE_MYSQL = 'MYSQL';
    const SYSTEM_DB_TYPE_POSTGRESQL = 'POSTGRESQL';

    const SYSTEM_DB_TYPE_MYSQL_DEFAULT_HOST = 'localhost';
    const SYSTEM_DB_TYPE_MYSQL_DEFAULT_PORT = '3306';

    const SYSTEM_DB_TYPE_POSTGRESQL_DEFAULT_HOST = 'localhost';
    const SYSTEM_DB_TYPE_POSTGRESQL_DEFAULT_PORT = '5432';

    /*/Configurações de Banco */
    private $systemDbType;
    private $host;
    private $port;
    private $url;
    private $user;
    private $password;
    private $databaseName;

    /**
     * SolvesConfBD constructor.
     */
    public function __construct($systemDbType, $databaseName,  $user, $password, $host=null, $port=null) {
        $this->systemDbType = $systemDbType;
        $this->databaseName = $databaseName;
        $this->user = $user;
        $this->password = $password;
        $this->host = (\Solves\Solves::isNotBlank($host) ? $host : $this->getDefaultHostByDbType());
        $this->port = (\Solves\Solves::isNotBlank($port) ? $port : $this->getDefaultPortByDbType());
        $this->url = $this->getUrlByDbType();
    }
    public function isBdMySql(){
        return self::SYSTEM_DB_TYPE_MYSQL == $this->systemDbType;
    }
    public function isBdPostgreSql(){
        return self::SYSTEM_DB_TYPE_POSTGRESQL == $this->systemDbType;
    }

    private function getUrlByDbType(){
        if($this->isBdMySql()) {
            return $this->host.(strlen($this->port)>0?':'.$this->port:'');
        }else if($this->isBdPostgreSql()) {
            return $this->host.(strlen($this->port)>0?':'.$this->port:'');
        }
        return '';
    }
    private function getDefaultHostByDbType(){
        if($this->isBdMySql()) {
            return self::SYSTEM_DB_TYPE_MYSQL_DEFAULT_HOST;
        }else if($this->isBdPostgreSql()) {
            return self::SYSTEM_DB_TYPE_POSTGRESQL_DEFAULT_HOST;
        }
        return '';
    }
    private function getDefaultPortByDbType(){
        if($this->isBdMySql()) {
            return self::SYSTEM_DB_TYPE_MYSQL_DEFAULT_PORT;
        }else if($this->isBdPostgreSql()) {
            return self::SYSTEM_DB_TYPE_POSTGRESQL_DEFAULT_PORT;
        }
        return '';
    }
    /**
     * @return string|null
     */
    public function getSystemDbType()
    {
        return $this->systemDbType;
    }


    /**
     * @return string|null
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string|null $host
     * @return SolvesConfBD
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string|null $port
     * @return SolvesConfBD
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return SolvesConfBD
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return SolvesConfBD
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return SolvesConfBD
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * @param mixed $databaseName
     * @return SolvesConfBD
     */
    public function setDatabaseName($databaseName)
    {
        $this->databaseName = $databaseName;
        return $this;
    }

}
class SolvesConfPayMethodPaypal{

    private $paypalEmail;
    private $paypalUsuario;
    private $paypalSenha;
    private $paypalAssinatura;
    private $paypalBrandName;
    private $paypalNotifyUrl;
    private $paypalReturnUrl;
    private $paypalCancelUrl;
    private $paypalPathLog = 'log_paypal.txt';

    /**
     * SolvesConfPayMethodPaypal constructor.
     * @param \Solves\SolvesConfIdentificacao $solvesConfIdentificacao
     * @param \Solves\SolvesConfUrls $solvesConfUrls
     * @param $paypalEmail
     * @param $paypalUsuario
     * @param $paypalSenha
     * @param $paypalAssinatura
     * @param $paypalBrandName
     * @param string $paypalPathLog
     */
    public function __construct(\Solves\SolvesConfIdentificacao $solvesConfIdentificacao,
                                \Solves\SolvesConfUrls $solvesConfUrls,
                                $paypalEmail, $paypalUsuario, $paypalSenha, $paypalAssinatura,
                                $paypalBrandName='', string $paypalPathLog='')    {
        $this->solvesConfIdentificacao = $solvesConfIdentificacao;
        $this->paypalEmail = $paypalEmail;
        $this->paypalUsuario = $paypalUsuario;
        $this->paypalSenha = $paypalSenha;
        $this->paypalAssinatura = $paypalAssinatura;
        $this->paypalNotifyUrl = $solvesConfUrls->getSolvesConfUrl()->getSiteUrl().'rest/paypal';
        $this->paypalReturnUrl = $solvesConfUrls->getSolvesConfUrl()->getSiteUrl().'paypal?action=return';
        $this->paypalCancelUrl = $solvesConfUrls->getSolvesConfUrl()->getSiteUrl().'paypal?action=cancel';

        if(\Solves\Solves::isNotBlank($paypalBrandName)){
            $this->paypalBrandName = $paypalBrandName;
        }else{
            $this->paypalBrandName = $solvesConfIdentificacao->getSiteTitulo();
        }

        if(\Solves\Solves::isNotBlank($paypalPathLog)){$this->paypalPathLog = $paypalPathLog;}
    }

    /**
     * @return mixed
     */
    public function getPaypalEmail()
    {
        return $this->paypalEmail;
    }

    /**
     * @return mixed
     */
    public function getPaypalUsuario()
    {
        return $this->paypalUsuario;
    }

    /**
     * @return mixed
     */
    public function getPaypalSenha()
    {
        return $this->paypalSenha;
    }

    /**
     * @return mixed
     */
    public function getPaypalAssinatura()
    {
        return $this->paypalAssinatura;
    }

    /**
     * @return string
     */
    public function getPaypalBrandName(): string
    {
        return $this->paypalBrandName;
    }

    /**
     * @return string
     */
    public function getPaypalNotifyUrl(): string
    {
        return $this->paypalNotifyUrl;
    }

    /**
     * @return string
     */
    public function getPaypalReturnUrl(): string
    {
        return $this->paypalReturnUrl;
    }

    /**
     * @return string
     */
    public function getPaypalCancelUrl(): string
    {
        return $this->paypalCancelUrl;
    }

    /**
     * @return string
     */
    public function getPaypalPathLog(): string
    {
        return $this->paypalPathLog;
    }


}
class SolvesConfPayMethodPagseguro{
    private $pagseguroEmail;
    private $pagseguroApiKey;
    private $pagseguroToken;
    private $pagseguroPathLog = 'pagseguro.log';
    private $pagseguroNumParcelasSemTaxa=2;
    private $pagseguroNumParcelasMaximo=6;
    private $pagseguroUrlNotification;
    private $pagseguroUrlRedirect;

    /**
     * SolvesConfPayMethodPagseguro constructor.
     * @param \Solves\SolvesConfUrls $solvesConfUrls
     * @param $pagseguroEmail
     * @param $pagseguroApiKey
     * @param $pagseguroToken
     * @param int $pagseguroNumParcelasSemTaxa
     * @param int $pagseguroNumParcelasMaximo
     * @param string $pagseguroPathLog
     */
    public function __construct(\Solves\SolvesConfUrls $solvesConfUrls,
                                $pagseguroEmail, $pagseguroApiKey, $pagseguroToken,
                                int $pagseguroNumParcelasSemTaxa, int $pagseguroNumParcelasMaximo,
                                string $pagseguroPathLog='')   {
        $this->pagseguroEmail = $pagseguroEmail;
        $this->pagseguroApiKey = $pagseguroApiKey;
        $this->pagseguroToken = $pagseguroToken;
        $this->pagseguroNumParcelasSemTaxa = $pagseguroNumParcelasSemTaxa;
        $this->pagseguroNumParcelasMaximo = $pagseguroNumParcelasMaximo;

        if(\Solves\Solves::isNotBlank($pagseguroPathLog)){$this->pagseguroPathLog = $pagseguroPathLog;}
        $this->pagseguroUrlNotification = $solvesConfUrls->getSolvesConfUrl()->getSiteUrl().'rest/pagseguro';
        $this->pagseguroUrlRedirect = $solvesConfUrls->getSolvesConfUrl()->getSiteUrl().'pagseguro';
    }


}
class SolvesConfPayMethod{

    private $confPaypalMethod;
    private $confPagseguroMethod;

    private $hasPaypalMethod=false;
    private $hasPagseguroMethod=false;

    /**
     * @return mixed
     */
    public function getConfPaypalMethod(): ?\Solves\SolvesConfPayMethodPaypal    {
        return $this->confPaypalMethod;
    }

    /**
     * @param mixed $confPaypalMethod
     * @return SolvesConfPayMethod
     */
    public function setConfPaypalMethod(\Solves\SolvesConfPayMethodPaypal $confPaypalMethod)    {
        $this->confPaypalMethod = $confPaypalMethod;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfPagseguroMethod(): ?\Solves\SolvesConfPayMethodPagseguro    {
        return $this->confPagseguroMethod;
    }

    /**
     * @param mixed $confPagseguroMethod
     * @return SolvesConfPayMethod
     */
    public function setConfPagseguroMethod(\Solves\SolvesConfPayMethodPagseguro $confPagseguroMethod)    {
        $this->confPagseguroMethod = $confPagseguroMethod;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHasPaypalMethod(): bool     {
        return $this->hasPaypalMethod;
    }

    /**
     * @return bool
     */
    public function isHasPagseguroMethod(): bool     {
        return $this->hasPagseguroMethod;
    }

}
class SolvesConfEmails{
    private $emailRemetente;
    private $emailRemetentePassword;
    private $emailHost;
    private $emailPort = '25';
    private $emailsDestino = array();

    /**
     * SolvesConfEmails constructor.
     * @param $emailRemetente
     * @param $emailRemetentePassword
     * @param $emailHost
     * @param string $emailPort
     */
    public function __construct($emailRemetente, $emailRemetentePassword, $emailHost, string $emailPort)
    {
        $this->emailRemetente = $emailRemetente;
        $this->emailRemetentePassword = $emailRemetentePassword;
        $this->emailHost = $emailHost;
        if(\Solves\Solves::isNotBlank($emailPort)){$this->emailPort = $emailPort;}
    }

    /**
     * @return mixed
     */
    public function getEmailRemetente()
    {
        return $this->emailRemetente;
    }

    /**
     * @return mixed
     */
    public function getEmailRemetentePassword()
    {
        return $this->emailRemetentePassword;
    }

    /**
     * @return mixed
     */
    public function getEmailHost()
    {
        return $this->emailHost;
    }

    /**
     * @return string
     */
    public function getEmailPort(): string
    {
        return $this->emailPort;
    }

    /**
     * @return array
     */
    public function getEmailsDestino(): array
    {
        return $this->emailsDestino;
    }

    public function addEmailDestino($key, $email){
        $this->emailsDestino[$key] = $email;
    }
    public function getEmailDestino($key){
        return $this->emailsDestino[$key];
    }
}
class SolvesConfFirebase{
    const INSIDE_NAV_SOLVES_AUTH = '../../../../';
    private $firebaseConfigJsonFile;
    private $firebaseConfigUser;
    private $firebaseConfigAuthGoogle;
    private $firebaseConfigJsonConfig;
    private $useAuthFacebook=false;

    /**
     * SolvesConfFirebase constructor.
     * @param $firebaseConfigJsonFile
     * @param $firebaseConfigUser
     * @param $firebaseConfigAuthGoogle
     */
    public function __construct($firebaseConfigJsonFile, $firebaseConfigUser, $firebaseConfigAuthGoogle)
    {
        $this->firebaseConfigJsonFile = $firebaseConfigJsonFile;
        $this->firebaseConfigUser = $firebaseConfigUser;
        $this->firebaseConfigAuthGoogle = $firebaseConfigAuthGoogle;
    }

    /**
     * @return mixed
     */
    public function getFirebaseConfigJsonFile()
    {
        return $this->firebaseConfigJsonFile;
    }

    /**
     * @return mixed
     */
    public function getFirebaseConfigUser()
    {
        return $this->firebaseConfigUser;
    }

    /**
     * @return mixed
     */
    public function getFirebaseConfigAuthGoogle()
    {
        return $this->firebaseConfigAuthGoogle;
    }

    /**
     * @return mixed
     */
    public function getFirebaseConfigJsonConfig()
    {
        return $this->firebaseConfigJsonConfig;
    }

    /**
     * @param mixed $firebaseConfigJsonConfig
     * @return SolvesConfFirebase
     */
    public function setFirebaseConfigJsonConfig($firebaseConfigJsonConfig)
    {
        $this->firebaseConfigJsonConfig = $firebaseConfigJsonConfig;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseAuthFacebook(): bool
    {
        return $this->useAuthFacebook;
    }

    /**
     * @param bool $useAuthFacebook
     * @return SolvesConfFirebase
     */
    public function setUseAuthFacebook(bool $useAuthFacebook): SolvesConfFirebase
    {
        $this->useAuthFacebook = $useAuthFacebook;
        return $this;
    }

}
class SolvesConfNotifications{
    const NOTIFICATIONS_SUBSCRIPTION_URL = '/rest/notifications';
    private $publicKey;
    private $privateKey;
    private $senderId;

    /**
     * SolvesConfNotifications constructor.
     * @param $publicKey
     * @param $privateKey
     * @param $senderId
     */
    public function __construct($publicKey, $privateKey, $senderId)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->senderId = $senderId;
    }

    /**
     * @return mixed
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @return mixed
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @return mixed
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

}
class SolvesConfResponsiveFileManager{

    private $dir;
    private $url;
    private $privateKey;

    /** @type \Solves\SolvesConfUrls */
    private $solvesConfUrls;

    /**
     * SolvesConfNotifications constructor.
     * @param \Solves\SolvesConfUrls $solvesConfUrls
     * @param $dir
     * @param $url
     * @param $privateKey
     */
    public function __construct(\Solves\SolvesConfUrls $solvesConfUrls, $dir, $url, $privateKey)
    {
        $this->solvesConfUrls = $solvesConfUrls;
        $this->dir = $solvesConfUrls->getPathRaiz().$dir;
        $this->url = $solvesConfUrls->getUrlRaiz().$url;
        $this->privateKey = $privateKey;
    }


    function getUrlSelectImage($fieldElmId, $isMultiple=true){
        return $this->url.'dialog.php?crossdomain=1&amp;type=1&amp;fldr='.$this->solvesConfUrls->getPublicFolderImagesName().'&amp;field_id='.$fieldElmId.'&amp;relative_url=1&amp;multiple='.($isMultiple?1:0).'&amp;akey='.$this->privateKey;
    }
    function getUrlSelectFile($fieldElmId, $isMultiple=true){
        return $this->url.'dialog.php?crossdomain=1&amp;type=2&amp;fldr='.$this->solvesConfUrls->getPublicFolderDocsName().'&amp;field_id='.$fieldElmId.'&amp;relative_url=1&amp;multiple='.($isMultiple?1:0).'&amp;akey='.$this->privateKey;
    }
    function getUrlSelectAll($fieldElmId, $isMultiple=true){
        return $this->url.'dialog.php?crossdomain=1&amp;type=2&amp;field_id='.$fieldElmId.'&amp;relative_url=1&amp;multiple='.($isMultiple?1:0).'&amp;akey='.$this->privateKey;
    }

}
class SolvesConfWebsocket{

    private $host;
    private $port;
    private $url;
    private $buildApp;
    /**
    *@var SolvesConfWebsocketRoute[]
    */
    private $routes = [];

    /**
     * SolvesConfWebsocket constructor.
     * @param $host
     * @param $port
     * @param $buildApp
     * @param $isWss
     */
    public function __construct(string $host, int $port, ?bool $buildApp=false, ?bool $isWss=false)
    {
        $this->host = $host;
        $this->port = $port;
        $this->buildApp = $buildApp;
        $this->url = ($isWss?'wss://':'ws://').$host.((isset($port) && $port>0)?':'.$port:'');
    }


    public function isBuildApp(): bool{return $this->buildApp;}
    public function setHost($p){$this->host = $p;}
    public function getHost(): string{return $this->host;}
    public function setPort($p){$this->port = $p;}
    public function getPort(): string{return $this->port;}
    /*public function setUrl($p){$this->url = $p;} */
    public function getUrl(): string{return $this->url;}
    public function addRoute(string $name, string $path){$this->routes[] = new SolvesConfWebsocketRoute($name, $path);}
    public function getRoutes(): array{return $this->routes;}

}
class SolvesConfWebsocketRoute{
    private $name;
    private $path;
    /**
     * SolvesConfWebsocketRoute constructor.
     * @param $name
     * @param $path
     */
    public function __construct(string $name, string $path){
        $this->name = $name;
        $this->path = $path;
    }
    public function getName(): string{return $this->name;}
    public function getPath(): string{return $this->path;}
}
class SolvesConfTelegram{
    private $botToken;
    private $chatId;
    private $urlSendMessage;
    /**
     * SolvesConfTelegram constructor.
     * @param $botToken
     * @param $chatId
     */
    public function __construct(string $botToken, string $chatId){
        $this->botToken = $botToken;
        $this->chatId = $chatId;
        $this->urlSendMessage = "https://api.telegram.org/" .$this->botToken. "/sendMessage?chat_id=" . $this->chatId;
    }
    public function getBotToken(): string{return $this->botToken;}
    public function getChatId(): string{return $this->chatId;}

    public function getUrlEnviaMensagem(string $mensagem): string{
        return $this->urlSendMessage . "&parse_mode=Markdown&text=" . urlencode($mensagem);
    }
    public function enviaMensagem(string $mensagem){
        $result=null;
        try{
            $url = $this->getUrlEnviaMensagem($mensagem);
            $ch = curl_init();
            $optArray = array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true
            );
            curl_setopt_array($ch, $optArray);
            $result = curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $ex){
            error_log("Erro no telegram" . $ex->getMessage() );
        }
        return $result;
    }
}
?>