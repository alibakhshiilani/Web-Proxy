<?php
namespace Alibakhshiilani\WebProxy\Classes;

class Proxy {
    private $url = null;
    private $baseUrl = null;

    private function getContentByCurl()
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->url);
//        curl_setopt($curl,CURLOPT_ENCODING,'gzip');
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
        curl_setopt($curl, CURLOPT_TIMEOUT, 2400);
//        curl_setopt($curl,CURLOPT_BINARYTRANSFER, true);

//        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
//            "Content-Type: text/event-stream",
//            "cache-control:no-cache",
//            "Accept-Language: {en-us,en;q=0.5}",
//            "Access-Control-Allow-Origin:*",
////            'Authorization: Basic '. base64_encode($user.':'.$pass)
//        ));

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        $cookies = array();
        foreach ($_COOKIE as $key => $value) {
            if ($key != 'Array') {
                $cookies[] = $key . '=' . $value;
            }
        }
        curl_setopt($curl, CURLOPT_COOKIE, implode(';', $cookies));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

//        session_write_close();

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if ($result === FALSE) {
            return "Error: " . curl_error($curl);
        }

//        echo "<pre>";
//
//        var_dump($info);
//
//        echo "<pre>";
//        die();

//        session_start();

        list($header, $body) = explode("\r\n\r\n", $result, 2);

        foreach (explode("\r\n", $header) as $i => $line){
            header($line, false);
        }

        return [
            "content"=>$body,
            "info"=>$info
        ];

    }

    private function getContentByGetContentFunction(){

    }

    private function getHtmlContent(){
        return $this->getContentByCurl();
    }

    public function getBaseUrl(){
        return $this->baseUrl;
    }

    public function go($url){
        $this->url = $url;
        $urlInfo = parse_url($url);
        if(isset($urlInfo['scheme']) && isset($urlInfo['host'])){
            $this->baseUrl = $urlInfo['scheme']."://".$urlInfo['host'];
        }else if(isset($urlInfo['host'])){
            $this->baseUrl = "http://".$urlInfo['host'];
        }
        $htmlParser = new HtmlParser();
        return $htmlParser->parse($this->getHtmlContent(),$this->baseUrl);
    }
}