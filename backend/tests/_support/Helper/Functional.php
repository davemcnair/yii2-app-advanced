<?php
namespace backend\tests\Helper;

class Functional extends \Codeception\Module
{

    function emptyTables(){
        $dbh = $this->getModule('Db')->dbh;
        $dbh->exec('SET FOREIGN_KEY_CHECKS=0;');
//        $res = $dbh->query("SHOW FULL TABLES WHERE TABLE_TYPE LIKE '%TABLE';")->fetchAll();
        $tables=['user'];
        foreach ($tables as $table) {
            $dbh->exec('delete from `' . $table . '` where id>1');
        }
        $dbh->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    function crawl_page($url, $depth = 5)
    {
        $urls = array();
        static $seen = array();
        if (isset($seen[$url]) || $depth === 0) {
            return;
        }

        $seen[$url] = true;

        $dom = new \DOMDocument('1.0');
        @$dom->loadHTMLFile($url);

        $anchors = $dom->getElementsByTagName('a');
        foreach ($anchors as $element) {
            $href = $element->getAttribute('href');
            if (0 !== strpos($href, 'http')) {
                $path = '/' . ltrim($href, '/');
                if (extension_loaded('http')) {
                    $href = http_build_url($url, array('path' => $path));
                } else {
                    $parts = parse_url($url);
                    $href = $parts['scheme'] . '://';
                    if (isset($parts['user']) && isset($parts['pass'])) {
                        $href .= $parts['user'] . ':' . $parts['pass'] . '@';
                    }
                    $href .= $parts['host'];
                    if (isset($parts['port'])) {
                        $href .= ':' . $parts['port'];
                    }
                    $href .= $path;
                }
            }

            $baseUrl = 'http://admin.example.com/';
            $baseChars = strlen($baseUrl);

            if (strpos($href, $baseUrl) !== false):
                $urls[] = substr($href, $baseChars - 1);
            endif;
        }
        return $urls;
    }
}
