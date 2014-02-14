<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/avaliar_curso2.php

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
  ARQUIVO : administracao/avaliar_curso2.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("../administracao/admin.inc");
  include("avaliarcurso.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("<script type=\"text/javascript\">\n\n");

  /* *********************************************************************
  Funcao Submissao - JavaScript. Transforma a form de Navega��o (anterior, pr�xima)
                                 para submiss�o.
    Entrada: onde - Se a p�gina a ser exibida � a anterior ou a pr�xima
    Saida:   Boolean, para controle do onClick;
             true, se nao houver erros no formulario,
             false, se houver.
  */
  
  echo("  function Submissao(onde)\n");
  echo("  {\n");
  echo("    document.Navega.onde.value=onde;\n");
  echo("    document.Navega.submit();\n");
  echo("    return false;\n");
  echo("  }\n\n");

  echo("  function SubmeteForm(opcao,situacao)\n");
  echo("  {\n");
  echo("	if(opcao=='Aceitar' && situacao=='N'){");
  echo("		if (confirm('".RetornaFraseDaLista($lista_frases,212)."')){\n");
  echo("    		document.avaliacao.opcao.value = opcao;\n");
  echo("    		document.avaliacao.submit();\n");
  echo("		}\n");
  echo("	}\n");
  echo("	else{");
  echo("		document.avaliacao.opcao.value = opcao;\n");
  echo("		document.avaliacao.submit();\n");
  echo("	}\n");
  echo("  }\n\n");
  
  echo("  function Cancela()\n");
  echo("  {\n");
  echo("    document.avaliacao.action = \"avaliar_curso.php\";\n");
  echo("    document.avaliacao.submit();\n");
  echo("  }\n\n");

  echo("  function Iniciar()\n");
  echo("  {\n");
  echo("    startList();\n");
  echo("  }\n");

  echo("</script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

 $lista_frases=RetornaListaDeFrases($sock,-5);

  /*
  ==================
  Programa Principal
  ==================
  */

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 244 - Avaliar requisi��es para abertura de cursos */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,244)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 2 - Cancelar (Ger) */
  echo("                  <li><span stitle=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" onClick=\"Cancela();\">".RetornaFraseDaLista($lista_frases_geral,2)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");

  if (isset($todos))
    $cursos=RetornaTodosCursos($sock);
  else
    $cursos=RetornaCursosRequisicao($sock);

  if(isset($onde))
  {
    if ($onde=='ant')
      $cod--;
    else if ($onde=='prox')
      $cod++;
  }

  $cod_curso=$cursos[$cod]['cod_curso'];
  $curso=RetornaDadosCursoReq($sock,$cod_curso);
  $confirmado=VerificaUsuario($sock,$curso['email_contato']);


  if ($curso['avaliado'] == 'N')
    /* 213 -  (nao-avaliado) */
    $status = RetornaFraseDaLista($lista_frases, 213);
  else if ($curso['avaliado'] == 'R')
    /* 212 -  (rejeitado) */
    $status = RetornaFraseDaLista($lista_frases, 212);

  echo("                <form name=\"avaliacao\" method=\"post\" action=\"avaliar_curso3.php\">\n");
  echo("                  <input type=\"hidden\" name=\"opcao\"     value=\"nenhuma\" />\n");
  echo("                  <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("                  <input type=\"hidden\" name=\"cod\"       value=\"".$cod."\" />\n");
  if(isset($todos))
    echo("                  <input type=\"hidden\" name=\"todos\"     value=\"".$todos."\" />\n");
  echo("                </form>\n");

  echo("                <form name=\"Navega\" method=\"post\" action=\"avaliar_curso2.php\">\n");
  echo("                  <input type=\"hidden\" name=\"onde\"  value=\"\"/>\n");
  echo("                  <input type=\"hidden\" name=\"cod\"   value=\"".$cod."\" />\n");
  if(isset($todos))
    echo("                  <input type=\"hidden\" name=todos     value=\"".$todos."\" />\n");

  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  /* 91 - Dados do Curso */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases, 91)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td align=\"center\">\n");
  echo("                      <table>");
  /* 92 - Nome do Curso: */
  echo("                        <tr>\n");
  echo("                          <td style=\"text-align:right;border:none;\"><b>".RetornaFraseDaLista($lista_frases, 92)."</b></td>\n");
  echo("                          <td style=\"text-align:left;border:none;\">\n");
  echo($curso['nome_curso']."<font color=red>".$status."</td>\n");
  echo("                        </tr>\n");

  /* 217 - Dura��o estimada: */
  echo("                        <tr>\n");
  echo("                          <td style=\"text-align:right;border:none;\"><b>".RetornaFraseDaLista($lista_frases, 217)." </b></td>\n");
  echo("                          <td style=\"text-align:left;border:none;\">\n");
  echo($curso['duracao']."</td>\n");
  echo("                        </tr>\n");

  /* 93 - N�mero de Alunos: */
  echo("                        <tr>\n");
  echo("                          <td style=\"text-align:right;border:none;\"><b>".RetornaFraseDaLista($lista_frases, 93)." </b></td>\n");
  echo("                          <td style=\"text-align:left;border:none;\">\n");
  echo($curso['num_alunos']."</td>\n");
  echo("                        </tr>\n");

  if ($curso['cod_pasta'] != "")
    $categoria=RetornaCategoria($sock,$curso['cod_pasta']);
  else
    $categoria="";

  /* 94 - Categoria: */
  echo("                        <tr>\n");
  echo("                          <td style=\"text-align:right;border:none;\"><b>".RetornaFraseDaLista($lista_frases, 94)." </b></td>\n");
  echo("                          <td style=\"text-align:left;border:none;\">\n");
  echo($categoria."</td>\n");
  echo("                        </tr>\n");

  /* 218 - P�blico alvo: */
  echo("                        <tr>\n");
  echo("                          <td style=\"text-align:right;border:none;\"><b>".RetornaFraseDaLista($lista_frases, 218)." </b></td>\n");
  echo("                          <td style=\"text-align:left;border:none;\">\n");
  echo($curso['publico_alvo']."</td>\n");
  echo("                        </tr>\n");

  /* 219 - Forma de inscri��o: */
  echo("                        <tr>\n");
  echo("                          <td style=\"text-align:right;border:none;\"><b>".RetornaFraseDaLista($lista_frases, 219)." </b></td>\n");
  echo("                          <td style=\"text-align:left;border:none;\">\n");
  echo($curso['tipo_inscricao']."</td>\n");
  echo("                        </tr>\n");

  /* 220 - Informa��es adicionais: */
  echo("                        <tr>\n");
  echo("                          <td style=\"text-align:right;border:none;\"><b>".RetornaFraseDaLista($lista_frases, 220)." </b></td>\n");
  echo("                          <td style=\"text-align:left;border:none;\">\n");
  echo($curso['informacoes']."</td>\n");
  echo("                        </tr>\n");

  /* 221 - Nome do Contatante: */
  echo("                        <tr>\n");
  echo("                          <td style=\"text-align:right;border:none;\"><b>".RetornaFraseDaLista($lista_frases, 221)." </b></td>\n");
  echo("                          <td style=\"text-align:left;border:none;\">\n");
  echo($curso['nome_contato']."</td>\n");
  echo("                        </tr>\n");

  /* 222 - Nome da Institui��o: */
  echo("                        <tr>\n");
  echo("                          <td style=\"text-align:right;border:none;\"><b>".RetornaFraseDaLista($lista_frases, 222)." </b></td>\n");
  echo("                          <td style=\"text-align:left;border:none;\">\n");
  echo($curso['instituicao']."</td>\n");
  echo("                        </tr>\n");

  /* 223 - E-mail para Contato: */
  echo("                        <tr>\n");
  echo("                          <td style=\"text-align:right;border:none;\"><b>".RetornaFraseDaLista($lista_frases, 223)." </b></td>\n");
  echo("                          <td style=\"text-align:left;border:none;\">\n");
  echo(								$curso['email_contato']);
  if(!$confirmado){
    /*Aten��o-email n�o confirmado pelo usu�rio)*/
    echo("                            <font color=red>".RetornaFrasedaLista($lista_frases,533)."</font>");
  }
  echo("                          </td>\n");
  echo("                        </tr>\n");

  $data_req=UnixTime2DataHora($curso['data']);
  /* 224 - Data de requisi��o: */
  echo("                        <tr>\n");
  echo("                          <td style=\"text-align:right;border:none;\"><b>".RetornaFraseDaLista($lista_frases, 224)." </b></td>\n");
  echo("                          <td style=\"text-align:left;border:none;\">\n");
  echo($data_req."</td>\n");
  echo("                        </tr>\n");

  if(!isset($todos))
    $todos = "";

  $min_cod_curso=RetornaMinCodCurso($sock,$todos);
  $max_cod_curso=RetornaMaxCodCurso($sock,$todos);

  if ($min_cod_curso!=$max_cod_curso)
  {
    echo("                        <tr>\n");

    if ($cod_curso>$min_cod_curso)
    {
      /* 225 - <<Anterior */
      echo("                          <td style=\"text-align:right;border:none;\"><b>\n");
      echo("<a href=# onClick=return(Submissao('ant'));>".LimpaTags(RetornaFraseDaLista($lista_frases, 225))."</a></b></td>\n");
    }
    else
    {
      /* 225 - <<Anterior */
      echo("                          <td style=\"text-align:right;border:none;\">\n");
      echo(LimpaTags(RetornaFraseDaLista($lista_frases, 225))."</td>\n");
    }

    if ($cod_curso<$max_cod_curso)
    {
      /* 226 - Pr�ximo>> */
      echo("                          <td style=\"text-align:left;border:none;\"><b>\n");
      echo("<a class=\"text\" href=\"#\" onClick=return(Submissao('prox'));>".LimpaTags(RetornaFraseDaLista($lista_frases, 226))."</a></b></td>\n");
    }
    else
    {
      /* 226 - Pr�ximo>> */
      echo("                          <td style=\"text-align:left;border:none;\">\n");
      echo(LimpaTags(RetornaFraseDaLista($lista_frases, 226))."</td>\n");
    }

    echo("                        </tr>\n");
  }

  echo("                        <tr>\n");
  echo("                          <td style=\"border:none;\" colspan=\"2\" >&nbsp;</td>\n");
  echo("                        </tr>\n");
  echo("                        <tr>\n");
  echo("                          <td style=\"border:0;text-align:center;\" colspan=\"2\">\n");
  /* 227 - Aceitar */
  if(!$confirmado)
  	echo("                              <input class=\"input\" type=\"button\" value='".RetornaFraseDaLista($lista_frases, 227)."' onClick=SubmeteForm('Aceitar','N')>&nbsp;&nbsp;\n");
  else
	echo("                              <input class=\"input\" type=\"button\" value='".RetornaFraseDaLista($lista_frases, 227)."' onClick=SubmeteForm('Aceitar','S')>&nbsp;&nbsp;\n");
  if ($curso['avaliado'] == 'N')
  {
    /* 228 - Rejeitar */
    echo("                              <input  class=\"input\" type=\"button\" value='".RetornaFraseDaLista($lista_frases, 228)."' onClick=SubmeteForm('Rejeitar','".$confirmado."')>\n");
  }

  echo("                          </td>\n");
  echo("                        </tr>\n");
  echo("                      </table>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
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

  Desconectar($sock);
?>
