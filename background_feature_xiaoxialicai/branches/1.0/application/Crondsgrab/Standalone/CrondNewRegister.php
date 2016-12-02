<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/run/crond.php "__=crond/rungrab&task=Standalone.CrondNewRegister&ymdh=20150819"
 * TODO: 
 *
 */
class CrondNewRegister extends \Rpt\Misc\DataCrondGather{
	public function init() {
		parent::init();
		$this->toBeContinue = true;
		$this->_secondsRunAgain = 180;
		$this->_iissStartAfter=400;
		$this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
	}
	public function free() {
		parent::free();
		$this->dbMysql=null;
	}
	protected $dbMysql;
	/**
	 * 
	 * @param \Sooh\Base\Time $dt
	 * @param \Sooh\Base\RetSimple $__ignore__ Description
	 */

	protected function gather()
	{
		$this->printLogOfTimeRang();
		$hisFrom = date('His', $this->dtFrom)-0;
		$hisTo = date('His', $this->dtTo)-0;

	    $where = array('hisReg]'=>$hisFrom,'hisReg['=>$hisTo,'ymdReg'=>$this->ymd);  
		$news = \Prj\Data\User::loopFindRecordsByFields($where, null, 'userId', 'getCol');
		$news = array_unique($news);
		error_log('[ Trace ] ### '.__CLASS__.' ### Sizeof newRegister:'.sizeof($news));
		if(!empty($news)){
            $news = array_chunk($news, 1000);
            foreach ($news as $group) {
                foreach ($group as $uid) {
                    $record = \Rpt\Funcs::getUserBasicInfo($uid);
//var_log($record, 'record>>>>>>');
                    if(empty($record)) {
                       continue; 
                    }
                    try{
                        \Sooh\DB\Broker::errorMarkSkip();
                        $this->dbMysql->addRecord(\Rpt\Tbname::tb_user_final, $record);
                        $this->ret->newadd++;
                    }catch(\ErrorException $e) {
                        if (\Sooh\DB\Broker::errorIs($e)){
                            unset($record['userId']);
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_user_final, $record,array('userId'=>$uid));
                            $this->ret->newupd++;
                        }else {
                            error_log($e->getMessage()."\n".$e->getTraceAsString());
                        }
                    }
                }
            }                
            
            
	    }

	    $this->lastMsg = $this->ret->toString();
		error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
	    return true;
	}
}
