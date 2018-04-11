<?php
namespace Message;

trait Tools
{

    public function curlByPost($url, $msg, $dataType = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($dataType == 'json') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type:application/json; charset=utf-8',
                'Content-Length:' . strlen($msg)
            ));
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}