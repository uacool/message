<?php
namespace Message\SMS;

use Whoops\Example\Exception;

class SMS
{
    protected $params;
    protected $config;
    protected $smsObj;

    public function __construct($params, $config)
    {
        $this->config = $config['msg'];
        $this->params = $params;
    }

    /**
     * 发送短信
     */
    public function send()
    {
        require __DIR__ . '/TopSdk.php';
        if (!$this->smsObj) {
            $this->smsObj = new \TopClient;
        }
        $this->smsObj->appkey = (string)$this->config['SMS']['app_key'];                          // 强转字符
        $this->smsObj->secretKey = $this->config['SMS']['secret_key'];
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType($this->config['SMS']['sms_type']);                                       // 短信类型
        $req->setSmsFreeSignName($this->config['SMS']['sms_sign']);                               // 签名
        $req->setSmsParam(json_encode(['code' => $this->params['msgContent']]));   //TODO::实际使用需在阿里大于后台配置模板
        $req->setSmsTemplateCode($this->config['SMS']['tmp_captcha']);                            // 模板编号
        if (!$this->params['receivers']) {
            throw new Exception('please set receivers');
        }
        foreach ($this->params['receivers'] as $receiver) {
            $req->setRecNum($receiver);
            $response = $this->smsObj->execute($req);
            $this->handle($response, $receiver);
        }
        return true;
    }

    /**
     * 返回结果处理
     * @param $response
     * @return array
     */
    public function handle($response, $receiver)
    {
        if (!empty($response->result->success)) {
            $this->log("phone:$receiver,code:{$this->params['msgContent']},statCode:0,result:success");
        } else {
            $this->log("phone:$receiver,statCode:$response->code,result:$response->sub_msg");
        }
    }

    /**
     * 短信详情日志
     * @param string $log
     */
    private function log($log = '')
    {
        $file = __DIR__ .  '/log/SMS_' . date('Ym') . '.log';
        $handle = fopen($file, "a+b");
        $text = date('Y-m-d H:i:s') . ' ' . $log . "\r\n";
        fwrite($handle, $text);
        fclose($handle);
    }


}