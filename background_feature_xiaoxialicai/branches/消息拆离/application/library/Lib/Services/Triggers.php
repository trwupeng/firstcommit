<?php
namespace Lib\Services;
/**
 * rpc 服务器管理
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Triggers
{
	/**
	 * @var SMS
	 */
	protected static $_instance = null;

	/**
	 *
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return \Lib\Services\Triggers
	 */
	public static function getInstance($rpcOnNew = null)
	{
		if (self::$_instance === null) {
			$c = get_called_class();
			self::$_instance = new $c;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}

	protected function dispatch($func,$logdata)
	{
		$this->rpc = \Prj\BaseCtrl::getRpcDefault('Triggers');
		//error_log("[EVT_".__FUNCTION__."]".$logdata);
		$classname = "\\Lib\\Services\\evts\\$func";
		$sys = new $classname;
		if(is_string($logdata)){
			$data = new \Sooh\Base\Log\Data();
			$data->fromArray(json_decode($logdata,true));
		}else{
			$data = $logdata;
		}
		$sys->run($data);
	}
	/**
	 * 用户成功注册以后做哪些任务
	 */
	public function onRegister($logdata)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('logdata' => $logdata))->send(__FUNCTION__);
		} else {
			error_log('data中应该能识别是否是注册后首次登入:当是注册并登入的时候，同时触发注册事件');
			$this->dispatch(__FUNCTION__, $logdata);
		}
	}	
	/**
	 * 用户成功登入以后做哪些任务 (data中应该能识别是否是注册后首次登入)
	 */
	public function onLogin($logdata)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('logdata' => $logdata))->send(__FUNCTION__);
		} else {
			error_log('data中应该能识别是否是注册后首次登入:当是注册并登入的时候，同时触发注册事件');
			$this->dispatch(__FUNCTION__, $logdata);
		}
	}
	/**
	 * 用户成功登出以后做哪些任务 
	 */
	public function onLogout($logdata)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('logdata' => $logdata))->send(__FUNCTION__);
		} else {
			error_log('data中应该能识别是否是注册后首次登入:当是注册并登入的时候，同时触发注册事件');
			$this->dispatch(__FUNCTION__, $logdata);
		}
	}
	/**
	 * 用户发出购买请求 以后做哪些任务
	 */
	public function onBuyRequest($logdata)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('logdata' => $logdata))->send(__FUNCTION__);
		} else {
			$this->dispatch(__FUNCTION__, $logdata);
		}
	}
	/**
	 * 用户确认购买 以后做哪些任务 （可能购买成功，可能购买失败）
	 */
	public function onBuyConfirm($logdata)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('logdata' => $logdata))->send(__FUNCTION__);
		} else {
			$this->dispatch(__FUNCTION__, $logdata);
		}
	}
	/**
	 * 用户发起充值请求以后做哪些任务
	 */
	public function onRechargeRequest($logdata)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('logdata' => $logdata))->send(__FUNCTION__);
		} else {
			$this->dispatch(__FUNCTION__, $logdata);
		}
	}
	/**
	 * 用户充值有结果以后做哪些任务 （可能成功充值，可能失败）
	 */
	public function onRechargeConfirm($logdata)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('logdata' => $logdata))->send(__FUNCTION__);
		} else {
			$this->dispatch(__FUNCTION__, $logdata);
		}
	}
	/**
	 * 用户发起提现请求以后做哪些任务
	 */
	public function onWithdrawRequest($logdata)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('logdata' => $logdata))->send(__FUNCTION__);
		} else {
			$this->dispatch(__FUNCTION__, $logdata);
		}
	}
	/**
	 * 用户提现操作有结果以后做哪些任务 （提现可能成功，提现可能失败）
	 */
	public function onWithdrawConfirm($logdata)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('logdata' => $logdata))->send(__FUNCTION__);
		} else {
			$this->dispatch(__FUNCTION__, $logdata);
		}
	}
	/**
	 * 标的满标转账以后做什么事情（可能成功，可能失败）
	 */
	public function onWaresFull($logdata)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('logdata' => $logdata))->send(__FUNCTION__);
		} else {
			$this->dispatch(__FUNCTION__, $logdata);
		}
	}	
	/**
	 * 标的按月付息以后做哪些任务 （可能成功可能失败）
	 */
	public function onWaresMonthly($logdata)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('logdata' => $logdata))->send(__FUNCTION__);
		} else {
			$this->dispatch(__FUNCTION__, $logdata);
		}
	}
	/**
	 * 标的全部结清还款操作以后做哪些任务 （可能成功，可能失败）
	 */
	public function onWaresEnd($logdata)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('logdata' => $logdata))->send(__FUNCTION__);
		} else {
			$this->dispatch(__FUNCTION__, $logdata);
		}
	}
	/**
	 * 自行调用的，不能经过ice-rpc调用，只能直接调用
	 */
	public function onCustomEvt($evt, $data)
	{
		if ($this->rpc !== null) {
			error_log(__FUNCTION__.' by rpc');
			return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
		} else {
			error_log(__FUNCTION__.' not by rpc');
			$this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
		}
	}

    public function onSendSmsCodeForUpdPwd($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onSendSmsCodeForQuickLogin($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onPassportLogin($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onPassportQuickReg($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onPassportQuickLogin($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onPassportResetPwd($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onOauthAppreg($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onOauthWebreg($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onOauthQuickReg($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onOauthSendInvalidcode($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onPublicReceiveVoucher($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onActivesCheckin($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onExchangecodeGetbonus($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onOrdersAdd($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onUserBindcard($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onUserRecharge($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onUserWithdraw($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onUserSendSmscode($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onUserUnBindCard($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }

    public function onWeekactiveFetch($evt, $data) {
        if ($this->rpc !== null) {
            error_log(__FUNCTION__.' by rpc');
            return $this->rpc->initArgs(array('evt'=>$evt,'data' => $data))->send(__FUNCTION__);
        } else {
            error_log(__FUNCTION__.' not by rpc');
            $this->dispatch(__FUNCTION__, ['evtName'=>$evt,'evtData'=>$data]);
        }
    }
}
}