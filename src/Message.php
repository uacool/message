<?php
namespace Message;

class Message
{

    public static $allowMsgType = ['DingTalk', 'WeChat', 'QQ', 'SMS', 'Email'];
    protected static $msgConfig = null;

    /**
     * 消息发送方法 详见README.MD
     * @param $msgType | 消息类型
     * @param $params | 参数；包含消息内容与接收者
     * @param $config | 从配置文件解析出的数组：包含消息的相关配置 和 redis配置
     * 格式如下 :
     *   array (
     *       'redis' => array (
     *           'host' => '127.0.0.1',
     *           'port' => 6379,
     *           'index' => 1,
     *           'prefix' => 'cache_',
     *       ),
     *
     *       'msg' => array (
     *           'SMS' =>
     *               array (
     *               'secret_key' => '############################',
     *               'app_key' => '########',
     *               'sms_type' => '####',
     *               'sms_sign' => '####',
     *               'tmp_captcha' => 'SMS_########',
     *               ),
     *           'Email' =>
     *               array (
     *               'host' => 'smtp.163.com',
     *               'platform' => '####',
     *               'username' => 'abcedfg@163.com',
     *               'password' => '########',
     *               'smtpAuth' => true,
     *               'charset' => 'UTF-8',
     *               'isHtml' => true,
     *               ),
     *           'WeChat' =>
     *               array (
     *               'corpID' => '##########',
     *               'agentId' => 1000003,
     *               'secret' => '######################',
     *               'api' => 'https://qyapi.weixin.qq.com',
     *               ),
     *           'DingTalk' =>
     *               array (
     *               'token' => '#################################################',
     *               'api' => 'https://oapi.dingtalk.com',
     *               ),
     *       ),
     *   )
     * @return mixed
     * @throws \Exception
     */
    public static function send($msgType, $params, $config)
    {
        if (empty($msgType) || empty($params) || empty($config)) {
            throw new \Exception('Params missing');
        }

        if (!in_array($msgType, self::$allowMsgType)) {
            throw new \Exception('Unsupported message type');
        }

        if (is_null(self::$msgConfig)) {
            self::$msgConfig = $config;
        }

        $className = "\\Message\\{$msgType}\\{$msgType}";
        $msgObj = new $className($params, self::$msgConfig);
        return $msgObj->send();
    }
}