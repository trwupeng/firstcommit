package sooh.connection.impl;
import sooh.connection.InterfaceConnection;
import java.net.*;

//import sooh.fastcgi.Base64;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

public class TCPStd extends Flgs implements InterfaceConnection{
	private Socket sock=null;
	private InputStream reader;
	private OutputStream writer;
	public String identifier()
	{
		return "port:"+sock.getLocalPort();
	}
	public boolean ensure() //throws UnknownHostException,IOException
	{
		boolean b;
		traceSockState("before re-connect");
		if(byPconnect){
			b = pconnect(_connStr);
		}else{
			b = connect(_connStr);
		}
		traceSockState("after re-connect");
		return b;
	}
	private void traceSockState(String prefix)
	{
		if(sock!=null){
//			System.out.println(prefix+")isConnected..."+(sock.isConnected()?"yes":"no")+"...."+identifier());
//			System.out.println(taskFlg+':'+debugFlg+prefix+")isClosed..."+(sock.isClosed()?"yes":"no")+"...."+identifier());
//			System.out.println(prefix+")isInputShutdown..."+(sock.isInputShutdown()?"yes":"no")+"...."+identifier());
//			System.out.println(prefix+")isOutputShutdown..."+(sock.isOutputShutdown()?"yes":"no")+"...."+identifier());
		}else{
//			System.out.println(prefix+")isConnected...null"+"...."+identifier());
//			System.out.println(taskFlg+':'+debugFlg+prefix+")isClosed...null"+"...."+identifier());
//			System.out.println(prefix+")isInputShutdown...null"+"...."+identifier());
//			System.out.println(prefix+")isOutputShutdown...null"+"...."+identifier());
		}
	}
	public boolean pconnect(String ip_port)
	{
		return connect(ip_port);
	}
	public void debugSetTaskflg(String s){
		taskFlg=s;
	}
	private String taskFlg = "task";
	private String debugFlg="default";
	//private SocketAddress address;
	public boolean connect(String ip_port)
	{
		_connStr=ip_port;
		String[] tmp = ip_port.split(":");
		//if(sock==null){
			debugFlg = String.valueOf(Math.round(Math.random()*10000));
			try{
				_lastError = "";
				
				sock = new Socket(tmp[0],Integer.parseInt(tmp[1]));
				//address = sock.getRemoteSocketAddress();
				sock.setKeepAlive(false);
				sock.setReuseAddress(false);
				sock.setTcpNoDelay(true);
				sock.setReceiveBufferSize(16192);
				
				//System.out.println("connect to "+sock.getChannel().getRemoteAddress().toString());
				//读取服务器端数据    
				reader = sock.getInputStream();
		        //向服务器端发送数据    
				writer = sock.getOutputStream();  
				return true;
			}catch(UnknownHostException e){
				_lastError = "UnknownHostException:"+ip_port;
				return false;
			}catch(IOException e){
				_lastError = "IOException:"+e.getMessage();
				return false;
			}
//		}else{
//			//sock.c;
//			//SocketAddress addr = So();
//			try{
//				sock.connect(address);
//				//读取服务器端数据    
//				reader = sock.getInputStream();
//		        //向服务器端发送数据    
//				writer = sock.getOutputStream(); 
//				traceSockState("reuse socket");
//				return true;
//			}catch(UnknownHostException e){
//				_lastError = "UnknownHostException:"+ip_port;
//				return false;
//			}catch(IOException e){
//				_lastError = "IOException:"+e.getMessage();
//				return false;
//			}
//			
//		}
	}
	public boolean disconnect() 
	{
		traceSockState("before disconnect");
		if(sock!=null && byPconnect==false){
			try{
				reader.close();
				reader=null;
				writer.close();
				writer=null;
				sock.close();
				sock = null;
				traceSockState("after Realclose");
				//reader=null;
				//writer=null;
				//sock=null;
				
				return true;
			}catch(IOException e){
				traceSockState("after Realclose-failed:"+e.getMessage());
				return false;
			}
			
		}else{
			traceSockState("after Fakeclose");
			return false;
		}
	}
	/**
	 * TODO:返回实际写入的字节数
	 * @param str
	 * @return
	 */
	public int writeBytes(byte[] str)
	{
		try{
			traceSockState("beforeWrite");
			writer.write(str);
			traceSockState("afterWrite");
			return str.length;
		}catch(Exception e){
			_lastError = "IOException when writeStr:"+e.getMessage();
			//System.out.println("Error on write:"+e.getClass()+"#"+e.getMessage());
			return 0;
		}
	}	
	/**
	 * TODO:返回实际写入的字节数
	 * @param str
	 * @return
	 */
	public int writeStr(String str)
	{
		try{
			return writeBytes(str.getBytes("utf-8"));
		}catch(Exception e){
			_lastError = "IOException when writeStr:"+e.getMessage();
			return 0;
		}
	}
	public byte[] readBytes(int maxlen)
	{
		int retryTimes=330;
		do{
			try{
				traceSockState("before--read[start]--"+String.valueOf(maxlen));
				int l = reader.available();
				//System.out.println("available to read "+reader.available());
				if(l>=maxlen){
					traceSockState("before--read[available]--"+String.valueOf(l));
			        byte[] buf = new byte[maxlen];
			        reader.read(buf, 0, maxlen);
			        traceSockState("before--read[readed]--"+String.valueOf(maxlen));
//					reader.readFully(buf);
					return buf;
				}else{
					if(sock.isClosed()){
						_lastError = "socket_closed when readStr";
						return null;
					}
				}
				traceSockState("before--read[retry]");
			}catch (IOException e){
				_lastError = "IOException when readStr:"+e.getMessage();
				return null;
			}
			try{
				Thread.sleep(30);
			}catch (InterruptedException e){
				
			}
			retryTimes--;
		}while(retryTimes>0);
		return null;
	}
	/**
	 * 读取，最长等待时间10秒
	 * @param maxsize
	 * @return
	 * @throws IOException, InterruptedException 
	 */
	public String readStr() 
	{
		int retryTimes=330;
		do{
			try{
				int l = reader.available();
				//System.out.println("available to read "+reader.available());
//				if(l>0){
//			        byte[] buf = new byte[l];
//					reader.read(arg0);
//
//					//System.out.println("after read - "+buf.length);
//					String str2=new String(buf,"UTF-8");
//					//System.out.println(str2);
//					traceSockState("afterRead");
//					return str2.trim();
//				}
			}catch (IOException e){
				_lastError = "IOException when readStr:"+e.getMessage();
				return "";
			}
			try{
				Thread.sleep(30);
			}catch (InterruptedException e){
				
			}
			retryTimes--;
		}while(retryTimes>0);
		return "";
	}
}
