<? 
/* 
<!--  
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/configurar/inscrever.php

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
  ARQUIVO : cursos/aplic/configurar/inscrever.php
  ========================================================== */
 
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("configurar.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");
  
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->registerFunction("MostraGrupoDinamic");
  $objAjax->registerFunction("EditarTituloDinamic");
  $objAjax->registerFunction("DecodificaString");
  $objAjax->registerFunction("MudarConfiguracaoDinamic");
  $objAjax->registerFunction("ExcluirComponentesDinamic");
  $objAjax->registerFunction("VerificaNovoTituloDinamic");
  
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();
 
  $cod_ferramenta=-7;
  $cod_ferramenta_ajuda=-1;
  $cod_pagina_ajuda=5;

  // TELA
  $bibliotecas="../../bibliotecas/";
//   include($bibliotecas."geral.inc");
  include("../menu.inc");

 
  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases_menu=RetornaListaDeFrases($sock,-4);
  
  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);
   
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  $tela_ordem_ferramentas=RetornaOrdemFerramentas($sock);
  $tela_lista_ferramentas=RetornaListaFerramentas($sock);
  $tela_lista_titulos=RetornaListaTitulos($sock, $_SESSION['cod_lingua_s']);
  $tela_email_suporte=RetornaConfiguracao($sock,"adm_email");

//     $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
//    $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');
  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,0);
  MarcaAcesso($sock,$cod_usuario,$cod_ferramenta);

  
   /************************ Funcoes **************************/

//   function ExibeLink($cod_curso,$cod_ferr,$nome_ferramenta,$diretorio,$data,$ultimo_acesso,$convidado,$convidado_passivo,$convidado_ativo,$style,$cod_ferramenta,$cod_usuario)
//   {
//     if ($cod_ferr != $cod_ferramenta)
//       ExibeLinkAux($cod_curso,$cod_ferr,$nome_ferramenta,$diretorio,$data,$ultimo_acesso,$convidado,$convidado_passivo,$convidado_ativo,$style,$cod_usuario);
//     else 
//       ExibeLinkAux2($cod_curso,$cod_ferr,$nome_ferramenta,$diretorio,$data,$ultimo_acesso,$convidado,$convidado_passivo,$convidado_ativo,$cod_usuario);
//       
//   }

//   function ExibeLinkAux($cod_curso,$cod_ferr,$nome_ferramenta,$diretorio,$data,$ultimo_acesso,$convidado,$convidado_passivo,$convidado_ativo,$style,$cod_usuario)
//   {
//     if ($data>$ultimo_acesso)
//       $novidade= "../estrelinha.gif";
//     else
//       $novidade="../estrela1.gif";
//     
//     // Ferramenta 17 - Estrutura de Ambiente
//     if ($cod_ferr==17)
//         echo("            <li class=\"endLine firstLine\">\n");
//         
//     // Ferramenta 8 - Mural
//     // Ferramenta 11 - Correio
//     // Ferramenta 15 - Portif�lio
//     // Ferramenta 19 - Intermap
//     // Ferramenta 22 - Avalia��es
//     else if(! $convidado)  
//     {
//       if ($cod_ferr==8 ||$cod_ferr==11 || $cod_ferr==15 || $cod_ferr==19 || $cod_ferr==22)
//         echo("            <li class=\"endLine\">\n");
//       else 
//         echo("            <li>\n");
//     }
//     else if($convidado_passivo)
//     {
//       if ($cod_ferr==1 ||$cod_ferr==10 || $cod_ferr==13 || $cod_ferr==19)
//         echo("            <li class=\"endLine\">\n");
//       else 
//         echo("            <li>\n");
//     }
//     else if(!$convidado_ativo)
//     {
//       if ($cod_ferr==1)
//         echo("            <li class=\"endLine\">\n");
//       else 
//         echo("            <li>\n");
//     }
// 
//     echo("             <div>\n");
//     echo("               <a href=\"../".$diretorio."/".$diretorio.".php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferr."\" $style >");
//     echo(" <img src='".$novidade."' border=\"0\" alt=\"\" /> <b>".$nome_ferramenta."</b>");
//     echo("               </a>\n");
//     echo("             </div>\n");
//     echo("            </li>\n");
//     
//   }
  
//   function ExibeLinkAux2($cod_curso,$cod_ferr,$nome_ferramenta,$diretorio,$data,$ultimo_acesso,$convidado,$convidado_passivo,$convidado_ativo,$cod_usuario)
// 
//   {
//     if ($data>$ultimo_acesso)
//       $novidade= "../estrelinha.gif";
//     else
//       $novidade="../estrela1.gif";
//       
//     // Ferramenta 17 - Estrutura de Ambiente
//     if ($cod_ferr==17)
//         echo("            <li class=\"endLine firstLine\">\n");
// 
//         
//     // Ferramenta 8 - Mural
//     // Ferramenta 11 - Correio
//     // Ferramenta 15 - Portif�lio
//     // Ferramenta 19 - Intermap
//     // Ferramenta 22 - Avalia��es
//     else if(!$convidado)  
//     {
//       if ($cod_ferr==8 ||$cod_ferr==11 || $cod_ferr==15 || $cod_ferr==19 || $cod_ferr==22)
//         echo("            <li class=\"endLine\">\n");
//       else 
//         echo("            <li>\n");
//     }
//     else if($convidado_passivo)
//     {
//       if ($cod_ferr==1 ||$cod_ferr==10 || $cod_ferr==13 || $cod_ferr==19)
//         echo("            <li class=\"endLine\">\n");
//       else 
//         echo("            <li>\n");
//     }
//     else if($convidado_ativo)
//     {
//       if ($cod_ferr==1)
//         echo("            <li class=\"endLine\">\n");
//       else 
//         echo("            <li>\n");
//     }
//     echo("             <div>\n");
//     echo("               <a style=\"color:#2a6686;\" href=\"../".$diretorio."/".$diretorio.".php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferr."\">");
//     echo("<img src=\"".$novidade."\" border=\"0\" alt=\"\" /> <b>".$nome_ferramenta."</b>\n");
//     echo("               </a>\n");
//     echo("             </div>\n");
//     echo("            </li>\n");
//   }
  
 
//   if (!isset($cod_ferramenta))
//     $cod_ferramenta=1; /* Agenda */

  
  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n");
  echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"pt\">\n"); 
  
  echo("  <head><title>TelEduc - ".$tela_lista_titulos[$cod_ferramenta]."</title>\n");
   
  echo("    <meta name=\"robots\" content=\"follow,index\" />\n");
  echo("    <meta name=\"description\" content=\"\" />\n");
  echo("    <meta name=\"keywords\" content=\"\" />\n");
  echo("    <meta name=\"owner\" content=\"\" />\n");
  echo("    <meta name=\"copyright\" content=\"\" />\n");
  echo("    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo("    <link href=\"../js-css/dhtmlgoodies_calendar.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo("    <script type=\"text/javascript\" src=\"../js-css/dhtmlgoodies_calendar.js\"></script>\n");
  
  echo("  </head>\n");
  
  echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" bgcolor=\"white\" onload=\"Iniciar();\" >\n");
  echo("    <a name=\"topo\"></a>\n");
  echo("    <h1><a href=\"home.htm\"><img src=\"../imgs/logo.gif\" border=\"0\" alt=\"TelEduc . Educa&ccedil;&atilde;o &agrave; Dist&acirc;ncia\" /></a></h1>\n");
  echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"container\">\n");
//   echo("      <tr>\n");
//   echo("        <td></td>\n");
  echo("        <td valign=\"top\">\n");
    
  $tela_curso_ferramentas    = RetornaFerramentasCurso($sock);
  $tela_novidade_ferramentas = RetornaNovidadeFerramentas($sock,$cod_curso,$cod_usuario);
  $tela_marcar_ferramenta    = RetornaFerramentasMarcadas($sock);

  $tela_ultimo_acesso = PenultimoAcesso($sock,$cod_usuario,"");
  $tela_visitante     = EVisitante($sock,$cod_curso,$cod_usuario);

  $tela_formador          = EFormador($sock,$cod_curso,$cod_usuario);
  $tela_formadormesmo     = EFormadorMesmo($sock,$cod_curso,$cod_usuario);

  // booleano, indica se usuario eh convidado
  $tela_convidado         = EConvidado ($sock, $cod_usuario, $cod_curso);
  // especifica que tipo de convidado eh
  $tela_convidado_ativo   = EConvidadoAtivo($sock, $cod_usuario, $cod_curso);
  $tela_convidado_passivo = EConvidadoPassivo($sock, $cod_usuario, $cod_curso);

  // Logo bonitin

  echo("          <h3 style=\"margin: 40px 0px 0px 130px\">".NomeCurso($sock,$cod_curso)."</h3>\n");
  echo("        </td>\n");
  echo("      </tr>\n");

  echo("      <tr>\n");
  //echo("        <td width=\"140\" valign=\"top\">\n");
  //echo("          <!-- Navegacao Principal -->\n");
//   echo("          <ul id=\"nav\">\n");


  // Ferramenta 11 - Correio
  // Ferramenta 12 - Grupos
  // Ferramenta 14 - Di�rio de bordo
  // Ferramenta 15 - Portfolio
  // Ferramenta 22 - Avaliacoes
  // Ferramenta 23 - Execicios

  // Lista das ferramentas a esconder de convidados passivos
  //$tela_array_convidado_passivo = array (11, 12, 14, 15, 22, 23);
  // Lista das ferramentas a esconder de convidados ativos
  //$tela_array_convidado_ativo   = array (12, 14, 22, 23);

   /*
 foreach($tela_ordem_ferramentas as $cod => $linha)
    {
      $tela_cod_ferr=$linha['cod_ferramenta'];
      $tela_nome_ferramenta=RetornaFraseDaLista($lista_frases_menu,$tela_lista_ferramentas[$tela_cod_ferr]['cod_texto_nome']);
      $tela_diretorio=$tela_lista_ferramentas[$tela_cod_ferr]['diretorio'];
      $tela_status=$tela_curso_ferramentas[$tela_cod_ferr]['status'];

  

      $tela_exibir = false;
      if (! $tela_convidado)
      {
        $tela_exibir = true;
      }
      else if ($tela_convidado_ativo)
      {
        // verifica na lista se deve exibir a ferramenta para o convidado ativo
        $tela_exibir = ! in_array ($tela_cod_ferr, $tela_array_convidado_ativo);
      }
      else if ($tela_convidado_passivo)
      {
        $tela_exibir = ! in_array ($tela_cod_ferr, $tela_array_convidado_passivo);
      }

      if ($tela_exibir)
      {
        $tela_data=$tela_novidade_ferramentas[$tela_cod_ferr];
        $tela_style= "style=\"\"";
        if ($tela_marcar_ferramenta[$tela_cod_ferr])
//           $nome_ferramenta.="<img src=\"../figuras/maozinha.gif\" border=\"0\" alt=\"\" />";
             $tela_style= "style=\"color:#d40000\"";
        if ($tela_cod_ferr!= -1 and $tela_status!="D" and ($tela_status!="F" or $tela_formador))
        {
          if (isset($tela_curso_ferramentas[$tela_cod_ferr]))
          {
            ExibeLink($cod_curso,$tela_cod_ferr,$tela_nome_ferramenta,$tela_diretorio,$tela_data,$tela_ultimo_acesso,$tela_convidado,$tela_convidado_passivo,$tela_convidado_ativo,$tela_style,$cod_ferramenta,$cod_usuario);
          }
        }
      }
    }
    */
//   echo("          </ul>\n");
  //echo("        </td>\n");
// fim do TELA

  $sock=Conectar("");
  $lista_escolaridade=RetornaListaEscolaridade($sock);
  Desconectar($sock);
  $sock=Conectar($cod_curso);

  //$eformador=EFormadorMesmo($sock,$cod_curso,$cod_usuario);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

   

  /* 
  ==================
  Funcoes JavaScript
  ==================
  */

  //GeraJSVerificacaoData();
 
  echo("    <script type=\"text/javascript\">\n");
  
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

    
  echo("    function Iniciar()\n");
  echo("    {\n");
  echo("      document.formulario.nome_usuario.focus();\n");
  echo("        startList();\n");
  echo("    }\n\n");

  // Validação do RG:
  echo("    function RGValido(numero){\n");
  echo("      if(numero.replace(/[ ]+/g, \"\").length == 0){\n");
  echo("        return false;\n");
  echo("      }\n");
  echo("      return true;\n");
  echo("    }\n");

  /* *********************************************************************
  Funcao Verificar - JavaScript. Verifica um a um cada campo do formulario
    Entrada: Nenhuma. Funcao espec�fica do formulario desta pagina
    Saida:   Boolean, para controle do onSubmit;
             true, se nao houver erros no formulario, 
             false, se houver.
  */
  echo("    function verificar()\n");
  echo("    {\n");
  echo("      nome_usuario = document.formulario.nome_usuario.value;\n");
  echo("      data = document.formulario.data.value;\n");
  echo("      email = document.formulario.email.value;\n");
  echo("      rg = document.formulario.rg.value;\n");
  echo("      endereco = document.formulario.endereco.value;\n");
  echo("      cidade = document.formulario.cidade.value;\n");
  echo("      estado = document.formulario.estado.value;\n");
  echo("      pais = document.formulario.pais.value;\n");
  echo("      if (nome_usuario == '')\n");
  echo("      {\n");
  /* 50 - O campo */ /* 32 - Nome */ /* 51 - n�o pode ser vazio */
  echo("        alert('".RetornaFraseDaLista($lista_frases,50)." ".RetornaFraseDaLista($lista_frases,32)." ".RetornaFraseDaLista($lista_frases,51).".');\n");
  echo("        document.formulario.nome_usuario.focus();\n");
  echo("        return false;\n");
  echo("      }\n");

  // Verifica se o e-mail é valido
  echo("      var EmailValido = /^[\w!#$%&'*+\/=?^`{|}~-]+(\.[\w!#$%&'*+\/=?^`{|}~-]+)*@(([\w-]+\.)+[A-Za-z]{2,6}|\[\d{1,3}(\.\d{1,3}){3}\])$/;\n");
  echo("      if (!EmailValido.test(email)){\n");
  /* 52 - O e-mail parece estar errado */
  echo("        alert('".RetornaFraseDaLista($lista_frases,52).".');\n");
  echo("        document.formulario.email.focus();\n");
  echo("        return false;\n");
  echo("      }\n");

  echo("      if (!RGValido(rg) || rg == ''){\n");
  /* 69 - O *//* 33 - RG *//* 69 - fornecido não é válido */
  echo("        alert('".RetornaFraseDaLista($lista_frases,69)." ".RetornaFraseDaLista($lista_frases,33)." ".RetornaFraseDaLista($lista_frases,68).".');\n");
  echo("        document.formulario.rg.focus();\n");
  echo("        return false;\n");
  echo("      }\n");

  // Verificação da Data.
  echo("      var ErroData = 0;\n");

  // Formato da data é valido?
  echo("      var DataValida = /^((0[1-9]|[12]\d)\/(0[1-9]|1[0-2])|30\/(0[13-9]|1[0-2])|31\/(0[13578]|1[02]))\/\d{4}$/;\n");
  echo("      if (!DataValida.test(data)){\n");
  echo("        ErroData = 1;\n");
  echo("      }\n");
  echo("      var data = data.split(\"/\");\n");
  echo("      var Hoje = new Date();\n");

  // Data no futuro?
  echo("      if (Hoje.getFullYear() < data[2]){\n");
  echo("        ErroData = 1;\n");
  echo("      } else if ((Hoje.getFullYear() == data[2]) && (Hoje.getMonth() <= data[1])){\n");
  echo("        ErroData = 1;\n");
  echo("      }\n");
  // 1 = Data Invalida
  // 2 = Data Nascimento no Futuro
  echo("      if (ErroData == 1){\n");
  echo("        alert('".RetornaFraseDaLista($lista_frases,34)." ".RetornaFraseDaLista($lista_frases,67).".');\n");
  echo("        document.formulario.data.focus();\n");
  echo("        return false;\n");
  echo("      }");


  echo("      if (endereco == '')\n");
  echo("      {\n");
  /* 50 - O campo */ /* 40 - Endere�o */ /* 51 - n�o pode ser vazio */
  echo("        alert('".RetornaFraseDaLista($lista_frases,50)." ".RetornaFraseDaLista($lista_frases,40)." ".RetornaFraseDaLista($lista_frases,51).".');\n");
  echo("        document.formulario.endereco.focus();\n");
  echo("        return false;\n");
  echo("      }\n");
  echo("      if (cidade == '')\n");
  echo("      {\n");
  /* 50 - O campo */ /* 41 - Cidade */ /* 51 - n�o pode ser vazio */
  echo("        alert('".RetornaFraseDaLista($lista_frases,50)." ".RetornaFraseDaLista($lista_frases,41)." ".RetornaFraseDaLista($lista_frases,51).".');\n");
  echo("        document.formulario.cidade.focus();\n");
  echo("        return false;\n");
  echo("      }\n");
  echo("      if (estado == '')\n");
  echo("      {\n");
  /* 50 - O campo */ /* 42 - Estado */ /* 51 - n�o pode ser vazio */
  echo("        alert('".RetornaFraseDaLista($lista_frases,50)." ".RetornaFraseDaLista($lista_frases,42)." ".RetornaFraseDaLista($lista_frases,51).".');\n");
  echo("        document.formulario.estado.focus();\n");
  echo("        return false;\n");
  echo("      }\n");
  echo("      if (pais == '')\n");
  echo("      {\n");
  /* 50 - O campo */ /* 43 - Pa�s */ /* 51 - n�o pode ser vazio */
  echo("        alert('".RetornaFraseDaLista($lista_frases,50)." ".RetornaFraseDaLista($lista_frases,43)." ".RetornaFraseDaLista($lista_frases,51).".');\n");
  echo("        document.formulario.pais.focus();\n");
  echo("        return false;\n");
  echo("      }\n");
  echo("      return true;\n");
  echo("    }\n");

  echo("    </script> \n");
  
  $objAjax->printJavascript("../xajax_0.2.4/");
  
  
  /* 1 - Configurar */ /* 27 - Alterar dados pessoais*/
  echo("          <h4 style=\"margin-left: 130px;\">".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,27)."\n");
  echo("          </h4>\n");
  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");
  
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");

  
   echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 27 - Alterar dados pessoais */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,27)."</td>\n");
  echo("                  </tr>\n");
  
  echo("                  <tr>\n");
  echo("                    <td class=\"alLeft\" style=\"border:none\">\n");
   
  /* 28 - Confira no formul�rio abaixo os seus dados */
  /* 29 - Modifique aqueles que forem necess�rios e pressione o bot�o */
  /* 11 - Enviar */
  /* 31 - para registrar os novos dados digitados */
  
  echo("                      <p>\n");
  
  echo("                         &nbsp;&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases,28).". ".RetornaFraseDaLista($lista_frases,29)." '".RetornaFraseDaLista($lista_frases_geral,11)."' ".RetornaFraseDaLista($lista_frases,31)."\n");
  echo("                      </p>\n");
  
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td align=\"left\" style=\"padding-left:265px;\" >\n");
  $linha=FichaUsuario($sock,$cod_usuario);
 
  $nome=$linha['nome'];
  $rg=$linha['rg'];
  $endereco=$linha['endereco'];
  $cidade=$linha['cidade'];
  $estado=$linha['estado'];
  $pais=$linha['pais'];
  $telefone=$linha['telefone'];
  $email=$linha['email'];
  $data_nasc=UnixTime2Data($linha['data_nasc']); 
  $sexo=$linha['sexo'];

  if ($sexo=="") $sexo='M';

  $local_trab=$linha['local_trab'];
  $profissao=$linha['profissao'];
  $informacoes=$linha['informacoes'];
  $escolaridade=$linha['cod_escolaridade'];

  echo("                      <form name=\"formulario\" action=\"inscrever2.php?\"".RetornaSessionID()." method=\"post\" onsubmit=\"return(verificar());\">\n");
  echo("                        <table>\n");
  echo("                          <tr>\n");
  /* 32 - Nome */
  echo("                            <td style=\"border:none; text-align:right;\">\n");

  echo("                              &nbsp;".RetornaFraseDaLista($lista_frases,32)." (*):\n");
  echo("                            </td>\n");
  echo("                            <td width=\"90%\" style=\"border:none\">\n");

  echo("                              <input class=\"input\" type=\"text\" size=\"30\" maxlength=\"128\" name=\"nome_usuario\" value='".$nome."' />\n");
  
  echo("                            </td>\n");
  echo("                          </tr>\n");


    echo("                          <tr>\n");
    /* 33 - RG */
    echo("                            <td style=\"border:none; text-align:right;\">\n");
    echo("                              &nbsp;".RetornaFraseDaLista($lista_frases,33)." (*):\n");
    echo("                            </td>\n");
    echo("                            <td style=\"border:none\">\n");
    echo("                              <input class=\"input\" type=\"text\" size=\"11\" maxlength=\"11\" name=\"rg\" value='".$rg."' />\n");
    echo("                            </td>\n");
    echo("                          </tr>\n");
  

  echo("                          <tr>\n");
  echo("                            <td style=\"border:none; text-align:right;\">\n");
  /* 34 - Data de nascimento */
  echo("                              &nbsp;".RetornaFraseDaLista($lista_frases,34).":\n");
  echo("                            </td>\n");
  echo("                            <td style=\"border:none\">\n");
  //echo("                              ".GeraCampoData("data", $data_nasc)."\n");
  echo("                              <input class=\"input\" type=\"text\" size=\"10\" maxlength=\"10\" id=\"data\" name=\"data_nascimento\" value=\"".$data_nasc."\" /><img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById ('data'),'dd/mm/yyyy',this);\" />");
  echo("                            </td>\n");
  echo("                          </tr>\n");
  echo("                          <tr>\n");
  echo("                            <td style=\"border:none; text-align:right;\">\n");
  /* 35 - Sexo */
  echo("                              &nbsp;".RetornaFraseDaLista($lista_frases,35).":\n");
  echo("                            </td>\n");
  if ($sexo=="M")
    $chM="checked=\"checked\"";
  else
    $chM="";
  if ($sexo=="F")
    $chF="checked=\"checked\"";
  else
    $chF="";
  echo("                            <td style=\"border:none\">\n");
  /* 36 - Masculino */ 
  echo("                              <input type=\"radio\" ".$chM." name=\"sexo\" value=\"M\" />".RetornaFraseDaLista($lista_frases,36)."\n");
  /* 37 - Feminino */
  echo("                              <input type=\"radio\" ".$chF." name=\"sexo\" value=\"F\" />".RetornaFraseDaLista($lista_frases,37)."\n");
  echo("                            </td>\n");
  echo("                          </tr>\n");
  echo("                          <tr>\n");
  echo("                            <td style=\"border:none; text-align:right;\">\n");
  /* 38 - Email */
  echo("                              &nbsp;".RetornaFraseDaLista($lista_frases,38)." (*):\n");
  echo("                            </td>\n");
  echo("                            <td style=\"border:none\">\n");
  echo("                              <input class=\"input\" type=\"text\" size=\"30\" maxlength=\"48\" name=\"email\" value='".$email."' />\n");
  echo("                            </td>\n");
  echo("                          </tr>\n");
  echo("                          <tr>\n");
  echo("                            <td style=\"border:none; text-align:right;\">\n");
  /* 39 - Telefone */
  echo("                              &nbsp;".RetornaFraseDaLista($lista_frases,39).":\n");
  echo("                            </td>\n");
  echo("                            <td style=\"border:none\">\n");
  echo("                              <input class=\"input\" type=\"text\" size=\"16\" maxlength=\"25\" name=\"telefone\" value='".$telefone."' />\n");
  echo("                            </td>\n");
  echo("                          </tr>\n");
  echo("                          <tr>\n");
  echo("                            <td style=\"border:none; text-align:right;\">\n");
  /* 40 - Endere�o */
  echo("                              &nbsp;".RetornaFraseDaLista($lista_frases,40)." (*):\n");
  echo("                            </td>\n");
  echo("                            <td style=\"border:none\">\n");
  echo("                              <input class=\"input\" type=\"text\" size=\"30\" maxlength=\"48\" name=\"endereco\" value='".$endereco."' />\n");
  echo("                            </td>\n");
  echo("                          </tr>\n");
  echo("                          <tr>\n");
  echo("                            <td style=\"border:none; text-align:right;\">\n");
  /* 41 - Cidade */
  echo("                              &nbsp;".RetornaFraseDaLista($lista_frases,41)." (*):\n");
  echo("                            </td>\n");
  echo("                            <td style=\"border:none\">\n");
  echo("                              <input class=\"input\" type=\"text\" size=\"20\" maxlength=\"32\" name=\"cidade\" value='".$cidade."' />\n");
  echo("                            </td>\n");
  echo("                          </tr>\n");
  echo("                          <tr>\n");
  echo("                            <td style=\"border:none; text-align:right;\">\n");
  /* 42 - Estado */
  echo("                              &nbsp;".RetornaFraseDaLista($lista_frases,42)." (*):\n");  
  echo("                            </td>\n");
  echo("                            <td style=\"border:none\">\n");
  echo("                              <input class=\"input\" type=\"text\" size=\"2\" maxlength=\"2\" name=\"estado\" value='".$estado."' />\n");
  /* 43 - Pa�s */
  echo("                              &nbsp;&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases,43)." (*):\n");
  echo("                              <input class=\"input\" type=\"text\" size=\"12\" maxlength=\"19\" name=\"pais\" value='".$pais."' />\n");
  echo("                            </td>\n");
  echo("                          </tr>\n");
  echo("                          <tr>\n");
  echo("                            <td style=\"border:none; text-align:right;\">\n");
  /* 44 - Profiss�o */
  echo("                                 &nbsp;".RetornaFraseDaLista($lista_frases,44).":\n");
  echo("                            </td>\n");
  echo("                            <td style=\"border:none\">\n");
  echo("                              <input class=\"input\" type=\"text\" size=\"20\" maxlength=\"32\" name=\"profissao\" value='".$profissao."' />\n");
  echo("                            </td>\n");
  echo("                          </tr>\n");
  echo("                          <tr>\n");
  echo("                            <td width=\"50%\" style=\"border:none; text-align:right;\">\n");
  /* 45 - Local de trabalho */
  echo("                              &nbsp;".RetornaFraseDaLista($lista_frases,45).":\n");
  echo("                            </td>\n");
  echo("                            <td style=\"border:none\">\n");
  echo("                              <input class=\"input\" type=\"text\" size=\"20\" maxlength=\"32\" name=\"local\" value='".$local_trab."' />\n");
  echo("                            </td>\n");
  echo("                          </tr>\n");
  echo("                          <tr>\n");
  echo("                            <td width=\"50%\" style=\"border:none; text-align:right;\">\n");
  /* 46 - Escolaridade */
  echo("                              &nbsp;".RetornaFraseDaLista($lista_frases,46).":\n");
  echo("                            </td>\n");
  
  echo("                            <td style=\"border:none\">\n");
  echo("                              <select class=\"input\" name=\"cod_escolaridade\" size=\"1\">\n");

  foreach ($lista_escolaridade as $cod => $linha)
  { 
    if($escolaridade == $linha['cod_escolaridade'])
      $selecionado="selected=\"selected\"";
    else
      $selecionado="";
  echo("                                <option value='".$linha['cod_escolaridade']."' ".$selecionado.">".RetornaFraseDaLista($lista_frases_geral,$linha['cod_texto_escolaridade'])."</option>\n");
  }
  echo("                              </select>\n");

  echo("                            </td>\n");
  echo("                          </tr>\n");
  echo("                          <tr>\n");
  echo("                            <td valign=\"top\" style=\"border:none; text-align:right;\">\n");
  /* 47 - Informa��es adicionais */
  echo("                              &nbsp;".RetornaFraseDaLista($lista_frases,47).":\n");
  echo("                            </td>\n");
  echo("                            <td style=\"border:none\">\n");
  echo("                              <textarea class=\"input\" rows=\"5\" cols=\"30\" name=\"informacoes\">".$informacoes."\n");
  echo("                              </textarea> <br /><br />\n");
  echo("                            </td>\n");
  echo("                          </tr>\n");
  echo("                          <tr>\n");
  echo("                            <td style=\"border:none\"></td>\n");
  echo("                            <td style=\"border:none\">\n");
  /* 66 - (*) Campos Obrigat�rios */
  echo("                              ".RetornaFraseDaLista($lista_frases,66)."\n");
  echo("                              <br /><br />\n");
  echo("                            </td>\n"); 
  echo("                          </tr>\n");  
  echo("                          <tr>\n"); 
  echo("                            <td style=\"border:none\"></td>\n");  
  echo("                            <td style=\"border:none\">\n");
  echo("                              <input type=\"submit\" class=\"link\" value=\"".RetornaFraseDaLista($lista_frases_geral,11)."\" id=\"registar_altd\" />\n");
  echo("                            </td>\n");

  echo("                          </tr>\n");
  echo("                        </table>\n"); 

  echo("                        <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("                        <input type=\"hidden\" name=\"dados\" value=\"".$dados."\" />\n");

  echo("                      </form>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  
  echo("      <script type=\"text/javascript\">\n");
  echo("        Iniciar();\n");
  echo("      </script>\n");
  
  echo("        </td>\n");
  echo("      </tr>\n"); 
  echo("    </table>\n");
  echo("    <table id=\"container\">\n");
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");
  
  Desconectar($sock);
  
?>
