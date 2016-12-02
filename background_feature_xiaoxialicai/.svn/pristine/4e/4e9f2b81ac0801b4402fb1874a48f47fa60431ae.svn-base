package sooh.services.msgcenter;

import Ice.Current;
import sooh.fastCGI.FCGIServeiceSimple;

public class bypushI extends _bypushDisp {

	@Override
	public void sendMsg(String receivers, String msg,  Current __current) {
		FCGIServeiceSimple obj = FCGIServeiceSimple.factory("bypush","sendMsg");
		obj.appendVar("receivers",receivers);
		obj.appendVar("msg",msg);
		obj.getStr();
	}
	@Override
	public void sendCmd(String receivers, String msg,  Current __current) {
		FCGIServeiceSimple obj = FCGIServeiceSimple.factory("bypush","sendCmd");
		obj.appendVar("receivers",receivers);
		obj.appendVar("msg",msg);
		obj.getStr();
	}	
}

