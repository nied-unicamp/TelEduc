<?php
class Arquivos{
	/* ************************************************************************
	 RetornaArrayDiretorio - Retorna um array com os arquivos do diretorio
	Entrada: $diretorio - diretorio a ser varrido.
	Saida: Array multidimensional com:
	$lista[<num>]['Caminho'] - caminho completo.
	$lista[<num>]['Diretorio'] - Diretorio do arquivo
	$lista[<num>]['Arquivo'] - Nome do arquivo
	$lista[<num>]['Status'] - Condi��o especial (true ou false);
	$lista[<num>]['Tamanho'] - tamanho do arquivo
	$lista[<num>]['Data'] - data da �ltima modifica��o
	*/
	static function RetornaArrayDiretorio($diretorio)
	{
	
		if (Arquivos::ExisteArquivo($diretorio) == false)
			return (array());
	
		$diretorio = rtrim($diretorio, DIRECTORY_SEPARATOR);
	
		// Inicializamos o contador de arquivos encontrados
		$cont = 0;
	
		// Inicializamos a fila
		$dirs = array();
		// Inserimos o primeiro elemento na fila
		array_push($dirs, $diretorio);
	
		// Enquanto houver diret�rios na fila procure
		// pelos arquivos
		while (count($dirs) > 0)
		{
			// Remove o primeiro elemento da fila
			$dir_cur = array_shift($dirs);
	
			// Lista o padr�o procurado
			if (($e1 = glob($dir_cur.DIRECTORY_SEPARATOR.'*')) === false)
				return (array());
	
			if (($e2 = glob($dir_cur.DIRECTORY_SEPARATOR.'.*')) === false)
				return (array());
	
			$e2 = array_filter($e2, "FiltraDiretorioRelativos");
			$encontrados = array_merge($e1, $e2);
	
			if (count($encontrados))
			{
				foreach ($encontrados as $idx => $nome)
				{
					clearstatcache();
					$tipo_arquivo = filetype($nome);
	
					if ($tipo_arquivo == 'dir')
					{
						$lista[$cont]['Caminho'] = $nome;
						$lista[$cont]['Diretorio'] = trim(substr($nome, strlen($diretorio) + 1), DIRECTORY_SEPARATOR);
						$lista[$cont]['Arquivo'] = "";
						$lista[$cont]['Tamanho'] = 0;
						clearstatcache();
						$lista[$cont]['Data'] = filemtime($nome);;
						$lista[$cont]['Status'] = Arquivos::RetornaStatusArquivo($nome);
					}
					else if ($tipo_arquivo == 'file')
					{
						$lista[$cont]['Caminho'] = $nome;
						$dir_pai = dirname($nome);
						$lista[$cont]['Diretorio'] = trim(substr($dir_pai, strlen($diretorio) + 1), DIRECTORY_SEPARATOR);
						// correção feita para exibicao de todos os acentos - a função basename não é "not utf-8 safe"
						$lista[$cont]['Arquivo'] = ltrim(basename(preg_replace("/\//", "/ ", $nome)));
						clearstatcache();
						$lista[$cont]['Tamanho'] = filesize($nome);
						clearstatcache();
						$lista[$cont]['Data'] = filemtime($nome);;
						$lista[$cont]['Status'] = Arquivos::RetornaStatusArquivo($nome);
					}
					$cont++;
				}
			}
			// Lista os subdiret�rios da pasta corrente e
			// adiciona-os na fila
			// Lista o padr�o procurado
			if (($c1 = glob($dir_cur.DIRECTORY_SEPARATOR.'*')) === false)
				return (array());
	
			if (($c2 = glob($dir_cur.DIRECTORY_SEPARATOR.'.*')) === false)
				return (array());
			$c2 = array_filter($c2, "FiltraDiretorioRelativos");
	
			$conteudo = array_merge($c1, $c2);
	
			if (count($conteudo) > 0)
			{
				foreach ($conteudo as $idx => $nome)
				{
					clearstatcache();
					if (filetype($nome) == "dir")
					{
						array_push($dirs, $nome);
					}
				}
			}
		}
		if (count($lista) > 0)
			uasort ($lista, 'CompareRetornaArrayDiretorio');
		return ($lista);
	}
	
	/* ************************************************************************
	 ExisteArquivo - verifica a exist�ncia de um arquivo
	Entrada: $arquivo - nome do arquivo
	Saida: true se existe, do contr�rio, false
	*/
	static function ExisteArquivo($arquivo)
	{
		clearstatcache();
		if (is_link($arquivo))
			return (true);
		else
		{
			clearstatcache();
			return(file_exists($arquivo));
		}
	}
	
	/* ************************************************************************
	 RetornaStatusArquivo - Retorna 1 se perms do arquivo = ????????x
	Entrada: $arquivo - nome passado
	Saida: true - Se tudo ok, false - se ocorreu algum erro
	*/
	static function RetornaStatusArquivo($arquivo)
	{
		clearstatcache();
		$perms = fileperms($arquivo);
		if (($perms & 0020)>0)
			return (true);
		else
			return (false);
	}
	
	/* ************************************************************************
	 FiltraDiretorioRelativos - Fun��o utilizada pela fun��o 'array_filter'.
	Remove de um array contendo caminhos, o diret�rio corrente ('.') e o
	diret�rio pai ('..').
	Entrada: $var - caminho para arquivo
	Saida: true - se for diferente de '.' e '..'
	*/
	static function FiltraDiretorioRelativos($var)
	{
		$v = basename($var);
		return(($v != '.') && ($v != '..'));
	}
	
	/* ************************************************************************
	 CompareRetornaArrayDiretorio - usado internamente (Diogo)
	em RetornaArrayDiretorio
	Compara o conte�do de uma sequencia
	*/
	static function CompareRetornaArrayDiretorio($ar1, $ar2)
	{
		if ($ar1['Diretorio'] < $ar2['Diretorio'])
			return -1;
		else if ($ar1['Diretorio'] > $ar2['Diretorio'])
			return 1;
		if ($ar1['Arquivo'] < $ar2['Arquivo'])
			return -1;
		else if ($ar1['Arquivo'] > $ar2['Arquivo'])
			return 1;
		return 0;
	}
	/* ************************************************************************
	 Remove arquivo - Apaga arquivo e link simbolico
	Entrada: $arquivo - nome do arquivo
	Saida: false - se houver algum erro
	*/
	static function RemoveArquivo($arquivo)
	{
		//  if (!file_exists($arquivo))
			//    return false;
			//  return(unlink($arquivo));
			return(unlink($arquivo));
	}
	
	/* ************************************************************************
	 CriaLinkSimbolico - Cria link simbolico
	Entrada: $origem - diret�rio de origem
	$destino - caminho e nome do link
	Saida: true - Se tudo ok, false - se ocorreu algum erro
	*/
	static function CriaLinkSimbolico($origem,$destino)
	{
		return(symlink($origem, $destino));
	}
	
	/* ************************************************************************
	 CriaDiretorio - cria diretorio com modo 755
	Entrada: $diretorio - nome do diretorio
	Saida: true - Se tudo ok, false - se ocorreu algum erro
	*/
	static function CriaDiretorio($diretorio)
	{
	
		if (Arquivos::ExisteArquivo($diretorio))
		{
			clearstatcache();
			if (filetype($diretorio) == "dir")
				return true;
		}
	
		$dir_tmp = dirname($diretorio);
		if (!Arquivos::CriaDiretorio($dir_tmp))
			return false;
	
		$r1 = mkdir ($diretorio, 0755);
		$r2 = chmod ($diretorio, 0755);
		return ($r1 && $r2);
	}
	
	
	
}