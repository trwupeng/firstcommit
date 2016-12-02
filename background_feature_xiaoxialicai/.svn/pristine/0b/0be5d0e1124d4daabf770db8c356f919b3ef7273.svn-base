<?php

namespace Lib\Logs;

/**
 * 记录查询 哪个管理员 在什么时间执行了什么， 目标涉及哪些
 * 数据库表记录 : 管理员标识， 时间， 描述， 目标(userId=123434545&waresId=353465O&ordersId=1345344123423)
 */
class Cmdlog extends \Sooh\DB\Broker {

    protected $target;
    protected $tbname;
    protected $db;
    protected $loginName;
    protected $orderby;
    protected $selectFields;
    protected $where;

    public function __construct() {
        $this->loginName = ($_SESSION['managerId'] ? $_SESSION['managerId'] : $_COOKIE['last_login_name']);     //获取当前登录的用户名
        $this->db = parent::getInstance(\Rpt\Tbname::db_p2p);
        $this->tbname = 'db_p2p.tb_cmdlog';
        $this->selectFields = '*';
    }

    /**
     * 添加target
     * @param string $targetName 目标key，如：userId、ordersId、waresId等
     * @param string $targetValue 目标key对应的值
     */
    public function appendTarget($targetName = 'waresId', $targetValue = '234552544365') {
        //最后用http_build_query保存入库 waresId=14325432552345&userId=234523423
        $this->target[$targetName] = $targetValue;
        return $this;
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
     * 保存操作日志
     * @param string $cmdDesc 对执行的描述
     * @param string $manager 管理员loginName
     */
    public function save($cmdDesc, $manager = FALSE) {
        $fields['loginName'] = ($manager ? $manager : $this->loginName);
        $fields['cmdDesc'] = $cmdDesc;
        $fields['createTime'] = date('YmdHis');
        $fields['target'] = http_build_query($this->target);
        $this->db->addRecord($this->tbname, $fields);
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

}
