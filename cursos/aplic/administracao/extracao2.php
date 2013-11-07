<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/extracao2.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/administracao/extracao2.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta=0;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  //$cod_pagina_ajuda = 1;

  include("../topo_tela.php");
  
  $total_ferramentas = count($tela_lista_ferramentas);

  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
    include("../menu_principal.php");

    echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
    
    /* 1 - Administracao  28 - Area restrita ao formador. */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,28)."</h4>\n");

    /*Voltar*/
    /* 509 - Voltar */
    echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* 23 - Voltar (gen) */
    echo("          <form><input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" /></form>\n");

    echo("        </td>\n");
    echo("      </tr>\n");

    include("../tela2.php");

    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }

  /*Funcao JavaScript*/
  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar() {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
    
  // Pï¿½gina Principal
  /* 1 - Administraï¿½ï¿½o */
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1)."\n");
  /* 212 - Agendar Extraï¿½ï¿½o do Curso */
  $cabecalho .= "- ".RetornaFraseDaLista($lista_frases, 212)."</h4>";
  echo($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/			
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <form name=frmMarcar method=get action=\"administracao.php\">\n");
  echo("            <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr>\n");
  echo("                    <td>\n");

  Desconectar($sock);
  $sock2 = Conectar("");  
  
  $query_teste = "select * from Extracoes_agendadas where extraido=0;";
  $res_teste = Enviar($sock2, $query_teste);

  if (RetornaNumLinhas($res_teste) >= 10) {
    /* 227 - O limite de 10 extracoes diarias foi ultrapassado. Por favor, tente agendar a extracao do curso um outro dia. */
    echo(RetornaFraseDaLista($lista_frases, 227));
   
  } else {
     
    foreach($cod_usu as $cod => $cod_usuario)
    {
      $query_teste = "select * from Extracoes_agendadas where cod_usuario=$cod_usuario and cod_curso=$cod_curso and extraido=0;";
      $res_teste = Enviar($sock2, $query_teste);
    }
  
    if (RetornaNumLinhas($res_teste) == 0)
    {
      /* 144 - Operaçao concluida com sucesso! */
      echo("        ".RetornaFraseDaLista($lista_frases, 144)."\n");
      echo("                      <br><br>");
    
      Desconectar($sock2);
    
      $sock = Conectar($cod_curso);
      /* 217 - O usuario agendado para extracao do curso foi: */
      echo(RetornaFraseDaLista($lista_frases, 217)." ".NomeUsuario($sock, $cod_usuario)."\n");
      echo("                      <br /><br />");

      /* 218 - A extracao sera feita na madrugada do dia seguinte. Um e-mail sera enviado para o usuario quando o curso tiver sido extraido. */
      echo(RetornaFraseDaLista($lista_frases, 218)."\n");
      Desconectar($sock);

      $sock2 = Conectar("");

      $data = time();      
    
      $query = "insert into Extracoes_agendadas values($cod_curso, $cod_usuario, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, $data);";
      Enviar($sock2, $query);
   
      Desconectar($sock2);
    
      if (($total = count($ferramentas)) > 0)
      {
        echo("                      <br><br>");
      
        /* 219 - As ferramentas escolhidas para a extraï¿½ï¿½o foram: */
        echo("                      ".RetornaFraseDaLista($lista_frases, 219)."<br><br>");
        foreach($ferramentas as $ferr => $ferramenta)
        {
          $nome_ferr = RetornaFraseDaLista($lista_frases_menu, $tela_lista_ferramentas[$ferramenta]['cod_texto_nome']);
        
          $sock = Conectar($cod_curso);
        
          if (($ferramenta != 18) || EFormador($sock, $cod_curso, $cod_usuario)) 
	         echo("                      <li>$nome_ferr</li>");
        
          switch($ferramenta)
	       {
	         case 17: $nome_bd_ferr = "estrutura"; break;
	         case 16: $nome_bd_ferr = "dinamica"; break;
     	         case 1: $nome_bd_ferr = "agenda"; break;
	         case 22: $nome_bd_ferr = "avaliacoes"; break;
	         case 3: $nome_bd_ferr = "atividades"; break;
	         case 4: $nome_bd_ferr = "material"; break;
	         case 5: $nome_bd_ferr = "leituras"; break;
	         case 6: $nome_bd_ferr = "perguntas"; break;
	         case 23: $nome_bd_ferr = "exercicios"; break;
	         case 7: $nome_bd_ferr = "parada"; break;
	         case 8: $nome_bd_ferr = "mural"; break;
	         case 9: $nome_bd_ferr = "forum"; break;
	         case 10: $nome_bd_ferr = "batepapo"; break;
	         case 11: $nome_bd_ferr = "correio"; break;
                 case 12: $nome_bd_ferr = "grupos"; break;
	         case 13: $nome_bd_ferr = "perfil"; break;
	         case 14: $nome_bd_ferr = "diario"; break;
  	         case 15: $nome_bd_ferr = "portfolio"; break;
	         case 18: $nome_bd_ferr = "acessos"; break;
	         case 19: $nome_bd_ferr = "intermap"; break;
	       }
        
          if (($ferramenta != 18) || (EFormador($sock, $cod_curso, $cod_usuario)))
          {
            Desconectar($sock);
            $sock2 = Conectar("");
            $query = "update Extracoes_agendadas set $nome_bd_ferr = 1 where cod_usuario=$cod_usuario and cod_curso=$cod_curso;";
	         Enviar($sock2, $query);
            Desconectar($sock2);
          } else {
            Desconectar($sock);
          }
        }	
      }
    }
    else
    {
      /* 220 - Usuï¿½rio ja cadastrado para extracao */
      echo("                      ".RetornaFraseDaLista($lista_frases, 220));
    }
  }
  
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");
  echo("    </form>\n");  
  echo("  </body>\n");
  echo("</html>\n");

?>
