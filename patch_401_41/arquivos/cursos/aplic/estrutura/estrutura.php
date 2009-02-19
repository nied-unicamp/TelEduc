<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/estrutura/estrutura.php

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
  ARQUIVO : cursos/aplic/administracao/alterar_cronograma.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("estrutura.inc");


  $paragrafo="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

  $sock=Conectar("");
  $lista_frases_ferramentas=RetornaListaDeFrases($sock,-4);
  Desconectar($sock);

  $cod_ferramenta=17;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");
  $ordem_ferramentas = $tela_ordem_ferramentas;
  $lista_ferramentas = $tela_lista_ferramentas;

  echo("    <script type=\"text/javascript\">\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n"); 

  echo("      function EscondeTr(spanId,inicio,tam)\n");
  echo("      {\n");
  echo("        var i,tag,arrayId,trElement,spanElement;\n");
  echo("        arrayId = spanId.split('_');\n");
  echo("        tag = arrayId[1];\n");
  echo("        for(i=inicio+1;i<inicio+1+tam;i++)\n");
  echo("        {\n");
  echo("          trElement = document.getElementById('tr_'+tag+'_'+i);\n");
  echo("          trElement.style.display = 'none';\n");
  echo("        }\n");
  echo("        spanElement = document.getElementById(spanId);\n");
  echo("        spanElement.innerHTML = '[+]';\n");
  echo("        spanElement.onclick = function(){ MostraTr(spanId,inicio,tam); };\n");
  echo("      }\n\n");

  echo("      function MostraTr(spanId,inicio,tam)\n");
  echo("      {\n");
  echo("        var i,tag,arrayId,trElement,spanElement;\n");
  echo("        arrayId = spanId.split('_');\n");
  echo("        tag = arrayId[1];\n");
  echo("        for(i=inicio+1;i<inicio+1+tam;i++)\n");
  echo("        {\n");
  echo("          trElement = document.getElementById('tr_'+tag+'_'+i);\n");
  echo("          trElement.style.display = '';\n");
  echo("        }\n");
  echo("        spanElement = document.getElementById(spanId);\n");
  echo("        spanElement.innerHTML = '[-]';\n");
  echo("        spanElement.onclick = function(){ EscondeTr(spanId,inicio,tam); };\n");
  echo("      }\n\n");

  echo("    </script>\n");

  include("../menu_principal.php");
  echo("      <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* 1 - Estrutura do Ambiente */
  echo("<h4>".RetornaFraseDaLista($lista_frases,1)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("<span class=\"btsNav\"><a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" alt=\"Voltar\" width=\"48\" height=\"14\" border=\"0\" /></a></span>\n");

  //<!----------------- Tabelao ----------------->
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                <tr class=\"head\">\n");
  /* 2 - Esta p�gina apresenta informa��es gerais sobre o ambiente TelEduc. Suas ferramentas s�o apresentadas e seus prop�sitos de utiliza��o s�o explicitados. */
  echo("                  <td align=left colspan=2>".RetornaFraseDaLista($lista_frases,2)."</td>\n");
  echo("                </tr>\n");

  $i=0;

  /* 3 - Autentica��o de acesso */
  echo("                    <tr class=\"head01\" id=\"tr_aut\">\n");
  echo("                      <td align=left><b>".RetornaFraseDaLista($lista_frases,3)."</b></td>\n");
  echo("                      <td width=5%><span id=\"span_aut\" class=\"link\" onClick=\"MostraTr(this.id,".$i.",1);\">[+]</span></td>\n");
  echo("                    <tr class=\"altColor".($i++)%(2)."\" id=\"tr_aut_".($i)."\" style=\"display:none;\">\n");
  echo("                      <td align=left colspan=2>\n");
  /* 4 - O ambiente possui um esquema de autentica��o de acesso aos cursos. Para que formadores e alunos tenham acesso ao curso � necess�ria uma senha e identifica��o pessoal (login) que s�o solicitadas ao participante sempre que ele acessar ao curso. */
  /* 5 - Para garantia da integridade sempre saia do navegador (Netscape Navigator/Microsoft Internet Explorer) ao terminar uma sess�o de acesso. */
  echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,4)."</p>\n");
  echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,5)."</p>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  /* 6 - P�gina de entrada do curso */
  echo("                    <tr class=\"head01\" id=\"tr_pgEntrada\">\n");
  echo("                      <td align=left><b>".RetornaFraseDaLista($lista_frases,6)."</b></td>\n");
  echo("                      <td width=5%><span id=\"span_pgEntrada\" class=\"link\" onClick=\"MostraTr(this.id,".$i.",1);\">[+]</span></td>\n");
  echo("                    <tr class=\"altColor".($i++)%(2)."\" id=\"tr_pgEntrada_".($i)."\" style=\"display:none;\">\n");
  echo("                      <td align=left colspan=2>\n");
  /* 7 - A p�gina de entrada do curso � dividida em duas partes. Na parte esquerda est�o as ferramentas que ser�o utilizadas durante o curso e, na parte direita � apresentado o conte�do correspondente a uma determinada ferramenta selecionada na parte esquerda. */
  /* 8 - Ao entrar no curso, � apresentado o conte�do da ferramenta "Agenda" que cont�m informa��es atualizadas, dicas ou sugest�es dos professores para os alunos. Esta p�gina funciona como um canal de comunica��o direto dos professores com os alunos. Nela s�o colocadas informa��es que seriam fornecidas normalmente no in�cio de uma aula presencial. O conte�do de "Agenda" � atualizado de acordo com a din�mica do curso. */
  /* 9 - Cada curso apoiado pelo ambiente TelEduc pode utilizar um subconjunto das ferramentas descritas abaixo. Assim, pode acontecer de em um determinado momento do curso algumas ferramentas n�o estarem vis�veis no menu � esquerda e, portanto, n�o dispon�veis. Oferecer ou n�o uma ferramenta, em diferentes momentos do curso, faz parte da metodologia adotada por cada formador. Geralmente, se h� a inser��o de uma nova ferramenta, este fato � avisado ao usu�rio por meio da Agenda. */
  echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,7)."</p>\n");
  echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,8)."</p>\n");
  echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,9)."</p>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");

  /* 10 - Ferramentas do ambiente */
  echo("                    <tr class=\"head01\" id=\"tr_ferrAmbiente\">\n");
  echo("                      <td align=left><b>".RetornaFraseDaLista($lista_frases,10)."</b></td>\n");
  echo("                      <td width=5%><span id=\"span_ferrAmbiente\" class=\"link\" onClick=\"MostraTr(this.id,".$i.",18);\">[+]</span></td>\n");
  echo("                    </tr>\n");

  foreach ($ordem_ferramentas as $cod=>$linha)
  {
    if ($linha['cod_ferramenta']>0)
    {
      $cod_ferramenta=$linha['cod_ferramenta'];
      
      echo("                    <tr class=\"altColor".($i++)%(2)."\" id=\"tr_ferrAmbiente_".($i)."\" style=\"display:none;\">\n");
      echo("                      <td style=\"padding:10px 25px 10px 25px;\" align=left colspan=2>\n");
      echo("                        <p style=\"font-weight:bold;\"> ".RetornaFraseDaLista($lista_frases_ferramentas,$lista_ferramentas[$cod_ferramenta]['cod_texto_nome'])."</p>\n");
      echo("                        <p style=\"text-indent:15px;\"> ".RetornaFraseDaLista($lista_frases_ferramentas,$lista_ferramentas[$cod_ferramenta]['cod_texto_descricao'])."</p>\n");
      echo("                      </td>\n");
      echo("                    </tr>\n");
    }
  }
  
  echo("                    <tr class=\"altColor".($i++)%(2)."\" id=\"tr_ferrAmbiente_".($i)."\" style=\"display:none;\">\n");
  echo("                      <td style=\"padding:10px 25px 10px 25px;\" align=left colspan=2>\n");
  echo("                        <p style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_frases_ferramentas,47)."</p>\n");
  echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases_ferramentas,51)."</p>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  
  echo("                    <tr class=\"head01\" id=\"tr_ferrExcForm\">\n");
  echo("                      <td align=left>\n");
  /* 25 - Ferramentas de acesso exclusivo dos formadores e do coordenador do curso */
  echo("                        ".RetornaFraseDaLista($lista_frases,25)."\n");
  echo("                      </td>\n");
  echo("                      <td><span id=\"span_ferrExcForm\" class=\"link\" onClick=\"MostraTr(this.id,".$i.",2);\">[+]</span></td>\n");
  echo("                    </tr>\n");
  
  /*****************/
  /* Administracao */ 
  /* 47 - (Ferramenta Obrigat�ria) */
  echo("                    <tr class=\"altColor".($i++)%(2)."\" id=\"tr_ferrExcForm_".($i)."\" style=\"display:none;\">\n");
  echo("                      <td align=left colspan=2>\n");
  echo("                        <p style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_frases_ferramentas,37)."</p>\n");
  echo("                        <div style=\"margin:15px;\">".RetornaFraseDaLista($lista_frases_ferramentas,38)."</div>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  /***********/
  /* Suporte */ 
  /* 47 - (Ferramenta Obrigat�ria) */
  echo("                    <tr class=\"altColor".($i++)%(2)."\" id=\"tr_ferrExcForm_".($i)."\" style=\"display:none;\">\n");
  echo("                      <td align=left colspan=2>\n");
  echo("                        <p style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_frases_ferramentas,39)."</p>\n");
  echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases_ferramentas,40)."</p>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");

  /* 12 - GNU General Public License*/
  /* 13 - O TelEduc � um software livre; voc� pode redistribu�-lo e/ou modific�-lo sob os termos da*/
  /* 14 - vers�o 2, como publicada pela Free Software Foundation.*/
  /* 15 - Observa��es Finais*/
  /* 16 - O TelEduc � um ambiente em desenvolvimento no*/
  /* 17 - N�cleo de Inform�tica Aplicada � Educa��o*/
  /* 18 - da Universidade Estadual de Campinas */
  echo("                    <tr class=\"head01\" id=\"\">\n");
  echo("                      <td align=left colspan=2>\n");
  echo("                        <b>".RetornaFraseDaLista($lista_frases,12)."</b>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  echo("                    <tr id=\"\">\n");
  echo("                      <td align=left colspan=2>\n");
  echo("                      ".$paragrafo.RetornaFraseDaLista($lista_frases,13)."\n");
  echo("                        <a href=gpl.txt target=GNU-GPL>".RetornaFraseDaLista($lista_frases,12)."</a>\n");
  echo("                      ".RetornaFraseDaLista($lista_frases,14)."\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  echo("                    <tr class=\"head01\" id=\"\">\n");
  echo("                      <td align=left colspan=2>\n");
  echo("                        <b>".RetornaFraseDaLista($lista_frases,15)."</b>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  echo("                    <tr id=\"\">\n");
  echo("                      <td align=left colspan=2>\n");
  echo("                        ".$paragrafo.RetornaFraseDaLista($lista_frases,16)."\n");
  echo("                        <a href=http://www.nied.unicamp.br target=nied>".RetornaFraseDaLista($lista_frases,17)." (NIED)</a>\n");
  echo("                        <a href=http://www.unicamp.br target=unicamp>".RetornaFraseDaLista($lista_frases,18)." (UNICAMP)</a>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");

  echo("              </table>\n");
  echo("            </td>\n");
  echo("          </tr>\n");
  echo("        </table>\n");
  include("../tela2.php");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n"); 
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
