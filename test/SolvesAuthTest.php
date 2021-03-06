 <?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 21/01/2020
 
*/

include_once 'src/Solves/Solves.php';
include_once 'src/SolvesAuth/SolvesAuth.php';
include_once 'src/Solves/SolvesConf.php';
use PHPUnit\Framework\TestCase;

class SolvesAuthTest extends TestCase {
	public static function setUpBeforeClass() : void{				
		$PATH_RAIZ = '../new/';
		$host = 'app.compartilhatube.com.br';
		$hostRaiz = 'https://compartilhatube.com.br/new/';

		/* -----------------------------  Configuração dados de identificação  ----------------------------------- */
		$solvesConfIdentificacao = new \Solves\SolvesConfIdentificacao('CompartilhaTube', 'compartilhatube', '2.0',
		    'CompartilhaTube',
		    'Aplicativo para divulgação de vídeos do Youtube. Temos também contador de inscritos para ajudar você a acompanhar o crescimento do seu canal',
		    'compartilhatube, youtube, divulgar vídeo, divulgar inscritos, divulgar like, youtuber iniciante, rede social de youtubers, canais do youtube, ganhar view, ganhar visualização, divulgar canal, como ser youtuber, vídeos de minecraft, divulgação youtube, divulgando canais, grupo gamer, vídeos de gameplay, grupo para youtubers, divulgar vídeos celular, divulgar vlog, crescer no youtube, app para subir inscrito, aplicativo para ganhar inscrito, live youtube, compartilhar tube, compartilha tube, tube',
		    1, 0);
		$solvesConfIdentificacao->setSiteAuthor('Thiago G.S. Goulart');
		$solvesConfIdentificacao->setTwitterCreator('@compartilhatube');
		\Solves\SolvesConf::setSolvesConfIdentificacao($solvesConfIdentificacao);

		/* -----------------------------  Configuração dados de URLs  -------------------------------------------- */
		$solvesConfUrls = new \Solves\SolvesConfUrls($solvesConfIdentificacao, $host, '', false);
		$solvesConfUrlDev = new \Solves\SolvesConfUrl('localhost', '/compartilhatube_new/app/', false, false, null, $PATH_RAIZ);
		$solvesConfUrlHml = new \Solves\SolvesConfUrl('hml-app.compartilhatube.com.br', '/', true, true, null, $PATH_RAIZ);
		$solvesConfUrlProd = new \Solves\SolvesConfUrl('app.compartilhatube.com.br', '/', true, true, null, $PATH_RAIZ, $hostRaiz);
		$solvesConfUrls->setSolvesConfUrlDev($solvesConfUrlDev);
		$solvesConfUrls->setSolvesConfUrlHml($solvesConfUrlHml);
		$solvesConfUrls->setSolvesConfUrlProd($solvesConfUrlProd);
		$solvesConfUrls->setAppGooglePlayStoreLink('https://play.google.com/store/apps/details?id=br.com.rbcworks.compartilhatube');
		\Solves\SolvesConf::setSolvesConfUrls($solvesConfUrls);

	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{
	  
	}
	public function tearDown()  : void{
	  
	}
    public function testAuthToken(){
    	$userId=7;
    	$tipo='User';
    	$senha='123456';

        $token = \SolvesAuth\SolvesAuth::getAuthToken($userId, $senha, $tipo);
        $tk = $token['token'];
        $userData = $token['userData'];

        $checkToken = \SolvesAuth\SolvesAuth::createToken($userData);
        
        $this->assertEquals($tk, $checkToken, 'Token nao bate');
    }
}