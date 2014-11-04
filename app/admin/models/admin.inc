<?php
/**
 * Classe Model do Modulo admin
 * @author     TelEduc
 * @copyright  20014 TelEduc
 * @license    http://teleduc.org.br/
 */
class Admin {
	
	/**
	 * Este metodo estático retorna um link para ser usado dentro de uma lista, dentro de tags UL. Ou seja, ele deve ser chamado dentro de um UL, pois retorna um <li> com um <a>.
	 * 
	 * @param string $nome          Texto que será usada como texto para o link
	 * @param string $endereco	Caminho relativo Ex.:"\"../extracao/extrair_curso.php\""
	 * @param string $parametros	Recebe parâmetros extras, opcional Ex.: ?id=10&app=10
	 * @return Exibi link em lista baseado nos paramentos
	 * @access public
	 */
	static function PreparaBoldLink($nome, $endereco, $parametros="") {
		echo ("<li><a href=" . $endereco . "" . $parametros . " target=_top>" . $nome . "</a></li>\n");
	}
}