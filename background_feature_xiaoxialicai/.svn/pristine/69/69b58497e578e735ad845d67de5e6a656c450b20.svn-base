package sooh.connection;

public interface InterfaceConnection {
	public String readStr();
	public byte[] readBytes(int maxlen);
	public int writeStr(String request);
	public int writeBytes(byte[] request);
	public boolean connect(String connectStr);
	public boolean pconnect(String connectStr);
	public boolean disconnect();
	public boolean pdisconnect();
	public boolean ensure();
	public boolean getFlgInUse();
	public void setFlgInUse(boolean b);
	public String identifier();
	public String getLastError();
	
	public void debugSetTaskflg(String s);
}
