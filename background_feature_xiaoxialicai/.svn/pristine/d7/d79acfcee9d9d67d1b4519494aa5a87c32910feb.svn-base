package sooh.services.logcenter;

import Ice.Current;
import sooh.fastCGI.FCGIServeiceSimple;

public class logerI extends _logerDisp {
	@Override
	public final String writeSyn(String logdata, String reqdata,  Current __current) {
        //System.out.println("logerI->sayhi() called with("+logdata+") on connection="+__current.con.toString());
        //FCGIServeiceSimple.iniFCGI = new sooh.services.iniServiceSimple();
		FCGIServeiceSimple obj = FCGIServeiceSimple.factory("loger","writeSyn");
		obj.appendVar("logdata",logdata);
		obj.appendVar("reqdata",reqdata);
		return obj.getStr();
	}
	@Override
	public void writeAsy(String logdata, String reqdata,  Current __current) {
        writeSyn(logdata,reqdata,__current);
	}
}

