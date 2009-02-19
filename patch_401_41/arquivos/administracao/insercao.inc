<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/insercao/insercao.inc

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
  ARQUIVO : administracao/insercao/insercao.inc
  ========================================================== */

 @include_once ("insercao_fachada.inc");

/* **********************************************************************
   PreparaCabecalho - Monta o Cabecalho padr�o da p�gina da administracao
   Entrada: $mensagem - mensagem a ser exibida quando script � encerrado
   Saida: exibe a mensagem e impede a continua��o da execu��o do script
*/
function EncerraScript($mensagem)
{

  $mensagem = Enter2BR($mensagem);

  echo("<p>".__FILE__.":".__LINE__.": <font color=red>".$mensagem."</font></p><br>");
  echo("</body>\n");
  echo("</html>\n");
  die();
}

/* **********************************************************************
   RetornaDiretorio - Retorna o Diretorio pedido
   Entrada: $item - item da tabela de diretorios a ser retornado
   Saida: Retorna o diretorio
*/
/*
function RetornaDiretorio($item)
{
  $sock=Conectar("");
  $query="select diretorio from Diretorio where item='".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  Desconectar($sock);
  return $linha['diretorio'];
}
*/
/* **********************************************************************
   RetornaCursosInseriveis - Retorna lista de cursos que podem ser inseridos
   Entrada: nenhuma
   Saida: Retorna array com [$cod_curso]=nome_curso
*/
/*
function RetornaCursosInseriveis()
{
  $sock = Conectar("");

  $query = "select codigo, nome, caminho from Cursos_extraidos order by codigo";
  $res = Enviar($sock, $query);
  $cursos = RetornaArrayLinhas($res);

  if (count($cursos) > 0)
    foreach ($cursos as $cod => $curso)
    {
      if (is_dir($curso['caminho']))
         $lista[$curso['codigo']] = $curso['nome'];
      else
      {
         $query1 = "delete from Cursos_extraidos where codigo=".$curso['codigo'];
         $query2 = "delete from Cursos_extraidos_espelho where codigo=".$curso['codigo'];
         $query3 = "delete from Cursos_extraidos_compart where codigo=".$curso['codigo'];
         Enviar($sock, $query1);
         Enviar($sock, $query2);
         Enviar($sock, $query3);
      }  
    }
  Desconectar($sock);

  return $lista;
}
*/

/* **********************************************************************
   RetornaCategorias - Retorna as categorias que um curso pode pertencer
   Entrada: nenhum
   Saida: array com [$cod_pasta]='nome da pasta'
*/
/*
function RetornaCategorias()
{
  $sock=Conectar("");
  $query="select * from Cursos_pastas order by pasta";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  Desconectar($sock);
  if (count($lista)>0)
    foreach($lista as $cod => $linha)
      $saida[$linha['cod_pasta']]=$linha['pasta'];
  return $saida;
}
*/

/* *************************************************************************
   SelecionarCategoria - Permite que o usuario selecione uma categoria para
                         o curso inserido

   Entrada: $cod_pasta_curso - codigo atual da categoria do curso a ser inserido
   Saida: nenhum
*/

function SelecionarCategoria($cod_pasta_curso)
{
  global $novo_cod_curso;
  global $lista_frases;

  $categ=RetornaCategorias();
  if (count($categ)>0)
  {
    // 255 - Selecione uma nova categoria para o curso inserido
    echo ("\n"."<br>".RetornaFraseDaLista($lista_frases,255)."<br>");

    echo("<form action='inserir_curso3.php?".RetornaSessionID()."&acao=novo' method=get>\n");
    echo("<select name=nova_categoria>\n");

    foreach ($categ as $codigo_pasta => $pasta)
    {
      if ($codigo_pasta == $cod_pasta_curso)
      {
        echo("  <option value=".$codigo_pasta." SELECTED>".$pasta."</option>\n");
      }
      else
      {
        echo("  <option value=".$codigo_pasta.">".$pasta."</option>\n");
      }
    }

    echo("</select><br><br>\n");

    echo("<input type=hidden name=cod_curso value =".$novo_cod_curso.">\n");

    // 256 - Selecionar Categoria
    echo("<input type=submit value='".RetornaFraseDaLista($lista_frases,256)."'>\n");
  }
  else
  {
    // 132 - Nenhuma categoria foi cadastrada!
    echo(RetornaFraseDaLista($lista_frases,132)."<br><br>");
  }
}


/* **********************************************************************
   AtualizaVersaoCurso - Atualiza as tabelas para a vers�o atual
   Entrada: $sock - Socket da conex�o
            $versao - Atual vers�o do curso
   Saida: nenhuma
*/
/*
function AtualizaVersaoCurso($sock,$versao)
{
  //   Aqui atualizamos a base de dados do curso que est� sendo inserido.
  //   As altera��es necess�rias foram retiradas dos arquivos
  // patch-v3.x.y.inc de cada patch.
  //   obs: os cases n�o possuem break porque se um curso est� numa vers�o
  // antiga deve ser atualizado at� chegar � vers�o atual, em sequ�ncia.

  switch ($versao)
  {
    case '3.0': // atualiza base de dados para vers�o 3.0.1
      $query="select * from Cursos";
      $res=Enviar($sock,$query);
      $linha=RetornaLinha($res);
      if (!isset($linha['_timestamp']))
      {
        $query="alter table Cursos add column _timestamp TIMESTAMP";
        Enviar($sock,$query);
        $query="alter table Curso_ferramentas add column _timestamp TIMESTAMP";
        Enviar($sock,$query);
      }

    case '3.0.1': // atualiza base de dados para vers�o 3.0.2
      // patch 3.0.2 n�o altera base dos cursos

    case '3.0.2': // atualiza base de dados para vers�o 3.0.3
      $query="select * from Correio_intermap limit 1";
      $res=Enviar($sock,$query);
      $linha=RetornaLinha($res);
      if (!isset($linha['data']))
      {
        $query="alter table Correio_intermap add column data INT";
        Enviar($sock,$query);

        $query="select cod_msg,data from Correio_mensagens";
        $res=Enviar($sock,$query);
        $lista1=RetornaArrayLinhas($res);
        if (count($lista1)>0)
        {
          foreach($lista1 as $cod => $linha)
          {
            $query="update Correio_intermap set data=".$linha['data']." where cod_msg=".$linha['cod_msg'];
            Enviar($sock,$query);
          }
        }
      }

    case '3.0.3': // atualiza base de dados para vers�o 3.0.4
      // patch 3.0.4 n�o altera base dos cursos

    case '3.0.4': // atualiza base de dados para vers�o 3.0.5
      // patch 3.0.5 n�o altera base dos cursos

    case '3.0.5': // atualiza base de dados para vers�o 3.0.6
      // patch 3.0.6 n�o altera base dos cursos

    case '3.0.6': // atualiza base de dados para vers�o 3.0.7
      // patch 3.0.7 n�o altera base dos cursos

    case '3.0.7': // atualiza base de dados para vers�o 3.0.8
      // patch 3.0.8 n�o altera base dos cursos

    case '3.0.8': // atualiza base de dados para vers�o 3.1
      $query="select * from Cursos";
      $res=Enviar($sock,$query);
      $linha=RetornaLinha($res);
      if (!isset($linha['cod_lingua']))
      {
        $query="alter table Cursos add column cod_lingua INT default 1";
        Enviar($sock,$query);
        $query="alter table Usuario add column cod_lingua INT default 1";
        Enviar($sock,$query);
        $query="update Curso_ferramentas set status='D' where cod_ferramenta=19";
        Enviar($sock,$query);
        $query="CREATE TABLE Config (item varchar(20),valor text)";
        Enviar($sock,$query);
        $query="INSERT INTO Config values ('status_coordenador','C')";
        Enviar($sock,$query);
      }

    case '3.1': // atualiza base de dados para vers�o 3.1.1
      $query = "update Usuario set cod_lingua = 1 where cod_lingua is NULL";
      $res = Enviar($sock, $query);

    case '3.1.1': // atualiza base de dados para vers�o 3.1.2
      $query = "update Usuario set cod_lingua = 1 where cod_lingua is NULL";
      $res = Enviar($sock, $query);

    case '3.1.2': // atualiza base de dados para vers�o 3.1.3
      $query = "ALTER TABLE Diario_itens ADD COLUMN tipo_compartilhamento CHAR(1) DEFAULT 'F'";
      $res = Enviar($sock, $query);

    case '3.1.3': // atualiza base de dados para vers�o 3.1.4
      // patch 3.1.4 n�o altera base dos cursos

    case '3.1.4': // atualiza base de dados para vers�o 3.1.5
      $query = "select UNIX_TIMESTAMP(_timestamp) 'data' from Cursos";
      $res = Enviar($sock,$query);
      $data = RetornaLinha($res);

      $query1 = "ALTER TABLE Cursos MODIFY _timestamp INT(11) DEFAULT NULL";
      $query2 = "UPDATE Cursos SET _timestamp = ".$data['data']." where cod_curso = ".$novo_cod_curso;
      $query3 = "CREATE TABLE Usuario_config (cod_usuario int(11) NOT NULL, notificar_email char(1) NOT NULL default '0', ";
      $query3 .= "notificar_data int(11) default 0, PRIMARY KEY  (cod_usuario))";
      $query4 = "SELECT cod_usuario, nome FROM Usuario";

      // parte comentada pois n�o sei como fazer para desconectar do sock passado, conectar-se a outro e depois
      // recuperar o antigo

      //$sock = Conectar("");
      //if (!Enviar($sock,$query2))
        //die("Erro ao atualizar o valor do campo '_timestamp' da tabela Cursos (Externa) do curso \"".$novo_cod_curso."\" na consulta: ".$query2);
      //Desconectar($sock);

      if (!Enviar($sock,$query1))
        die("Erro ao modificar o tipo do campo '_timestamp' da tabela 'Cursos' (Interna) na consulta: ".$query1);

      if (!Enviar($sock, $query2))
        die("Erro ao atualizar o valor do campo '_timestamp' da tabela Cursos (Interna) do curso \"".$novo_cod_curso."\" na c
onsulta: ".$query2);

      if (!Enviar($sock, $query3))
        die("Erro ao criar a tabela 'Usuario_config' do curso ".$novo_cod_curso." na consulta: ".$query3);

      $res = Enviar($sock, $query4);
      $total_usuarios = RetornaNumLinhas($res);
      $lista_usuarios = RetornaArrayLinhas($res);

      for ($k = 0; $k < $total_usuarios; $k++)
      {
        $query5 = "INSERT INTO Usuario_config VALUES (".($lista_usuarios[$k]['cod_usuario']).", 0, ".time().")";
        if (!Enviar($sock, $query5))
          die("Erro ao inserir dados para notificar usuário \"".($lista_usuarios[$k]['nome'])."\"(".($lista_usuarios[$k]['co
dusuario']).") do curso ".$novo_cod_curso." sobre novidades no ambiente na consulta: ".$query5);
      }

    case '3.1.5': // atualiza base de dados para vers�o 3.1.6
      // patch 3.1.6 n�o altera base dos cursos

    case '3.1.6': // atualiza base de dados para vers�o 3.1.7
      // patch 3.1.7 n�o altera base dos cursos

    case '3.1.7': // atualiza base de dados para vers�o 3.1.8
      $query1 = "SELECT cod_ferramenta, _timestamp FROM Curso_ferramentas";
      $query2 = "ALTER TABLE Curso_ferramentas MODIFY _timestamp int(11) default NULL";

      // Obt�m o valor do '_timestamp' para cada ferramenta.
      $res1 = Enviar($sock, $query1);
      $total_ferramentas = RetornaNumLinhas($res1);
      $lista_ferramentas = RetornaArrayLinhas($res1);

      // Modifica o tipo de registro do campo '_timestamp': TIMESTAMP => INT
      $res2 = Enviar($sock, $query2);

      if ($total_ferramentas > 0)
      {
        foreach ($lista_ferramentas as $j => $ferr)
        {
          $query3 = "UPDATE Curso_ferramentas SET _timestamp = ".$ferr['_timestamp']." WHERE cod_ferramenta = ".$ferr['cod_fe
rramenta'];

          $res3 = Enviar($sock, $query3);
          if (!$res3)
            echo ("N�o foi poss�vel converter o tipo do campo '_timestamp' da tabela 'Curso_ferramentas' do curso: ".$novo_cod_curso."<br>\n");
        }
      }
  }
}
*/

/* **********************************************************************
   RetornaCaminhoCursoExtraido - Retorna o caminho do curso extra�do que ser� inserido
   Entrada: $sock - Socket da conex�o
            $codigo - C�digo do curso na tabela de extra�dos
   Saida: caminho do curso
*/
function RetornaCaminhoCursoExtraido($sock, $codigo)
{
  $query = "select caminho from Cursos_extraidos where codigo=".$codigo;
  $res = Enviar($sock, $query);
  $linha = RetornaLinha($res);

  return $linha['caminho'];
}

/* **********************************************************************
   LeCursoResumo - Le o arquivo de resumo do curso
   Entrada: $arquivo - caminho e nome do arquivo
   Saida: array['id_curso']
               ['nome_curso']
               ['curso_categoria']
               ['curso_inicio']
               ['curso_fim']
               ['versao_teleduc']
               ['ferr_compart'][3]
                               [7]
                               [10]
*/
function LeCursoResumo($arquivo)
{
  if (ExisteArquivo($arquivo))
  {
    if (!($fh = fopen($arquivo, "r")))   
      die(__FUNCTION__.": erro ao abrir o arquivo: ".$arquivo);
    else
    {
      while (!feof($fh))
      {
        $linha = fgets($fh, 256);
        $partes = explode("=", $linha, 2);
        $conteudo = rtrim($partes[1], "\n");
        if ($partes[0] == "id_curso")
          $resultado['id_curso'] = $conteudo;
        if ($partes[0] == "nome_curso")
          $resultado['nome_curso'] = $conteudo;
        if ($partes[0] == "curso_categoria")
          $resultado['curso_categoria'] = $conteudo;
        if ($partes[0] == "curso_inicio")
          $resultado['curso_inicio'] = $conteudo;
        if ($partes[0] == "curso_fim")
          $resultado['curso_fim'] = $conteudo;
        if ($partes[0] == "versao_teleduc")
          $resultado['versao_teleduc'] = $conteudo;
        if ($partes[0] == "ferr_compart")
          $ferr = $conteudo;
        if ($partes[0] == "cod_coordenador")
          $resultado['cod_coordenador'] = $conteudo;

      }
      fclose($fh);
      $resultado['ferr_compart'] = explode(",", $ferr);
      return ($resultado);
    }
  }
}


/* **********************************************************************
   CriaArquivoCursoResumoDump - Cria o arquivo de resumo do curso a partir
       dos dumps das bases de dados do ambiente e do curso.
   Entrada: $ambiente_dump - caminho e nome do arquivo do dump do ambiente
            $curso_dump    - caminho e nome do arquivo do dump do curso
            $resumo        - caminho e nome do arquivo de resumo do curso
   Saida: true, se criou, false do contr�rio.
*/
function CriaArquivoCursoResumoDump($ambiente_dump, $curso_dump, $resumo)
{
  if (function_exists("CriaArquivoCursoResumoDumpFachada"))
  {
    return(CriaArquivoCursoResumoDumpFachada($ambiente_dump, $curso_dump, $resumo));
  }

  // Definimos as tabelas do dump do curso para chamada
  // � fun��o ExtraiValorDeCampoDump
  $tbl1['Cursos'] = array('cod_curso',
                          'nome_curso',
                          'curso_inicio',
                          'curso_fim',
                          'cod_pasta');


  // Definimos as tabelas do dump do ambiente para chamada
  // � fun��o ExtraiValorDeCampoDump
  $tbl2['Config'] = array('item',
                          'valor');
  $tbl2['Cursos_pastas'] = array('cod_pasta',
                                'pasta');
  $tbl2['Cursos_compart'] = array('cod_curso',
                                  'cod_ferramenta');

  if ((is_array($res1 = ExtraiValorDeCampoDump($curso_dump, $tbl1))) &&
      (is_array($res2 = ExtraiValorDeCampoDump($ambiente_dump, $tbl2))))
  {

    // Criamos o arquivo de resumo
    if ($fp = fopen($resumo, "w"))
    {

      // Obtemos o valor para identifica��o
      $id_curso = $res1['Cursos']['cod_curso']['valores'][0];

      // Obtemos o nome do curso
      $nome_curso = trim($res1['Cursos']['nome_curso']['valores'][0], "'");

      // Obtemos a categoria do curso
      $cod_pastas = $res2['Cursos_pastas']['cod_pasta']['valores'];
      if (($total_pastas = count($cod_pastas)) > 0)
      {
        for ($i = 0; $i < $total_pastas; $i++)
        {
          if ($cod_pastas[$i] == $res1['Cursos']['cod_pasta']['valores'][0])
          {
            $idx_pasta = $i;
            break;
          }
        }
        $curso_categoria = trim($res2['Cursos_pastas']['pasta']['valores'][$idx_pasta], "'");
      }

      // Obtemos o inicio do curso
      $curso_inicio = $res1['Cursos']['curso_inicio']['valores'][0];

      // Obtemos o fim do curso
      $curso_fim = $res1['Cursos']['curso_fim']['valores'][0];

      // Obtemos a vers�o do TelEduc do qual foi extraido
      $config_itens = $res2['Config']['item']['valores'];
      if (($total_itens = count($config_itens)) > 0)
      {
        for ($i = 0; $i < $total_itens; $i++)
        {
          if ($config_itens[$i] == "'versao'")
          {
            $idx_item = $i;
            break;
          }
        }
        $versao_teleduc = trim($res2['Config']['valor']['valores'][$idx_item], "'");
      }

      // Obtemos as ferramentas que compartilhava
      $cod_cursos = $res2['Cursos_compart']['cod_curso']['valores'];
      if (($total_cursos = count($cod_cursos)) > 0)
      {
        for ($i = 0; $i < $total_cursos; $i++)
        {
          if ($cod_cursos[$i] == $res1['Cursos']['cod_curso']['valores'][0])
          {
            $cod_ferramentas[] = $res2['Cursos_compart']['cod_curso']['valores'][$i];
          }
        }
        /*
        $cod_ferramentas = array_slice($res2['Cursos_compart']['cod_ferramenta']['valores'],
                                     reset($idx_ferr),
                                     count($idx_ferr));
        */
        $ferr_compart = implode(",", $cod_ferramentas);
      }

      // Formatamos o texto
      $texto = "id_curso=".$id_curso."\n";
      $texto .= "nome_curso=".$nome_curso."\n";
      $texto .= "curso_categoria=".$curso_categoria."\n";
      $texto .= "curso_inicio=".$curso_inicio."\n";
      $texto .= "curso_fim=".$curso_fim."\n";
      $texto .= "versao_teleduc=".$versao_teleduc."\n";
      $texto .= "ferr_compart=".$ferr_compart;

      fwrite($fp, $texto);
      fclose($fp);
      return true;
    }
  }

  return false;
}


/* **********************************************************************
   FinalizaInsercao - Realiza p�s-processamento da inser��o de cursos.

   Entrada: $ARQUIVO_CURSO_ANTIGO (VARI�VEL GLOBAL) - caminho para o arquivo
                de dump do curso gerado na extra��o do curso (ainda sem associa��o
                com o novo c�digo para o curso).
            $ARQUIVO_AMBIENTE (VARI�VEL GLOBAL) - caminho para o arquivo de
                dump do ambiente;
            $novo_cod_curso (VARI�VEL GLOBAL) - novo c�digo de curso reservado para
                o curso a ser inserido
            $args - quaisquer argumentos.

   Saida: nenhuma.

*/
function FinalizaInsercao($args)
{
  global $ARQUIVO_CURSO_ANTIGO, $ARQUIVO_AMBIENTE, $novo_cod_curso;

  if (function_exists("FinalizaInsercaoFachada"))
  {
    return(FinalizaInsercaoFachada($args));
  }
}

/* **********************************************************************
   AtualizaTabelasSequencia - Inicializa as tabelas sequencia com o MAX(cod_...)

   Entrada: $cod_curso  - codigo do curso cujas tabelas _sequencia serao atualizadas 

   Saida: nenhuma.

*/
function AtualizaTabelasSequencia($cod_curso)
{
  $sock=Conectar($cod_curso);
  $query = "show tables like \"%_sequencia\"";
 
  $res =  Enviar($sock, $query); 
  $tabelas_sequencia = RetornaArrayLinhas($res);
  
  foreach ($tabelas_sequencia as $tabela_seq)
  {
     $tabela=$tabela_seq[0];
    if($tabela=="Correio_mensagens_sequencia")
      $query = "select MAX(cod_msg) from Correio_mensagens";  
    elseif($tabela=="Usuario_sequencia")
      $query = "select MAX(cod_usuario) from Usuario";  
    elseif($tabela=="Avaliacao_sequencia")
      $query = "select MAX(cod_avaliacao) from Avaliacao";  
    elseif($tabela=="Avaliacao_notas_sequencia")
      $query = "select MAX(cod_nota) from Avaliacao_notas";  
    elseif($tabela=="Grupos_sequencia")
      $query = "select MAX(cod_grupo) from Grupos";
    elseif($tabela=="Exercicios_alternativa_sequencia")
    {
          $query = "select MAX(cod_alternativa) from Exercicios_alternativa_objetiva";
	  $res = Enviar($sock, $query);
	  $maximo_obj = RetornaLinha($res);
          if($maximo_obj[0]=="")
              $maximo_obj=0;
		      

	  $query = "select MAX(cod_alternativa) from Exercicios_resp_dissertativa";
	  $res = Enviar($sock, $query);
	  $maximo_dis = RetornaLinha($res);
	  if($maximo_dis[0]=="")
	    $maximo_dis=0;

	  if($maximo_obj[0]>$maximo_dis[0])
            $maximo=$maximo_obj[0];
	  else
	    $maximo=$maximo_dis[0];
	    
	  if($maximo!="")
          {
              $query1="insert into ".$tabela_seq[0]." (cod,cod_usuario,data) values (".$maximo.",0,1)";
              $query2="delete from ".$tabela_seq[0]." where  cod=".$maximo;
              $res = Enviar($sock, $query1);
	      $res = Enviar($sock, $query2);
	  }
	  continue;
			      
    }
    elseif($tabela=="Exercicios_modelo_sequencia")
      $query = "select MAX(cod_modelo) from Exercicios_modelo";
    elseif($tabela=="Exercicios_questao_sequencia")
      $query = "select MAX(cod_quest) from Exercicios_questao";
    elseif($tabela=="Exercicios_resolucao_sequencia")
      $query = "select MAX(cod_resolucao) from Exercicios_resolucao";
    elseif($tabela=="Exercicios_topico_sequencia")
      $query = "select MAX(cod_topico) from Exercicios_topico";
    elseif($tabela=="Exercicios_alternativa_objetiva_sequencia")
      $query = "select MAX(cod_alternativa) from Exercicios_alternativa_objetiva";
    else
    { 
    $tabela1=explode("_",$tabela_seq[0]);
    $total=count($tabela1);
    $tabela2=$tabela1[0];
    if($total==2)
    {
      $tabela2=$tabela2."_".$tabela1[1];
    }
    if($total>2)
    {
      for($i=1;$i<=($total-2);$i++)
      {
        $tabela2=$tabela2."_".$tabela1[$i];
      }
    }
    $query = "select MAX(cod_item) from ".$tabela2;  
    }
    $res = Enviar($sock, $query);
    $maximo = RetornaLinha($res);
    if($maximo[0]!="")
      {
      $query1="insert into ".$tabela_seq[0]." (cod,cod_usuario,data) values (".$maximo[0].",0,1)";
      $query2="delete from ".$tabela_seq[0]." where  cod=".$maximo[0];
 
      $res = Enviar($sock, $query1);
      $res = Enviar($sock, $query2);
      }
 } 
}

/* **********************************************************************
   PreparaCabecalhoInsercao - Monta o Cabecalho padr�o da p�gina da administracao
   Entrada: Nenhuma
   Saida: Monta o cabe�alho no HTML
*/
 function PreparaCabecalhoInsercao($lista_frases)
 {
  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  echo("<head>\n");
  /* 1- Administra��o */
  echo("<title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title>\n");
  echo("<meta name=\"robots\" content=\"follow,index\" />\n");
  echo("<meta name=\"description\" content=\"\" />\n");
  echo("<meta name=\"keywords\" content=\"\" />\n");
  echo("<meta name=\"owner\" content=\"\" />\n");
  echo("<meta name=\"copyright\" content=\"\" />\n");
  echo("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n");
  echo("<link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo("</head>\n");

  echo("<body text=#000000 bgcolor=#FFFFFF link=#0000FF vlink=#0000FF ALINK=#0000FF>\n");
  echo("<a name=\"topo\"></a>\n");
  echo("<h1><a href=\"index.htm\"><img src=\"../imgs/logo.gif\" border=\"0\" alt=\"TelEduc . Educa&ccedil;&atilde;o a Dist&acirc;ncia\" /></a></h1>\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"container\">\n");
  echo("<tr>\n");
  echo("<td></td>\n");
  echo("<td valign=\"top\" id=\"topo\"><!--NAVEGACAO NIVEL 3-->\n");
  echo("<h3>".RetornaFraseDaLista($lista_frases,2)."</h3>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("<td valign=\"top\"><!--NAVEGACAO PRINCIPAL-->\n");
  echo("<ul id=\"nav\">\n");

  /* 3 - Cria��o de Curso */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,3),"index_criar_curso.php","");

  /* 125 - Editar Categorias */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,125),"editar_categoria.php","");

  /* 131 - Selecionar Categoria dos Cursos */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,131),"selecionar_categoria.php","");

  /* 4 - Extra��o de Curso */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,4),"extracao/extrair_curso.php","");

  /* 141 - Inser��o de Cursos Extra�dos */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,141),"insercao/inserir_curso.php","");

  /* 245 - Reutiliza��o de Cursos Encerrados */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,245),"resetar_curso.php","");

  /* 293 - Reenvio de dados aos coordenadores*/
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,293),"infocurso/reenvio.php","");

  /* 9 - Enviar e-mail para usu�rios */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,9),"enviar_email.php","");

  /* 8 - Trocar login */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,8),"trocar_login.php","");

  /* 5 - Consulta a Base de Dados */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,5),"consultar_base.php","");

  /* 153 - Estat�sticas do Ambiente */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,153),"estatistica/index.php","");

  /* 183 - Configurar dados do ambiente */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,183), "configurar.php", "");

  /* 11 - Cadastro de L�nguas */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,11),"cadastro_linguas.php","");

  /* 171 - Cadastro de texto da Ajuda */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,171),"ajuda/index.php","");

  /* 13 - Contato - NIED - Unicamp */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,13),"mailto:curso@nied.unicamp.br","");

  echo("</ul>\n");
  echo("<p align=\"center\"><a href=\"#\"><img src=\"../imgs/logoNied.gif\" alt=\"Nied\" width=\"28\" height=\"38\" border=\"0\" style=\"margin-right:8px;\" /></a> <a href=\"#\"><img src=\"../imgs/logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" width=\"44\" height=\"38\" border=\"0\" style=\"margin-right:6px;\" /></a> <a href=\"#\"><img src=\"../imgs/logoUnicamp.gif\" alt=\"UNICAMP\" width=\"35\" height=\"38\" border=\"0\" /></a></p>\n");
  echo("</td>\n");
  echo("<td width=\"100%\" valign=\"top\" style=\"padding-bottom:40px; padding-top:22px;\">\n");
}

/* **********************************************************************
   Curso_Existe - Verifica se um curso que est� sendo inserido j� n�o existe
   Entrada: o nome do curso e uma lista dos nomes dos cursos
   Saida: o valor maximo deste campo na tabela
 */
function Curso_Existe($dados_curso, $lista_cursos)
{
	foreach ($lista_cursos as $curso){
   	if ((strcmp($curso['nome_curso'], $dados_curso['nome_curso'])==0)&&(strcmp($curso['curso_inicio'], $dados_curso['curso_inicio'])==0)&&(strcmp($curso['curso_fim'], $dados_curso['curso_fim'])==0))
			return (true);
	}
	return (false);
}

/* **********************************************************************
   DesfazInsercao - Desfaz todas as etapas da Insercao de Curso caso ocorra algum erro
   Entrada: sock, o nome do campo e da tabela
   Saida: o valor maximo deste campo na tabela
*/
function DesfazInsercao( $cod_curso, $dbnamecurso, $dir_arquivos)
{
  //nesta funcao precisamos basicamente apagar a base do curso, a entrada do curso na base de dados e a pasta de arquivos
  $sock = Conectar("");
  $query = "DELETE IF EXISTS FROM Cursos WHERE cod_curso=$cod_curso";
  EnviarNC($sock, $query);
		
  $query = "DROP DATABASE IF EXISTS ".$dbnamecurso.$cod_curso;
  EnviarNC($sock, $query);
		
  shell_exec("rm -rf $dir_arquivos/$cod_curso");
		
  echo("<form action='' method='post'> \n");
  echo("<input type=button value='".RetornaFraseDaLista($lista_frases_geral,2)."' onClick='javascript: window.location.href=\"inserir_curso.php\";'>\n");
  echo ("</form>");
}

?>
