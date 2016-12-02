<?php
use Sooh\Base\Form\Item as form_def;

class ConfigController extends \Prj\ManagerCtrl {
	protected $lists=[	/*'WECHAT_ACCESSTOKEN'=>'微信token',*/		];
	protected $options=[ /*'sms_tunnel'=>['chuanglan'=>'创蓝'] */	];
	protected $tips = [];
	public function init()
	{
		parent::init();
		$this->db=  \Sooh\DB\Broker::getInstance();
		$rs = $this->db->getRecords($this->tb, '*',['intro*'=>'#%']);
		$this->rs = array();
		foreach($rs as $r){
			//#title#format#options
			$tmp = explode('#', $r['intro']);
			$k = $r['k'];
			$this->rs[$k]=$r['v'];
			$this->lists[$k]=$tmp[1].'<br><font color=gray>('.$tmp[2].')</font>';
			if(sizeof($tmp)==4){
				$this->options[$k] = json_decode($tmp[3],true);
			}
		}
	}
	protected $tb = 'db_p2p.tb_config';
	/**
	 *
	 * @var \Sooh\DB\Interfaces\All
	 */
	protected $db;
	protected $rs;
	/**
	 * 协议一览
	 */
	public function indexAction() {
		$this->_view->assign('tips',$this->tips);
		$frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(''), 'get', \Sooh\Base\Form\Broker::type_u);
		
		foreach($this->rs as $k=>$v){
			if(!empty($this->options[$k])){
				if(empty($v)){
					$v = key($this->options[$k]);
				}
				$item = form_def::factory($this->lists[$k], $v, form_def::select)->initMore(new \Sooh\Base\Form\Options($this->options[$k]));
			}else{
				$item = form_def::factory($this->lists[$k], htmlspecialchars($v), form_def::text);
			}
			$frm->addItem($k, $item);
		}
		
		
		$frm->fillValues();
		if($frm->flgIsThisForm){
			$fields = $frm->getFields();
			foreach($fields as $k=>$v){
				if($v!=$this->lists[$k]){
					try{
						$tmp = \Prj\Data\TbConfigItem::getCopy($k);
                        $tmp->load();
						$tmp->setField('v', $v);
						$tmp->update();
						$this->rs[$k]=$v;
						\Prj\Data\TbConfigItem::freeAll();
					} catch (\Exception $ex) {
						$this->returnError("更新【".$this->lists[$k]."】失败：".$ex->getMessage());
					}
				}
			}
			$this->returnOK('全部更新');
		}
	}

}