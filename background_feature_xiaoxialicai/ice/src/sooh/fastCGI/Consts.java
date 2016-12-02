package sooh.fastCGI;

public class Consts {
	final static byte VERSION_1        = 1;

    // Packet types
    final static byte BEGIN_REQUEST        = 1;
    final static byte ABORT_REQUEST        = 2;
    final static byte END_REQUEST          = 3;
    final static byte PARAMS               = 4;
    final static byte STDIN                = 5;
    final static byte STDOUT               = 6;
    final static byte STDERR               = 7;
    final static byte DATA                 = 8;
    final static byte GET_VALUES           = 9;
    final static byte GET_VALUES_RESULT    = 10;
    final static byte UNKNOWN_TYPE         = 11;

    final static int RESPONDER            = 1;
    final static int AUTHORIZER           = 2;
    final static int FILTER               = 3;

    // Response codes
    final static int  REQUEST_COMPLETE     = 0;
    final static int  CANT_MPX_CONN        = 1;
    final static int  OVERLOADED           = 2;
    final static int  UNKNOWN_ROLE         = 3;
    //final int  MAX_CONNS            = 'MAX_CONNS';
    //final int  MAX_REQS             = 'MAX_REQS';
    //final int  MPXS_CONNS           = 'MPXS_CONNS';

    // Number of bytes used in FastCGI header packet
    final static int  HEADER_LEN           = 8;    
}
