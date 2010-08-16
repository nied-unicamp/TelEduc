<?php 
/* 
<!--  
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perfil/editar_orientacao.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�cia
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

    Nied - Ncleo de Inform�ica Aplicada �Educa�o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/
/*==========================================================
  ARQUIVO : cursos/aplic/perfil/editar_orientacao.php
  ========================================================== */
    
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perfil.inc");

  $cod_ferramenta=13;
  
  $cod_ferramenta_ajuda = $cod_ferramenta;
 
  $cod_pagina_ajuda=2;

  include("../topo_tela.php");

  $eformador   = EFormador ($sock, $cod_curso, $cod_usuario);

  echo("    <script type=\"text/javascript\">\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

   echo("    </script>\n");
  
  include("../menu_principal.php");
  
  
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
 
  if (!$eformador)
    {

    /* 1 - Perfil */
    $cabecalho = "<h4>".RetornaFraseDaLista($lista_frases, 1);
    /* 130 - �ea restrita a formadores */
    $cabecalho .= "  <b> - ".RetornaFraseDaLista($lista_frases, 130)."</h4>";
    echo("          ".$cabecalho);
    echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");
    
    echo("        </td>\n");
    echo("      </tr>\n");
    include("../tela2.php");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();    
  }
  
//   Global $cod_lingua_s;
  if (!isset($cod_lingua))
      $cod_lingua=$_SESSION['cod_lingua_s'];

   /* 1 - Perfil */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)."</h4>\n");
  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <div id=\"mudarFonte\">\n");
  echo("	    <a href=\"#\" onclick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	    <a href=\"#\" onclick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	    <a href=\"#\" onclick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("          </div>\n");
  
  echo("          <form action=\"editar_orientacao2.php?cod_curso=".$cod_curso."\" method=\"post\">\n");
    
  echo("            <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("              <tr>\n");

  echo("                <!-- Botoes de Acao -->\n");
  echo("                <td valign=\"top\">\n");
  
  
  
  echo("                    <ul class=\"btAuxTabs\">\n");

  /* G 2 - Cancelar */ 
  echo("                      <li><a href=\"perfil.php?cod_curso=".$cod_curso."\">".RetornaFraseDaLista($lista_frases_geral ,2)."</a></li>\n");
  
  echo("                    </ul>\n");
  echo("                  </td>\n");
  echo("                </tr>\n");
  echo("                <tr>\n");
  echo("                  <td>\n");
  echo("                    <ul class=\"btAuxTabs03\">");
  
  echo("                      <li><a href=\"editar_orientacao.php?cod_curso=".$cod_curso."&amp;cod_lingua=1\">Portugu&ecirc;s</a></li> \n");

  echo("                      <li><a href=\"editar_orientacao.php?cod_curso=".$cod_curso."&amp;cod_lingua=2\">Espa&ntilde;ol</a></li> \n");

  echo("                      <li><a href=\"editar_orientacao.php?cod_curso=".$cod_curso."&amp;cod_lingua=3\">English</a></li> \n");

  echo("                      <li><a href=\"editar_orientacao.php?cod_curso=".$cod_curso."&amp;cod_lingua=4\">Portugu&ecirc;s PT</a></li>\n");
  
  echo("                    </ul>\n");
  echo("                    <input type=\"hidden\" value=\"".$cod_lingua."\" name=\"cod_lingua\" />\n");
  echo("                </td>\n");
  echo("              </tr>\n");
  echo("              <tr>\n");
  echo("                <td>\n");
  echo("                  <br />");
  echo("                  <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaInterna\" class=\"tabInterna\" >\n");
  echo("                    <tr>\n");
  echo("                      <td align=\"left\">\n");  
 
  /* 47 - O campo abaixo �uma orienta�o sobre como cada participante 
          do curso deve preencher o perfil pessoal. */
  /* 48 - Essa orienta�o aparecer�sempre que o participante for preencher ou alterar seu 
          perfil. */
  /* 49 - Utilize-o para direcionar o participante a preencher dados relevantes ao curso, 
          bem como os dados pessoais que desejar compartilhar com os demais participantes*/
  echo("                          ".RetornaFraseDaLista($lista_frases,47));
  echo(". ");
  echo(RetornaFraseDaLista($lista_frases,48));
  echo(". ");
  echo(RetornaFraseDaLista($lista_frases,49)."\n");
  echo("                          <br /><br />\n");  

  if(ExisteOrientacao($sock, $cod_lingua))
    {$orientacao_perfil=RetornaOrientacaoPerfil($sock, $cod_lingua);}
  else
    $orientacao_perfil="";


  echo("                          <textarea name=\"nova_orientacao\" cols=\"60\" rows=\"8\" class=\"text\" id=\"nova_orientacao\">".$orientacao_perfil."</textarea>\n");
  echo("                          ".$texto."\n");
  echo("                          <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("                          <br />\n");
  
 
  
/* 11 - Enviar */
  echo("                          <input class=\"input\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases_geral,11)."\" />\n");
 
  echo("                      </td>\n");
  echo("                    </tr>\n");
  echo("                  </table>\n");
  
  
  echo("                </td>\n");
 
  echo("              </tr>\n");
  
 
  echo("            </table>\n");
  
  echo("          </form>\n");
  
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");


  Desconectar($sock);

?>
