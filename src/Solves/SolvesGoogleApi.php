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
    private static $YOUTUBE_API_KEY = "";
    
    private static  $YOUTUBE_API_URL_DADOS = "";
    private static  $YOUTUBE_API_URL_DADOS_QUERY = "";
    private static  $YOUTUBE_API_URL_ESTATISTICAS = "";
    private static  $YOUTUBE_API_URL_ESTATISTICAS_CANAL = "";
    private static  $YOUTUBE_API_URL_VIDEO_ESTATISTICAS = "";
    private static  $YOUTUBE_API_URL_SEARCH_VIDEO = "";
    
    
    
    public static function configRecaptchaSecretKey($recaptchaKey){
        SolvesGoogleApi::setRecaptchaSecretKey($recaptchaKey);
    }
    public static function configYoutubeKey($youtubeKey){
        SolvesGoogleApi::setYoutubeApiKey($youtubeKey);
    }

    public static function getRecaptchaSecretKey(){return SolvesGoogleApi::$RECAPTCHA_SECRET_KEY;}
    public static function setRecaptchaSecretKey($p){SolvesGoogleApi::$RECAPTCHA_SECRET_KEY = $p;}

    public static function getYoutubeApiKey(){return SolvesGoogleApi::$YOUTUBE_API_KEY;}
    public static function setYoutubeApiKey($p){
        SolvesGoogleApi::$YOUTUBE_API_KEY = $p;
        SolvesGoogleApi::$YOUTUBE_API_URL_DADOS = self::GOOGLE_API_YOUTUBE_V3."channels?part=snippet&maxResults=50&key=". SolvesGoogleApi::$YOUTUBE_API_KEY."&";
        SolvesGoogleApi::$YOUTUBE_API_URL_DADOS_QUERY = self::GOOGLE_API_YOUTUBE_V3."search?part=snippet&type=channel&maxResults=50&key=". SolvesGoogleApi::$YOUTUBE_API_KEY."&";
        
        SolvesGoogleApi::$YOUTUBE_API_URL_ESTATISTICAS = self::GOOGLE_API_YOUTUBE_V3."channels?part=statistics&key=".SolvesGoogleApi::$YOUTUBE_API_KEY."&";
        SolvesGoogleApi::$YOUTUBE_API_URL_ESTATISTICAS_CANAL = self::GOOGLE_API_YOUTUBE_V3."channels?part=statistics&key=".SolvesGoogleApi::$YOUTUBE_API_KEY."&";
        SolvesGoogleApi::$YOUTUBE_API_URL_VIDEO_ESTATISTICAS = self::GOOGLE_API_YOUTUBE_V3."videos?part=statistics&key=".SolvesGoogleApi::$YOUTUBE_API_KEY."&id=";
        
        SolvesGoogleApi::$YOUTUBE_API_URL_SEARCH_VIDEO = self::GOOGLE_API_YOUTUBE_V3."search?part=snippet&key=".SolvesGoogleApi::$YOUTUBE_API_KEY."&type=video&order=date&channelId=";
    
    }
    public static function getEstatisticasVideo($idVideo){
        $dadosVideo = array("likeCount" => 0 , "dislikeCount"=>0, "viewCount"=>0 , "commentCount"=>0, "favoriteCount"=>0);
        try{
        if($idVideo){
            $URL = SolvesGoogleApi::$YOUTUBE_API_URL_VIDEO_ESTATISTICAS . urlencode($idVideo);
            
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

        if(strpos($url_canal , "user/") !== false){

            $ar_link = explode("user/" , $url_canal);
            $URL = SolvesGoogleApi::$YOUTUBE_API_URL_DADOS . "forUsername=" + trim($ar_link[1]);

        } else if(strpos($url_canal ,"channel/") !== false){
            $ar_link = explode("channel/" , $url_canal);
            $URL = SolvesGoogleApi::$YOUTUBE_API_URL_DADOS . "id=" . trim($ar_link[1]);
        }else if(strpos($url_canal ,"c/") !== false){
            $ar_link = explode("c/" , $url_canal);
            $URL = SolvesGoogleApi::$YOUTUBE_API_URL_DADOS_QUERY . "q=" .  urlencode($ar_link[1]);
        }else if(strpos($url_canal ,"youtube.com/") !== false){
            $ar_link = explode("youtube.com/" , $url_canal);
            if($ar_link[1] != "" && $ar_link[1] != null){
                $URL = SolvesGoogleApi::$YOUTUBE_API_URL_DADOS_QUERY . "q=" .  urlencode($ar_link[1]);
            }
        }else if(strpos($url_canal ,"user/") !== false){
            $ar_link = explode("user/" , $url_canal);
            if($ar_link[1] != "" && $ar_link[1] != null){
                $URL = SolvesGoogleApi::$YOUTUBE_API_URL_DADOS . "forUsername=" . $ar_link[1];
            }
        }else if($url_canal != "" && strpos($url_canal ,"http") === false){
            $URL = SolvesGoogleApi::$YOUTUBE_API_URL_DADOS_QUERY . "q=" . urlencode($url_canal);
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
            
            if($jsonDados != null && count($jsonDados->items) > 0){
                foreach ($jsonDados->items as $item){
                    if(@$item->snippet->channelId != null){
                        
                        $avatar = @$item->snippet->thumbnails->medium->url;
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
                        $avatar = @$item->snippet->thumbnails->medium->url;
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
                
            }

        }
        
        return $ar_canais;
    }
    public static function getListaVideos($canal , $limit = 50){
        //$dadosVideo = array("videoId" => "" ,"publishedAt" => "" , "title"=>"", "thumbnail"=>"" );
        $dadosVideo = null;
        if($canal){
            $URL = SolvesGoogleApi::$YOUTUBE_API_URL_SEARCH_VIDEO . urlencode($canal) . '&maxResults='.$limit;
            
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
            $URL = SolvesGoogleApi::$YOUTUBE_API_URL_ESTATISTICAS_CANAL."id=".urlencode($idCanal);
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