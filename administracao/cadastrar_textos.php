<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/cadastrar_textos.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�ncia
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

    Nied - N�cleo de Inform�tica Aplicada � Educa��o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : administracao/cadastra_textos.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  VerificaAutenticacaoAdministracao();

  require_once("../cursos/aplic/xajax_0.5/xajax_core/xajax.inc.php");

  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../cursos/aplic/xajax_0.5");
  $objAjax->configure('errorHandler', true);
  $objAjax->register(XAJAX_FUNCTION,"AlteraTextoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"InsereTextoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"ApagaTextoDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  include("../topo_tela_inicial.php");

  $lista_frases_adm=RetornaListaDeFrases($sock,-5);

  /* Inicio do JavaScript */
  echo("    <script language=\"javascript\" type=\"text/javascript\">\n");

  echo("      function Iniciar() {\n");
  echo("	startList();\n");
  echo("      }\n");

  echo("      function RespostaUsuario(acao,num,texto)\n");
  echo("      {\n");
  echo("	if(acao == 'I')\n");
  echo("        {\n");
  echo("          window.location = 'cadastrar_textos.php?cod_ferramenta=".$cod_ferramenta."&cod_lingua=".$cod_lingua."&opcao=".$opcao."';;");
  echo("        }\n");
  echo("	else if(acao == 'A')\n");
  echo("        {\n");
  /* 88 - Frase de c�digo */
  /* 89 - alterada para  */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,88)." '+num+'  ".RetornaFraseDaLista($lista_frases_adm,89)." '+texto);");
  echo("        }\n");
  echo("      }\n");

  echo("    </script>\n");
  
  $objAjax->printJavascript();

  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  Desconectar($sock);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 11 - Cadastro de L�nguas */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,11)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaInterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span style=\"href: #\" title=\"Voltar\" onClick=\"document.location='cadastro_linguas.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");

  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <form name=\"frmIncluir\" action='cadastrar_textos2.php' method=\"post\" onsubmit=\"return false;\">\n");
  echo("                  <input type=\"hidden\" name=\"cod_lin\" value=".$cod_lingua." />\n");
  echo("                  <input type=\"hidden\" name=\"cod_ferr\" value=".$cod_ferramenta." />\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  $sock=Conectar("");
  $lista_lingua=ListaLinguas($sock);
  Desconectar($sock);

  if ($cod_ferramenta=="Completar" && $cod_lingua==1)
  {
    /* 84 - Op��o inv�lida! */
    echo("                  <tr>\n");
    echo("                    <td>".RetornaFraseDaLista($lista_frases,84)."</td>\n");
    echo("                  </tr>\n");
    echo("                </table>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    include("../rodape_tela_inicial.php");
    echo("  </body>\n");
    echo("</html>\n");
    exit();
  }

  /* 82 - L�ngua: */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,82)."&nbsp;".$lista_lingua[$cod_lingua]."</td>\n");
  echo("                  </tr>\n");

  if ($cod_ferramenta!="Completar")
  {
    if ($cod_ferramenta<=0)
    {
      if ($cod_ferramenta==-1)
        $nome_fer=RetornaFraseDaLista($lista_frases,75);
      else if ($cod_ferramenta==-2)
        $nome_fer=RetornaFraseDaLista($lista_frases,76);
      else if ($cod_ferramenta==-3)
        $nome_fer=RetornaFraseDaLista($lista_frases,77);
      else if ($cod_ferramenta==-4)
        $nome_fer=RetornaFraseDaLista($lista_frases,78);
      else if ($cod_ferramenta==-5)
        $nome_fer=RetornaFraseDaLista($lista_frases,79);
      else if ($cod_ferramenta==0)
        $nome_fer=RetornaFraseDaLista($lista_frases,80);

      echo("                  <tr class=\"head01\">\n");
      echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,83)."&nbsp;".$nome_fer."</td>\n");
      echo("                  </tr>\n");
    }
    else
    {
      $lista_ferramentas=RetornaFerramentasOrdemMenu();
      /* 83 - Ferramenta: */
      echo("                  <tr class=\"head01\">\n");
      echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,83)."&nbsp;".$lista_ferramentas[$cod_ferramenta]."</td>\n");
      echo("                  </tr>\n");
    }
  }

  if ($cod_ferramenta!="Completar")
  {
    $lista_port=ListaFrasesDecrescente(1,$cod_ferramenta); 
    if ($cod_lingua!=1)
      $lista_estr=ListaFrasesDecrescente($cod_lingua,$cod_ferramenta);
  }
  else 
  {
    $lista_inco=ListaFrasesNaoPreenchidas($cod_lingua);
  }

  if ($cod_ferramenta=="Completar")
  {
    if (count($lista_inco)>0)
    {
      echo("                  <tr style=\"display:none;\">\n");
      echo("                    <td><input type=\"hidden\" name=\"acao\" value=LD /></td>\n");
      echo("                  </tr>\n");

      foreach($lista_inco as $cod_ferr => $linha)
      {
        foreach($linha as $cod_texto => $texto)
        {
          if ($opcao=="E")
          {
            echo("                  <tr>\n");
            echo("                    <td><b>".$cod_ferr." ".$cod_texto.":</b>&nbsp;".LimpaTags($texto)." </td>\n");
            echo("                    <td><input class=\"input\" type=\"text\" name=\"texto_".$cod_texto."\" size=\"45\" /></td>\n");
            /* 24 - Alterar */
            echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,24)."\" onClick=\"xajax_AlteraTextoDinamic(".$cod_texto.",".$cod_ferr.",".$cod_lingua.",document.frmIncluir.texto_".$cod_texto.".value);\" type=\"button\" /></td>\n");
            echo("                  </tr>\n");
          }
          else
          {
            echo("                  <tr>\n");
            echo("                    <td><b>".$cod_ferr." ".$cod_texto."</b>: ".LimpaTags($texto)." </td>\n");
            echo("                  </tr>\n");
          }
        }
      }
    }
  }
  else if ($cod_lingua==1)
  {
    if ($opcao=="E")
    {


      echo("                  <tr>\n");
      /* 85 - Nova: */
      echo("                    <td align=\"right\"><b>".LimpaTags(RetornaFraseDaLista($lista_frases,85))."</b></td>\n");
      
      echo("                    <td><input class=\"input\" type=\"text\" name=\"texto\" size=\"45\" /></td>\n");
      /* 49 - Incluir (ger) */
      echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,49)."\" onClick=\"xajax_InsereTextoDinamic(".$cod_ferramenta.",".$cod_lingua.",document.frmIncluir.texto.value);\" type=\"button\" />\n");

      echo("                    </td>\n");
      echo("                  </tr>\n");
      echo("                  <tr>\n");
      echo("                    <td colspan=\"3\">&nbsp;</td>\n");
      echo("                  </tr>\n");
    }

    if (count($lista_port)>0)
    {
      echo("                  <tr style=\"display:none;\" id=\"tr_ref\">\n");
      echo("                    <td><input type=\"hidden\" name=\"acao\" value=AD /></td>\n");
      echo("                  </tr>\n");

      foreach($lista_port[$cod_ferramenta] as $cod_texto => $texto)
      {
        if ($opcao=="E")
        {
          echo("                  <tr id=\"tr_".$cod_texto."\">\n");
          echo("                    <td align=\"left\"><b>".$cod_texto."</b>: ".LimpaTags($texto)."</td>\n");
          echo("                    <td><input class=\"input\" type=\"text\" name=\"texto_".$cod_texto."\" size=\"45\" value=\"".$texto."\" /></td>\n");
          echo("                    <td width=\"15%\">\n");
          /* 24 - Alterar */
          echo("                      <input class=\"input\" type=\"button\" onClick=\"xajax_AlteraTextoDinamic(".$cod_texto.",".$cod_ferramenta.",".$cod_lingua.",document.frmIncluir.texto_".$cod_texto.".value);\" value='".RetornaFraseDaLista($lista_frases_geral,24)."' />\n");
          /* 1 - Apagar */
          /* 531 - Tem certeza que deseja remover essa frase? */
          echo("                      <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,1)."\" onClick=\"if(confirm('".RetornaFraseDaLista($lista_frases,531)."')) { document.getElementById('tr_".$cod_texto."').style.display = 'none';xajax_ApagaTextoDinamic(".$cod_texto.",".$cod_ferramenta."); }\" type=\"button\" />\n");


          echo("                    </td>\n");
          echo("                  </tr>\n");
        }
        else
        {
          echo("                  <tr>\n");
          echo("                    <td align=\"left\"><b>".$cod_texto."</b>: ".LimpaTags($texto)."</td>\n");
          echo("                  </tr>\n");
        }
      }
    }
  }
  else
  {
    if (count($lista_port)>0)
    {
      echo("                  <tr style=\"display:none;\">\n");
      echo("                    <td><input type=\"hidden\" name=\"acao\" value=AD /></td>\n");
      echo("                  </tr>\n");

      foreach($lista_port[$cod_ferramenta] as $cod_texto => $texto)
      {
        if ($opcao=="E")
        {
          echo("                  <tr>\n");
          echo("                    <td align=left width=\"50%\"><b>".$cod_texto."</b>: ".LimpaTags($texto)."</td>\n");
          echo("                    <td><input class=\"input\" type=\"text\" size=\"45\" name=\"texto_".$cod_texto."\" value='".$lista_estr[$cod_ferramenta][$cod_texto]."' /></td>\n");
          /* 24 - Alterar */
          //echo("                    <td><input class=\"input\" type=\"button\" onClick=\"xajax_AlteraTextoDinamic(".$cod_texto.",".$cod_ferramenta.",".$cod_lingua.",document.frmIncluir.texto[".$cod_ferramenta."][".$cod_texto."].value);\" value='".RetornaFraseDaLista($lista_frases_geral,24)."' /></td>\n");
          echo("                    <td><input class=\"input\" type=\"button\" onClick=\"xajax_AlteraTextoDinamic(".$cod_texto.",".$cod_ferramenta.",".$cod_lingua.",document.frmIncluir.texto_".$cod_texto.".value);\" value='".RetornaFraseDaLista($lista_frases_geral,24)."' /></td>\n");
          echo("                  </tr>\n");
        }
        else
        {
          echo("                  <tr>\n");
          echo("                    <td align=\"left\"><b>".$cod_texto.": ".LimpaTags($texto)."</b>\n");
          echo("<br>".$lista_estr[$cod_ferramenta][$cod_texto]."</td>\n");
          echo("                  </tr>\n");
        }
      }
    }
  }

  echo("                </table>\n");
  echo("                </form>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>