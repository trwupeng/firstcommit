module sooh{
    module services{
        module logcenter{
        	/**
        	 * 日志中心
        	 */
            interface loger{
            	// 记录日志 
                string writeSyn(string logdata, string reqdata);
                // 记录日志（oneway） 
                void   writeAsy(string logdata, string reqdata);
            };
        };
    };
};

