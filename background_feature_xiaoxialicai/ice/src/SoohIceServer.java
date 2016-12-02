
import sooh.fastCGI.FCGIServeiceSimple;
import java.io.File;
import java.util.HashMap;
import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
//import sooh.services.msgcenter.messagerI;
public class SoohIceServer extends Ice.Application{


    public static void main(String[] args) {  
        SoohIceServer app = new SoohIceServer();  
        System.out.println("sooh-ice-server start...");  
		//---------------
		int status = app.run(args);
        System.exit(status);
        //System.exit(app.run(args));  
    } 

	private HashMap<String,String> myParseAdapterEndpoints(String iniFile)
	{
		HashMap <String,String> arg= new HashMap<String,String>();
		File file = new File(iniFile);
		BufferedReader reader = null;
        try {
            reader = new BufferedReader(new FileReader(file));
            String tempString = null;
            //int line = 1;
            int pos;
            String adapter;
            //System.out.println("myParseAdapterEndpoints 4"+iniFile);
            // bysmsAdapter.Endpoints=udp -h 10.144.152
            while ((tempString = reader.readLine()) != null) {
            	pos = tempString.indexOf(".Endpoints=");
            	if(pos!=-1){
            		adapter = tempString.substring(0,pos);
            		tempString = tempString.substring(pos+11);
            		System.out.println("FOUND "+adapter+" = "+tempString);
            		arg.put(adapter, tempString);
            	}
                // 显示行号
                //System.out.println("line " + line + ": " + tempString);
                
                //line++;
            }
            reader.close();
        } catch (IOException e) {
        	 System.out.println("myParseAdapterEndpoints ee "+e.getMessage()+"\n"+e.toString());
            e.printStackTrace();
        } finally {
            if (reader != null) {
                try {
                    reader.close();
                } catch (IOException e1) {
                }
            }
        }
		return arg;
	}

	@Override
	public int run(String[] args)
    {
        int status = 0;
        FCGIServeiceSimple.iniFCGI = new sooh.services.iniServiceSimple();
        Ice.Communicator ic = null;
        try {
            ic = Ice.Util.initialize(args);
            for(int i =0; i<args.length;i++){
            	System.out.println("args)"+args[i]);
            }
            
            //--Ice.Config=/var/www/SoohIce/ice-writable/iceNodes/node100/servers/XXMsg201/config/config
			String endpoint="tcp";
			HashMap<String,String> endpoints=null;
     
            if(args[0].substring(0, 13).equals("--Ice.Config=")){
            	endpoints = myParseAdapterEndpoints(args[0].substring(13));
            }else{
            	endpoints = new HashMap<String,String>();
            }
            
            if(endpoints.containsKey("logerAdapter")){
	            endpoint = endpoints.get("logerAdapter");
            System.out.println("logerAdapter)"+endpoint);
            Ice.ObjectAdapter adapter1 = ic.createObjectAdapterWithEndpoints("logerAdapter", endpoint);
            adapter1.add(new sooh.services.logcenter.logerI(), ic.stringToIdentity("loger"));
            adapter1.activate();
            }
            
            if(endpoints.containsKey("sample0000Adapter")){
	            endpoint = endpoints.get("sample0000Adapter");
            System.out.println("sample0000Adapter)"+endpoint);
            Ice.ObjectAdapter adapter2 = ic.createObjectAdapterWithEndpoints("sample0000Adapter", endpoint);
            adapter2.add(new sooh.services.samples.sample0000I(), ic.stringToIdentity("sample0000"));
            adapter2.activate();
            }
            
            if(endpoints.containsKey("triggersAdapter")){
	            endpoint = endpoints.get("triggersAdapter");
            System.out.println("triggersAdapter)"+endpoint);
            Ice.ObjectAdapter adapter3 = ic.createObjectAdapterWithEndpoints("triggersAdapter", endpoint);
            adapter3.add(new sooh.services.evtcenter.triggersI(), ic.stringToIdentity("triggers"));
            adapter3.activate();
            }
            
            if(endpoints.containsKey("bypushAdapter")){
	            endpoint = endpoints.get("bypushAdapter");
            System.out.println("bypushAdapter)"+endpoint);
            Ice.ObjectAdapter adapter4 = ic.createObjectAdapterWithEndpoints("bypushAdapter", endpoint);
            adapter4.add(new sooh.services.msgcenter.bypushI(), ic.stringToIdentity("bypush"));
            adapter4.activate();
            }
      
      		if(endpoints.containsKey("bysmsAdapter")){
	     		endpoint = endpoints.get("bysmsAdapter");
            System.out.println("bysmsAdapter)"+endpoint);
            Ice.ObjectAdapter adapter5 = ic.createObjectAdapterWithEndpoints("bysmsAdapter", endpoint);
            adapter5.add(new sooh.services.msgcenter.bysmsI(), ic.stringToIdentity("bysms"));
            adapter5.activate();
            }
            
            ic.waitForShutdown();
        } catch (Ice.LocalException e) {
            e.printStackTrace();
            status = 1;
        } catch (Exception e) {
            System.err.println(e.getMessage());
            status = 1;
        }

        if (ic != null) {
            try {
                ic.destroy();
            } catch (Exception e) {
                System.err.println(e.getMessage());
                status = 1;
            }
        }
        return status;
    }
}

