<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 30/07/2019
 */ 
namespace SolvesUi;


class SolvesServiceWorker {

    public static function getScript(){
        $script = '
const CACHE = "'.\SolvesUi\SolvesUi::getCacheUiVersion().'";
const precacheFiles = [
    "'.\Solves\Solves::getRelativePath('offline').'",
    "'.\Solves\Solves::getRelativePath('manifest.webmanifest').'"';

/*CSS*/
    $cssFilePaths = \SolvesUi\SolvesUi::getCssFilePaths();
    foreach($cssFilePaths as $cssFilePath){
        $script .= ', "'.$cssFilePath.'"';
    }


/*JS*/
    $jsFilePaths = \SolvesUi\SolvesUi::getScriptFilePaths();
    foreach($jsFilePaths as $js){
        $jsFilePath = \SolvesUi\SolvesUi::getSingleScriptFilePath($js);
        if(\Solves\Solves::getRelativePath('sw_register.js')==$jsFilePath || \Solves\Solves::getRelativePath('sw.js')==$jsFilePath || "https://www.youtube.com/iframe_api"==$jsFilePath){
            continue;
        }
        $script .= ', "'.$jsFilePath.'"';
    }

$script .= '
];
const offlineFallbackPage = "'.\Solves\Solves::getRelativePath('offline').'";
const networkFirstPaths = [
            "'.\Solves\Solves::getRelativePath('admin').'",
            "'.\Solves\Solves::getRelativePath('rest').'",
            "'.\Solves\Solves::getRelativePath('controller').'",
            "'.\Solves\Solves::getRelativePath('sw.js').'",
            "'.\Solves\Solves::getRelativePath('sw_register.js').'"
            ];
const avoidCachingPaths = [
            "'.\Solves\Solves::getRelativePath('admin').'",
            "'.\Solves\Solves::getRelativePath('rest').'",
            "'.\Solves\Solves::getRelativePath('controller').'",
            "'.\Solves\Solves::getRelativePath('sw.js').'",
            "'.\Solves\Solves::getRelativePath('sw_register.js').'"
            ];
let newWorker;

function pathComparer(requestUrl, pathRegEx) {
  return requestUrl.match(new RegExp(pathRegEx));
}
function comparePaths(requestUrl, pathsArray) {
  if (requestUrl) {
    for (let index = 0; index < pathsArray.length; index++) {
      const pathRegEx = pathsArray[index];
      if (pathComparer(requestUrl, pathRegEx)) {
        return true;
      }
    }
  }
  return false;
}

self.addEventListener("install", function (event) {
  console.log("[PWA Builder] Install Event processing");
  console.log("[PWA Builder] Skip waiting on install");
  self.skipWaiting();

  event.waitUntil(
    caches.open(CACHE).then(function (cache) {
      console.log("[PWA Builder] Caching pages during install");
      return cache.addAll(precacheFiles).then(function () {
        return cache.add(offlineFallbackPage);
      }).catch(err => console.log("Error while fetching assets on serviceWorker.", err));
    })
  );
});

self.addEventListener("message", function(event) {
  if (event.data.action === "skipWaiting") {
    self.skipWaiting();
  }
});

// Allow sw to control of current page
self.addEventListener("activate", function (event) {
  console.log("[PWA Builder] Claiming clients for current page");
  event.waitUntil(self.clients.claim());
});

// If any fetch fails, it will look for the request in the cache and serve it from there first
self.addEventListener("fetch", function (event) {
  if (event.request.method !== "GET") return;

  if (comparePaths(event.request.url, networkFirstPaths)) {
    networkFirstFetch(event);
  } else {
    cacheFirstFetch(event);
  }
});

self.addEventListener("push", function (event) {
    if (!(self.Notification && self.Notification.permission === "granted")) {
        return;
    }
    const sendNotification = json => {
        // you could refresh a notification badge here with postMessage API
        const title = (json.title?json.title:null);
        self.registration.showNotification(title, json);
    };
    if (event.data) {
        const json = event.data.json();
        event.waitUntil(sendNotification(json));
    }
});
function cacheFirstFetch(event) {
  event.respondWith(
    fromCache(event.request).then(
      function (response) {
        // The response was found in the cache so we responde with it and update the entry
        // This is where we call the server to get the newest version of the
        // file to use the next time we show view
        event.waitUntil(
          fetch(event.request).then(function (response) {
            return updateCache(event.request, response);
          })
        );

        return response;
      },
      function () {
        // The response was not found in the cache so we look for it on the server
        return fetch(event.request)
          .then(function (response) {
            // If request was success, add or update it in the cache
            event.waitUntil(updateCache(event.request, response.clone()));
            return response;
          })
          .catch(function (error) {
            // The following validates that the request was for a navigation to a new document
            if (event.request.destination !== "document" || event.request.mode !== "navigate") {
              return;
            }

            console.log("[PWA Builder] Network request failed and no cache." + error);
            // Use the precached offline page as fallback
            return caches.open(CACHE).then(function (cache) {
              cache.match(offlineFallbackPage);
            });
          });
      }
    )
  );
}

function networkFirstFetch(event) {
  event.respondWith(
    fetch(event.request)
      .then(function (response) {
        // If request was success, add or update it in the cache
        event.waitUntil(updateCache(event.request, response.clone()));
        return response;
      })
      .catch(function (error) {
        console.log("[PWA Builder] Network request Failed. Serving content from cache: " + error);
        return fromCache(event.request);
      })
  );
}

function fromCache(request) {
  // Check to see if you have it in the cache
  // Return response
  // If not in the cache, then return error page
  return caches.open(CACHE).then(function (cache) {
    return cache.match(request).then(function (matching) {
      if (!matching || matching.status === 404) {
        return Promise.reject("no-match");
      }
      return matching;
    });
  });
}

function updateCache(request, response) {
  if (!comparePaths(request.url, avoidCachingPaths)) {
    return caches.open(CACHE).then(function (cache) {
      return cache.put(request, response);
    });
  }
  return Promise.resolve();
}
';

        return $script;
    }
}