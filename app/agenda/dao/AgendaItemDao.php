<?php

require_once '../../../lib/Conexao.php';

 /**
  * Agenda_Item Data Access Object (DAO).
  * This class contains all database handling that is needed to 
  * permanently store and retrieve Agenda_Item object instances. 
  */

class Agenda_ItemDao {


    /**
     * createValueObject-method. This method is used when the Dao class needs
     * to create new value object instance. The reason why this method exists
     * is that sometimes the programmer may want to extend also the valueObject
     * and then this method can be overrided to return extended valueObject.
     * NOTE: If you extend the valueObject class, make sure to override the
     * clone() method in it!
     */
    function createValueObject() {
          return new Agenda_Item();
    }


    /**
     * getObject-method. This will create and load valueObject contents from database 
     * using given Primary-Key as identifier. This method is just a convenience method 
     * for the real load-method which accepts the valueObject as a parameter. Returned
     * valueObject will be created using the createValueObject() method.
     */
    function getObject($conn, $cod_item) {

          $valueObject = $this->createValueObject();
          $valueObject->setCod_item($cod_item);
          $this->load($conn, $valueObject);
          return $valueObject;
    }


    /**
     * load-method. This will load valueObject contents from database using
     * Primary-Key as identifier. Upper layer should use this so that valueObject
     * instance is created and only primary-key should be specified. Then call
     * this method to complete other persistent information. This method will
     * overwrite all other fields except primary-key and possible runtime variables.
     * If load can not find matching row, NotFoundException will be thrown.
     *
     * @param conn         This method requires working database connection.
     * @param valueObject  This parameter contains the class instance to be loaded.
     *                     Primary-key field must be set for this to work properly.
     */
    function load($id) {

          $sql = "SELECT * FROM Agenda_Item WHERE (cod_item = ".$id.") "; 

          if ($this->singleQuery($conn, $sql, $valueObject))
               return true;
          else
               return false;    

    }


    /**
     * LoadAll-method. This will read all contents from database table and
     * build an Vector containing valueObjects. Please note, that this method
     * will consume huge amounts of resources if table has lot's of rows. 
     * This should only be used when target tables have only small amounts
     * of data.
     *
     * @param conn         This method requires working database connection.
     */
    function loadAll() {
	  
          $sql = "SELECT * FROM Agenda_item ORDER BY cod_item ASC ";
          
          $conexao = new Conexao();
          
          $conexao->Conectar();
          
          $res = $conexao->Enviar($sql);
          
          $listaAgendas = $conexao->RetornaArrayLinhas($res);
          
          $conexao->Desconectar();

          return $listaAgendas;
    }



    /**
     * create-method. This will create new row in database according to supplied
     * valueObject contents. Make sure that values for all NOT NULL columns are
     * correctly specified. Also, if this table does not use automatic surrogate-keys
     * the primary-key must be specified. After INSERT command this method will 
     * read the generated primary-key back to valueObject if automatic surrogate-keys
     * were used. 
     *
     * @param conn         This method requires working database connection.
     * @param valueObject  This parameter contains the class instance to be created.
     *                     If automatic surrogate-keys are not used the Primary-key 
     *                     field must be set for this to work properly.
     */
    function create($conn, $valueObject) {

          $sql = "INSERT INTO Agenda_item (Curso_cod_curso, Usuario_cod_usuario, ";
          $sql = $sql."titulo, texto, situacao, ";
          $sql = $sql."data_criacao, data_publicacao, status, ";
          $sql = $sql."inicio_edicao) VALUES (".$valueObject->getCurso_cod_curso().", ";
          $sql = $sql."".$valueObject->getUsuario_cod_usuario().", ";
          $sql = $sql."'".$valueObject->getTitulo()."', ";
          $sql = $sql."'".$valueObject->getTexto()."', ";
          $sql = $sql."'".$valueObject->getSituacao()."', ";
          $sql = $sql."'".$valueObject->getData_criacao()."', ";
          $sql = $sql."'".$valueObject->getData_publicacao()."', ";
          $sql = $sql."'".$valueObject->getStatus()."', ";
          $sql = $sql."".$valueObject->getInicio_edicao().") ";
          
          
          $rs = $conn->executeUpdate($sql);
          
          return $rs;
          
    }  
    //Função que cria uma nova agenda no banco e trata campos nulos
    function create2($conn, $valueObject) {
          $mysqli= $conn->db;
          if(!($stmt = $mysqli->prepare("INSERT INTO Agenda_item (Curso_cod_curso, Usuario_cod_usuario,
            titulo, texto, situacao,data_criacao, data_publicacao, status,inicio_edicao)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)"))){
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;  
          }
          $Curso_cod_cursoIn= $valueObject->getCurso_cod_curso();
          $Usuario_cod_usuarioIn = $valueObject->getUsuario_cod_usuario();
          $tituloIn= $valueObject->getTitulo();
          $textoIn=$valueObject->getTexto();
          $situacaoIn=$valueObject->getSituacao();
          $data_criacaoIn=$valueObject->getData_criacao();
          $data_publicacaoIn=$valueObject->getData_publicacao();
          $statusIn=$valueObject->getStatus();
          $inicio_edicaoIn= $valueObject->getInicio_edicao();
          
          if(!($stmt->bind_param('iisssiisi', $Curso_cod_cursoIn, $Usuario_cod_usuarioIn, $tituloIn, $textoIn,
            $situacaoIn, $data_criacaoIn, $data_publicacaoIn, $statusIn,$inicio_edicaoIn))){
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;

          }

          if(!( $rs = $stmt->execute())){
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
         
          
          return $rs;
          
    }


    /**
     * save-method. This method will save the current state of valueObject to database.
     * Save can not be used to create new instances in database, so upper layer must
     * make sure that the primary-key is correctly specified. Primary-key will indicate
     * which instance is going to be updated in database. If save can not find matching 
     * row, NotFoundException will be thrown.
     *
     * @param conn         This method requires working database connection.
     * @param valueObject  This parameter contains the class instance to be saved.
     *                     Primary-key field must be set for this to work properly.
     */
    function save($valueObject) {

          $sql = "UPDATE Agenda_item SET Curso_cod_curso = ".$valueObject->getCurso_cod_curso().", ";
          $sql = $sql."Usuario_cod_usuario = ".$valueObject->getUsuario_cod_usuario().", ";
          $sql = $sql."titulo = '".$valueObject->getTitulo()."', ";
          $sql = $sql."texto = '".$valueObject->getTexto()."', ";
          $sql = $sql."situacao = '".$valueObject->getSituacao()."', ";
          $sql = $sql."data_criacao = '".$valueObject->getData_criacao()."', ";
          $sql = $sql."data_publicacao = '".$valueObject->getData_publicacao()."', ";
          $sql = $sql."status = '".$valueObject->getStatus()."', ";
          $sql = $sql."inicio_edicao = ".$valueObject->getInicio_edicao()."";
          $sql = $sql." WHERE (cod_item = ".$valueObject->getCod_item().") ";
         
          $conexao = new Conexao();
          $conexao->Conectar();
          $rs = $conexao->executeUpdate($sql);
          
          if ($rs){
          	echo 'atualizou';
          }
          else{
          	echo 'nao atualizou';
          }
          
          $conexao->Desconectar();
    }


    /**
     * delete-method. This method will remove the information from database as identified by
     * by primary-key in supplied valueObject. Once valueObject has been deleted it can not 
     * be restored by calling save. Restoring can only be done using create method but if 
     * database is using automatic surrogate-keys, the resulting object will have different 
     * primary-key than what it was in the deleted object. If delete can not find matching row,
     * NotFoundException will be thrown.
     *
     * @param conn         This method requires working database connection.
     * @param valueObject  This parameter contains the class instance to be deleted.
     *                     Primary-key field must be set for this to work properly.
     */
    function delete($id) {

          $sql = "DELETE FROM Agenda_item WHERE (cod_item = ".$id.") ";
          
          $conexao = new Conexao();
          $conexao->Conectar();
          $rs = $conexao->executeUpdate($sql);
          
          if ($rs){
          	echo 'deletou';
          }
          else{
          	echo 'nao deletou';
          }
          $conexao->Desconectar();
    }


    /**
     * deleteAll-method. This method will remove all information from the table that matches
     * this Dao and ValueObject couple. This should be the most efficient way to clear table.
     * Once deleteAll has been called, no valueObject that has been created before can be 
     * restored by calling save. Restoring can only be done using create method but if database 
     * is using automatic surrogate-keys, the resulting object will have different primary-key 
     * than what it was in the deleted object. (Note, the implementation of this method should
     * be different with different DB backends.)
     *
     * @param conn         This method requires working database connection.
     */
    function deleteAll($conn) {

          $sql = "DELETE FROM Agenda_Item";
          $result = $this->databaseUpdate($conn, $sql);

          return true;
    }


    /**
     * coutAll-method. This method will return the number of all rows from table that matches
     * this Dao. The implementation will simply execute "select count(primarykey) from table".
     * If table is empty, the return value is 0. This method should be used before calling
     * loadAll, to make sure table has not too many rows.
     *
     * @param conn         This method requires working database connection.
     */
    function countAll($conn) {

          $sql = "SELECT count(*) FROM Agenda_Item";
          $allRows = 0;

          $result = $conn->execute($sql);

          if ($row = $conn->nextRow($result))
                $allRows = $row[0];

          return $allRows;
    }


    /** 
     * searchMatching-Method. This method provides searching capability to 
     * get matching valueObjects from database. It works by searching all 
     * objects that match permanent instance variables of given object.
     * Upper layer should use this by setting some parameters in valueObject
     * and then  call searchMatching. The result will be 0-N objects in vector, 
     * all matching those criteria you specified. Those instance-variables that
     * have NULL values are excluded in search-criteria.
     *
     * @param conn         This method requires working database connection.
     * @param valueObject  This parameter contains the class instance where search will be based.
     *                     Primary-key field should not be set.
     */
    function searchMatching($conn, $valueObject) {

          $first = true;
          $sql = "SELECT * FROM Agenda_Item WHERE 1=1 ";

          if ($valueObject->getCod_item() != 0) {
              if ($first) { $first = false; }
              $sql = $sql."AND cod_item = ".$valueObject->getCod_item()." ";
          }

          if ($valueObject->getCurso_cod_curso() != 0) {
              if ($first) { $first = false; }
              $sql = $sql."AND Curso_cod_curso = ".$valueObject->getCurso_cod_curso()." ";
          }

          if ($valueObject->getUsuario_cod_usuario() != 0) {
              if ($first) { $first = false; }
              $sql = $sql."AND Usuario_cod_usuario = ".$valueObject->getUsuario_cod_usuario()." ";
          }

          if ($valueObject->getTitulo() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND titulo LIKE '".$valueObject->getTitulo()."%' ";
          }

          if ($valueObject->getTexto() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND texto LIKE '".$valueObject->getTexto()."%' ";
          }

          if ($valueObject->getSituacao() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND situacao LIKE '".$valueObject->getSituacao()."%' ";
          }

          if ($valueObject->getData_criacao() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND data_criacao = '".$valueObject->getData_criacao()."' ";
          }

          if ($valueObject->getData_publicacao() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND data_publicacao = '".$valueObject->getData_publicacao()."' ";
          }

          if ($valueObject->getStatus() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND status LIKE '".$valueObject->getStatus()."%' ";
          }

          if ($valueObject->getInicio_edicao() != 0) {
              if ($first) { $first = false; }
              $sql = $sql."AND inicio_edicao = ".$valueObject->getInicio_edicao()." ";
          }


          $sql = $sql."ORDER BY cod_item ASC ";

          // Prevent accidential full table results.
          // Use loadAll if all rows must be returned.
          if ($first)
               return array();

          $searchResults = $this->listQuery($conn, $sql);

          return $searchResults;
    }

    /**
     * databaseUpdate-method. This method is a helper method for internal use. It will execute
     * all database handling that will change the information in tables. SELECT queries will
     * not be executed here however. The return value indicates how many rows were affected.
     * This method will also make sure that if cache is used, it will reset when data changes.
     *
     * @param conn         This method requires working database connection.
     * @param stmt         This parameter contains the SQL statement to be excuted.
     */
    function databaseUpdate($conn, $sql) {

          $result = $conn->execute($sql);

          return $result;
    }



    /**
     * databaseQuery-method. This method is a helper method for internal use. It will execute
     * all database queries that will return only one row. The resultset will be converted
     * to valueObject. If no rows were found, NotFoundException will be thrown.
     *
     * @param conn         This method requires working database connection.
     * @param stmt         This parameter contains the SQL statement to be excuted.
     * @param valueObject  Class-instance where resulting data will be stored.
     */
    function singleQuery($conn, $sql, $valueObject) {

          $result = $conn->execute($sql);

          if ($row = $conn->nextRow($result)) {

                   $valueObject->setCod_item($row[0]); 
                   $valueObject->setCurso_cod_curso($row[1]); 
                   $valueObject->setUsuario_cod_usuario($row[2]); 
                   $valueObject->setTitulo($row[3]); 
                   $valueObject->setTexto($row[4]); 
                   $valueObject->setSituacao($row[5]); 
                   $valueObject->setData_criacao($row[6]); 
                   $valueObject->setData_publicacao($row[7]); 
                   $valueObject->setStatus($row[8]); 
                   $valueObject->setInicio_edicao($row[9]); 
          } else {
               //print " Object Not Found!";
               return false;
          }
          return true;
    }


    /**
     * databaseQuery-method. This method is a helper method for internal use. It will execute
     * all database queries that will return multiple rows. The resultset will be converted
     * to the List of valueObjects. If no rows were found, an empty List will be returned.
     *
     * @param conn         This method requires working database connection.
     * @param stmt         This parameter contains the SQL statement to be excuted.
     */
    function listQuery($conn, $sql) {

          $searchResults = array();
          $result = $conn->execute($sql);

          while ($row = $conn->nextRow($result)) {
               $temp = $this->createValueObject();

               $temp->setCod_item($row[0]); 
               $temp->setCurso_cod_curso($row[1]); 
               $temp->setUsuario_cod_usuario($row[2]); 
               $temp->setTitulo($row[3]); 
               $temp->setTexto($row[4]); 
               $temp->setSituacao($row[5]); 
               $temp->setData_criacao($row[6]); 
               $temp->setData_publicacao($row[7]); 
               $temp->setStatus($row[8]); 
               $temp->setInicio_edicao($row[9]); 
               array_push($searchResults, $temp);
          }

          return $searchResults;
    }

    /*Retorna prox código da agendaitem que pode ser usado*/
    public function proxId($conn){
      if($rs= $conn->RetornaMaiorCodigo('Agenda_item', 'cod_item')){
        return intval($rs[0])+1;
      }
      else{
        return "Nada encontrado";
      }

    }

}

?>