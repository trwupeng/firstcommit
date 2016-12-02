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
// Generated from file `Instrumentation.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//

require_once __DIR__.'/../Ice/EndpointF.php';
require_once __DIR__.'/../Ice/ConnectionF.php';
require_once __DIR__.'/../Ice/Current.php';

global $Ice_Instrumentation__t_Observer;

if(!interface_exists('Ice_Instrumentation_Observer',false))
{
    interface Ice_Instrumentation_Observer
    {
        public function attach();
        public function detach();
        public function failed($exceptionName);
    }

    $Ice_Instrumentation__t_Observer = IcePHP_defineClass('::Ice::Instrumentation::Observer', 'Ice_Instrumentation_Observer', -1, true, false, $Ice__t_Object, null, null);
}

global $Ice_Instrumentation__t_ThreadState;

if(!class_exists('Ice_Instrumentation_ThreadState',false))
{
    class Ice_Instrumentation_ThreadState
    {
        const ThreadStateIdle = 0;
        const ThreadStateInUseForIO = 1;
        const ThreadStateInUseForUser = 2;
        const ThreadStateInUseForOther = 3;
    }

    $Ice_Instrumentation__t_ThreadState = IcePHP_defineEnum('::Ice::Instrumentation::ThreadState', array('ThreadStateIdle', 0, 'ThreadStateInUseForIO', 1, 'ThreadStateInUseForUser', 2, 'ThreadStateInUseForOther', 3));
}

global $Ice_Instrumentation__t_ThreadObserver;

if(!interface_exists('Ice_Instrumentation_ThreadObserver',false))
{
    interface Ice_Instrumentation_ThreadObserver extends Ice_Instrumentation_Observer
    {
        public function stateChanged($oldState, $newState);
    }

    $Ice_Instrumentation__t_ThreadObserver = IcePHP_defineClass('::Ice::Instrumentation::ThreadObserver', 'Ice_Instrumentation_ThreadObserver', -1, true, false, $Ice__t_Object, array($Ice_Instrumentation__t_Observer), null);
}

global $Ice_Instrumentation__t_ConnectionState;

if(!class_exists('Ice_Instrumentation_ConnectionState',false))
{
    class Ice_Instrumentation_ConnectionState
    {
        const ConnectionStateValidating = 0;
        const ConnectionStateHolding = 1;
        const ConnectionStateActive = 2;
        const ConnectionStateClosing = 3;
        const ConnectionStateClosed = 4;
    }

    $Ice_Instrumentation__t_ConnectionState = IcePHP_defineEnum('::Ice::Instrumentation::ConnectionState', array('ConnectionStateValidating', 0, 'ConnectionStateHolding', 1, 'ConnectionStateActive', 2, 'ConnectionStateClosing', 3, 'ConnectionStateClosed', 4));
}

global $Ice_Instrumentation__t_ConnectionObserver;

if(!interface_exists('Ice_Instrumentation_ConnectionObserver',false))
{
    interface Ice_Instrumentation_ConnectionObserver extends Ice_Instrumentation_Observer
    {
        public function sentBytes($num);
        public function receivedBytes($num);
    }

    $Ice_Instrumentation__t_ConnectionObserver = IcePHP_defineClass('::Ice::Instrumentation::ConnectionObserver', 'Ice_Instrumentation_ConnectionObserver', -1, true, false, $Ice__t_Object, array($Ice_Instrumentation__t_Observer), null);
}

global $Ice_Instrumentation__t_DispatchObserver;

if(!interface_exists('Ice_Instrumentation_DispatchObserver',false))
{
    interface Ice_Instrumentation_DispatchObserver extends Ice_Instrumentation_Observer
    {
        public function userException();
        public function reply($size);
    }

    $Ice_Instrumentation__t_DispatchObserver = IcePHP_defineClass('::Ice::Instrumentation::DispatchObserver', 'Ice_Instrumentation_DispatchObserver', -1, true, false, $Ice__t_Object, array($Ice_Instrumentation__t_Observer), null);
}

global $Ice_Instrumentation__t_ChildInvocationObserver;

if(!interface_exists('Ice_Instrumentation_ChildInvocationObserver',false))
{
    interface Ice_Instrumentation_ChildInvocationObserver extends Ice_Instrumentation_Observer
    {
        public function reply($size);
    }

    $Ice_Instrumentation__t_ChildInvocationObserver = IcePHP_defineClass('::Ice::Instrumentation::ChildInvocationObserver', 'Ice_Instrumentation_ChildInvocationObserver', -1, true, false, $Ice__t_Object, array($Ice_Instrumentation__t_Observer), null);
}

global $Ice_Instrumentation__t_RemoteObserver;

if(!interface_exists('Ice_Instrumentation_RemoteObserver',false))
{
    interface Ice_Instrumentation_RemoteObserver extends Ice_Instrumentation_ChildInvocationObserver
    {
    }

    $Ice_Instrumentation__t_RemoteObserver = IcePHP_defineClass('::Ice::Instrumentation::RemoteObserver', 'Ice_Instrumentation_RemoteObserver', -1, true, false, $Ice__t_Object, array($Ice_Instrumentation__t_ChildInvocationObserver), null);
}

global $Ice_Instrumentation__t_CollocatedObserver;

if(!interface_exists('Ice_Instrumentation_CollocatedObserver',false))
{
    interface Ice_Instrumentation_CollocatedObserver extends Ice_Instrumentation_ChildInvocationObserver
    {
    }

    $Ice_Instrumentation__t_CollocatedObserver = IcePHP_defineClass('::Ice::Instrumentation::CollocatedObserver', 'Ice_Instrumentation_CollocatedObserver', -1, true, false, $Ice__t_Object, array($Ice_Instrumentation__t_ChildInvocationObserver), null);
}

global $Ice_Instrumentation__t_InvocationObserver;

if(!interface_exists('Ice_Instrumentation_InvocationObserver',false))
{
    interface Ice_Instrumentation_InvocationObserver extends Ice_Instrumentation_Observer
    {
        public function retried();
        public function userException();
        public function getRemoteObserver($con, $endpt, $requestId, $size);
        public function getCollocatedObserver($adapter, $requestId, $size);
    }

    $Ice_Instrumentation__t_InvocationObserver = IcePHP_defineClass('::Ice::Instrumentation::InvocationObserver', 'Ice_Instrumentation_InvocationObserver', -1, true, false, $Ice__t_Object, array($Ice_Instrumentation__t_Observer), null);
}

global $Ice_Instrumentation__t_ObserverUpdater;

if(!interface_exists('Ice_Instrumentation_ObserverUpdater',false))
{
    interface Ice_Instrumentation_ObserverUpdater
    {
        public function updateConnectionObservers();
        public function updateThreadObservers();
    }

    $Ice_Instrumentation__t_ObserverUpdater = IcePHP_defineClass('::Ice::Instrumentation::ObserverUpdater', 'Ice_Instrumentation_ObserverUpdater', -1, true, false, $Ice__t_Object, null, null);
}

global $Ice_Instrumentation__t_CommunicatorObserver;

if(!interface_exists('Ice_Instrumentation_CommunicatorObserver',false))
{
    interface Ice_Instrumentation_CommunicatorObserver
    {
        public function getConnectionEstablishmentObserver($endpt, $connector);
        public function getEndpointLookupObserver($endpt);
        public function getConnectionObserver($c, $e, $s, $o);
        public function getThreadObserver($parent, $id, $s, $o);
        public function getInvocationObserver($prx, $operation, $ctx);
        public function getDispatchObserver($c, $size);
        public function setObserverUpdater($updater);
    }

    $Ice_Instrumentation__t_CommunicatorObserver = IcePHP_defineClass('::Ice::Instrumentation::CommunicatorObserver', 'Ice_Instrumentation_CommunicatorObserver', -1, true, false, $Ice__t_Object, null, null);
}
?>
