<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bibliotecas/conversor.inc

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
  ARQUIVO : bibliotecas/conversor.inc
  ========================================================== */
  
/* **********************************************************************
   AtualizaTabelasVersaoCurso - Atualiza as tabelas para a vers�o atual
   Entrada: $sock - Socket da conex�o
            $tabelas - array contendo os nomes das tabelas ou "" para todas
            $versao - Atual vers�o do curso
   Saida: nenhuma
*/
function AtualizaTabelasVersaoCurso($sock, $tabelas, $versao)
{
  /*   Aqui atualizamos a base de dados do curso que est� sendo inserido.  */
  /*   As altera��es necess�rias foram retiradas dos arquivos              */
  /* patch-v3.x.y.inc de cada patch.
  /*   obs: os cases n�o possuem break porque se um curso est� numa vers�o */
  /* antiga deve ser atualizado at� chegar � vers�o atual, em sequ�ncia.   */

  switch ($versao)
  {
    case '3.0': // atualiza base de dados para vers�o 3.0.1
      $query = "select * from Cursos";
      $res = Enviar($sock,$query);
      $linha = RetornaLinha($res);
      if (!isset($linha['_timestamp']))
      {
        if (($tabelas == "") ||
            ((is_array($tabelas)) && (in_array("Cursos", $tabelas)))
           )
        {
          $query = "alter table Cursos add column _timestamp TIMESTAMP";
          Enviar($sock,$query);
        }
        if (($tabelas == "") ||
            ((is_array($tabelas)) && (in_array("Curso_ferramentas", $tabelas)))
           )
        {
          $query = "alter table Curso_ferramentas add column _timestamp TIMESTAMP";
          Enviar($sock,$query);
        }
      }
    case '3.0.1': /*atualiza base de dados para vers�o 3.0.2 */
      /* patch 3.0.2 n�o altera base dos cursos */

    case '3.0.2': /*atualiza base de dados para vers�o 3.0.3 */
      $query="select * from Correio_intermap limit 1";
      $res=Enviar($sock,$query);
      $linha=RetornaLinha($res);
      if (!isset($linha['data']))
      {
        if (($tabelas == "") ||
            ((is_array($tabelas)) && (in_array("Correio_intermap", $tabelas)))
           )
        {
          $query="alter table Correio_intermap add column data INT";
          Enviar($sock,$query);

          $query="select cod_msg, data from Correio_mensagens";
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
      }
    case '3.0.3': /*atualiza base de dados para vers�o 3.0.4 */
      /* patch 3.0.4 n�o altera base dos cursos */

    case '3.0.4': /*atualiza base de dados para vers�o 3.0.5 */
      /* patch 3.0.5 n�o altera base dos cursos */

    case '3.0.5': /*atualiza base de dados para vers�o 3.0.6 */
      /* patch 3.0.6 n�o altera base dos cursos */

    case '3.0.6': /*atualiza base de dados para vers�o 3.0.7 */
      /* patch 3.0.7 n�o altera base dos cursos */

    case '3.0.7': /*atualiza base de dados para vers�o 3.0.8 */
      /* patch 3.0.8 n�o altera base dos cursos */

    case '3.0.8': /*atualiza base de dados para vers�o 3.1 */
      $query="select * from Cursos";
      $res=Enviar($sock,$query);
      $linha=RetornaLinha($res);
      if (!isset($linha['cod_lingua']))
      {
        if (($tabelas == "") ||
            ((is_array($tabelas)) && (in_array("Cursos", $tabelas)))
           )
        {
          $query="alter table Cursos add column cod_lingua INT default 1";
          Enviar($sock,$query);
        }
        if (($tabelas == "") ||
            ((is_array($tabelas)) && (in_array("Usuario", $tabelas)))
           )
        {
           $query="alter table Usuario add column cod_lingua INT default 1";
           Enviar($sock,$query);
        }

        if (($tabelas == "") ||
            ((is_array($tabelas)) && (in_array("Curso_ferramentas", $tabelas)))
           )
        {
          $query="update Curso_ferramentas set status='D' where cod_ferramenta=19";
          Enviar($sock,$query);
        }
        if ($tabelas == "")
        {
          $query="CREATE TABLE Config (item varchar(20),valor text)";
          Enviar($sock,$query);
          $query="INSERT INTO Config values ('status_coordenador','C')";
          Enviar($sock,$query);
        }
      }

    case '3.1': /*atualiza base de dados para vers�o 3.1.1 */
      if (($tabelas == "") ||
          ((is_array($tabelas)) && (in_array("Usuario", $tabelas)))
         )
      {
        $query = "update Usuario set cod_lingua = 1 where cod_lingua is NULL";
        $res = Enviar($sock, $query);
      }
    case '3.1.1': /*atualiza base de dados para vers�o 3.1.2 */
      if (($tabelas == "") ||
          ((is_array($tabelas)) && (in_array("Usuario", $tabelas)))
         )
      {
        $query = "update Usuario set cod_lingua = 1 where cod_lingua is NULL";
        $res = Enviar($sock, $query);
      }
    case '3.1.2': /*atualiza base de dados para vers�o 3.1.3 */
      if (($tabelas == "") ||
          ((is_array($tabelas)) && (in_array("Diario_itens", $tabelas)))
         )
      {
        $query = "ALTER TABLE Diario_itens ADD COLUMN tipo_compartilhamento CHAR(1) DEFAULT 'F'";
        $res = Enviar($sock, $query);
      }
    case '3.1.3': /*atualiza base de dados para vers�o 3.1.4 */
      /* patch 3.1.4 n�o altera base dos cursos */

    case '3.1.4': /*atualiza base de dados para vers�o 3.1.5 */
      $query = "select UNIX_TIMESTAMP(_timestamp) 'data' from Cursos";
      $res = Enviar($sock,$query);
      $data = RetornaLinha($res);

      $query1 = "ALTER TABLE Cursos MODIFY _timestamp INT(11) DEFAULT NULL";
      $query2 = "UPDATE Cursos SET _timestamp = ".$data['data']." where cod_curso = ".$novo_cod_curso;
      $query3 = "CREATE TABLE Usuario_config (cod_usuario int(11) NOT NULL, notificar_email char(1) NOT NULL default '0', ";
      $query3 .= "notificar_data int(11) default 0, PRIMARY KEY  (cod_usuario))";
      $query4 = "SELECT cod_usuario, nome FROM Usuario";

      /*   parte comentada pois n�o sei como fazer para desconectar do sock passado, conectar-se a outro e depois */
      /* recuperar o antigo */

      //$sock = Conectar("");
      //if (!Enviar($sock,$query2))
        //die("Erro ao atualizar o valor do campo '_timestamp' da tabela Cursos (Externa) do curso \"".$novo_cod_curso."\" na consulta: ".$query2);
      //Desconectar($sock);
      if (($tabelas == "") ||
          ((is_array($tabelas)) && (in_array("Cursos", $tabelas)))
         )
      {
        if (!Enviar($sock, $query1))
          die("Erro ao modificar o tipo do campo '_timestamp' da tabela 'Cursos' (Interna) na consulta: ".$query1);

        if (!Enviar($sock, $query2))
          die("Erro ao atualizar o valor do campo '_timestamp' da tabela Cursos (Interna) do curso \"".$novo_cod_curso."\" na consulta: ".$query2);
      }
      if ($tabelas == "")
      {
        if (!Enviar($sock, $query3))
          die("Erro ao criar a tabela 'Usuario_config' do curso ".$novo_cod_curso." na consulta: ".$query3);

        $res = Enviar($sock, $query4);
        $total_usuarios = RetornaNumLinhas($res);
        $lista_usuarios = RetornaArrayLinhas($res);

        for ($k = 0; $k < $total_usuarios; $k++)
        {
          $query5 = "INSERT INTO Usuario_config VALUES (".($lista_usuarios[$k]['cod_usuario']).", 0, ".time().")";
          if (!Enviar($sock, $query5))
            die("Erro ao inserir dados para notificar usu�rio \"".($lista_usuarios[$k]['nome'])."\"(".($lista_usuarios[$k]['cod_usuario']).") do curso ".$novo_cod_curso." sobre novidades no ambiente na consulta: ".$query5);
        }
      }

    case '3.1.5': /*atualiza base de dados para vers�o 3.1.6 */
      /* patch 3.1.6 n�o altera base dos cursos */

    case '3.1.6': /*atualiza base de dados para vers�o 3.1.7 */
      /* patch 3.1.7 n�o altera base dos cursos */

    case '3.1.7': /*atualiza base de dados para vers�o 3.1.8 */
      if (($tabelas == "") ||
          ((is_array($tabelas)) && (in_array("Curso_ferramentas", $tabelas)))
         )
      {
        $query1 = "SELECT cod_ferramenta, _timestamp FROM Curso_ferramentas";
        $query2 = "ALTER TABLE Curso_ferramentas MODIFY _timestamp int(11) default NULL";

        // Obtém o valor do '_timestamp' para cada ferramenta.
        $res1 = Enviar($sock, $query1);
        $total_ferramentas = RetornaNumLinhas($res1);
        $lista_ferramentas = RetornaArrayLinhas($res1);

        // Modifica o tipo de registro do campo '_timestamp': TIMESTAMP => INT
        $res2 = Enviar($sock, $query2);

        if ($total_ferramentas > 0)
        {
          foreach ($lista_ferramentas as $j => $ferr)
          {
            $query3 = "UPDATE Curso_ferramentas SET _timestamp = ".$ferr['_timestamp']." WHERE cod_ferramenta = ".$ferr['cod_ferramenta'];

            $res3 = Enviar($sock, $query3);
            if (!$res3)
              echo ("N�o foi poss�vel converter o tipo do campo '_timestamp' da tabela 'Curso_ferramentas' do curso: ".$novo_cod_curso."<br>\n");
          }
        }
      }
  }
}

?>