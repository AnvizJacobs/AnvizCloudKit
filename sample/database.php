<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-7
 * Time: 18:02
 * File Name: database.php
 */

/** Connect the database */
$db = new DB(array(
    'hostname' => $config['db']['host'],
    'username' => $config['db']['username'],
    'password' => $config['db']['password'],
    'database' => $config['db']['dbname']
));

class DB
{
    /**
     * Username
     *
     * @var    string
     */
    public $username;

    /**
     * Password
     *
     * @var    string
     */
    public $password;

    /**
     * Hostname
     *
     * @var    string
     */
    public $hostname;

    /**
     * Database name
     *
     * @var    string
     */
    public $database;

    /**
     * Database driver
     *
     * @var    string
     */
    public $dbdriver = 'mysqli';

    /**
     * Connection ID
     *
     * @var    object|resource
     */
    public $conn_id = FALSE;

    /**
     * Result ID
     *
     * @var    object|resource
     */
    public $result_id = FALSE;

    public function __construct($params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $val) {
                $this->$key = $val;
            }
        }

        $this->db_connect();
        $this->db_select();
    }

    public function db_connect()
    {
        $this->conn_id = ($this->dbdriver == 'mysqli') ? mysqli_connect($this->hostname, $this->username, $this->password) : mysql_connect($this->hostname, $this->username, $this->password);

        if (!$this->conn_id) {
            die('The Database can not connect!');
        }

        return $this->conn_id;
    }

    public function db_select()
    {
        if (!$this->conn_id)
            return false;


        ($this->dbdriver == 'mysqli') ? mysqli_select_db($this->conn_id, $this->database) : mysql_select_db($this->database, $this->conn_id);

        ($this->dbdriver == 'mysqli') ? mysqli_query($this->conn_id, "set names 'utf8'") : mysql_query("set names 'utf8'", $this->conn_id);
    }

    public function query($sql = '')
    {
        if (!$this->conn_id)
            return false;

        if (empty($sql))
            return false;

        $this->result_id = ($this->dbdriver == 'mysqli') ? mysqli_query($this->conn_id, $sql) : mysql_query($sql, $this->conn_id);

        return $this->result_id;
    }

    public function num_rows()
    {
        if (!$this->result_id)
            return false;

        return ($this->dbdriver == 'mysqli') ? mysqli_num_rows($this->result_id) : mysql_num_rows($this->result_id);
    }

    public function fetch_array()
    {
        if (!$this->result_id)
            return false;

        return ($this->dbdriver == 'mysqli') ? mysqli_fetch_array($this->result_id) : mysql_fetch_array($this->result_id);
    }
}