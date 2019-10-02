<div class="container">
    <h1>Página não encontrada!</h1>
    Esta URL não está disponível (ERRO 404).<br/>
    <a href="<?php echo \Solves\Solves::getIndexUrl($IS_APP); ?>" alt="Página Inicial de <?php echo \Solves\Solves::getSiteTitulo(); ?>" title="Página Inicial de <?php echo \Solves\Solves::getSiteTitulo(); ?>">Volte para a Página Inicial</a>

    <div id="erro_details" class="mt-5 pb-0" style="display:none">
        <b>ATUAL_URL:</b> <?php echo $ROUTER->ATUAL_URL; ?><br/>
        <b>CANNONICAL:</b> <?php echo $ROUTER->CANNONICAL; ?><br/>
        <b>MODO_SOON_ATIVADO:</b> <?php echo ($ROUTER->MODO_SOON_ATIVADO ? 'Sim' : 'Não'); ?><br/>
        <b>IS_SOON_PAGE:</b> <?php echo ($ROUTER->IS_SOON_PAGE ? 'Sim' : 'Não'); ?><br/>
        <b>IS_APP:</b> <?php echo ($ROUTER->IS_APP ? 'Sim' : 'Não'); ?><br/>
        <b>restService:</b> <?php echo $ROUTER->restService; ?><br/>
        <b>restDetails:</b> <?php echo $ROUTER->restDetails; ?><br/>
        <b>p:</b> <?php echo $ROUTER->p; ?>
    </div>
    <div class="mt-3"><button class="btn btn-danger" onclick="$('#erro_details').show()">Ver detalhes do erro</button></div>
  </div>'