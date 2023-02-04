<?php
namespace Alibakhshiilani\WebProxy\Classes;

class Proxy {
    private $url = null;

    private function getContentByCurl(){
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            ("Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko"),
            "Accept-Language: {en-us,en;q=0.5}"
        ));

        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        $cookies = array();
        foreach ($_COOKIE as $key => $value)
        {
            if ($key != 'Array')
            {
                $cookies[] = $key . '=' . $value;
            }
        }
        curl_setopt( $curl, CURLOPT_COOKIE, implode(';', $cookies) );

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        session_write_close();

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if($result === FALSE)
        {
            return "Error: " . curl_error($curl);
        }

        session_start();


        list($header, $body) = explode("\r\n\r\n", $result, 2);

        preg_match_all('/^(Set-Cookie:\s*[^\n]*)$/mi', $header, $cookies);
        foreach($cookies[0] AS $cookie)
        {
            header($cookie, false);
        }

        return $body;

    }

    private function getContentByGetContentFunction(){

    }

    private function getHtmlContent(){
        return $this->getContentByCurl();
    }

    public function go($url){
        $this->url = $url;
        $htmlParser = new HtmlParser();
        return $htmlParser->parse($this->getHtmlContent());

    }
}