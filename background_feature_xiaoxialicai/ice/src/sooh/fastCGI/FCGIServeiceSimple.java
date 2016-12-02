package sooh.fastCGI;
import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.util.HashMap;

import sooh.fastCGI.Args;
import sooh.fastCGI.FastcgiAdapter;
import sooh.fastCGI.FastcgiResult;

public class FCGIServeiceSimple {
	public static sooh.fastCGI.serviceSimple iniFCGI;
	public static FCGIServeiceSimple factory(String ctrlname, String actname)
	{
		System.out.println("ice-call:bypush.sendMsg("+ctrlname+","+actname+")");
		FCGIServeiceSimple obj = new FCGIServeiceSimple();
		obj.cmd= "services/"+ctrlname+"/"+actname;
		try{
			obj.QString = "cmd="+URLEncoder.encode(obj.cmd,"utf-8");
		}catch(UnsupportedEncodingException $e){
			
		}
		//System.out.println("FCGIServeiceSimple->factory with-cmd:"+obj.cmd+" with-qstr:"+obj.QString);
		return obj;
	}
	protected String cmd;
	public FCGIServeiceSimple appendVar(String varname, String varval)
	{
		try{
			this.appendInner(URLEncoder.encode(varval,"utf-8"));
		}catch(UnsupportedEncodingException $e){
			
		}
		return this;
	}
	public FCGIServeiceSimple appendVar(String varname, int varval)
	{
		this.appendInner(String.valueOf(varval));
		return this;
	}
	protected int argindex=0;
	protected void appendInner(String str)
	{
		this.QString = this.QString+"&arg["+String.valueOf(argindex)+"]="+str;
		
		argindex++;
	}
	protected void appendInner(int num)
	{
		this.QString = this.QString+"&arg["+String.valueOf(argindex)+"]="+String.valueOf(num);
		argindex++;
	}
	protected String QString="";
	protected FastcgiResult ret=null;
	protected FastcgiAdapter fcgi=null;
	public String getStr()
	{
		String traceError="";
		try{

			//System.out.println("fcgi("+iniFCGI.getFCGI_host()+","+String.valueOf(iniFCGI.getFCGI_port()));

			fcgi = FastcgiAdapter.factory(iniFCGI.getFCGI_host(), iniFCGI.getFCGI_port());
			traceError = "factory fcgi done";
		}catch(Exception e){
			System.err.println("FCGIServeiceSimple error"+e.getMessage());
			traceError = "factory fcgi failed";
		}
		//System.out.println("fcgi ok");
		HashMap <String,String> arg= new HashMap<String,String>();
		arg.put(Args.REQUEST_METHOD, "GET");
		arg.put(Args.SCRIPT_FILENAME, iniFCGI.getFCGI_index());
		//System.out.println("php file:"+iniFCGI.getFCGI_index());
		try{
			if(fcgi==null){
				throw new Exception("fcgi-factory return null");
			}
			
			arg.put(Args.QUERY_STRING, this.QString);
			traceError = "fcgi-arg settted";
			fcgi.exec(arg);
			traceError = "fcgi-execed";
			//String s= fcgi.freeAndGetResultString();
			ret = fcgi.freeAndGetResult();
			//System.out.println("【fcgi】"+ret.body);
			return ret.body;

		}catch(Exception e){
			
			if(fcgi!=null){
				System.err.println("FCGIServeiceSimple error:[FCGIServeiceSimple]"+e.getClass().toString()+":"+e.getMessage()+" [lastError] FCGIServeiceSimple:"+traceError+" fcgi:"+fcgi.getLasterror());
				return ("【error】Error:"+e.getMessage()+" ("+traceError+"#"+fcgi.getLasterror()+")");
			}else{
				System.err.println("FCGIServeiceSimple error:[FCGIServeiceSimple]"+e.getClass().toString()+":"+e.getMessage()+" [lastError] FCGIServeiceSimple:"+traceError+" fcgi:not created");
				return("【error】Error:"+e.getMessage()+" (not socket error:"+traceError+")");
			}
		}	
	}
	public String getJsonStr() throws Ice.ConnectionLostException
	{
		String tmp = getStr();
		if(tmp.length()==0){
			System.err.print("on getJsonStr --empty-str-received");
			throw new Ice.ConnectionLostException();
		}else{
			char first = tmp.charAt(0);
			char last = tmp.charAt(tmp.length()-1);
			if((first=='[' && last==']') || (first=='{' && last=='}')){
				return tmp;
			}else{
				System.err.print("on getJsonStr --not-json-received:"+tmp);
				throw new Ice.ConnectionLostException();
			}
		}
		
	}
}
