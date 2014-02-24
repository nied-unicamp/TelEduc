<?php
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("enquete.inc");

  $cod_ferramenta=24;

  include("../topo_tela.php");

  /* INICIO - JavaScript */
  echo("<script type=\"text/javascript\">\n\n");
  echo("  function Iniciar()\n");
  echo("  {\n");
  echo("    startList();\n");
  echo("  }\n"); 

  /* Volta a Pagina de edicao desta Enquete */
  echo("  function VoltaPaginaPrincipal(atualizacao)\n");
  echo("  {\n");
  echo("    document.location.href='enquete.php?cod_curso=".$cod_curso."&acao=votarEnquete&atualizacao='+atualizacao;\n");
  echo("    return(true);\n");
  echo("  }\n\n");
  echo("</script>\n\n");
  /* FIM - JavaScript */

  include("../menu_principal.php");

  $enquete = getEnquete($sock, $idEnquete);
  $status_enquete = getStatusEnquete($sock, $enquete);
  $input_type = getInputType($enquete['num_escolhas']);
  $ator = getTipoAtor($sock, $cod_curso, $cod_usuario);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* SE tem permiss� para votar e a enquete est�em andamento, pode acessar */
  if (($vota = votaEnquete($sock, $ator, $enquete)) && ((strcmp($status_enquete, "ANDAMENTO") == 0)))
  {

    //se s�puder votar em uma alternativa e estiver tentando votar em mais de uma, s�insere o voto da primeira
    if(strcmp($input_type, "RADIO") == 0)
    {
      $resposta = array_slice($resposta, 0, 1);
    }

    if ($status = insertVoto($sock, $cod_usuario, $idEnquete, $resposta))
    {
      $atualizacao="true";
      Desconectar($sock);
      echo("  <script type=\"text/javascript\" language=\"javascript\">VoltaPaginaPrincipal('".$atualizacao."');</script>");
      exit;
    }
    else
    {
      $atualizacao="false";
      Desconectar($sock);
      echo("  <script type=\"text/javascript\" language=\"javascript\">VoltaPaginaPrincipal('".$atualizacao."');</script>");
      exit;
    }
  }
  else
  {
    /* 1 - Enquete */
    /* 52- Votar Enquete */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,52)."</h4>\n");
    /* 509 - Voltar */
    echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
    
    if(!$vota)
    {
      /* 93 - Voc�n� tem permiss� para votar nesta enquete. */
      echo("          <p>".RetornaFraseDaLista($lista_frases, 93)."</p>");
    }
    if((strcmp($status_enquete, "ANDAMENTO") != 0))
    {
      /* 94 - A consulta para esta enquete j�terminou.  */
      echo("          <p>".RetornaFraseDaLista($lista_frases, 94)."</p>");
    }

    echo("          <form name=\"voltar\" action=\"enquete.php?cod_curso=".$cod_curso."\" method=\"post\">\n");
    /* 23 - Voltar */
    echo("            <input type=\"submit\" class=\"input\" value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
    echo("          </form>\n");
  }

  echo("        </td>\n");
  echo("      </tr>\n"); 
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
  exit;
?>
