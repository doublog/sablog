<?php
// ========================== 文件说明 ==========================//
// 本文件说明：数据库函数类
// --------------------------------------------------------------//
// 本程序作者：angel
// --------------------------------------------------------------//
// 本程序版本：SaBlog-X Ver 1.6
// --------------------------------------------------------------//
// 本程序主页：http://www.sablog.net
// ========================== 开发环境 ==========================//
// register_globals = Off
// --------------------------------------------------------------//
// magic_quotes_gpc = On
// --------------------------------------------------------------//
// safe_mode = On
// --------------------------------------------------------------//
// Windows server 2003 & Linux & FreeBSD
// --------------------------------------------------------------//
// Apache/1.3.33 & PHP/4.3.2 & MySQL/4.0.17
// --------------------------------------------------------------//
// Apache/1.3.34 & PHP/4.4.1 & MySQL/5.0.16
// --------------------------------------------------------------//
// Apache/2.0.55 & PHP/5.1.1 & MySQL/5.0.15
// --------------------------------------------------------------//
// Copyright (C) Security Angel Team All Rights Reserved.
// ==============================================================//

if(!defined('SABLOG_ROOT')){
    exit('Access Denied');
}

class DB_MySQL
{
    public $querycount = 0;
    private $version;
    /**
     * @var PDO
     */
    private $pdo;

    public function geterrdesc()
    {
        return $this->getPdo()
                    ->errorInfo();
    }

    public function geterrno()
    {
        return $this->getPdo()
                    ->errorCode();
    }

    public function insert_id()
    {
        return $this->getPdo()
                    ->lastInsertId();
    }

    function version()
    {
        if($this->version){
            return $this->version;
        }
        return $this->version = $this->getPdo()
                                     ->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    public function connect($servername, $dbusername, $dbpassword, $dbname, $usepconnect = 0)
    {
        global $dbcharset, $charset;
        $options = [PDO::ATTR_PERSISTENT => true];
        if(!$dbcharset && in_array(strtolower($charset), ['gbk', 'big5', 'utf-8'])){
            $dbcharset = str_replace('-', '', $charset);
        }
        if($dbcharset){
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$dbcharset}";
        }
        $dsn = sprintf('mysql:host=%s;port=3306;dbname=%s;charset=UTF8;', $servername, $dbname);
        $this->pdo = new PDO($dsn, $dbusername, $dbpassword, [PDO::ATTR_PERSISTENT => true]);
        if($this->version() > '5.0.1'){
            $this->pdo->query("SET sql_mode=''");
        }
    }

    public function getPdo()
    {
        global $servername, $dbusername, $dbpassword, $dbname;
        if(!$this->pdo){
            $this->connect($servername, $dbusername, $dbpassword, $dbname);
        }
        try{
            $this->version = $this->pdo->getAttribute(PDO::ATTR_SERVER_INFO);
        } catch(PDOException $e){
            $this->connect($servername, $dbusername, $dbpassword, $dbname);
        }
        return $this->pdo;
    }

    function query($sql, $type = '')
    {
        $query = $this->getPdo()
                      ->prepare($sql);
        $query->execute();
        $this->querycount++;
        if($type == 'SLIENT'){
            return $this->result($query,0);
        }
        return $query;
    }

    function unbuffered_query($sql)
    {
        return $this->query($sql);
    }

    public function fetch_array(PDOStatement $query, $result_type = PDO::FETCH_ASSOC)
    {
        return $query->fetch($result_type);
    }

    function fetch_row(PDOStatement $query)
    {
        return $query->fetch(PDO::FETCH_COLUMN);
    }

    function fetch_one_array($sql)
    {
        $query = $this->query($sql);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    function num_rows(PDOStatement $query)
    {
        return $query->rowCount();
    }

    function num_fields(PDOStatement $query)
    {
        return $query->columnCount();
    }

    function result(PDOStatement $query, $row)
    {
        return $query->fetchColumn($row);
    }

    function free_result($query)
    {
        return $query;
    }

    function close()
    {
        return true;
    }

    function halt($msg, $sql = '')
    {
        global $php_self, $timestamp, $onlineip;

        if($sql){
            @$fp = fopen(SABLOG_ROOT . 'cache/log/dberrorlog.php', 'a');
            @fwrite($fp, "<?PHP exit('Access Denied'); ?>\t$timestamp\t$onlineip\t" . basename($php_self) . "\t" . htmlspecialchars($this->geterrdesc()) .
                         "\t" . str_replace(["\r", "\n", "\t"], [' ', ' ', ' '], trim(htmlspecialchars($sql))) . "\n");
            @fclose($fp);
        }

        $message = "<html>\n<head>\n";
        $message .= "<meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\">\n";
        $message .= "<style type=\"text/css\">\n";
        $message .= "body,p,pre {\n";
        $message .= "font:12px Verdana;\n";
        $message .= "}\n";
        $message .= "</style>\n";
        $message .= "</head>\n";
        $message .= "<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#006699\" vlink=\"#5493B4\">\n";

        $message .= "<p>数据库出错:</p><pre><b>" . htmlspecialchars($msg) . "</b></pre>\n";
        $message .= "<b>Mysql error description</b>: " . htmlspecialchars($this->geterrdesc()) . "\n<br />";
        $message .= "<b>Mysql error number</b>: " . $this->geterrno() . "\n<br />";
        $message .= "<b>Date</b>: " . date("Y-m-d @ H:i") . "\n<br />";
        $message .= "<b>Script</b>: http://" . $_SERVER['HTTP_HOST'] . getenv("REQUEST_URI") . "\n<br />";

        $message .= "</body>\n</html>";
        echo $message;
        exit;
    }
}

class DB_MySQL2
{
    var $querycount = 0;

    function fetch_array($query, $result_type = MYSQL_ASSOC)
    {
        return mysql_fetch_array($query, $result_type);
    }

    function query($sql, $type = '')
    {
        //echo "<div style=\"text-align: left;\">".htmlspecialchars($sql)."</div>";
        /*
        遇到问题时用这个来检查SQL执行语句
        $fp = fopen('sqlquerylog.txt', 'a');
        flock($fp, 2);
        fwrite($fp, $sql."\n");
        fclose($fp);
        */
        $func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query')
            ? 'mysql_unbuffered_query'
            : 'mysql_query';
        if(!($query = $func($sql)) && $type != 'SILENT'){
            $this->halt('MySQL Query Error', $sql);
        }
        $this->querycount++;
        return $query;
    }

    function unbuffered_query($sql)
    {
        $query = $this->query($sql, 'UNBUFFERED');
        return $query;
    }

    function select_db($dbname)
    {
        return mysql_select_db($dbname);
    }

    function fetch_row($query)
    {
        $query = mysql_fetch_row($query);
        return $query;
    }

    function fetch_one_array($query)
    {
        $result = $this->query($query);
        $record = $this->fetch_array($result);
        return $record;
    }

    function num_rows($query)
    {
        $query = mysql_num_rows($query);
        return $query;
    }

    function num_fields($query)
    {
        return mysql_num_fields($query);
    }

    function result($query, $row)
    {
        $query = @mysql_result($query, $row);
        return $query;
    }

    function free_result($query)
    {
        $query = mysql_free_result($query);
        return $query;
    }

    function version()
    {
        //return mysql_get_server_info();
        $query = $this->query("SELECT VERSION()");
        return $this->result($query, 0);
    }

    function close()
    {
        return mysql_close();
    }

    function halt($msg, $sql = '')
    {
        global $php_self, $timestamp, $onlineip;

        if($sql){
            @$fp = fopen(SABLOG_ROOT . 'cache/log/dberrorlog.php', 'a');
            @fwrite($fp, "<?PHP exit('Access Denied'); ?>\t$timestamp\t$onlineip\t" . basename($php_self) . "\t" . htmlspecialchars($this->geterrdesc()) .
                         "\t" . str_replace(["\r", "\n", "\t"], [' ', ' ', ' '], trim(htmlspecialchars($sql))) . "\n");
            @fclose($fp);
        }

        $message = "<html>\n<head>\n";
        $message .= "<meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\">\n";
        $message .= "<style type=\"text/css\">\n";
        $message .= "body,p,pre {\n";
        $message .= "font:12px Verdana;\n";
        $message .= "}\n";
        $message .= "</style>\n";
        $message .= "</head>\n";
        $message .= "<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#006699\" vlink=\"#5493B4\">\n";

        $message .= "<p>数据库出错:</p><pre><b>" . htmlspecialchars($msg) . "</b></pre>\n";
        $message .= "<b>Mysql error description</b>: " . htmlspecialchars($this->geterrdesc()) . "\n<br />";
        $message .= "<b>Mysql error number</b>: " . $this->geterrno() . "\n<br />";
        $message .= "<b>Date</b>: " . date("Y-m-d @ H:i") . "\n<br />";
        $message .= "<b>Script</b>: http://" . $_SERVER['HTTP_HOST'] . getenv("REQUEST_URI") . "\n<br />";

        $message .= "</body>\n</html>";
        echo $message;
        exit;
    }
}

?>
