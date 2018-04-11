#### 使用说明

> 示例代码基于phalcon框架，别的框架使用类似。

```
<?php

namespace MyApp\Controllers;

use Message\Message;
use Phalcon\DI;

class MessageController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
    }


    public function sendAction()
    {
        // $config 为解析配置文件得到的数组
        // 格式如下，不同的请自行转换
        #array (
        #    'redis' => array (
        #        'host' => '127.0.0.1',
        #        'port' => 6379,
        #        'index' => 1,
        #        'prefix' => 'cache_',
        #    ),

        #    'msg' => array (
        #        'SMS' =>
        #            array (
        #            'secret_key' => '############################',
        #            'app_key' => '########',
        #            'sms_type' => '####',
        #            'sms_sign' => '####',
        #            'tmp_captcha' => 'SMS_########',
        #            ),
        #        'Email' =>
        #            array (
        #            'host' => 'smtp.163.com',
        #            'platform' => '####',
        #            'username' => 'abcedfg@163.com',
        #            'password' => '########',
        #            'smtpAuth' => true,
        #            'charset' => 'UTF-8',
        #            'isHtml' => true,
        #            ),
        #        'WeChat' =>
        #            array (
        #            'corpID' => '##########',
        #            'agentId' => 1000003,
        #            'secret' => '######################',
        #            'api' => 'https://qyapi.weixin.qq.com',
        #            ),
        #        'DingTalk' =>
        #            array (
        #            'token' => '#################################',
        #            'api' => 'https://oapi.dingtalk.com',
        #            ),
        #    ),
        #)
        $config = DI::getDefault()->get('config')->toArray();

        // 短信发送示例代码
        $content = '短信内容';
        $receivers = ['18000000000', '15811111111'];
        $params = ['msgContent' => $content, 'receivers' => $receivers];
        $result = Message::send('SMS', $params, $config);

        // email发送示例代码
        $content = ['subject' => '邮件主题', 'body' => '邮件内容'];
        $receivers = ['zhangsan666@163.com', 'lisi888@qq.com'];
        $params = ['msgContent' => $content, 'receivers' => $receivers];
        $result = Message::send('Email', $params, $config);

        // 企业微信发送示例代码  点击用户可看到用户ID
        $receivers = ['LiKunLong', 'WangXiaoHan', 'LiHe'];   // $receivers=[]给所有人发
        $content = '消息内容';
        $params = ['msgContent' => $content, 'receivers' => $receivers];
        $result = Message::send('WeChat', $params, $config);

        // 钉钉发送示例代码     钉钉用户名为手机号
        $receivers = ['13711111111', '18000000000'];  // $receivers=[]给所有人发
        $content = "钉钉";
        $params = ['msgContent' => $content, 'receivers' => $receivers];
        $result = Message::send('DingTalk', $params, $config);

        var_dump($result);
    }

}
```