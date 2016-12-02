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
// Generated from file `Identity.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//


global $Ice__t_Identity;

if(!class_exists('Ice_Identity',false))
{
    class Ice_Identity
    {
        public function __construct($name='', $category='')
        {
            $this->name = $name;
            $this->category = $category;
        }

        public function __toString()
        {
            global $Ice__t_Identity;
            return IcePHP_stringify($this, $Ice__t_Identity);
        }

        public $name;
        public $category;
    }

    $Ice__t_Identity = IcePHP_defineStruct('::Ice::Identity', 'Ice_Identity', array(
        array('name', $IcePHP__t_string), 
        array('category', $IcePHP__t_string)));
}

global $Ice__t_ObjectDict;

if(!isset($Ice__t_ObjectDict))
{
    $Ice__t_ObjectDict = IcePHP_defineDictionary('::Ice::ObjectDict', $Ice__t_Identity, $Ice__t_Object);
}

global $Ice__t_IdentitySeq;

if(!isset($Ice__t_IdentitySeq))
{
    $Ice__t_IdentitySeq = IcePHP_defineSequence('::Ice::IdentitySeq', $Ice__t_Identity);
}
?>
