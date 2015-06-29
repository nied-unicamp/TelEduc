<?php

require_once '../../../lib/Conexao.php';
require_once '../../administracao/dao/FerramentaDao.php';

class FerramentaController{

	public function listaFerramentas(){
		$dao = new FerramentaDao();
		return $dao->loadAll();
	}
	
	public function exibeLink($cod_curso,$cod_ferr,$nome_ferramenta,$diretorio,$data,$ultimo_acesso,$style,$cod_ferramenta){
		if ($cod_ferr == $cod_ferramenta)
			$style .= "Selecionada ";
		
		/* Verifica se a ferramenta atual esta na divisa */
		if ($cod_ferr==8 ||$cod_ferr==11 || $cod_ferr==15 || $cod_ferr==17 || $cod_ferr==19 || $cod_ferr==22)
			$style .= "Divisa ";
		
		/* Verifica se ha novidades na ferramenta atual */
		$novidades = ($data>$ultimo_acesso);
		if ($novidades)
			$style .= "Novidade ";
		
		echo("            <li class=\"".$style."\">\n");
		echo("              <div>\n");
		
		//if ($cod_ferr==15)
			//echo("                <a class=\"".$style."\">");
		//else
			echo("                <a class=\"".$style."\" href='../../agenda/view/agenda.php?cod_curso=".$cod_curso."'>");
		
		/* Coloca estrela se houver novidade */
		if ($novidades)
			echo("<img src='../img/estrelinha.gif' border=\"0\" alt=\"\" />");
		
		echo($nome_ferramenta."\n");
		echo("                </a>\n");
		echo("              </div>\n");
		echo("            </li>\n");
	}
}