<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/sala_base.php

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
  ARQUIVO : cursos/aplic/batepapo/sala_base.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");
  // Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registre os nomes das funï¿½ï¿½es em PHP que vocï¿½ quer chamar atravï¿½s do xajax
  $objAjax->register(XAJAX_FUNCTION,"RetornaListaApelidosOnlineDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
//   include("../topo_tela.php");

//   topo_tela.php faz isso
  $cod_curso = $_GET['cod_curso'];
  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,10);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,10); 

  if (!isset($cod_fala))
    $cod_fala=2;

  $cod_sessao=RetornaSessaoCorrente($sock);

  if($cod_usuario_r!="")
    if((!(UsuarioOnline($sock, $cod_usuario_r)) )){
      $cod_usuario_r = "";
      $apelido_usuario_r="Todos";
    }

  if($enviar=="sim")
  {
    $lista_falae=RetornaFalas($sock);
    foreach($lista_falae as $code=>$falae)
    {
      if ($code==$cod_fala)
      $fala=RetornaFraseDaLista($lista_frases,$falae);
    }
    InsereConversa($sock,$cod_curso,$cod_sessao,$cod_usuario,$cod_usuario_r,$cod_fala,$fala,$mensagem);
  }
  echo("<!DOCTYPE HTML SYSTEM \"http://teleduc.nied.unicamp.br/~teleduc/loose-custom.dtd\">\n");
  echo("<html lang=\"pt\">\n"); 
  echo("  <head>\n");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\">\n");
  echo("    <link href=\"../js-css/dhtmlgoodies_calendar.css\" rel=\"stylesheet\" type=\"text/css\">\n");
  echo("    <script type=\"text/javascript\" src=\"../js-css/jscript.js\"></script>\n");

  echo("    <script type=\"text/javascript\" language=\"javascript\">\n");
  echo("      var timeout;\n");
  echo("      var ScrollDownIniciado = false;\n");
  echo("      var step = 2;\n");
  echo("      var y = 0;\n");
  echo("      var intervalSelect;\n");
  echo("      intervalSelect = setInterval(\"xajax_RetornaListaApelidosOnlineDinamic(".$cod_curso.", ".$cod_sessao.", ".$cod_usuario.")\", 1000);\n");

  echo("      function Iniciar() \n");
  echo("      { \n");
  echo("        startList(); \n");
  echo("      } \n");

  echo("      function CheckCheckBox()\n");
  echo("      {\n");
  echo("        if (document.formBaixo.scrollbox.checked) { ScrollDown(); }\n");
  echo("        else {Stop();ScrollDownIniciado=false; }\n");
  echo("      }\n");

  echo("      function ScrollDown()\n");
  echo("      {\n");
  echo("        if (y < 0) { y = 0; }\n");
  echo("        parent.meio.scrollTo(0,999999);\n");
  echo("        y = y + step;\n");
  echo("        timeout=setTimeout('ScrollDown()',10);\n");
  echo("      }\n");
		

  echo("      function Stop()\n");
  echo("      {\n");
  echo("        clearTimeout(timeout);\n");
  echo("      }\n");
  
  echo("      function AtualizaLista(lista_apelidos)\n");
  echo("      {\n");
  /* 20 - Todos */
  echo("        var valorDefault = '".RetornaFraseDaLista($lista_frases,20)."';\n");
  echo("        var valorAnterior = valorDefault;\n");
  echo("        var select = document.getElementById('select_cod_usuario_r');\n");
  
  // Pega o valor anteriormente selecionado.
  echo("        var apelidoOption = select.options[select.selectedIndex];\n");
  echo("        if (apelidoOption != null)\n");
  echo("          valorAnterior = apelidoOption.value;\n");
  
  // Limpa as opcções do select.
  echo("        select.options.length = 0;\n");
  
  echo("        apelidoOption = document.createElement('option');\n");

  echo("        apelidoOption.innerHTML = valorDefault;\n");
  echo("        select.appendChild(apelidoOption);\n");

  // Coloca os apelidos vindos do ajax.
  echo("        if (typeof(lista_apelidos) == 'object') {\n");
  echo("          for (var cod_usu in lista_apelidos) {\n");
  echo("            if (typeof(lista_apelidos[cod_usu]) == 'string') {\n");
  echo("              apelidoOption = document.createElement('option');\n");
  echo("              apelidoOption.setAttribute('value', cod_usu);\n");
  echo("              apelidoOption.innerHTML = lista_apelidos[cod_usu];\n");
  echo("              select.appendChild(apelidoOption);\n");
  echo("            }\n");
  echo("          }\n");
  echo("        }\n");
  
  echo("        select.value = valorAnterior;\n");
  echo("      }\n");

  echo("      function Valida()\n");
  echo("      {\n");
  echo("        if (document.formBaixo.mensagem.value=='')\n");
  echo("          return false;\n");
  #  echo("  document.formBaixo.submits.disabled = true;\n");
  echo("        return true;\n");
  echo("      }\n");


  if ($scrollbox=="sim")
    echo("  ScrollDown();\n");

  echo("      function VoltaPadroes()\n");
  echo("      {\n");
  echo("        document.formBaixo.cod_fala.selectedIndex = ".($cod_fala-2).";\n");
//   if(isset($cod_usuario_r) && ($cod_usuario_r!=""))
//     echo("        document.formBaixo.cod_usuario_r.selectedIndex = \"".$cod_usuario_r."\";\n");
//   else 
//     echo("        document.formBaixo.cod_usuario_r.selectedIndex = 0;\n");
  echo("      }\n");
  
  echo("      function ConfirmaSair()\n");
  echo("      {\n");
  /* 86 - Tem certeza que deseja sair da sala de batepapo? */
  echo("        if (confirm('".RetornaFraseDaLista($lista_frases,86)."'))\n");
  echo("        {\n");
  echo("          document.location='index_batepapo.php?cod_curso=".$cod_curso."';\n");
  echo("        }\n"); 
  echo("      }\n");

  echo("    </script>\n");

  $objAjax->printJavascript();

  echo("  </head>\n");

  echo("    <body onLoad=\"Iniciar();\">\n");

  echo("      <form name=\"formBaixo\" action=\"sala_base.php?cod_curso=".$cod_curso."\" method=\"post\">\n");
  //echo(RetornaSessionIDInput());
  echo("        <input type=\"hidden\" name=\"cod_curso\" value=".$cod_curso." />\n");

  echo("        <br/>\n");

  /* <!----------------- Tabelao -----------------> */
  echo("        <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("        <tr>\n");
  echo("          <td valign=\"top\">\n");
  echo("            <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("              <tr class=\"head01\">\n");
  echo("                <td>".stripslashes(RetornaApelido($sock,$cod_sessao,$cod_usuario))."</td>\n");

  $lista_fala=RetornaFalas($sock);
  echo("                <td><select class=\"input\" name=\"cod_fala\">\n");

  foreach($lista_fala as $cod=>$fala)
  {
    if ($cod>1)
    {
      $selected="";
      if ($cod==$cod_fala)
        $selected="selected=\"selected\"";
      echo("                <option value=".$cod.">".RetornaFraseDaLista($lista_frases,$fala))."</option>\n";
    }
  }
  echo("                  </select></td>\n");

  echo("                <td width=\"15%\">\n");
   echo("                  <select id=\"select_cod_usuario_r\" class=\"input\" name=\"cod_usuario_r\">\n");
   $selected="";
   if (!isset($cod_usuario_r))
     $selected="selected";
   /* 20 - Todos */
   echo("                    <option value=''>".RetornaFraseDaLista($lista_frases,20)."\n");
 
   $lista_apelidos=RetornaListaApelidosOnline($sock,$cod_sessao);
   if (count($lista_apelidos)>0)
     foreach($lista_apelidos as $cod => $apelido)
     {
       $selected="";
         
       if ($cod_usuario_r==$cod)
         $selected="selected";
       if ($cod!=$cod_usuario) {
         $cod_usuario_r = $cod;
         $apelido_usuario_r = stripslashes($apelido);
         echo("                <option value=".$cod." ".$selected.">".stripslashes($apelido)."</option>\n");
       }
     }
   echo("                </select>\n");

  //Voltar as opï¿½ï¿½es padrï¿½o "fala para" e "Todos"
  echo("          <script  type=\"text/javascript\" language=\"javascript\">\n");
  echo("            VoltaPadroes();\n");
  echo("          </script>\n");

  //echo("         <span id=\"apelido_r\">".(($apelido_usuario_r!="")?$apelido_usuario_r:'Todos')."</span>\n");
  echo("         <input type=\"hidden\" name=\"cod_usuario_r\" id=\"cod_usuario_r\" value=\"".(($cod_usuario_r!="")?$cod_usuario_r:'')."\" />\n");
  echo("         <input type=\"hidden\" name=\"apelido_usuario_r\" id=\"apelido_usuario_r\" value=\"".(($apelido_usuario_r!="")?$apelido_usuario_r:'Todos')."\" /></td>\n");
  echo("        </tr>\n");
  echo("        <tr>\n");
  echo("          <td>\n");
  echo("            <input class=\"input\" type=\"text\"   name=\"mensagem\" autocomplete=\"off\" size=50 />");
  echo("            <input class=\"input\" type=\"submit\" name=\"submits\"  value='".RetornaFraseDaLista($lista_frases_geral,11)."' onclick='return(Valida());' />\n");
  echo("          </td>\n");

  $checked="";
  if ($scrollbox=="sim")
    $checked="checked";
  /* 21 - Rolagem Automï¿½tica */
  echo("          <td colspan=2><input type=\"checkbox\" $checked name=\"scrollbox\" onclick='CheckCheckBox();' value=\"sim\">".RetornaFraseDaLista($lista_frases,21)."</td>\n");

  echo("        </tr>\n");
  // Fim Tabela Interna
  echo("      </table>\n");

  echo("      <div align=\"right\">\n");
  /* 20 - Sair (ger) */
  echo("        <input class=\"input\" type=\"button\" value='".RetornaFraseDaLista($lista_frases_geral,20)."' onclick=ConfirmaSair(); style=\"target=Batepapo\" />\n");
  echo("      </div>\n");

  echo("    </td>\n");
  echo("  </tr>\n");
  // Fim Tabelï¿½o
  echo("</table>\n");
  echo("<input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("<input type=\"hidden\" name=\"enviar\"    value=\"sim\" />\n");
  echo("</form>\n");
  echo("</body>\n");
  echo("</html>\n");
  Desconectar($sock);

?>
