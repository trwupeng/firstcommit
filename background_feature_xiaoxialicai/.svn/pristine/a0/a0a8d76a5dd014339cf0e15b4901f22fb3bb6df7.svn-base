<?php

namespace Prj\Data;

/**
 * Description of Weekactivity
 *
 * @author wu.chen
 */
class WeekActivity extends \Sooh\DB\Broker {

    protected $tbname;
    protected $db;

    public function __construct() {
        $this->db = parent::getInstance(\Rpt\Tbname::db_p2prpt);
        $this->tbname = \Rpt\Tbname::tb_weekactivity_final;
    }

    /**
     * 根据userid和taskNumber查询列表和总数
     * @param $userId 用户id
     * @param $taskNumber 期数
     * @param isCount 是否查询总数
     * @return array
     */
    public function getWeekActivityByUserIdAndTaskNumber($userId = FALSE, $taskNumber = FALSE, $isCount = FALSE, $pageSize = 30, $pageIndex = 0) {
        $where = [];
        if ($userId) {
            $where['userId'] = $userId;
        }
        if ($taskNumber) {
            $where['taskNumber'] = $taskNumber;
        }
        return ($isCount ? $this->db->getOne($this->tbname, "count(*)", $where) : $this->db->getRecords($this->tbname, '*', $where, FALSE, $pageSize, $pageIndex));
    }
    
    /**
     * 查询所有数据
     * @param $where 查询条件 sql片段 如：userId=xxxx AND ymgReg=xxxx
     * @return array
     */
    public function getWeekActivityAll($where = FALSE) {
        if (!$where) {
            $where = "1=1";
        }
        $sql = "SELECT * FROM {$this->tbname} WHERE $where";
        $res = $this->db->execCustom(['sql' => $sql]);
        return $this->db->fetchAssocThenFree($res);
    }

}
