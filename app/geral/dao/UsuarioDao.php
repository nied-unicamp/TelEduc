<?php

require_once '../../../lib/Conexao.php';

 /**
  * Usuario Data Access Object (DAO).
  * This class contains all database handling that is needed to 
  * permanently store and retrieve Usuario object instances. 
  */


class UsuarioDao {


    /**
     * createValueObject-method. This method is used when the Dao class needs
     * to create new value object instance. The reason why this method exists
     * is that sometimes the programmer may want to extend also the valueObject
     * and then this method can be overrided to return extended valueObject.
     * NOTE: If you extend the valueObject class, make sure to override the
     * clone() method in it!
     */
    function createValueObject() {
          return new Usuario();
    }


    /**
     * getObject-method. This will create and load valueObject contents from database 
     * using given Primary-Key as identifier. This method is just a convenience method 
     * for the real load-method which accepts the valueObject as a parameter. Returned
     * valueObject will be created using the createValueObject() method.
     */
    function getObject( $conn, $cod_usuario) {

          $valueObject = $this->createValueObject();
          $valueObject->setCod_usuario($cod_usuario);
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

          $sql = "SELECT * FROM Usuario WHERE (cod_usuario = ".$id.") "; 

          $conexao = new Conexao();
          
          $conexao->Conectar();
          
          $res = $conexao->Enviar($sql);
          
          $usuario = $conexao->RetornaLinha($res);
          
          $conexao->Desconectar();

          return $usuario;
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


          $sql = "SELECT * FROM Usuario ORDER BY cod_usuario ASC ";

          $conexao = new Conexao();
          
          $conexao->Conectar();
          
          $res = $conexao->Enviar($sql);
          
          $listaUsuarios = $conexao->RetornaArrayLinhas($res);
          
          $conexao->Desconectar();

          return $listaUsuarios;
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
    function create($valueObject) {

          $sql = "INSERT INTO Usuario ( login, senha, ";
          $sql = $sql."nome_usuario, rg, email, ";
          $sql = $sql."telefone, endereco, cidade, ";
          $sql = $sql."estado, pais, data_nasc, ";
          $sql = $sql."sexo, local_trabalho, profissao, ";
          $sql = $sql."escolaridade, informacoes, data_inscricao, ";
          $sql = $sql."cod_lingua, confirmacao) VALUES ("."'".$valueObject->getLogin()."', ";
          $sql = $sql."'".$valueObject->getSenha()."', ";
          $sql = $sql."'".$valueObject->getNome_usuario()."', ";
          $sql = $sql."'".$valueObject->getRg()."', ";
          $sql = $sql."'".$valueObject->getEmail()."', ";
          $sql = $sql."'".$valueObject->getTelefone()."', ";
          $sql = $sql."'".$valueObject->getEndereco()."', ";
          $sql = $sql."'".$valueObject->getCidade()."', ";
          $sql = $sql."'".$valueObject->getEstado()."', ";
          $sql = $sql."'".$valueObject->getPais()."', ";
          $sql = $sql."".$valueObject->getData_nasc().", ";
          $sql = $sql."'".$valueObject->getSexo()."', ";
          $sql = $sql."'".$valueObject->getLocal_trabalho()."', ";
          $sql = $sql."'".$valueObject->getProfissao()."', ";
          $sql = $sql."'".$valueObject->getEscolaridade()."', ";
          $sql = $sql."'".$valueObject->getInformacoes()."', ";
          $sql = $sql."".$valueObject->getData_inscricao().", ";
          $sql = $sql."".$valueObject->getCod_lingua().", ";
          $sql = $sql."'".$valueObject->getConfirmacao()."') ";
          
          $conexao = new Conexao();
          $conexao->Conectar();
          $rs = $conexao->executeUpdate($sql);
          
          if ($rs){
          	echo 'inseriu';
          }
          else{
          	echo 'nao inseriu';
          }
          $conexao->Desconectar();
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

          $sql = "UPDATE Usuario SET login = '".$valueObject->getLogin()."', ";
          $sql = $sql."senha = '".$valueObject->getSenha()."', ";
          $sql = $sql."nome_usuario = '".$valueObject->getNome_usuario()."', ";
          $sql = $sql."rg = '".$valueObject->getRg()."', ";
          $sql = $sql."email = '".$valueObject->getEmail()."', ";
          $sql = $sql."telefone = '".$valueObject->getTelefone()."', ";
          $sql = $sql."endereco = '".$valueObject->getEndereco()."', ";
          $sql = $sql."cidade = '".$valueObject->getCidade()."', ";
          $sql = $sql."estado = '".$valueObject->getEstado()."', ";
          $sql = $sql."pais = '".$valueObject->getPais()."', ";
          $sql = $sql."data_nasc = ".$valueObject->getData_nasc().", ";
          $sql = $sql."sexo = '".$valueObject->getSexo()."', ";
          $sql = $sql."local_trabalho = '".$valueObject->getLocal_trabalho()."', ";
          $sql = $sql."profissao = '".$valueObject->getProfissao()."', ";
          $sql = $sql."escolaridade = '".$valueObject->getEscolaridade()."', ";
          $sql = $sql."informacoes = '".$valueObject->getInformacoes()."', ";
          $sql = $sql."data_inscricao = ".$valueObject->getData_inscricao().", ";
          $sql = $sql."cod_lingua = ".$valueObject->getCod_lingua().", ";
          $sql = $sql."confirmacao = '".$valueObject->getConfirmacao()."'";
          $sql = $sql." WHERE (cod_usuario = ".$valueObject->getCod_usuario().") ";
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


          if (!$valueObject->getCod_usuario()) {
               //print "Can not delete without Primary-Key!";
               return false;
          }

          $sql = "DELETE FROM Usuario WHERE (cod_usuario = ".$valueObject->getCod_usuario().") ";
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

          $sql = "DELETE FROM Usuario";
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

          $sql = "SELECT count(*) FROM Usuario";
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
          $sql = "SELECT * FROM Usuario WHERE 1=1 ";

          if ($valueObject->getCod_usuario() != 0) {
              if ($first) { $first = false; }
              $sql = $sql."AND cod_usuario = ".$valueObject->getCod_usuario()." ";
          }

          if ($valueObject->getLogin() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND login LIKE '".$valueObject->getLogin()."%' ";
          }

          if ($valueObject->getSenha() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND senha LIKE '".$valueObject->getSenha()."%' ";
          }

          if ($valueObject->getNome_usuario() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND nome_usuario LIKE '".$valueObject->getNome_usuario()."%' ";
          }

          if ($valueObject->getRg() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND rg LIKE '".$valueObject->getRg()."%' ";
          }

          if ($valueObject->getEmail() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND email LIKE '".$valueObject->getEmail()."%' ";
          }

          if ($valueObject->getTelefone() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND telefone LIKE '".$valueObject->getTelefone()."%' ";
          }

          if ($valueObject->getEndereco() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND endereco LIKE '".$valueObject->getEndereco()."%' ";
          }

          if ($valueObject->getCidade() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND cidade LIKE '".$valueObject->getCidade()."%' ";
          }

          if ($valueObject->getEstado() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND estado LIKE '".$valueObject->getEstado()."%' ";
          }

          if ($valueObject->getPais() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND pais LIKE '".$valueObject->getPais()."%' ";
          }

          if ($valueObject->getData_nasc() != 0) {
              if ($first) { $first = false; }
              $sql = $sql."AND data_nasc = ".$valueObject->getData_nasc()." ";
          }

          if ($valueObject->getSexo() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND sexo LIKE '".$valueObject->getSexo()."%' ";
          }

          if ($valueObject->getLocal_trabalho() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND local_trabalho LIKE '".$valueObject->getLocal_trabalho()."%' ";
          }

          if ($valueObject->getProfissao() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND profissao LIKE '".$valueObject->getProfissao()."%' ";
          }

          if ($valueObject->getEscolaridade() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND escolaridade LIKE '".$valueObject->getEscolaridade()."%' ";
          }

          if ($valueObject->getInformacoes() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND informacoes LIKE '".$valueObject->getInformacoes()."%' ";
          }

          if ($valueObject->getData_inscricao() != 0) {
              if ($first) { $first = false; }
              $sql = $sql."AND data_inscricao = ".$valueObject->getData_inscricao()." ";
          }

          if ($valueObject->getCod_lingua() != 0) {
              if ($first) { $first = false; }
              $sql = $sql."AND cod_lingua = ".$valueObject->getCod_lingua()." ";
          }

          if ($valueObject->getConfirmacao() != "") {
              if ($first) { $first = false; }
              $sql = $sql."AND confirmacao LIKE '".$valueObject->getConfirmacao()."%' ";
          }


          $sql = $sql."ORDER BY cod_usuario ASC ";

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

                   $valueObject->setCod_usuario($row[0]); 
                   $valueObject->setLogin($row[1]); 
                   $valueObject->setSenha($row[2]); 
                   $valueObject->setNome_usuario($row[3]); 
                   $valueObject->setRg($row[4]); 
                   $valueObject->setEmail($row[5]); 
                   $valueObject->setTelefone($row[6]); 
                   $valueObject->setEndereco($row[7]); 
                   $valueObject->setCidade($row[8]); 
                   $valueObject->setEstado($row[9]); 
                   $valueObject->setPais($row[10]); 
                   $valueObject->setData_nasc($row[11]); 
                   $valueObject->setSexo($row[12]); 
                   $valueObject->setLocal_trabalho($row[13]); 
                   $valueObject->setProfissao($row[14]); 
                   $valueObject->setEscolaridade($row[15]); 
                   $valueObject->setInformacoes($row[16]); 
                   $valueObject->setData_inscricao($row[17]); 
                   $valueObject->setCod_lingua($row[18]); 
                   $valueObject->setConfirmacao($row[19]); 
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

               $temp->setCod_usuario($row[0]); 
               $temp->setLogin($row[1]); 
               $temp->setSenha($row[2]); 
               $temp->setNome_usuario($row[3]); 
               $temp->setRg($row[4]); 
               $temp->setEmail($row[5]); 
               $temp->setTelefone($row[6]); 
               $temp->setCidade($row[8]); 
               $temp->setEstado($row[9]); 
               $temp->setPais($row[10]); 
               $temp->setData_nasc($row[11]); 
               $temp->setSexo($row[12]); 
               $temp->setLocal_trabalho($row[13]); 
               $temp->setProfissao($row[14]); 
               $temp->setEscolaridade($row[15]); 
               $temp->setInformacoes($row[16]); 
               $temp->setData_inscricao($row[17]); 
               $temp->setCod_lingua($row[18]); 
               $temp->setConfirmacao($row[19]); 
               array_push($searchResults, $temp);
          }

          return $searchResults;
    }
}

?>