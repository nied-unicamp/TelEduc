<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bibliotecas/extracao.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : bibliotecas/extracao.inc
  ========================================================== */

 if (!defined("NOME_RESUMO"))
  define("NOME_RESUMO", "curso.txt");

 if (!defined("NOME_ARQUIVOS"))
  define("NOME_ARQUIVOS", "arquivos.tar.gz");

 if (!defined("DUMP_AMBIENTE"))
  define("DUMP_AMBIENTE", ".ambiente.table");

 if (!defined("DUMP_CURSO"))
  define("DUMP_CURSO", ".curso.table");

  $sock = Conectar("");
  $lista_frases = RetornaListaDeFrases($sock, -2);
  Desconectar($sock);


  /* *********************************************************************
   UnixCaminho - Coloca barras em caracteres especiais.
   Entradas: $caminho - string que pode contem caracteres especiais: ? / +
   Saida: string barrada: \?\/\+
  */
  function UnixCaminho($caminho)
  {
    return(preg_quote($caminho));

  }

  /* *********************************************************************
   CamposExistem - Busca os campos_procurados no amontoado_campos. Retorna
      os campos nao encontrados.
   Entradas: $campos_procurados - array com os nomes dos campos procurados
             $amontoado_campos - array com os nomes dos campos onde sera feita a busca
   Saida: array ($flag_todos_campos_existem,
                 array ($campo_ausente1,
                        $campo_ausente6)
                )
  */
  function CamposExistem($campos_procurados, $amontoado_campos)
  {
     $flag_todos_campos_existem = true;
     for ($k = 0, $total = count($campos_procurados); $k < $total; $k++)
     {
       if (!in_array($campos_procurados[$k], $amontoado_campos, false))
       {
         $flag_todos_campos_existem = false;
         $campos_ausentes[] = $campos_procurados[$k];
       }
     }
     return(array($flag_todos_campos_existem, $campos_ausentes));
  }

  /* *********************************************************************
   MontaSelectCaminho - Testa se os campos de restricao sao suficientes e
       monta a select para o caminho variavel.
   Entradas: $sock - socket da base de dados do AMBIENTE
             $caminho_variavel - array com conteudo
                 '{Tabela1.campo1}, {Tabela1.campo5}, ...'
             $campos_restricao - array com nomes dos campos utilizados para restringir
                   a consulta (na clausula where)
   Saida: string = select campo1, campo2 from ".$tabela_selecionada
                    where campo4 = ']campo7[' and campo7 = ']campo7[' and ...
  */
  function MontaSelectCaminho($sock, $caminho_variavel, $campos_restricao)
  {
    if (is_array($caminho_variavel) && is_array($campos_restricao)) // Se encontrou parte dinamica
    {
      // Obtem o nome da tabela e dos campos selecionados
      list($tabela_selecionada, $campos_selecionados) = ExtraiTabelaECampos($caminho_variavel);
      // Lista os detalhes de TODOS os campos da tabela selecionada.
      $descr_todos_campos = RetornaListaCampos($sock, $tabela_selecionada);

      if (is_array($descr_todos_campos))
      {
        // Obtemos os nomes de TODOS os campos da tabela selecionada, e
        // os nomes daqueles que dentre eles sao chave primaria
        $j = 0;
        $total = count($descr_todos_campos);
        for ($c = 0; $c < $total; $c++)
        {
          $nomes_todos_campos[$c] = $descr_todos_campos[$c]['name'];
          if ($descr_todos_campos[$c]['primary_key'])
            $nomes_campos_primarios[$j++] = $descr_todos_campos[$c]['name'];
        }
        reset ($nomes_todos_campos);
        reset ($nomes_campos_primarios);

        // Extrai os nomes dos campos do array passado pelo usuario
        $nomes_campos_restricao = array_keys($campos_restricao);

        # VERIFICAR SE O CAMINHO A SER MONTADO POSSUI TODOS CAMPOS DA CHAVE PRIMARIA

        // Verifica se ha algum campo de restricao que nao existe na tabela
        // selecionada.
        list ($flag_campos_restricao_presentes, $nomes_campos_restricao_ausentes) =
          CamposExistem($nomes_campos_restricao, $nomes_todos_campos);

        if ($flag_campos_restricao_presentes == true)
        {
          // TESTE3: verificamos se as chaves primarias da tabela estao
          //         entre os campos passados pelo usuario

          // Verifica se os campos que constituem a chave primaria estao
          // entre os campos de restricao
          list($flag_campos_primarios_presentes, $nomes_campos_primarios_ausentes) =
            CamposExistem($nomes_campos_primarios, $nomes_campos_restricao);

          // Verifica se apenas alguns campos que constituem a chave primaria
          // estao ausentes, ou todos.
          $num_campos_primarios_ausentes = count($nomes_campos_primarios) -
                                            count($nomes_campos_primarios_ausentes);

          if (($flag_campos_primarios_presentes == true) ||
              (($flag_campos_primarios_presentes == false) && ($num_campos_primarios_ausentes > 0))
             )
          {
            return (MontaSelectTemplate($tabela_selecionada, $campos_selecionados, $campos_restricao));
          }
          else
          {
            // 59 - Os seguintes campos que formam a chave primï¿½ria <b>nï¿½o</b> foram passados: 
            $msg = RetornaFraseDaLista($lista_frases, 59)."<font color=red>".implode(", ", $nomes_campos_primarios_ausentes)."</font><br>\n";

            die($msg);
          }
        }
        else
        {
          // 60 - Os seguinte campos sao invï¿½lidos: 
          $msg = RetornaFraseDaLista($lista_frases, 60)." <font color=red>".implode(", ", $nomes_campos_restricao_ausentes)."</font><br>\n";
          die($msg);
        }
      }
      else
      {
        // 61- ERRO! A tabela nï¿½o possui campos:
        die(RetornaFraseDaLista($lista_frases, 61)." ".$nome_tabela_selecionada);
      }
    }  // fim do if: apenas uma (1) tabela foi especificada no caminho
    else
    {
      // 62 - ERRO: caminho deve conter parte variï¿½vel e devem ser especificados campos de restriï¿½ï¿½o.
      die(RetornaFraseDaLista($lista_frases, 62));
    }
  }
  
   /* *********************************************************************
   MontaSelectCaminho2 - Testa se os campos de restricao sao suficientes e
       monta a select para o caminho variavel.
   Entradas: $sock - socket da base de dados do AMBIENTE
             $caminho_variavel - array com conteudo
                 '{Tabela1.campo1}, {Tabela1.campo5}, ...'
             $campos_restricao - array com nomes dos campos utilizados para restringir
                   a consulta (na clausula where)
   Saida: string = select campo1, campo2 from ".$tabela_selecionada
                    where campo4 = ']campo7[' and campo7 = ']campo7[' and ...
  */
  function MontaSelectCaminho2($sock, $caminho_variavel, $campos_restricao)
  {
    if (is_array($caminho_variavel) && is_array($campos_restricao)) // Se encontrou parte dinamica
    {
      // Obtem o nome da tabela e dos campos selecionados
      $retorno=ExtraiTabelaECampos2($caminho_variavel);
      if($retorno=='php')
        return('tabela');
      else
      list($tabela_selecionada, $campos_selecionados) = $retorno;
      // Lista os detalhes de TODOS os campos da tabela selecionada.
      $descr_todos_campos = RetornaListaCamposExtracao($sock, $tabela_selecionada);

      if (is_array($descr_todos_campos))
      {
        // Obtemos os nomes de TODOS os campos da tabela selecionada, e
        // os nomes daqueles que dentre eles sao chave primaria
        $j = 0;
        $total = count($descr_todos_campos);
        for ($c = 0; $c < $total; $c++)
        {
          $nomes_todos_campos[$c] = $descr_todos_campos[$c]['name'];
          if ($descr_todos_campos[$c]['primary_key'])
            $nomes_campos_primarios[$j++] = $descr_todos_campos[$c]['name'];
        }
        reset ($nomes_todos_campos);
        reset ($nomes_campos_primarios);

        // Extrai os nomes dos campos do array passado pelo usuario
        $nomes_campos_restricao = array_keys($campos_restricao);

        # VERIFICAR SE O CAMINHO A SER MONTADO POSSUI TODOS CAMPOS DA CHAVE PRIMARIA

        // Verifica se ha algum campo de restricao que nao existe na tabela
        // selecionada.
        list ($flag_campos_restricao_presentes, $nomes_campos_restricao_ausentes) =
          CamposExistem($nomes_campos_restricao, $nomes_todos_campos);

        if ($flag_campos_restricao_presentes == true)
        {
          // TESTE3: verificamos se as chaves primarias da tabela estao
          //         entre os campos passados pelo usuario

          // Verifica se os campos que constituem a chave primaria estao
          // entre os campos de restricao
          list($flag_campos_primarios_presentes, $nomes_campos_primarios_ausentes) =
            CamposExistem($nomes_campos_primarios, $nomes_campos_restricao);

          // Verifica se apenas alguns campos que constituem a chave primaria
          // estao ausentes, ou todos.
          $num_campos_primarios_ausentes = count($nomes_campos_primarios) -
                                            count($nomes_campos_primarios_ausentes);

          if (($flag_campos_primarios_presentes == true) ||
              (($flag_campos_primarios_presentes == false) && ($num_campos_primarios_ausentes > 0))
             )
          {
            return (MontaSelectTemplate($tabela_selecionada, $campos_selecionados, $campos_restricao));
          }
          else
          {
          /*  $msg = "Os seguintes campos que formam a chave primaria <b>nao</b> ";
            $msg .= "foram passados: <font color=red>".implode(", ", $nomes_campos_primarios_ausentes);
            $msg .= "</font><br>para a tabela <b>".$nome_tabela_selecionada."</b><br>\n"; */
            return(-2);
          }
        }
        else
        {
         /* $msg = "Os seguinte campos sao invalidos: <font color=red>".implode(", ", $nomes_campos_restricao_ausentes)."</font><br>\n";
          $msg .= "para a tabela <b>".$nome_tabela_selecionada."</b><br>\n";*/
          return(-3);
        }
      }
      else
      {
        /*die("ERRO na modelagem da tabela: NAO possui campos!");  */
        return(-4);
      }
    }  // fim do if: apenas uma (1) tabela foi especificada no caminho
    else
    {
      /*die("ERRO: caminho deve conter parte variavel e devem ser especificados campos de restricao");*/
      return(-5);
    }
  }


  /* *********************************************************************
   MontaSelectTemplate - monta um template da consulta de select.
   Entradas: $tabela_selecionada - nome da tabela cujos campos serao selecionados
             $campos_selecionados - array com nomes dos campos a ser selecionados
             $campos_restricao - array com nomes dos campos utilizados para restringir
                   a consulta (na clausula where)
   Saida: string = select campo1, campo2 from ".$tabela_selecionada
                    where campo4 = ']campo7[' and campo7 = ']campo7[' and ...
  */
  function MontaSelectTemplate($tabela_selecionada, $campos_selecionados, $campos_restricao)
  {
    if (is_array($campos_restricao))
    {
      $tmp = array();
      
      $query = "select ".implode(", ", $campos_selecionados);
      $query .= " from ".$tabela_selecionada." where ";

      foreach ($campos_restricao as $campo => $valores)
      {
        $tmp[$i] = $campo." = ']".$campo."['";
      }
      $where_clause = implode (" and ", $tmp);

      $query .= $where_clause;
      
      return($query);
    }
  }

  /* *********************************************************************
   ExtraiCaminhoVariavel - extrai a parte variavel do caminho.
   Entradas: $sock - socket da base de dados do AMBIENTE
             $caminho - string array com conteudo variavel
                        no seguinte formato:
                          '/fixo1/fixo2/{Tabela1.campo1}/{Tabela1.campo5}/...'
   Saida: array ('{Tabela1.campo1}',
                 '{Tabela1.campo5}',
                 '{Tabela5.campo2}'
                )
  */
  function ExtraiCaminhoVariavel($caminho)
  {
    preg_match_all("/\{[^}]+\}/", $caminho, $matches);
    return $matches[0];
  }

  /* *********************************************************************
   ExtraiTabelaECampos - extrai o nome da tabela e respectivos
       campos selecionados.
   Entradas: $sock - socket da base de dados do AMBIENTE
             $caminho_variavel - array com conteudo
                 '{Tabela1.campo1}, {Tabela1.campo5}, ...'

   Saida: array (Tabela1,
                 array (campo1,
                        campo5)
                )
  */
  function ExtraiTabelaECampos($caminho_variavel)
  {
    if (is_array($caminho_variavel))
    {
      $arr_tbls_cmps = array();

      // Cria array tblcampos ('tabela1 => campo1', 'tabela1' => campo2)
      for ($i = 0, $total = count($caminho_variavel); $i < $total; $i++)
      {
        unset($tmp, $nome_tabela, $nome_campo);

        // $tmp[0] = "{Tabela";
        // $tmp[1] = "campo}";
        $tmp = explode(".", $caminho_variavel[$i]);
        $nome_tabela = ltrim($tmp[0], "/"); // Elimina o '{' do nome da tabela
        $nome_campo = rtrim($tmp[1], "/"); // Elimina o '}' do nome do campo

        $arr_tbls_cmps[$nome_tabela][] = $nome_campo;
      }

      // Remove nomes repetidos de tabelas.
      $arr_tabelas = array_unique(array_keys($arr_tbls_cmps));

      if (count($arr_tabelas) > 1)
        // 63 - Erro: nï¿½o ï¿½ permitido especificar mais que uma tabela para montar o caminho para extraï¿½ï¿½o.
        die(RetornaFraseDaLista($lista_frases, 63));
      else
      {
        // Nome da tabela especificada no caminho
        $tabela_selecionada = $arr_tabelas[0];
        // Nome dos campos da tabela especificada no caminho
        $campos_selecionados = $arr_tbls_cmps[$tabela_selecionada];
        
        return (array($tabela_selecionada, $campos_selecionados));
      }
    }
  }
  
   /* *********************************************************************
   ExtraiTabelaECampos2 - extrai o nome da tabela e respectivos
       campos selecionados.
   Entradas: $sock - socket da base de dados do AMBIENTE
             $caminho_variavel - array com conteudo
                 '{Tabela1.campo1}, {Tabela1.campo5}, ...'

   Saida: array (Tabela1,
                 array (campo1,
                        campo5)
                )
  */
  function ExtraiTabelaECampos2($caminho_variavel)
  {
    if (is_array($caminho_variavel))
    {
      $arr_tbls_cmps = array();

      // Cria array tblcampos ('tabela1 => campo1', 'tabela1' => campo2)
      for ($i = 0, $total = count($caminho_variavel); $i < $total; $i++)
      {
        unset($tmp, $nome_tabela, $nome_campo);

        // $tmp[0] = "{Tabela";
        // $tmp[1] = "campo}";
        $tmp = explode(".", $caminho_variavel[$i]);
        $nome_tabela = ltrim($tmp[0], "/"); // Elimina o '{' do nome da tabela
        $nome_campo = rtrim($tmp[1], "/"); // Elimina o '}' do nome do campo

        $arr_tbls_cmps[$nome_tabela][] = $nome_campo;
      }

      // Remove nomes repetidos de tabelas.
      $arr_tabelas = array_unique(array_keys($arr_tbls_cmps));

      if (count($arr_tabelas) > 1)
        return('php');      /*Mais de uma tabela selecionadas*/
      else
      {
        // Nome da tabela especificada no caminho
        $tabela_selecionada = $arr_tabelas[0];
        // Nome dos campos da tabela especificada no caminho
        $campos_selecionados = $arr_tbls_cmps[$tabela_selecionada];

        return (array($tabela_selecionada, $campos_selecionados));
      }
    }
  }

  /* *********************************************************************
   ResolveCaminhoVariavel - substitui as partes variaveis do caminho pelos
       valores dos campos selecionados.
   Entradas: $caminho - string array com conteudo variavel
                        no seguinte formato:
                          '/fixo1/fixo2/{Tabela1.campo1}/{Tabela1.campo5}/...'
             $campos_selecionados - array com os campos e respectivos valores:
                        ('campo1' => valor1, 'campo5' => valor5, ...)
   Saida: string com o caminho resolvido: '/fixo1/fixo2/valor1/valor5/...'
  */
  function ResolveCaminhoVariavel($caminho, $campos_selecionados)
  {
    if (is_array($campos_selecionados)) // Se encontrou parte dinamica
    {
      foreach ($campos_selecionados as $campos => $valores)
      {
        $search[] = "/\{[^}]+\.".$campos."\}/";
        $replace[] = $valores;
      }
      return preg_replace($search, $replace, $caminho);
    }
  }
  
  /* *********************************************************************
   ResolveSelectTemplate - substitui o template do select com os valores
        dos campos selecionados.
   Entradas: $select_query - string com conteudo no formato:
               'select campo2, campo5 from Tabela where campo1 = ']campo1['
             $campos_selecionados - array com os campos e respectivos valores:
                        ('campo1' => valor1, 'campo5' => valor5, ...)
   Saida: query resolvida:
             'select campo2, campo5 from Tabela where campo1 = 'valor1'
  */
  function ResolveSelectTemplate($select_query, $campos_selecionados)
  {
    if (is_array($campos_selecionados))
    {
      // Substitui os valores dos campos
      foreach ($campos_selecionados as $campos => $valores)
      {
        $search[] = "/]".$campos."\[/";
        $replace[] = $valores;
      }

      $query = preg_replace($search, $replace, $select_query);
      return($query);
    }
  }

  /* *********************************************************************
   RetornaCaminhoExtracao - substitui o template do select com os valores
        dos campos selecionados.
   Entradas: $sock - socket da base de dados do AMBIENTE
             $campos_restricao - array com nomes dos campos utilizados para restringir
                   a consulta (na clausula where)
   Saida: caminho resolvida:
             '/fixo1/fixo2/valor1/valor5/...'
  */
  function RetornaCaminhoExtracao($sock, $campos_restricao, $caminho_base_extracao)
  {
    // Obtem o caminho da tabela
    $query_template = RetornaSelectTemplate($sock);

    // Se não termina com '/', adicione-o no final.
    if (substr($caminho_base_extracao, -1) != '/')
      $caminho_base_extracao .= '/';

    $caminho_variavel = RetornaCaminhoVariavel($sock);
    $caminho          = $caminho_base_extracao.$caminho_variavel;
    $caminho_variavel = ExtraiCaminhoVariavel($caminho_variavel);
    
    // Verifica a existencia de parte configuravel do caminho: OBRIGATORIA a existencia
    if (is_array($caminho_variavel))
    {
      if ($query_template == "")
      {
        $query_template = MontaSelectCaminho($sock, $caminho_variavel, $campos_restricao);
        AtualizaSelectTemplate($sock, $query_template);
      }
      // Substitui os valores para os campos de restriï¿½ï¿½o (where campo_restricao1 = valor1)
      $query_resolvida = ResolveSelectTemplate($query_template, $campos_restricao);
      
      // Executa o select
      $id = EnviarNC($sock, $query_resolvida);

      // Obtemos os valores e resolvemos o caminho, substituindo
      // {Tabela.campo_selecionado} pelos valores obtidos da query
      if (!$id)
      {
        $query_template = MontaSelectCaminho($sock, $caminho_variavel, $campos_restricao);

        AtualizaSelectTemplate($sock, $query_template);
        
        $query_resolvida = ResolveSelectTemplate($query_template, $campos_restricao);
        $id = EnviarNC($sock, $query_resolvida);
      }

      if (RetornaNumLinhas($id) == 1)
      {
        $linha = RetornaLinha($id);

        $campos_selecionados = $linha;
        $caminho_resolvido = ResolveCaminhoVariavel($caminho, $campos_selecionados);

        return (UnixCaminho($caminho_resolvido));
      }
      else
      {
        // 64 - ERRO: a consulta para obter informaï¿½ï¿½es utilizadas na montagem do caminho para extraï¿½ï¿½o de cursos nï¿½o retornou dados.
        die(RetornaFraseDaLista($lista_frases, 64));
        return(-6);
      }
    }
    else
    {
      // 65 - ERRO: o template para a montagem do caminho para extraï¿½ï¿½o de cursos nï¿½o possui seï¿½ï¿½o variï¿½vel. Por favor verifique o valor armazenado na tabela 'Extraï¿½ï¿½o'.
      die(RetornaFraseDaLista($lista_frases, 65));
      return(-7);
    }
    
  }
  
   /* *********************************************************************
   RetornaCaminhoExtracao2 - substitui o template do select com os valores
        dos campos selecionados.
   Entradas: $sock - socket da base de dados do AMBIENTE
             $campos_restricao - array com nomes dos campos utilizados para restringir
                   a consulta (na clausula where)
   Saida: caminho resolvida:
             '/fixo1/fixo2/valor1/valor5/...'
             ou codigos de erro
  */
  function RetornaCaminhoExtracao2($sock, $campos_restricao)
  {
    // Obtem o caminho da tabela
    $query_template = RetornaSelectTemplate2($sock);
    if($query_template==-1)
       return(-1);       /*tabela Extracao*/


    $caminho = RetornaCaminhoVariavel2($sock);
    if($caminho==-1)
       return(-1);      /*tabela Extracao*/



    $caminho_variavel = ExtraiCaminhoVariavel($caminho);
    // Verifica a existencia de parte configuravel do caminho: OBRIGATORIA a existencia
    //if (ereg("\{[^}]+\}", $caminho))
    if (is_array($caminho_variavel))
    {
      if ($query_template == "")
      {
        $query_template = MontaSelectCaminho2($sock, $caminho_variavel, $campos_restricao);
        if((ErroSql($sock)!=0)||($query_template<0))
        {
          return($query_template);
        }
        AtualizaSelectTemplate($sock, $query_template);
        if(ErroSql($sock)!=0)
          return(-1);
        
      }
      // Substitui os valores para os campos de restriï¿½ï¿½o (where campo_restricao1 = valor1)
      $query_resolvida = ResolveSelectTemplate($query_template, $campos_restricao);

      // Executa o select
      $id = EnviarNC($sock, $query_resolvida);

      // Obtemos os valores e resolvemos o caminho, substituindo
      // {Tabela.campo_selecionado} pelos valores obtidos da query
      if (!$id)
      {
        $query_template = MontaSelectCaminho2($sock, $caminho_variavel, $campos_restricao);
         if((ErroSql($sock)!=0)||($query_template<0))
        {
          return($query_template);
        }

        AtualizaSelectTemplate($sock, $query_template);
        if(ErroSql($sock)!=0)
          return(-1);

        $query_resolvida = ResolveSelectTemplate($query_template, $campos_restricao);
        $id = EnviarNC($sock, $query_resolvida);
        if(ErroSql($sock)!=0)
          return(-3);    /*Qual tabela?*/
      }

      if (RetornaNumLinhas($id) == 1)
      {
        $linha = RetornaLinha($id);

        $campos_selecionados = $linha;
        $caminho_resolvido = ResolveCaminhoVariavel($caminho, $campos_selecionados);

        return (UnixCaminho($caminho_resolvido));
      }
      else
      {
      /*die("ERRO: consulta nao obtida");*/
        return(-6);
      }
    }
    else
    {
      /*die("ERRO: caminho invalido para extracao");*/
      return(-7);
    }

  }


  /* *********************************************************************
   RetornaCaminhoVariavel - obtem o caminho variavel.
   Entradas: $sock - socket da base de dados do AMBIENTE
   Saida: caminho variavel:
             '/fixo1/fixo2/{Tabela1.campo1}/{Tabela2.valor5}/...'
  */
  function RetornaCaminhoVariavel($sock)
  {
    $query = "select valor from Extracao where item = 'diretorio'";
    $id = Enviar($sock, $query);
    $linha = RetornaLinha($id);
    return ($linha[0]);
  }
  
  /* *********************************************************************
   RetornaCaminhoVariavel2 - obtem o caminho variavel.
   Entradas: $sock - socket da base de dados do AMBIENTE
   Saida: caminho variavel:
             '/fixo1/fixo2/{Tabela1.campo1}/{Tabela2.valor5}/...'
  */
  function RetornaCaminhoVariavel2($sock)
  {
    $query = "select valor from Extracao where item = 'diretorio'";
    $id = EnviarNC($sock, $query);
    if(ErroSql($sock)!=0)
     return(-1);
    $linha = RetornaLinha($id);
    return ($linha[0]);
  }
  
  /* *********************************************************************
   AtualizaCaminhoVariavel - atualiza o caminho variavel.
   Entradas: $sock - socket da base de dados do AMBIENTE
             $caminho - caminho variavel no formato:
                 '/fixo1/fixo2/{Tabela1.campo1}/{Tabela2.valor5}/...'
   Saida: true, se bem sucedido, do contrario, false.
  */
   function AtualizaCaminhoVariavel($sock, $caminho)
   {
     // Limpamos o template do select para que possa ser remontado "automaticamente"
     AtualizaSelectTemplate($sock, "");

     $query = "update Extracao set valor = '".VerificaStringQuery($caminho)."' where item = 'select_query'";
     return(Enviar($sock, $query));
   }

   function RetornaRaizCaminhoVariavel($caminho)
   {
     preg_match("/\{[^}]+\}/", $caminho, $matches, PREG_OFFSET_CAPTURE);
     if (is_array($matches))
     {
       $raiz = substr($caminho, 0, $matches[0][1]);
       $raiz = rtrim($raiz, "/");
       return($raiz);
     }
   }
  
  /* *********************************************************************
   RetornaSelectTemplate - obtem o template do select.
   Entradas: $sock - socket da base de dados do AMBIENTE
   Saida: template de select:
             'select campo1, campo2 from Tabela1 where campo5 = ']campo5['
  */
  function RetornaSelectTemplate($sock)
  {
    $query = "select valor from Extracao where item = 'select_query'";
    $id = Enviar($sock, $query);
    $linha = RetornaLinha($id);
    return ($linha[0]);
  }
  
   /* *********************************************************************
   RetornaSelectTemplate2 - obtem o template do select.
   Entradas: $sock - socket da base de dados do AMBIENTE
   Saida: template de select:
             'select campo1, campo2 from Tabela1 where campo5 = ']campo5['
  */
  function RetornaSelectTemplate2($sock)
  {
    $query = "select valor from Extracao where item = 'select_query'";
    $id=EnviarNC($sock, $query);
    if(ErroSql($sock)!=0)
    {
       return (-1);
    }
    $linha = RetornaLinha($id);
    return ($linha[0]);

  }
  
  
   /* *********************************************************************
   AtualizaSelectTemplate - atualiza o template do select.
   Entradas: $sock - socket da base de dados do AMBIENTE
             $query_template - caminho variavel no formato:
                 'select campo1, campo2 from Tabela1 where campo5 = ']campo5['
   Saida: true, se bem sucedido, do contrario, false.
  */
   function AtualizaSelectTemplate($sock, $query_template)
   {
     $query = "update Extracao set valor = '".addslashes($query_template)."' where item = 'select_query'";
     return(EnviarNC($sock, $query));
   }
   
/***********************************************************
   RetornaListaCamposExtracao- Retorna dados dos campos de uma
     tabela
   Entrada: $sock - sock da base em uso
            $tabela - nome da tabela
   Saida: array com dados dos campos da tabela
*/
function RetornaListaCamposExtracao($sock, $tabela)
{
  $ar="";
  unset($ar);

  $query = "select * from ".$tabela." limit 1";

  $id = EnviarNC($sock, $query);
   if(ErroSql($sock)!=0)
    {
      return(-1);
    }
  $num = mysql_num_fields($id);
  $i = 0;
  while ($i < $num)
  {
    $field = mysql_fetch_field($id);
    $ar[$i] = array("name" => $field->name,
                    "table" => $field->table,
                    "max_length" => $field->max_length,
                    "not_null" => $field->not_null,
                    "primary_key" => $field->primary_key,
                    "unique_key" => $field->unique_key,
                    "multiple_key" => $field->multiple_key,
                    "numeric" => $field->numeric,
                    "blob" => $field->blob,
                    "type" => $field->type,
                    "unsigned" => $field->unsigned,
                    "zerofill" => $field->zerofill);
    $i++;
  }
  return ($ar);
}

?>
