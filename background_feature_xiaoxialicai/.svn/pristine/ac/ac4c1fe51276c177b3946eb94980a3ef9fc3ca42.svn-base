package sooh.services.msgcenter;

import Ice.Current;
import sooh.fastCGI.FCGIServeiceSimple;

public class bysmsI extends _bysmsDisp {
	@Override
	public void sendCode(String receiver, String msg,  Current __current) {
		FCGIServeiceSimple obj = FCGIServeiceSimple.factory("bysms","sendCode");
		obj.appendVar("receiver",receiver);
		obj.appendVar("msg",msg);
		obj.getStr();
	}
	@Override
	public void sendNotice(String receivers, String msg,  Current __current) {
		FCGIServeiceSimple obj = FCGIServeiceSimple.factory("bysms","sendNotice");
		obj.appendVar("receivers",receivers);
		obj.appendVar("msg",msg);
		obj.getStr();
	}
	@Override
	public void sendMarket(String receivers, String msg,  Current __current) {
		FCGIServeiceSimple obj = FCGIServeiceSimple.factory("bysms","sendMarket");
		obj.appendVar("receivers",receivers);
		obj.appendVar("msg",msg);
		obj.getStr();
	}	
}

