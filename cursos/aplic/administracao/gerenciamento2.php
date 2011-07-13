<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/gerenciamento2.php

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
  ARQUIVO : cursos/aplic/administracao/gerenciamento2.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta = 0;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda = 9;

  include("../topo_tela.php");
  
  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,0);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  $lista_esc=RetornaListaEscolaridade($sock);

  Desconectar($sock);

  #Verifica se o email foi passado por post para atualização
  if(isset($_POST['c_email'])){
  	AtualizaEmailUsuario(Conectar(''),$cod_curso,$_POST['c_usuario'],$_POST['c_email']);
  }
	$sock=Conectar($cod_curso);
	VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  echo("    <script type=\"text/javascript\" src=\"../js-css/dhtmllib.js\"></script>\n");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor.js\"></script>");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor_biblioteca.js\"></script>");
  echo("    <script type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/javacrypt.js\" defer></script>\n");

  if($opcao == "dados")
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

    /* 51 - Infelizmente n�o foi poss�vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
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

  echo("</script>");
  include("../menu_principal.php");
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
  	/* 1 - Administracao  297 - Area restrita ao formador. */
  	echo("<h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,28)."</h4>\n");
	
    /*Voltar*/
    echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* 23 - Voltar (gen) */
    echo("<form><input class=\"input\" type=button value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" /></form>\n");

    Desconectar($sock);
    exit();
  }
  
  // P�gina Principal
  /* 1 - Administra��o */
  $cabecalho = "          <h4>".RetornaFraseDaLista ($lista_frases, 1);

  if ($acao=="A")
  {
    /* 102 - Gerenciamento de Alunos */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 102)."</h4>\n";
    $tipo_usuario="A";
    $cod_pagina=9;
  }
  else if ($acao=="F")
  {
    /* 103 - Gerenciamento de Formadores */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 103)."</h4>\n";
    $tipo_usuario="F";
    $cod_pagina=10;
  }
  else if ($acao=="G")
  {
    /* 258 - Gerenciamento de Formadores desligados */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 258)."</h4>\n";
    $tipo_usuario="F";
    $cod_pagina=10;
  }
  else if ($acao=="AG")
  {
    /* 283 - Gerenciamento de Alunos desligados */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 283)."</h4>\n";
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
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 74)."</h4>\n";
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
  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br />\n");

  // se veio da pagina de gerenciamento de convidados, para l� deve voltar.
  // Senao volta para pagina de gerenciamento de alunos / formadores
  if ($origem == "convidado")
    $redireciona = "gerenciamento4.php";
  else if ($origem == "visitantes")
    $redireciona = "gerenciamento_visitantes.php";
  else
    $redireciona = "gerenciamento.php";

  echo("            <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("              <tr>\n");
  echo("                <td valign=\"top\">\n");
  /* 23 - Voltar (gen) */
  echo("                  <ul class=\"btAuxTabs\">\n");
  echo("                    <li><a href=\"".$redireciona."?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=".$acao."&amp;opcao=".$opcao."&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  if ((count($cod_usu)==0) && (!isset($origem)))
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
    if ($opcao == "dados")
    {
      /* 50 - Salvar em Arquivo (ger) */
      if ($SalvarEmArquivo!=1)
        echo("                    <li><a href=\"salvar_gerenciamento2.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=".$acao."&amp;ordem=".$ordem."&amp;opcao=".$opcao."\">".RetornaFraseDaLista($lista_frases_geral,50)."</a></li>\n");
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

    if ($opcao=="aceitar" || $opcao=="rejeitar" || $opcao == "aceitar_vis" || $opcao == "remover_aluno" || $opcao == "remover_form" || $opcao == "encerrarConvite" || $opcao == "rejeitarVisitantes"|| $opcao == "religar_form" || $opcao == "religar_aluno")
    {
      echo("                  <form action=\"gerenciamento3.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\" name=\"gerenc\" method=\"post\" onsubmit=\"return(updateRTE('mensagem'));\">\n");
      echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

      $dados_curso=DadosCursoParaEmail($sock, $cod_curso);

      if ($opcao=="aceitar")
      {
        /* 90 - Aceitar Inscri��es */
        $titulo=RetornaFraseDaLista($lista_frases,90);

        /* 112 - Inscri��o aceita */
        $assunto=RetornaFraseDaLista($lista_frases,112);
		$assunto.=$dados_curso['nome_curso'];
        /* 99 - Sua inscri��o como aluno para o curso */
        /* 100 - foi aceita. */
        /* 65 - Visite a p�gina do curso para obter informa��es sobre o seu in�cio */
        /* 101 - Agradecemos sinceramente o seu interesse */
        /* 66 - Atenciosamente, Coordena��o do curso */

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
      else if ($opcao == "aceitar_vis")
      {
        /* 90 - Aceitar Inscri��es */
        $titulo=RetornaFraseDaLista($lista_frases,90);

        /* 112 - Inscri��o aceita */
        $assunto=RetornaFraseDaLista($lista_frases,112).$dados_curso['nome_curso'];

        /* 188 - Voc� foi inscrito como visitante para o curso */
        /* 65 - Visite a p�gina do curso para obter informa��es sobre o seu in�cio */
        /* 101 - Agradecemos sinceramente o seu interesse */
        /* 66 - Atenciosamente, Coordena��o do curso */
        $mensagem ="<font size=\"2\">";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,188);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,65).".</p>";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,101).".</p><br />";
        $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases,66);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
        $mensagem.="</font>";
      }
      else if ($opcao == "rejeitar")
      {
        /* 91 - Rejeitar Inscri��es */
        $titulo=RetornaFraseDaLista($lista_frases,91);

        /* 111 - Inscri��o n�o aceita */
        $assunto=RetornaFraseDaLista($lista_frases,111);

        /* 96 - Infelizmente sua inscri��o como aluno para o curso */
        /* 97 - n�o foi aceita. */
        /* 98 - Agradecemos sinceramente o seu interesse e recomendamos a visita peri�dica do ambiente para a verifica��o de novos cursos agendados. */
        /* 66 - Atenciosamente, Coordena��o do curso */
        $mensagem ="<font size=\"2\">";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,96);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong> ";
        $mensagem.=RetornaFraseDaLista($lista_frases,97)."</p>";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,98)."</p><br />";
        $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases,66);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
        $mensagem.="</font>";
      }

      else if($opcao == "remover_form")
      {
        // 199 - Desligar Formador
        $titulo=RetornaFraseDaLista($lista_frases,199);

        // 200 - Desligamento de formador
        $assunto=RetornaFraseDaLista($lista_frases, 200);

        // 201 - Infelizmente voc� foi desligado como Formador do curso
        // 98 - Agradecemos sinceramente o seu interesse e recomendamos a visita peri�dica do ambiente para a verifica��o de novos cursos agendados.
        // 66 - Atenciosamente, Coordena��o do curso
        $mensagem ="<font size=\"2\">";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 201);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong></p>";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 98)."</p><br />";
        $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases, 66);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
        $mensagem.="</font>";
      }
      else if($opcao == "religar_form")
      {
        // 260 - Religar Formador
        $titulo=RetornaFraseDaLista($lista_frases,260);

        // 267 - Religamento de formador
        $assunto=RetornaFraseDaLista($lista_frases, 267);

        // 264 -Voce foi religado como Formador do curso
        // 265 - Seu login e senha não foram alterados
        // 266 - Acesse o curso:
        // 66 - Atenciosamente, Coordena��o do curso

 
        
        $mensagem ="<font size=\"2\">";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 264);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong></p>";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 265)."</p><br />";
        $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases, 66);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
        $mensagem.="</font>";

      }
      else if($opcao == "religar_aluno")
      {
        // 285 - Religar Aluno
        $titulo=RetornaFraseDaLista($lista_frases,285);

        // 286 - Religamento de Aluno
        $assunto=RetornaFraseDaLista($lista_frases, 286);

        // 287 -Voce foi religado como Aluno do curso
        // 265 - Seu login e senha não foram alterados
        // 266 - Acesse o curso:
        // 66 - Atenciosamente, Coordena��o do curso

 
        
        $mensagem ="<font size=\"2\">";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 287);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong></p>";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 265)."</p><br />";
        $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases, 66);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
        $mensagem.="</font>";

      }
      else if($opcao == "remover_aluno")
      {
        // 288 - Desligar Aluno
        $titulo=RetornaFraseDaLista($lista_frases,288);

        // 289 - Desligamento de Aluno
        $assunto=RetornaFraseDaLista($lista_frases, 289);

        // 290 - Infelizmente voc� foi desligado como Aluno do curso
        // 98 - Agradecemos sinceramente o seu interesse e recomendamos a visita peri�dica do ambiente para a verifica��o de novos cursos agendados.
        // 66 - Atenciosamente, Coordena��o do curso
        $mensagem ="<font size=\"2\">";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 290);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong></p>";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 98)."</p><br />";
        $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases, 66);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
        $mensagem.="</font>";
      }
      
      else if($opcao == "encerrarConvite")
      {
        /* 91 - Rejeitar Inscri��es */
        $titulo=RetornaFraseDaLista($lista_frases,91);

        // 195 - Inscri��o encerrada.
        $assunto=RetornaFraseDaLista($lista_frases,195);

        // 183 - Sua inscri��o como convidado para o curso
        // 194 - foi encerrada
        // 98 - Agradecemos o seu interesse e sugerimos a visita peri�dica do ambiente para a verifica��o de novos cursos agendados.
        // 66 - Atenciosamente, Coordena��o do curso
        $mensagem ="<font size=\"2\">";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases, 183);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong> ";
        $mensagem.=RetornaFraseDaLista($lista_frases, 194)."</p>";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,98)."</p><br />";
        $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases, 66);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
        $mensagem.="</font>";
      }

      else if($opcao == "rejeitarVisitantes")
      {
        /* 91 - Rejeitar Inscri��es */
        $titulo=RetornaFraseDaLista($lista_frases,91);
  
        // 195 - Inscricao encerrada
        $assunto=RetornaFraseDaLista($lista_frases,195);
  
        // 185 - Sua inscri��o como visitante para o curso
        // 194 - foi encerrada
        // 98 - Agradecemos a sua participa��o e sugerimos a visita peri�dica do ambiente para a verifica��o de novos cursos agendados.
    
    
        /* 66 - Atenciosamente, Coordena��o do curso */
        $mensagem ="<font size=\"2\">";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,185);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong> ";
        $mensagem.=RetornaFraseDaLista($lista_frases, 194)."</p>";
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,98)."</p><br />";
        $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases, 66);
        $mensagem.=" <strong>".$dados_curso['nome_curso']."</strong>.</p>";
        $mensagem.="</font>";
      }

      echo("                    <tr class=\"head\">\n");
      echo("                      <td colspan=\"6\"><b>".$titulo."</b></td>\n");
      echo("                    </tr>\n");
      echo("                    <tr class=\"head01\">\n");
      echo("                      <td colspan=\"3\" style=\"border:0; text-align:right;\">\n");
      $cod_coordenador = RetornaCodigoCoordenador ($sock, $cod_curso);

      if ($opcao=="aceitar")
      {
        /* 113 - Ao enviar a mensagem, estar� confirmando a aceita��o das inscri��es de: */
        echo("                      ".RetornaFraseDaLista($lista_frases,113)."\n");
      }
      else if ($opcao == "rejeitar")
      {
        /* 92 - Ao enviar a mensagem, estar� confirmando a rejei��o das inscri��es de: */
        echo("                      ".RetornaFraseDaLista($lista_frases,92)."\n");
      }
      else if ($opcao == "aceitar_vis")
      {
        /* 189 - Ao enviar a mensagem, estar� inscrevendo como Visitantes: */
        echo("                      ".RetornaFraseDaLista($lista_frases,189)."\n");
      }
      else if($opcao == "encerrarConvite")
      {
        // 184 - Ao enviar a mensagem, estar� confirmando o cancelamento dos seguintes convidados:
        echo("                      ".RetornaFraseDaLista($lista_frases,184)."\n");
      }
      else if ($opcao == "rejeitarVisitantes")
      {
        // 186 - Ao enviar a mensagem, estar� confirmando o cancelamento dos seguintes visitantes:
        echo("                      ".RetornaFraseDaLista($lista_frases,186)."\n");
      }
      else if($opcao == "remover_form")
      {
        // 245 - Ao enviar a mensagem, estar� confirmando o desligamento dos seguintes formadores:
        echo("                      ".RetornaFraseDaLista($lista_frases,245)."\n");
      }
      else if($opcao == "religar_form")
      {
        // 263 - Ao enviar a mensagem, estara confirmando o religamento dos seguintes formadores:
        echo("                      ".RetornaFraseDaLista($lista_frases,263)."\n");
      }
      else if($opcao == "religar_aluno")
      {
        // 291 - Ao enviar a mensagem, estara confirmando o religamento dos seguintes alunos:
        echo("                      ".RetornaFraseDaLista($lista_frases,291)."\n");
      }
      
      else if($opcao == "remover_aluno")
      {
        // 292 - Ao enviar a mensagem, estar� confirmando o desligamento dos seguintes alunos:
        echo("                      ".RetornaFraseDaLista($lista_frases,292)."\n");
      }
      echo("                      <td colspan=\"3\" width=\"50%\" style=\"border:none;text-align:left;\">\n");
      foreach($cod_usu as $cod => $linha)
      {
        if($cod != 0)
          echo(", \n");
        else
          echo("\n");
        echo("                        ".NomeUsuario($sock,$linha, $cod_curso)."\n");
        echo("                        <input type=\"hidden\" name=\"cod_usu[]\" value='".$linha."'>\n");
      }
      echo("                      </td>\n");
      echo("                    </tr>\n");
      echo("                    <tr>\n");
      echo("                      <td width=\"25%\" style=\"border:0;\">&nbsp;</td>\n");
      echo("                      <td colspan=\"4\" style=\"border:0;\">\n");
      echo("                        <input type=\"hidden\" name=\"acao\" value=\"".$acao."\">\n");
      echo("                        <input type=\"hidden\" name=\"opcao\" value=\"".$opcao."\">\n");
      echo("                        <input type=\"hidden\" name=\"ordem\" value=\"".$ordem."\">\n");
      echo("                        <input type=\"hidden\" name=\"origem\" value=\"".$redireciona."\">\n");
     
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
//     else if ($opcao=="aluno" || $opcao=="formador" || $opcao == "convidado")
//     {
//       echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
//       echo("                  <tr>\n");
//       echo("                    <td>\n");
//       // transformar um cara que eh aluno ou convidado em formador ou um cara que eh formador ou convidado em aluno
//       echo("                      <br />\n");
//       if ($opcao=="formador")
//       {
//         $tipo_usuario="F";
// 
//         if (isset ($convidado))
//           // 170 - Convidado
//           $parte1=RetornaFraseDaLista($lista_frases,170);
//         else
//           // 115 - Aluno
//           $parte1=RetornaFraseDaLista($lista_frases,115);
// 
//         /* 116 - transformado em formador. */
//           $parte2=RetornaFraseDaLista($lista_frases,116);
// 
// 
//       }
// 
//       else if ($opcao == "aluno")
//       {
//         $tipo_usuario="A";
// 
//         if (isset($convidado))
//           // 170 - Convidado
//           $parte1=RetornaFraseDaLista($lista_frases, 170);
//         else
//           // 117 - Formador
//           $parte1=RetornaFraseDaLista($lista_frases,117);
// 
//         /* 118 - transformado em aluno. */
//           $parte2=RetornaFraseDaLista($lista_frases,118);
// 
//       }
// 
//       else if ($opcao == "convidado")
//       {
//         $tipo_usuario="z";
// 
//         if ($acao == 'A')
//           // 115 - Aluno
//           $parte1=RetornaFraseDaLista($lista_frases, 115);
//         else
//           // 117 - Formador
//           $parte1=RetornaFraseDaLista($lista_frases,117);
// 
//         /* 190 - transformado em convidado. */
//         $parte2=RetornaFraseDaLista($lista_frases,190);
// 
//       }
// 
//       {
//           foreach($cod_usu as $cod => $cod_usuario)
//           {
//             MudaTipoUsuario($sock,$cod_curso,$cod_usuario,$tipo_usuario);
//             echo("                      ".$parte1."<b>".NomeUsuario($sock,$cod_usuario, $cod_curso)."</b>".$parte2."<br>\n");
//           }
//       }

//       echo("                      </td>\n");
//       echo("                    </tr>\n");
//       echo("                  </table>\n");
// 
//       /* 23 - Voltar (gen) */
//       echo("                <ul class=\"btAuxTabs\">\n");
//       echo("                  <li><a href=\"".$redireciona."?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=".$acao."&amp;opcao=".$opcao."&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
//       echo("                </ul>\n");
//     }
    else if ($opcao == "dados")  // aqui, opcao == "dados"
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
        if (($acao=="A" || $acao=="F") && $SalvarEmArquivo!=1)
          /* Gerenciamento de Aluno ou Formador */
          echo("                            <a href=\"#\" onclick=\"return(OpenWindowPerfil(".$dados['cod_usuario']."));\">");
        echo($dados['nome']);
        if (($acao=="A" || $acao=="F") && $SalvarEmArquivo!=1) /* Gerenciamento de Aluno ou Formador */
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
		    if(EFormadorMesmo($sock, $cod_curso, $cod_usuario_local)){			
		    	echo("															<div>".$dados['email']." <input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,24)."\" onclick=\"document.getElementById('div_email').style.display='block'; this.parentNode.style.display='none';\"/></div>
		    																			<div id=\"div_email\" style=\"display:none;\">
			    																			<form action=\"\" method=\"post\">
						                              				<input class=\"input\" type=\"text\" name=\"c_email\" value=\"".$dados['email']."\"/>
						                              				<input type=\"hidden\" name=\"c_usuario\" value=\"".$dados['cod_usuario']."\"/>
						                              				<input class=\"input\" type=\"submit\">
																								</form>
																							</div>");
				}else
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
       
        /* 123 - Endere�o: */
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

        /* 129 - Profiss�o: */
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

        /* 131 - Informa��es Adicionais: */
        echo("                        <tr>\n");
        echo("                          <td style=\"border:none; text-align:right;\">\n");
        echo("                            &nbsp;<b>".RetornaFraseDaLista($lista_frases,131)."</b>\n");
        echo("                          </td>\n");
        echo("                          <td style=\"border:none\">\n");
        echo("                            ".$dados['informacoes']."\n");
        echo("                          </td>\n");
        echo("                        </tr>\n");

        /* 132 - Data de Inscri��o: */
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
//     else if(($opcao == "desativar_port") || ($opcao == "ativar_port"))
//     {
//         echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
// 
// 	// 210 - Status de Portfolio
//     	echo("                  ".RetornaFraseDaLista($lista_frases, 210)."<br><br>\n");
//     	
// 	echo("                  <tr class=\"head\">\n");
//         // 119 - Nome
//         echo("                    <td align=\"left\"><b>".RetornaFraseDaLista($lista_frases,119)."</b></td>\n");
//         // 132 - Data de inscri��o
//         echo("                    <td align=\"center\" width=\"15%\"><b>".RetornaFraseDaLista($lista_frases,132)."</b></td>\n");
//         // 211 - Portifolio
//         echo("                    <td align=\"center\" width=\"15%\"><b>".RetornaFraseDaLista($lista_frases,211)."</b></td>\n");
//         echo("                  </tr>\n");
// 	
// 	foreach($cod_usu as $cod => $cod_usuario){
// 		StatusPortfolio($sock,$cod_curso, $cod_usuario, $opcao);
// 		$dados=RetornaDadosUsuario($sock,$cod_curso,$cod_usuario);
// 		
// 		echo("                  <tr>\n");
// 		echo("                    <td align=\"left\">".$dados['nome']."</td>\n");
// 		echo("                    <td>".Unixtime2Data($dados['data_inscricao'])."</td>\n");
// 		if($dados['portfolio'] == "ativado")
// 		  echo("                    <td>".RetornaFraseDaLista($lista_frases, 208)."</td>\n");
// 		else
// 		  echo("                    <td>".RetornaFraseDaLista($lista_frases, 209)."</td>\n");
// 		echo("                  </tr>\n");
// 		
//       	}
// 	echo("                </table>\n");
//         /* 23 - Voltar (gen) */
//         echo("                <ul class=\"btAuxTabs\">\n");
//         echo("                  <li><a href=\"".$redireciona."?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=".$acao."&amp;opcao=".$opcao."&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
//         echo("                </ul>\n");
//     
//     }
    else
    {
      echo("<big>variavel opcao inesperada !</big><br>\n");
      var_dump ($opcao);
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
