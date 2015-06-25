<?php

$dir_static = '../../../static_includes/';
$ctrl_agenda = '../controller/';

include $ctrl_agenda.'AgendaController.php';

//Adciona o topo tela que contém referencias aos css
include $dir_static.'topo_tela.php';

echo("    <script type=\"text/javascript\" src=\"../../../js/agenda.js\"></script>\n");
echo("    <script type=\"text/javascript\" src=\"../../../js/dhtmllib.js\"></script>\n");
echo("    <script type=\"text/javascript\" src=\"../../../js/jscript.js\"></script>\n");

include $dir_static.'menu_principal.php';

$usr_formador = true;

$controlerAgenda = new AgendaController();

//Imprime o conteúdo da ferrameta

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <ul class=\"btAuxTabs\">\n");

//if($usr_formador)
 // {
    /* 6 - Nova Agenda*/
    echo("                  <li><span OnClick='NovaAgenda();'>Nova Agenda</span></li>");
     /* 3 - Editar Agenda*/
    //echo("                  <li><a href=\"ver_editar.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."\">".RetornaFraseDaLista($lista_frases, 3)."</a></li>\n");
    //echo("                  <li>retornafrasedalista($lista_frases, 3)."</a></li>\n");
 // }
  /* 2- Agenda Anteriores*/
  //echo("                  <li><a href=\"ver_anteriores.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_usuario=".$cod_usuario."\">".RetornaFraseDaLista($lista_frases, 2)."</a></li>\n");
//  }
echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
/* Tabela Interna */
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
/*18 - Titulo */
echo("                    <td class=\"alLeft\">"."Título"."</td>\n");
echo("                  </tr>\n");

/* Novo Item */
echo("    <div id=\"layer_nova_agenda\" class=\"popup\">\n");
echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_nova_agenda);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
echo("      <div class=\"int_popup\">\n");
echo("        <form name=\"form_nova_agenda\" method=\"post\" action=\"acoes_linha.php\" onSubmit=\"return(VerificaNovoTitulo(document.form_nova_agenda.novo_titulo, 1));\">\n");
//echo("        ".RetornaSessionIDInput());
echo("          <div class=\"ulPopup\">\n");
/* 18 - Titulo: */
echo("            Titulo<br />\n");
echo("            <input class=\"input\" type=\"text\" name=\"novo_titulo\" id=\"nome\" value=\"\" maxlength=\"150\" /><br />\n");
//echo("            <input type=\"hidden\" name=\"cod_curso\"   value=\"".$cod_curso."\" />\n");
echo("            <input type=\"hidden\" name=\"acao\"        value=\"criarAgenda\" />\n");
//echo("            <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\" />\n");
echo("            <input type=\"hidden\" name=\"origem\"      value=\"ver_editar\" />\n");
/* 18 - Ok (gen) */

echo("            <input type=\"submit\" id=\"ok_novoitem\" class=\"input\" value=\"Ok\" />\n");

/* 2 - Cancelar (gen) */
echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_nova_agenda);\" value=\"Cancelar\" />\n");
echo("         </div>\n");
echo("        </form>\n");
echo("      </div>\n");
echo("    </div>\n\n");
echo("</html>" );

/* Conteudo */