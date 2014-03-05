<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/portfolio/ver_lixeira.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist?cia
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

    Nied - Ncleo de Inform?ica Aplicada ?Educa?o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit?ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/portfolio/ver_lixeira.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("portfolio.inc");
  include("avaliacoes_portfolio.inc");

   require_once("../xajax_0.5/xajax_core/xajax.inc.php");
  
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"ExcluirItensDinamic");
  $objAjax->register(XAJAX_FUNCTION,"RecuperarItensDinamic");
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta =15;
  $cod_ferramenta_ajuda = 15;
  $cod_pagina_ajuda = 6;
  include("../topo_tela.php");
   
   $dir_item_temp=CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

  $eformador=EFormador($sock,$cod_curso,$cod_usuario);

  $status_portfolio = RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo_portfolio);

  $dono_portfolio    = $status_portfolio ['dono_portfolio'];
  $portfolio_apagado = $status_portfolio ['portfolio_apagado'];
  $portfolio_grupo   = $status_portfolio ['portfolio_grupo'];

  if (!$dono_portfolio)
  {
     /* 1 - Portfólio */
    $cabecalho = "<br><br><h5>".RetornaFraseDaLista($lista_frases, 1);
    /* 50- Área restrita ao(s) dono(s) do portfólio */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 50)."</h5>";
    echo($cabecalho);
    Desconectar($sock);
    exit();
  }


  /* 1 - Portf?io */

  echo("    <script type=\"text/javascript\">\n");

  echo("      function OpenWindowPerfil(id)\n");
  echo("      {\n");
  echo("         window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+id,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n");

 

  echo("    function Excluir(){\n");
      /* 120 - Tem certeza de que deseja excluir este item ? */
      /* 100 - (o item será excluído definitivamente) */
  echo("      if (confirm('".RetornaFraseDaLista($lista_frases,120)."\\n".RetornaFraseDaLista($lista_frases,100)."')){\n");
  echo("        xajax_ExcluirItensDinamic('".$cod_curso."', '".$cod_usuario."', '".$cod_item."');\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function Recuperar(){\n");
  /* 101 - Você tem certeza de que deseja recuperar este item? */
  /* 102 - (o item será movida para a pasta Raiz e estará como não compartilhado) */
  echo("      if (confirm('".RetornaFraseDaLista($lista_frases,101)."\\n".RetornaFraseDaLista($lista_frases,102)."')){\n");
  echo("        xajax_RecuperarItensDinamic('".$cod_curso."', '".$cod_usuario."','".$cod_grupo_portfolio."', '".$cod_item."');\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function Recarregar(){\n");
  echo("      window.location='portfolio_lixeira.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&cod_grupo_portfolio=".$cod_grupo_portfolio."&cod_usuario_portfolio=".$cod_usuario_portfolio."';\n");
  echo("    }\n\n");

  echo("      function WindowOpenVer(id)\n");
  echo("      {\n");
  echo("         window.open('".$dir_item_temp['link']."'+id+'?".time()."','Portfolio','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
  echo("      }\n\n");

  echo("      function Iniciar(){\n");
  echo("        startList();\n");
  echo("      }\n");

  echo("    </script>\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* Página Principal */

  if ($ferramenta_avaliacao)
  {
    if ($ferramenta_grupos_s && $cod_grupo_portfolio != '')
    {
      // 3 - Portfolios de grupos
      $cod_frase  =  3;
      $cod_pagina = 26;
    }
    else
    {
      // 2 - Portfolios individual
      $cod_frase  =  2;
      $cod_pagina = 22;
    }
  }
  else
  {
    if ($ferramenta_grupos_s && $cod_grupo_portfolio != '')
    {
      // 3 - Portfolios de grupos
      $cod_frase = 3;
      $cod_pagina=15;
    }
    else
    {
      // 2 - Portfolios individual
      $cod_frase = 2;
      $cod_pagina=9;
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
  

 
  if ($portfolio_grupo)
  {
    $nome=NomeGrupo($sock,$cod_grupo_portfolio);

    //Figura de Grupo
    $fig_portfolio = "<img alt=\"\" src=\"../imgs/icGrupo.gif\" border=\"0\" />";


    /* 84 - Grupo Excluído */
    if ($grupo_apagado && $eformador) $complemento=" <span>(".RetornaFraseDaLista($lista_frases,84).")</span>\n";

    echo("          <a class=\"text\" href=\"#\" onclick=\"return(AbreJanelaComponentes(".$cod_grupo_portfolio."));\">".$fig_portfolio." ".$nome."</a>".$complemento." - ");
    echo("          <a href=\"#\" onmousedown=\"js_cod_item='".$cod_item."'; MostraLayer(cod_topicos,0);return(false);\"><img alt=\"\" src=\"../imgs/estruturag.gif\" border=\"0\" /></a>");
  }
  else
  {
    $nome=NomeUsuario($sock,$cod_usuario_portfolio, $cod_curso);

    // Selecionando qual a figura a ser exibida ao lado do nome
    $fig_portfolio = "<img alt=\"\" src=\"../imgs/icPerfil.gif\" border=\"0\" />";


    echo("          <a class=\"text\" href=\"#\" onclick=\"return(OpenWindowPerfil(".$cod_usuario_portfolio."));\" >".$fig_portfolio." ".$nome."</a>".$complemento." - ");
    echo("<a href=\"#\" onmousedown=\"js_cod_item='".$cod_item."'; MostraLayer(cod_topicos,0);return(false);\"><img alt=\"\" src=\"../imgs/estrutura.gif\" border=\"0\" /></a>");
  }


  // 7 - Lixeira
  echo("<a href=\"portfolio_lixeira.php?&amp;cod_curso=".$cod_curso."&amp;cod_topico=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\" >".RetornaFraseDaLista($lista_frases,7)."</a>\n");
 
  $EhAvaliacao=RetornaAssociacaoItemAvaliacao($sock,$cod_item);

  echo("          <span id=\"associadoItem\">\n");
  if (count($EhAvaliacao)>0)
  {
    $dados=RetornaDadosAvaliacao($sock,$EhAvaliacao['cod_avaliacao']);
    $atividade=RetornaTituloAtividade($sock,$dados['cod_atividade']);
    /* 149 - Item associado a atividade: */
    echo("            <br><br>".RetornaFraseDaLista($lista_frases,149)." <a class=\"text\" href=\"#\" onclick=\"window.open('../avaliacoes/ver_popup.php?&amp;cod_curso=".$cod_curso."&amp;cod_avaliacao=".$EhAvaliacao['cod_avaliacao']."','VerAvaliacao','width=450,height=450,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');EscondeLayers();return(false);\">".$atividade."</a><br>");

    $cod_avaliacao=$EhAvaliacao['cod_avaliacao'];

  }
  echo("          </span>\n");

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
    echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=enc\">".RetornaFraseDaLista($lista_frases,177)."</a></li>\n"); 
  }

  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs03\">\n");

  $cod_topico_raiz_usuario=RetornaPastaRaizUsuario($sock,$cod_usuario,"");

  unset($array_params);
  $array_params['cod_topico_raiz']       = $cod_topico_raiz;
  $array_params['cod_item']              = $cod_item;
  $array_params['cod_usuario_portfolio'] = $cod_usuario_portfolio;
  $array_params['cod_grupo_portfolio']   = $cod_grupo_portfolio;

  /* 70 - Ver Outros Itens */
  echo("                  <li><a href=\"portfolio_lixeira.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">".RetornaFraseDaLista($lista_frases,70)."</a></li>\n");

  $figura = "arquivo_";
  $figura.= ( $portfolio_grupo ? "g_" : "i_" );
  if ($portfolio_apagado)
  {
    $figura .= "x.gif";
  }
  else
  {
    if ($dono_portfolio)
    {
      $figura .= "p.gif";
    }
    else
    {
      $figura .= "n.gif";
    }
  }

  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 41 - Título */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,41)."</td>\n");

  // 70 (ger) - Opções
  echo("                    <td width=\"14%\" align=\"center\">".RetornaFraseDaLista($lista_frases_geral,70)."</td>\n");


  /* 119 - Compartilhar */
  echo("                    <td width=\"10%\" align=\"center\">".RetornaFraseDaLista($lista_frases,119)."</td>\n");

  // se a ferramenta Avaliacoes estiver ativada, a tabela com os itens e pastas do portfolio tem 6 colunas, senao sao 5
  if ($ferramenta_avaliacao)
  {
    /* 139 - Avaliação */
    echo("                    <td width=\"8%\" align=\"center\">".RetornaFraseDaLista($lista_frases,139)."</td>\n");
  }

  echo("                  </tr>\n");


  $linha_item=RetornaDadosDoItem($sock, $cod_item);

  $titulo=$linha_item['titulo'];

  $texto="<span id=\"text_".$linha_item['cod_item']."\">".AjustaParagrafo($linha_item['texto'])."</span>";


  // se a ferramenta Avaliacoes estiver ativa, descobrimos quais avaliacoes estao presas a cada item
  if ($ferramenta_avaliacao) $lista = RetornaAssociacaoItemAvaliacao($sock,$linha_item['cod_item']);
  // senao, passamos uma variavel fake para enganar o codigo abaixo
  else $lista = NULL;


  $titulo="<span id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";

  $editar="<span onclick=\"AlteraTexto(".$linha_item['cod_item'].");\">Editar</span>";



  echo("                  <tr id='tr_".$linha_item['cod_item']."'>\n");
  echo("                    <td class=\"itens\"><img alt=\"\" src=\"../imgs/".$figura."\" border=\"0\" /> ".$titulo."</td>\n");

  echo("                    <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
  echo("                      <ul>\n");
  /* 48 - Recuperar (gen) */
  echo("                        <li><span onclick=\"Recuperar();\">".RetornaFraseDaLista ($lista_frases_geral, 48)."</span></li>\n");
  /* 12 - Excluir (gen) */
  echo("                        <li><span onclick=\"Excluir();\">".RetornaFraseDaLista ($lista_frases_geral, 12)."</span></li>\n");
  echo("                      </ul>\n");
  echo("                    </td>\n");


  echo("                    <td align=\"center\">".$compartilhamento."</td>\n");

  $Sim = "<span id=\"estadoAvaliacao\">".RetornaFraseDaLista($lista_frases_geral, 35)."</span>";

  if ($ferramenta_avaliacao)
  {
    echo("                    <td align=\"center\">");
    if (is_array($lista))
    {
      $foiavaliado=ItemFoiAvaliado($sock,$lista['cod_avaliacao'],$linha_item['cod_item']);
      if ($foiavaliado){
        if ($eformador){
          echo($Sim."</span><span class=\"avaliado\"> (a)</span>\n");
        }
        //else = não é formador
        else{
          $compartilhado=NotaCompartilhadaAluno($sock,$linha_item['cod_item'],$lista['cod_avaliacao'],$cod_grupo_portfolio,$cod_usuario);
          if ($compartilhado){
            echo($Sim."</span><span class=\"avaliado\"> (a)</span>\n");
          }
          //else = não é compartilhado
          else{
            echo($Sim);
          }
        }
       } 
       else{
         echo($Sim);
       }
      }
    //else = não tem avaliação
    else{
      // G 36 - Não
      echo("<span id=\"estadoAvaliacao\">".RetornaFraseDaLista($lista_frases_geral, 36)."</span>\n");
    }
    echo("                    </td>");
  }
  echo("                  </tr>");

  // "<P>&nbsp;</P>" = texto em branco
  // "<br>" = texto em branco
  if (($linha_item['texto']!="")&&($linha_item['texto']!="<P>&nbsp;</P>")&&($linha_item['texto']!="<br>"))
  {
    echo("                  <tr class=\"head\">\n");
    /* 42 - Texto  */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,42)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td class=\"itens\" colspan=\"4\">\n");
    echo("                      ".$texto."\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  $lista_arq=RetornaArquivosMaterialVer($cod_curso, $dir_item_temp['diretorio']);
  $num_arq_vis = RetornaNumArquivosVisiveis($lista_arq);

  if (count($lista_arq)>0){
    echo("                  <tr class=\"head\">\n");
    /* 71 - Arquivos */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,71)."</td>\n");
    echo("                  </tr>\n");

    $conta_arq=0;
    echo("                  <tr>\n");
    echo("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");
    // Procuramos na lista de arquivos se existe algum visivel
    $ha_visiveis = false;

    while (( list($cod, $linha) = each($lista_arq) ) && !$ha_visiveis)
    {
      if ($linha[Arquivo] != "")
        $ha_visiveis = !($linha['Status']);
    }

    if (($ha_visiveis) || ($dono_portfolio))
    {
      $nivel_anterior=0;
      $nivel=-1;

      foreach($lista_arq as $cod => $linha)
      {
        if (!($linha['Arquivo']=="" && $linha['Diretorio']==""))
          if ((!$linha['Status'])||(($linha['Status'])&&($dono_portfolio)))
          {
            $nivel_anterior=$nivel;
            $espacos="";
            $espacos2="";
            $temp=explode("/",$linha['Diretorio']);
            $nivel=count($temp)-1;
            for ($c=0;$c<=$nivel;$c++){
              $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
              $espacos2.="  ";
            }

            $caminho_arquivo = $dir_item_temp['link'].ConverteUrl2Html($linha['Diretorio']."/".$linha['Arquivo']);

            if ($linha[Arquivo] != "")
            {

              if ($linha['Diretorio']!=""){
                $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                $espacos2.="  ";
              }


              if ($linha['Status']) $arqOculto="arqOculto='sim'";
              else $arqOculto="arqOculto='nao'";

              if (eregi(".zip$",$linha['Arquivo']))
              {
                // arquivo zip
                $imagem    = "<img alt=\"\" src=\"../imgs/arqzip.gif\" border=\"0\" />";
                $tag_abre  = "<span class=\"link\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".$caminho_arquivo."');\" tipoArq=\"zip\" nomeArq=\"".htmlentities($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqOculto.">";
              }
              else
              {
                // arquivo comum
                $imagem    = "<img alt=\"\" src=\"../imgs/arqp.gif\" border=\"0\" />";
                $tag_abre  = "<span class=\"link\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".$caminho_arquivo."');\" tipoArq=\"comum\" nomeArq=\"".htmlentities($caminho_arquivo)."\" ".$arqOculto.">";
              }

              $tag_fecha = "</span>";

              echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");

              if ($dono_portfolio){
                echo("                          ".$espacos2."<input class=\"input\" type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\">\n");
              }

              echo("                          ".$espacos2.$espacos.$imagem.$tag_abre.$linha['Arquivo'].$tag_fecha." - (".round(($linha[Tamanho]/1024),2)."Kb)");

              echo("<span id=\"local_oculto_".$conta_arq."\">");
              if ($linha['Status']) 
                // 118 - Oculto
                  echo("<span id=\"arq_oculto_".$conta_arq."\"> - <span style=\"color:red;\">".RetornaFraseDaLista($lista_frases,118)."</span></span>");
              echo("</span>\n");
              echo("                          ".$espacos2."<br>\n");
              echo("                        ".$espacos2."</span>\n");
            }
            else{
              if ($nivel_anterior>=$nivel){
                $i=$nivel_anterior-$nivel;
                $j=$i;
                $espacos3="";
                do{
                  $espacos3.="  ";
                  $j--;
                }while($j>=0);
                do{
                  echo("                      ".$espacos3."</span>\n");
                  $i--;
                }while($i>=0);
              }
              // pasta
              $imagem    = "<img alt=\"\" src=\"../imgs/pasta.gif\" border=\"0\" />";
              echo("                      ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");
              echo("                        ".$espacos2."<span class=\"link\" id=\"nomeArq_".$conta_arq."\" tipoArq=\"pasta\" nomeArq=\"".htmlentities($caminho_arquivo)."\"></span>\n"); 
              if ($dono_portfolio){
                echo("                        ".$espacos2."<input class=\"input\" type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\">\n");
              }
              echo("                        ".$espacos2.$espacos.$imagem.$temp[$nivel]."\n");
              echo("                        ".$espacos2."<br>\n");
            }

          }
        $conta_arq++;
      }
      do{
        $j=$nivel;
        $espacos3="";
        do{
          $espacos3.="  ";
          $j--;
        }while($j>=0);
        echo("                      ".$espacos3."</span>\n");
        $nivel--;
      }while($nivel>=0);

      echo("                      <script type=\"text/javascript\">js_conta_arq=".$conta_arq.";</script>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
    }

    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  $lista_url=RetornaEnderecosMaterial($sock, $cod_item);

   if (is_array($lista_url)){

    echo("                  <tr class=\"head\">\n");
      /* 44 - Endereços */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,44)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td class=\"itens\" colspan=\"4\" id=\"listaEnderecos\">\n");

    if (count($lista_url)>0)
    {
      foreach ($lista_url as $cod => $linha)
      {
  
        $linha['endereco'] = RetornaURLValida($linha['endereco']);

        echo("                      <span id='end_".$linha['cod_endereco']."'>\n");

        if ($linha['nome']!="")
        {
          echo("                      <span class=\"link\" onclick=\"WindowOpenVerURL('".ConverteSpace2Mais($linha['endereco'])."');\">".$linha['nome']."</span>&nbsp;&nbsp;(".$linha['endereco'].")");
        }
        else
        {
          echo("                      <span class=\"link\" onclick=\"WindowOpenVerURL('".ConverteSpace2Mais($linha['endereco'])."');\">".$linha['endereco']."</span>");
        }

        if($dono_portfolio){
          /* (gen) 1 - Apagar */
          echo(" - <span class=\"link\" onClick=\"ApagarEndereco('".$cod_curso."', '".$linha['cod_endereco']."');\">".RetornaFraseDaLista ($lista_frases_geral, 1)."</span>\n");
        }
        echo("                        <br />\n");
        echo("                      </span>\n");

      }
    }

    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  echo("                </table>\n"); 
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n"); 
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>
