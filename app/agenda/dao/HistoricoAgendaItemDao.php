

<?php


 /**
  * Historico_Agenda_Item Data Access Object (DAO).
  * This class contains all database handling that is needed to 
  * permanently store and retrieve Historico_Agenda_Item object instances. 
  */

class Historico_Agenda_ItemDao {


    /**
     * createValueObject-method. This method is used when the Dao class needs
     * to create new value object instance. The reason why this method exists
     * is that sometimes the programmer may want to extend also the valueObject
     * and then this method can be overrided to return extended valueObject.
     * NOTE: If you extend the valueObject class, make sure to override the
     * clone() method in it!
     */
    function createValueObject() {
          return new Historico_Agenda_Item();
    }


    /**
     * getObject-method. This will create and load valueObject contents from database 
     * using given Primary-Key as identifier. This method is just a convenience method 
     * for the real load-method which accepts the valueObject as a parameter. Returned
     * valueObject will be created using the createValueObject() method.
     */
    function getObject($conn, $cod_historico) {

          $valueObject = $this->createValueObject();
          $valueObject->setCod_historico($cod_historico);
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
    function load($conn, $valueObject) {

          if (!$valueObject->getCod_historico()) {
               //print "Can not select without Primary-Key!";
               return false;
          }

          $sql = "SELECT * FROM Historico_Agenda_Item WHERE (cod_historico = ".$valueObject->getCod_historico().") "; 

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
    function loadAll($conn) {


          $sql = "SELECT * FROM Historico_Agenda_Item ORDER BY cod_historico ASC ";

          $searchResults = $this->listQuery($conn, $sql);

          return $searchResults;
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

          $sql = "INSERT INTO Historico_Agenda_Item (Usuario_cod_usuario, Acao_cod_acao, ";
          $sql = $sql."Agenda_item_cod_item, data) VALUES (".$valueObject->getUsuario_cod_usuario().", ";
          $sql = $sql."".$valueObject->getAcao_cod_acao().", ";
          $sql = $sql."".$valueObject->getAgenda().", ";
          $sql = $sql."'".$valueObject->getData()."') ";
          $result = $this->databaseUpdate($conn, $sql);


          return true;
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
    function save($conn, $valueObject) {

          $sql = "UPDATE Historico_Agenda_Item SET Usuario_cod_usuario = ".$valueObject->getUsuario_cod_usuario().", ";
          $sql = $sql."Acao_cod_acao = ".$valueObject->getAcao_cod_acao().", ";
          $sql = $sql."Agenda_item_cod_item = ".$valueObject->getAgenda().", ";
          $sql = $sql."data = '".$valueObject->getData()."'";
          $sql = $sql." WHERE (cod_historico = ".$valueObject->getCod_historico().") ";
          $result = $this->databaseUpdate($conn, $sql);

          if ($result != 1) {
               //print "PrimaryKey Error when updating DB!";
               return false;
          }

          return true;
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
    function delete($conn, $valueObject) {


          if (!$valueObject->getCod_historico()) {
               //print "Can not delete without Primary-Key!";
               return false;
          }

          $sql = "DELETE FROM Historico_Agenda_Item WHERE (cod_historico = ".$valueObject->getCod_historico().") ";
          $result = $this->databaseUpdate($conn, $sql);

          if ($result != 1) {
               //print "PrimaryKey Error when updating DB!";
               return false;
          }
          return true;
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

          $sql = "DELETE FROM Historico_Agenda_Item";
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

          $sql = "SELECT count(*) FROM Historico_Agenda_Item";
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
          $sql = "SELECT * FROM Historico_Agenda_Item WHERE 1=1 ";

          if ($valueObject->getCod_historico() != 0) {
              if ($first) { $first = false; }
              $sql = $sql."AND cod_historico = ".$valueObject->getCod_historico()." ";
          }

          if ($valueObject->getUsuario_cod_usuario() != 0) {
              if ($first) { $first = false; }
              $sql = $sql."AND Usuario_cod_usuario = ".$valueObject->getUsuario_cod_usuario()." ";
          }

          if ($valueObject->getAcao_cod_acao() != 0) {
              if ($first) { $first = false; }
              $sql = $sql."AND Acao_cod_acao = ".$valueObject->getAcao_cod_acao()." ";
          }

          if ($valueObject->getAgenda() != 0) {
              if ($first) { $first = false; }
              $sql = $sql."AND Agenda_item_cod_item = ".$valueObject->getAgenda()." ";
          }

          if ($valueObject->getData() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND data = '".$valueObject->getData()."' ";
          }


          $sql = $sql."ORDER BY cod_historico ASC ";

          // Prevent accidential full table results.
          // Use loadAll if all rows must be returned.
          if ($first)
               return array();

          $searchResults = $this->listQuery($conn, $sql);

          return $searchResults;
    }


    /** 
     * getDaogenVersion will return information about
     * generator which created these sources.
     */
    function getDaogenVersion() {
        return "DaoGen version 2.4.1";
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

                   $valueObject->setCod_historico($row[0]); 
                   $valueObject->setUsuario_cod_usuario($row[1]); 
                   $valueObject->setAcao_cod_acao($row[2]); 
                   $valueObject->setAgenda($row[3]); 
                   $valueObject->setData($row[4]); 
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

               $temp->setCod_historico($row[0]); 
               $temp->setUsuario_cod_usuario($row[1]); 
               $temp->setAcao_cod_acao($row[2]); 
               $temp->setAgenda($row[3]); 
               $temp->setData($row[4]); 
               array_push($searchResults, $temp);
          }

          return $searchResults;
    }
    
    function retornaHistoricoDoItem($cod_item){
    	$sql = "select HA.cod_historico, AI.titulo, AH.nome, HA.data, U.nome_usuario 
				from  Agenda_item AI, Acao_Historico AH, Historico_Agenda_Item HA, Usuario U
				where  HA.Agenda_item_cod_item = AI.cod_item
				and HA.Acao_cod_acao = AH.cod_acao
				and HA.Usuario_cod_usuario = U.cod_usuario
				and AI.cod_item =".$cod_item." order by HA.data desc,AH.nome desc";
    	
    	$conexao = new Conexao();
    	
    	$conexao->Conectar();
    	
    	$res = $conexao->Enviar($sql);
    	
    	$listaHistorico = $conexao->RetornaArrayLinhas($res);
    	
    	$conexao->Desconectar();
    	
    	return $listaHistorico;
    	
    	
    }
}

?>