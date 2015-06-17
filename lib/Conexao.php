<?php

class Conexao {

        var $dbLink;

       /**
        * Constructor. Call this once when initializing system core.
        * Then save the instance of this class in $connection variable 
        * and pass it as an argument when using services from core.
        */
       function conectar($dbHost, $dbName, $dbuser, $dbpasswd) {

                // Change this line to reflect whatever Database system you are using:
                 if($this->dbLink = mysqli_connect ($dbHost, $dbuser, $dbpasswd)){
                 	echo ("Conectou");
                 }
                 else{
                 	echo ("Não conectou");
                 }

                // Change this line to reflect whatever Database system you are using:
                mysqli_select_db($this->dbLink, $dbName);
		}


        /**
         * Function to execute SQL-commands. Use this thin wrapper to avoid 
         * MySQL dependency in application code.
         */
        function execute($sql) {

                // Change this line to reflect whatever Database system you are using:
                $result = mysqli_query($this->dbLink, $sql);
                $this->checkErrors($sql);

                return $result;
        }


        /**
         * Function to "blindly" execute SQL-commands. This will not put up 
         * any notifications if SQL fails, so make sure this is not used for 
         * normal operations.
         */
        function executeBlind($sql) {

                // Change this line to reflect whatever Database system you are using:
                $result = mysqli_query($sql, $this->dbLink);

                return $result;
        }


        /**
         * Function to iterate trough the resultset. Use this thin wrapper to 
         * avoid MySQL dependency in application code.
         */
        function nextRow ($result) {

                // Change this line to reflect whatever Database system you are using:
                $row = mysqli_fetch_array($result);

                return $row;
        }


        /**
         * Check if sql-queries triggered errors. This will be called after an 
         * execute-command. Function requires attempted SQL string as parameter 
         * since it can be logged to application spesific log if errors occurred.
         * This whole method depends heavily from selected Database-system. Make
         * sure you change this method when using some other than MySQL database.
         */
        function checkErrors($sql) {

                //global $systemLog;

                // Only thing that we need todo is define some variables
                // And ask from RDBMS, if there was some sort of errors.
                $err=mysqli_error();
                $errno=mysqli_errno();

                if($errno) {
                        // SQL Error occurred. This is FATAL error. Error message and 
                        // SQL command will be logged and aplication will teminate immediately.
                        $message = "The following SQL command ".$sql." caused Database error: ".$err.".";

                        //$message = addslashes("SQL-command: ".$sql." error-message: ".$message);
                        //$systemLog->writeSystemSqlError ("SQL Error occurred", $errno, $message);

                        print "Unrecowerable error has occurred. All data will be logged.";
                        print "Please contact System Administrator for help! \n";
                        print "<!-- ".$message." -->\n";
                        exit;

                } else {
                        // Since there was no error, we can safely return to main program.
                        return;
                }
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
        function databaseUpdate(&$conn, &$sql) {
        
        	$result = $conn->execute($sql);
        
        	return $result;
        }
        
        
}

?>