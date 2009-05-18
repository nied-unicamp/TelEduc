<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/gerenciamento3.php

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
  ARQUIVO : cursos/aplic/administracao/gerenciamento3.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta=0;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda = 9;

  include("../topo_tela.php");
  

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,0);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

 
  if (!isset($ordem))
  {
    $ordem="nome";
  }

  

  echo("    <script type=\"text/javascript\">\n\n");
  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");
  echo("</script>");

  include("../menu_principal.php");
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  // P�gina Principal
  /* 1 - Administra��o */
  $cabecalho = "          <h4>".RetornaFraseDaLista ($lista_frases, 1);

  if ($acao=="A")
  {
    /* 102 - Gerenciamento de Alunos */
    $cabecalho .= "  - ".RetornaFraseDaLista($lista_frases, 102)."</h4>";
    $tipo_usuario="A";
    $cod_pagina=9;
  }
  else if ($acao=="F")
  {
    /* 103 - Gerenciamento de Formadores */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 103)."</h4>";
    $tipo_usuario="F";
    $cod_pagina=10;
  }
  else if ($acao=="G")
  {
    /* 258 - Gerenciamento de Formadores desligados */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 258)."</h4>";
    $tipo_usuario="F";
    $cod_pagina=10;
  }
  else if ($acao=="AG")
  {
    /* 283 - Gerenciamento de Alunos desligados */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 283)."</h4>";
    $tipo_usuario="A";
    $cod_pagina=10;
  }
  else if ($acao == 'z')
  {
    // 165 - Gerenciamento de Convidados
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 165)."</h4>\n";
    $cod_pagina=13;
  }
  else if ($acao == 'V')
  {
    // 179 - Gerenciamento de Visitantes
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 179)."</h4>\n";
    $cod_pagina=16;
  }
  else
  {
    /* 74 - Gerenciamento de Inscri��es */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 74)."</h4>";
    $cod_pagina=8;
  }
  
  echo($cabecalho); 

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/			
  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  if ($opcao == "aceitar" || $opcao == "rejeitar" || $opcao == "aceitar_vis" || $opcao == "remover_aluno" || $opcao == "remover_form" || $opcao == "encerrarConvite" || $opcao == "rejeitarVisitantes" || $opcao=="religar_form" || $opcao == "religar_aluno")
  {
    $mensagem=ConverteBarraAspas2Aspas($mensagem);
    if ($opcao=="aceitar")
    {
       /* 90 - Aceitar Inscri��es */
      $titulo=RetornaFraseDaLista($lista_frases,90);

      /* 86 - Inscri��es aceitas com sucesso. */
      $confirmacao=RetornaFraseDaLista($lista_frases,86);

      /* 87 - Para cada inscri��o aceita est� sendo enviado automaticamente um e-mail que lhe garantir� acesso ao curso. Este acesso somente ser� liberado para os alunos a partir da data de in�cio do curso, que pode ser alterada na ferramenta <b>Cronograma</b> (em Gerenciamento de Curso). */
      $aviso=RetornaFraseDaLista($lista_frases,87);
      
      // vamos ativar os portfolios dos usuarios aceitos
      foreach($cod_usu as $cod => $cod_usuario)
       {
        StatusPortfolio($sock,$cod_curso, $cod_usuario, "ativar_port");
       }
	
      $tipo_usuario="A";
    }
    else if ($opcao == "rejeitar" || $opcao == "rejeitarVisitantes")
    {
      /* 91 - Rejeitar Inscri��es */
      $titulo=RetornaFraseDaLista($lista_frases,91);

      /* 93 - Inscri��es rejeitadas com sucesso. */
      $confirmacao=RetornaFraseDaLista($lista_frases,93);

      /* 94 - Para cada inscri��o rejeitada est� sendo enviado automaticamente um e-mail informando sua decis�o de n�o aceit�-los em seu curso. */
      $aviso=RetornaFraseDaLista($lista_frases,94);

      $tipo_usuario="r";
    }
    else if ($opcao == "aceitar_vis")
    {
       /* 90 - Aceitar Inscri��es */
      $titulo=RetornaFraseDaLista($lista_frases,90);

      /* 86 - Inscri��es aceitas com sucesso. */
      $confirmacao=RetornaFraseDaLista($lista_frases,86);

      /* 87 - Para cada inscri��o aceita est� sendo enviado automaticamente um e-mail que lhe garantir� acesso ao curso. Este acesso somente ser� liberado para os alunos a partir da data de in�cio do curso, que pode ser alterada na ferramenta <b>Cronograma</b> (em Gerenciamento de Curso). */
      $aviso=RetornaFraseDaLista($lista_frases,87);

      $tipo_usuario="V";
    }
    else if ($opcao == "remover_form")
    {
      // 199 - Desligar Formador
      $titulo=RetornaFraseDaLista($lista_frases,199);

      /* 240 - Formador(es) desligado(s) com sucesso. */
      $confirmacao= RetornaFraseDaLista($lista_frases,240);

      /* 241 - Mensagem de cancelamento*/
      $aviso= RetornaFraseDaLista($lista_frases,241);

      foreach($cod_usu as $cod => $cod_usuario)
      {
        RemoverFormador($sock,$cod_curso, $cod_usuario);
      }
  
      $tipo_usuario="f";  
    }
    else if ($opcao == "religar_form")
    {
      // 260 - Religar Formador
      $titulo=RetornaFraseDaLista($lista_frases,260);

      /* 262 - Formador(es) religado(s) com sucesso. */
      $confirmacao= RetornaFraseDaLista($lista_frases,262);

      /* 281 - Mensagem de confirmacao*/
      $aviso= RetornaFraseDaLista($lista_frases,281);

      $tipo_usuario="F";  
    }
    else if ($opcao == "religar_aluno")
    {
      // 285 - Religar Aluno
      $titulo=RetornaFraseDaLista($lista_frases,285);

      /* 293 - Aluno(s) religado(s) com sucesso. */
      $confirmacao= RetornaFraseDaLista($lista_frases,293);

      /* 296 - Mensagem de confirmacao*/
      $aviso= RetornaFraseDaLista($lista_frases,296);

      $tipo_usuario="A";  
    }
    else if ($opcao == "remover_aluno")
    {
      // 288 - Desligar Aluno
      $titulo=RetornaFraseDaLista($lista_frases,288);

      /* 294 - Aluno(s) desligado(s) com sucesso. */
      $confirmacao= RetornaFraseDaLista($lista_frases,294);

      /* 295 - Mensagem de cancelamento*/
      $aviso= RetornaFraseDaLista($lista_frases,295);

      foreach($cod_usu as $cod => $cod_usuario)
      {
        RemoverAluno($sock,$cod_curso, $cod_usuario);
      }
  
      $tipo_usuario="a";  
    }
    else if ($opcao == "encerrarConvite")
    {
      /* 91 - Rejeitar Inscri��es */
      $titulo=RetornaFraseDaLista($lista_frases,91);

      /* 242 - Convites encerrados com sucesso. */
      $confirmacao= RetornaFraseDaLista($lista_frases,242);

      /* 243 - Mensagem de cancelamento*/
      $aviso= RetornaFraseDaLista($lista_frases,243);

      $tipo_usuario="t";
    }

    $sock=MudarTipoUsuarioComMensagem($sock,$cod_curso,$cod_usu,$tipo_usuario,$assunto,$mensagem);

    echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
    echo("            <tr>\n");
    echo("              <td valign=\"top\">\n");
    /* 23 - Voltar (gen) */
    echo("              <ul class=\"btAuxTabs\">\n");
    echo("                <li><a href=\"".$origem."?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=".$acao."&amp;opcao=".$opcao."&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
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
  }else{
    echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
    echo("            <tr>\n");
    echo("              <td valign=\"top\">\n");
    /* 1 - Administração */
    echo("              <ul class=\"btAuxTabs\">\n");
    echo("                <li><a href=\"gerenciamento.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=".$acao."&amp;opcao=".$opcao."&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,1)."</a></li>\n");
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

  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
