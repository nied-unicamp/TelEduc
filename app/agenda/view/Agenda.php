<?php


include '../controller/AgendaController.php';

//Adciona o topo tela que contém referencias aos css
include '../../../static_includes/topo_tela.php';


$controle = new AgendaController();


//Imprime o conteúdo da ferrameta
echo("<html>" );
echo("<head>" );


echo("</head>" );

echo("<body>" );


echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <ul class=\"btAuxTabs\">\n");

// if($usr_formador)
// {
/* 6 - Nova Agenda*/
echo("                  <li><span OnClick='NovaAgenda();'>"."Agenda NOva"."</span></li>");

// 	/* 3 - Editar Agenda*/
// 	echo("                  <li><a href=\"ver_editar.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."\">".RetornaFraseDaLista($lista_frases, 3)."</a></li>\n");
// // }
// /* 2- Agenda Anteriores*/
// echo("                  <li><a href=\"ver_anteriores.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_usuario=".$cod_usuario."\">".RetornaFraseDaLista($lista_frases, 2)."</a></li>\n");


echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
/* Tabela Interna */
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
/*18 - Titulo */
echo("                    <td class=\"alLeft\">"."Alguma Coisa"."</td>\n");
echo("                  </tr>\n");
echo("</html>" );

/* Conteudo */