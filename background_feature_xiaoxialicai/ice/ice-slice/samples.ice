module sooh{
	module services{
		/**
		 * 测试用的
		 */
		module samples{
			interface sample0000{
                // 测试用的say hello （oneway）
                void   sayhi(string who);
                // 测试用的say hello 
                string echohi(string who);
                // 测试用的say hello （oneway）
                void   forwardAsy(string serviceClass, string serviceFunc, string jsonedArray);
                // 测试用的say hello 
                string forwardSyn(string serviceClass, string serviceFunc, string jsonedArray);   
			};
		};
	};
};
