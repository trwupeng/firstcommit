<?php
// **********************************************************************
//
// Copyright (c) 2003-2016 ZeroC, Inc. All rights reserved.
//
// This copy of Ice is licensed to you under the terms described in the
// ICE_LICENSE file included in this distribution.
//
// **********************************************************************
//
// Ice version 3.6.2
//
// <auto-generated>
//
// Generated from file `Session.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//

require_once __DIR__.'/../Glacier2/Session.php';
require_once __DIR__.'/../IceGrid/Exception.php';

global $IceGrid__t_Session;
global $IceGrid__t_SessionPrx;

if(!interface_exists('IceGrid_Session',false))
{
    interface IceGrid_Session extends Glacier2_Session
    {
        public function keepAlive();
        public function allocateObjectById($id);
        public function allocateObjectByType($type);
        public function releaseObject($id);
        public function setAllocationTimeout($timeout);
    }

    class IceGrid_SessionPrxHelper
    {
        public static function checkedCast($proxy, $facetOrCtx=null, $ctx=null)
        {
            return $proxy->ice_checkedCast('::IceGrid::Session', $facetOrCtx, $ctx);
        }

        public static function uncheckedCast($proxy, $facet=null)
        {
            return $proxy->ice_uncheckedCast('::IceGrid::Session', $facet);
        }

        public static function ice_staticId()
        {
            return '::IceGrid::Session';
        }
    }

    $IceGrid__t_Session = IcePHP_defineClass('::IceGrid::Session', 'IceGrid_Session', -1, true, false, $Ice__t_Object, array($Glacier2__t_Session), null);

    $IceGrid__t_SessionPrx = IcePHP_defineProxy($IceGrid__t_Session);

    IcePHP_defineOperation($IceGrid__t_Session, 'keepAlive', 2, 2, 0, null, null, null, null);
    IcePHP_defineOperation($IceGrid__t_Session, 'allocateObjectById', 0, 0, 0, array(array($Ice__t_Identity, false, 0)), null, array($Ice__t_ObjectPrx, false, 0), array($IceGrid__t_ObjectNotRegisteredException, $IceGrid__t_AllocationException));
    IcePHP_defineOperation($IceGrid__t_Session, 'allocateObjectByType', 0, 0, 0, array(array($IcePHP__t_string, false, 0)), null, array($Ice__t_ObjectPrx, false, 0), array($IceGrid__t_AllocationException));
    IcePHP_defineOperation($IceGrid__t_Session, 'releaseObject', 0, 0, 0, array(array($Ice__t_Identity, false, 0)), null, null, array($IceGrid__t_ObjectNotRegisteredException, $IceGrid__t_AllocationException));
    IcePHP_defineOperation($IceGrid__t_Session, 'setAllocationTimeout', 2, 2, 0, array(array($IcePHP__t_int, false, 0)), null, null, null);
}
?>
