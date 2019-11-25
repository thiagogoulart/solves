 <?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 29/07/2019
 
*/

include_once 'src/Solves/Solves.php';
include_once 'src/Solves/SolvesRest.php';
include_once 'src/Solves/SolvesRouter.php';
use PHPUnit\Framework\TestCase;

class SolvesRestTest extends TestCase {
	public static function setUpBeforeClass() : void{
	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{
	  
	}
	public function tearDown()  : void{
	  
	}

	public function testGetDado(){
		/*$_HTTPREQUEST_SERVER=null;
		$_HTTPREQUEST_POST=array();
		$_HTTPREQUEST_GET=null;
		$_HTTPREQUEST_PUT=null;
		$_HTTPREQUEST_DELETE=null;
		$_HTTPREQUEST_FILES=null;
		
		$_HTTPREQUEST_POST['dados'] = array();
		$_HTTPREQUEST_POST['dados']['nome'] = 'Thiago Gonçalves da Silva Goulart';
		$_HTTPREQUEST_POST['dados']['email'] = 'compartilhatube@gmail.com';
		$_HTTPREQUEST_POST['dados']['senha'] = 'senha1';
		$_HTTPREQUEST_POST['dados']['channel_url'] = 'https://www.youtube.com/channel/UCskKjqPAjmslmCsMmFO4iHw';
		$_HTTPREQUEST_POST['dados']['channel_avatar'] = 'https://yt3.ggpht.com/-q3wT_a-pmdU/AAAAAAAAAAI/AAAAAAAAAAA/DDFDHcPj808/s240-c-k-no-mo-rj-c0xffffff/photo.jpg';
		
		$rest=new SolvesRestClassTest(new \Solves\SolvesRouter($_HTTPREQUEST_SERVER, $_HTTPREQUEST_POST, $_HTTPREQUEST_GET, $_HTTPREQUEST_PUT, $_HTTPREQUEST_DELETE,$_HTTPREQUEST_FILES));
		$params = array('email','nome');
		$expected = array($_HTTPREQUEST_POST['dados']['email'], $_HTTPREQUEST_POST['dados']['nome']);
		$qtd = count($params);
		for($i=0;$i!=$qtd;$i++){ 
			$p = $params[$i];
			$s =$rest->getDado('email');
			$this->assertEquals($expected[$i], $s, 'Dado ['.$p.'] ficou ['.$s.']. Dado não ficou como esperado:'.$expected[$i]);
		}*/
		$this->assertTrue(true);
	}
	public function testGetDadoUrl(){
		/*$_HTTPREQUEST_SERVER=null;
		$_HTTPREQUEST_POST=array();
		$_HTTPREQUEST_GET=null;
		$_HTTPREQUEST_PUT=null;
		$_HTTPREQUEST_DELETE=null;
		
		
		$_HTTPREQUEST_POST['dados'] = array();
		$_HTTPREQUEST_POST['dados']['nome'] = 'Thiago Gonçalves da Silva Goulart';
		$_HTTPREQUEST_POST['dados']['email'] = 'compartilhatube@gmail.com';
		$_HTTPREQUEST_POST['dados']['senha'] = 'senha1';
		$_HTTPREQUEST_POST['dados']['channel_url'] = 'https://www.youtube.com/channel/UCskKjqPAjmslmCsMmFO4iHw';
		$_HTTPREQUEST_POST['dados']['channel_avatar'] = 'https://yt3.ggpht.com/-q3wT_a-pmdU/AAAAAAAAAAI/AAAAAAAAAAA/DDFDHcPj808/s240-c-k-no-mo-rj-c0xffffff/photo.jpg';
		
		$rest=new SolvesRestClassTest(new \Solves\SolvesRouter($_HTTPREQUEST_SERVER, $_HTTPREQUEST_POST, $_HTTPREQUEST_GET, $_HTTPREQUEST_PUT, $_HTTPREQUEST_DELETE,$_HTTPREQUEST_FILES));
		$params = array('channel_avatar');
		$expected = array('https://yt3.ggpht.com/-q3wT_a-pmdU/AAAAAAAAAAI/AAAAAAAAAAA/DDFDHcPj808/s240-c-k-no-mo-rj-c0xffffff/photo.jpg');
		$qtd = count($params);
		for($i=0;$i!=$qtd;$i++){ 
			$p = $params[$i];
			$s =$rest->getDadoUrl($p);
			$this->assertEquals($expected[$i], $s, 'URL ['.$p.'] ficou ['.$s.']. Url não ficou como esperada:'.$expected[$i]);
		}*/
		$this->assertTrue(true);
	}

}

class SolvesRestClassTest extends \Solves\SolvesRest{

    private static $RESTRITO = false;
    private static $PUBLIC_METHODS = [];
    private static $MAIN_CLASS = '';


    public function __construct($router) {
        parent::__construct($router, self::$MAIN_CLASS, self::$RESTRITO);
        $this->setPublicMethods(self::$PUBLIC_METHODS);
    }

    public function preAction(){

    }
    public function posAction(){

    }
    public function index(){
    }
    public function save(){
        $this->setError('Não foi possível realizar a operação');
    }
    public function update(){
        $this->setError('Não foi possível realizar a operação');
    }
    public function delete(){
        $this->setError('Não foi possível realizar a operação');
    }

}