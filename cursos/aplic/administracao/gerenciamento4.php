<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/gerenciamento4.php

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
  ARQUIVO : cursos/aplic/administracao/gerenciamento4.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta = 0;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda = 13;

  include("../topo_tela.php");
  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);

  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  // 255 - Erro na operacao
  // 256 - Transformacao ocorrida com sucesso.
  $feedbackObject->addAction("transformar", 256, 255);
  // 257 - Participacao alterada com sucesso
  $feedbackObject->addAction("mudarinteracao", 257, 255);
  // 268 - Inscrição realizada com sucesso
  $feedbackObject->addAction("inscrever_cadastrado", 268, 255);
  $feedbackObject->addAction("inscrever", 268, 255);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,0);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  $ecoordenador = ECoordenador($sock,$cod_curso,$cod_usuario);

  if (!isset($ordem))
  {
    $ordem="nome";
  }


  if ((!isset($pag_atual))or($pag_atual=='')or($pag_atual==0))
    $pag_atual =  1;
  else $pag_atual = min($pag_atual, $total_pag);

  

  /*Funcao JavaScript*/
  echo("    <script type=\"text/javascript\">\n");
  echo("      var pag_atual = ".$pag_atual.";\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
                $feedbackObject->returnFeedback($_GET['acao_fb'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");

  echo("      function MarcaOuDesmarcaTodos()\n");
  echo("      {\n");
  echo("        var e;\n");
  echo("        var CabecalhoMarcado=document.gerenc.check_all.checked;\n");
  echo("        for (var i=0; i < document.gerenc.elements.length;i++)\n");
  echo("        {\n");
  echo("          e = document.gerenc.elements[i];\n");
  echo("          if (e.name=='cod_usu[]')\n");
  echo("          {\n");
  echo("            e.checked=CabecalhoMarcado;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        VerificaCheck();\n");
  echo("      }\n");

  echo("      function DesmarcaCabecalho()\n");
  echo("      {\n");
  echo("        document.gerenc.check_all.checked=false;\n");
  echo("      }\n"); 

  echo("      function VerificaCheck(){\n");
  echo("        var i;\n");
  echo("        var j=0;\n");
  echo("        var cod_itens = document.getElementsByName('cod_usu[]');\n");
  echo("        var Cabecalho = document.getElementById('check_all');\n");
  echo("        array_itens = new Array();\n");
  echo("        for (i=0; i<cod_itens.length; i++){\n");
  echo("          if (cod_itens[i].checked){\n");
  echo("            j++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if ((j)==(cod_itens.length)) Cabecalho.checked=true;\n");
  echo("        else Cabecalho.checked=false;\n");
  echo("        if(j > 0){\n");
  echo("          document.getElementById('mDados_Selec').className=\"menuUp02\";\n");
  echo("          document.getElementById('mDados_Selec').onclick=function(){ Alerta('dados',''); };\n");

  if($ecoordenador){
//   echo("          document.getElementById('mApagar_Selec').className=\"menuUp02\";\n");
//   echo("          document.getElementById('mApagar_Selec').onclick=function(){ Apagar(); };\n");
    echo("          document.getElementById('mFormador_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mFormador_Selec').onclick=function(){ Alerta('formador','transformar'); };\n");
    echo("          document.getElementById('mAluno_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mAluno_Selec').onclick=function(){ Alerta('aluno','transformar'); };\n");
    echo("          document.getElementById('mAltPart_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mAltPart_Selec').onclick=function(){ AlterarInteracao(); };\n");
  }
  echo("          document.getElementById('mEncerrar_Selec').className=\"menuUp02\";\n");
  echo("          document.getElementById('mEncerrar_Selec').onclick=function(){ CancelarConvite(); };\n");
  
  echo("        }else{\n");
  echo("          document.getElementById('mDados_Selec').className=\"menuUp\";\n");
  echo("          document.getElementById('mDados_Selec').onclick=function(){ };\n");
  
  if($ecoordenador){
//   echo("          document.getElementById('mApagar_Selec').className=\"menuUp\";\n");
//   echo("          document.getElementById('mApagar_Selec').onclick=function(){ };\n");
    echo("          document.getElementById('mFormador_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mFormador_Selec').onclick=function(){ };\n");
    echo("          document.getElementById('mAluno_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mAluno_Selec').onclick=function(){ };\n");
    echo("          document.getElementById('mAltPart_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mAltPart_Selec').onclick=function(){ };\n");
  }
  echo("          document.getElementById('mEncerrar_Selec').className=\"menuUp\";\n");
  echo("          document.getElementById('mEncerrar_Selec').onclick=function(){ };\n");
  
  echo("        }\n");
  echo("      }\n\n");

  echo("      function AlertaBoolean()\n");
  echo("      {\n");
  echo("        var cont=false;\n");
  echo("        var e;\n");
  echo("        for (var i=0; i < document.gerenc.elements.length;i++)\n");
  echo("        {\n");
  echo("          e = document.gerenc.elements[i];\n");
  echo("          if (e.checked==true)\n");
  echo("          {\n");
  echo("            cont=true;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if (cont==true)\n");
  echo("        {\n");
  echo("          return true;\n");
  echo("        }\n");
  /* Se n�o houver nada selecionado */
  echo("        else\n");
  echo("        {\n");
  echo("        return false;\n");
  echo("        }\n");
  echo("      }\n");

  echo("      function Dados()\n");
  echo("      {\n");
  echo("        if (AlertaBoolean()==true)\n");
  echo("        {\n");
  echo("          document.gerenc.action='/gerenciamento2.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."';\n");
  echo("          document.gerenc.method='post';\n");
  echo("          document.gerenc.opcao.value='dados';\n");
  echo("          document.gerenc.submit();\n");
  echo("        }\n");
  /* Se n�o houver nada selecionado */
  echo("        else\n");
  echo("        {\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases, 114)."');  \n");
  echo("        }\n");
  echo("      }\n");

  echo("      function AlterarInteracao()\n");
  echo("      {\n");
  echo("        if (AlertaBoolean()==true)\n");
  echo("        {\n");
  echo("          document.gerenc.method='post';\n");
  echo("          document.gerenc.action='acoes.php';\n");
  echo("          document.gerenc.action_js.value='mudarinteracao';\n");
  echo("          document.gerenc.submit();\n");
  echo("        }\n");
  /* Se n�o houver nada selecionado */
  echo("        else\n");
  echo("        {\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases, 114)."');  \n");
  echo("        }\n");
  echo("      }\n");

  echo("      function CancelarConvite()\n");
  echo("      {\n");
  echo("        var nenhuma_selecionada = true;\n");
  echo("        for (var i = 0; i < document.gerenc.elements.length && nenhuma_selecionada; i++)\n");
  echo("        {\n");
  echo("          var e = document.gerenc.elements[i];\n");
  echo("          if (e.name == 'cod_usu[]')\n");
  echo("            if (e.checked)\n");
  echo("              nenhuma_selecionada = false;\n");
  echo("        }\n");
  echo("        if (! nenhuma_selecionada)\n");
  echo("        {\n");
  // 177 - Voc� deseja realmente encerrar o convite destes convidados ?
  echo("          if (confirm ('".RetornaFraseDaLista($lista_frases, 177)."'))\n");
  echo("          {\n");
  echo("            document.gerenc.method='post';\n");
  echo("            document.gerenc.action='gerenciamento2.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."';\n");
  echo("            document.gerenc.opcao.value='encerrarConvite';\n");
  echo("            document.gerenc.submit();    \n");
  echo("          }\n");
  echo("        }\n");
  echo("        else\n");
  echo("        {\n");
  // 114 - Nenhuma pessoa selecionada
  echo("          alert('".RetornaFraseDaLista($lista_frases, 114)."');  \n");
  echo("        }\n");
  echo("      }\n");

  echo("      function Alerta(tipo,action)\n");
  echo("      {\n");
  echo("        var cont=false;\n");
  echo("        var e;\n");
  echo("        for (var i=0;i < document.gerenc.elements.length;i++)\n");
  echo("        {\n");
  echo("          e = document.gerenc.elements[i];\n");
  echo("          if (e.checked==true)\n");
  echo("          {\n");
  echo("            cont=true;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if (cont==true)\n");
  echo("        {\n");
  /* Entra na p�gina do tipo de bot�o clicado */
  echo("          if (action == '')\n");
  echo("            document.gerenc.action='gerenciamento2.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."';\n");
  echo("          else\n");
  echo("            document.gerenc.action='acoes.php';\n");
  echo("          document.gerenc.opcao.value=tipo;\n");
  echo("          document.gerenc.action_ger.value=action;\n");
  echo("          document.gerenc.submit();\n");
  echo("        }\n");
  /* Se n�o houver nada selecionado */
  echo("        else\n");
  echo("        {\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases, 114)."');  \n");
  echo("        return false;\n");
  echo("        }\n");
  echo("      }\n");





//   echo("      function ApagarUsuarios(){\n");
//   echo("        var j=0;\n");
//   echo("        var elementos = document.getElementsByName('cod_usu[]')\n");
//   echo("        elementosSelecionados = new Array();\n");
//   echo("          for(i=0 ; i < elementos.length; i++){\n");
//   echo("            if(elementos[i].checked){\n"); 
//   echo("              elementosSelecionados[j] = elementos[i].value\n");   
//   echo("              j++\n"); 
//   echo("            }\n"); 
//   echo("          }\n");
//   echo("        document.location='acoes.php?origem=gerenciamento.php&acao=".$acao."&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&action2=ApagaUsuario&elementos='+elementosSelecionados+'';\n");
//   echo("      }\n");

  echo("      function SelecionouCheckbox()\n");
  echo("      {\n");
  echo("        var e;");
  echo("        for (var i=0;i<document.gerenc.elements.length;i++)\n");
  echo("        {\n");
  echo("          if (document.gerenc.elements[i].checked==true)\n");
  echo("          {\n");
  echo("            return true;\n");
  echo("          }\n");
  echo("        }\n");
  /* 114 - Nenhuma pessoa selecionada */
  echo("        alert('".RetornaFraseDaLista($lista_frases, 114)."');\n");
  echo("        return false;\n");
  echo("      }\n");

//   echo("      function Apagar()\n");
//   echo("      {\n");
//   echo("        if(SelecionouCheckbox() && confirm('Tem certeza disso ?'))\n");
//   echo("          ApagarUsuarios();\n");
//   echo("      }\n");

  echo("    </script>\n");
 
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

/*Forms*/
  echo("    <form action=\"#\" name=\"gerenc\" method=\"post\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");
  echo("      <input type=\"hidden\" name=\"cod_ferramenta\" value=\"".$cod_ferramenta."\">\n");
  if ($ecoordenador = ECoordenador($sock,$cod_curso,$cod_usuario))
  {
    // mostrando convidados com inscricao ativa no curso
    // indica que � um convidado a trabsformar em aluno ou formador
    echo("      <input type=\"hidden\" name=\"convidado\" value=\"1\">\n");
  }
  // variavel que indica gerenciamento de convidados
  echo("      <input type=\"hidden\" name=\"acao\" value=\"z\">\n");
  echo("      <input type=\"hidden\" name=\"opcao\" value=\"nenhuma\">\n");
  // variavel que indica que veio da pagina gerenciamento4.php
  echo("      <input type=\"hidden\" name=\"origem\" value=\"convidado\">");
  echo("      <input type=\"hidden\" name=\"action_ger\" value=\"nenhuma\">\n");
  echo("      <input type=\"hidden\" name=\"action_js\" value=\"nenhuma\">\n");
  // P�gina Principal
  /* 1 - Administra��o */
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1)."\n");
  // 165 - Gerenciamento de Convidados
  $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 165)."</h4>";
  echo($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/			
  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br><br>\n");

  // 166 - N� de Convidados:
  $frase_qtde=RetornaFraseDaLista($lista_frases, 166);

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  // 23 - Voltar (geral)
  echo("                  <li><a href=\"administracao.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."&amp;confirma=0\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");

  /* C�digo de montagem do conte�do a partir daqui */
  $lista_convidados = RetornaListaConvidados ($sock,$cod_curso, $acao, $ordem);
  $num=count($lista_convidados);

  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head01 alLeft\">\n");
  echo("                    <td colspan=\"3\">");
  echo("                      ".$frase_qtde." ".$num."\n");
  echo("                    </td>\n");
  echo("                    <td colspan=\"2\" align=\"right\">");
  echo("                      ".RetornaFraseDaLista($lista_frases,146)." <select name=\"ordem\" onChange=\"document.location='gerenciamento4.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=".$acao."&amp;ordem='+this[this.selectedIndex].value;\" style=\"margin:5px 0 0 0;\">\n");
  $tmp = ($ordem == "nome" ? " selected" : "");
  // 147 - nome
  echo("                        <option value=\"nome\"".$tmp.">".RetornaFraseDaLista($lista_frases,147)."\n");
  $tmp = ($ordem == "data" ? " selected" : "");
  // 155 - data de inscri��o
  echo("                        <option value=\"data\"".$tmp.">".RetornaFraseDaLista($lista_frases,155)."\n");
  echo("                      </select>\n");
  echo("                    </td>\n");
  echo("                  </tr>");
  echo("                  <tr class=\"head\">\n");
  echo("                    <td width=\"2\"><input type=\"checkbox\" name=\"check_all\" id=\"check_all\" onclick=\"MarcaOuDesmarcaTodos();\"></td>\n");
  // 119 - Nome
  echo("                    <td align=\"left\"><b>".RetornaFraseDaLista($lista_frases,119)."</b></td>\n");
  // 132 - Data de inscri��o
  echo("                    <td align=\"center\" width=\"15%\"><b>".RetornaFraseDaLista($lista_frases,132)."</b></td>\n");
  // 169 - Participa��o no curso
  echo("                    <td align=\"center\" width=\"15%\"><b>".RetornaFraseDaLista($lista_frases, 169)."</b></td>");
  // 79 - Dados
  echo("                    <td align=\"center\" width=\"15%\"><b>".RetornaFraseDaLista($lista_frases,79)."</b></td>\n");
  
  
  if ($num==0)
  {
    echo("                  <tr>\n");
    echo("                    <td colspan=\"5\">\n");
    /* 104 - Nenhuma pessoa registrada. */
    echo("                      ".RetornaFraseDaLista($lista_frases,104)."\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                </table>\n");
  }
  else
  {
    foreach($lista_convidados as $cod_usuario_l => $linha)
    {
      echo("                  <tr>\n");
      echo("                    <td><input type=\"checkbox\" name=\"cod_usu[]\" onclick=\"VerificaCheck();\" value=".$cod_usuario_l."></td>\n");
      echo("                    <td align=\"left\">".$linha['nome']."</td>\n");
      echo("                    <td>".Unixtime2Data($linha['data_inscricao'])."</td>\n");

      if ($linha ['interacao'])
      {
        // convidado com interacao: participa��o no curso habilitada
        // 173 - habilitada
        $participa = RetornaFraseDaLista($lista_frases, 173);
      }
      else
      {
        // convidado sem interacao: participa��o n�o habilitada
        // 174 - n�o habilitada
        $participa = RetornaFraseDaLista($lista_frases, 174);
      }
      echo("                    <td>".$participa."</td>\n");

      /* 79 - Dados */
      echo("                    <td><a href=\"gerenciamento2.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=z&amp;ordem=".$ordem."&amp;opcao=dados&amp;origem=convidado&amp;cod_usu[]=".$cod_usuario_l."\">".RetornaFraseDaLista($lista_frases,79)."</a></td>\n");

      echo("                  </tr>\n");
    }

    echo("                </table>\n");
  }
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul>\n");
  /* 79 - Dados */
  echo("                  <li id=\"mDados_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,79)."</span></li>\n");
  // Apagar:
//   echo("                  <li id=\"mApagar_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases_geral, 1)."</span></li>\n");
  if ($ecoordenador = ECoordenador($sock,$cod_curso,$cod_usuario))
  {
    // 107 - Transformar em Aluno
    echo("                  <li id=\"mAluno_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,107)."</span></li>\n");
      
    // 108 - Transformar em Formador
    echo("                  <li id=\"mFormador_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,108)."</span></li>\n");
      
    // 167 - Alterar Participa��o
    echo("                  <li id=\"mAltPart_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,167)."</span></li>\n");
  }
  // 178 - Encerrar Convite
  echo("                  <li id=\"mEncerrar_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,178)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          </form>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>