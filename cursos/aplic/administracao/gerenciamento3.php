<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/gerenciamento3.php

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
  ARQUIVO : cursos/aplic/administracao/gerenciamento3.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta=0;
  $cod_ferramenta_ajuda = $cod_ferramenta;

  include("../topo_tela.php");

  if (!isset($ordem))
  {
    $ordem="nome";
  }

  // Pï¿½gina Principal
  /* 1 - Administraï¿½ï¿½o */
  $cabecalho = "          <h4>".RetornaFraseDaLista ($lista_frases, 1);
  
  switch($tipo_usuario) {
    case 'a':
      /* 283 - Gerenciamento de Alunos desligados */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 283);
      $cod_pagina       = 9;
      $cod_pagina_ajuda = 9;
      break;
    case 'A':
      /* 102 - Gerenciamento de Alunos */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 102);
      $cod_pagina       = 9;
      $cod_pagina_ajuda = 9;
      break;
    case 'f':
      /* 258 - Gerenciamento de Formadores desligados */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 258);
      $cod_pagina       = 10;
      $cod_pagina_ajuda = 10;
      break;
    case 'F':
      /* 103 - Gerenciamento de Formadores */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 103);
      $cod_pagina       = 10;
      $cod_pagina_ajuda = 10;
      break;
    case 'z':
      /* 319 - Gerenciamento de Colaboradores desligados */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 319);
      $cod_pagina       = 13;
      $cod_pagina_ajuda = 13;
      break;
    case 'Z':
      /* 165 - Gerenciamento de Colaboradores  */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 165);
      $cod_pagina       = 13;
      $cod_pagina_ajuda = 13;
      break;
    case 'v':
      /* 321 - Gerenciamento de Visitantes desligados */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 321);
      $cod_pagina       = 16;
      $cod_pagina_ajuda = 16;
      break;
    case 'V':
      /* 179 - Gerenciamento de Visitantes */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 179);
      $cod_pagina       = 16;
      $cod_pagina_ajuda = 16;
      break;
    case 'i':
    case 'r':
      /* 74 - Gerenciamento de Inscriï¿½ï¿½es */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 74);
      $cod_pagina       = 8;
      $cod_pagina_ajuda = 8;
      break;
  }

  echo("    <script type=\"text/javascript\">\n\n");
  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");
  echo("    </script>");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
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
  
  echo($cabecalho."</h4>");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/
  /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  if (count($cod_usu) == 0) {
    echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
    echo("            <tr>\n");
    echo("              <td valign=\"top\">\n");
    /* 1 - AdministraÃ§Ã£o */
    echo("              <ul class=\"btAuxTabs\">\n");
    echo("                <li><a href=\"".$origem."?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=".$tipo_usuario."&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,1)."</a></li>\n");
    echo("              </ul>\n");
    echo("            </tr>");
    echo("            <tr>");
    echo("              <td>\n");
    echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("                  <tr>\n");
    echo("                    <td>\n");
    /* 114 - Nenhuma pessoa selecionada. */
    echo("                      ".RetornaFraseDaLista($lista_frases,114)."\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                </table>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
  }
  else {
  
    if ($action_ger == "aceitar" || $action_ger == "rejeitar" || $action_ger == "desligar_usuario" || $action_ger == "religar_usuario")
    {
      $mensagem = ConverteBarraAspas2Aspas($mensagem);
  
      if ($action_ger == "aceitar")
      {
  
        /* 90 - Aceitar Inscriï¿½ï¿½es */
        $titulo=RetornaFraseDaLista($lista_frases,90);
  
        /* 86 - Inscriï¿½ï¿½es aceitas com sucesso. */
        $confirmacao=RetornaFraseDaLista($lista_frases,86);
  
        /* 87 - Para cada inscriï¿½ï¿½o aceita estï¿½ sendo enviado automaticamente um e-mail que lhe garantirï¿½ acesso ao curso. Este acesso somente serï¿½ liberado para os alunos a partir da data de inï¿½cio do curso, que pode ser alterada na ferramenta <b>Cronograma</b> (em Gerenciamento de Curso). */
        $aviso=RetornaFraseDaLista($lista_frases,87);
  
        // vamos ativar os portfolios dos usuarios aceitos
        foreach($cod_usu as $cod => $cod_usuario)
        {
          StatusPortfolio($sock,$cod_curso, $cod_usuario, "ativar_port");
        }
  
        $tipo_usuario = 'A';
  
      }
      else if ($action_ger == "rejeitar")
      {
  
        /* 91 - Rejeitar Inscriï¿½ï¿½es */
        $titulo=RetornaFraseDaLista($lista_frases,91);
  
        /* 93 - Inscriï¿½ï¿½es rejeitadas com sucesso. */
        $confirmacao=RetornaFraseDaLista($lista_frases,93);
  
        /* 94 - Para cada inscriï¿½ï¿½o rejeitada estï¿½ sendo enviado automaticamente um e-mail informando sua decisï¿½o de nï¿½o aceitï¿½-los em seu curso. */
        $aviso=RetornaFraseDaLista($lista_frases,94);
  
        $tipo_usuario = 'r';
  
      }
      else if ($action_ger == "desligar_usuario")
      {
  
        switch ($tipo_usuario) {
          case 'A':
            /* 288 - Desligar Aluno */
            $titulo = RetornaFraseDaLista($lista_frases,288);
  
            /* 294 - Aluno(s) desligado(s) com sucesso. */
            $confirmacao = RetornaFraseDaLista($lista_frases,294);
  
            /* 295 - Mensagem de cancelamento*/
            $aviso = RetornaFraseDaLista($lista_frases,295);
  
            $tipo_usuario = 'a';
            break;
  
          case 'F':
            /* 199 - Desligar Formador */
            $titulo = RetornaFraseDaLista($lista_frases,199);
  
            /* 240 - Formador(es) desligado(s) com sucesso. */
            $confirmacao = RetornaFraseDaLista($lista_frases,240);
  
            /* 241 - Mensagem de cancelamento */
            $aviso = RetornaFraseDaLista($lista_frases,241);
  
            $tipo_usuario = 'f';
            break;
  
          case 'Z':
            /* 328 - Desligar Colaborador */
            $titulo = RetornaFraseDaLista($lista_frases,328);
  
            /* 242 - Colaborador(es) desligado(s) com sucesso. */
            $confirmacao = RetornaFraseDaLista($lista_frases,242);
  
            /* 243 - Mensagem de cancelamento*/
            $aviso = RetornaFraseDaLista($lista_frases,243);
  
            $tipo_usuario = 'z';
          case 'V':
            /* 178 - Desligar Visitante */
            $titulo = RetornaFraseDaLista($lista_frases,178);
  
            /* 336 - Visitante(s) desligado(s) com sucesso. */
            $confirmacao = RetornaFraseDaLista($lista_frases,336);
  
            /* 244 - Mensagem de cancelamento*/
            $aviso = RetornaFraseDaLista($lista_frases,244);
  
            $tipo_usuario = 'v';
            break;
        }
  
      }
      else if ($action_ger == "religar_usuario")
      {
  
        switch($tipo_usuario) {
          case 'a':
  
            // 285 - Religar Aluno
            $titulo=RetornaFraseDaLista($lista_frases,285);
  
            /* 293 - Aluno(s) religado(s) com sucesso. */
            $confirmacao= RetornaFraseDaLista($lista_frases,293);
  
            /* 296 - Mensagem de confirmacao*/
            $aviso= RetornaFraseDaLista($lista_frases,296);
  
            $tipo_usuario = 'A';
            break;
  
          case 'f':
            /* 260 - Religar Formador */
            $titulo = RetornaFraseDaLista($lista_frases,260);
  
            /* 262 - Formador(es) religado(s) com sucesso. */
            $confirmacao = RetornaFraseDaLista($lista_frases,262);
  
            /* 281 - Mensagem de confirmacao*/
            $aviso = RetornaFraseDaLista($lista_frases,281);
  
            $tipo_usuario = 'F';
            break;
  
          case 'z':
            /* 329 - Religar Colaborador */
            $titulo = RetornaFraseDaLista($lista_frases,329);
  
            /* 337 - Colaboradores(s) religado(s) com sucesso. */
            $confirmacao = RetornaFraseDaLista($lista_frases,337);
  
            /* 281 - Mensagem de confirmacao*/
            $aviso = RetornaFraseDaLista($lista_frases,281);
  
            $tipo_usuario = 'Z';
            break;
  
          case 'v':
            /* 330 - Religar Visitante */
            $titulo = RetornaFraseDaLista($lista_frases,330);
  
            /* 281 - Mensagem de confirmacao*/
            $aviso = RetornaFraseDaLista($lista_frases,281);
  
            /* 315 - Visitantes(s) religado(s) com sucesso. */
            $confirmacao = RetornaFraseDaLista($lista_frases,315);
  
            $tipo_usuario = 'V';
            break;
        }
  
      }
  
      $sock = MudarTipoUsuarioComMensagem($sock,$cod_curso,$cod_usu,$tipo_usuario,$assunto,$mensagem);
  
      echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
      echo("            <tr>\n");
      echo("              <td valign=\"top\">\n");
      /* 23 - Voltar (gen) */
      echo("              <ul class=\"btAuxTabs\">\n");
      echo("                <li><a href=\"".$origem."?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=".$tipo_usuario."&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
      echo("              </ul>\n");
      echo("            </tr>");
      echo("            <tr>");
      echo("              <td>\n");
      echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
      echo("                  <tr class=\"head\">\n");
      echo("                    <td><b>".$titulo." - ".$confirmacao."</b></td>\n");
      echo("                  </tr>");
      echo("                  <tr>");
      echo("                    <td class=\"alLeft\" style=\"padding:25px 5px 25px 5px; text-indent:25px;\">\n");
      echo("                      ".$aviso."\n");
      echo("                    </td>\n");
      echo("                  </tr>");
      echo("                </table>\n");
      echo("              </td>\n");
      echo("            </tr>\n");
      echo("          </table>\n");
    }
      else
    {
      echo("<big>Ação inválida inesperada !</big><br>\n");
      var_dump ($action_ger);
      exit();
    }
  }

  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
