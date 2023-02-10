<?php
namespace Alibakhshiilani\WebProxy\Classes;

class HtmlParser {

    private $remoteBaseUrl = "";

    private function proxyUrlsInsideAssets($content){

        $matches = array();

        preg_match_all('/url(?:\([\'"]?)([.\/..\/\/].*?)(?:[\'"]\))/', $content, $matches);


//        var_dump($matches);
//        die();

        foreach ($matches[1] as $cssUrl){
            $content = str_replace($cssUrl,BASE_URL."?wp_url=".$this->remoteBaseUrl.$cssUrl,$content);
        }

        return $content;
    }

    private function proxyUrls($html){

        $prefixWithBaseUrl = 'http://localhost:8080?wp_url='.$this->remoteBaseUrl;
        $prefix = 'http://localhost:8080?wp_url=';
        $domain = $this->remoteBaseUrl;

        $targets = [
            "//img[not(starts-with(@src, '//'))]",
            "//script[not(starts-with(@src, '//'))]",
            "//link[not(starts-with(@href, '//'))]",
            "//form[not(starts-with(@action, '//'))]",
            "//a[not(starts-with(@href, '//'))]"
        ];

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query(implode('|', $targets)) as $node) {
            if ($src = $node->getAttribute('src')) {
                $node->setAttribute('src', (str_contains($src,$domain) ? $prefix : $prefixWithBaseUrl ). $src);
            } elseif ($action = $node->getAttribute('action')) {
                $node->setAttribute('action',    (str_contains($action,$domain) ? $prefix : $prefixWithBaseUrl ). $action);
            } else {
                $node->setAttribute('href', (str_contains($src,$domain) ? $prefix : $prefixWithBaseUrl) . $node->getAttribute('href'));
            }
        }
//        echo ;

        return $dom->saveHTML();
    }


    private function addInfoBar($html,$info){
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $template = $dom->createDocumentFragment();
        $template->appendXML('<h1>This is <em>my</em> template</h1>');

        $dom->getElementsByTagName("body")
            ->item(0)
            ->appendChild($template);

        return $dom->saveHTML();
    }

    public function parse($page,$baseUrl){
//        echo "dd";
        $this->remoteBaseUrl = $baseUrl;
        $content = $page["content"];

        if(
            isset($page["info"]["content_type"]) &&
            str_contains($page["info"]["content_type"],"text/html")
        ){
            $content = $this->proxyUrls($content);
            $content = $this->addInfoBar($content,$page["info"]);
        }else if(
            isset($page["info"]["content_type"]) &&
            str_contains($page["info"]["content_type"],"text/css")
        ){
            $content = $this->proxyUrlsInsideAssets($content);
        }

        return $content;
    }
}