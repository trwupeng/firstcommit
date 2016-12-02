package sooh.fastCGI;
import java.util.HashMap;
public class FastcgiProtocol {
	public int version=0;
	public int type=0;
	public int requestId=0;
	public int contentLength=0;
	public int paddingLength=0;
	public int reserved=0;

	public static FastcgiProtocol parseHead(byte[] r) throws Exception
	{
		if(r==null){
			throw new Exception("read sock failed on parseHead");
		}
		FastcgiProtocol head = new FastcgiProtocol();
		head.version = byteRevt128(r[0])&0xff;
		head.type = byteRevt128(r[1])&0xff;
		head.requestId = ((byteRevt128(r[2])&0xff)<< 8)+(byteRevt128(r[3])&0xff);
		head.contentLength = ((byteRevt128(r[4])&0xff)<< 8)+(byteRevt128(r[5])&0xff);
		head.paddingLength = byteRevt128(r[6]);
		head.reserved = byteRevt128(r[7]);
		return head;
	}
	
	/**
     * Build a FastCGI packet
     *
     * @param Integer $type Type of the packet
     * @param String $content Content of the packet
     * @param Integer $requestId RequestId
     * @return String
     */
    public static byte[] buildPacket(byte type, byte[] content, int requestId)
    {
        int totLen = content.length;
        int beginIndex = 0;
        int segLen=0;
        if(totLen==0){
        	byte[] buf = {Consts.VERSION_1,type,  0,0,0,0,  0,0};
        	return buf;
        }else{
        	byte[] buf = {};
	        do {
	            // Packets can be a maximum of 65535 bytes
	        	segLen = beginIndex+0xffff;
	        	if(segLen>=totLen){
	        		segLen=totLen-beginIndex;
	        	}else{
	        		segLen = 0xffff;
	        	}
	        	
	        	byte[] parts = onepart(type,requestId,content,beginIndex,segLen);
	        	buf = byteMerge(buf,parts);
	        	
	        	beginIndex += segLen;
	        } while (beginIndex < totLen);
	        return buf;
        }
//        + (char)((requestId >> 8) & 0xFF) /* requestIdB1 */
//        + (char)(requestId & 0xFF)        /* requestIdB0 */
//        + (char)((segLen >> 8) & 0xFF)    /* contentLengthB1 */
//        + (char)(segLen & 0xFF)           /* contentLengthB0 */
    }
    private static byte[] onepart(byte type,int requestId,byte[] content, int start, int segLen)
    {
        byte[] buf = new byte[8+segLen];
    	System.arraycopy(content, start, buf, 8, segLen);
    	buf[0] = Consts.VERSION_1;
    	buf[1] = type;
    	if(requestId>0){
    		buf[2] = byteConv128((requestId >> 8) & 0xFF);
    		buf[3] = byteConv128(requestId & 0xFF);
    	}else{
    		buf[2] = 0;
        	buf[3] = 0;
    	}
    	if(segLen>0){
    		buf[4] = byteConv128((segLen >> 8) & 0xFF);
    		buf[5] = byteConv128(segLen & 0xFF);
    	}else{
    		buf[4] = 0;
        	buf[5] = 0;
    	}
    	
    	buf[6] = 0;
    	buf[7] = 0;

        return buf;
    }
    public static int byteRevt128(byte b)
    {
    	if(b<0){
			return (int)(256+b);
		}else{
			return (int)b;
		}
    }
    public static byte byteConv128(int v)
    {
    	if(v>=128){
			return (byte)(v - 256);
		}else{
			return (byte)v;
		}
    }
    /**
     * Build an FastCGI Name value pair
     *
     * @param String $name Name
     * @param String $value Value
     * @return String FastCGI Name value pair
     */
    public static byte[] buildNvpair(String name, String value) throws Exception
    {
        int nlen = name.length();
        int vlen = value.length();
        byte[] nvpair;
        if(nlen<128){
        	if(vlen<128){
        		nvpair = new byte[2];
        	}else{
        		nvpair = new byte[5];
        	}
        }else{
        	if(vlen<128){
        		nvpair = new byte[5];
        	}else{
        		nvpair = new byte[8];
        	}
        }
        int p;
        if (nlen < 128) {
            /* nameLengthB0 */
            nvpair[0] = (byte)nlen;
            p=1;
        } else {
            /* nameLengthB3 & nameLengthB2 & nameLengthB1 & nameLengthB0 */
            nvpair[0] = byteConv128((nlen >> 24) | 0x80) ;
            nvpair[1] = byteConv128((nlen >> 16) & 0xFF);
            nvpair[2] = byteConv128((nlen >> 8) & 0xFF);
            nvpair[3] = byteConv128(nlen & 0xFF);
            p=4;
        }
        if (vlen < 128) {
            /* valueLengthB0 */
            nvpair[p] = (byte)vlen;
        } else {
            /* valueLengthB3 & valueLengthB2 & valueLengthB1 & valueLengthB0 */
        	nvpair[p] = byteConv128((vlen >> 24) | 0x80);
        	nvpair[p+1] = byteConv128((vlen >> 16) & 0xFF);
        	nvpair[p+2] = byteConv128((vlen >> 8) & 0xFF);
        	nvpair[p+3] = byteConv128(vlen & 0xFF);
        }

        /* nameData & valueData */
        return byteMerge(nvpair, byteMerge(name.getBytes("utf-8"),value.getBytes("utf-8")));
    }

    /**
     * Read a set of FastCGI Name value pairs
     *
     * @param String $data Data containing the set of FastCGI NVPair
     * @param Integer $length
     * @return HashMap <String,String>
     */
    public static HashMap <String,String> readNvpair(String str, int length)
    {
//        if ($length == null) {
//            $length = strlen($data);
//        }

        HashMap <String,String> ret= new HashMap<String,String>();
        char[]data=str.toCharArray();
        int p = 0;
        int nlen;
        int vlen;
        while (p != length) {
            nlen = (int)data[p++];
            if (nlen >= 128) {
                nlen = (nlen & 0x7F << 24);
                nlen |= ((int)(data[p++]) << 16);
                nlen |= ((int)(data[p++]) << 8);
                nlen |= ((int)(data[p++]));
            }
            vlen = (int)(data[p++]);
            if (vlen >= 128) {
                vlen = (nlen & 0x7F << 24);
                vlen |= ((int)(data[p++]) << 16);
                vlen |= ((int)(data[p++]) << 8);
                vlen |= ((int)(data[p++]));
            }
            ret.put(str.substring(p,p+nlen),str.substring(p+nlen, p+nlen+vlen));
            p += (nlen + vlen);
        }

        return ret;
    }
    public static byte[] byteMerge(byte[] byte_1, byte[] byte_2){  
        byte[] byte_3 = new byte[byte_1.length+byte_2.length];  
        System.arraycopy(byte_1, 0, byte_3, 0, byte_1.length);  
        System.arraycopy(byte_2, 0, byte_3, byte_1.length, byte_2.length);  
        return byte_3;
    }
    /**
     * Decode a FastCGI Packet
     *
     * @param String $data String containing all the packet
     * @return HashMap <String,Integer>
     */
    public static HashMap <String,Integer> decodePacketHeader(String str)
    {
    	HashMap <String,Integer> ret= new HashMap<String,Integer>();
    	char[]data=str.toCharArray();
    	ret.put("version", (int)data[0]);
    	ret.put("type", (int)(data[1]));
    	ret.put("requestId", ((int)(data[2])<<8)+(int)(data[3]));
    	ret.put("contentLength", ((int)(data[4])<<8)+(int)(data[5]));
    	ret.put("paddingLength", (int)(data[6]));
    	ret.put("reserved", (int)(data[7]));
        return ret;
    }
    
    public static FastcgiResult formatResponse(byte[] bs)throws Exception
    {
    	if(bs==null){
			throw new Exception("read sock failed on formatResponse");
		}
        // HTTP uses 2 CR/NL's to separate body from header
    	String stdout = new String(bs);
        int boundary = stdout.indexOf("\r\n\r\n");
        HashMap <String,String> headers= new HashMap<String,String>();
        
        if (-1 == boundary) {
        	FastcgiResult ret = new FastcgiResult();
            ret.statusCode = FastcgiResult.REQ_STATE_ERR;
            ret.stdErr="invalid output got";
            System.out.println("[invalid output got]"+stdout);
            ret.body = stdout;
            ret.headers = null;
            return ret;
        }else{

            // Split the header from the body
        	//TODO: utf-8 bom?? Primary script unknown??????Status: 404 Not Found
            String rawHead = stdout.substring(3, boundary);

//          byte[] ttt = stdout.getBytes();
//          int t = ttt.length-15;
//          if(t<0)t=0;
//          for(;t<ttt.length;t++){
//          	System.out.println("["+t+"]"+(int)ttt[t]);
//          }
            
            //int boundary2 = stdout.indexOf(""+(char)1+(char)3+(char)0+(char)1+(char)0+(char)8+(char)0+(char)0+(char)0+(char)0);
            int boundary2 = stdout.indexOf(""+(char)0+(char)0+(char)0+(char)0);
            if(boundary2!=-1){
            	stdout = stdout.substring(boundary + 4,boundary2);
            }else{
            	stdout = stdout.substring(boundary + 4);
            }
            // Iterate over the found headers
            String[] headerLines = rawHead.split("\n");
            for(int i=0;i<headerLines.length;i++){
            	String line = headerLines[i];

            	String[] tmp = line.split(": ");
            	if(tmp.length>=2){
            		String headerName  = tmp[0].trim();
            		tmp[0]="";
            		StringBuffer sb = new StringBuffer();
            		for(int k = 0; k < tmp.length; k++){
            		 sb. append(tmp[k]);
            		}
            		String headerValue = sb.toString();
                    
                    if (headers.containsKey(headerName)) {
                        //TODO header 是数组的情况 Ensure is array
//                        if (!is_array($headers[$headerName])) {
//                            $headers[$headerName] = [ $headers[$headerName] ];
//                        }
//                        $headers[$headerName][] = $headerValue;
                        headers.put(headerName, headerValue);
                    } else {
                        headers.put(headerName, headerValue);
                    }
            	}
            }
            if(false==headers.containsKey("status")){
            	headers.put("status", "200 OK");
            }
            FastcgiResult ret = new FastcgiResult();
            ret.statusCode = FastcgiResult.REQ_STATE_OK;
            ret.stdErr="";
            ret.body = stdout;
            ret.headers = headers;

            return ret;
        }
        
    }
}
