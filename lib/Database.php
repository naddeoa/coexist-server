<?php
require_once("autoload.php");

class Database
{
  
  private $conf;
  private $conn;
  
  public function __construct()
  {
    $this->conf = new Config();
  }

  function __destruct()
  {
    $this->setConnection(NULL);
  }

  //test method 
  private function getTestSync()
  {
    $response = array();
    $response["version"] = $this->getConfig()->getVersion();
    $response["tables"] = array();
    foreach($this->getAllTables() as $row)
    {
      array_push($response["tables"],$row[0]);
    }

    $response["mod_ts"] = array();
    for($i = 0; $i < count($response["tables"]); $i++)
    {
      array_push($response["mod_ts"], "2011-12-28 20:46:02");
    }
    return $response;
  }

  protected function getAllTables()
  {
    $db = $this->connect();
    $result = $db->query("show full tables where Table_type = 'BASE TABLE'");

    $this->disconnect();
    return $result;
  }

  /**
  Validates the request from the client. There should be one
  timestamp for every table entry. The table entries should
  mirror the tables on the backend, excluding views.
  */
  private function validate($tables,$timestamps)
  {
    if(count($tables) !== count($timestamps))
      Error::tableMismatch();

    //TODO check that tables match here
  }

  /**
  Generates a diff to send back to the client. This will
  end up being an associative array where the keys are
  table names and the values are arrays of rows that
  the client is missing.
  */
  public function getDiff($tables,$timestamps)
  {
    $db = $this->connect();
    $response = array();

    $this->validate($tables,$timestamps);

    //it appears that binding cannot be used for table names.
    $sql = 'SELECT * FROM %1$s WHERE mod_ts > \'%2$s\' ';

    for($i = 0; $i < count($tables); $i++)
    {
      $table = $tables[$i];
      $mod_ts = $timestamps[$i];

      $query = sprintf($sql,$table,$mod_ts);
      
      $response[$table] = array();
      foreach($db->query($query,PDO::FETCH_OBJ) as $row)
        array_push($response[$table], $row);
      
    }

    $this->disconnect();
    return $response;
  }
  
  public function insertOrUpdate($sync)
  {
    $db = $this->connect();
    $db->beginTransaction();
    
    foreach(array_keys($sync) as $table)
    {
      $missingRows = $sync[$table];
      foreach($missingRows as $row)
      {
        $columns = $this->getSqlString($row);
        $bindArgs = $this->getSqlBindString($row);
        $sql = "INSERT INTO $table ($columns) VALUES ($bindArgs)";
        
        $statement = $db->prepare($sql);
        $i = 1;
        foreach(array_keys($row) as $cols)
        {
          $statement->bindParam($i, $row[$cols], PDO::PARAM_STR);
          $i++;
        }
        $statement->execute();
        
      }
      
    }
    $db->commit();
    $this->disconnect();
  }
  
  private function getSqlString($row)
  {
    $columns = array_keys($row);
    $sql = $columns[0];
    for($i = 1; $i < count($columns); $i++){
      $sql .= ",".$columns[$i];
    }
    return $sql;
  }
  
  private function getSqlBindString($row)
  {
    $columns = array_keys($row);
    $sql = "?";
    for($i = 1; $i < count($columns); $i++){
      $sql .= ",?";
    }
    return $sql;
  }


  /**
  Establishes a connection to the database based on the contents
  of the .ini file. A new connection will be made if the current
  connection is set to NULL, else, the connection still exists so
  none will be remade. Persistent connections are being used.

  It is the job of the code that calls Database::getInstance() to
  catch this error. The error should be handled using the Error 
  class, and it should be handeled outside of this class because the
  Error class takes a Response object so that it can send useful
  information back to the client in JSON form. This class never
  deals with Responses directly.
  */
  private function connect()
  {
    if($this->getConnection() !== NULL)
      return $this->getConnection();

    $conn = $this->getConfig();
    $user = $conn->getUsername();
    $pass = $conn->getPassword();
    $options = $this->getDbOptions();
    
    $pdo = new PDO($this->getDSN(),$user,$pass,$options);
    $this->setConnection($pdo);
    return $this->getConnection();
  }

  /**
  Disconnect a database connection if persistence is not being 
  used.
  */
  private function disconnect()
  {
    $options = $this->getDbOptions();
    if($options[PDO::ATTR_PERSISTENT])
      return;

    $this->setConnection(NULL);
  }

  private function getConfig()
  {
    return $this->conf;
  }

  private function getConnection()
  {
    return $this->conn;
  }

  private function setConnection($conn)
  {
    $this->conn = $conn;
  }

  private function getDbOptions()
  {
    return array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_PERSISTENT => true);
  }

  private function getDSN()
  {
    $conn = $this->getConfig();
    $dbms = $conn->getDBMS();
    $host = $conn->getHost();
    $db   = $conn->getDatabase();

    $dsn = '%1$s:host=%2$s;dbname=%3$s';
    return sprintf($dsn,$dbms,$host,$db);
  }

}

?>
