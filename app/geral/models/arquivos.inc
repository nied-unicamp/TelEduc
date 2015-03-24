<?php

/**
 * Classe Arquivos do modulo geral
 * @author     TelEduc
 * @copyright  20014 TelEduc
 * @license    http://teleduc.org.br/
 */
class Arquivos {

     /**
     * Dado uma string contendo um caminho para um arquivo, 
     * esse metodo ira retornar true caso existe um arquivo arquivo no final do caminho enviado.
     * 
     * @param string $var Caminho a ser verificado 
     * @return boolean Se true existe um arquivo no final desde caminho.
     */
    static function FiltraDiretorioRelativos($var) {
        $v = basename($var);
        return(($v != '.') && ($v != '..'));
    }

    /**
     * Metódo estático auxiliar de uso interno para ordenacao,
     * 
     * Utilizado em um uasort como  funcao de comparação definida pelo usuário. 
     * 
     * @param array $ar1 Deve possuir o indexador Diretorio ou Arquivo $ar1['Diretorio'] ou $ar1['Arquivo']
     * @param array $ar2 Deve possuir o indexador Diretorio ou Arquivo $ar2['Diretorio'] ou $ar2['Arquivo']
     * @return int Retorna 0 se nao houve a comparacao, -1 caso o ar1 seja menor que ar2 e 1 caso o ar1 seja maior que ar2
     */
    static function CompareRetornaArrayDiretorio($ar1, $ar2) {
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

    /*     * ***********************************************************************
      RetornaArrayDiretorio - Retorna um array com os arquivos do diretorio
      Entrada: $diretorio - diretorio a ser varrido.
      Saida: Array multidimensional com:
      $lista[<num>]['Caminho'] - caminho completo.
      $lista[<num>]['Diretorio'] - Diretorio do arquivo
      $lista[<num>]['Arquivo'] - Nome do arquivo
      $lista[<num>]['Status'] - Condiï¿½ï¿½o especial (true ou false);
      $lista[<num>]['Tamanho'] - tamanho do arquivo
      $lista[<num>]['Data'] - data da ï¿½ltima modificaï¿½ï¿½o
     */

    /**
     * Este metodo estático retorna um array dos arquivos dentro do diretorio que é passado pelos parametros.
     * 
     * 
     * @param String $diretorio Caminho do diretorio a ser consultado
     * @return array Retorna array multidimensional com os arquivos
     */
    static function RetornaArrayDiretorio($diretorio) {

        if (Arquivos::ExisteArquivo($diretorio) == false)
            return (array());

        $diretorio = rtrim($diretorio, DIRECTORY_SEPARATOR);

        // Inicializamos o contador de arquivos encontrados
        $cont = 0;

        // Inicializamos a fila
        $dirs = array();
        // Inserimos o primeiro elemento na fila
        array_push($dirs, $diretorio);

        // Enquanto houver diretï¿½rios na fila procure
        // pelos arquivos
        while (count($dirs) > 0) {
            // Remove o primeiro elemento da fila
            $dir_cur = array_shift($dirs);

            // Lista o padrï¿½o procurado
            if (($e1 = glob($dir_cur . DIRECTORY_SEPARATOR . '*')) === false)
                return (array());

            if (($e2 = glob($dir_cur . DIRECTORY_SEPARATOR . '.*')) === false)
                return (array());

            $e2 = array_filter($e2, array('Arquivos', "FiltraDiretorioRelativos"));
            $encontrados = array_merge($e1, $e2);

            if (count($encontrados)) {
                foreach ($encontrados as $idx => $nome) {
                    clearstatcache();
                    $tipo_arquivo = filetype($nome);

                    if ($tipo_arquivo == 'dir') {
                        $lista[$cont]['Caminho'] = $nome;
                        $lista[$cont]['Diretorio'] = trim(substr($nome, strlen($diretorio) + 1), DIRECTORY_SEPARATOR);
                        $lista[$cont]['Arquivo'] = "";
                        $lista[$cont]['Tamanho'] = 0;
                        clearstatcache();
                        $lista[$cont]['Data'] = filemtime($nome);
                        ;
                        $lista[$cont]['Status'] = Arquivos::RetornaStatusArquivo($nome);
                    } else if ($tipo_arquivo == 'file') {
                        $lista[$cont]['Caminho'] = $nome;
                        $dir_pai = dirname($nome);
                        $lista[$cont]['Diretorio'] = trim(substr($dir_pai, strlen($diretorio) + 1), DIRECTORY_SEPARATOR);
                        // correÃ§Ã£o feita para exibicao de todos os acentos - a funÃ§Ã£o basename nÃ£o Ã© "not utf-8 safe"
                        $lista[$cont]['Arquivo'] = ltrim(basename(preg_replace("/\//", "/ ", $nome)));
                        clearstatcache();
                        $lista[$cont]['Tamanho'] = filesize($nome);
                        clearstatcache();
                        $lista[$cont]['Data'] = filemtime($nome);
                        ;
                        $lista[$cont]['Status'] = Arquivos::RetornaStatusArquivo($nome);
                    }
                    $cont++;
                }
            }
            // Lista os subdiretï¿½rios da pasta corrente e
            // adiciona-os na fila
            // Lista o padrï¿½o procurado
            if (($c1 = glob($dir_cur . DIRECTORY_SEPARATOR . '*')) === false)
                return (array());

            if (($c2 = glob($dir_cur . DIRECTORY_SEPARATOR . '.*')) === false)
                return (array());
            $c2 = array_filter($c2, array('Arquivos', "FiltraDiretorioRelativos"));

            $conteudo = array_merge($c1, $c2);

            if (count($conteudo) > 0) {
                foreach ($conteudo as $idx => $nome) {
                    clearstatcache();
                    if (filetype($nome) == "dir") {
                        array_push($dirs, $nome);
                    }
                }
            }
        }
	if (count($lista) > 0)
        	uasort ($lista, array('Arquivos', "CompareRetornaArrayDiretorio"));
        return ($lista);
    }

    /*     * ***********************************************************************
      ExisteArquivo - verifica a existï¿½ncia de um arquivo
      Entrada: $arquivo - nome do arquivo
      Saida: true se existe, do contrï¿½rio, false
     */

    /**
     * Este metodo estático retorna se existe um arquivo baseado em um caminho passado pelos parametros.
     * 
     * @param string $arquivo caminho do arquivo, incluindo as informacoes do proprio arquivo, Ex.: /mnt/pasta1/arq.html
     * @return boolean Se true arquivo exista, caso contrario arquivo nao existe
     */
    static function ExisteArquivo($arquivo) {
        clearstatcache();
        if (is_link($arquivo))
            return (true);
        else {
            clearstatcache();
            return(file_exists($arquivo));
        }
    }

    /*     * ***********************************************************************
      RetornaStatusArquivo - Retorna 1 se perms do arquivo = ????????x
      Entrada: $arquivo - nome passado
      Saida: true - Se tudo ok, false - se ocorreu algum erro
     */

    /**
     * Metódo estático que retorna se no momento você tem permissao de acesso ao arquivo.
     * 
     * @param String $arquivo caminho do arquivo, incluindo as informacoes do proprio arquivo, Ex.: /mnt/pasta1/arq.html
     * @return boolean Retorna true se haver permissoes suficientes no arquivo e false caso contrário.
     */
    static function RetornaStatusArquivo($arquivo) {
        clearstatcache();
        $perms = fileperms($arquivo);
        if (($perms & 0020) > 0)
            return (true);
        else
            return (false);
    }

    

    /*     * ***********************************************************************
      Remove arquivo - Apaga arquivo e link simbolico
      Entrada: $arquivo - nome do arquivo
      Saida: false - se houver algum erro
     */
    /**
     * Apaga um arquivo baseado no caminho enviado.
     * 
     * @param String $arquivo caminho do arquivo, incluindo as informacoes do proprio arquivo, Ex.: /mnt/pasta1/arq.html
     * @return boolean Retorna true caso consiga apagar o arquivo e false caso nao.
     */
    static function RemoveArquivo($arquivo) {
        //  if (!file_exists($arquivo))
        //    return false;
        //  return(unlink($arquivo));
        return(unlink($arquivo));
    }

    /*     * ***********************************************************************
      CriaLinkSimbolico - Cria link simbolico
      Entrada: $origem - diretï¿½rio de origem
      $destino - caminho e nome do link
      Saida: true - Se tudo ok, false - se ocorreu algum erro
     */
    
    
    /**
     * Cria um link simbolico de sistema.
     * Nao é um link HTML.
     * 
     * @param String $origem Alvo do link
     * @param String $destino O nome do link
     * @return boolean Retorna TRUE em caso de sucesso ou FALSE em caso de falha.
     */
    static function CriaLinkSimbolico($origem, $destino) {
        return(symlink($origem, $destino));
    }

    /*     * ***********************************************************************
      CriaDiretorio - cria diretorio com modo 755
      Entrada: $diretorio - nome do diretorio
      Saida: true - Se tudo ok, false - se ocorreu algum erro
     */
    
    
    /**
     * Cria pasta a partir do caminho enviado com permissão 755 
     * 
     * @param String $diretorio 
     * @return boolean
     */
    static function CriaDiretorio($diretorio) {

        if (Arquivos::ExisteArquivo($diretorio)) {
            clearstatcache();
            if (filetype($diretorio) == "dir")
                return true;
        }

        $dir_tmp = dirname($diretorio);
        if (!Arquivos::CriaDiretorio($dir_tmp))
            return false;

        $r1 = mkdir($diretorio, 0755);
        $r2 = chmod($diretorio, 0755);
        return ($r1 && $r2);
    }

    /* ************************************************************************
	 RealizaUpload - Faz upload do arquivo e move para diretorio de
	destino
	OBS.: O FORM DE ENVIO DEVE TER OS SEGUINTES PARAMETROS:
	<form action={prox_.php}" enctype="multipart/form-data" method=post>
	Entrada: $arquivo - nome passado apos submit do arquivo
	$destino - path e NOME do destino do arquivo
	Saida: true - Se tudo ok, false - se ocorreu algum erro
	*/
	static function RealizaUpload($arquivo,$destino)
	{
	
		if(preg_match("/.*((\.php(\.|$))|(\.php3(\.|$))|(\.phtml(\.|$))).*/", $destino)){ return false;}
		$flag=true;
	
		$num = 0;
		$destinoAux = $destino;
		while($flag){
			if(Arquivos::ExisteArquivo($destinoAux)){
				$num++;
				$destinoAux = explode("/", $destino);
				$tamCamArq = count($destinoAux);
	
				$nomeArq = $destinoAux[$tamCamArq-1];
	
				$nomeArq = explode(".", $nomeArq);
				$nomeArq[0] .= "(".$num.")";
				$nomeArq = implode(".", $nomeArq);
				$destinoAux[$tamCamArq-1] = $nomeArq;
	
				$destinoAux=implode("/",$destinoAux);
				//$destino = implode("/", $camArq);
	
				/*$destino = preg_replace_callback(
				 $pattern,
						'acrescentaNum',
						$destino
				);*/
				$flag= true;
			}else{
				$destino = $destinoAux;
				$flag=false;
			}
	
		}
	
		$r1 = move_uploaded_file($arquivo,$destino);
		if (Arquivos::ExisteArquivo($destino))
		{
			$r2 = chmod($destino,0644);
			return $r1;
		}
		else
			return false;
	}
	
	/* ************************************************************************
	 RemoveDiretorio - Apaga diretorio e todo seu conteudo
		Entrada: $diretorio - diretorio
	Saida: false - se houver algum erro
	*/
	static function RemoveDiretorio($diretorio)
	{
		$flag = true;
	
		if ($diretorio == "")
			return (false);
	
		// Verifica se o arquivo existe
		if (($diretorio = realpath($diretorio)) === false)
			return (false);
	
		clearstatcache();
		if (($c1 = glob($diretorio.DIRECTORY_SEPARATOR."*")) === false)
			return (false);
	
		if (($c2 = glob($diretorio.DIRECTORY_SEPARATOR.".*")) === false)
			return (false);
		$c2 = array_filter($c2, array('Arquivos', "FiltraDiretorioRelativos"));
	
		$conteudo = array_merge($c1, $c2);
	
		if (($total = count($conteudo)) > 0)
		{
			for ($i = 0; ($i < $total) && ($flag); $i++)
			{
			$nome_real = realpath($conteudo[$i]);
	
			if (($nome_real != ".") and ($nome_real != ".."))
			{
					clearstatcache();
				if (filetype($nome_real) == "dir")
					$flag = Arquivos::RemoveDiretorio($nome_real);
					else
					$flag = Arquivos::RemoveArquivo($nome_real);
			}
			}
			}
			clearstatcache();
			if ((filetype($diretorio)) == 'dir')
					$flag = rmdir($diretorio);
					else
  					{
				// INCONSISTENTE: apenas para manter compatibilidade com algumas
					// chamadas feitas no ambiente cujo parametro e um arquivo!!
					$flag = Arquivos::RemoveArquivo($diretorio);
				}
	
					return ($flag);
	}
	
	/* ************************************************************************
	 DescompactarArquivoZip - Descompactar arquivo zip.
	Entrada: $zip - caminho e nome do arquivo zip
	$destino - caminho onde descompactar o arquivo zip
	Saida: true se tudo ok
	*/
	
	static function DescompactarArquivoZip($zip,$destino)
	{
		$comando="unzip -q -o \"".$zip."\" -d \"".$destino."\"";
		system($comando,$situacao);
		if ((!Arquivos::RenomeiaPHPDoDiretorio($destino)) || (!Arquivos::SetaPermissoes($destino)))
			return (false);
	
		return(!$situacao);
	}
	
	/* ************************************************************************
	 RenomeiaPHPDoDiretorio- Renomeia todo arquivo .PHP do diretorio para
	.PHP.TXT
	Entrada: $diretorio - caminho onde descompactar o arquivo zip.
	Saida: true se tudo ok
	*/
	static function RenomeiaPHPDoDiretorio ($diretorio)
	{
		$flag = true;
	
		$lista = Arquivos::RetornaArrayDiretorio($diretorio);
	
		if ( ($total = count($lista)) > 0)   {
			foreach($lista as $cod => $linha) {
				if ($linha['Arquivo'] != "") {
					if (eregi("php$", $linha['Arquivo'])) {
						$flag = rename($linha['Caminho'], $linha['Caminho'].".txt");
					}
				}
			}
		} else {
			return (false);
		}
	
		return ($flag);
	}
	
	/* ************************************************************************
	 SetaPermissoes- Seta para 755 todos os diretorios, e para 644 os arquivos.
	Entrada: $diretorio - diretorio cujas permissoes serao alteradas
	Saida: true se tudo ok
	*/
	static function SetaPermissoes($diretorio)
	{
		$flag = true;
	
		$lista = Arquivos::RetornaArrayDiretorio($diretorio);
		if (($total = count($lista)) > 0)
		{
			for ($i = 0; ($i < $total) && ($flag); $i++)
			{
			if ($lista[$i]['Arquivo'] == "")
				$flag = chmod($diretorio.DIRECTORY_SEPARATOR.$lista[$i]['Diretorio'],0755);
				else if (!$lista[$i]['Status'])
						$flag = chmod($diretorio.DIRECTORY_SEPARATOR.$lista[$i]['Diretorio'].DIRECTORY_SEPARATOR.$lista[$i]['Arquivo'],0644);
			}
			}
			else
				return (false);
	
				return $flag;
	}
	
	/* ************************************************************************
	 AlteraStatusArquivo - Retorna 1 se perms do arquivo = rw-rw-r--
	Entrada: $arquivo - nome do arquivo
	$estado - true ativa, false desativa
	Sem Saida
	*/
        
        /**
         * Retorna 1 se perms do arquivo = rw-rw-r--
         * 
         * @param String $arquivo Nome do arquivo
         * @param boolean $estado true ativa, false desativa
         * @return int Retorna 1 se perms do arquivo = rw-rw-r--
         */
	static function AlteraStatusArquivo($arquivo,$estado)
	{
		clearstatcache();
		$perms = fileperms($arquivo);
		if ($estado)
			$perms |= 0020;
		else
			$perms -= ($perms & 0020);
		return(chmod($arquivo, $perms));
	}

/* ************************************************************************
	 CopiaArquivosDiretorio - Copia apenas o conteudo de diretorio de origem para destino
	Entrada: $origem - diretorio de origem
	$destino - diretorio de destino
	Saida: false - se houver algum erro
	OBS.: se o arquivo a ser copiado ja existir no destino, entao ele e
	sobrescrito.
	para copiar o conteudo de /temp/arquivos/* para /final/*
	use: $origem="/temp/arquivos/"
	$destino="/final/"
	*/
	static function CopiaArquivosDiretorio($origem, $destino)
	{
		// Se a origem ou destino forem vazios retorna false
		if (($origem == "") || ($destino == ""))
			return false;
	
		// Variavel de retorno da funcao
		$flag = true;
	
		// Resolve o caminho da origem
		$origem = realpath($origem);
		if ($origem === false)
			return false;
	
		// Lista os arquivo existentes na pasta
		$origem = rtrim($origem, DIRECTORY_SEPARATOR);
	
		clearstatcache();
		// Listamos o conteudo da pasta
		if (($c1 = glob($origem.DIRECTORY_SEPARATOR."*")) === false)
			return (false);
		// Listamos o conteudo oculto da pasta e, como sao listadas tambem
		// '.' e '..', filtramos essas pastas relativas
		if (($c2 = glob($origem.DIRECTORY_SEPARATOR.".*")) === false)
			return (false);
		$c2 = array_filter($c2, array('Arquivos', "FiltraDiretorioRelativos"));
		// Concatenamos o resultado das listagens
		$conteudo = array_merge($c1, $c2);
		if (($total = count($conteudo)) > 0)
		{
			for ($i = 0; ($i < $total) && ($flag); $i++)
			{
			$nome_arquivo = basename($conteudo[$i]);
	
			clearstatcache();
			// Se for diretario, copiamos recursivamente seus conteudos
			if (filetype($conteudo[$i]) == "dir")
			$flag = Arquivos::CopiaDiretorio($conteudo[$i], $destino);
			else
			{
			// Sendo arquivo, verificamos se nao houve erros na execucao
				// anterior de algum comando. Caso o arquivo ja exista no caminho
				// destino, entao o removemos e em seguida copiamos o arquivo.
				if ($flag)
				{
				$dest = $destino.DIRECTORY_SEPARATOR.$nome_arquivo;
				if (Arquivos::ExisteArquivo($dest))
					$flag = Arquivos::RemoveArquivo($dest);
	
				// O teste aqui tambem e necessario para ver se 'RemoveArquivo'
				// nao retornou false.
				if ($flag)
				{
			$stat = Arquivos::RetornaStatusArquivo($conteudo[$i]);
			if (copy($conteudo[$i], $dest))
			{
			if (!Arquivos::AlteraStatusArquivo($dest, $stat))
				return (false);
			}
			else
				return (false);
			}
			}
			}
			}
			}
			return $flag;
		}
		
		/* ************************************************************************
		 CopiaDiretorio - Copia todo o conteudo de diretorio de origem para destino
		Entrada: $origem - diretorio de origem
		$destino - diretorio de destino
		Saida: false - se houver algum erro
		OBS.: se o arquivo a ser copiado ja existir no destino, entao ele e
		sobrescrito.
		para copiar o conteudo de /temp/arquivos/ para /final/arquivos/
		use: $origem="/temp/arquivos/"
		$destino="/final/"
		*/
		static function CopiaDiretorio($origem, $destino)
		{
			// Se a origem ou destino forem vazios retorna false
			if (($origem == "") || ($destino == ""))
				return false;
		
			// Variavel de retorno da funcao
			$flag = true;
		
			// Resolve o caminho da origem
			$origem = realpath($origem);
			if ($origem === false)
				return false;
		
			$origem = rtrim($origem, DIRECTORY_SEPARATOR);
		
			clearstatcache();
		
			// Se o caminho especificado nao existe, entao copiamos
			// o diretorio com um novo nome (o nome de destino).
			if (!Arquivos::ExisteArquivo($destino))
			{
				if (!Arquivos::CriaDiretorio($destino))
					return (false);
			}
			else
			{
				// Se nao existir um diretorio com o nome da pasta
				// a ser copiada da origem, criamo-na no destino.
				$nome_dir = basename($origem);
				$destino = $destino.DIRECTORY_SEPARATOR.$nome_dir;
				if (!Arquivos::ExisteArquivo($destino))
				if (!Arquivos::CriaDiretorio($destino))
					return (false);
			}
		
			clearstatcache();
			// Listamos o conteudo da pasta
			if (($c1 = glob($origem.DIRECTORY_SEPARATOR."*")) === false)
				return (false);
			// Listamos o conteudo oculto da pasta e, como sao listadas tambem
			// '.' e '..', filtramos essas pastas relativas
			if (($c2 = glob($origem.DIRECTORY_SEPARATOR.".*")) === false)
				return (false);
			$c2 = array_filter($c2, array('Arquivos', "FiltraDiretorioRelativos"));
			// Concatenamos o resultado das listagens
			$conteudo = array_merge($c1, $c2);
			if (($total = count($conteudo)) > 0)
			{
				for ($i = 0; ($i < $total) && ($flag); $i++)
				{
					$nome_arquivo = basename($conteudo[$i]);
					// Se for diretorio, copiamos recursivamente seus conteudos
					clearstatcache();
			
					if (filetype($conteudo[$i]) == "dir")
						$flag = Arquivos::CopiaDiretorio($conteudo[$i], $destino);
						else
						{
						// Sendo arquivo, verificamos se nao houve erros na execucao
						// anterior de algum comando. Caso o arquivo ja exista no caminho
						// destino, entao o removemos e em seguida copiamos o arquivo.
							if ($flag)
							{
							$dest = $destino.DIRECTORY_SEPARATOR.$nome_arquivo;
								if (Arquivos::ExisteArquivo($dest))
									$flag = Arquivos::RemoveArquivo($dest);
				
								// O teste aqui tambem e necessario para ver se 'RemoveArquivo'
								// nao retornou false.
								if ($flag)
								{
									$stat = Arquivos::RetornaStatusArquivo($conteudo[$i]);
									if (copy($conteudo[$i], $dest))
									{
										if (!Arquivos::AlteraStatusArquivo($dest, $stat))
											return (false);
									}
										else
											eturn (false);
								}
							}
						}
					}
				}
				return $flag;
	}
	
	/************************************************************************************************************
	 haArquivosVisiveisDir - verifica se ha arquivo visivel dentro da pasta
	Entrada: $lista_arq_vis - vetor retornado da funcao RetornaArquivosVisiveis
	Saida: Retorna true se existe algum arquivo nao oculto dentro da pasta ou das subpastas, false caso               contrario
	*************************************************************************************************************/
	static function haArquivosVisiveisDir($dir, $lista_arq_vis){
		foreach($lista_arq_vis as $cod => $linha)
		{
			if(!($linha['Status'])){
				if (($linha['Arquivo']!="") && (strncmp($linha['Diretorio'], $dir, strlen($dir)) == 0)){
					return true;
				}
			}
		}
	
		return false;
	}
}
?>
