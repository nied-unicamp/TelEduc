<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/estrutura/estrutura.php

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
  /* 2 - Esta pï¿½gina apresenta informaï¿½ï¿½es gerais sobre o ambiente TelEduc. Suas ferramentas sï¿½o apresentadas e seus propï¿½sitos de utilizaï¿½ï¿½o sï¿½o explicitados. */
  echo("                  <td align=left colspan=2>".RetornaFraseDaLista($lista_frases,2)."</td>\n");
  echo("                </tr>\n");

  $i=0;

  /* 3 - Autenticaï¿½ï¿½o de acesso */
  echo("                    <tr class=\"head01\" id=\"tr_aut\">\n");
  echo("                      <td align=left><b>".RetornaFraseDaLista($lista_frases,3)."</b></td>\n");
  echo("                      <td width=5%><span id=\"span_aut\" class=\"link\" onClick=\"MostraTr(this.id,".$i.",1);\">[+]</span></td>\n");
  echo("                    <tr class=\"altColor".($i++)%(2)."\" id=\"tr_aut_".($i)."\" style=\"display:none;\">\n");
  echo("                      <td align=left colspan=2>\n");
  /* 4 - O ambiente possui um esquema de autenticaï¿½ï¿½o de acesso aos cursos. Para que formadores e alunos tenham acesso ao curso ï¿½ necessï¿½ria uma senha e identificaï¿½ï¿½o pessoal (login) que sï¿½o solicitadas ao participante sempre que ele acessar ao curso. */
  /* 5 - Para garantia da integridade sempre saia do navegador (Netscape Navigator/Microsoft Internet Explorer) ao terminar uma sessï¿½o de acesso. */
  echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,4)."</p>\n");
  echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,5)."</p>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  /* 6 - Pï¿½gina de entrada do curso */
  echo("                    <tr class=\"head01\" id=\"tr_pgEntrada\">\n");
  echo("                      <td align=left><b>".RetornaFraseDaLista($lista_frases,6)."</b></td>\n");
  echo("                      <td width=5%><span id=\"span_pgEntrada\" class=\"link\" onClick=\"MostraTr(this.id,".$i.",1);\">[+]</span></td>\n");
  echo("                    <tr class=\"altColor".($i++)%(2)."\" id=\"tr_pgEntrada_".($i)."\" style=\"display:none;\">\n");
  echo("                      <td align=left colspan=2>\n");
  /* 7 - A pï¿½gina de entrada do curso ï¿½ dividida em duas partes. Na parte esquerda estï¿½o as ferramentas que serï¿½o utilizadas durante o curso e, na parte direita ï¿½ apresentado o conteï¿½do correspondente a uma determinada ferramenta selecionada na parte esquerda. */
  /* 8 - Ao entrar no curso, ï¿½ apresentado o conteï¿½do da ferramenta "Agenda" que contï¿½m informaï¿½ï¿½es atualizadas, dicas ou sugestï¿½es dos professores para os alunos. Esta pï¿½gina funciona como um canal de comunicaï¿½ï¿½o direto dos professores com os alunos. Nela sï¿½o colocadas informaï¿½ï¿½es que seriam fornecidas normalmente no inï¿½cio de uma aula presencial. O conteï¿½do de "Agenda" ï¿½ atualizado de acordo com a dinï¿½mica do curso. */
  /* 9 - Cada curso apoiado pelo ambiente TelEduc pode utilizar um subconjunto das ferramentas descritas abaixo. Assim, pode acontecer de em um determinado momento do curso algumas ferramentas nï¿½o estarem visï¿½veis no menu ï¿½ esquerda e, portanto, nï¿½o disponï¿½veis. Oferecer ou nï¿½o uma ferramenta, em diferentes momentos do curso, faz parte da metodologia adotada por cada formador. Geralmente, se hï¿½ a inserï¿½ï¿½o de uma nova ferramenta, este fato ï¿½ avisado ao usuï¿½rio por meio da Agenda. */
  echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,7)."</p>\n");
  echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,8)."</p>\n");
  echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,9)."</p>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");

  /* 10 - Ferramentas do ambiente */
  echo("                    <tr class=\"head01\" id=\"tr_ferrAmbiente\">\n");
  echo("                      <td align=left><b>".RetornaFraseDaLista($lista_frases,10)."</b></td>\n");
  echo("                      <td width=5%><span id=\"span_ferrAmbiente\" class=\"link\" onClick=\"MostraTr(this.id,".$i.",23);\">[+]</span></td>\n"); //FIXME: O tamanho (numero de linhas) passado pela chamada da fun‹o MostraTr est‡ sendo passado harcoded
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
  echo("                        <p style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_frases_ferramentas,61)."</p>\n");
  echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases_ferramentas,62)."</p>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");

  //FIXME: A descri‹o ($lista_frases_ferramentas,51) est‡ estranho, n‹o est‡ descrevendo a funcionalidade de Notifica‹o ($lista_frases_ferramentas,47)
  //echo("                    <tr class=\"altColor".($i++)%(2)."\" id=\"tr_ferrAmbiente_".($i)."\" style=\"display:none;\">\n");
  //echo("                      <td style=\"padding:10px 25px 10px 25px;\" align=left colspan=2>\n");
  //echo("                        <p style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_frases_ferramentas,47)."</p>\n");
  //echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases_ferramentas,51)."</p>\n");
  //echo("                      </td>\n");
  //echo("                    </tr>\n");
  
  echo("                    <tr class=\"head01\" id=\"tr_ferrExcForm\">\n");
  echo("                      <td align=left>\n");
  /* 25 - Ferramentas de acesso exclusivo dos formadores e do coordenador do curso */
  echo("                        ".RetornaFraseDaLista($lista_frases,25)."\n");
  echo("                      </td>\n");
  echo("                      <td><span id=\"span_ferrExcForm\" class=\"link\" onClick=\"MostraTr(this.id,".$i.",2);\">[+]</span></td>\n");
  echo("                    </tr>\n");
  
  /*****************/
  /* Administracao */ 
  /* 47 - (Ferramenta Obrigatï¿½ria) */
  echo("                    <tr class=\"altColor".($i++)%(2)."\" id=\"tr_ferrExcForm_".($i)."\" style=\"display:none;\">\n");
  echo("                      <td align=left colspan=2>\n");
  echo("                        <p style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_frases_ferramentas,37)."</p>\n");
  echo("                        <div style=\"margin:15px;\">".RetornaFraseDaLista($lista_frases_ferramentas,38)."</div>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  /***********/
  /* Suporte */ 
  /* 47 - (Ferramenta Obrigatï¿½ria) */
  echo("                    <tr class=\"altColor".($i++)%(2)."\" id=\"tr_ferrExcForm_".($i)."\" style=\"display:none;\">\n");
  echo("                      <td align=left colspan=2>\n");
  echo("                        <p style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_frases_ferramentas,39)."</p>\n");
  echo("                        <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases_ferramentas,40)."</p>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");

  /* 12 - GNU General Public License*/
  /* 13 - O TelEduc ï¿½ um software livre; vocï¿½ pode redistribuï¿½-lo e/ou modificï¿½-lo sob os termos da*/
  /* 14 - versï¿½o 2, como publicada pela Free Software Foundation.*/
  /* 15 - Observaï¿½ï¿½es Finais*/
  /* 16 - O TelEduc ï¿½ um ambiente em desenvolvimento no*/
  /* 17 - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o*/
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
