<?php

use Sooh\Base\Form\Item as form_def;

class SqlmanageController extends \Prj\ManagerCtrl {

    public $sqlString = ""; //SQL查询字符串
    public $tableName = ""; //表名

    protected static function getDB() {
        return \Sooh\DB\Broker::getInstance();
    }

    /**
     * sql列表数据
     */
    public function indexAction() {
        $fieldsMapArr = [
            'sqlId' => ['SQL编号', '30'],
            'sqlDesc' => ['SQL功能说明', '40'],
            'sqlContent' => ['SQL语句', '200'],
            'status' => ['状态', '30'],
            'result' => ['执行结果', '80'],
            'createTime' => ['创建时间', '30'],
            'updateTime' => ['执行时间', '30']
        ];

        //查询功能分类分组
        $db = \Sooh\DB\Broker::getInstance();
        $tbName = 'tb_sql_manage';
        $rs = $db->getRecords($tbName, 'sqlDesc', '', 'group sqlDesc');
        //数据格式化
        $optionArray = [];
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $optionArray[$v['sqlDesc']] = $v['sqlDesc'];
            }
        }


        $pageId = $this->_request->get('pageId', 1) - 0;
        $pageSize = $this->_request->get('pageSize', 10);
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_ sqlDesc_eq', form_def::factory('功能', '', form_def::select)->initMore(new \Sooh\Base\Form\Options($optionArray, '不限')))
                ->addItem('_status_eq', form_def::factory('状态', '', form_def::select)->initMore(new \Sooh\Base\Form\Options(\Prj\Consts\Sqlmanage::$status, '不限')))
                ->addItem('pageId', $pageId)
                ->addItem('pageSize', $pageSize);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
        } else {
            $where = [];
        }
        $pager = new \Sooh\DB\Pager($pageSize, $this->pageSizeEnum, false);
        $pager->init(-1, $pageId);
        $records = \Prj\Data\Sqlmanage::paged($pager, $where, null, implode(',', array_keys($fieldsMapArr)));
        $header = [];
        foreach ($fieldsMapArr as $k => $v) {
            $header[$v[0]] = $v[1];
        }
        foreach ($records as $v) {
            foreach ($fieldsMapArr as $kk => $vv) {
                if ($kk == 'status') {
                    $temp[$kk] = \Prj\Consts\Sqlmanage::$status[$v[$kk]];
                } elseif ($kk == 'createTime') {
                    $temp[$kk] = substr($v[$kk], 0, 8);
                } elseif ($kk == 'updateTime') {
                    $temp[$kk] = substr($v[$kk], 0, 8);
                } else {
                    $temp[$kk] = $v[$kk];
                }
            }
            $temp['_pkey_val_'] = \Prj\Misc\View::encodePkey(['sqlId' => $v['sqlId']]);
            $new[] = $temp;
        }
        $this->_view->assign('where', $where);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('header', $header);
        $this->_view->assign('rs', $new);
    }

    /**
     * sql列表的新增、修改
     */
    public function editAction() {

        $pkey = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));

        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(''), 'get', empty($pkey) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);
        if ($frm->type() == \Sooh\Base\Form\Broker::type_c) {
            $frm->addItem('sqlDesc', form_def::factory('SQL功能说明', '', form_def::text))
                    ->addItem('sqlContent', form_def::factory('SQL语句', '', form_def::mulit))
                    ->addItem('status', form_def::factory('状态', '1', form_def::select)->initMore(new \Sooh\Base\Form\Options(\Prj\Consts\Sqlmanage::$status)))
                    ->addItem('_pkey_val_', '');
        } else {
            $obj = \Prj\Data\Sqlmanage::getCopy($pkey);
            $obj->load();
            $updateTime = $obj->getField('updateTime');
            $frm->addItem('sqlDesc', form_def::factory('SQL功能说明', $obj->getField('sqlDesc'), form_def::text))
                    ->addItem('sqlContent', form_def::factory('SQL语句', $obj->getField('sqlContent'), form_def::mulit))
                    ->addItem('status', form_def::factory('状态', $obj->getField('status'), form_def::select)->initMore(new \Sooh\Base\Form\Options(\Prj\Consts\Sqlmanage::$status)))
                    ->addItem('result', form_def::factory('执行结果', $obj->getField('result'), form_def::constval))
                    ->addItem('createTime', form_def::factory('创建时间', $obj->getField('createTime'), form_def::constval))
                    ->addItem('updateTime', form_def::factory('执行时间', $obj->getField('updateTime'), form_def::constval))
                    ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));
        }
        $frm->fillValues();
        $fields = $frm->getFields();
        if (empty($pkey) && $frm->flgIsThisForm) {
            $obj = \Prj\Data\Sqlmanage::getCopy(['sqlId' => $fields['sqlId']]);
        }

        if ($frm->flgIsThisForm) {
            foreach ($fields as $k => $v) {
                $obj->setField($k, $v);
            }
            if (empty($pkey)) {
                $obj->setField('createTime', date('YmdHis'));
            }
            try {
                $obj->update();
            } catch (\ErrorException $e) {
                return $this->returnError('保存失败:' . $e->getMessage());
            }
            $this->closeAndReloadPage();
            return $this->returnOK('ok');
        }
    }

    /**
     * 导入csv文件
     */
    public function importAction() {
        
    }

    /**
     * 上传csv文件并导入数据库
     */
    public function uploadCsvAction() {

        $filename = $_FILES['file']['tmp_name'];
        if (empty($filename)) {
            echo '请选择要导入的CSV文件！';
            exit;
        }
        $file = fopen($filename, "r");
        while (!feof($file)) {
            $row[] = fgetcsv($file);
        }
        fclose($file);
        if (!empty($row)) {
            foreach ($row as $k => $v) {
                if ($k == 0) {
                    continue;
                }
                $where = array('sqlContent' => base64_decode($v[2]));
                $records = self::getDB()->getRecord('tb_sql_manage', '*', $where);
                //判断如果有重复数据则跳过
                if (!empty($records)) {
                    continue;
                }
                $ret = self::getDB()->addRecord('tb_sql_manage', array('sqlDesc' => iconv('gb2312', 'utf-8', $v[3]), 'sqlContent' => base64_decode($v[2]), 'status' => 1, 'createTime' => date('YmdHis')));
            }
        }
        $this->returnOK('成功');
    }

    /**
     * SQL语句执行请求Action
     */
    public function runAction() {
        //请求的sqlId
        $pkey = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $obj = \Prj\Data\Sqlmanage::getCopy($pkey);
        $obj->load();
        //获取sql语句
        $sql_content = $obj->getField('sqlContent');
        //匹配表名
        $pattern = '/{(.*)}/isU';
        preg_match_all($pattern, $sql_content, $matches);
        $this->tableName = $matches[0][0];
        $this->tableModelName = $matches[0][0];

        //匹配执行方法，只有CREATE不用判断主从数据库，其他都需要判断。
        $exeMethod = '/@(.*)@/isU';
        preg_match_all($exeMethod, $sql_content, $ma);


        $excMethod = strtolower($ma[1][0]);
        //只有CREATE才直接执行sql,其他方式都需要判断主从
        if ($excMethod == 'create' || $excMethod == 'insert') {
            $this->createExSql($obj, $sql_content);
        } else {
            $this->sqlObj = $obj;
            $this->sqlString = $sql_content;
            $this->tableModelName = str_replace('{', '', $this->tableModelName);
            $this->tableModelName = str_replace('}', '', $this->tableModelName);

            //表名格式化
            $tableArr = explode("_", $this->tableModelName);
            if (!empty($tableArr[1]) && !empty($tableArr[1])) {
                $dbModel = ucfirst($tableArr[1]) . ucfirst($tableArr[2]);
            } else {
                $dbModel = ucfirst($tableArr[1]);
            }
            try {
                //找到数据实例
                $dbBase = '\Prj\Data\\' . $dbModel;
                if (!class_exists($dbBase)) {
                    return $this->returnError('没找到数据类！');
                } else {
                    $dbBase::loop([$this, 'exeSql']);
                }
            } catch (\ErrorException $e) {
                return $this->returnError('执行失败，:' . $e->getMessage());
            }
        }
    }

    /**
     * SQL语句执行回调函数
     */
    public function exeSql($db, $tb) {
        $sql = str_replace($this->tableName, $tb, $this->sqlString);
        $sql = str_replace('@', '', $sql);
        $sql = str_replace('@', '', $sql);
        $sqlObj = $this->sqlObj;
        try {
            $rrt = $db->execCustom(['sql' => $sql]);
            if ($rrt == true) {
                $this->saveResult($sqlObj, 1);
                return $this->returnOK('ok');
            }
        } catch (\ErrorException $e) {
            $this->saveResult($sqlObj, 2, $e->getMessage());
            return $this->returnError('执行失败:' . $e->getMessage());
        }
    }

    /**
     * CREATE 语句直接执行，无需判断是否多个数据表
     */
    public function createExSql($obj, $sql) {


        //匹配CREATE并去掉[ ]
        $exMethod = '/@(.*)@/isU';
        preg_match_all($exMethod, $sql, $matchMethod);
        if (empty($matchMethod[0][0])) {
            return $this->returnError('执行失败,没匹配到SQL执行方式！');
        }
        $tableNm = '@' . $matchMethod[1][0] . '@';
        $sql = str_replace($tableNm, $matchMethod[1][0], $sql);

        //匹配表名并替换去掉{ }
        $nameMethod = '/{(.*)}/isU';
        preg_match_all($nameMethod, $sql, $matchName);
        if (empty($matchName[0][0])) {
            return $this->returnError('执行失败,没匹配到SQL执行方式！');
        }
        $tbNm = '{' . $matchName[1][0] . '}';
        $sql = str_replace($tbNm, $matchName[1][0], $sql);

        $db = \Sooh\DB\Broker::getInstance();
        try {

            $rrt = $db->execCustom(['sql' => $sql]);
            if ($rrt == true) {
                $this->saveResult($obj, 1);
                return $this->returnOK('ok');
            }
        } catch (\ErrorException $e) {
            $this->saveResult($obj, 2, $e->getMessage());
            return $this->returnError('执行失败:' . $e->getMessage());
        }
    }

    /**
     * 根据SQL语句执行结果判断，记录成功或者失败和失败日志
     */
    public function saveResult($obj, $flag, $error = null) {

        if ($flag == 1) { //1 成功 2失败
            $sqlId = $obj->getField('sqlId');
            $obj->setField('sqlId', $sqlId);
            $obj->setField('status', 2);
            $obj->setField('updateTime', date('YmdHis'));
            $obj->setField('result', '执行OK!');
            $obj->update();
        } else {
            $sqlId = $obj->getField('sqlId');
            $obj->setField('sqlId', $sqlId);
            $obj->setField('status', 3);
            $obj->setField('updateTime', date('YmdHis'));
            $obj->setField('result', $error);
            $obj->update();
        }
    }

    public function downExecelAction() {

        $exportType = $this->_request->get('__EXCEL__');
        if ($exportType == 'select') {
            $keysstr = $this->_request->get('idp');
            $keys = is_array($keysstr) ? $keysstr : explode(',', $keysstr);
            if (!empty($keysstr)) { //导出选中
                foreach ($keys as $k => $v) {
                    $keys[$k] = \Prj\Misc\View::decodePkey($v)['sqlId'];
                }
                $where = array('sqlId' => $keys);
            }
            //print_r($where);
            //exit;
            $db = \Sooh\DB\Broker::getInstance();
            $records = $db->getRecords('tb_sql_manage', 'sqlId,status,sqlContent,sqlDesc,updateTime,result,createTime', $where);
        } else {
            $db = \Sooh\DB\Broker::getInstance();
            $records = $db->getRecords('tb_sql_manage', 'sqlId,status,sqlContent,sqlDesc,updateTime,result,createTime', array());
        }
        //替换SQL语句换行符号
        if (!empty($records)) {
            foreach ($records as $k => $v) {
                $records[$k]['sqlContent'] = base64_encode(str_replace("\r\n", '', $v['sqlContent']));
            }
        }
        $title = array('SQL编号', '状态', 'SQL语句', 'SQL功能说明', '执行时间', '执行结果', '创建时间');
        $this->downOutExcel($records, $title, $filename = null, $scientificFlg = true, '.csv');
    }

}
