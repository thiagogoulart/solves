<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 31/07/2019
 */ 
namespace SolvesUi;


class SolvesServiceWorkerRegister {

    public static function getScript(){
        $script = ' 
 function showUpdateBar() {
    $("#notification_new_version").show();
  }
  let newWorker;

  // The click event on the pop up notification
  document.getElementById("reload_new_version").addEventListener("click", function(){
    if(newWorker!==undefined){newWorker.postMessage({ action: "skipWaiting" });}
  });

if ("serviceWorker" in navigator) {
  window.addEventListener("load", function() {
    // Registra um service worker hospeadado na raiz do
    // site usando o escopo padrão
    navigator.serviceWorker.register("'.\Solves\Solves::getRelativePath('sw.js').'").then(function(reg ) {
      console.log("Service worker  registrado com sucesso:", reg );
      try {
        $.SolvesNotifications.push_updateSubscription();
      } catch (err) {
        console.log(err);
      }
      
      try {
        firebase.messaging().useServiceWorker(reg);
      } catch (err) {
        console.log(err);
      }

      reg.addEventListener("updatefound", () => {
        // A wild service worker has appeared in reg.installing!
        newWorker = reg.installing;
        newWorker.addEventListener("statechange", () => {
          // Has network.state changed?
          switch (newWorker.state) {
            case "installed":
              if (navigator.serviceWorker.controller) {
                // new update available
                showUpdateBar();
              }
              // No update available
              break;
          }
        });
      });


    }).catch(function(error) {
      console.log("Falha ao Registrar o Service Worker:", error);
    });
    let refreshing;

    // Esse evento será chamado quando o Service Worker for atualizado
    // Aqui estamos recarregando a página
    navigator.serviceWorker.addEventListener("controllerchange", function() {
      if (refreshing) {
        return;
      }
      window.location.reload();
      refreshing = true;
    });

  });

} else {
  console.log("Service workers não suportado!");
}
';

        return $script;
    }
}