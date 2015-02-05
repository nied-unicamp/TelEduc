<?php
$diretorio_imgs = '../../../web-content/imgs/';
/* Rodap� */
echo("      <tr>\n");

if(!$SalvarEmArquivo){
	echo("        <td valign=\"bottom\" height=\"80\">\n");
	echo("        </td>\n");
}

echo("        <td valign=\"bottom\" class=\"rodape\">\n");
/*	Para fins de SEO existe um random que alterna o alt e title da imagem do teleduc  */
if(rand(1,2) == 1){
	echo("          <a href=\"http://www.teleduc.org.br\"><img src=\"".$diretorio_imgs."teleduc-EAD.jpg\" alt=\"TelEduc - Ensino &agrave; dist&acirc;ncia\" title=\"TelEduc - Ensino &agrave; dist&acirc;ncia\" border=\"0\" style=\"margin-right:5px;\" /></a>\n");
}
else{
	echo("          <a href=\"http://www.teleduc.org.br\"><img src=\"".$diretorio_imgs."teleduc-EAD.jpg\" alt=\"TelEduc - Educa&ccedil;&atilde;o &agrave; dist&acirc;ncia\" title=\"TelEduc - Educa&ccedil;&atilde;o &agrave; dist&acirc;ncia\" border=\"0\" style=\"margin-right:5px;\" /></a>\n");
}
echo("          <a href=\"http://www.nied.unicamp.br\"><img src=\"".$diretorio_imgs."logoNied.gif\" alt=\"nied\" border=\"0\" style=\"margin-right: 8px; margin-bottom: 6px;\" /></a><a href=\"http://www.ic.unicamp.br\"><img src=\"".$diretorio_imgs."logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" border=\"0\" style=\"margin-right: 6px; margin-bottom: -2px;\" /></a><a href=\"http://www.unicamp.br\" title=\"Unicamp\"><img src=\"".$diretorio_imgs."logoUnicamp.gif\" alt=\"UNICAMP\" style=\"margin-bottom: 2px;\"border=\"0\" /></a>\n");
echo("        </td>\n");
echo("      </tr>\n");
echo("      <tr>\n");

if(!$SalvarEmArquivo){
	echo("        <td valign=\"bottom\">\n");
	echo("        </td>\n");
}

/*
 * 514 (geral) - 2010  - TelEduc - Todos os direitos reservados.
 * 515 (geral) - All rights reserved
 * 516 (geral) - NIED - UNICAMP
 * */

echo("        <td valign=\"bottom\" class=\"rodape\">"._("msg514_-1")." "._("msg515_-1")." - "._("msg516_-1")."</td>\n");
echo("      </tr>\n");
echo("    </table>\n");
?>