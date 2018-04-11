<?php
namespace Message\DingTalk;

class DingTalk
{
    use \Message\Tools;
    protected $params;
    protected $config;

    public function __construct($params, $config)
    {
        $this->config = $config['msg'];
        $this->params = $params;
    }


    /**
     * 发送消息，此处@群组全体成员。
     * ps：可通过配置'at'参数@指定成员。
     * 相关参数说明见钉钉官方文档 https://open-doc.dingtalk.com/docs/doc.htm?spm=a219a.7629140.0.0.karFPe&treeId=257&articleId=105735&docType=1
     * @return bool
     */
    public function send()
    {
        $data = [
            "msgtype" => "text",
            "text" => ["content" => $this->params['msgContent']],
        ];

        if ($this->params['receivers']) {
            $data['at'] = [
                "atMobiles" => $this->params['receivers'],
                "isAtAll" => false
            ];
        }

        $data = json_encode($data, true);
        $url = $this->config['DingTalk']['api'] . '/robot/send?access_token=' . $this->config['DingTalk']['token'];
        $result = json_decode(self::curlByPost($url, $data, 'json'), true);
        return (isset($result['errcode']) && $result['errcode'] == 0) ? true : false;
    }
}