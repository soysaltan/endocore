<?php

namespace Endocore\Core;

use Endocore\App\Configs\AppConfig;

class Model
{
    /**
     * Veritabanını nesnesini tutar
     * @var void
     */
    private $link;

    public function __construct()
    {
        $this->link = new \mysqli(AppConfig::DB_HOST, AppConfig::DB_USR, AppConfig::DB_PWD, AppConfig::DB_NAME, AppConfig::DB_PORT);
        if ($this->link->connect_error) {
            trigger_error('Error: Could not make a database link (' . $this->link->connect_errno . ') ' . $this->link->connect_error);
            exit();
        }
        $this->link->set_charset(AppConfig::DB_CHARSET);
        $this->link->query("SET SQL_MODE = ''");
    }

    public function query($sql)
    {
        $query = $this->link->query($sql);
        if (!$this->link->errno) {
            if ($query instanceof \mysqli_result) {
                $data = array();
                while ($row = $query->fetch_assoc()) {
                    $data[] = $row;
                }
                /* $result = new \stdClass();
                  $result->num_rows = $query->num_rows;
                  $result->row = isset($data[0]) ? $data[0] : array();
                  $result->rows = $data; */
                global $rows, $row, $num_rows;
                $this->num_rows = $query->num_rows;
                $this->row = isset($data[0]) ? $data[0] : array();
                $this->rows = $data;

                $query->close();
                return $this;
            } else {
                return true;
            }
        } else {
            trigger_error('Hata: ' . $this->link->error . '<br />Hata No: ' . $this->link->errno . '<br />' . $sql);
        }
    }

    public function escape($value)
    {
        return $this->link->real_escape_string($value);
    }

    public function count_affected()
    {
        return $this->link->affected_rows;
    }

    public function get_last_id()
    {
        return $this->link->insert_id;
    }

    public function __destruct()
    {
        $this->link->close();
    }
}
