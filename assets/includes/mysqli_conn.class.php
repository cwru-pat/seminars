<?php

/* @file
 * 
 * This file implements logic to create a basic mysqli connection, and implements a couple other functions contained
 * in the _database.php include file.  Currently most of the results system relies on mysql functionality, which will
 * soon be deprecated.
 * 
 * Please help improve this class!  Extending functionality here can help us implement a maintainable codebase.
 * 
 */


/*
 * @var DBConn
 * A class used to connect to the results database, perform basic validation, make calls, etc.
 * Results system benchmarking isn't currently reliable, so let's not implement it here yet.
 */
class DBConn
{
    public $conn;
    private $debug = FALSE;

    public function __construct($config, $charset = "utf8")
    {
        $this->conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['name']);
        if($this->conn->connect_errno)
        {
            trigger_error("Failed to connect to MySQL: (" . $$this->conn->connect_errno . ") " . $$this->conn->connect_error, E_USER_ERROR);
        }

        /* change character set */
        if(!$this->conn->set_charset($charset))
        {
            printf("Error loading character set {$charset}: %s\n", $this->conn->error);
        }
    }

    public function setDebug($val)
    {
        $this->debug = $val ? TRUE : FALSE;
        return $this;
    }

    // kill script, display error message.
    public function showDatabaseError($message)
    {
        // Normal users just get a "Sorry", developers/debuggers get more details
        if($_SERVER['SERVER_NAME'] == 'localhost'  ||  $this->debug){
            die("<p>$message<br />\n(" . mysql_error() . ")</p>\n");
        }
        else
        {
            die("<p>Uh-oh!  There was a problem with the database. Please try again later, and contact the website administrator if this message persists.</p>");
        }
    }

    public function mysqlEscape($val)
    {
        if($val == "") {
            return "";
        }
        elseif(is_numeric($val))
        {
            return $val;
        }
        elseif(is_string($val))
        {
            return $this->conn->real_escape_string($val);
        }
        else
        {
            $this->showDatabaseError("Error attempting to escape mysql data!");
        }
    }

    // return an array of result objects.
    public function dbQuery($query)
    {

        if($this->debug){
            $this->printCommand($query);
        }

        $result = $this->conn->query($query);
        if($this->conn->error)
        {
            $this->showDatabaseError("Unable to perform database query!");
        }

        $rows = array();
        while ($row = $result->fetch_object())
        {
            $rows[] = $row;
        }

        $result->close();

        return $rows;
    }

    // just run a query, don't need to process return values.
    public function dbCommand($command)
    {

        // print command if in debug mode
        if($this->debug){
            $this->printCommand($command);
        }

        $this->conn->query($command);

        if($this->conn->error)
        {
            $this->showDatabaseError("Unable to execute database command!");
        }

        return $this;
    }

    function dbDebug($query)
    {
        echo "<table border='1'>";
        foreach($this->dbQuery($query) as $result)
        {
            echo "<tr>";
            foreach(get_object_vars($result) as $property => $value)
            {
                echo "<td>" . htmlEntities($value) . "</td>"; 
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    public function printCommand($command)
    {
            if(strlen($command) < 1010)
            {
                $commandForShow =  $command;
            }
            else
            {
                $commandForShow =  substr($command,0,1000) . '[...' . (strlen($command)-1000) . '...]';;
            }
            echo "\n\n<pre>$commandForShow</pre>\n\n";

            return $this;
    }

}
