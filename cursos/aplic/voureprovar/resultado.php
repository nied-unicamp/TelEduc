<?

$bibliotecas="../bibliotecas/";
include($bibliotecas."geral.inc");

$cod_ferramenta = 31;
$resultado=$_GET['result'];
$cod_curso=$_GET['cod_curso'];

include("../topo_tela.php");
include("../menu_principal.php");

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
echo("	      <h4> Teste </h4>\n");

// 3 A's - Muda o Tamanho da fonte
echo("      <div id=\"mudarFonte\">\n");
echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
echo("      </div>\n");

/*Voltar*/
echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td>\n");

/* Tabela Interna */
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

echo("            		<tr>\n");
echo("              		<td>\n");
/*Resultado*/
echo(RetornaFraseDaLista($lista_frases,$resultado));


echo("						</td></tr></table>");
echo("						</td></tr></table>");
echo("						</td></tr>");

include("../tela2.php");



?>
