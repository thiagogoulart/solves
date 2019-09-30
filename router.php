<?php
  $ROUTER = new \Solves\SolvesRouter($_SERVER, $_POST, $_GET, $_PUT, $_DELETE, $_FILES, $navInside);
  $ROUTER->doIncludePage();
?>