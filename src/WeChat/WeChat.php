<?php
namespace Message\WeChat;


class WeChat
{
    use \Message\Tools;
    protected $accessToken = null;
    protected $redis = null;
    protected $params;
    protected $config;

    public function __construct($params, $config)
    {
        $this->config = $config;
        $this->params = $params;
        if (is_null($this->redis)) {
            try {
                $this->redis = new \Redis();
                $this->redis->connect($this->config['redis']['host'], $this->config['redis']['port']);
            } catch (\Exception $e) {
                throw new \Exception('Redis init failed!' . $e->getMessage());
            }
        }
        if (is_null($this->accessToken)) {
            $this->getAccessTokenByRedis();
        }
    }

    /**
     * 从redis缓存中获取accessToken，如果获取不到则通过api获取并缓存
     */
    protected function getAccessTokenByRedis()
    {
        $accessToken = $this->redis->get('wcAccessToken');
        if (!$accessToken) {
            $this->getAccessTokenByApi();
        } else {
            $this->accessToken = $accessToken;
        }
    }

    /**
     * 通过接口获取accessToken
     * 相关参数说明见企业微信官方文档 https://work.weixin.qq.com/api/doc#10013
     */
    protected function getAccessTokenByApi()
    {
        $url = $this->config['msg']['WeChat']['api'] . "/cgi-bin/gettoken?corpid={$this->config['msg']['WeChat']['corpID']}&corpsecret={$this->config['msg']['WeChat']['secret']}";
        $result = json_decode(file_get_contents($url), true);
        if ($result['errcode'] != 0) {
            throw new \Exception('Get WeChat access_token error');
        }
        $this->accessToken = $result['access_token'];
        $this->redis->setex('wcAccessToken', 5400, $result['access_token']);
    }

    /**
     * 发送消息，此处面向应用下的全体用户发送
     * 相关参数文档说明见企业微信官方文档 https://work.weixin.qq.com/api/doc#10167
     * @return bool
     */
    public function send()
    {
        $url = $this->config['msg']['WeChat']['api'] . "/cgi-bin/message/send?access_token={$this->accessToken}";
        $toWho = !empty($this->params['receivers']) ? implode('|' ,$this->params['receivers']) : '@all';
        $data = [
            "touser" => $toWho,
            "toparty" => "",
            "totag" => "",
            "msgtype" => "text",
            "agentid" => $this->config['msg']['WeChat']['agentId'],
            "text" => [
                "content" => $this->params['msgContent']
            ],
            "safe" => 0
        ];
        $data = json_encode($data);
        $result = json_decode(self::curlByPost($url, $data, 'json'), true);
        return (isset($result['errcode']) && $result['errcode'] == 0) ? true : false;
    }

}