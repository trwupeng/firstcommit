<?php

namespace Rpt\Medal;

/**
 * Description of MedalFinal
 *
 * @author wu.chen
 */
class MedalFinal extends \Sooh\DB\Base\KVObj {

    protected $tbname;
    protected $db;
    protected $orderby;
    protected $selectFields;
    protected $where;

    public function __construct() { 
        $this->db = parent::db(); // parent::getInstance(\Rpt\Tbname::db_p2p);        
        $this->tbname = 'db_p2prpt.tb_medal_final';       
        $this->selectFields = '*';
    }

    /**
     * 设置查询字段
     * @param obj $selectFields 查询字段 数组['aa','bb']或字符串'aa,bb,cc'
     */
    public function setSelectFields($selectFields = '*') {
        $this->selectFields = $selectFields;
        return $this;
    }

    /**
     * 设置查询条件
     * @param array $where 条件数组
     */
    public function setWhere($where) {
        $this->where = $where;
        return $this;
    }

    /**
     * 设置排序条件
     * @param string $orderby rsort/sort key
     */
    public function setOrderBy($orderby) {
        $this->orderby = $orderby;
        return $this;
    }

    /**
     * 根据条件查询总数
     * return int
     */
    public function getRecordsCount() {
        return $this->db->getOne($this->tbname, 'count(*)', $this->where);
    }

    /**
     * 根据条件查询
     * @param $pageId 页码
     * @param $pageSize 每页显示数量
     * return array
     */
    public function getRecords($pageId = 0, $pageSize = 30) {
        return $this->db->getRecords($this->tbname, $this->selectFields, $this->where, $this->orderby, $pageSize, $pageId);
    }

    /**
     * 获得所有数据
     * return array
     */
    public function getAllRecords() {
        return $this->db->getRecords($this->tbname, $this->selectFields, $this->where, $this->orderby);
    }

    /**
     * 查询一条结果
     * return array
     */
    public function getRecord() {
        return $this->db->getRecord($this->tbname, $this->selectFields, $this->where);
    }

    /**
     * 更新/添加数据
     */
    public function replaceRec($data) {
        if ($data) {
            $sql = "REPLACE INTO {$this->tbname} (`medalKey`, `medalName`, `medalReport`) VALUES ('{$data['medalKey']}', '{$data['medalName']}', '{$data['medalReport']}')";
            $this->db->execCustom(['sql' => $sql]);
        }
    }

}
