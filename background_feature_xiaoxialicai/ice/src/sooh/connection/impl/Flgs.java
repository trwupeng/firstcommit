package sooh.connection.impl;

public class Flgs {
	protected String _connStr;
	protected boolean _flgInUse=false;
	protected boolean byPconnect=false;
	public boolean getFlgInUse()
	{
		return _flgInUse;
	}
	public void setFlgInUse(boolean b)
	{
		_flgInUse = b;
	}
	protected String _lastError="";
	public String getLastError()
	{
		return _lastError;
	}
	public boolean connect(String ip_port)
	{
		return false;
	}
	public boolean pconnect(String ip_port)

	{
		byPconnect = true;
		return connect(ip_port);
	}
	public boolean disconnect()
	{
		return false;
	}
	public boolean pdisconnect()
	{
		byPconnect = false;
		return disconnect();
	}
//	public String readStr() {return "";}
//	public int writeStr(String request){return 0;}
//	public boolean ensure(){return false;}
//
//	public String identifier(){return "";}
}
