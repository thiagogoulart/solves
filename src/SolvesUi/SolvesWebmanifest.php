<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 12/08/2019
 */ 
namespace SolvesUi;


class SolvesWebmanifest {

    public static function getManifest(){
      $relatedAppsJson = SolvesWebmanifest::getRelatedAppsJson();

        $result = '{
      "name": "'.\Solves\Solves::getSiteTitulo().'",
      "short_name": "'.\Solves\Solves::getSystemName().'",
      "start_url": "'.\Solves\Solves::getAppUrl().'?utm_source=homescreen",
      "display": "standalone",
      "background_color": "'.\SolvesUi\SolvesUi::getThemeBackgroundColor().'",
      "theme_color": "'.\SolvesUi\SolvesUi::getThemeColor().'",
      "orientation": "any",
      "description": "'.\Solves\Solves::getSiteDescr().'",'.
      (\Solves\Solves::isNotBlank(\SolvesNotification\SolvesNotification::getSenderId()) ? 
          '"gcm_sender_id": "'.\SolvesNotification\SolvesNotification::getSenderId().'",':
      '').
      '"icons": [{
        "src": "'.\Solves\Solves::getCompleteImgPathLogo().'favicon-48x48.png", 
        "sizes": "48x48",
        "type": "image/png"
      }, {
        "src": "'.\Solves\Solves::getCompleteImgPathLogo().'android-72x72.png",
        "sizes": "72x72",
        "type": "image/png"
      }, {
        "src": "'.\Solves\Solves::getCompleteImgPathLogo().'favicon-96x96.png",
        "sizes": "96x96",
        "type": "image/png"
      }, {
        "src": "'.\Solves\Solves::getCompleteImgPathLogo().'favicon-128x128.png",
        "sizes": "128x128",
        "type": "image/png"
      }, {
        "src": "'.\Solves\Solves::getCompleteImgPathLogo().'mstile-144x144.png",
        "sizes": "144x144",
        "type": "image/png"
      }, {
        "src": "'.\Solves\Solves::getCompleteImgPathLogo().'apple-touch-icon-152x152.png", 
        "sizes": "152x152",
        "type": "image/png"
      }, {
        "src": "'.\Solves\Solves::getCompleteImgPathLogo().'apple-touch-icon-167x167.png", 
        "sizes": "168x168",
        "type": "image/png"
      }, {
        "src": "'.\Solves\Solves::getCompleteImgPathLogo().'pwa-192x192.png",
        "sizes": "192x192",
        "type": "image/png"
      }, {
        "src": "'.\Solves\Solves::getCompleteImgPathLogo().'logo-384x384.png",
        "sizes": "384x384",
        "type": "image/png"
      }, {
        "src": "'.\Solves\Solves::getCompleteImgPathLogo().'pwa-512x512.png",
        "sizes": "512x512",
        "type": "image/png"
      }],
      "related_applications": [
          {"platform": "web","url": "'.\Solves\Solves::getSiteUrl().'"}
          '.$relatedAppsJson.'
        ]
    }
    ';

        return $result;
    }

    private static function getRelatedAppsJson(){
      $json = '';
      if(\Solves\Solves::isNotBlank(\Solves\Solves::getGooglePlayStoreLink())){
        $json .= ',{"platform": "play","url": "'.\Solves\Solves::getGooglePlayStoreLink().'"}';
      }
      if(\Solves\Solves::isNotBlank(\Solves\Solves::getAppleStoreLink())){
        $json .= ',{"platform": "itunes","url": "'.\Solves\Solves::getAppleStoreLink().'"}';
      }
      if(\Solves\Solves::isNotBlank(\Solves\Solves::getWindowsStoreLink())){
        $json .= ',{"platform": "windows","url": "'.\Solves\Solves::getWindowsStoreLink().'"}';
      }
      return $json;
    }
}