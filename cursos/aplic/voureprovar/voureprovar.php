<?

$bibliotecas="../bibliotecas/";
include($bibliotecas."geral.inc");

$cod_ferramenta = 31;

include("../topo_tela.php");

echo("<script language=\"JavaScript\" type=\"text/javascript\">\n");
echo("function ConvertUnid(){\n");
echo("     cr=document.altera_dados.cr.value;\n");
echo("if(cr>=1){\n");
echo("      cr=cr/10;\n");
echo("      document.altera_dados.cr.value=cr;\n");
echo("    }");
echo("}\n");
echo("function VerificaHora(){\n");
echo("	   hora=document.altera_dados.estudo.value;");
echo("     if(hora<1){\n");
echo("           alert(\"Digite uma quantidade maior que 1\");\n");
echo("			 document.altera_dados.estudo.focus();");
echo("       }\n");
echo("  }\n");
echo("</script>");  

// inicio Java script //
include("../menu_principal.php");



echo("		<td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
echo("		<h4> ".RetornaFraseDaLista($lista_frases_menu,60). "</h4>\n");

// 3 A's - Muda o Tamanho da fonte
echo("		<div id=\"mudarFonte\">\n");
echo("		<a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
echo("		<a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
echo("		<a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
echo("		</div>\n");

/*Voltar*/

echo("		<span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

/*Definindo a tabela externa*/

echo("		<table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("			<tr>\n");
echo("				<td>\n");

/*Definindo a tabela interna*/

echo("					<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("						<tr>\n");
echo("							<td>\n");

/*Formulário referente a criação de uma ferramenta para exemplo, a criação esta sendo feita dentro da tabela interna*/

echo("								<form action =\"acoes.php\" method=\"post\" name=\"altera_dados\" onSubmit='return(confirma());'>\n");
echo(  		                        RetornaFraseDaLista($lista_frases,63));
echo("								<input onBlur=\"VerificaHora();\" type=\"text\" name=\"estudo\" class=\"input\">\n");
echo(  		                        RetornaFraseDaLista($lista_frases,62));
echo("								<input onBlur=\"ConvertUnid();\" type=\"text\" name=\"cr\" class=\"input\">\n");
echo("								<input type=\"hidden\" name=\"acao\" value=\"Voureprovar\">");
echo("								<input type=\"hidden\" name=\"cod_curso\" value=".$cod_curso.">");
echo("								<input type=\"submit\" name=\"envia\" value=\"Enviar\" class=\"input\">\n");

/*Finalizando as tabelas interna e externa respectivamente, considerando a linha e a coluna abertas em ambas tabelas*/

echo("							</td>");
echo("						</tr>");
echo("					</table>");
echo("				</td>");
echo("			</tr>");
echo("		</table>");

/*Fim td conteudo*/

echo("		</td>");
echo("</tr>");

/*a tabela container sera fechado pelo tela2.php*/

include("../tela2.php");



?>
