<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/inscrever.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
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

    Nied - Núcleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
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

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"SugerirLoginDinamic");
  // Registra funções para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  switch($tipo_usuario)
  {
    case 'F':
      //formador
      $cod_pagina_ajuda = 6;
      break;
    case 'A':
      //aluno
      $cod_pagina_ajuda = 7;
      break;
    case 'Z':
      //colaborador
      $cod_pagina_ajuda = 14;
      break;
    case 'V':
      //visitante
      //$cod_pagina_ajuda = ;
      break;
  }

  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);

  //adicionar as acoes possiveis, 1o parametro é
  $feedbackObject->addAction("dadosPreenchidosLogin", 0, 281);
  $feedbackObject->addAction("erroUsuarioCadastrado", 0, 308);

  /*Funcao JavaScript*/
  echo("    <script type=\"text/javascript\" src=\"../js-css/sorttable.js\"></script>\n");
  echo("    <script type=\"text/javascript\" language=\"javascript\" src='../bibliotecas/dhtmllib.js'></script>\n");
  echo("    <script type=\"text/javascript\" language=\"javascript\" src='../js-css/tablednd.js'></script>\n");
  echo("    <script type=\"text/javascript\">\n\n");


  echo("      var numLogins = 4;");
  echo("      var flagOnDivSugs=0;");

  echo("      var Xpos,Ypos;\n");
  echo("      var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("      var isMinNS6 = ((navigator.userAgent.indexOf(\"Gecko\") != -1) && (isNav));\n");
  echo("      var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");

  echo("      if (isNav){\n");
  echo("        document.captureEvents(Event.MOUSEMOVE);\n");
  echo("      }\n");

  echo("\n");
  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        cod_sugestao= getLayer(\"sugestao\");\n");
  echo("        Popula_campo();\n");
  echo("        startList();\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("      }\n\n");

  echo("function Popula_campo(){\n");
  $j=0; 
  echo("var i=0;");
  echo("var nome=document.getElementsByName('nome[]');\n");
  echo("var email=document.getElementsByName('email[]');\n");
  echo("var login=document.getElementsByName('login[]');\n");
  echo("tabela=document.getElementsByClassName('tabInterna');\n");

  //pega a $_SESSION correspondente ao retorno caso exista algum problema de login logo depois mata a $_SESSION
  //para evitar que os dados permaneçam na memória por muito tempo

  $dados_pree=$_SESSION['array_inscricao'];
  unset($_SESSION['array_inscricao']);

  //percorre a variavel(matriz) correspondentes aos campos retornados, evitando que o usuário tenha que preencher 
  //novamente
  foreach($dados_pree as $cod => $linha){

    $nome=$linha['nome'];
    $email=$linha['email'];
    $login=$linha['login'];
    if($j<=4){
      echo("nome[i].value=\"$nome\";\n");
      echo("email[i].value=\"$email\";\n");
      echo("login[i].value=\"$login\";\n");	
      if($linha['status_login']==1){
        echo("var td_login=document.getElementById('login_'+$j);\n");
        echo("td_login.innerHTML='<span class=\"asterisco\">* </span>'+td_login.innerHTML;\n");
        echo("login[i].value=\"$login\";\n");	
      }
      echo("i++\n");
      $j++;
    }
    else{
      echo("linha=document.createElement('tr');\n");
      echo("td_numlinha=document.createElement('td');\n");
      echo("td_nome=document.createElement('td');\n");
      echo("td_email=document.createElement('td');\n");
      echo("td_login=document.createElement('td');\n");
      echo("td_numlinha=document.createElement('td');\n");
      echo("tr_addlogin=document.getElementById('addLogin');\n");

      echo("td_numlinha.innerHTML=\"<b>$j</b>\";\n");

      echo("nome=document.createElement('input');\n");
      echo("nome.setAttribute(\"name\",\"nome[]\");\n");
      echo("nome.setAttribute(\"type\",\"text\");\n");
      echo("nome.setAttribute(\"size\",\"20\");\n");
      echo("nome.setAttribute(\"maxlength\",\"127\");\n");
      echo("nome.className=\"input\";\n");
      echo("nome.value=\"$nome\";\n");

      echo("email=document.createElement('input');\n");
      echo("email.setAttribute(\"name\",\"email[]\");\n");
      echo("email.setAttribute(\"type\",\"text\");\n");
      echo("email.setAttribute(\"size\",\"30\");\n");
      echo("email.setAttribute(\"maxlength\",\"127\");\n");
      echo("email.className=\"input\";\n");
      echo("email.value=\"$email\";\n");

      echo("login=document.createElement('input');\n");
      echo("login.setAttribute(\"name\",\"login[]\");\n");
      echo("login.setAttribute(\"type\",\"text\");\n");
      echo("login.setAttribute(\"size\",\"10\");\n");
      echo("login.setAttribute(\"maxlength\",\"20\");\n");
      echo("login.className=\"input\";\n");
      echo("login.value=\"$login\";\n");

      echo("td_nome.appendChild(nome);\n");
      echo("td_email.appendChild(email);\n");
      echo("td_login.appendChild(login);\n");

      if($linha['status_login']==1){
        echo("td_login.innerHTML='<span class=\"asterisco\">* </span> <input type=\"text\" name=\"login[]\" class=\"input\" maxlength=\"20\" size=\"10\" value=\"$login\">';\n");	
      }

      echo("linha.appendChild(td_numlinha);\n");
      echo("linha.appendChild(td_email);\n");
      echo("linha.appendChild(td_nome);\n");
      echo("linha.appendChild(td_login);\n");

      echo("tr_addlogin.parentNode.insertBefore(linha,tr_addlogin);\n");

      $j++;

    }
  }
  echo("}\n");

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
  echo("            if ((e.value!='') && (e.name=='email[]'))\n");
  echo("            {\n");
  echo("                email++;\n");
  echo("            }\n");
  echo("            if ((e.value!='') && (e.name=='nome[]'))\n");
  echo("            {\n");
  echo("                nome++;\n");
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
  // 225 - Existem logins repetidos. Especifique logins diferentes para cada usuário.
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

  echo("        email=document.getElementsByName('email[]');\n");

  echo("        elementoTdEmail.innerHTML=\"<input autocomplete='off' class='input' type='text' id='email' name='email[]' size='30' maxlength='127' onkeyup=xajax_SugerirLoginDinamic(this.value,'".RetornaFraseDaLista($lista_frases, 316)."',\"+email.length+\"); onblur=TesteBlur();>;\"\n");

  echo("        inputLogin=document.createElement('input');\n");
  echo("        inputLogin.setAttribute(\"type\", \"text\");\n");
  echo("        inputLogin.setAttribute(\"size\", \"10\");\n");
  echo("        inputLogin.setAttribute(\"name\", \"login[]\");\n");
  echo("        inputLogin.setAttribute(\"class\", \"input\");\n");
  echo("        inputLogin.setAttribute(\"maxlength\", \"20\");\n");
  echo("        elementoTdNum.appendChild(elementoBold);\n");
  echo("        elementoTdNome.appendChild(inputNome);\n");
  echo("        elementoTdLogin.appendChild(inputLogin);\n");
  echo("        elementoTr.appendChild(elementoTdNum);\n");
  echo("        elementoTr.appendChild(elementoTdEmail);\n");
  echo("        elementoTr.appendChild(elementoTdNome);\n");
  echo("        elementoTr.appendChild(elementoTdLogin);\n");
  echo("        elementoTrOldAddLogin.parentNode.insertBefore(elementoTr, elementoTrOldAddLogin);\n");
  echo("      }\n\n");


  echo("function XajaxMostraLayer(pos){\n");
  echo("  MostraLayer(cod_sugestao,pos);\n");
  echo("}\n");

  echo("function XajaxEscondeLayer(){\n");
  echo("  EscondeLayer(cod_sugestao);\n");
  echo("}\n");

  echo("function getY( oElement ){\n");
  echo("   var iReturnValue = 0;\n");
  echo("   while( oElement != null ){\n");
  echo("     iReturnValue += oElement.offsetTop;\n");
  echo("     oElement = oElement.offsetParent;\n");
  echo(" }\n");
  echo("   return iReturnValue;\n");
  echo("}\n");

  echo("function getX( oElement ){\n");
  echo("   var iReturnValue = 0;\n");
  echo("   while( oElement != null ){\n");
  echo("     iReturnValue += oElement.offsetLeft;\n");
  echo("     oElement = oElement.offsetParent;\n");
  echo(" }\n");
  echo("   return iReturnValue;\n");
  echo("}\n");


  echo("      function MostraLayer(cod_layer,pos){\n");
  echo("        EscondeLayer(cod_layer);\n");
  echo("        email=document.getElementsByName('email[]');\n");
  echo("        Xpos=getX(email[pos]);\n");
  echo("        Ypos=getY(email[pos]);\n");	 
  echo("        moveLayerTo(cod_layer,Xpos,Ypos+30);\n");
  echo("        showLayer(cod_layer);\n");
  echo("      }\n\n");

  echo("     function EscondeLayer(cod_layer) {\n");
  echo("        hideLayer(cod_layer);\n");
  echo("     }\n");

  echo("    function TesteBlur()");
  echo("    {\n");
  echo("      if(flagOnDivSugs == 0)\n");
  echo("      {\n");
  echo("	     EscondeLayer(cod_sugestao);\n");
  echo("      }\n");
  echo("    }\n");

  echo("    </script>\n\n");

  $objAjax->printJavascript();

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

  echo("    <form name=\"formul\" action=\"acoes.php\" method=\"post\" onsubmit=\"return(verificar());\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");
  echo("      <input type=\"hidden\" name=\"cod_ferramenta\" value=\"".$cod_ferramenta."\">\n");
  echo("      <input type=\"hidden\" name=\"tipo_usuario\" value=\"".$tipo_usuario."\">\n");
  echo("      <input type=\"hidden\" name=\"action\" value=\"inscrever\">\n");

  // Página Principal
  /* 1 - Administração */
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1)."\n");

  if ($tipo_usuario == 'F')
  {
    /* 50 - Inscrever Formadores */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 50)."</h4>";
    $cod_pagina=6;
  }
  else if ($tipo_usuario == 'Z')
  {
    // 164 - Inscrever Colaboradores
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 164)."</h4>";
    $cod_pagina=14;
  }
  else if ($tipo_usuario == 'V')
  {
    // 182 - Inscrever Visitantes
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 182)."</h4>";
  }
  else if ($tipo_usuario == 'A')
  {
    /* 51 - Inscrever Alunos */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 51)."</h4>";
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
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (geral)*/
  echo("                  <li><a href=\"administracao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;confirma=0\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
    /* 82 - Cadastrar por Arquivo*/

  echo("                  <li><a href=\"inscrever_arquivo.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=".$tipo_usuario."\">".RetornaFraseDaLista($lista_frases_geral,82)."</a></li>\n");
  /* Botão de Gerenciamento de usuário*/
  echo("                  <li><a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&tipo_usuario=".$tipo_usuario."\">\n");
  if ($tipo_usuario == "F")
  {
    /* 87 - Gerenciamento de formadores */
    echo(RetornaFraseDaLista($lista_frases_geral,87));
  }
  else if ($tipo_usuario == 'Z')
  {
    // 85 - Gerenciamento de Colaboradores
    echo(RetornaFraseDaLista($lista_frases_geral,85));
  }
  else if ($tipo_usuario == 'V')
  {
    // 88 - Gerenciamento de Visitantes
    echo(RetornaFraseDaLista($lista_frases_geral,88));
  }
  else if ($tipo_usuario == 'A')
  {
    /* 86 - Gerenciamento de Alunos */
    echo(RetornaFraseDaLista($lista_frases_geral,86));
  }
  echo("</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table  cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head alLeft\">\n");
  /* 58 - Preencha os dados abaixo para cadastrá-los. */
  echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,58)."</td>\n");
  echo("                  </tr>\n"); 
  echo("                  <tr class=\"head01\">\n");
  echo("                    <td>#</td>\n");
  /* 52 - E-mail */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,52)."</b></td>\n");
  /* 15 - Nome */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,15)."</b></td>\n");
  /* 53 - Login */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,53)."</b></td>\n");
  echo("                  </tr>\n");


  if (!isset($dados_preenchidos_s))
  {
    for ($i=0; $i < 5 ;$i ++)
    {
      echo("                  <tr>\n");
      echo("                    <td><b>".$i.".</b></td>\n");
      echo("                    <td><input autocomplete=\"off\" class=\"input\" type=\"text\" id=\"email\" size=\"30\" maxlength=\"127\" onkeyup=\"xajax_SugerirLoginDinamic(this.value,'".RetornaFraseDaLista($lista_frases, 316)."',$i);\" onblur=\"TesteBlur();\" name=\"email[]\" ></td>\n");
      echo("                    <td><input class=\"input\" type=\"text\" id=\"nome\" name=\"nome[]\" size=\"20\" maxlength=\"127\"></td>\n");
      echo("                    <td id=\"login_$i\"><input class=\"input\" type=\"text\" id=\"login\" name=\"login[]\" size=\"10\" maxlength=\"20\"></td>\n");
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
        echo("                    <td><input class=\"input\" type=\"text\" name=\"email[]\" size=\"30\" maxlength=\"127\" value='".$dados_preenchidos_s[$i]."'></td>\n");
        echo("                    <td><input class=\"input\" type=\"text\" name=\"nome[]\" size=\"20\" maxlength=\"127\" value='".$dados_preenchidos_s[$i+1]."'></td>\n");
        echo("                    <td><input class=\"input\" type=\"text\" name=\"login[]\" size=\"10\" maxlength=\"20\" value='".$dados_preenchidos_s[$i+2]."'></td>\n");
        echo("                  </tr>\n");
      } 
    }
  }

  echo("                </table>\n");
  if (isset($dados_preenchidos_s))
    /* 247 - Preencha os Logins e E-mails em branco novamente e com novos valores, pois os anteriormente fornecidos já existem.*/
    echo("                <font color=\"red\">* ".RetornaFraseDaLista($lista_frases,247)."</font><br>");

  /* 54 - Todas as colunas são obrigatórias (nome, e-mail e login). Preencha apenas as linhas necessárias. */
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
  
  /*layers sugestões de usuários*/
  
  echo("  <div id=\"sugestao\" class=\"popup\">\n");
  echo("  <div class=\"posX\"><span onclick=\"EscondeLayer(cod_sugestao);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span>\n");
  echo("     <div id=\"divSugs\" style=\"display:none;background-color:#FFF;position:absolute;border:1pt solid #EEE;padding:5px; margin-top:-22px;\" onmouseover=\"flagOnDivSugs=1;\" onmouseout=\"flagOnDivSugs=0;\" class=\"int_popup ulPopup\">\n");
  echo("     </div>\n");
  echo("  </div>\n");
  echo("  </div>\n");
  
  echo("</html>\n");

  Desconectar($sock);

?>
