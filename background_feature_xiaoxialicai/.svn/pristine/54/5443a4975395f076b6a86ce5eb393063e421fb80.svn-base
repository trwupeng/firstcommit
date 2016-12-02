<?php

namespace Prj\Data;

/**
 * Description of Menu
 *
 * @author wu.chen
 */
class Menu extends \Sooh\DB\Base\KVObj {

    protected $tbname;
    protected $db;
    protected $orderby;
    protected $selectFields;
    protected $where;

    public function __construct() {
        $this->db = parent::db();
        $this->tbname = 'db_p2p.tb_menu';
        $this->selectFields = '*';
    }
    
    public function setSelectFields($fields) {
        $this->selectFields = $fields;
        return $this;
    }
    
    public function getMenu() {
       return $this->db->getRecords($this->tbname,  $this->selectFields);
    }

}
