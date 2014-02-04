<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/gerenciamento2.php

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
  ARQUIVO : cursos/aplic/administracao/gerenciamento2.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta = 0;
  $cod_ferramenta_ajuda = $cod_ferramenta;

  include("../topo_tela.php");
  
  $sock=Conectar("");
  $lista_esc=RetornaListaEscolaridade($sock);
  Desconectar($sock);

  $sock=Conectar($cod_curso);

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

  #Verifica se o email foi passado por post para atualizaÃ§Ã£o
  if(isset($_POST['c_email'])) {
    AtualizaEmailUsuario(Conectar(''),$cod_curso,$_POST['c_usuario'],$_POST['c_email']);
  }

  echo("    <script type=\"text/javascript\" src=\"../js-css/dhtmllib.js\"></script>\n");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor.js\"></script>");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor_biblioteca.js\"></script>");
  echo("    <script type=\"text/javascript\" language=\"javascript\" src=\"../bibliotecas/javacrypt.js\" defer></script>\n");

  if($action_ger == "dados")
  {
    echo("    <script type=\"text/javascript\">\n");
    echo("      function OpenWindowPerfil(funcao)\n");
    echo("      {\n");
    echo("                 window.open('../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&cod_aluno[]='+funcao,'PerfilDisplay','width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
    echo("        return(false);\n");
    echo("      }\n");

    echo("      function ImprimirRelatorio()\n");
    echo("      {\n");
    echo("        if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
    echo("        {\n");
    echo("          self.print();\n");
    echo("        }\n");
    echo("        else\n");
    echo("        {\n");

    /* 51 - Infelizmente nï¿½o foi possï¿½vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
    echo("          alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
    echo("        }\n");
    echo("      }\n");
    echo("    </script>\n");
  }

  /*Forms?*/
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
  
  echo($cabecalho."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/
  /* 509 - Voltar */
  echo("            <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

  echo("            <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("              <tr>\n");
  echo("                <td valign=\"top\">\n");
  /* 23 - Voltar (gen) */
  echo("                  <ul class=\"btAuxTabs\">\n");

  echo("                    <li><a href=\"".$origem."?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=".$tipo_usuario."&amp;opcao=".$opcao."&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  if (count($cod_usu) == 0)
  {
    echo("                </ul>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("            <tr>\n");
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
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr>\n");
    echo("        <td valign=\"bottom\" height=\"80\"><img src=\"../imgs/logoNied.gif\" alt=\"nied\" border=\"0\" style=\"margin-right:8px;\" /> <img src=\"../imgs/logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" border=\"0\" style=\"margin-right:6px;\" /> <img src=\"../imgs/logoUnicamp.gif\" alt=\"UNICAMP\" border=\"0\" /></td>\n");
    echo("        <td valign=\"bottom\" id=\"rodape\">2006  - TelEduc - Todos os direitos reservados. All rights reserved - NIED - UNICAMP</td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </body>\n");
    echo("</html>\n");
  
    Desconectar($sock);
    exit();
  }
  else
  {
    if ($action_ger == "dados")
    {
      /* 50 - Salvar em Arquivo (ger) */
      if ($SalvarEmArquivo!=1)
        echo("                    <li><a href=\"salvar_gerenciamento2.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;action_ger=".$action_ger."&amp;ordem=".$ordem."&amp;opcao=".$opcao."\">".RetornaFraseDaLista($lista_frases_geral,50)."</a></li>\n");
      /* 14 - Imprimir */
      echo("                    <li><span onclick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");
      echo("                  </ul>\n");
      echo("                </td>\n");
      echo("              </tr>\n");
      echo("              <tr>\n");
      echo("                <td>\n");
    }else{
      echo("                  </ul>\n");
    }
    echo("                </td>\n");
    echo("              </tr>\n");
    echo("              <tr>\n");
    echo("                <td>\n");

    // Ações que mandam emails de notificação aos usuários gerenciados.
    if ($action_ger == "aceitar" || $action_ger =="rejeitar" || $action_ger == "desligar_usuario" || $action_ger == "religar_usuario")
    {
      echo("                  <form action=\"gerenciamento3.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\" name=\"gerenc\" method=\"post\" onsubmit=\"return(updateRTE('mensagem'));\">\n");
      echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

      $dados_curso = DadosCursoParaEmail($sock, $cod_curso);

      if ($action_ger == "aceitar")
      {

        /* 90 - Aceitar Inscriï¿½ï¿½es */
        $titulo=RetornaFraseDaLista($lista_frases,90);

        /* 112 - Inscriï¿½ï¿½o aceita */
        $assunto =RetornaFraseDaLista($lista_frases,112);
        $assunto.=$dados_curso['nome_curso'];
        /* 99 - Sua inscriï¿½ï¿½o como aluno para o curso */
        /* 100 - foi aceita. */
        /* 65 - Visite a pï¿½gina do curso para obter informaï¿½ï¿½es sobre o seu inï¿½cio */
        /* 101 - Agradecemos sinceramente o seu interesse */
        /* 66 - Atenciosamente, Coordenaï¿½ï¿½o do curso */

        $mensagem ="<font size=\"2\">";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,99);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong> ";
        $mensagem.=RetornaFraseDaLista($lista_frases,100)."</p>";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,65)."</p>";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,101)."</p><br />";
        $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases,66);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
        $mensagem.="</font>";

      }
      else if ($action_ger == "rejeitar")
      {

        /* 91 - Rejeitar Inscriï¿½ï¿½es */
        $titulo=RetornaFraseDaLista($lista_frases,91);

        /* 111 - Inscriï¿½ï¿½o nï¿½o aceita */
        $assunto=RetornaFraseDaLista($lista_frases,111);

        /* 96 - Infelizmente sua inscriï¿½ï¿½o como aluno para o curso */
        /* 97 - nï¿½o foi aceita. */
        /* 98 - Agradecemos sinceramente o seu interesse e recomendamos a visita periï¿½dica do ambiente para a verificaï¿½ï¿½o de novos cursos agendados. */
        /* 66 - Atenciosamente, Coordenaï¿½ï¿½o do curso */
        $mensagem ="<font size=\"2\">";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,96);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong> ";
        $mensagem.=RetornaFraseDaLista($lista_frases,97)."</p>";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,98)."</p><br />";
        $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases,66);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
        $mensagem.="</font>";

      }
      else if($action_ger == "religar_usuario")
      {

        switch ($tipo_usuario) {
          case 'a':
            // 285 - Religar Aluno
            $titulo=RetornaFraseDaLista($lista_frases,285);
          
            // 286 - Religamento de Aluno
            $assunto=RetornaFraseDaLista($lista_frases, 286);
          
            // 287 -Voce foi religado como Aluno do curso
            // 265 - Seu login e senha nÃ£o foram alterados
            // 266 - Acesse o curso:
            // 66 - Atenciosamente, Coordenaï¿½ï¿½o do curso
            $mensagem ="<font size=\"2\">";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 287);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong></p>";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 265)."</p><br />";
            $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases, 66);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
            $mensagem.="</font>";
            break;
          case 'f':
            // 260 - Religar Formador
            $titulo=RetornaFraseDaLista($lista_frases,260);
            
            // 267 - Religamento de formador
            $assunto=RetornaFraseDaLista($lista_frases, 267);
            
            // 264 -Voce foi religado como Formador do curso
            // 265 - Seu login e senha nÃ£o foram alterados
            // 266 - Acesse o curso:
            // 66 - Atenciosamente, Coordenaï¿½ï¿½o do curso
            $mensagem ="<font size=\"2\">";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 264);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong></p>";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 265)."</p><br />";
            $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases, 66);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
            $mensagem.="</font>";
            break;
          case 'z':
            // 329 - Religar Colaborador
            $titulo=RetornaFraseDaLista($lista_frases,329);
            
            // 286 - Religamento de colaborador
            $assunto=RetornaFraseDaLista($lista_frases, 331);
            
            // 333 -Voce foi religado como Colaborador do curso
            // 265 - Seu login e senha nÃ£o foram alterados
            // 266 - Acesse o curso:
            // 66 - Atenciosamente, Coordenaï¿½ï¿½o do curso
            $mensagem ="<font size=\"2\">";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 333);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong></p>";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 265)."</p><br />";
            $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases, 66);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
            $mensagem.="</font>";
            break;
          case 'v':
            // 330 - Religar Visitante
            $titulo=RetornaFraseDaLista($lista_frases,330);
            
            // 286 - Religamento de visitante
            $assunto=RetornaFraseDaLista($lista_frases, 332);
            
            // 334 -Voce foi religado como Visitante do curso
            // 265 - Seu login e senha nÃ£o foram alterados
            // 266 - Acesse o curso:
            // 66 - Atenciosamente, Coordenaï¿½ï¿½o do curso
            $mensagem ="<font size=\"2\">";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 334);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong></p>";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 265)."</p><br />";
            $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases, 66);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
            $mensagem.="</font>";
            break;
        }

      }
      else if($action_ger == "desligar_usuario")
      {

        switch ($tipo_usuario) {
          case 'A':
            // 288 - Desligar Aluno
            $titulo=RetornaFraseDaLista($lista_frases,288);
            
            // 289 - Desligamento de Aluno
            $assunto=RetornaFraseDaLista($lista_frases, 289);
            
            // 290 - Infelizmente vocï¿½ foi desligado como Aluno do curso
            // 98 - Agradecemos sinceramente o seu interesse e recomendamos a visita periï¿½dica do ambiente para a verificaï¿½ï¿½o de novos cursos agendados.
            // 66 - Atenciosamente, Coordenaï¿½ï¿½o do curso
            $mensagem ="<font size=\"2\">";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 290);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong></p>";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 98)."</p><br />";
            $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases, 66);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
            $mensagem.="</font>";
            break;
          case 'F':
            // 199 - Desligar Formador
            $titulo=RetornaFraseDaLista($lista_frases,199);
            
            // 200 - Desligamento de formador
            $assunto=RetornaFraseDaLista($lista_frases, 200);
            
            // 201 - Infelizmente vocï¿½ foi desligado como Formador do curso
            // 98 - Agradecemos sinceramente o seu interesse e recomendamos a visita periï¿½dica do ambiente para a verificaï¿½ï¿½o de novos cursos agendados.
            // 66 - Atenciosamente, Coordenaï¿½ï¿½o do curso
            $mensagem ="<font size=\"2\">";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 201);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong></p>";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 98)."</p><br />";
            $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases, 66);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
            $mensagem.="</font>";
            break;
          case 'Z':
            /* 91 - Rejeitar Inscriï¿½ï¿½es */
            $titulo=RetornaFraseDaLista($lista_frases,91);
            
            // 195 - Inscriï¿½ï¿½o encerrada.
            $assunto=RetornaFraseDaLista($lista_frases,195);
            
            // 183 - Sua inscriï¿½ï¿½o como colaborador para o curso
            // 194 - foi encerrada
            // 98 - Agradecemos o seu interesse e sugerimos a visita periï¿½dica do ambiente para a verificaï¿½ï¿½o de novos cursos agendados.
            // 66 - Atenciosamente, Coordenaï¿½ï¿½o do curso
            $mensagem ="<font size=\"2\">";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 183);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong> ";
            $mensagem.=RetornaFraseDaLista($lista_frases, 194)."</p>";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases,98)."</p><br />";
            $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases, 66);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
            $mensagem.="</font>";
            break;
          case 'V':
            /* 91 - Rejeitar Inscriï¿½ï¿½es */
            $titulo=RetornaFraseDaLista($lista_frases,91);
            
            // 195 - Inscricao encerrada
            $assunto=RetornaFraseDaLista($lista_frases,195);
            
            // 185 - Sua inscriï¿½ï¿½o como visitante para o curso
            // 194 - foi encerrada
            // 98 - Agradecemos a sua participaï¿½ï¿½o e sugerimos a visita periï¿½dica do ambiente para a verificaï¿½ï¿½o de novos cursos agendados.
            /* 66 - Atenciosamente, Coordenaï¿½ï¿½o do curso */
            $mensagem ="<font size=\"2\">";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases,185);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong> ";
            $mensagem.=RetornaFraseDaLista($lista_frases, 194)."</p>";
            $mensagem.="<p>".RetornaFraseDaLista($lista_frases,98)."</p><br />";
            $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases, 66);
            $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
            $mensagem.="</font>";
            break;
        }

      }

      echo("                    <tr class=\"head\">\n");
      echo("                      <td colspan=\"6\"><b>".$titulo."</b></td>\n");
      echo("                    </tr>\n");
      echo("                    <tr class=\"head01\">\n");
      echo("                      <td colspan=\"3\" style=\"border:0; text-align:center;\">\n");

      $cod_coordenador = RetornaCodigoCoordenador ($sock, $cod_curso);

      if ($action_ger == "aceitar")
      {
        /* 113 - Ao enviar a mensagem, estarï¿½ confirmando a aceitaï¿½ï¿½o das inscriï¿½ï¿½es de: */
        echo("                      ".RetornaFraseDaLista($lista_frases,113)."\n");
      }
      else if ($action_ger == "rejeitar")
      {
        /* 92 - Ao enviar a mensagem, estarï¿½ confirmando a rejeiï¿½ï¿½o das inscriï¿½ï¿½es de: */
        echo("                      ".RetornaFraseDaLista($lista_frases,92)."\n");
      }
      else if ($action_ger == 'religar_usuario') {
        switch ($tipo_usuario){
          case 'a':
            // 291 - Ao enviar a mensagem, estara confirmando o religamento dos seguintes alunos:
            echo("                      ".RetornaFraseDaLista($lista_frases,291)."\n");
            break;
          case 'f':
            // 263 - Ao enviar a mensagem, estara confirmando o religamento dos seguintes formadores:
            echo("                      ".RetornaFraseDaLista($lista_frases,263)."\n");
            break;
          case 'z':
            // 335 - Ao enviar a mensagem, estara confirmando o religamento dos seguintes colaboradores:
            echo("                      ".RetornaFraseDaLista($lista_frases,335)."\n");
            break;
          case 'v':
            // 189 - Ao enviar a mensagem, estara confirmando o religamento dos seguintes visitantes:
            echo("                      ".RetornaFraseDaLista($lista_frases,189)."\n");
            break;
        }
      }
      else if($action_ger == "desligar_usuario")
      {
        switch ($tipo_usuario){
          case 'A':
            // 292 - Ao enviar a mensagem, estará confirmando o desligamento dos seguintes alunos:
            echo("                      ".RetornaFraseDaLista($lista_frases,292)."\n");
            break;
          case 'F':
            // 245 - Ao enviar a mensagem, estará confirmando o desligamento dos seguintes formadores:
            echo("                      ".RetornaFraseDaLista($lista_frases,245)."\n");
            break;
          case 'Z':
            // 184 - Ao enviar a mensagem, estará confirmando o desligamento dos seguintes colaboradores:
            echo("                      ".RetornaFraseDaLista($lista_frases,184)."\n");
            break;
          case 'V':
            // 186 - Ao enviar a mensagem, estarï¿½ confirmando o desligamento dos seguintes visitantes:
            echo("                      ".RetornaFraseDaLista($lista_frases,186)."\n");
            break;
        }
      }
      echo("                      </td>\n");
      echo("                    </tr>\n");
      echo("                    <tr class=\"head01\">\n");
      echo("                      <td colspan=\"3\" width=\"50%\" style=\"border:none;text-align:center;\">\n");
      foreach($cod_usu as $cod => $linha)
      {
        if($cod != 0)
          echo(",&nbsp;");
        echo("                        ".NomeUsuario($sock, $linha, $cod_curso)."\n");
        echo("                        <input type=\"hidden\" name=\"cod_usu[]\" value='".$linha."'>\n");
      }
      echo("                      </td>\n");
      echo("                    </tr>\n");
      echo("                    <tr>\n");
      echo("                      <td width=\"25%\" style=\"border:0;\">&nbsp;</td>\n");
      echo("                      <td style=\"border:0;\">\n");
      echo("                        <input type=\"hidden\" name=\"action_ger\"   value=\"".$action_ger."\">\n");
      echo("                        <input type=\"hidden\" name=\"opcao\"        value=\"".$opcao."\">\n");
      echo("                        <input type=\"hidden\" name=\"ordem\"        value=\"".$ordem."\">\n");
      echo("                        <input type=\"hidden\" name=\"tipo_usuario\" value=\"".$tipo_usuario."\">\n");
      echo("                        <input type=\"hidden\" name=\"origem\"       value=\"".$origem."\">\n");
     
      /* 83 - Titulo: */
      echo("                      <br>".RetornaFraseDaLista($lista_frases,83)."<br />\n");
      echo("                      <input class=\"input\" type=\"text\" size=\"60\" name=\"assunto\" value=\"".$assunto."\"><br /><br />\n");

      /* 84 - Mensagem: */
      echo("                      ".RetornaFraseDaLista($lista_frases,84)."\n");
      echo("                      <div align=\"center\"><script type=\"text/javascript\">\n");
      echo("                        writeRichText('mensagem', '".$mensagem."', 520, 200, true, false, false);\n");
      echo("                      </script></div>\n");
      echo("                      <font class=\"text\" color=\"red\">".RetornaFraseDaLista($lista_frases,85)."</font>\n");
      echo("                    </td>\n");
      echo("                    <td width=\"25%\" style=\"border:0;\">&nbsp;</td>\n");
      echo("                  </tr>\n");
      echo("                </table>\n");
      echo("                <p style=\"text-align:right;\">\n");
      /* 11 - Enviar */
      echo("                  <input class=\"input\" type=\"submit\" value='".RetornaFraseDaLista($lista_frases_geral,11)."'>\n");
      echo("                </p>\n");
      echo("              </td>\n");
      echo("            </tr>\n");
    }
    else if ($action_ger == "dados")  // aqui, acao == "dados"
    {
      echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

      if ((isset($origem)) && ($origem=="acoes")) {
        $cod_usu=$cod_usu_s;
      }else{
        session_register("cod_usu_s");
        $cod_usu_s=$cod_usu;
      }

      $cod_usuario_local=$cod_usuario;
      foreach($cod_usu as $cod => $cod_usuario)
      {
        /* Exibir dados dados */

        echo("                  <tr class=\"head\">\n");
        /* 79 - Dados */
        echo("                    <td><b>".RetornaFraseDaLista($lista_frases,79)."</b></td>\n");
        echo("                  </tr>\n");
        echo("                  <tr>\n");
        echo("                    <td align=\"left\" style=\"padding-left:225px;\" >\n");

        echo("                      <table>\n");

        $dados=RetornaDadosUsuario($sock,$cod_curso,$cod_usuario);

        /* 15 - Nome */
        echo("                        <tr>\n");
        echo("                          <td style=\"border:none; text-align:right;\">\n");
        echo("                              &nbsp;<b>".RetornaFraseDaLista($lista_frases,15).":</b>\n");
        echo("                          </td>\n");
        echo("                          <td style=\"border:none\">\n");
        if (($tipo_usuario == "A" || $tipo_usuario == "F") && $SalvarEmArquivo!=1)
          /* Gerenciamento de Aluno ou Formador */
          echo("                            <a href=\"#\" onclick=\"return(OpenWindowPerfil(".$dados['cod_usuario']."));\">");
        echo($dados['nome']);
        if (($tipo_usuario == "A" || $tipo_usuario == "F") && $SalvarEmArquivo!=1) /* Gerenciamento de Aluno ou Formador */
          echo("</a>\n");
        echo("                          </td>\n");
        echo("                        </tr>\n");
        
        if ($dados['tipo_usuario']=="F")
        {
          /* 120 - RG: */
          echo("                        <tr>\n");
          echo("                          <td style=\"border:none; text-align:right;\">\n");
          echo("                            &nbsp;<b>".RetornaFraseDaLista($lista_frases,120)."</b>\n");
          echo("                          </td>\n");
          echo("                          <td style=\"border:none\">\n");
          echo("                            ".$dados['rg']."\n");
          echo("                          </td>\n");
          echo("                        </tr>\n");
        }
        
        /* 121 - E-mail: */
        echo("                        <tr>\n");
        echo("                          <td style=\"border:none; text-align:right;\">\n");
        echo("                            &nbsp;<b>".RetornaFraseDaLista($lista_frases,121)."</b>\n");
        echo("                          </td>\n");
        echo("                          <td style=\"border:none\">\n");
        echo("                              ".$dados['email']."\n");
        echo("                          </td>\n");
        echo("                        </tr>\n");
        /* 122 - Telefone: */
        echo("                        <tr>\n");
        echo("                          <td style=\"border:none; text-align:right;\">\n");
        echo("                            &nbsp;<b>".RetornaFraseDaLista($lista_frases,122)."</b>\n");
        echo("                          </td>\n");
        echo("                          <td style=\"border:none\">\n");
        echo("                            ".$dados['telefone']."\n");
        echo("                          </td>\n");
        echo("                        </tr>\n");
       
        /* 123 - Endereï¿½o: */
        echo("                        <tr>\n");
        echo("                          <td style=\"border:none; text-align:right;\">\n");
        echo("                            &nbsp;<b>".RetornaFraseDaLista($lista_frases,123)."</b>\n");
        echo("                          </td>\n");
        echo("                          <td style=\"border:none\">\n");
        echo("                            ".$dados['endereco']."\n");
        echo("                          </td>\n");
        echo("                        </tr>\n");
        /*246 - Complemento:*/
        echo("                        <tr>\n");
        echo("                          <td style=\"border:none; text-align:right;\">\n");
        echo("                            &nbsp;<b>".RetornaFraseDaLista($lista_frases,246)."</b>\n");
        echo("                          </td>\n");
        echo("                          <td width=\"90%\" style=\"border:none\">\n");
        echo("                            ".$dados['cidade']." - ".$dados['estado']." - ".$dados['pais']."\n");
        echo("                          </td>\n");
        echo("                        </tr>\n");

        /* 124 - Data de Nascimento: */
        echo("                        <tr>\n");
        echo("                          <td style=\"border:none; text-align:right;\">\n");
        echo("                            &nbsp;<b>".RetornaFraseDaLista($lista_frases,124)."</b>\n");
        echo("                          </td>\n");
        echo("                          <td style=\"border:none\">\n");
        echo("                            ".Unixtime2Data($dados['data_nasc'])."\n");
        echo("                          </td>\n");
        echo("                        </tr>\n");
        
        /* 125 - Sexo: */
        echo("                        <tr>\n");
        echo("                          <td style=\"border:none; text-align:right;\">\n");
        echo("                            &nbsp;<b>".RetornaFraseDaLista($lista_frases,125)."</b>\n");
        echo("                          </td>\n");
        echo("                          <td style=\"border:none\">\n");
        if ($dados['sexo']=="M")
          /* 126 - Masculino */
          echo("                            ".RetornaFraseDaLista($lista_frases,126));

        if ($dados['sexo']=="F")
          /* 127 - Feminino */
          echo("                            ".RetornaFraseDaLista($lista_frases,127));
        echo("                          </td>\n");
        echo("                        </tr>\n");

        /* 128 - Local de Trabalho: */
        echo("                        <tr>\n");
        echo("                          <td style=\"border:none; text-align:right;\">\n");
        echo("                            &nbsp;<b>".RetornaFraseDaLista($lista_frases,128)."</b>\n");
        echo("                          </td>\n");
        echo("                          <td style=\"border:none\">\n");
        echo("                            ".$dados['local_trab']."\n");
        echo("                          </td>\n");
        echo("                        </tr>\n");

        /* 129 - Profissï¿½o: */
        echo("                        <tr>\n");
        echo("                          <td style=\"border:none; text-align:right;\">\n");
        echo("                            &nbsp;<b>".RetornaFraseDaLista($lista_frases,129)."</b>\n");
        echo("                          </td>\n");
        echo("                          <td style=\"border:none\">\n");
        echo("                            ".$dados['profissao']."\n");
        echo("                          </td>\n");
        echo("                        </tr>\n");

        /* 130 - Escolaridade: */
        $escolaridade=RetornaFraseDaLista($lista_frases_geral,$lista_esc[$dados['cod_escolaridade']]['cod_texto_escolaridade']);
        echo("                        <tr>\n");
        echo("                          <td style=\"border:none; text-align:right;\">\n");
        echo("                            &nbsp;<b>".RetornaFraseDaLista($lista_frases,130)."</b>\n");
        echo("                          </td>\n");
        echo("                          <td style=\"border:none\">\n");
        echo("                            ".$escolaridade."\n");
        echo("                          </td>\n");
        echo("                        </tr>\n");

        /* 131 - Informaï¿½ï¿½es Adicionais: */
        echo("                        <tr>\n");
        echo("                          <td style=\"border:none; text-align:right;\">\n");
        echo("                            &nbsp;<b>".RetornaFraseDaLista($lista_frases,131)."</b>\n");
        echo("                          </td>\n");
        echo("                          <td style=\"border:none\">\n");
        echo("                            ".$dados['informacoes']."\n");
        echo("                          </td>\n");
        echo("                        </tr>\n");

        /* 132 - Data de Inscriï¿½ï¿½o: */
        echo("                        <tr>\n");
        echo("                          <td width=\"20%\" style=\"border:none; text-align:right;\">\n");
        echo("                            &nbsp;<b>".RetornaFraseDaLista($lista_frases,132)."</b>\n");
        echo("                          </td>\n");
        echo("                          <td style=\"border:none\">\n");
        echo("                            ".UnixTime2Data($dados['data_inscricao'])."\n");
        echo("                          </td>\n");
        echo("                        </tr>\n");

        echo("                      </table>\n");

      }

      echo("                    </td>\n");
      echo("                  </tr>\n");
      echo("                </table>\n");
    }
    else
    {
      echo("<big>Ação inválida inesperada !</big><br>\n");
      var_dump ($action_ger);
      exit();
    }
     echo("                </td>\n");
     echo("              </tr>\n");
     echo("            </table>\n");

  }

  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
