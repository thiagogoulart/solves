<?php
namespace SolvesUi;

/**
 * Class SolvesUiCss
 * @package SolvesUi
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 09/01/2020
 */
class SolvesUiCss {

    /**
     * @var
     */
    private $dir;
    /**
     * @var string
     */
    private $fileName;
     /**
     * @var string
     */
    private $fileNameIfProd;
    /**
     * @var
     */
    private $path;
    /**
     * @var bool|null
     */
    private $external=false;
    /**
     * @var bool
     */
    private $inline=true;


    /**
     * @var bool
     */
    private $usingAssetsCss=false;
    /**
     * @var bool
     */
    private $usingAssetsLib=false;
    /**
     * @var bool
     */
    private $usingCdnApp=false;
    /**
     * @var bool
     */
    private $usingLocalCdn=false;
    /**
     * @var bool
     */
    private $usingLocalCdnAddress=false;
    /**
     * @var bool
     */
    private $usingLocalCdnApp=false;
    /**
     * @var bool
     */
    private $usingLocalCdnAppAddress=false;
    /**
     * @var string
     */
    private $rel='stylesheet';
    /**
     * @var string
     */
    private $as;
    /**
     * @var string
     */
    private $crossorigin;

    /**
     * SolvesUi constructor.
     * @param string $fileName
     * @param ?bool $external
     */
    public function __construct(string $fileName, ?bool $external=true){
        $this->fileName = $fileName;
        $this->fileNameIfProd = $this->fileName;
        $this->external = $external;
        $this->configPath();
    }

    /**
     * @return string
     */
    public function getPath(): string{
        return $this->path;
    }
    /**
     * @return string
     */
    public function getRel(): string{
        return $this->rel;
    }
    /**
     * @return string
     */
    public function getAs(): string{
        return $this->as;
    }
    /**
     * @return string
     */
    public function getCrossorigin(): string{
        return $this->crossorigin;
    }

    /**
     * @return bool
     */
    public function isInline(): bool{
        return $this->inline;
    }

    /**
     * @return SolvesUiCss
     */
    public function setFileNameIfProd(string $fileNameIfProd): SolvesUiCss{
        $this->fileNameIfProd = $fileNameIfProd;
        return $this;
    }

    /**
     * @return string
     */
    private function getFileNameInUse(): string{
        if(\Solves\SolvesConf::getSolvesConfUrls()->isModeProd()){
            return $this->fileNameIfProd;
        }
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getIncludeTag(): string{
        if($this->external){
            //Possibilidade de ajustar tag link para casos de ler URL. Avaliar benefício de  rel="preconnect"
            return '<link rel="'.$this->rel.'" href="'.$this->path.'" '.(\Solves\Solves::isNotBlank($this->as)?'as="'.$this->as.'"':'').'  '.(\Solves\Solves::isNotBlank($this->crossorigin)?'crossorigin="'.$this->crossorigin.'"':'').'>';
        }else if($this->inline){
            $content = $this->getInlineContent();
            if(\Solves\Solves::isNotBlank($content)){
                return $content;
            }
        }
        return '<link rel="'.$this->rel.'" href="'.$this->path.'" '.(\Solves\Solves::isNotBlank($this->as)?'as="'.$this->as.'"':'').'>';
    }

    /**
     * @return string
     */
    private function getInlineContent(): string{
        if(file_exists($this->path)){
            $content = file_get_contents($this->path);
            if(\Solves\Solves::isNotBlank($content)){
                $content = $this->ajustaCaminhosDoContent($content);
                $content = "
                <!-- '".$this->getFileNameInUse()."' -->
                <style type=\"text/css\">
                ".$content."
                </style>
                ";
                return $content;
            }else{
                return null;
            }
        }else{
            return "<!-- '".$this->path."' não econtrado -->";
        }
    }

    /**
     * @param string $content
     * @return string|string[]|null
     */
    private function ajustaCaminhosDoContent(string $content){
        //replace de ../ se for assets e de  ../../../ se for cdn no cntent
        if($this->usingAssetsCss){
            $replaceFor = \Solves\Solves::getSiteUrl().\Solves\Solves::removeBarraInicial(\Solves\SolvesConfUrls::ASSETS);
            $content = str_replace("../", $replaceFor, $content);
        }else if($this->usingLocalCdn || $this->usingLocalCdnApp){
            $replaceFor = \Solves\SolvesConf::getSolvesConfUrls()->getLocalCdnAddress();
            $content = str_replace("../../../", $replaceFor, $content); 
        }
        return $content;
    }

    public function setRelPreload(): SolvesUiCss{
        $this->rel='preload';
        return $this;
    }
    public function setAsFont(): SolvesUiCss{
        $this->as='font';
        return $this;
    }
    public function setCrossoriginAnonymous(): SolvesUiCss{
        $this->crossorigin='anonymous';
        return $this;
    }

    /**
     * @return SolvesUiCss
     */
    private function configPath(): SolvesUiCss{
        if($this->external){
            $this->path = $this->getFileNameInUse();
        }else{
            $this->path = $this->dir.$this->getFileNameInUse();
        }
        return $this;
    }

    /**
     * @return SolvesUiCss
     */
    public function useAssetsCss(): SolvesUiCss{
        $this->usingAssetsCss = true;
        $this->external = false;
        $this->inline = true;
        $this->dir = \Solves\Solves::removeBarraInicial(\Solves\SolvesConf::getSolvesConfUrls()::CSS);
        /*\Solves\SolvesConf::getSolvesConfUrls()->getSolvesConfUrlAtivo()->getSiteContext().\Solves\Solves::removeBarraInicial(\Solves\SolvesConf::getSolvesConfUrls()::CSS);*/
        $this->configPath();
        return $this;
    }

    /**
     * @return SolvesUiCss
     */
    public function useAssetsLib(string $dirInsideLib): SolvesUiCss{
        $dirInsideLib = \Solves\Solves::removeBarraInicial($dirInsideLib);
        $dirInsideLib = \Solves\Solves::removeBarraFinal($dirInsideLib);
        $this->usingAssetsLib = true;
        $this->external = false;
        $this->inline = false;
        $this->dir = \Solves\Solves::removeBarraInicial(\Solves\SolvesConf::getSolvesConfUrls()::LIB).$dirInsideLib.'/';
        $this->configPath();
        return $this;
    }


    /**
     * @return SolvesUiCss
     */
    public function useCdnApp(): SolvesUiCss{
        $this->usingCdnApp = true;
        $this->external = true;
        $this->inline = false;
        $this->dir = \Solves\SolvesConf::getSolvesConfUrls()->getCdnApp();
        $this->configPath();
        return $this;
    }

    /**
     * @return SolvesUiCss
     */
    public function useLocalCdn(): SolvesUiCss{
        $this->usingLocalCdn = true;
        $this->external = false;
        $this->inline = true;
        $this->dir = \Solves\SolvesConf::getSolvesConfUrls()->getLocalCdn();
        $this->configPath();
        return $this;
    }

    /**
     * @return SolvesUiCss
     */
    public function useLocalCdnCss(): SolvesUiCss{
        $this->usingLocalCdn = true;
        $this->external = false;
        $this->inline = true;
        $this->dir = \Solves\SolvesConf::getSolvesConfUrls()->getLocalCdnCss();
        $this->configPath();
        return $this;
    }
    /**
     * @return SolvesUiCss
     */
    public function useLocalCdnLib(): SolvesUiCss{
        $this->usingLocalCdn = true;
        $this->external = false;
        $this->inline = true;
        $this->dir = \Solves\SolvesConf::getSolvesConfUrls()->getLocalCdnLib();
        $this->configPath();
        return $this;
    }

    /**
     * @return SolvesUiCss
     */
    public function useLocalCdnAddress(): SolvesUiCss{
        $this->usingLocalCdnAddress = true;
        $this->external = false;
        $this->inline = true;
        $this->dir = \Solves\SolvesConf::getSolvesConfUrls()->getLocalCdnAddress();
        $this->configPath();
        return $this;
    }

    /**
     * @return SolvesUiCss
     */
    public function useLocalCdnApp(): SolvesUiCss{
        $this->usingLocalCdnApp = true;
        $this->external = false;
        $this->inline = true;
        $this->dir = \Solves\SolvesConf::getSolvesConfUrls()->getLocalCdnApp();
        $this->configPath();
        return $this;
    }

    /**
     * @return SolvesUiCss
     */
    public function useLocalCdnAppAddress(): SolvesUiCss{
        $this->usingLocalCdnAppAddress = true;
        $this->external = false;
        $this->inline = true;
        $this->dir = \Solves\SolvesConf::getSolvesConfUrls()->getLocalCdnAppAddress();
        $this->configPath();
        return $this;
    }

}
?>