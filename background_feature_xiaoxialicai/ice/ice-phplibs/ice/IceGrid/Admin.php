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
// Generated from file `Admin.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//

require_once __DIR__.'/../Ice/Identity.php';
require_once __DIR__.'/../Ice/BuiltinSequences.php';
require_once __DIR__.'/../Ice/Properties.php';
require_once __DIR__.'/../Ice/SliceChecksumDict.php';
require_once __DIR__.'/../Glacier2/Session.php';
require_once __DIR__.'/../IceGrid/Exception.php';
require_once __DIR__.'/../IceGrid/Descriptor.php';

global $IceGrid__t_ServerState;

if(!class_exists('IceGrid_ServerState',false))
{
    class IceGrid_ServerState
    {
        const Inactive = 0;
        const Activating = 1;
        const ActivationTimedOut = 2;
        const Active = 3;
        const Deactivating = 4;
        const Destroying = 5;
        const Destroyed = 6;
    }

    $IceGrid__t_ServerState = IcePHP_defineEnum('::IceGrid::ServerState', array('Inactive', 0, 'Activating', 1, 'ActivationTimedOut', 2, 'Active', 3, 'Deactivating', 4, 'Destroying', 5, 'Destroyed', 6));
}

global $IceGrid__t_StringObjectProxyDict;

if(!isset($IceGrid__t_StringObjectProxyDict))
{
    $IceGrid__t_StringObjectProxyDict = IcePHP_defineDictionary('::IceGrid::StringObjectProxyDict', $IcePHP__t_string, $Ice__t_ObjectPrx);
}

global $IceGrid__t_ObjectInfo;

if(!class_exists('IceGrid_ObjectInfo',false))
{
    class IceGrid_ObjectInfo
    {
        public function __construct($proxy=null, $type='')
        {
            $this->proxy = $proxy;
            $this->type = $type;
        }

        public function __toString()
        {
            global $IceGrid__t_ObjectInfo;
            return IcePHP_stringify($this, $IceGrid__t_ObjectInfo);
        }

        public $proxy;
        public $type;
    }

    $IceGrid__t_ObjectInfo = IcePHP_defineStruct('::IceGrid::ObjectInfo', 'IceGrid_ObjectInfo', array(
        array('proxy', $Ice__t_ObjectPrx), 
        array('type', $IcePHP__t_string)));
}

global $IceGrid__t_ObjectInfoSeq;

if(!isset($IceGrid__t_ObjectInfoSeq))
{
    $IceGrid__t_ObjectInfoSeq = IcePHP_defineSequence('::IceGrid::ObjectInfoSeq', $IceGrid__t_ObjectInfo);
}

global $IceGrid__t_AdapterInfo;

if(!class_exists('IceGrid_AdapterInfo',false))
{
    class IceGrid_AdapterInfo
    {
        public function __construct($id='', $proxy=null, $replicaGroupId='')
        {
            $this->id = $id;
            $this->proxy = $proxy;
            $this->replicaGroupId = $replicaGroupId;
        }

        public function __toString()
        {
            global $IceGrid__t_AdapterInfo;
            return IcePHP_stringify($this, $IceGrid__t_AdapterInfo);
        }

        public $id;
        public $proxy;
        public $replicaGroupId;
    }

    $IceGrid__t_AdapterInfo = IcePHP_defineStruct('::IceGrid::AdapterInfo', 'IceGrid_AdapterInfo', array(
        array('id', $IcePHP__t_string), 
        array('proxy', $Ice__t_ObjectPrx), 
        array('replicaGroupId', $IcePHP__t_string)));
}

global $IceGrid__t_AdapterInfoSeq;

if(!isset($IceGrid__t_AdapterInfoSeq))
{
    $IceGrid__t_AdapterInfoSeq = IcePHP_defineSequence('::IceGrid::AdapterInfoSeq', $IceGrid__t_AdapterInfo);
}

global $IceGrid__t_ServerInfo;

if(!class_exists('IceGrid_ServerInfo',false))
{
    class IceGrid_ServerInfo
    {
        public function __construct($application='', $uuid='', $revision=0, $node='', $descriptor=null, $sessionId='')
        {
            $this->application = $application;
            $this->uuid = $uuid;
            $this->revision = $revision;
            $this->node = $node;
            $this->descriptor = $descriptor;
            $this->sessionId = $sessionId;
        }

        public function __toString()
        {
            global $IceGrid__t_ServerInfo;
            return IcePHP_stringify($this, $IceGrid__t_ServerInfo);
        }

        public $application;
        public $uuid;
        public $revision;
        public $node;
        public $descriptor;
        public $sessionId;
    }

    $IceGrid__t_ServerInfo = IcePHP_defineStruct('::IceGrid::ServerInfo', 'IceGrid_ServerInfo', array(
        array('application', $IcePHP__t_string), 
        array('uuid', $IcePHP__t_string), 
        array('revision', $IcePHP__t_int), 
        array('node', $IcePHP__t_string), 
        array('descriptor', $IceGrid__t_ServerDescriptor), 
        array('sessionId', $IcePHP__t_string)));
}

global $IceGrid__t_NodeInfo;

if(!class_exists('IceGrid_NodeInfo',false))
{
    class IceGrid_NodeInfo
    {
        public function __construct($name='', $os='', $hostname='', $release='', $version='', $machine='', $nProcessors=0, $dataDir='')
        {
            $this->name = $name;
            $this->os = $os;
            $this->hostname = $hostname;
            $this->release = $release;
            $this->version = $version;
            $this->machine = $machine;
            $this->nProcessors = $nProcessors;
            $this->dataDir = $dataDir;
        }

        public function __toString()
        {
            global $IceGrid__t_NodeInfo;
            return IcePHP_stringify($this, $IceGrid__t_NodeInfo);
        }

        public $name;
        public $os;
        public $hostname;
        public $release;
        public $version;
        public $machine;
        public $nProcessors;
        public $dataDir;
    }

    $IceGrid__t_NodeInfo = IcePHP_defineStruct('::IceGrid::NodeInfo', 'IceGrid_NodeInfo', array(
        array('name', $IcePHP__t_string), 
        array('os', $IcePHP__t_string), 
        array('hostname', $IcePHP__t_string), 
        array('release', $IcePHP__t_string), 
        array('version', $IcePHP__t_string), 
        array('machine', $IcePHP__t_string), 
        array('nProcessors', $IcePHP__t_int), 
        array('dataDir', $IcePHP__t_string)));
}

global $IceGrid__t_RegistryInfo;

if(!class_exists('IceGrid_RegistryInfo',false))
{
    class IceGrid_RegistryInfo
    {
        public function __construct($name='', $hostname='')
        {
            $this->name = $name;
            $this->hostname = $hostname;
        }

        public function __toString()
        {
            global $IceGrid__t_RegistryInfo;
            return IcePHP_stringify($this, $IceGrid__t_RegistryInfo);
        }

        public $name;
        public $hostname;
    }

    $IceGrid__t_RegistryInfo = IcePHP_defineStruct('::IceGrid::RegistryInfo', 'IceGrid_RegistryInfo', array(
        array('name', $IcePHP__t_string), 
        array('hostname', $IcePHP__t_string)));
}

global $IceGrid__t_RegistryInfoSeq;

if(!isset($IceGrid__t_RegistryInfoSeq))
{
    $IceGrid__t_RegistryInfoSeq = IcePHP_defineSequence('::IceGrid::RegistryInfoSeq', $IceGrid__t_RegistryInfo);
}

global $IceGrid__t_LoadInfo;

if(!class_exists('IceGrid_LoadInfo',false))
{
    class IceGrid_LoadInfo
    {
        public function __construct($avg1=0.0, $avg5=0.0, $avg15=0.0)
        {
            $this->avg1 = $avg1;
            $this->avg5 = $avg5;
            $this->avg15 = $avg15;
        }

        public function __toString()
        {
            global $IceGrid__t_LoadInfo;
            return IcePHP_stringify($this, $IceGrid__t_LoadInfo);
        }

        public $avg1;
        public $avg5;
        public $avg15;
    }

    $IceGrid__t_LoadInfo = IcePHP_defineStruct('::IceGrid::LoadInfo', 'IceGrid_LoadInfo', array(
        array('avg1', $IcePHP__t_float), 
        array('avg5', $IcePHP__t_float), 
        array('avg15', $IcePHP__t_float)));
}

global $IceGrid__t_ApplicationInfo;

if(!class_exists('IceGrid_ApplicationInfo',false))
{
    class IceGrid_ApplicationInfo
    {
        public function __construct($uuid='', $createTime=0, $createUser='', $updateTime=0, $updateUser='', $revision=0, $descriptor=null)
        {
            $this->uuid = $uuid;
            $this->createTime = $createTime;
            $this->createUser = $createUser;
            $this->updateTime = $updateTime;
            $this->updateUser = $updateUser;
            $this->revision = $revision;
            $this->descriptor = is_null($descriptor) ? new IceGrid_ApplicationDescriptor : $descriptor;
        }

        public function __toString()
        {
            global $IceGrid__t_ApplicationInfo;
            return IcePHP_stringify($this, $IceGrid__t_ApplicationInfo);
        }

        public $uuid;
        public $createTime;
        public $createUser;
        public $updateTime;
        public $updateUser;
        public $revision;
        public $descriptor;
    }

    $IceGrid__t_ApplicationInfo = IcePHP_defineStruct('::IceGrid::ApplicationInfo', 'IceGrid_ApplicationInfo', array(
        array('uuid', $IcePHP__t_string), 
        array('createTime', $IcePHP__t_long), 
        array('createUser', $IcePHP__t_string), 
        array('updateTime', $IcePHP__t_long), 
        array('updateUser', $IcePHP__t_string), 
        array('revision', $IcePHP__t_int), 
        array('descriptor', $IceGrid__t_ApplicationDescriptor)));
}

global $IceGrid__t_ApplicationInfoSeq;

if(!isset($IceGrid__t_ApplicationInfoSeq))
{
    $IceGrid__t_ApplicationInfoSeq = IcePHP_defineSequence('::IceGrid::ApplicationInfoSeq', $IceGrid__t_ApplicationInfo);
}

global $IceGrid__t_ApplicationUpdateInfo;

if(!class_exists('IceGrid_ApplicationUpdateInfo',false))
{
    class IceGrid_ApplicationUpdateInfo
    {
        public function __construct($updateTime=0, $updateUser='', $revision=0, $descriptor=null)
        {
            $this->updateTime = $updateTime;
            $this->updateUser = $updateUser;
            $this->revision = $revision;
            $this->descriptor = is_null($descriptor) ? new IceGrid_ApplicationUpdateDescriptor : $descriptor;
        }

        public function __toString()
        {
            global $IceGrid__t_ApplicationUpdateInfo;
            return IcePHP_stringify($this, $IceGrid__t_ApplicationUpdateInfo);
        }

        public $updateTime;
        public $updateUser;
        public $revision;
        public $descriptor;
    }

    $IceGrid__t_ApplicationUpdateInfo = IcePHP_defineStruct('::IceGrid::ApplicationUpdateInfo', 'IceGrid_ApplicationUpdateInfo', array(
        array('updateTime', $IcePHP__t_long), 
        array('updateUser', $IcePHP__t_string), 
        array('revision', $IcePHP__t_int), 
        array('descriptor', $IceGrid__t_ApplicationUpdateDescriptor)));
}

global $IceGrid__t_Admin;
global $IceGrid__t_AdminPrx;

if(!interface_exists('IceGrid_Admin',false))
{
    interface IceGrid_Admin extends Ice_Object
    {
        public function addApplication($descriptor);
        public function syncApplication($descriptor);
        public function updateApplication($descriptor);
        public function syncApplicationWithoutRestart($descriptor);
        public function updateApplicationWithoutRestart($descriptor);
        public function removeApplication($name);
        public function instantiateServer($application, $node, $desc);
        public function patchApplication($name, $shutdown);
        public function getApplicationInfo($name);
        public function getDefaultApplicationDescriptor();
        public function getAllApplicationNames();
        public function getServerInfo($id);
        public function getServerState($id);
        public function getServerPid($id);
        public function getServerAdminCategory();
        public function getServerAdmin($id);
        public function enableServer($id, $enabled);
        public function isServerEnabled($id);
        public function startServer($id);
        public function stopServer($id);
        public function patchServer($id, $shutdown);
        public function sendSignal($id, $signal);
        public function getAllServerIds();
        public function getAdapterInfo($id);
        public function removeAdapter($id);
        public function getAllAdapterIds();
        public function addObject($obj);
        public function updateObject($obj);
        public function addObjectWithType($obj, $type);
        public function removeObject($id);
        public function getObjectInfo($id);
        public function getObjectInfosByType($type);
        public function getAllObjectInfos($expr);
        public function pingNode($name);
        public function getNodeLoad($name);
        public function getNodeInfo($name);
        public function getNodeAdmin($name);
        public function getNodeProcessorSocketCount($name);
        public function shutdownNode($name);
        public function getNodeHostname($name);
        public function getAllNodeNames();
        public function pingRegistry($name);
        public function getRegistryInfo($name);
        public function getRegistryAdmin($name);
        public function shutdownRegistry($name);
        public function getAllRegistryNames();
        public function shutdown();
        public function getSliceChecksums();
    }

    class IceGrid_AdminPrxHelper
    {
        public static function checkedCast($proxy, $facetOrCtx=null, $ctx=null)
        {
            return $proxy->ice_checkedCast('::IceGrid::Admin', $facetOrCtx, $ctx);
        }

        public static function uncheckedCast($proxy, $facet=null)
        {
            return $proxy->ice_uncheckedCast('::IceGrid::Admin', $facet);
        }

        public static function ice_staticId()
        {
            return '::IceGrid::Admin';
        }
    }

    $IceGrid__t_Admin = IcePHP_defineClass('::IceGrid::Admin', 'IceGrid_Admin', -1, true, false, $Ice__t_Object, null, null);

    $IceGrid__t_AdminPrx = IcePHP_defineProxy($IceGrid__t_Admin);

    IcePHP_defineOperation($IceGrid__t_Admin, 'addApplication', 0, 0, 0, array(array($IceGrid__t_ApplicationDescriptor, false, 0)), null, null, array($IceGrid__t_AccessDeniedException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'syncApplication', 0, 0, 0, array(array($IceGrid__t_ApplicationDescriptor, false, 0)), null, null, array($IceGrid__t_AccessDeniedException, $IceGrid__t_DeploymentException, $IceGrid__t_ApplicationNotExistException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'updateApplication', 0, 0, 0, array(array($IceGrid__t_ApplicationUpdateDescriptor, false, 0)), null, null, array($IceGrid__t_AccessDeniedException, $IceGrid__t_DeploymentException, $IceGrid__t_ApplicationNotExistException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'syncApplicationWithoutRestart', 0, 0, 0, array(array($IceGrid__t_ApplicationDescriptor, false, 0)), null, null, array($IceGrid__t_AccessDeniedException, $IceGrid__t_DeploymentException, $IceGrid__t_ApplicationNotExistException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'updateApplicationWithoutRestart', 0, 0, 0, array(array($IceGrid__t_ApplicationUpdateDescriptor, false, 0)), null, null, array($IceGrid__t_AccessDeniedException, $IceGrid__t_DeploymentException, $IceGrid__t_ApplicationNotExistException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'removeApplication', 0, 0, 0, array(array($IcePHP__t_string, false, 0)), null, null, array($IceGrid__t_AccessDeniedException, $IceGrid__t_DeploymentException, $IceGrid__t_ApplicationNotExistException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'instantiateServer', 0, 0, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_string, false, 0), array($IceGrid__t_ServerInstanceDescriptor, false, 0)), null, null, array($IceGrid__t_AccessDeniedException, $IceGrid__t_ApplicationNotExistException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'patchApplication', 0, 0, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_bool, false, 0)), null, null, array($IceGrid__t_ApplicationNotExistException, $IceGrid__t_PatchException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getApplicationInfo', 2, 1, 0, array(array($IcePHP__t_string, false, 0)), null, array($IceGrid__t_ApplicationInfo, false, 0), array($IceGrid__t_ApplicationNotExistException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getDefaultApplicationDescriptor', 2, 1, 0, null, null, array($IceGrid__t_ApplicationDescriptor, false, 0), array($IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getAllApplicationNames', 2, 1, 0, null, null, array($Ice__t_StringSeq, false, 0), null);
    IcePHP_defineOperation($IceGrid__t_Admin, 'getServerInfo', 2, 1, 0, array(array($IcePHP__t_string, false, 0)), null, array($IceGrid__t_ServerInfo, false, 0), array($IceGrid__t_ServerNotExistException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getServerState', 2, 1, 0, array(array($IcePHP__t_string, false, 0)), null, array($IceGrid__t_ServerState, false, 0), array($IceGrid__t_ServerNotExistException, $IceGrid__t_NodeUnreachableException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getServerPid', 2, 1, 0, array(array($IcePHP__t_string, false, 0)), null, array($IcePHP__t_int, false, 0), array($IceGrid__t_ServerNotExistException, $IceGrid__t_NodeUnreachableException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getServerAdminCategory', 2, 2, 0, null, null, array($IcePHP__t_string, false, 0), null);
    IcePHP_defineOperation($IceGrid__t_Admin, 'getServerAdmin', 2, 2, 0, array(array($IcePHP__t_string, false, 0)), null, array($Ice__t_ObjectPrx, false, 0), array($IceGrid__t_ServerNotExistException, $IceGrid__t_NodeUnreachableException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'enableServer', 2, 2, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_bool, false, 0)), null, null, array($IceGrid__t_ServerNotExistException, $IceGrid__t_NodeUnreachableException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'isServerEnabled', 2, 1, 0, array(array($IcePHP__t_string, false, 0)), null, array($IcePHP__t_bool, false, 0), array($IceGrid__t_ServerNotExistException, $IceGrid__t_NodeUnreachableException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'startServer', 0, 0, 0, array(array($IcePHP__t_string, false, 0)), null, null, array($IceGrid__t_ServerNotExistException, $IceGrid__t_ServerStartException, $IceGrid__t_NodeUnreachableException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'stopServer', 0, 0, 0, array(array($IcePHP__t_string, false, 0)), null, null, array($IceGrid__t_ServerNotExistException, $IceGrid__t_ServerStopException, $IceGrid__t_NodeUnreachableException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'patchServer', 0, 0, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_bool, false, 0)), null, null, array($IceGrid__t_ServerNotExistException, $IceGrid__t_NodeUnreachableException, $IceGrid__t_DeploymentException, $IceGrid__t_PatchException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'sendSignal', 0, 0, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_string, false, 0)), null, null, array($IceGrid__t_ServerNotExistException, $IceGrid__t_NodeUnreachableException, $IceGrid__t_DeploymentException, $IceGrid__t_BadSignalException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getAllServerIds', 2, 1, 0, null, null, array($Ice__t_StringSeq, false, 0), null);
    IcePHP_defineOperation($IceGrid__t_Admin, 'getAdapterInfo', 2, 1, 0, array(array($IcePHP__t_string, false, 0)), null, array($IceGrid__t_AdapterInfoSeq, false, 0), array($IceGrid__t_AdapterNotExistException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'removeAdapter', 0, 0, 0, array(array($IcePHP__t_string, false, 0)), null, null, array($IceGrid__t_AdapterNotExistException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getAllAdapterIds', 2, 1, 0, null, null, array($Ice__t_StringSeq, false, 0), null);
    IcePHP_defineOperation($IceGrid__t_Admin, 'addObject', 0, 0, 0, array(array($Ice__t_ObjectPrx, false, 0)), null, null, array($IceGrid__t_ObjectExistsException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'updateObject', 0, 0, 0, array(array($Ice__t_ObjectPrx, false, 0)), null, null, array($IceGrid__t_ObjectNotRegisteredException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'addObjectWithType', 0, 0, 0, array(array($Ice__t_ObjectPrx, false, 0), array($IcePHP__t_string, false, 0)), null, null, array($IceGrid__t_ObjectExistsException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'removeObject', 0, 0, 0, array(array($Ice__t_Identity, false, 0)), null, null, array($IceGrid__t_ObjectNotRegisteredException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getObjectInfo', 2, 1, 0, array(array($Ice__t_Identity, false, 0)), null, array($IceGrid__t_ObjectInfo, false, 0), array($IceGrid__t_ObjectNotRegisteredException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getObjectInfosByType', 2, 1, 0, array(array($IcePHP__t_string, false, 0)), null, array($IceGrid__t_ObjectInfoSeq, false, 0), null);
    IcePHP_defineOperation($IceGrid__t_Admin, 'getAllObjectInfos', 2, 1, 0, array(array($IcePHP__t_string, false, 0)), null, array($IceGrid__t_ObjectInfoSeq, false, 0), null);
    IcePHP_defineOperation($IceGrid__t_Admin, 'pingNode', 2, 1, 0, array(array($IcePHP__t_string, false, 0)), null, array($IcePHP__t_bool, false, 0), array($IceGrid__t_NodeNotExistException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getNodeLoad', 2, 1, 0, array(array($IcePHP__t_string, false, 0)), null, array($IceGrid__t_LoadInfo, false, 0), array($IceGrid__t_NodeNotExistException, $IceGrid__t_NodeUnreachableException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getNodeInfo', 2, 1, 0, array(array($IcePHP__t_string, false, 0)), null, array($IceGrid__t_NodeInfo, false, 0), array($IceGrid__t_NodeNotExistException, $IceGrid__t_NodeUnreachableException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getNodeAdmin', 2, 2, 0, array(array($IcePHP__t_string, false, 0)), null, array($Ice__t_ObjectPrx, false, 0), array($IceGrid__t_NodeNotExistException, $IceGrid__t_NodeUnreachableException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getNodeProcessorSocketCount', 2, 1, 0, array(array($IcePHP__t_string, false, 0)), null, array($IcePHP__t_int, false, 0), array($IceGrid__t_NodeNotExistException, $IceGrid__t_NodeUnreachableException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'shutdownNode', 0, 0, 0, array(array($IcePHP__t_string, false, 0)), null, null, array($IceGrid__t_NodeNotExistException, $IceGrid__t_NodeUnreachableException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getNodeHostname', 2, 1, 0, array(array($IcePHP__t_string, false, 0)), null, array($IcePHP__t_string, false, 0), array($IceGrid__t_NodeNotExistException, $IceGrid__t_NodeUnreachableException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getAllNodeNames', 2, 1, 0, null, null, array($Ice__t_StringSeq, false, 0), null);
    IcePHP_defineOperation($IceGrid__t_Admin, 'pingRegistry', 2, 2, 0, array(array($IcePHP__t_string, false, 0)), null, array($IcePHP__t_bool, false, 0), array($IceGrid__t_RegistryNotExistException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getRegistryInfo', 2, 2, 0, array(array($IcePHP__t_string, false, 0)), null, array($IceGrid__t_RegistryInfo, false, 0), array($IceGrid__t_RegistryNotExistException, $IceGrid__t_RegistryUnreachableException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getRegistryAdmin', 2, 2, 0, array(array($IcePHP__t_string, false, 0)), null, array($Ice__t_ObjectPrx, false, 0), array($IceGrid__t_RegistryNotExistException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'shutdownRegistry', 2, 2, 0, array(array($IcePHP__t_string, false, 0)), null, null, array($IceGrid__t_RegistryNotExistException, $IceGrid__t_RegistryUnreachableException));
    IcePHP_defineOperation($IceGrid__t_Admin, 'getAllRegistryNames', 2, 2, 0, null, null, array($Ice__t_StringSeq, false, 0), null);
    IcePHP_defineOperation($IceGrid__t_Admin, 'shutdown', 0, 0, 0, null, null, null, null);
    IcePHP_defineOperation($IceGrid__t_Admin, 'getSliceChecksums', 2, 1, 0, null, null, array($Ice__t_SliceChecksumDict, false, 0), null);
}

global $IceGrid__t_FileIterator;
global $IceGrid__t_FileIteratorPrx;

if(!interface_exists('IceGrid_FileIterator',false))
{
    interface IceGrid_FileIterator extends Ice_Object
    {
        public function read($size, $lines);
        public function destroy();
    }

    class IceGrid_FileIteratorPrxHelper
    {
        public static function checkedCast($proxy, $facetOrCtx=null, $ctx=null)
        {
            return $proxy->ice_checkedCast('::IceGrid::FileIterator', $facetOrCtx, $ctx);
        }

        public static function uncheckedCast($proxy, $facet=null)
        {
            return $proxy->ice_uncheckedCast('::IceGrid::FileIterator', $facet);
        }

        public static function ice_staticId()
        {
            return '::IceGrid::FileIterator';
        }
    }

    $IceGrid__t_FileIterator = IcePHP_defineClass('::IceGrid::FileIterator', 'IceGrid_FileIterator', -1, true, false, $Ice__t_Object, null, null);

    $IceGrid__t_FileIteratorPrx = IcePHP_defineProxy($IceGrid__t_FileIterator);

    IcePHP_defineOperation($IceGrid__t_FileIterator, 'read', 0, 0, 0, array(array($IcePHP__t_int, false, 0)), array(array($Ice__t_StringSeq, false, 0)), array($IcePHP__t_bool, false, 0), array($IceGrid__t_FileNotAvailableException));
    IcePHP_defineOperation($IceGrid__t_FileIterator, 'destroy', 0, 0, 0, null, null, null, null);
}

global $IceGrid__t_RegistryObserver;
global $IceGrid__t_RegistryObserverPrx;
if(!isset($IceGrid__t_RegistryObserver))
{
    $IceGrid__t_RegistryObserver = IcePHP_declareClass('::IceGrid::RegistryObserver');
    $IceGrid__t_RegistryObserverPrx = IcePHP_declareProxy('::IceGrid::RegistryObserver');
}

global $IceGrid__t_NodeObserver;
global $IceGrid__t_NodeObserverPrx;
if(!isset($IceGrid__t_NodeObserver))
{
    $IceGrid__t_NodeObserver = IcePHP_declareClass('::IceGrid::NodeObserver');
    $IceGrid__t_NodeObserverPrx = IcePHP_declareProxy('::IceGrid::NodeObserver');
}

global $IceGrid__t_ApplicationObserver;
global $IceGrid__t_ApplicationObserverPrx;
if(!isset($IceGrid__t_ApplicationObserver))
{
    $IceGrid__t_ApplicationObserver = IcePHP_declareClass('::IceGrid::ApplicationObserver');
    $IceGrid__t_ApplicationObserverPrx = IcePHP_declareProxy('::IceGrid::ApplicationObserver');
}

global $IceGrid__t_AdapterObserver;
global $IceGrid__t_AdapterObserverPrx;
if(!isset($IceGrid__t_AdapterObserver))
{
    $IceGrid__t_AdapterObserver = IcePHP_declareClass('::IceGrid::AdapterObserver');
    $IceGrid__t_AdapterObserverPrx = IcePHP_declareProxy('::IceGrid::AdapterObserver');
}

global $IceGrid__t_ObjectObserver;
global $IceGrid__t_ObjectObserverPrx;
if(!isset($IceGrid__t_ObjectObserver))
{
    $IceGrid__t_ObjectObserver = IcePHP_declareClass('::IceGrid::ObjectObserver');
    $IceGrid__t_ObjectObserverPrx = IcePHP_declareProxy('::IceGrid::ObjectObserver');
}

global $IceGrid__t_AdminSession;
global $IceGrid__t_AdminSessionPrx;

if(!interface_exists('IceGrid_AdminSession',false))
{
    interface IceGrid_AdminSession extends Glacier2_Session
    {
        public function keepAlive();
        public function getAdmin();
        public function getAdminCallbackTemplate();
        public function setObservers($registryObs, $nodeObs, $appObs, $adptObs, $objObs);
        public function setObserversByIdentity($registryObs, $nodeObs, $appObs, $adptObs, $objObs);
        public function startUpdate();
        public function finishUpdate();
        public function getReplicaName();
        public function openServerLog($id, $path, $count);
        public function openServerStdErr($id, $count);
        public function openServerStdOut($id, $count);
        public function openNodeStdErr($name, $count);
        public function openNodeStdOut($name, $count);
        public function openRegistryStdErr($name, $count);
        public function openRegistryStdOut($name, $count);
    }

    class IceGrid_AdminSessionPrxHelper
    {
        public static function checkedCast($proxy, $facetOrCtx=null, $ctx=null)
        {
            return $proxy->ice_checkedCast('::IceGrid::AdminSession', $facetOrCtx, $ctx);
        }

        public static function uncheckedCast($proxy, $facet=null)
        {
            return $proxy->ice_uncheckedCast('::IceGrid::AdminSession', $facet);
        }

        public static function ice_staticId()
        {
            return '::IceGrid::AdminSession';
        }
    }

    $IceGrid__t_AdminSession = IcePHP_defineClass('::IceGrid::AdminSession', 'IceGrid_AdminSession', -1, true, false, $Ice__t_Object, array($Glacier2__t_Session), null);

    $IceGrid__t_AdminSessionPrx = IcePHP_defineProxy($IceGrid__t_AdminSession);

    IcePHP_defineOperation($IceGrid__t_AdminSession, 'keepAlive', 2, 2, 0, null, null, null, null);
    IcePHP_defineOperation($IceGrid__t_AdminSession, 'getAdmin', 2, 1, 0, null, null, array($IceGrid__t_AdminPrx, false, 0), null);
    IcePHP_defineOperation($IceGrid__t_AdminSession, 'getAdminCallbackTemplate', 2, 2, 0, null, null, array($Ice__t_ObjectPrx, false, 0), null);
    IcePHP_defineOperation($IceGrid__t_AdminSession, 'setObservers', 2, 2, 0, array(array($IceGrid__t_RegistryObserverPrx, false, 0), array($IceGrid__t_NodeObserverPrx, false, 0), array($IceGrid__t_ApplicationObserverPrx, false, 0), array($IceGrid__t_AdapterObserverPrx, false, 0), array($IceGrid__t_ObjectObserverPrx, false, 0)), null, null, array($IceGrid__t_ObserverAlreadyRegisteredException));
    IcePHP_defineOperation($IceGrid__t_AdminSession, 'setObserversByIdentity', 2, 2, 0, array(array($Ice__t_Identity, false, 0), array($Ice__t_Identity, false, 0), array($Ice__t_Identity, false, 0), array($Ice__t_Identity, false, 0), array($Ice__t_Identity, false, 0)), null, null, array($IceGrid__t_ObserverAlreadyRegisteredException));
    IcePHP_defineOperation($IceGrid__t_AdminSession, 'startUpdate', 0, 0, 0, null, null, array($IcePHP__t_int, false, 0), array($IceGrid__t_AccessDeniedException));
    IcePHP_defineOperation($IceGrid__t_AdminSession, 'finishUpdate', 0, 0, 0, null, null, null, array($IceGrid__t_AccessDeniedException));
    IcePHP_defineOperation($IceGrid__t_AdminSession, 'getReplicaName', 2, 2, 0, null, null, array($IcePHP__t_string, false, 0), null);
    IcePHP_defineOperation($IceGrid__t_AdminSession, 'openServerLog', 0, 0, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_string, false, 0), array($IcePHP__t_int, false, 0)), null, array($IceGrid__t_FileIteratorPrx, false, 0), array($IceGrid__t_FileNotAvailableException, $IceGrid__t_ServerNotExistException, $IceGrid__t_NodeUnreachableException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_AdminSession, 'openServerStdErr', 0, 0, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_int, false, 0)), null, array($IceGrid__t_FileIteratorPrx, false, 0), array($IceGrid__t_FileNotAvailableException, $IceGrid__t_ServerNotExistException, $IceGrid__t_NodeUnreachableException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_AdminSession, 'openServerStdOut', 0, 0, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_int, false, 0)), null, array($IceGrid__t_FileIteratorPrx, false, 0), array($IceGrid__t_FileNotAvailableException, $IceGrid__t_ServerNotExistException, $IceGrid__t_NodeUnreachableException, $IceGrid__t_DeploymentException));
    IcePHP_defineOperation($IceGrid__t_AdminSession, 'openNodeStdErr', 0, 0, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_int, false, 0)), null, array($IceGrid__t_FileIteratorPrx, false, 0), array($IceGrid__t_FileNotAvailableException, $IceGrid__t_NodeNotExistException, $IceGrid__t_NodeUnreachableException));
    IcePHP_defineOperation($IceGrid__t_AdminSession, 'openNodeStdOut', 0, 0, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_int, false, 0)), null, array($IceGrid__t_FileIteratorPrx, false, 0), array($IceGrid__t_FileNotAvailableException, $IceGrid__t_NodeNotExistException, $IceGrid__t_NodeUnreachableException));
    IcePHP_defineOperation($IceGrid__t_AdminSession, 'openRegistryStdErr', 0, 0, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_int, false, 0)), null, array($IceGrid__t_FileIteratorPrx, false, 0), array($IceGrid__t_FileNotAvailableException, $IceGrid__t_RegistryNotExistException, $IceGrid__t_RegistryUnreachableException));
    IcePHP_defineOperation($IceGrid__t_AdminSession, 'openRegistryStdOut', 0, 0, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_int, false, 0)), null, array($IceGrid__t_FileIteratorPrx, false, 0), array($IceGrid__t_FileNotAvailableException, $IceGrid__t_RegistryNotExistException, $IceGrid__t_RegistryUnreachableException));
}
?>
