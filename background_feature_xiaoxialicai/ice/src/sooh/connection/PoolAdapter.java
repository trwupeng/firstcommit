package sooh.connection;
import java.util.HashMap;
import sooh.connection.impl.*;
public class PoolAdapter {
	public static int maxSize=10;
	private static HashMap<String,InterfaceConnection[]> pool=null;
	private static HashMap<String,Integer> prefers=null;
	public static void putConnection(InterfaceConnection connection)
	{
		connection.disconnect();
		connection.setFlgInUse(false);
	}
	protected static InterfaceConnection newOne(String protocol,String connectionStr, boolean usePconnect)
	{
		if(protocol.compareTo("TCPStd")==0){
			TCPStd conn = new TCPStd();

			if(usePconnect){
				conn.pconnect(connectionStr);
			}else{
				conn.connect(connectionStr);
			}
			return conn;
		}else{
			return null;
		}
	}
	public static InterfaceConnection getConnection(String connectionStr)
			throws Exception
	{
		return getConnection(connectionStr,false);
	}
	public static InterfaceConnection getConnection(String connectionStr, boolean usePconnect)
		throws Exception
	{
		//TCPStd://127.0.0.1:9000
		int pos = connectionStr.indexOf("://");
		String protocol = connectionStr.substring(0,pos);
		connectionStr = connectionStr.substring(pos+3); 
		InterfaceConnection conn=null;
		
		if(pool==null){
			conn = newOne(protocol,connectionStr,usePconnect);
			InterfaceConnection[] arr = new InterfaceConnection[maxSize];
			arr[0] = conn;
			pool = new HashMap<String,InterfaceConnection[]>();
			pool.put(protocol,arr);
			//System.out.println("------------init");
			prefers = new HashMap<String,Integer>();
			prefers.put(protocol, 1);
		}else{
			int preferid = prefers.get(protocol);
			int lastId = preferid+maxSize;
			InterfaceConnection[] arr = pool.get(protocol);
			for(;preferid<lastId;preferid++){
				int i = preferid % maxSize;
				conn = arr[i];
				if(conn==null){
					//System.out.println(i+")------------next new");
					arr[i] = conn = newOne(protocol,connectionStr,usePconnect);
					prefers.put(protocol,i+1);
					break;
				}else {
					if(conn.getFlgInUse()==false){
						if(conn.ensure()){
							prefers.put(protocol,i+1);
							//System.out.println(i+")------------next re-use ok");
							break;
						}else{
							//System.out.println(i+")------------next re-use next");
						}
					}
				}					
			}
		}
		
		conn.setFlgInUse(true);
		return conn;
	}
}
