package sooh.fastCGI;
import java.util.HashMap;
import java.util.Map.Entry;

import sooh.connection.*;
public class FastcgiAdapter {
	public static FastcgiAdapter factory(String ipOrFile, int port)
			throws Exception
	{
	
//		if(port>0 && _port==0){
			_fcgi = new FastcgiAdapter();
			_port=port;
			_ip=ipOrFile;
			//System.out.println("FastcgiAdapter::factory-new("+ipOrFile+","+String.valueOf(port)+")");
			return _fcgi;
//		}else{
//			System.out.println("FastcgiAdapter::factory-exist("+ipOrFile+","+String.valueOf(port)+")");
//			return _fcgi;
//		}
	}
	private static FastcgiAdapter _fcgi;
	private static int _port=0;
	private static String _ip;
	public FastcgiResult ret;
	private InterfaceConnection conn;
	public String freeAndGetResultString() throws Exception
	{
		//sooh.connection.PoolAdapter.putConnection(conn);
		if(ret.statusCode!=FastcgiResult.REQ_STATE_OK){
			throw new Exception("fastcgi result code:"+ret.statusCode+" body:"+ret.body + " error:"+ret.stdErr);
		}
		return ret.body;
	}
	private String _lastError;
	public String getLasterror()
	{
		return _lastError;
	}
	public FastcgiResult freeAndGetResult() throws Exception
	{
		//sooh.connection.PoolAdapter.putConnection(conn);
		if(ret.statusCode!=FastcgiResult.REQ_STATE_OK){
			throw new Exception("fastcgi result code:"+ret.statusCode+" body:"+ret.body + " error:"+ret.stdErr);
		}
		return ret;
	}
	private static int _requestCounter=32765;//0
	
	public FastcgiAdapter exec(HashMap <String,String> args) throws Exception
	{
		try{
			String taskFlg = Thread.currentThread().getName();//String.valueOf(Math.round(Math.random()*10000));
			_lastError="try get Connection";
	        conn = sooh.connection.PoolAdapter.getConnection("TCPStd://"+_ip+":"+_port,true);
	        if(conn==null){
	        	_lastError = "connect failed";
	        	throw new Exception(_lastError);
	        }
	        _lastError="get Connection ok";
	        // Ensure new requestID is not already being tracked
	        _requestCounter++;
	        if (_requestCounter >= 32767 /* or 32768 */) {
	            _requestCounter = 1;
	        }
	        
	        byte keepAlive=0;
	        byte[] tmp = {0,Consts.RESPONDER,keepAlive,0,0,0,0,0};
	       
	        byte[] request = FastcgiProtocol.buildPacket(Consts.BEGIN_REQUEST, tmp, _requestCounter);
	        byte[] paramsRequest = {};

	        for(Entry<String, String> entry:args.entrySet()){
	        	paramsRequest = FastcgiProtocol.byteMerge(paramsRequest,FastcgiProtocol.buildNvpair(entry.getKey().toString(), entry.getValue().toString()));
	        }
	    
	        if (paramsRequest.length>0) {
	        	request = FastcgiProtocol.byteMerge(request, FastcgiProtocol.buildPacket(Consts.PARAMS, paramsRequest, _requestCounter));
	        }
	        byte[] emptyb = {};
	        request = FastcgiProtocol.byteMerge(request, FastcgiProtocol.buildPacket(Consts.PARAMS, emptyb, _requestCounter));
	        request = FastcgiProtocol.byteMerge(request, FastcgiProtocol.buildPacket(Consts.STDIN, emptyb, _requestCounter));
	        _lastError="request build ok";
	        //System.out.println("Wrote socket:"+Base64.encode(request.getBytes()));
	        //conn.debugSetTaskflg(taskFlg);
	        if (0 == conn.writeBytes(request)) {
	            // The developer may wish to close() and re-open the socket
	        	_lastError = "write socket end"+conn.getLastError();
	            throw new Exception("write socket failed:"+_lastError);
	        }else{
	        	_lastError="request ok";
	        }

	        //conn.debugSetTaskflg(taskFlg);
	        byte[] tmpbs = conn.readBytes(8);
	        _lastError="parse head ok";
//		        Base64.printBytes(tmpbs);
	        FastcgiProtocol packHead = FastcgiProtocol.parseHead(tmpbs);
	        _lastError="parse head ok2";
	        int len = packHead.contentLength;
	        _lastError="parse head ok2 - 0 "+String.valueOf(len);
	        do{
	        	//conn.debugSetTaskflg(taskFlg);
	        	 tmpbs=conn.readBytes(len);
	        	 len = packHead.contentLength-tmpbs.length;
	        	 _lastError="parse head ok2 --- "+String.valueOf(len);
	        }while(tmpbs.length<packHead.contentLength);
	        _lastError="parse head ok3";
	        ret = FastcgiProtocol.formatResponse(tmpbs);
	        _lastError="parse head ok4";
	        if(packHead.paddingLength>0){
	        	//conn.debugSetTaskflg(taskFlg);
	        	conn.readBytes(packHead.paddingLength);
	        }
	        _lastError="parse head ok5";
	        if(ret.statusCode!=FastcgiResult.REQ_STATE_OK){
	        	_lastError = conn.getLastError();
	        }
	        _lastError="parse head ok6";
	        ret.debug = conn.identifier()+" head.content-len="+packHead.contentLength+" head.requestId="+packHead.requestId;
	        _lastError="parse head ok7";
	        sooh.connection.PoolAdapter.putConnection(conn);
	        _lastError="parse head ok8";
		}catch(Exception e){
			sooh.connection.PoolAdapter.putConnection(conn);
			e.printStackTrace();
			throw e;
		}
		return this;
	}
}
