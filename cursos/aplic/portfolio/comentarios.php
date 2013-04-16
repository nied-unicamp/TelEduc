<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/portfolio/comentarios.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
    Copyright (C) 2001  NIED - Unicamp

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2 as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    You could contact us through the following addresses:

    Nied - Ncleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/portfolio/comentarios.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("portfolio.inc");
  include("avaliacoes_portfolio.inc");

  // Descobre os diretorios de arquivo, para os comentarios com anexo
  $sock = Conectar("");
  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');
  Desconectar($sock);

  $cod_ferramenta = 15;
  $cod_ferramenta_ajuda = 15;
  $cod_pagina_ajuda = 4;
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("comentar", 107, sprintf(RetornaFraseDaLista($lista_frases,189), ((int) ini_get('upload_max_filesize'))));

  
  /* 1 - Portffolio */
  echo("    <script type=\"text/javascript\">\n");

  echo("      function OpenWindowPerfil(id)\n");
  echo("      {\n");
  echo("         window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+id,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n");

 
  echo("      function Iniciar()\n");
  echo("      {\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");

  echo("      function WindowOpenVer(id)\n");
  echo("      {\n");
  echo("         window.open('".$dir_item_temp['link']."'+id+'?".time()."','Portfolio','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
  echo("      }\n\n");

  echo("      var many_arqs=0;\n\n");
  echo("      function removeInputFile(numero){\n");
  echo("        elementoDiv = document.getElementById('text_coment');\n");
  echo("        elementoDiv.removeChild(document.getElementById('br_'+numero));\n");
  echo("        elementoDiv.removeChild(document.getElementById('remover_arquivo_'+numero));\n");
  echo("        elementoDiv.removeChild(document.getElementById('space_'+numero));\n");
  echo("        elementoDiv.removeChild(document.getElementById('input_file_'+numero));\n");
  echo("      }\n\n");

  echo("      function addInputFile(){\n");
  echo("        var num = many_arqs;\n");
  echo("        elementoDiv = document.getElementById('text_coment');\n");
  echo("        while(elementoDiv.lastChild.tagName!=\"IMG\")\n");
  echo("          elementoDiv.removeChild(elementoDiv.lastChild);\n");
  echo("        elementoDiv.removeChild(elementoDiv.lastChild);\n");
  echo("        inputFile=document.createElement('input');\n");
  echo("        inputFile.setAttribute(\"type\", \"file\");\n");
  echo("        inputFile.setAttribute(\"size\", \"40\");\n");
  echo("        inputFile.setAttribute(\"name\", \"input_files[]\");\n");
  echo("        inputFile.setAttribute(\"id\", \"input_file_\"+many_arqs);\n");
  echo("        inputFile.setAttribute(\"style\", \"border:2px solid #9bc\");\n\n");
  echo("        createSpace=document.createElement('span');\n");
  echo("        createSpace.setAttribute(\"id\", \"space_\"+many_arqs);\n");
  echo("        createSpace.innerHTML=\"&nbsp;&nbsp;&nbsp;\"\n");
  echo("        createSpan = document.createElement('span');\n");
  echo("        createSpan.onclick = function() { removeInputFile(num); };\n");
  echo("        createSpan.setAttribute(\"id\", \"remover_arquivo_\"+many_arqs);\n");
  echo("        createSpan.className=\"link\";\n");
  echo("        createSpan.innerHTML=\"".RetornaFraseDaLista($lista_frases,182)."\";\n\n");
  echo("        createBr = document.createElement('br');\n");
  echo("        createBr.setAttribute(\"id\", \"br_\"+many_arqs);\n\n");
  echo("        createImg = document.createElement('img');\n");
  echo("        createImg.setAttribute(\"src\", \"../imgs/paperclip.gif\");\n");
  echo("        createImg.setAttribute(\"border\", \"0\");\n");
  echo("        createSpan2 = document.createElement('span');\n");
  echo("        createSpan2.className=\"link\";\n");
  echo("        createSpan2.onclick = function (){ addInputFile(); };\n");
  echo("        createSpan2.setAttribute(\"id\", \"anexar_arquivo\");\n");
  echo("        createSpan2.innerHTML=\"".RetornaFraseDaLista($lista_frases,57)."\";\n\n");
  echo("        elementoDiv.appendChild(inputFile);\n");
  echo("        elementoDiv.appendChild(createSpace);\n");
  echo("        elementoDiv.appendChild(createSpan);\n");
  echo("        elementoDiv.appendChild(createBr);\n");
  echo("        elementoDiv.appendChild(createImg);\n\n");
  echo("        elementoDiv.appendChild(createSpan2);\n\n");
  echo("        many_arqs++;\n");
  echo("      }\n\n");

  echo("      function EnviarComent(){\n");
  echo("        document.getElementById('OKComent').style.visibility='visible';\n");
  echo("        document.getElementById('cancComent').style.visibility='visible';\n");
  echo("        document.getElementById('textArea_coment').style.visibility='visible';\n");
  echo("        document.getElementById('td_coment').style.background='';\n");
  echo("        document.getElementById('text_coment').className='divShow';\n");
  echo("        document.getElementById('button_coment').className='divShow';\n");
  echo("        elementoDiv = document.getElementById('text_coment');\n");
  echo("        elementoDiv.removeChild(elementoDiv.lastChild);\n");
  echo("        document.getElementById('btnComentar').onclick = function() {};\n");
  echo("      }\n\n");

  echo("      function CancelarComent(){\n");
  echo("        document.getElementById('textArea_coment').value='';\n");
  echo("        document.getElementById('td_coment').style.background='#DCDCDC';\n");
  echo("        document.getElementById('text_coment').className='divHidden';\n");
  echo("        document.getElementById('button_coment').className='divHidden';\n");
  echo("        elementoDiv = document.getElementById('text_coment');\n");
  echo("        createBr = document.createElement('br');\n");
  echo("        elementoDiv.appendChild(createBr);\n");
  echo("        document.getElementById('btnComentar').onclick = function() { EnviarComent(); };\n");
  echo("        element=document.getElementsByName('input_files[]');\n");
  echo("        for (i=0; i < element.length; i++){\n");
  echo("          document.getElementById('text_coment').removeChild(element[i].nextSibling);\n");
  echo("          document.getElementById('text_coment').removeChild(element[i].nextSibling);\n");
  echo("          document.getElementById('text_coment').removeChild(element[i].nextSibling);\n");
  echo("          document.getElementById('text_coment').removeChild(element[i]);\n");
  echo("          i--;\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function submitForm(){\n");
  echo("        if(document.getElementById('textArea_coment').value==''){\n");
                  /* 106 - Seu coment�io est�vazio. Para n� envi�lo, pressione o botao Cancelar. */
  echo("          alert('".RetornaFraseDaLista($lista_frases,106)."');\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        element=document.getElementsByName('input_files[]');\n");
  echo("        for (i=0; i < element.length; i++){\n");
  echo("          if((element[i].value)==\"\"){\n");
  echo("            document.getElementById('text_coment').removeChild(element[i].nextSibling);\n");
  echo("            document.getElementById('text_coment').removeChild(element[i].nextSibling);\n");
  echo("            document.getElementById('text_coment').removeChild(element[i].nextSibling);\n");
  echo("            document.getElementById('text_coment').removeChild(element[i]);\n");
  echo("            i--;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        return true;\n");
  echo("      }\n\n");

  
  echo("    </script>\n");
  include("../menu_principal.php");
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");


  /* Verificação se o item está em Edição */
  /* Se estiver, voltar a tela anterior, e disparar a tela de Em Edição... */
  $linha=RetornaUltimaPosicaoHistorico ($sock, $cod_item);
  if ($linha['acao']=="E")
  {
    if ($linha_item['inicio_edicao']<(time()-1800) || $cod_usuario == $linha_historico['cod_usuario']){
      AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario);
    }else{
      /* Está em edição... */
      echo("          <script type=\"text/javascript\">\n");
      echo("            window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver&amp;cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
      echo("            document.location='portfolio.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha_item['cod_item']."&origem=ver&cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."&amp;cod_topico_raiz=".$cod_topico_raiz."';\n");
      echo("          </script>\n");
      echo("        </td>\n");
      echo("      </tr>\n");
      echo("    </table>\n");
      echo("  </body>\n");
      echo("</html>\n");
      exit();
    }
  }

  $eformador=EFormador($sock,$cod_curso,$cod_usuario);

  $status_portfolio = RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo_portfolio);

  $dono_portfolio    = $status_portfolio ['dono_portfolio'];
  $portfolio_apagado = $status_portfolio ['portfolio_apagado'];
  $portfolio_grupo   = $status_portfolio ['portfolio_grupo'];

  if ($acao=="mudarcomp" && $dono_portfolio)
  {
    MudarCompartilhamento($sock, $cod_item, $tipo_comp);
  }
  /* P�ina Principal */
//   $ferramenta_avaliacao = TestaAcessoAFerramenta($sock, $cod_curso, $cod_usuario, COD_AVALIACAO)
  $ferramenta_avaliacao = false;

  if ($ferramenta_avaliacao)
  {
    if ($ferramenta_grupos_s && $cod_grupo_portfolio != '')
    {
      // 3 - Portfolios de grupos
      $cod_frase  =  3;
      $cod_pagina = 24;
    }
    else
    {
      // 2 - Portfolios individual
      $cod_frase  =  2;
      $cod_pagina = 20;
    }
  }
  else
  {
    if ($ferramenta_grupos_s && $cod_grupo_portfolio != '')
    {
      // 3 - Portfolios de grupos
      $cod_frase = 3;
      $cod_pagina=11;
    }
    else
    {
      // 2 - Portfolios individual
      $cod_frase = 2;
      $cod_pagina=5;
    }
  }


  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases, $cod_frase)."</h4>\n");



  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  $lista_topicos_ancestrais=RetornaTopicosAncestrais($sock, $cod_topico_raiz);
  unset($path);
  foreach ($lista_topicos_ancestrais as $cod => $linha)
  {
    if ($cod_topico_raiz!=$linha['cod_topico'])
    {
      $path="<a href=\"portfolio.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$linha['cod_topico']."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">".$linha['topico']."</a> &gt;&gt; ".$path;
    }
    else
    {
      $path="<a href=\"portfolio.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$linha['cod_topico']."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">".$linha['topico']."</a>";
    }
  }

  if ($portfolio_grupo)
  {
    $nome=NomeGrupo($sock,$cod_grupo_portfolio);

    //Figura de Grupo
    $fig_portfolio = "<img alt=\"\" src=\"../imgs/icGrupo.gif\" border=\"0\" />";


    /* 84 - Grupo Exclu�o */
    if ($grupo_apagado && $eformador) $complemento=" <span>(".RetornaFraseDaLista($lista_frases,84).")</span>\n";

    echo("          <a href=\"#\" onclick=\"return(AbreJanelaComponentes(".$cod_grupo_portfolio."))\";>".$fig_portfolio." ".$nome."</a>".$complemento." - ");
    echo("          <a href=\"#\" onMouseDown=\"MostraLayer(cod_topicos,0);return(false);\"><img  alt=\"\" src=\"../imgs/estrutura.gif\" border=\"0\" /></a>");
  }
  else
  {
    $nome=NomeUsuario($sock,$cod_usuario_portfolio, $cod_curso);

    // Selecionando qual a figura a ser exibida ao lado do nome
    $fig_portfolio = "<img alt=\"\" src=\"../imgs/icPerfil.gif\" border=\"0\" />";
    
    echo("          <a href=\"#\" onclick=\"return(OpenWindowPerfil(".$cod_usuario_portfolio."));\" >".$fig_portfolio." ".$nome."</a>".$complemento." - ");
    echo("          <a href=\"#\" onMouseDown=\"MostraLayer(cod_topicos,0);return(false);\"><img  alt=\"\" src=\"../imgs/estrutura.gif\" border=\"0\" /></a>");
  }

  echo("          ".$path);

  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <!-- Botoes de Acao -->\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");

   //174 - Meus portfolios 
  echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=myp\">".RetornaFraseDaLista($lista_frases,174)."</a></li>\n");    
  // 74 - Portfolios Individuais
  echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=ind\">".RetornaFraseDaLista($lista_frases,74)."</a></li>\n"); 
  // 75 - Portfolios de Grupos
  if ($ferramenta_grupos_s) {
  echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=grp\">".RetornaFraseDaLista($lista_frases,75)."</a></li>\n"); 
    // 177 - Portfolios encerrados
  echo("                  <li><a href= \"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=enc\">".RetornaFraseDaLista($lista_frases,177)."</a></li>\n"); 
  }
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs03\">\n");

  $cod_topico_raiz_usuario=RetornaPastaRaizUsuario($sock,$cod_usuario,"");
  $ultimo_acesso=PenultimoAcesso($sock,$cod_usuario,"");
  $lista_comentario=RetornaComentariosDoItem($sock, $cod_item);

  /* 23 (ger) - Voltar */
  echo("                  <li><a href=\"ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$cod_item."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  /* 70 - Ver Outros Itens */
  echo("                  <li><a href=\"portfolio.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">".RetornaFraseDaLista($lista_frases,70)."</a></li>\n");
  /* 3 (ger) - Comentar */
  if ((count($lista_comentario)>0)&&($lista_comentario!=""))
    echo("                  <li><span id=\"btnComentar\" onclick=\"EnviarComent();\">".RetornaFraseDaLista($lista_frases_geral,3)."</span></li>\n");
  else
    echo("                  <li><span id=\"btnComentar\">".RetornaFraseDaLista($lista_frases_geral,3)."</span></li>\n");

  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr>\n");
  if ((count($lista_comentario)>0)&&($lista_comentario!=""))
    echo("                    <td colspan=\"3\" width=\"70%\" id=\"td_coment\" style=\"background-color:#DCDCDC;\" align=\"left\">\n");
  else
    echo("                    <td colspan=\"3\" width=\"70%\" id=\"td_coment\" align=\"left\">\n");

  echo("                      <form name=\"formFiles\" id=\"formFiles\" action=\"acoes.php\" method=\"post\" enctype=\"multipart/form-data\" onsubmit=\"return(submitForm());\">\n");
  echo("                        <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\" />\n");
  echo("                        <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("                        <input type=\"hidden\" name=\"cod_item\" value=\"".$cod_item."\" />\n");
  echo("                        <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"".$cod_topico_raiz."\" />\n");
  echo("                        <input type=\"hidden\" name=\"cod_usuario_portfolio\" value=\"".$cod_usuario_portfolio."\" />\n");
  echo("                        <input type=\"hidden\" name=\"cod_grupo_portfolio\" value=\"".$cod_grupo_portfolio."\" />\n");
  echo("                        <input type=\"hidden\" name=\"acao\" value=\"comentar\" />\n");

  if ((count($lista_comentario)>0)&&($lista_comentario!=""))
    echo("                        <div id=\"text_coment\" class=\"divHidden\">\n");
  else
    echo("                        <div id=\"text_coment\" class=\"divShow\">\n");    

  echo("                          <b>".RetornaFraseDaLista($lista_frases,105).":</b><br />\n");
  echo("                          <textarea name=\"comentario\" id=\"textArea_coment\" rows=\"8\" cols=\"70\" style=\"border: 2px solid #9bc;\"></textarea><br /><br />\n");
  echo("                          <img alt=\"\" src=\"../imgs/paperclip.gif\" border=\"0\" /><span id=\"anexar_arquivo\" onclick=\"addInputFile();\" class=\"link\">".RetornaFraseDaLista($lista_frases,57)."</span>\n");
  echo("                        </div>\n");

  if ((count($lista_comentario)>0)&&($lista_comentario!="")){
    echo("                        <div id=\"button_coment\" class=\"divHidden\">\n");
    echo("                          <br />\n");
    echo("                          <input type=\"submit\" id=\"OKComent\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" class=\"input\" />\n");
    echo("                          <input type=\"button\" id=\"cancComent\" onclick=\"CancelarComent();\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" class=\"input\" />\n");

    echo("                        </div>\n");
  }else{
    echo("                        <div id=\"button_coment\" class=\"divShow\">\n");
    echo("                          <br />\n");
    echo("                          <input class=\"input\" type=\"submit\" id=\"OKComent\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
    echo("                          <input class=\"input\" type=\"button\" id=\"cancComent\" onclick=\"window.location='ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$cod_item."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."';\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");

    echo("                        </div>\n");
  }

  echo("                      </form>\n");
  echo("                    </td>\n");
  echo("                    <td style=\"background-color:#DCDCDC; border:0;\">\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");

  if ((count($lista_comentario)>0)&&($lista_comentario!=""))
  {
  echo("                  <tr class=\"head\">\n");
  /* 105 - Comentários */
  echo("                    <td colspan=\"3\" width=\"70%\">".RetornaFraseDaLista($lista_frases,105)."</td>\n");
  /* 109 - Emissor */
  echo("                    <td colspan=\"1\">".RetornaFraseDaLista($lista_frases,109)."</td>\n");
  echo("                  </tr>\n");
    $i=1;
    foreach ($lista_comentario as $cod => $linha)
    {
    echo("                  <tr class=\"altColor".($i%2)."\">\n");
    $i++;

     $cod_autor = $linha['cod_comentarista'];
     $data_coment=UnixTime2DataHora($linha['data']);

      $bstt="";
      $bend="";
      if ($linha['data']>$ultimo_acesso)
      {
        $bstt="<b>";
        $bend="</b>";
      }

      
      $dir_item_temp=CriaLinkVisualizarComentar($sock, $cod_curso, $cod_usuario, $linha['cod_comentario'], $diretorio_arquivos, $diretorio_temp);


      //listagem dos arquivos
      $lista_arq=RetornaArquivosMaterialVer($cod_curso, $dir_item_temp['link']);
      echo("                    <td colspan=\"3\" align=\"left\">\n");
      echo("                      ".IndentarComentario(Enter2Br($linha['comentario']),"                      ")."<br /><br />\n");
      
      if (count($lista_arq)>0)
      {
        // Procuramos na lista de arquivos se existe algum visivel
        $ha_visiveis = false;

        reset ($lista_arq);
        while (( list($cod, $linha) = each($lista_arq) ) && !$ha_visiveis)
        {
          $ha_visiveis = !($linha['Status']);
        }

        if ($ha_visiveis)
        {
           /* 71 - Arquivos */
          echo("                      <b>".RetornaFraseDaLista($lista_frases,71).": </b> ");
          $dir_atual="";
          $c = 0;

          foreach($lista_arq as $cod_arq => $linha_arq)
          {
            if (!$linha_arq['Status'])
            {
              //Vamos exibir todos os arquivos em um mesmo nivel, como se nao houvesse pastas
              $linha_arq['Arquivo'] = mb_convert_encoding($linha_arq['Arquivo'], "ISO-8859-1", "UTF-8");
              if ($linha_arq['Arquivo'] != "")              
              {
                $caminho_arquivo = $dir_item_temp['link'].ConverteUrl2Html($linha['Diretorio']."/".$linha_arq['Arquivo']);
                $tag_abre  = "<a href=".$caminho_arquivo." onclick=\"WindowOpenVer('".$caminho_arquivo."');return(false);\">";
                $tag_fecha = "</a>";
                echo($tag_abre.$imagem.$linha_arq['Arquivo'].$tag_fecha);
              }
              if ($cod_arq<count($lista_arq)-1)
                echo(" | ");
              else
                echo("\n");
            }
          }
        }
      }
      //fim da listagem dos arquivos

      echo("                    </td>\n");
      echo("                    <td>\n");
      echo("                      <a href=# onclick=\"OpenWindowPerfil(".$cod_autor.");return(false);\">".NomeUsuario($sock,$cod_autor, $cod_curso)."</a><br />".RetornaFraseDaLista($lista_frases,108)." <br />".$data_coment."\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
   }
  }
  echo("                </table>\n");//TabInterna
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");//TabExterna
  echo("        </td>\n");
  echo("      </tr>\n");


  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
