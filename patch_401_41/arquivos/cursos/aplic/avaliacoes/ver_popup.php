<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/ver.php

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
  ARQUIVO : cursos/aplic/avaliacoes/ver_popup.php
  ========================================================== */
// TODO - Incluir topo tela e tela2
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  $cod_usuario_global=VerificaAutenticacao($cod_curso);
  
  $sock=Conectar("");
  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);


  $lista_frases=RetornaListaDeFrases($sock,COD_AVALIACAO);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  $lista_frases_biblioteca =RetornaListaDeFrases($sock,-2);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario, COD_AVALIACAO);
  
  // Verifica se o usuario eh formador.
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);
  $usr_aluno = EAluno($sock, $cod_curso, $cod_usuario);

  $dados_avaliacao=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);

  if (EConvidado($sock, $cod_usuario, $cod_curso) || EVisitante($sock, $cod_curso, $cod_usuario))
  {
    echo("<html>\n");
    echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
    // 1 - Avalia��es
    $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases, 1)."</b>";
    // 94 - Usu�rio sem acesso
    $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 94)."</b>";
    echo(PreparaCabecalho($cod_curso, $cabecalho, COD_AVALIACAO, 1));
    echo("    <br>\n");
    echo("    <p>\n");

    echo("  </body>\n");
    echo("  </html>\n");
    exit();
  }

  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n"); echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  echo("  <head>\n");
  echo("    <title>TelEduc . Ensino &agrave; Dist&acirc;ncia</title>\n");
  echo("    <meta name=\"robots\" content=\"follow,index\" />\n");
  echo("    <meta name=\"description\" content=\"\" />\n");
  echo("    <meta name=\"keywords\" content=\"\" />\n");
  echo("    <meta name=\"owner\" content=\"\" />\n");
  echo("    <meta name=\"copyright\" content=\"\" />\n");
  echo("    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n");
  echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo("    <script type=\"text/javascript\">\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n");

  echo("      function startList() {\n");
  echo("        if (document.all && document.getElementById) {\n");
  echo("          nodes = document.getElementsByTagName(\"span\");\n");
  echo("          for (i=0; i < nodes.length; i++) {\n");
  echo("            node = nodes[i];\n");
  echo("            node.onmouseover = function() {\n");
  echo("              this.className += \"Hover\";\n");
  echo("            }\n");
  echo("            node.onmouseout = function() {\n");
  echo("              this.className = this.className.replace(\"Hover\", \"\");\n");
  echo("            }\n");
  echo("          }\n");
  echo("          nodes = document.getElementsByTagName(\"li\");\n");
  echo("          for (i=0; i < nodes.length; i++) {\n");
  echo("            node = nodes[i];\n");
  echo("            node.onmouseover = function() {\n");
  echo("              this.className += \"Hover\";\n");
  echo("            }\n");
  echo("            node.onmouseout = function() {\n");
  echo("              this.className = this.className.replace(\"Hover\", \"\");\n");
  echo("            }\n");
  echo("          }\n");
  echo("        }\n");
  echo("      }\n\n");

  if (isset($SalvarEmArquivo))
  {
    echo("<style>\n");
    include("../teleduc.css");
    include("avaliacoes.css");
    echo("</style>\n");
  }
  else
  {
    echo("  function SalvarVerAvaliacao()\n");
    echo("  {\n");
    echo("    document.frmSalvar.action = 'salvar_ver_avaliacao.php'; \n");
    echo("    document.frmSalvar.submit();\n");
    echo("  }\n");

  }

  echo("  function ImprimirRelatorio()\n");
  echo("  {\n");
  echo("    if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
  echo("    {\n");
  echo("      self.print();\n");
  echo("    }\n");
  echo("    else\n");
  echo("    {\n");
    // 51 (gen)- Infelizmente n�o foi poss�vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir.
  echo("      alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
  echo("    }\n");
  echo("  }\n");

  echo("</script>\n");
  echo("  </head>\n");

  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");

  /* Verificação se a avaliacao está em Edição */
  /* Se estiver, voltar a tela anterior, e disparar a tela de Em Edição... */
  $linha=RetornaStatusAvaliacao($sock, "Avaliacao", $cod_avaliacao);

  if ($linha['status']=="E")
  {
    if (($linha['data']<(time()-1800)) || ($cod_usuario == $linha['cod_usuario'])){
      CancelaEdicaoAvaliacao($sock, "Avaliacao", $cod_avaliacao,$cod_usuario);
    }else{
      /* Está em edição... */
      echo("          <script language=javascript>\n");
      echo("            window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&origem=ver','EmEdicao','width=400,height=250,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
      echo("            window.location='avaliacoes.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=22&cod_avaliacao=".$cod_avaliacao."&tela_avaliacao=".$tela_avaliacao."&operacao=".$cod_operacao."';\n");
      echo("          </script>\n");
      echo("        </td>\n");
      echo("      </tr>\n");
      echo("    </table>\n");
      echo("  </body>\n");
      echo("</html>\n");
      exit();
    }
  }

  echo("<br><br>\n");

  // P�gina Principal
  // 120 - Ver Avalia��o
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases, 120)."</h4>\n");

  echo("          <div id=\"mudarFonte\">\n");
  echo("	    <a href=\"#\" onClick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	    <a href=\"#\" onClick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	    <a href=\"#\" onClick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("          </div>\n");
  //<!----------------- Cabe�alho Acaba Aqui ----------------->

  echo("<br>\n");

  //<!----------------- Tabelao ----------------->
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("            <tr>\n");

  //<!----------------- Botoes de Acao ----------------->
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  // 13 - Fechar (ger)
  echo("                  <li><span onclick=\"onClick=self.close();\">".RetornaFraseDaLista($lista_frases_geral, 13)."</span></li>\n");
  echo("    <form name=frmSalvar>\n");
  echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("      <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
  // G 50 - Salvar em Arquivo
  echo("                  <li><span onClick='SalvarVerAvaliacao();'>".RetornaFraseDaLista($lista_frases_geral, 50)."</span></li>\n");
  // G 14 - Imprimir
  echo("                  <li><span onClick='ImprimirRelatorio();'>".RetornaFraseDaLista($lista_frases_geral, 14)."</span></li>\n");
  echo("    </form>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");

  echo("    <form name=frmAvaliacao method=post>\n");
  echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  // Passa o cod_avaliacao para executar a��es sobre ela.
  echo("      <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
  // $tela_avaliacao eh a variavel que indica se esta tela deve mostrar avaliacoes 'P'assadas, 'A'tuais ou 'F'uturas
  echo("      <input type=hidden name=tela_avaliacao value=".$tela_avaliacao.">\n");
  echo("      <input type=hidden name=origem value=ver>\n");
  echo("      <input type=hidden name=action value=null>\n"); 
  echo("    </form>\n");

  $tipo = "";
  // Soh existe o conceito de tipo de avaliacao (individual ou em grupo) se for a avaliacao de uma atividade no portfolio ou em exerc�cios
  if ($dados_avaliacao['Ferramenta'] == 'P')
  {
    $existe_tipo = true;
    // 14 - Atividade no Portf�lio
    $ferramenta = RetornaFraseDaLista($lista_frases,14);
    if ($dados_avaliacao['Tipo'] == 'I')
      // 161 - Atividade individual no portfolio
      $tipo = RetornaFraseDaLista($lista_frases, 161);
    elseif ($dados_avaliacao['Tipo'] == 'G')
      // 162 - Atividade em grupo no portfolio
      $tipo = RetornaFraseDaLista($lista_frases, 162);
  }
  else if ($dados_avaliacao['Ferramenta'] == 'E')
  {
    $existe_tipo = true;
    // 173 - Exerc�cio
    $ferramenta = RetornaFraseDaLista($lista_frases,173);
    if ($dados_avaliacao['Tipo'] == 'I')
      // 176 - Exercicio individual 
      $tipo = RetornaFraseDaLista($lista_frases, 176);
    elseif ($dados_avaliacao['Tipo'] == 'G')
      // 174 - Exercicio em grupo
      $tipo = RetornaFraseDaLista($lista_frases, 174);
  }
  else if ($dados_avaliacao['Ferramenta'] == 'F')
    // 145 - F�rum de Discuss�o
    $tipo = RetornaFraseDaLista($lista_frases,145);
  elseif ($dados_avaliacao['Ferramenta'] == 'B')
    // 146 - Sess�o de Bate-Papo
    $tipo = RetornaFraseDaLista($lista_frases,146);
  else if($dados_avaliacao['Ferramenta']=='N')
  {
    if($dados_avaliacao['Tipo']=='I')
      $tipo= RetornaFraseDaLista($lista_frases, 185); 
    else
      $tipo= RetornaFraseDaLista($lista_frases, 186);
  }  

  if ($dados_avaliacao['Objetivos'] == '')
  {
    // 157 - N�o definidos
    $objetivos=RetornaFraseDaLista($lista_frases,157);
  }
  else
    $objetivos=$dados_avaliacao['Objetivos'];

  if ($dados_avaliacao['Criterios'] == '')
  {
    // 157 - N�o definidos
    $criterios=RetornaFraseDaLista($lista_frases,157);
  }
  else
    $criterios=$dados_avaliacao['Criterios'];

  $titulo = RetornaTituloAvaliacao($sock, $dados_avaliacao['Ferramenta'], $dados_avaliacao['Cod_atividade']);
  $valor = FormataNota($dados_avaliacao['Valor']);
  $icone="<img src=../figuras/avaliacao.gif border=0> ";
  $obj = "<span id=\"text_obj\">".AjustaParagrafo($objetivos)."</span>";
  $crt = "<span id=\"text_crt\">".AjustaParagrafo($criterios)."</span>";
  $data_inicio = UnixTime2Data($dados_avaliacao['Data_inicio']);
  $data_fim = UnixTime2Data($dados_avaliacao['Data_termino']);

  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head alLeft\">\n");
  // 123 - T�tulo
  echo("                    <td>".RetornaFraseDaLista($lista_frases, 123)."</td>\n");
  // 113 - Tipo de Avalia��o
  echo("                    <td width=\"15%\" align=\"center\">".RetornaFraseDaLista($lista_frases, 113)."</td>\n");
  // 19 - Valor
  echo("                    <td width=15% align=\"center\">".RetornaFraseDaLista($lista_frases, 19)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td align=left rowspan=\"3\">".$icone.$titulo."</td>\n");
  echo("                    <td align=\"center\">&nbsp;&nbsp;".$tipo."</td>\n");
  echo("                    <td align=\"center\">".$valor."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head\">\n");
  /* 16 - Data de in�cio*/
  echo("                    <td>".RetornaFraseDaLista($lista_frases,16)."</td>\n");
  /* 17 - Data de T�rmino */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,17)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  /* 16 - Data de in�cio*/
  echo("                    <td align=\"center\">&nbsp;&nbsp;".$data_inicio."</td>\n");
  /* 17 - Data de T�rmino */
  echo("                    <td align=\"center\">&nbsp;&nbsp;".$data_fim."</td>\n");
  echo("                  </tr>\n");
  // 75 - Objetivos
  echo("                  <tr class=\"head alLeft\">\n");
  echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,75)."</td></tr>\n");
  echo("                  <tr>\n");
  echo("                    <td class=\"itens\" colspan=\"4\" align=left>".$obj."</td></tr>\n");
  // 23 - Criterios
  echo("                  <tr class=\"head alLeft\">");
  echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,23)."</td></tr>\n");
  echo("                  <tr>\n");
  echo("                    <td class=\"itens\" colspan=\"4\" align=left>".$crt."</td></tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");	
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("  </body>\n");			
  echo("</html>\n");	

  Desconectar($sock);
  exit;

?>
