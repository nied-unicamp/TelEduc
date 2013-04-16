<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bibliotecas/sql_dump.inc

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
  ARQUIVO : bibliotecas/sql_dump.inc
  ========================================================== */
  
/* **********************************************************************
   InsereTabelasBD - Insere na base de dados as tabelas especificadas, usando o arquivo passado
   Entrada: $sock - Socket da conex�o
            $tabelas - Tabelas a serem inseridas ("" para inserir tudo)
            $arquivo - Arquivo com o dump
   Saida: nenhuma
*/
function InsereTabelasBD($sock, $tabelas, $arquivo)
{
  if ($fh = fopen($arquivo, "r"))
  {
    $comando = "";

    ini_set("auto_detect_line_endings", "On");
    while (!feof($fh))
    {
      $linha = fgets($fh, 1024);
      
      if (ereg("^[^#-]", $linha))
      {
        $linha = eregi_replace("\n"," ",$linha);
        $linha = trim($linha);
        $comando .= $linha;

        if (eregi(";", $linha))
        {
          $comando = eregi_replace(";"," ",$comando);
          $comando = trim($comando);

          // Todas as tabelas
          if ($tabelas == "")
            Enviar($sock, $comando);
          else if (is_array($tabelas)) // Apenas as tabelas especificadas
          {
            foreach($tabelas as $idx => $tabela)
            {
               if (mysql_get_server_info() >= 4.1)
               {
                  if (eregi("INSERT[ ]+INTO[ ]+`".$tabela."`[ ]+VALUES[ ]+\(", $comando) ||
                      eregi("CREATE[ ]+[^ ]+[ ]+`".$tabela."`[ ]+\(", $comando) || 
                      eregi("LOCK[ ]+TABLES[ ]+`".$tabela."`[ ]+WRITE", $comando) ||
                      eregi("DROP[ ]+TABLE[ ]+IF[ ]+EXISTS[ ]+`".$tabela."`", $comando))
                  {
                     Enviar($sock, $comando);
                  }
               } else {
                  if (eregi("INSERT[ ]+INTO[ ]+".$tabela."[ ]+VALUES[ ]+\(", $comando) ||
                      eregi("CREATE[ ]+[^ ]+[ ]+".$tabela."[ ]+\(", $comando) ||
                      eregi("LOCK[ ]+TABLES[ ]+".$tabela."[ ]+WRITE", $comando) ||
                      eregi("DROP[ ]+TABLE[ ]+IF[ ]+EXISTS[ ]+".$tabela."", $comando))
                  {
                     Enviar($sock, $comando);
                  }                                                                                                                                                                 
               }
            }
          }

          $comando = "";
        }
      }
    }
    ini_restore("auto_detect_line_endings");
  }
}

/* **********************************************************************
   ExtraiValorDeCampoDump - Extrai os valores dos campos das tabelas
       especificadas de um arquivo de dump das bases de dados.
   Entrada: $arquivo - caminho e nome do arquivo do dump
            $tabelas['Tabela_1'][0] = 'campo_A'
                                [1] = 'campo_B'
                    ['Tabela_2'][0] = 'campo_1'
                                [1] = 'campo_3'
                                [2] = 'campo_5'
   Saida: $res['Tabela_1']['campo_A']['posicao'] - posicao do campo na tabela
                                     ['valores'][0] - valor para a primeira ocorr�ncia
                                                [1]
                          ['campo_B']['posicao']
                                     ['valores'][0]
              ['Tabela_2']['campo_1']['posicao']
                                     ['valores'][0]
                          ['campo_3']['posicao']
                                     ['valores'][0]
                          ['campo_5']['posicao']
                                     ['valores'][0]

*/
function ExtraiValorDeCampoDump($arquivo, $tabelas)
{
  if (function_exists("ExtraiValorDeCampoDumpFachada"))
  {
    return(ExtraiValorDeCampoDumpFachada());
  }

  $resultado = false;
  if  (count($tabelas) > 0)
  {
    // Abre o arquivo
    if ($fh = fopen($arquivo, "r"))
    {
      while (!feof($fh))
      {
        // L� uma linha do arquivo
        $linha = fgets($fh, 1024);

        // Para cada tabela procuramos sua defini��o (CREATE TABLE)
        foreach ($tabelas as $nome_tabela => $campos)
        {
          $total_campos = count($campos);

          if ($total_campos > 0)
          {
            // Encontramos a defini��o da tabela. Procuramos ent�o a posi��o
            // de cada campo na tabela
            if (preg_match("/^CREATE(?>[ ]+)TABLE(?>[ ]+)".$nome_tabela."(?>[ ]+)\\(/i", $linha))
            {
              $pos = 0;
              $num_campos_encontrados = 0;
              // Procuramos a posi��o at� achar todos os campos especificados
              while (($num_campos_encontrados < $total_campos) && (!preg_match("/^(?>[ ]*)\\)(?>.*);$/i", $linha)))
              {
                $linha = fgets($fh, 1024);
                foreach ($campos as $idx => $nome_campo)
                {
                  // Zeramos a posi��o do campo
                  if (!isset($resultado[$nome_tabela][$nome_campo]['posicao']))
                    $resultado[$nome_tabela][$nome_campo]['posicao'] = 0;

                  // Encontramos o campo, guardamos sua posi��o
                  if (preg_match("/^(?>[ ]*)".$nome_campo."(?>[ ]+)/", $linha))
                  {
                    $resultado[$nome_tabela][$nome_campo]['posicao'] = $pos;
                    $num_campos_encontrados++;
                    break;
                  }
                }
                $pos++; // incrementamos o contador de posi��o
              }
            }
            // Encontramos as inser��es na tabela
            else if (preg_match("/^INSERT(?>[ ]+)INTO(?>[ ]+)".$nome_tabela."(?>[ ]+)VALUES(?>[ ]+)\\(/i", $linha))
            {
              // Obt�m strings entre ap�strofos
              $pat_entre_apostrofos = "'(?:(?:(?:\\\\\\\\)*\\\\')|(?:[^']))+'";
              // Obt�m inteiros ou null
              $pat_num_entre_virgulas = "(?<=,|\\()(?:[ ]*)(?:(?:[0-9]+)|(?:null))(?:[ ]*)(?=,|\\))";

              // Obt�m strings entre ap�strofos ou inteiros ou null
              $pattern = "/(?:".$pat_entre_apostrofos.")|(?:".$pat_num_entre_virgulas.")/i";

              // Obt�m os valores dos campos do INSERT
              preg_match_all($pattern, $linha, $matches);

              // Para cada campo guardamos os valores correpondentes � inser��o corrente
              foreach ($campos as $idx => $nome_campo)
              {
                $resultado[$nome_tabela][$nome_campo]['valores'][] =
                   trim($matches[0][$resultado[$nome_tabela][$nome_campo]['posicao']]);
              }
            }
          }
        }
      }
      fclose($fh);
    }
  }
  return $resultado;
}


/* **********************************************************************
   ExtraiValorDeCampoDumpWhere - Extrai os valores dos campos das tabelas
       especificadas de um arquivo de dump das bases de dados.
   Entrada: $arquivo - caminho e nome do arquivo do dump
            $tabelas['Tabela_1']['CAMPOS'][0] = 'campo_A'
                                ['CAMPOS'][1] = 'campo_B'
                                ['WHERE'] = "campo1 = 3 or campo9 <> 12"
                    ['Tabela_2']['CAMPOS'][0] = 'campo_1'
                                ['CAMPOS'][1] = 'campo_3'
                                ['CAMPOS'][2] = 'campo_5'
                                ['WHERE'] = "campo12 = 'uma string'"
   Saida: $res['Tabela_1']['campo_A']['posicao'] - posicao do campo na tabela
                                     ['valores'][0] - valor para a primeira ocorr�ncia
                                                [1]
                          ['campo_B']['posicao']
                                     ['valores'][0]
              ['Tabela_2']['campo_1']['posicao']
                                     ['valores'][0]
                          ['campo_3']['posicao']
                                     ['valores'][0]
                          ['campo_5']['posicao']
                                     ['valores'][0]

*/
function ExtraiValorDeCampoDumpWhere($arquivo, $tabelas)
{
  if (function_exists("ExtraiValorDeCampoDumpWhereFachada"))
  {
    return(ExtraiValorDeCampoDumpFachada());
  }

  $resultado = false;
  if  (count($tabelas) > 0)
  {
    // Abre o arquivo
    if ($fh = fopen($arquivo, "r"))
    {
      while (!feof($fh))
      {
        // L� uma linha do arquivo
        $linha = fgets($fh, 1024);

        // Para cada tabela procuramos sua defini��o (CREATE TABLE)
        foreach ($tabelas as $nome_tabela => $dados)
        {
          $total_campos = count($dados['CAMPOS']);

          if ((1) || ($total_campos > 0))
          {
            // Encontramos a defini��o da tabela. Procuramos ent�o a posi��o
            // de cada campo na tabela
            if (preg_match("/^(?>[[:space:]]*)CREATE(?>[[:space:]]*)TABLE(?>[[:space:]]*)".$nome_tabela."(?>[[:space:]]*)\\(/i", $linha))
            {

              $pos = 0;
              $num_campos_encontrados = 0;
              $palavras_reservadas = array('PRIMARY', 'KEY', 'INDEX', 'FULLTEXT',
                                           'UNIQUE', 'CONSTRAINT', 'FOREIGN',
                                           'CHECK', 'COMMENT');

              // Procuramos a posi��o at� achar todos os campos especificados
              while (!preg_match("/^(?>[ ]*)\\)(?>[^;)]*);$/i", $linha))
              {

                $linha = fgets($fh, 1024);

                // Encontramos o campo, guardamos sua posi��o
                if (preg_match("/^(?>[ ]*)(?>[^ ]*)[ ]/", $linha, $match))
                {
                  $nome_campo = trim($match[0]);
                  if (!in_array(strtoupper($nome_campo), $palavras_reservadas))
                  {
                    $resultado[$nome_tabela][$nome_campo]['posicao'] = $pos;
                    $pos++; // incrementamos o contador de posi��o
                  }
                }
              }

            }
            // Encontramos as inser��es na tabela
            else if (preg_match("/^INSERT INTO ".$nome_tabela." VALUES \\(/i", $linha))
            {
              // Obt�m strings entre ap�strofos
              $pat_entre_apostrofos = "'(?:(?:(?:\\\\\\\\)*\\\\')|(?:[^']))+'";
              // Obt�m inteiros ou null
              $pat_num_entre_virgulas = "(?<=,|\\()(?:[ ]*)(?:(?:[0-9]+)|(?:null))(?:[ ]*)(?=,|\\))";

              // Obt�m strings entre ap�strofos ou inteiros ou null
              $pattern = "/(?:".$pat_entre_apostrofos.")|(?:".$pat_num_entre_virgulas.")/i";

              // Obt�m os valores dos campos do INSERT
              preg_match_all($pattern, $linha, $matches);

              //$dados['WHERE'];
              
              preg_replace($arr_nomes_campos, $arr_valores_campos, $dados['WHERE']);

              // Para cada campo guardamos os valores correpondentes � inser��o corrente
              foreach ($dados['CAMPOS'] as $idx => $nome_campo)
              {
                $resultado[$nome_tabela][$nome_campo]['valores'][] =
                   trim($matches[0][$resultado[$nome_tabela][$nome_campo]['posicao']]);
              }
            }
          }
        }
      }
      fclose($fh);
    }
  }
  return $resultado;
}

?>
