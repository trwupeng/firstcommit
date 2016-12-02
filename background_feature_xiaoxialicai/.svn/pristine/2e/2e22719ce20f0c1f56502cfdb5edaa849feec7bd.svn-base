package sooh.services.samples;

import Ice.Current;
import sooh.fastCGI.FCGIServeiceSimple;

public class sample0000I extends _sample0000Disp {
	@Override
	public final String forwardSyn(String serviceClass, String serviceFunc, String jsonedArray,  Current __current) {
        //System.out.println("logerI->sayhi() called with("+logdata+") on connection="+__current.con.toString());
        FCGIServeiceSimple.iniFCGI = new sooh.services.iniServiceSimple();
		FCGIServeiceSimple obj = FCGIServeiceSimple.factory("sample0000","forwardSyn");
		obj.appendVar("serviceClass",serviceClass);
		obj.appendVar("serviceFunc",serviceFunc);
		obj.appendVar("jsonedArray",jsonedArray);
		return obj.getJsonStr();
	}
	@Override
	public void forwardAsy(String serviceClass, String serviceFunc, String jsonedArray, Current __current) {
		forwardSyn(serviceClass,serviceFunc,jsonedArray,__current);
	}
	
	@Override
	public final String echohi(String who,  Current __current) {
        return "{\"code\":300,\"msg\":\"hi, "+who+"\"}";
	}
	@Override
	public void sayhi(String who, Current __current) {
		echohi(who,__current);
	}
}

