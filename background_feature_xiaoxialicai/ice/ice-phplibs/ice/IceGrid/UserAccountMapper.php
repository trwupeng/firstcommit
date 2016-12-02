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
// Generated from file `UserAccountMapper.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//


global $IceGrid__t_UserAccountNotFoundException;

if(!class_exists('IceGrid_UserAccountNotFoundException',false))
{
    class IceGrid_UserAccountNotFoundException extends Ice_UserException
    {
        public function __construct()
        {
        }

        public function ice_name()
        {
            return 'IceGrid::UserAccountNotFoundException';
        }

        public function __toString()
        {
            global $IceGrid__t_UserAccountNotFoundException;
            return IcePHP_stringifyException($this, $IceGrid__t_UserAccountNotFoundException);
        }
    }

    $IceGrid__t_UserAccountNotFoundException = IcePHP_defineException('::IceGrid::UserAccountNotFoundException', 'IceGrid_UserAccountNotFoundException', false, null, null);
}

global $IceGrid__t_UserAccountMapper;
global $IceGrid__t_UserAccountMapperPrx;

if(!interface_exists('IceGrid_UserAccountMapper',false))
{
    interface IceGrid_UserAccountMapper extends Ice_Object
    {
        public function getUserAccount($user);
    }

    class IceGrid_UserAccountMapperPrxHelper
    {
        public static function checkedCast($proxy, $facetOrCtx=null, $ctx=null)
        {
            return $proxy->ice_checkedCast('::IceGrid::UserAccountMapper', $facetOrCtx, $ctx);
        }

        public static function uncheckedCast($proxy, $facet=null)
        {
            return $proxy->ice_uncheckedCast('::IceGrid::UserAccountMapper', $facet);
        }

        public static function ice_staticId()
        {
            return '::IceGrid::UserAccountMapper';
        }
    }

    $IceGrid__t_UserAccountMapper = IcePHP_defineClass('::IceGrid::UserAccountMapper', 'IceGrid_UserAccountMapper', -1, true, false, $Ice__t_Object, null, null);

    $IceGrid__t_UserAccountMapperPrx = IcePHP_defineProxy($IceGrid__t_UserAccountMapper);

    IcePHP_defineOperation($IceGrid__t_UserAccountMapper, 'getUserAccount', 0, 0, 0, array(array($IcePHP__t_string, false, 0)), null, array($IcePHP__t_string, false, 0), array($IceGrid__t_UserAccountNotFoundException));
}
?>
