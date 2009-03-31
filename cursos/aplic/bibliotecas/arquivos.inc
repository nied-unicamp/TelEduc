<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bibliotecas/arquivos.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�ncia
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

    Nied - N�cleo de Inform�tica Aplicada � Educa��o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : bibliotecas/arquivos.inc
  ========================================================== */
/*

/* ************************************************************************
   RealizaUpload - Faz upload do arquivo e move para diretorio de
                         destino
   OBS.: O FORM DE ENVIO DEVE TER OS SEGUINTES PARAMETROS:
         <form action={prox_.php}" enctype="multipart/form-data" method=post>
   Entrada: $arquivo - nome passado ap�s submit do arquivo
            $destino - path e NOME do destino do arquivo
   Saida: true - Se tudo ok, false - se ocorreu algum erro
*/
function RealizaUpload($arquivo,$destino)
{

  if(preg_match("/.*((\.php(\.|$))|(\.php3(\.|$))|(\.phtml(\.|$))).*/", $destino)){ return false;}	
  $flag=true;

  $num = 0;	
  $destinoAux = $destino;    
  while($flag){
  	if(ExisteArquivo($destinoAux)){
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
  if (ExisteArquivo($destino))
  {
    $r2 = chmod($destino,0644);
    return $r1;
  }
  else
    return false;
}

/* ************************************************************************
   CriaLinkSimbolico - Cria link simbolico
   Entrada: $origem - diret�rio de origem
            $destino - caminho e nome do link
   Saida: true - Se tudo ok, false - se ocorreu algum erro
*/
function CriaLinkSimbolico($origem,$destino)
{
  return(symlink($origem, $destino));
}

/* ************************************************************************
   ExisteArquivo - verifica a exist�ncia de um arquivo
   Entrada: $arquivo - nome do arquivo
   Saida: true se existe, do contr�rio, false
*/
function ExisteArquivo($arquivo)
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
   CriaDiretorio - cria diretorio com modo 755
   Entrada: $diretorio - nome do diretorio
   Saida: true - Se tudo ok, false - se ocorreu algum erro
*/
function CriaDiretorio($diretorio)
{

  if (ExisteArquivo($diretorio))
  {
    clearstatcache();
    if (filetype($diretorio) == "dir")
      return true;
  }

  $dir_tmp = dirname($diretorio);
  if (!CriaDiretorio($dir_tmp))
    return false;

  $r1 = mkdir ($diretorio, 0755);
  $r2 = chmod ($diretorio, 0755);
  return ($r1 && $r2);
}

/* ************************************************************************
   RetornaStatusArquivo - Retorna 1 se perms do arquivo = ????????x
   Entrada: $arquivo - nome passado
   Saida: true - Se tudo ok, false - se ocorreu algum erro
*/
function RetornaStatusArquivo($arquivo)
{
  clearstatcache();
  $perms = fileperms($arquivo);
  if (($perms & 0020)>0)
    return (true);
  else
    return (false);
}

/* ************************************************************************
   AlteraStatusArquivo - Retorna 1 se perms do arquivo = rw-rw-r--
   Entrada: $arquivo - nome do arquivo
            $estado - true ativa, false desativa
   Sem Saida
*/
function AlteraStatusArquivo($arquivo,$estado)
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
   Remove arquivo - Apaga arquivo e link simbolico
   Entrada: $arquivo - nome do arquivo
   Saida: false - se houver algum erro
*/
function RemoveArquivo($arquivo)
{
//  if (!file_exists($arquivo))
//    return false;
//  return(unlink($arquivo));
  return(unlink($arquivo));
}

/* ************************************************************************
   MudarParaDiretorio - Muda para o diret�rio especificado
   Entrada: $diretorio - diretorio
   Saida: false - se n�o for poss�vel mudar para o diret�rio
*/
function MudarParaDiretorio($diretorio)
{
  if (($diretorio = realpath($diretorio)) === false )
    return false;
  return chdir($diretorio);
}

/* ************************************************************************
   RetornaDiretorioCorrente - Retorna o diret�rio corrente
   Entrada: nenhuma
   Saida: diret�rio corrente
*/
function RetornaDiretorioCorrente()
{
  return(getcwd());
}

/* ************************************************************************
   RemoveDiretorio - Apaga diretorio e todo seu conte�do
   Entrada: $diretorio - diretorio
   Saida: false - se houver algum erro
*/
function RemoveDiretorio($diretorio)
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
  $c2 = array_filter($c2, "FiltraDiretorioRelativos");

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
          $flag = RemoveDiretorio($nome_real);
        else
          $flag = RemoveArquivo($nome_real);
      }
    }
  }
  clearstatcache();
  if ((filetype($diretorio)) == 'dir')
    $flag = rmdir($diretorio);
  else
  {
    // INCONSISTENTE: apenas para manter compatibilidade com algumas
    // chamadas feitas no ambiente cujo par�metro � um arquivo!!
    $flag = RemoveArquivo($diretorio);
  }

  return ($flag);
}


/* ************************************************************************
   FiltraDiretorioRelativos - Fun��o utilizada pela fun��o 'array_filter'.
     Remove de um array contendo caminhos, o diret�rio corrente ('.') e o
     diret�rio pai ('..').
   Entrada: $var - caminho para arquivo
   Saida: true - se for diferente de '.' e '..'
*/
function FiltraDiretorioRelativos($var)
{
  $v = basename($var);
  return(($v != '.') && ($v != '..'));
}


/* ************************************************************************
   CopiaArquivosDiretorio - Copia apenas o conteudo de diretorio de origem para destino
   Entrada: $origem - diretorio de origem
            $destino - diretorio de destino
   Saida: false - se houver algum erro
   OBS.: se o arquivo a ser copiado j� existir no destino, ent�o ele �
           sobrescrito.
         para copiar o conte�do de /temp/arquivos/* para /final/*
           use: $origem="/temp/arquivos/"
                $destino="/final/"
*/
function CopiaArquivosDiretorio($origem, $destino)
{
  // Se a origem ou destino forem vazios retorna false
  if (($origem == "") || ($destino == ""))
    return false;

  // Vari�vel de retorno da fun��o
  $flag = true;

  // Resolve o caminho da origem
  $origem = realpath($origem);
  if ($origem === false)
    return false;

  // Lista os arquivo existentes na pasta
  $origem = rtrim($origem, DIRECTORY_SEPARATOR);

  clearstatcache();
  // Listamos o conte�do da pasta
  if (($c1 = glob($origem.DIRECTORY_SEPARATOR."*")) === false)
    return (false);
  // Listamos o conte�do oculto da pasta e, como s�o listadas tamb�m
  // '.' e '..', filtramos essas pastas relativas
  if (($c2 = glob($origem.DIRECTORY_SEPARATOR.".*")) === false)
    return (false);
  $c2 = array_filter($c2, "FiltraDiretorioRelativos");
  // Concatenamos o resultado das listagens
  $conteudo = array_merge($c1, $c2);
  if (($total = count($conteudo)) > 0)
  {
    for ($i = 0; ($i < $total) && ($flag); $i++)
    {
      $nome_arquivo = basename($conteudo[$i]);

      clearstatcache();
      // Se for diret�rio, copiamos recursivamente seus conte�dos
      if (filetype($conteudo[$i]) == "dir")
        $flag = CopiaDiretorio($conteudo[$i], $destino);
      else
      {
        // Sendo arquivo, verificamos se n�o houve erros na execu��o
        // anterior de algum comando. Caso o arquivo j� exista no caminho
        // destino, ent�o o removemos e em seguida copiamos o arquivo.
        if ($flag)
        {
          $dest = $destino.DIRECTORY_SEPARATOR.$nome_arquivo;
          if (ExisteArquivo($dest))
            $flag = RemoveArquivo($dest);

          // O teste aqui tamb�m � necess�rio para ver se 'RemoveArquivo'
          // n�o retornou false.
          if ($flag)
          {
            $stat = RetornaStatusArquivo($conteudo[$i]);
            if (copy($conteudo[$i], $dest))
            {
              if (!AlteraStatusArquivo($dest, $stat))
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
   OBS.: se o arquivo a ser copiado j� existir no destino, ent�o ele �
           sobrescrito.
         para copiar o conte�do de /temp/arquivos/ para /final/arquivos/
           use: $origem="/temp/arquivos/"
                $destino="/final/"
*/
function CopiaDiretorio($origem, $destino)
{
  // Se a origem ou destino forem vazios retorna false
  if (($origem == "") || ($destino == ""))
    return false;

  // Vari�vel de retorno da fun��o
  $flag = true;

  // Resolve o caminho da origem
  $origem = realpath($origem);
  if ($origem === false)
    return false;

  $origem = rtrim($origem, DIRECTORY_SEPARATOR);

  clearstatcache();

  // Se o caminho especificado n�o existe, ent�o copiamos
  // o diret�rio com um novo nome (o nome de destino).
  if (!ExisteArquivo($destino))
  {
    if (!CriaDiretorio($destino))
      return (false);
  }
  else
  {
    // Se n�o existir um diret�rio com o nome da pasta
    // a ser copiada da origem, criamo-na no destino.
    $nome_dir = basename($origem);
    $destino = $destino.DIRECTORY_SEPARATOR.$nome_dir;
    if (!ExisteArquivo($destino))
      if (!CriaDiretorio($destino))
        return (false);
  }

  clearstatcache();
  // Listamos o conte�do da pasta
  if (($c1 = glob($origem.DIRECTORY_SEPARATOR."*")) === false)
    return (false);
  // Listamos o conte�do oculto da pasta e, como s�o listadas tamb�m
  // '.' e '..', filtramos essas pastas relativas
  if (($c2 = glob($origem.DIRECTORY_SEPARATOR.".*")) === false)
    return (false);
  $c2 = array_filter($c2, "FiltraDiretorioRelativos");
  // Concatenamos o resultado das listagens
  $conteudo = array_merge($c1, $c2);
  if (($total = count($conteudo)) > 0)
  {
    for ($i = 0; ($i < $total) && ($flag); $i++)
    {
      $nome_arquivo = basename($conteudo[$i]);
      // Se for diret�rio, copiamos recursivamente seus conte�dos
      clearstatcache();

      if (filetype($conteudo[$i]) == "dir")
        $flag = CopiaDiretorio($conteudo[$i], $destino);
      else
      {
        // Sendo arquivo, verificamos se n�o houve erros na execu��o
        // anterior de algum comando. Caso o arquivo j� exista no caminho
        // destino, ent�o o removemos e em seguida copiamos o arquivo.
        if ($flag)
        {
          $dest = $destino.DIRECTORY_SEPARATOR.$nome_arquivo;
          if (ExisteArquivo($dest))
            $flag = RemoveArquivo($dest);

          // O teste aqui tamb�m � necess�rio para ver se 'RemoveArquivo'
          // n�o retornou false.
          if ($flag)
          {
            $stat = RetornaStatusArquivo($conteudo[$i]);
            if (copy($conteudo[$i], $dest))
            {
              if (!AlteraStatusArquivo($dest, $stat))
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
   CopiaArquivo - Copia arquivo ou todo o conteudo de diretorio de
     origem para destino
   Entrada: $origem - arquivo ou diretorio de origem
            $destino - diretorio de destino
   Saida: false - se houver algum erro
   OBS.: se o arquivo a ser copiado j� existir no destino, e este for um
           arquivo ent�o ele � sobrescrito. Se o destino existir, por�m for
           um diret�rio, ent�o o arquivo � copiado para dentro deste.
         para copiar 'arquivo.txt' de /temp/arquivo.txt para /final/texto.txt
           use: $origem="/temp/arquivo.txt"
                $destino="/final/texto.txt"
*/
function CopiaArquivo($origem, $destino)
{
  // Se a origem ou destino forem vazios retorna false
  if (($origem == "") || ($destino == ""))
    return false;

  // Vari�vel de retorno da fun��o
  $flag = true;

  // Resolve o caminho da origem
  $origem = realpath($origem);
  if ($origem === false)
    return false;

  clearstatcache();

  // Listamos o conte�do da pasta
  if (($conteudo = glob($origem)) === false)
    return (false);
  if (($total = count($conteudo)) > 0)
  {
    for ($i = 0; ($i < $total) && ($flag); $i++)
    {
      $nome_arquivo = basename($conteudo[$i]);

      clearstatcache();
      // Se for diret�rio, copiamos recursivamente seus conte�dos
      if (filetype($conteudo[$i]) == "dir")
        $flag = CopiaDiretorio($conteudo[$i], $destino);
      else
      {
        // Sendo arquivo, verificamos se n�o houve erros na execu��o
        // anterior de algum comando. Caso o arquivo j� exista no caminho
        // destino, ent�o o removemos e em seguida copiamos o arquivo.
        if ($flag)
        {
          if (ExisteArquivo($destino))
          {
            $dest = $destino.DIRECTORY_SEPARATOR.$nome_arquivo;

            clearstatcache();
            // Se o destino existe e � um diret�rio, ent�o copiamos o
            // arquivo para dentro dele. Caso contr�rio, apagamos o arquivo
            // existente para ent�o copiarmos (sobrescrever).
            if (filetype($destino) == "dir")
              $dest = $destino.DIRECTORY_SEPARATOR.$nome_arquivo;
            else
            {
              $dest = $destino;
              $flag = RemoveArquivo($dest);
            }
          }
          else
            $dest = $destino;

          // O teste aqui tamb�m � necess�rio para ver se 'RemoveArquivo'
          // n�o retornou false.
          if ($flag)
          {
            $stat = RetornaStatusArquivo($conteudo[$i]);
            if (copy($conteudo[$i], $dest))
            {
              if (!AlteraStatusArquivo($dest, $stat))
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
   MoveDiretorio - Copia todo o conteudo de diretorio de origem para destino
   Entrada: $origem - diretorio de origem
            $destino - diretorio de destino
   Saida: false - se houver algum erro
   OBS.: para mover o conte�do de /temp/arquivos/ para /final/arquivos/
         use: $origem="/temp/arquivos/"
              $destino="/final/"
*/
function MoveDiretorio($origem, $destino)
{
  // Se a origem ou destino forem vazios retorna false
  if (($origem == "") || ($destino == ""))
    return false;

  // Vari�vel de retorno da fun��o
  $flag = true;

  // Resolve o caminho da origem
  $origem = realpath($origem);
  if ($origem === false)
    return false;

  clearstatcache();
  if (filetype($origem) != 'dir')
    return false;

  // Se o caminho de destino especificado n�o existe, ent�o movemos
  // o diret�rio com um novo nome (o nome de destino).
  if (!ExisteArquivo($destino))
  {
    if (!CriaDiretorio($destino))
      return (false);
  }
  else
  {
    // Se n�o existir um diret�rio com o nome da pasta
    // a ser copiada da origem, criamo-na no destino.
    $nome_dir = basename($origem);
    $destino = $destino.DIRECTORY_SEPARATOR.$nome_dir;

    if (!ExisteArquivo($destino))
      if (!CriaDiretorio($destino))
        return (false);
  }

  clearstatcache();
  // Listamos o conte�do da pasta
  if (($c1 = glob($origem.DIRECTORY_SEPARATOR."*")) === false)
    return (false);
  // Listamos o conte�do oculto da pasta e, como s�o listadas tamb�m
  // '.' e '..', filtramos essas pastas relativas
  if (($c2 = glob($origem.DIRECTORY_SEPARATOR.".*")) === false)
    return (false);
  $c2 = array_filter($c2, "FiltraDiretorioRelativos");
  // Concatenamos o resultado das listagens
  $conteudo = array_merge($c1, $c2);

  if (($total = count($conteudo)) > 0)
  {
    for ($i = 0; ($i < $total) && ($flag); $i++)
    {
      $nome_arquivo = basename($conteudo[$i]);

      clearstatcache();
      // Se for diret�rio, movemos recursivamente seus conte�dos
      if (filetype($conteudo[$i]) == 'dir')
      {
        if (!MoveDiretorio($conteudo[$i], $destino))
          return(false);
      }
      else
      {
        // Sendo arquivo, verificamos se n�o houve erros na execu��o
        // anterior de algum comando. Caso o arquivo j� exista no caminho
        // destino, ent�o o removemos e em seguida copiamos o arquivo.
        if ($flag)
        {
          $dest = $destino.DIRECTORY_SEPARATOR.$nome_arquivo;
          if (ExisteArquivo($dest))
            $flag = RemoveArquivo($dest);

          // O teste aqui tamb�m � necess�rio para ver se 'RemoveArquivo'
          // n�o retornou false.
          if ($flag)
          {
            $stat = RetornaStatusArquivo($conteudo[$i]);
            if (copy($conteudo[$i], $dest))
            {
              if (AlteraStatusArquivo($dest, $stat))
                $flag = RemoveArquivo($conteudo[$i]);
              else
                return (false);
            }
            else
              return(false);
          }
        }
      }
    }
  }
  clearstatcache();
  if (($flag) && (filetype($origem) == 'dir'))

    $flag = RemoveDiretorio($origem);

  return $flag;
}

/* ************************************************************************
   MoveArquivo - Move o arquivo para o destino
   Entrada: $origem - diretorio de origem
            $destino - diretorio de destino
   Saida: false - se houver algum erro
*/
function MoveArquivo($origem, $destino)
{
  // Se a origem ou destino forem vazios retorna false
  if (($origem == "") || ($destino == ""))
    return false;

  // Vari�vel de retorno da fun��o
  $flag = true;

  // Resolve o caminho da origem ('realpath' testa tamb�m se o arquivo
  // existe).
  $origem = realpath($origem);
  if ($origem === false)
    return false;

  // Caso estejamos movendo um diret�rio para uma pasta n�o
  // existente, criamos essa pasta
  clearstatcache();
  if (filetype($origem) == 'dir')
  {
    if (!ExisteArquivo($destino))
    {
      if (!CriaDiretorio($destino))
        return (false);
    }
    else
    {
      // Se n�o existir um diret�rio com o nome da pasta
      // a ser copiada da origem, criamo-na no destino.
      $nome_dir = basename($origem);
      $destino = $destino.DIRECTORY_SEPARATOR.$nome_dir;
      if (!ExisteArquivo($destino))
        if (!CriaDiretorio($destino))
          return (false);
    }

    // Listamos o conte�do da pasta $origem
    if (($c1 = glob($origem.DIRECTORY_SEPARATOR."*")) === false)
      return (false);
    // Listamos o conte�do oculto da pasta e, como s�o listadas tamb�m
    // '.' e '..', filtramos essas pastas relativas
    if (($c2 = glob($origem.DIRECTORY_SEPARATOR.".*")) === false)
      return (false);
    $c2 = array_filter($c2, "FiltraDiretorioRelativos");
    // Concatenamos o resultado das listagens
    $conteudo = array_merge($c1, $c2);

  }
  else
  {
    // Listamos o arquivo (array com 1 elemento)
    $conteudo[0] = $origem;
  }

  // Se retornar mais que um elemento (ou seja, pelo menos
  // um que n�o seja o pr�prio arquivo ou pasta a ser movida)
  // ent�o os movemos.
  $total = count($conteudo);

  if ($total > 0)
  {
    for ($i = 0; ($i < $total) && ($flag); $i++)
    {
      $nome_arquivo = basename($conteudo[$i]);

      $dest = $destino.DIRECTORY_SEPARATOR.$nome_arquivo;

      // Se for diret�rio, movemos recursivamente seus conte�dos
      clearstatcache();
      if (filetype($conteudo[$i]) == 'dir')
      {
        if (!MoveArquivo($conteudo[$i], $dest))
          return(false);
      }
      else
      {
        // Sendo arquivo, verificamos se n�o houve erros na execu��o
        // anterior de algum comando. Caso o arquivo j� exista no caminho
        // destino, ent�o o removemos e em seguida copiamos o arquivo.
        if ($flag)
        {
          if (ExisteArquivo($dest))
            $flag = RemoveArquivo($dest);

          // O teste aqui tamb�m � necess�rio para ver se 'RemoveArquivo'
          // n�o retornou false.
          if ($flag)
          {
            $stat = RetornaStatusArquivo($conteudo[$i]);
            if (copy($conteudo[$i], $dest))
            {
              if (AlteraStatusArquivo($dest, $stat))
              {
                if (!RemoveArquivo($conteudo[$i]))
                  return(false);
              }
              else
                return (false);
            }
            else
              return(false);
          }
        }
      }
    }
  }
  clearstatcache();
  if ((filetype($origem)) == 'dir')
    $flag = RemoveDiretorio($origem);

  return $flag;
}

/* ************************************************************************
   SobrepoeDiretorio - move o diretorio temporario para a pasta de
                     destino. Se algum erro acontecer, restaura a
                     pasta original
   Entrada: $temp - diretorio de origem
            $destino - diretorio de destino
   Saida: "ok" - se houver ok
          "noBkp" - Se n�o foi poss�vel criar backup
          "noMv" - Se n�o foi poss�vel mover o diretorio
   OBS.: para mover o conte�do de /temp/arquivos/ para /final/arquivos/
         use: $origem="/temp/arquivos/"
              $destino="/final/"
*/
function SobrepoeDiretorio($origem, $destino)
{
  $flag = true;
  // Se a origem ou destino forem vazios, ou caso
  // eles n�o existam (testado por 'realpath'), ou se eles n�o
  // forem diret�rios ent�o saimos da fun��o com false.
  if (($origem == "") || ($destino == ""))
    return (false);

  if ((($origem = realpath($origem)) === false) ||
      (($destino = realpath($destino)) === false))
    return (false);
  else
  {
    clearstatcache();
    if (filetype($origem) != 'dir')
      return (false);

    clearstatcache();
    if (filetype($destino) != 'dir')
      return (false);
  }


  // Pegamos o nome do diretorio final
  $final = basename($origem);
  $time = time();

  // Concatenamos com o SEPARADOR DE PASTAS ('realpath' remove
  // separadores em excesso no final)
  $dest = $destino.DIRECTORY_SEPARATOR.$final;

  // Checa existe a pasta em destino
  if (ExisteArquivo($dest))
  {
    // Se existe, fazemos backup
    if (!MoveDiretorio($dest, $dest."~".$time))
      return (false);
    // Caso ocorra um erro ao sobrescrever os arquivos,
    // recuperamos o backup
    if (!MoveDiretorio($origem, $destino))
    {
      if (!MoveDiretorio($dest."~".$time, $dest))
      {
        // OBS: o que ocorre aqui � um erro ao tentar recuperar
        //      do backup.
        return (false);
      }
      return (false); // N�o � else
    }
    // Removemos o backup
    return(RemoveDiretorio($dest."~".$time));
  }
  else
  {
    // N�o existe a pasta EM destino, movemos a pasta de origem
    // para dentro da pasta destino.
    return(MoveDiretorio($origem, $destino));
  }
}

/* ************************************************************************
   CompareRetornaArrayDiretorio - usado internamente (Diogo)
                                  em RetornaArrayDiretorio
                                  Compara o conte�do de uma sequencia
*/
function CompareRetornaArrayDiretorio($ar1, $ar2)
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
function RetornaArrayDiretorio($diretorio)
{

  if (!ExisteArquivo($diretorio))
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
          $lista[$cont]['Status'] = RetornaStatusArquivo($nome);
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
          $lista[$cont]['Status'] = RetornaStatusArquivo($nome);
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
   RetornaSubDiretorios - Retorna um array com os subdiretorios (do primeiro
                          nivel de profundidade, apenas) do diretorio informado

   Entrada: $diretorio - diretorio a ser varrido.
   Saida:   Array multidimensional com:
              $lista[<num>] - caminho completo.
            false, se algo der errado (testar com '===')
*/
function RetornaSubDiretorios($diretorio)
{
  // Prepara lista de arquivos
  clearstatcache();

  if (($conteudo = ListaArquivos($diretorio, '*', 'D', 1)) === false)
    return (array());

  if (is_array($conteudo))
  {
    foreach($conteudo as $idx => $nome)
      $lista[] = basename($nome['Diretorio']);
  }

  return ($lista);
}

/* ************************************************************************
   ListaArquivos - Procura um arquivo que case com o padr�o no caminho
       especificado.
   Entrada: $caminho - diret�rio onde ser procurado,
            $padrao - padr�o a ser procurado,
            $tipo - 'T': todos arquivos e diret�rios
                    'D': somente diret�rios
                    'A': somente arquivos
            $nivel - n�vel de profundidade da busca
              (negativo para busca infinita)

   Saida: Array multidimensional com:
            $lista[<num>]['Caminho'] - caminho completo.
            $lista[<num>]['Diretorio'] - Diretorio do arquivo
            $lista[<num>]['Arquivo'] - Nome do arquivo
            $lista[<num>]['Status'] - Condi��o especial (true ou false);
            $lista[<num>]['Tamanho'] - tamanho do arquivo
            $lista[<num>]['Data'] - data da �ltima modifica��o
          false se ocorrer um erro (testar com '===')
*/
function ListaArquivos($caminho, $padrao, $tipo = 'T', $nivel)
{
  $caminho = realpath($caminho);
  $padrao = trim ($padrao, "/");

  // Inicializamos o contador de arquivos encontrados
  $cont = 0;
  // Inicializamos o contador de n�vel de pastas
  $n = 0;

  // Inicializamos a fila
  $dirs = array();
  // Inserimos o primeiro elemento na fila
  array_push($dirs, $caminho);

  // Enquanto houver diret�rios na fila procure
  // pelos arquivos
  while ((count($dirs) > 0) &&
         (($nivel < 0) || ($n < $nivel))
        )
  {
    // Remove o primeiro elemento da fila
    $dir_cur = array_shift($dirs);

    // Lista o padr�o procurado
    $encontrados = glob($dir_cur."/".$padrao);
    if (count($encontrados))
    {
      foreach ($encontrados as $idx => $nome)
      {
        clearstatcache();
        if ((filetype($nome) == "dir") &&
            (('T' == $tipo) || ('D' == $tipo)))
        {
          $lista[$cont]['Caminho'] = $nome;
          $lista[$cont]['Diretorio'] = basename($nome);
          $lista[$cont]['Arquivo'] = "";
          $lista[$cont]['Tamanho'] = 0;
          clearstatcache();
          $lista[$cont]['Data'] = filemtime($nome);;
          $lista[$cont]['Status'] = RetornaStatusArquivo($nome);
        }
        clearstatcache();
        if ((filetype($nome) == "file") &&
            (('T' == $tipo) || ('A' == $tipo)))
        {
          $lista[$cont]['Caminho'] = $nome;
          $dir_pai = dirname($nome);
          $lista[$cont]['Diretorio'] = dirname(substr($nome, strlen($dir_pai) - 1));
          $lista[$cont]['Arquivo'] = basename($nome);
          clearstatcache();
          $lista[$cont]['Tamanho'] = filesize($nome);
          clearstatcache();
          $lista[$cont]['Data'] = filemtime($nome);;
          $lista[$cont]['Status'] = RetornaStatusArquivo($nome);
        }
        $cont++;
      }
    }
    // Lista os subdiret�rios da pasta corrente e
    // adiciona-os na fila
    $conteudo = glob($dir_cur."/*");

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
      $n++; // incrementamos o contador de n�vel de pasta
    }
  }
  return ($lista);
}

/* ************************************************************************
   DescompactarArquivoZip - Descompactar arquivo zip.
   Entrada: $zip - caminho e nome do arquivo zip
            $destino - caminho onde descompactar o arquivo zip
   Saida: true se tudo ok
*/

function DescompactarArquivoZip($zip,$destino)
{
  $comando="unzip -q -o \"".$zip."\" -d \"".$destino."\"";
  system($comando,$situacao);
  if ((!RenomeiaPHPDoDiretorio($destino)) || (!SetaPermissoes($destino)))
    return (false);

  return(!$situacao);
}


/* ************************************************************************
   SetaPermissoes- Seta para 755 todos os diret�rios, e para 644 os arquivos.
   Entrada: $diretorio - diretorio cujas permiss�es ser�o alteradas
   Saida: true se tudo ok
*/
function SetaPermissoes($diretorio)
{
  $flag = true;

  $lista = RetornaArrayDiretorio($diretorio);
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
   RenomeiaPHPDoDiretorio- Renomeia todo arquivo .PHP do diretorio para
                          .PHP.TXT
   Entrada: $diretorio - caminho onde descompactar o arquivo zip.
   Saida: true se tudo ok
*/
function RenomeiaPHPDoDiretorio ($diretorio)
{
  $flag = true;

  $lista = RetornaArrayDiretorio($diretorio);

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


?>
