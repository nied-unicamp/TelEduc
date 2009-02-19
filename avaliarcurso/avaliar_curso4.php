<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/avaliar_curso4.php

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
  ARQUIVO : administracao/avaliar_curso4.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("../administracao/admin.inc");
  include("avaliarcurso.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  $query="select valor from Config where item='host'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $host=$linha['valor'];

  $query="select diretorio from Diretorio where item='raiz_www'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $raiz_www=$linha['diretorio'];

  /* Inicio do JavaScript */
  echo("<script type=\"text/javascript\">\n");

  echo("  function Iniciar()\n"); 
  echo("  {\n");
  echo("	startList();\n");
  echo("  }\n");

  echo("</script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 244 - Avaliar requisi��es para abertura de cursos */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,244)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");


  $curso=RetornaDadosCurso($sock,$cod_curso);

  $cod_curso_antigo=$cod_curso;
  $cod_curso=RetornaNovoCodCurso($sock);

  if ($opcao=="Aceitar")
  {

    if ($curso['cod_pasta'] == "")
      $curso['cod_pasta'] = "NULL";

    Desconectar($sock);
    CriarCurso($cod_curso,$cod_curso_antigo,$curso['nome_curso'],$curso['num_alunos'],$curso['cod_pasta'],$curso['informacoes'],$curso['publico_alvo'],$curso['tipo_inscricao'],$curso['email_contato']);
    $sock=Conectar("");

    // Criar Diret�rios

    $diretorio=RetornaDiretorioOpcao($sock,'Arquivos');

    CriaDiretorio($diretorio."/".$cod_curso);
    CriaDiretorio($diretorio."/".$cod_curso."/dinamica");
    CriaDiretorio($diretorio."/".$cod_curso."/agenda");
    CriaDiretorio($diretorio."/".$cod_curso."/atividades");
    CriaDiretorio($diretorio."/".$cod_curso."/apoio");
    CriaDiretorio($diretorio."/".$cod_curso."/leituras");
    CriaDiretorio($diretorio."/".$cod_curso."/obrigatoria");
    CriaDiretorio($diretorio."/".$cod_curso."/correio");
    CriaDiretorio($diretorio."/".$cod_curso."/perfil");
    CriaDiretorio($diretorio."/".$cod_curso."/portfolio");
  }

  // Enviar e-mail para o coordenador

  $remetente = RetornaConfigOpcao($sock,'adm_email');
  $destino = $curso['email_contato'];

  Desconectar($sock);

  $mensagem_envio = MontaMsg($host, $raiz_www, $cod_curso, $mensagem, $assunto);
  MandaMsg($remetente,$destino,$assunto,$mensagem_envio);

  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  /* 23 - Voltar (gen) */
  echo("                  <ul class=\"btAuxTabs\">\n");
  echo("                    <li><a href=\"#\" onclick=\"window.location = '../administracao/index.php';\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("                  </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr>\n");
  echo("                    <td>\n");
  if ($opcao=="Aceitar")
  {
    /* 106 - Curso criado corretamente. */
    echo("                      <p>".RetornaFraseDaLista($lista_frases,106)."</p>\n");
    /* 107 - Um email com as instru��es de acesso ao curso foi enviado ao coordenador cadastrado. */
    echo("                      <p>".RetornaFraseDaLista($lista_frases,107)."</p>\n");
  }
  else if ($opcao=="Rejeitar")
  {
    $sock=Conectar("");
    RejeitaCurso($sock,$cod_curso_antigo);
    Desconectar($sock);

    /* 242 - Curso rejeitado corretamente. */
    echo("                      <p>".RetornaFraseDaLista($lista_frases,242)."</p>\n");
    /* 243 - Um email com essa informa��o foi enviado ao contatante. */
    echo("                      <p>".RetornaFraseDaLista($lista_frases, 243)."</p>\n");
  }
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>
