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
// Generated from file `msgcenter.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//

package sooh.services.msgcenter;

public interface bypushPrx extends Ice.ObjectPrx
{
    /**
     * 全平台发送推送消息，receivers是逗号分隔的用户id列表
     **/
    public void sendMsg(String receivers, String msg);

    /**
     * 全平台发送推送消息，receivers是逗号分隔的用户id列表
     * @param __ctx The Context map to send with the invocation.
     **/
    public void sendMsg(String receivers, String msg, java.util.Map<String, String> __ctx);

    /**
     * 全平台发送推送消息，receivers是逗号分隔的用户id列表
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendMsg(String receivers, String msg);

    /**
     * 全平台发送推送消息，receivers是逗号分隔的用户id列表
     * @param __ctx The Context map to send with the invocation.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendMsg(String receivers, String msg, java.util.Map<String, String> __ctx);

    /**
     * 全平台发送推送消息，receivers是逗号分隔的用户id列表
     * @param __cb The asynchronous callback object.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendMsg(String receivers, String msg, Ice.Callback __cb);

    /**
     * 全平台发送推送消息，receivers是逗号分隔的用户id列表
     * @param __ctx The Context map to send with the invocation.
     * @param __cb The asynchronous callback object.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendMsg(String receivers, String msg, java.util.Map<String, String> __ctx, Ice.Callback __cb);

    /**
     * 全平台发送推送消息，receivers是逗号分隔的用户id列表
     * @param __cb The asynchronous callback object.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendMsg(String receivers, String msg, Callback_bypush_sendMsg __cb);

    /**
     * 全平台发送推送消息，receivers是逗号分隔的用户id列表
     * @param __ctx The Context map to send with the invocation.
     * @param __cb The asynchronous callback object.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendMsg(String receivers, String msg, java.util.Map<String, String> __ctx, Callback_bypush_sendMsg __cb);

    /**
     * 全平台发送推送消息，receivers是逗号分隔的用户id列表
     * @param __responseCb The lambda response callback.
     * @param __exceptionCb The lambda exception callback.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendMsg(String receivers, 
                                         String msg, 
                                         IceInternal.Functional_VoidCallback __responseCb, 
                                         IceInternal.Functional_GenericCallback1<Ice.Exception> __exceptionCb);

    /**
     * 全平台发送推送消息，receivers是逗号分隔的用户id列表
     * @param __responseCb The lambda response callback.
     * @param __exceptionCb The lambda exception callback.
     * @param __sentCb The lambda sent callback.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendMsg(String receivers, 
                                         String msg, 
                                         IceInternal.Functional_VoidCallback __responseCb, 
                                         IceInternal.Functional_GenericCallback1<Ice.Exception> __exceptionCb, 
                                         IceInternal.Functional_BoolCallback __sentCb);

    /**
     * 全平台发送推送消息，receivers是逗号分隔的用户id列表
     * @param __ctx The Context map to send with the invocation.
     * @param __responseCb The lambda response callback.
     * @param __exceptionCb The lambda exception callback.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendMsg(String receivers, 
                                         String msg, 
                                         java.util.Map<String, String> __ctx, 
                                         IceInternal.Functional_VoidCallback __responseCb, 
                                         IceInternal.Functional_GenericCallback1<Ice.Exception> __exceptionCb);

    /**
     * 全平台发送推送消息，receivers是逗号分隔的用户id列表
     * @param __ctx The Context map to send with the invocation.
     * @param __responseCb The lambda response callback.
     * @param __exceptionCb The lambda exception callback.
     * @param __sentCb The lambda sent callback.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendMsg(String receivers, 
                                         String msg, 
                                         java.util.Map<String, String> __ctx, 
                                         IceInternal.Functional_VoidCallback __responseCb, 
                                         IceInternal.Functional_GenericCallback1<Ice.Exception> __exceptionCb, 
                                         IceInternal.Functional_BoolCallback __sentCb);

    /**
     * 全平台发送推送消息，receivers是逗号分隔的用户id列表
     * @param __result The asynchronous result object.
     **/
    public void end_sendMsg(Ice.AsyncResult __result);

    /**
     * 全平台发送推送命令，receivers是逗号分隔的用户id列表
     **/
    public void sendCmd(String receivers, String cmd);

    /**
     * 全平台发送推送命令，receivers是逗号分隔的用户id列表
     * @param __ctx The Context map to send with the invocation.
     **/
    public void sendCmd(String receivers, String cmd, java.util.Map<String, String> __ctx);

    /**
     * 全平台发送推送命令，receivers是逗号分隔的用户id列表
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendCmd(String receivers, String cmd);

    /**
     * 全平台发送推送命令，receivers是逗号分隔的用户id列表
     * @param __ctx The Context map to send with the invocation.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendCmd(String receivers, String cmd, java.util.Map<String, String> __ctx);

    /**
     * 全平台发送推送命令，receivers是逗号分隔的用户id列表
     * @param __cb The asynchronous callback object.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendCmd(String receivers, String cmd, Ice.Callback __cb);

    /**
     * 全平台发送推送命令，receivers是逗号分隔的用户id列表
     * @param __ctx The Context map to send with the invocation.
     * @param __cb The asynchronous callback object.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendCmd(String receivers, String cmd, java.util.Map<String, String> __ctx, Ice.Callback __cb);

    /**
     * 全平台发送推送命令，receivers是逗号分隔的用户id列表
     * @param __cb The asynchronous callback object.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendCmd(String receivers, String cmd, Callback_bypush_sendCmd __cb);

    /**
     * 全平台发送推送命令，receivers是逗号分隔的用户id列表
     * @param __ctx The Context map to send with the invocation.
     * @param __cb The asynchronous callback object.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendCmd(String receivers, String cmd, java.util.Map<String, String> __ctx, Callback_bypush_sendCmd __cb);

    /**
     * 全平台发送推送命令，receivers是逗号分隔的用户id列表
     * @param __responseCb The lambda response callback.
     * @param __exceptionCb The lambda exception callback.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendCmd(String receivers, 
                                         String cmd, 
                                         IceInternal.Functional_VoidCallback __responseCb, 
                                         IceInternal.Functional_GenericCallback1<Ice.Exception> __exceptionCb);

    /**
     * 全平台发送推送命令，receivers是逗号分隔的用户id列表
     * @param __responseCb The lambda response callback.
     * @param __exceptionCb The lambda exception callback.
     * @param __sentCb The lambda sent callback.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendCmd(String receivers, 
                                         String cmd, 
                                         IceInternal.Functional_VoidCallback __responseCb, 
                                         IceInternal.Functional_GenericCallback1<Ice.Exception> __exceptionCb, 
                                         IceInternal.Functional_BoolCallback __sentCb);

    /**
     * 全平台发送推送命令，receivers是逗号分隔的用户id列表
     * @param __ctx The Context map to send with the invocation.
     * @param __responseCb The lambda response callback.
     * @param __exceptionCb The lambda exception callback.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendCmd(String receivers, 
                                         String cmd, 
                                         java.util.Map<String, String> __ctx, 
                                         IceInternal.Functional_VoidCallback __responseCb, 
                                         IceInternal.Functional_GenericCallback1<Ice.Exception> __exceptionCb);

    /**
     * 全平台发送推送命令，receivers是逗号分隔的用户id列表
     * @param __ctx The Context map to send with the invocation.
     * @param __responseCb The lambda response callback.
     * @param __exceptionCb The lambda exception callback.
     * @param __sentCb The lambda sent callback.
     * @return The asynchronous result object.
     **/
    public Ice.AsyncResult begin_sendCmd(String receivers, 
                                         String cmd, 
                                         java.util.Map<String, String> __ctx, 
                                         IceInternal.Functional_VoidCallback __responseCb, 
                                         IceInternal.Functional_GenericCallback1<Ice.Exception> __exceptionCb, 
                                         IceInternal.Functional_BoolCallback __sentCb);

    /**
     * 全平台发送推送命令，receivers是逗号分隔的用户id列表
     * @param __result The asynchronous result object.
     **/
    public void end_sendCmd(Ice.AsyncResult __result);
}
