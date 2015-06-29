<?php

require_once '../../../lib/teleduc.inc';

class Conexao {
 
    var $host;
    var $user;
    var $pass;
    var $dbname;
    var $data;
    var $db;
    var $saida;
    var $status;
    var $entrada;

    function Conectar() {
        $this->status = 0;
        $this->host = $_SESSION['dbhost'];
        $this->user = $_SESSION['dbuser'];
        $this->pass = $_SESSION['dbpassword'];
        $this->dbname = $_SESSION['dbnamebase'];
        $this->db = mysqli_connect($this->host, $this->user, $this->pass);
 
        if (!$this->db) {
            //echo "Erro ao conectar no banco" . mysqli_error($this->db);
        } else {
            //echo "Conectado no banco";
            $this->status = 1;
        }
        mysqli_select_db($this->db,$this->dbname);
        //mysql_set_charset('utf8');
    }
 
    public function Enviar($query) {
        if ($this->status == 1) {
            //echo "lista...";
            if ($this->saida = mysqli_query($this->db,$query)) {
                // echo 'Rs loaded';
                return $this->saida;
            } else {
                echo "<pre class=\"error\">";
                echo "SQL ERROR: " . mysqli_error($this->db);
                echo "SQL : " . $query;
                echo "</pre>";
                $this->desconecta();
            }
        }
    }
 
    public function executeUpdate($query) {
        if ($this->status == 1) {
            if ($this->entrada = mysqli_query($this->db,$query)) {
                return true;
            } else {
                echo "<pre class=\"error\">";
                echo "SQL ERROR: " . mysqli_error($this->db);
                echo "</pre>";
                $this->Desconectar();
                return false;
            }
        }
    }
 
    function Desconectar() {
        return mysqli_close($this->db);
    }
    
    public function RetornaNumLinhas($result){
    	return(mysqli_num_rows($result));
    }
    
    public function RetornaArrayLinhas($result, $result_type = MYSQLI_BOTH){
    	$num = $this->RetornaNumLinhas($result);
    	$cont = 0;
    	$ar = array();
    	while ($num > 0) {
    		$num--;
    		$ar[$cont] = mysqli_fetch_array($result, $result_type);
    		$cont++;
    	}
    	return($ar);
    }
    
    public function RetornaLinha($result) {
    	return(mysqli_fetch_array($result));
    }
 
}
 
?>