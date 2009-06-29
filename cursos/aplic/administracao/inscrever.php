<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/inscrever.php

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
  ARQUIVO : cursos/aplic/administracao/inscrever.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta=0;
  $cod_ferramenta_ajuda = $cod_ferramenta;

  switch($tipo_usuario)
  {
    case 'z':
    //convidado
    $cod_pagina_ajuda = 14;
    break;
    case 'A':
    //aluno
    $cod_pagina_ajuda = 7;
    break;
  }

    include("../topo_tela.php");
  
  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);

  //adicionar as acoes possiveis, 1o parametro é
  $feedbackObject->addAction("dadosPreenchidosEmail", 0, 282);
  $feedbackObject->addAction("dadosPreenchidosLogin", 0, 281);

  /*Funcao JavaScript*/
  echo("    <script type=\"text/javascript\">\n\n");
  echo("      var numLogins = 5;");

  echo("\n");
  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("      }\n\n");

  echo("      function verificar()\n");
  echo("      {\n");
  echo("        var nome,email,login;\n");
  echo("        var mail_admin;\n");
  echo("        var regras1 = /(@.*@)|(\.{2,})|(@\.)|(\.@)|(^\.)|(\.$)/;\n");
  echo("        var regras2 = /^[a-zA-Z0-9\_\.\-]+\@[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})$/;\n");
  echo("        nome = 0;\n");
  echo("        email = 0;\n");
  echo("        login = 0;\n");
  echo("          for (var i=0;i<document.formul.elements.length;i++)\n");
  echo("          {\n");
  echo("            e = document.formul.elements[i];\n");
  echo("            if (e.value!='' && e.name=='email[]')\n");
  echo("            {\n");
  echo("              mail_admin=e.value;\n");
  echo("              if (regras1.test(mail_admin) || !regras2.test(mail_admin))\n");
  echo("              {\n");
  echo("                alert('".RetornaFraseDaLista($lista_frases, 157)."');\n");
  echo("                return(false);\n");
  echo("              }\n");
  echo("            }\n");
  echo("            if ((e.value!='') && (e.name=='nome[]'))\n");
  echo("            {\n");
  echo("                nome++;\n");
  echo("            }\n");
  echo("            if ((e.value!='') && (e.name=='email[]'))\n");
  echo("            {\n");
  echo("                email++;\n");
  echo("            }\n");
  echo("            if ((e.value!='') && (e.name=='login[]'))\n");
  echo("            {\n");
  echo("                login++;\n");
  echo("            }\n");
  echo("          }\n");
  echo("          if (((nome>0) && (email>0) && (login>0)) && ((nome==email) && (email==login)))  {\n");
  echo("            if (verifica_logins())\n");
  echo("            {\n");
  echo("              return true;\n");
  echo("            }\n");
  echo("            else {\n");
  // 225 - Existem logins repetidos. Especifique logins diferentes para cada usu�rio.
  echo("              alert('".RetornaFraseDaLista($lista_frases, 225)."');\n");
  echo("              return false;\n");
  echo("            }\n");
  echo("          }\n");
  echo("          else  {\n");
  echo("           alert('".RetornaFraseDaLista($lista_frases, 60)."');\n");
  echo("           return false;\n");
  echo("          }\n");
  echo("      }\n");

  echo("      function verifica_logins()\n");
  echo("      {\n");
  echo("        var todos_logins = new Array(numLogins);\n");
  echo("        var cont = 0;\n");
  echo("        for (var i=0; i<document.formul.elements.length; i++)\n");
  echo("        {\n");
  echo("          e = document.formul.elements[i];\n");
  echo("          if (e.value!='' && e.name=='login[]')\n");
  echo("          {\n");
  echo("            todos_logins[cont] = e.value;\n");
  echo("            cont++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        for (var j=0; j<cont; j++)\n");
  echo("        {\n");
  echo("          for (var k=0; k<cont; k++)\n");
  echo("          {\n");
  echo("            if (j != k)\n");
  echo("            {\n");
  echo("              if (todos_logins[j] == todos_logins[k])\n");
  echo("              {\n");
  echo("                return false;\n");
  echo("              }\n");
  echo("            }\n");
  echo("          }\n");
  echo("        }\n");
  echo("        return true;\n");
  echo("      }\n");

  echo("      function addLogin(){\n");
  echo("        numLogins++;\n");
  echo("        elementoTable=document.getElementById('tabInterna');\n");
  echo("        elementoTrOldAddLogin=document.getElementById('addLogin');\n");
  echo("        elementoTr=document.createElement('tr');\n");
  echo("        elementoTdNum=document.createElement('td');\n");
  echo("        elementoBold=document.createElement('b');\n");
  echo("        elementoBold.innerHTML=numLogins+'.';\n");
  echo("        elementoTdNome=document.createElement('td');\n");
  echo("        elementoTdEmail=document.createElement('td');\n");
  echo("        elementoTdLogin=document.createElement('td');\n");
  echo("        inputNome=document.createElement('input');\n");
  echo("        inputNome.setAttribute(\"type\", \"text\");\n");
  echo("        inputNome.setAttribute(\"size\", \"20\");\n");
  echo("        inputNome.setAttribute(\"name\", \"nome[]\");\n");
  echo("        inputNome.setAttribute(\"class\", \"input\");\n");
  echo("        inputNome.setAttribute(\"maxlength\", \"127\");\n");
  echo("        inputEmail=document.createElement('input');\n");
  echo("        inputEmail.setAttribute(\"type\", \"text\");\n");
  echo("        inputEmail.setAttribute(\"size\", \"30\");\n");
  echo("        inputEmail.setAttribute(\"name\", \"email[]\");\n");
  echo("        inputEmail.setAttribute(\"class\", \"input\");\n");
  echo("        inputEmail.setAttribute(\"maxlength\", \"127\");\n");
  echo("        inputLogin=document.createElement('input');\n");
  echo("        inputLogin.setAttribute(\"type\", \"text\");\n");
  echo("        inputLogin.setAttribute(\"size\", \"10\");\n");
  echo("        inputLogin.setAttribute(\"name\", \"login[]\");\n");
  echo("        inputLogin.setAttribute(\"class\", \"input\");\n");
  echo("        inputLogin.setAttribute(\"maxlength\", \"20\");\n");
  echo("        elementoTdNum.appendChild(elementoBold);\n");
  echo("        elementoTdNome.appendChild(inputNome);\n");
  echo("        elementoTdEmail.appendChild(inputEmail);\n");
  echo("        elementoTdLogin.appendChild(inputLogin);\n");
  echo("        elementoTr.appendChild(elementoTdNum);\n");
  echo("        elementoTr.appendChild(elementoTdNome);\n");
  echo("        elementoTr.appendChild(elementoTdEmail);\n");
  echo("        elementoTr.appendChild(elementoTdLogin);\n");
  echo("        elementoTrOldAddLogin.parentNode.insertBefore(elementoTr, elementoTrOldAddLogin);\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

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
  
  echo("    <form name=\"formul\" action=\"acoes.php\" method=\"post\" onsubmit=\"return(verificar());\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso\" value=".$cod_curso.">\n");
  echo("      <input type=\"hidden\" name=\"cod_ferramenta\" value=".$cod_ferramenta.">\n");
  echo("      <input type=\"hidden\" name=\"tipo_usuario\" value=".$tipo_usuario.">\n");
  echo("      <input type=\"hidden\" name=\"action\" value='inscrever'>\n");

  // P�gina Principal
  /* 1 - Administra��o */
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1)."\n");

  if ($tipo_usuario=="F")
  {
    /* 50 - Inscrever Formadores */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 50)."</h4>";
    $cod_pagina=6;
  }
  else if ($tipo_usuario == 'z')
  {
    // 164 - Inscrever Convidados
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 164)."</h4>";

    // 166 - N� de Convidados:
    $frase_qtde=RetornaFraseDaLista($lista_frases, 166);
    $cod_pagina=14;
  }
  else if ($tipo_usuario == 'V')
  {
    // 164 - Inscrever Visitantes
    $cabecalho .= " - "."[Inscrever Visitantes]"."</h4>";

    // 166 - N� de Visitantes:
    $frase_qtde="N� de Visitantes:";

  }
  else if ($tipo_usuario == 'A')
  {
    /* 51 - Inscrever Alunos */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 51)."</h4>";
    $tipo_usuario="A";
    $cod_pagina=7;
  }
  else
  {
    echo("Arquivo inscrever.php, tipo_usuario invalido, tipo_usuario = [");
    var_dump($tipo_usuario);
    echo("]<br>\n");
    Desconectar($sock);
    die();
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

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (geral)*/
  echo("                  <li><a href=\"administracao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;confirma=0\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("                  <li><a href=\"inscrever_arquivo.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=".$tipo_usuario."\">Cadastrar por Arquivo"./*RetornaFraseDaLista($lista_frases_geral,2).*/"</a></li>\n");
  echo("                  <li><a href=\"inscrever3.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=".$tipo_usuario."\">Usu&aacute;rios j&aacute; cadastrados"./*RetornaFraseDaLista($lista_frases_geral,2).*/"</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table  cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head alLeft\">\n");
  /* 58 - Preencha os dados abaixo para cadastr�-los. */
  echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,58)."</td>\n");
  echo("                  </tr>\n"); 
  echo("                  <tr class=\"head01\">\n");
  echo("                    <td>#</td>\n");
  /* 15 - Nome */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,15)."</b></td>\n");
  /* 52 - E-mail */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,52)."</b></td>\n");
  /* 53 - Login */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,53)."</b></td>\n");
  echo("                  </tr>\n");


  if (!isset($dados_preenchidos_s))
  {
    for ($i=1; $i <= 5 ;$i ++)
    {
      echo("                  <tr>\n");
      echo("                    <td><b>".$i.".</b></td>\n");
      echo("                    <td><input class=\"input\" type=\"text\" name=\"nome[]\" size=\"20\" maxlength=\"127\"></td>\n");
      echo("                    <td><input class=\"input\" type=\"text\" name=\"email[]\" size=\"30\" maxlength=\"127\"></td>\n");
      echo("                    <td><input class=\"input\" type=text name=\"login[]\" size=\"10\" maxlength=\"20\"></td>\n");
      echo("                  </tr>\n");
    }
    echo("                  <tr id=\"addLogin\">\n");
    /*248 - (+) Mais*/
    echo("                    <td colspan=\"4\" align=\"left\"><span class=\"link\" onclick='addLogin();'>".RetornaFraseDaLista($lista_frases,248)."</span></td>\n");
    echo("                  </tr>\n");
  }
  else
  {
    $count=0;
    for($i=0;$i<count($dados_preenchidos_s);$i=$i+3)
    {
      $count++;
      if ($dados_preenchidos_s[$i]!="")
      {
        echo("                  <tr>\n");
        echo("                    <td><b>".$count.".</b></td>\n");
        echo("                    <td><input class=\"input\" type=\"text\" name=\"nome[]\" size=\"20\" maxlength=\"127\" value='".$dados_preenchidos_s[$i]."'></td>\n");
        echo("                    <td><input class=\"input\" type=\"text\" name=\"email[]\" size=\"30\" maxlength=\"127\" value='".$dados_preenchidos_s[$i+1]."'></td>\n");
        echo("                    <td><input class=\"input\" type=\"text\" name=\"login[]\" size=\"10\" maxlength=\"20\" value='".$dados_preenchidos_s[$i+2]."'></td>\n");
        echo("                  </tr>\n");
      } 
    }
  }

  echo("                </table>\n");
  if (isset($dados_preenchidos_s))
    /* 247 - Preencha os Logins e E-mails em branco novamente e com novos valores, pois os anteriormente fornecidos já existem.*/
    echo("                <font color=\"red\">* ".RetornaFraseDaLista($lista_frases,247)."</font><br>");

  /* 54 - Todas as colunas s�o obrigat�rias (nome, e-mail e login). Preencha apenas as linhas necess�rias. */
  echo("                * ".RetornaFraseDaLista($lista_frases,54)."<br>\n");
  /* 59 - Inscrever */
  echo("                <div align=\"right\"><br><input type=\"submit\" class=\"input\" value='".RetornaFraseDaLista($lista_frases,59)."'></div>\n");
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
