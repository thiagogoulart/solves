<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 30/08/2019
 */ 
namespace Solves;


class SolvesGoogleApi {
    const GOOGLE_API_YOUTUBE_V3 = "https://www.googleapis.com/youtube/v3/";
    const GOOGLE_API_RECAPTCHA = "https://www.google.com/recaptcha/api/siteverify";

    private static $RECAPTCHA_SECRET_KEY = "";
    
    private static $YOUTUBE_API_KEYS = array();  
    private static $YOUTUBE_API_KEY_MAX_INDEX=0;
    private static $YOUTUBE_API_KEY_LAST_USED=0;
    
    
    public static function configRecaptchaSecretKey($recaptchaKey){
        SolvesGoogleApi::setRecaptchaSecretKey($recaptchaKey);
    }
    public static function configYoutubeKeys($arrYoutubeKey){
       if(isset($arrYoutubeKey)){
             $i=0;
            foreach($arrYoutubeKey as $key){
                if(\Solves\Solves::isNotBlank($key) && !in_array($key, SolvesGoogleApi::$YOUTUBE_API_KEYS)){
                    $solvesYoutubeApiKey = new SolvesYoutubeApiKey($i, $key);
                    SolvesGoogleApi::$YOUTUBE_API_KEYS[$i] = $solvesYoutubeApiKey;
                    SolvesGoogleApi::$YOUTUBE_API_KEY_MAX_INDEX = $i;
                    $i++;
                }
            }
       }
    }
    public static function getYoutubeKeys(){
        return SolvesGoogleApi::$YOUTUBE_API_KEYS;
    }
    public static function getAvailableKey(){
        if(isset(self::$YOUTUBE_API_KEYS)){
            $key = self::$YOUTUBE_API_KEYS[self::$YOUTUBE_API_KEY_LAST_USED];
            $key->addQuota();
            self::$YOUTUBE_API_KEYS[self::$YOUTUBE_API_KEY_LAST_USED] = $key;
            self::$YOUTUBE_API_KEY_LAST_USED++;
            if(self::$YOUTUBE_API_KEY_LAST_USED>self::$YOUTUBE_API_KEY_MAX_INDEX){
                self::$YOUTUBE_API_KEY_LAST_USED = 0;
            }
            if($key->isExceeded()){
                return null;
            }
            return $key;
        }
        return null;
    }
    public static function setErroQuotaKey($keyName){
        $key = self::$YOUTUBE_API_KEYS[$keyName];
        $key->setQuotaExceeded();
    }


    public static function getGoogleApiYoutubeV3(){return self::GOOGLE_API_YOUTUBE_V3;}

    public static function getRecaptchaSecretKey(){return SolvesGoogleApi::$RECAPTCHA_SECRET_KEY;}
    public static function setRecaptchaSecretKey($p){SolvesGoogleApi::$RECAPTCHA_SECRET_KEY = $p;}

    public static function getEstatisticasVideo($idVideo){
        $dadosVideo = array("likeCount" => 0 , "dislikeCount"=>0, "viewCount"=>0 , "commentCount"=>0, "favoriteCount"=>0);
        try{
            if($idVideo){
                $key = SolvesGoogleApi::getAvailableKey();
                $URL = $key->getYoutubeApiUrlVideoEstatisticas(). urlencode($idVideo);
                try{
                    //echo $URL . "<br/><br/>";
                    $responseDados = @file_get_contents($URL);
                    $jsonDados = @json_decode($responseDados);
                } catch (\Exception $e){                
                    error_log(print_r($e, true));
                    return $dadosVideo;
                }
                if($jsonDados != null && count($jsonDados->items) > 0){
                    foreach ($jsonDados->items as $item){
                        if(@$item->statistics->viewCount != null){
                            $dadosVideo["likeCount"] = @$item->statistics->likeCount;
                            $dadosVideo["dislikeCount"] = @$item->statistics->dislikeCount;
                            $dadosVideo["viewCount"] = @$item->statistics->viewCount;
                            $dadosVideo["commentCount"] = @$item->statistics->commentCount;
                            $dadosVideo["favoriteCount"] = @$item->statistics->favoriteCount;
                        } 
                    }
                }
            }
        } catch (\Exception $ex){
            error_log(print_r($ex, true));
        }
        return $dadosVideo;
    }
    public static function procuraCanal($url_canal){
        $ar_canais = array();
       
        $URL = "";
        
        $url_canal = str_replace("/featured" , "" , $url_canal);
        $url_canal = str_replace("/videos" , ""  , $url_canal);
        $url_canal = str_replace("/about" , "" , $url_canal);
        $url_canal = str_replace("?app=desktop" , "" , $url_canal);
        $url_canal = str_replace("?sub_confirmation=1" , "" , $url_canal);
        $url_canal = str_replace("/feed/account" , "" , $url_canal);
        $url_canal = str_replace("watch" , "" , $url_canal);
        $url_canal = str_replace("/my_videos" , "" , $url_canal);
        if(strpos($url_canal ,"?") !== false){
            $ar_link = explode("?", $url_canal);
            $url_canal = $ar_link[0];
        }
        $key = SolvesGoogleApi::getAvailableKey();
        if(strpos($url_canal , "user/") !== false){
            $ar_link = explode("user/" , $url_canal);
            $URL = $key->getYoutubeApiUrlDados(). "forUsername=" + trim($ar_link[1]);
        } else if(strpos($url_canal ,"channel/") !== false){
            $ar_link = explode("channel/" , $url_canal);
            $URL = $key->getYoutubeApiUrlDados(). "id=" . trim($ar_link[1]);
        }else if(strpos($url_canal ,"c/") !== false){
            $ar_link = explode("c/" , $url_canal);
            $URL = $key->getYoutubeApiUrlDadosQuery(). "q=" .  urlencode($ar_link[1]);
        }else if(strpos($url_canal ,"youtube.com/") !== false){
            $ar_link = explode("youtube.com/" , $url_canal);
            if($ar_link[1] != "" && $ar_link[1] != null){
                $URL = $key->getYoutubeApiUrlDadosQuery() . "q=" .  urlencode($ar_link[1]);
            }
        }else if(strpos($url_canal ,"user/") !== false){
            $ar_link = explode("user/" , $url_canal);
            if($ar_link[1] != "" && $ar_link[1] != null){
                $URL = $key->getYoutubeApiUrlDados() . "forUsername=" . $ar_link[1];
            }
        }else if($url_canal != "" && strpos($url_canal ,"http") === false){
            $URL = $key->getYoutubeApiUrlDadosQuery() . "q=" . urlencode($url_canal);
        }
        //LogAPI($URL);
        if($URL != ""){
            try{
                //echo $URL . "<br/><br/>";
                $responseDados = @file_get_contents($URL);
                $jsonDados = json_decode($responseDados);
            } catch (\Exception $e){
                die($e);
            }
            //LogAPI
            
            if($jsonDados != null){
                if(count($jsonDados->items) > 0){
                    foreach ($jsonDados->items as $item){
                        if(@$item->snippet->channelId != null){
                            
                            $avatar = @$item->snippet->thumbnails->high->url;
                            $youtube_id = @$item->snippet->channelId;
                            $nome = @$item->snippet->title;
                            
                            //$youtube_id = @$item->id->channelId;
                            //$youtube_id = ($youtube_id != null ) ? $youtube_id : @$item->id;
                            if($avatar != null && $youtube_id != null && $youtube_id != "UCrFiA0hztL9e8zTi_qBuW4w"){
                                $ar_canais[] = array("id" => $youtube_id ,
                                    "avatar" => $avatar,
                                    "nome" => $nome
                                );
                            }
                            
                        } else if(@$item->id != null){
                            //print_r($item);
                            $avatar = @$item->snippet->thumbnails->high->url;
                            $youtube_id = @$item->id;
                            $nome = @$item->snippet->title;
                            
                            //$youtube_id = @$item->id->channelId;
                            //$youtube_id = ($youtube_id != null ) ? $youtube_id : @$item->id;
                            if($avatar != null && $youtube_id != null && $youtube_id != "UCrFiA0hztL9e8zTi_qBuW4w"){
                                $ar_canais[] = array("id" => $youtube_id ,
                                    "avatar" => $avatar,
                                    "nome" => $nome
                                );
                            }
                        }
                    }
                }else if(property_exists($jsonDados, 'error') && count($jsonDados->error->errors) > 0){
                    SolvesGoogleApi::logError('Resposta  de ERRO da URL ['.$URL.']:'.$jsonDados->error->errors);
                    if($jsonDados->error->errors[0]->reason== "dailyLimitExceeded"){
                        self::setErroQuotaKey($key);
                    }
                }else{
                    SolvesGoogleApi::logError('Não recebemos resposta da URL ['.$URL.'], $jsonDados:['.json_encode($jsonDados).']');
                }
            }else{
                SolvesGoogleApi::logError('Não recebemos resposta da URL ['.$URL.'], $responseDados:['.$responseDados.']');
            }
        }        
        return $ar_canais;
    }
    public static function logError($msg){ 
        error_log("[".date('Y-m-d H:i:s')."]SOLVES_GOOGLE_API:".$msg);
    }
    public static function getListaVideos($canal , $limit = 50){
        //$dadosVideo = array("videoId" => "" ,"publishedAt" => "" , "title"=>"", "thumbnail"=>"" );
        $dadosVideo = null;
        if($canal){
            $key = SolvesGoogleApi::getAvailableKey();
            $URL = $key->getYoutubeApiUrlSearchVideo($canal, $limit);
            
            try{
            //echo $URL . "<br/><br/>";
                $responseDados = @file_get_contents($URL);
                $jsonDados = json_decode($responseDados);
            } catch (\Exception $e){
                die($e);
            }
            if($jsonDados != null && count($jsonDados->items) > 0){
                foreach ($jsonDados->items as $item){
                    if(@$item->id->videoId != null){
                        $lDadosVideo["videoId"] = $item->id->videoId;
                        $lDadosVideo["publishedAt"] = $item->snippet->publishedAt;
                        $lDadosVideo["title"] = $item->snippet->title;
                        $lDadosVideo["description"] = $item->snippet->description;
                        $lDadosVideo["thumbnail"] = $item->snippet->thumbnails->medium->url;
                        $lDadosVideo["thumbnail_high"] = $item->snippet->thumbnails->high->url;
                        $dadosVideo[] = $lDadosVideo;
                    } 
                }
            }
        }
        return $dadosVideo;
    }
    /**
     * Retorna true se for inválido
     * @param type $token
     * @return boolean
     */
    public static function validaCaptcha($token){
        try{
            $url = self::GOOGLE_API_RECAPTCHA;
            $data = array(
                'secret' => SolvesGoogleApi::$RECAPTCHA_SECRET_KEY,
                'response' => $token
            );
            $options = array(
                'http' => array (
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context  = stream_context_create($options);
            $verify = file_get_contents($url, false, $context);
            $captcha_success=json_decode($verify);
            if ($captcha_success->success==false) {
                return true;
            } else if ($captcha_success->success==true) {
                        
                return false;
            }
        }catch (\Exception $ex){
            error_log("ERRO RECAPTCHA: " . print_r($ex , true));
        }
    }
     public static function getEstatisticasCanal($idCanal){
        $dadosVideo = array("subscriberCount" => 0 , 
                            "videoCount" => 0, 
                            "viewCount" => 0,
                            "commentCount" =>0,
                            "hiddenSubscriberCount" =>0);
        
        try{
        if($idCanal){
            $key = SolvesGoogleApi::getAvailableKey();
            $URL = $key->getYoutubeApiUrlEstatisticasCanal()."id=".urlencode($idCanal);
            try{
            //echo $URL . "<br/><br/>";
                $responseDados = @file_get_contents($URL);
                $jsonDados = @json_decode($responseDados);
            } catch (\Exception $e){
                
                error_log(print_r($e, true));
                return $dadosVideo;
            }
            if($jsonDados != null && count($jsonDados->items) > 0){
                foreach ($jsonDados->items as $item){
                    if(@$item->statistics->subscriberCount != null){
                        $dadosVideo["subscriberCount"] = @$item->statistics->subscriberCount;
                        $dadosVideo["videoCount"] = @$item->statistics->videoCount;
                        $dadosVideo["viewCount"] = @$item->statistics->viewCount;
                        $dadosVideo["commentCount"] = @$item->statistics->commentCount;
                        $dadosVideo["hiddenSubscriberCount"] = @$item->statistics->hiddenSubscriberCount;
                    } 
                }
            }
        }
        } catch (\Exception $ex){
            error_log(print_r($ex, true));
        }
        return $dadosVideo;
     }
}

class SolvesYoutubeApiKey{
    const LIMIT_QUOTA_PER_DAY = 10000;
    
    private $quotaUsed=0;
    private $quotaDay=null;
    private $i=0;

    private $YOUTUBE_API_KEY = "";    
    private $YOUTUBE_API_URL_DADOS = "";
    private $YOUTUBE_API_URL_DADOS_QUERY = "";
    private $YOUTUBE_API_URL_ESTATISTICAS = "";
    private $YOUTUBE_API_URL_ESTATISTICAS_CANAL = "";
    private $YOUTUBE_API_URL_VIDEO_ESTATISTICAS = "";
    private $YOUTUBE_API_URL_SEARCH_VIDEO = "";

    public function __construct($i, $youtubeApiKey){
        $this->i = $i;

        $this->YOUTUBE_API_KEY = $youtubeApiKey;
        $this->YOUTUBE_API_URL_DADOS = SolvesGoogleApi::getGoogleApiYoutubeV3()."channels?part=snippet&maxResults=50&key=". $this->YOUTUBE_API_KEY."&";
        $this->YOUTUBE_API_URL_DADOS_QUERY = SolvesGoogleApi::getGoogleApiYoutubeV3()."search?part=snippet&type=channel&maxResults=50&key=". $this->YOUTUBE_API_KEY."&";
        
        $this->YOUTUBE_API_URL_ESTATISTICAS = SolvesGoogleApi::getGoogleApiYoutubeV3()."channels?part=statistics&key=".$this->YOUTUBE_API_KEY."&";
        $this->YOUTUBE_API_URL_ESTATISTICAS_CANAL = SolvesGoogleApi::getGoogleApiYoutubeV3()."channels?part=statistics&key=".$this->YOUTUBE_API_KEY."&";
        $this->YOUTUBE_API_URL_VIDEO_ESTATISTICAS = SolvesGoogleApi::getGoogleApiYoutubeV3()."videos?part=statistics&key=".$this->YOUTUBE_API_KEY."&id=";
        
        $this->YOUTUBE_API_URL_SEARCH_VIDEO = SolvesGoogleApi::getGoogleApiYoutubeV3()."search?part=snippet&key=".$this->YOUTUBE_API_KEY."&type=video&order=date&channelId=";
    }

    public function getYoutubeApiKey(){return $this->YOUTUBE_API_KEY; }
    public function getYoutubeApiUrlDados(){return $this->YOUTUBE_API_URL_DADOS; }
    public function getYoutubeApiUrlDadosQuery(){return $this->YOUTUBE_API_URL_DADOS_QUERY; }
    public function getYoutubeApiUrlEstatisticas(){return $this->YOUTUBE_API_URL_ESTATISTICAS; }
    public function getYoutubeApiUrlEstatisticasCanal(){return $this->YOUTUBE_API_URL_ESTATISTICAS_CANAL; }
    public function getYoutubeApiUrlVideoEstatisticas(){return $this->YOUTUBE_API_URL_VIDEO_ESTATISTICAS; }
    public function getYoutubeApiUrlSearchVideo($canal, $limit){return $this->YOUTUBE_API_URL_SEARCH_VIDEO. urlencode($canal) . '&maxResults='.$limit; }

    public function addQuota(){
        if($this->quotaDay==null ||  $this->quotaUsed || \Solves\SolvesTime::getDataAtual()!=$this->quotaDay){
            $this->quotaDay = \Solves\SolvesTime::getDataAtual();
            $this->quotaUsed=0;
        }
        $this->quotaUsed++;
    }
    public function setQuotaExceded(){
        $this->quotaUsed = self::LIMIT_QUOTA_PER_DAY;
    }
    public function isExceeded(){
        return self::LIMIT_QUOTA_PER_DAY<=$this->quotaUsed;
    }

    public function __toString(){
        return "KEY:".$this->YOUTUBE_API_KEY.";quota_day:".$this->quotaDay.";quotaUsed:".$this->quotaUsed.";";
    }
}