<?php
  $navInside = '';
  $navInsideConfs = '';

  $ROUTER = new \Solves\SolvesRouter($_SERVER, $_POST, $_GET, $_PUT, $_DELETE);
  $ROUTER->doIncludePage();
?>