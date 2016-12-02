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
// Generated from file `IceStorm.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//

require_once __DIR__.'/../Ice/Identity.php';
require_once __DIR__.'/../Ice/SliceChecksumDict.php';
require_once __DIR__.'/../IceStorm/Metrics.php';

global $IceStorm__t_Topic;
global $IceStorm__t_TopicPrx;
if(!isset($IceStorm__t_Topic))
{
    $IceStorm__t_Topic = IcePHP_declareClass('::IceStorm::Topic');
    $IceStorm__t_TopicPrx = IcePHP_declareProxy('::IceStorm::Topic');
}

global $IceStorm__t_LinkInfo;

if(!class_exists('IceStorm_LinkInfo',false))
{
    class IceStorm_LinkInfo
    {
        public function __construct($theTopic=null, $name='', $cost=0)
        {
            $this->theTopic = $theTopic;
            $this->name = $name;
            $this->cost = $cost;
        }

        public function __toString()
        {
            global $IceStorm__t_LinkInfo;
            return IcePHP_stringify($this, $IceStorm__t_LinkInfo);
        }

        public $theTopic;
        public $name;
        public $cost;
    }

    $IceStorm__t_LinkInfo = IcePHP_defineStruct('::IceStorm::LinkInfo', 'IceStorm_LinkInfo', array(
        array('theTopic', $IceStorm__t_TopicPrx), 
        array('name', $IcePHP__t_string), 
        array('cost', $IcePHP__t_int)));
}

global $IceStorm__t_LinkInfoSeq;

if(!isset($IceStorm__t_LinkInfoSeq))
{
    $IceStorm__t_LinkInfoSeq = IcePHP_defineSequence('::IceStorm::LinkInfoSeq', $IceStorm__t_LinkInfo);
}

global $IceStorm__t_QoS;

if(!isset($IceStorm__t_QoS))
{
    $IceStorm__t_QoS = IcePHP_defineDictionary('::IceStorm::QoS', $IcePHP__t_string, $IcePHP__t_string);
}

global $IceStorm__t_LinkExists;

if(!class_exists('IceStorm_LinkExists',false))
{
    class IceStorm_LinkExists extends Ice_UserException
    {
        public function __construct($name='')
        {
            $this->name = $name;
        }

        public function ice_name()
        {
            return 'IceStorm::LinkExists';
        }

        public function __toString()
        {
            global $IceStorm__t_LinkExists;
            return IcePHP_stringifyException($this, $IceStorm__t_LinkExists);
        }

        public $name;
    }

    $IceStorm__t_LinkExists = IcePHP_defineException('::IceStorm::LinkExists', 'IceStorm_LinkExists', false, null, array(
        array('name', $IcePHP__t_string, false, 0)));
}

global $IceStorm__t_NoSuchLink;

if(!class_exists('IceStorm_NoSuchLink',false))
{
    class IceStorm_NoSuchLink extends Ice_UserException
    {
        public function __construct($name='')
        {
            $this->name = $name;
        }

        public function ice_name()
        {
            return 'IceStorm::NoSuchLink';
        }

        public function __toString()
        {
            global $IceStorm__t_NoSuchLink;
            return IcePHP_stringifyException($this, $IceStorm__t_NoSuchLink);
        }

        public $name;
    }

    $IceStorm__t_NoSuchLink = IcePHP_defineException('::IceStorm::NoSuchLink', 'IceStorm_NoSuchLink', false, null, array(
        array('name', $IcePHP__t_string, false, 0)));
}

global $IceStorm__t_AlreadySubscribed;

if(!class_exists('IceStorm_AlreadySubscribed',false))
{
    class IceStorm_AlreadySubscribed extends Ice_UserException
    {
        public function __construct()
        {
        }

        public function ice_name()
        {
            return 'IceStorm::AlreadySubscribed';
        }

        public function __toString()
        {
            global $IceStorm__t_AlreadySubscribed;
            return IcePHP_stringifyException($this, $IceStorm__t_AlreadySubscribed);
        }
    }

    $IceStorm__t_AlreadySubscribed = IcePHP_defineException('::IceStorm::AlreadySubscribed', 'IceStorm_AlreadySubscribed', false, null, null);
}

global $IceStorm__t_InvalidSubscriber;

if(!class_exists('IceStorm_InvalidSubscriber',false))
{
    class IceStorm_InvalidSubscriber extends Ice_UserException
    {
        public function __construct($reason='')
        {
            $this->reason = $reason;
        }

        public function ice_name()
        {
            return 'IceStorm::InvalidSubscriber';
        }

        public function __toString()
        {
            global $IceStorm__t_InvalidSubscriber;
            return IcePHP_stringifyException($this, $IceStorm__t_InvalidSubscriber);
        }

        public $reason;
    }

    $IceStorm__t_InvalidSubscriber = IcePHP_defineException('::IceStorm::InvalidSubscriber', 'IceStorm_InvalidSubscriber', false, null, array(
        array('reason', $IcePHP__t_string, false, 0)));
}

global $IceStorm__t_BadQoS;

if(!class_exists('IceStorm_BadQoS',false))
{
    class IceStorm_BadQoS extends Ice_UserException
    {
        public function __construct($reason='')
        {
            $this->reason = $reason;
        }

        public function ice_name()
        {
            return 'IceStorm::BadQoS';
        }

        public function __toString()
        {
            global $IceStorm__t_BadQoS;
            return IcePHP_stringifyException($this, $IceStorm__t_BadQoS);
        }

        public $reason;
    }

    $IceStorm__t_BadQoS = IcePHP_defineException('::IceStorm::BadQoS', 'IceStorm_BadQoS', false, null, array(
        array('reason', $IcePHP__t_string, false, 0)));
}

global $IceStorm__t_Topic;
global $IceStorm__t_TopicPrx;

if(!interface_exists('IceStorm_Topic',false))
{
    interface IceStorm_Topic extends Ice_Object
    {
        public function getName();
        public function getPublisher();
        public function getNonReplicatedPublisher();
        public function subscribeAndGetPublisher($theQoS, $subscriber);
        public function unsubscribe($subscriber);
        public function link($linkTo, $cost);
        public function unlink($linkTo);
        public function getLinkInfoSeq();
        public function getSubscribers();
        public function destroy();
    }

    class IceStorm_TopicPrxHelper
    {
        public static function checkedCast($proxy, $facetOrCtx=null, $ctx=null)
        {
            return $proxy->ice_checkedCast('::IceStorm::Topic', $facetOrCtx, $ctx);
        }

        public static function uncheckedCast($proxy, $facet=null)
        {
            return $proxy->ice_uncheckedCast('::IceStorm::Topic', $facet);
        }

        public static function ice_staticId()
        {
            return '::IceStorm::Topic';
        }
    }

    $IceStorm__t_Topic = IcePHP_defineClass('::IceStorm::Topic', 'IceStorm_Topic', -1, true, false, $Ice__t_Object, null, null);

    $IceStorm__t_TopicPrx = IcePHP_defineProxy($IceStorm__t_Topic);

    IcePHP_defineOperation($IceStorm__t_Topic, 'getName', 2, 1, 0, null, null, array($IcePHP__t_string, false, 0), null);
    IcePHP_defineOperation($IceStorm__t_Topic, 'getPublisher', 2, 1, 0, null, null, array($Ice__t_ObjectPrx, false, 0), null);
    IcePHP_defineOperation($IceStorm__t_Topic, 'getNonReplicatedPublisher', 2, 1, 0, null, null, array($Ice__t_ObjectPrx, false, 0), null);
    IcePHP_defineOperation($IceStorm__t_Topic, 'subscribeAndGetPublisher', 0, 0, 0, array(array($IceStorm__t_QoS, false, 0), array($Ice__t_ObjectPrx, false, 0)), null, array($Ice__t_ObjectPrx, false, 0), array($IceStorm__t_AlreadySubscribed, $IceStorm__t_InvalidSubscriber, $IceStorm__t_BadQoS));
    IcePHP_defineOperation($IceStorm__t_Topic, 'unsubscribe', 2, 2, 0, array(array($Ice__t_ObjectPrx, false, 0)), null, null, null);
    IcePHP_defineOperation($IceStorm__t_Topic, 'link', 0, 0, 0, array(array($IceStorm__t_TopicPrx, false, 0), array($IcePHP__t_int, false, 0)), null, null, array($IceStorm__t_LinkExists));
    IcePHP_defineOperation($IceStorm__t_Topic, 'unlink', 0, 0, 0, array(array($IceStorm__t_TopicPrx, false, 0)), null, null, array($IceStorm__t_NoSuchLink));
    IcePHP_defineOperation($IceStorm__t_Topic, 'getLinkInfoSeq', 2, 1, 0, null, null, array($IceStorm__t_LinkInfoSeq, false, 0), null);
    IcePHP_defineOperation($IceStorm__t_Topic, 'getSubscribers', 0, 0, 0, null, null, array($Ice__t_IdentitySeq, false, 0), null);
    IcePHP_defineOperation($IceStorm__t_Topic, 'destroy', 0, 0, 0, null, null, null, null);
}

global $IceStorm__t_TopicDict;

if(!isset($IceStorm__t_TopicDict))
{
    $IceStorm__t_TopicDict = IcePHP_defineDictionary('::IceStorm::TopicDict', $IcePHP__t_string, $IceStorm__t_TopicPrx);
}

global $IceStorm__t_TopicExists;

if(!class_exists('IceStorm_TopicExists',false))
{
    class IceStorm_TopicExists extends Ice_UserException
    {
        public function __construct($name='')
        {
            $this->name = $name;
        }

        public function ice_name()
        {
            return 'IceStorm::TopicExists';
        }

        public function __toString()
        {
            global $IceStorm__t_TopicExists;
            return IcePHP_stringifyException($this, $IceStorm__t_TopicExists);
        }

        public $name;
    }

    $IceStorm__t_TopicExists = IcePHP_defineException('::IceStorm::TopicExists', 'IceStorm_TopicExists', false, null, array(
        array('name', $IcePHP__t_string, false, 0)));
}

global $IceStorm__t_NoSuchTopic;

if(!class_exists('IceStorm_NoSuchTopic',false))
{
    class IceStorm_NoSuchTopic extends Ice_UserException
    {
        public function __construct($name='')
        {
            $this->name = $name;
        }

        public function ice_name()
        {
            return 'IceStorm::NoSuchTopic';
        }

        public function __toString()
        {
            global $IceStorm__t_NoSuchTopic;
            return IcePHP_stringifyException($this, $IceStorm__t_NoSuchTopic);
        }

        public $name;
    }

    $IceStorm__t_NoSuchTopic = IcePHP_defineException('::IceStorm::NoSuchTopic', 'IceStorm_NoSuchTopic', false, null, array(
        array('name', $IcePHP__t_string, false, 0)));
}

global $IceStorm__t_TopicManager;
global $IceStorm__t_TopicManagerPrx;

if(!interface_exists('IceStorm_TopicManager',false))
{
    interface IceStorm_TopicManager extends Ice_Object
    {
        public function create($name);
        public function retrieve($name);
        public function retrieveAll();
        public function getSliceChecksums();
    }

    class IceStorm_TopicManagerPrxHelper
    {
        public static function checkedCast($proxy, $facetOrCtx=null, $ctx=null)
        {
            return $proxy->ice_checkedCast('::IceStorm::TopicManager', $facetOrCtx, $ctx);
        }

        public static function uncheckedCast($proxy, $facet=null)
        {
            return $proxy->ice_uncheckedCast('::IceStorm::TopicManager', $facet);
        }

        public static function ice_staticId()
        {
            return '::IceStorm::TopicManager';
        }
    }

    $IceStorm__t_TopicManager = IcePHP_defineClass('::IceStorm::TopicManager', 'IceStorm_TopicManager', -1, true, false, $Ice__t_Object, null, null);

    $IceStorm__t_TopicManagerPrx = IcePHP_defineProxy($IceStorm__t_TopicManager);

    IcePHP_defineOperation($IceStorm__t_TopicManager, 'create', 0, 0, 0, array(array($IcePHP__t_string, false, 0)), null, array($IceStorm__t_TopicPrx, false, 0), array($IceStorm__t_TopicExists));
    IcePHP_defineOperation($IceStorm__t_TopicManager, 'retrieve', 2, 1, 0, array(array($IcePHP__t_string, false, 0)), null, array($IceStorm__t_TopicPrx, false, 0), array($IceStorm__t_NoSuchTopic));
    IcePHP_defineOperation($IceStorm__t_TopicManager, 'retrieveAll', 2, 1, 0, null, null, array($IceStorm__t_TopicDict, false, 0), null);
    IcePHP_defineOperation($IceStorm__t_TopicManager, 'getSliceChecksums', 2, 1, 0, null, null, array($Ice__t_SliceChecksumDict, false, 0), null);
}

global $IceStorm__t_Finder;
global $IceStorm__t_FinderPrx;

if(!interface_exists('IceStorm_Finder',false))
{
    interface IceStorm_Finder extends Ice_Object
    {
        public function getTopicManager();
    }

    class IceStorm_FinderPrxHelper
    {
        public static function checkedCast($proxy, $facetOrCtx=null, $ctx=null)
        {
            return $proxy->ice_checkedCast('::IceStorm::Finder', $facetOrCtx, $ctx);
        }

        public static function uncheckedCast($proxy, $facet=null)
        {
            return $proxy->ice_uncheckedCast('::IceStorm::Finder', $facet);
        }

        public static function ice_staticId()
        {
            return '::IceStorm::Finder';
        }
    }

    $IceStorm__t_Finder = IcePHP_defineClass('::IceStorm::Finder', 'IceStorm_Finder', -1, true, false, $Ice__t_Object, null, null);

    $IceStorm__t_FinderPrx = IcePHP_defineProxy($IceStorm__t_Finder);

    IcePHP_defineOperation($IceStorm__t_Finder, 'getTopicManager', 0, 0, 0, null, null, array($IceStorm__t_TopicManagerPrx, false, 0), null);
}
?>
