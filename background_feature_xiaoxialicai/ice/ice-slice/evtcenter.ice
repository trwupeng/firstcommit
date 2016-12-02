module sooh{
	module services{
		/**
		 * 事件中心
		 */
		module evtcenter{
			interface triggers{
				// 注册事件
				void onRegister(string data);	
				// 登出事件 
				void onLogout(string data);			
				// 登入事件（data中应该能识别是否是注册后首次登入） 
				void onLogin(string data);
				// 购买事件（请求）
				void onBuyRequest(string data);
				// 购买事件（确认）
				void onBuyConfirm(string data);
				// 充值事件（请求）
				void onRechargeRequest(string data);
				// 充值事件（确认）
				void onRechargeConfirm(string data);
				// 提现事件（请求）
				void onWithdrawRequest(string data);
				// 提现事件（确认）
				void onWithdrawConfirm(string data);				
				// 满标事件
				void onWaresFull(string data);
				// 标的月还息事件
				void onWaresMonthly(string data);
				// 标的最后一笔回款事件
				void onWaresEnd(string data);
				// 自定义扩展事件调用
				void onCustomEvt(string evt, string data);
			};
		};
	};
};
