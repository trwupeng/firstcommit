package sooh.fastCGI;
import java.util.HashMap;
public class FastcgiResult {
    public static final int REQ_STATE_WRITTEN    = 1;
    public static final int REQ_STATE_OK         = 2;
    public static final int REQ_STATE_ERR        = 3;
    public static final int REQ_STATE_TIMED_OUT  = 4;
	
	
	public int statusCode=0;
	public HashMap<String,String> headers;
	public String body ="";
	public String stdErr = "";
	public String debug="";
}
