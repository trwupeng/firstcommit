<?php
return array(
    'logout_success'                            => 'logout_done',//退出登录成功
    'phone_number_is_not_valid'                 => 'phone_number_is_not_valid',//手机号不合法
    'phone_number_is_not_registered'            => 'phone_number_is_not_registered',//手机号未注册
    'smscode_is_not_valid'                      => 'smscode_is_not_valid',//短信验证码不合法
    'smscode_send_failed'                       => 'smscode_send_failed',//验证码发送失败
    'smscode_send_success'                      => 'smscode_send_success',//验证码已发送至手机
    'smscode_not_correct_or_timed_out'          => 'smscode_not_correct_or_timed_out',//验证码不正确或已经超时
    'password_is_not_valid'                     => 'password_is_not_valid',//密码不合法
    'success'                                   => 'success',//成功
    'clientType_is_not_valid'                   => 'clientType_is_not_valid',//clientType不合法
    'clientId_is_not_valid'                     => 'clientId_is_not_valid',//clientId不合法
    'clientSecret_is_not_valid'                 => 'clientSecret_is_not_valid',//clientSecret不合法
    'contractId_is_not_valid'                   => 'contractId_is_not_valid',//渠道ID不合法
    'deviceId_is_not_valid'                     => 'deviceId_is_not_valid',//设备ID不正确
    'no_login_or_has_timed_out'                 => 'no_login_or_has_timed_out',//未登入或已经超时，请重新登入
    'ban_repeated_refreshes'                    => 'ban_repeated_refreshes',//禁止重复刷新'//禁止重复刷新-Prj\Oauth中重复刷新token触发
    //登录信息已过期，请重新登录-token凭证失效-长时间（超过refreshToken有效期）未登录、刷新触发
    'login_info_has_expired_please_login_again' => 'login_info_has_expired_please_login_again',// '登录信息已过期，请重新登录',
    'smscode_has_been_sent'                     => '短信已发送，请您稍后再试',//短信已发送，请您稍后再试
);