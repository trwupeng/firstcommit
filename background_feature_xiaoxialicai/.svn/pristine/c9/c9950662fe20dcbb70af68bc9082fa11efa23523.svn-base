module sooh{
	module services{
		/**
		 * 消息中心
		 */
		module msgcenter{
			interface bysms{ 
				/**
				 * 发送验证码类短信，receiver是手机号
				 */
				void sendCode(string receiver, string msg);
				/**
				 * 发送通知类短信，receiver是逗号分隔的手机号列表
				 */
				void sendNotice(string receivers, string msg);
				/**
				 * 发送营销类短信，receivers是逗号分隔的手机号列表
				 */
				void sendMarket(string receivers, string msg);
			};
			interface bypush{ 
				/**
				 * 全平台发送推送消息，receivers是逗号分隔的用户id列表
				 */
				void sendMsg(string receivers, string msg);
				/**
				 * 全平台发送推送命令，receivers是逗号分隔的用户id列表
				 */
				void sendCmd(string receivers, string cmd);
			};			
		};
	};
};
