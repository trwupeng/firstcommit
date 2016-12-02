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
// Generated from file `evtcenter.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//

package sooh.services.evtcenter;

public interface _triggersOperationsNC
{
    void onRegister(String data);

    void onLogout(String data);

    void onLogin(String data);

    void onBuyRequest(String data);

    void onBuyConfirm(String data);

    void onRechargeRequest(String data);

    void onRechargeConfirm(String data);

    void onWithdrawRequest(String data);

    void onWithdrawConfirm(String data);

    void onWaresFull(String data);

    void onWaresMonthly(String data);

    void onWaresEnd(String data);

    void onCustomEvt(String evt, String data);
}
