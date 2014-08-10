<?php

/**
 * Clase para generar Feeds
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @date 09.agosto-2014
 */
class Feeds {

    /**
     * Devuelve xml en formato RSS
     * 
     * @param string $titulo EL titulo del Rss
     * @param array $rows La información del feed
     * @return string xml
     */
    static function getRss($titulo, $rows) {

        $items = "";

        foreach ($rows as $row) {

            $items .= "<item>"
                    . "<guid isPermaLink='true'>{$_SESSION['appUrl']}{$row['UrlFriendly']}</guid>"
                    . "<title>{$row['Titulo']}</title>"
                    . "<link>{$_SESSION['appUrl']}{$row['UrlFriendly']}</link>"
                    . "<description><![CDATA[{$row['Resumen']}]]></description>"
                    . "<pubDate>" . date('r', strtotime($row['PublishedAt'])) . "</pubDate>"
                    . "</item>";
        }

        $xml = '<?xml version="1.0" encoding="UTF-8" ?>'
                . "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">"
                . "<channel>"
                . "<title>RSS {$_SESSION['varWeb']['Pro']['globales']['empresa']} - {$titulo}</title>"
                . "<link>{$_SESSION['appUrl']}/rss</link>"
                . "<description>RSS {$_SESSION['varWeb']['Pro']['globales']['empresa']}</description>"
                . "<language>es</language>"
                . "<atom:link href=\"{$_SESSION['appUrl']}/rss\" rel=\"self\" type=\"application/rss+xml\" />"
                . $items
                . "</channel></rss>";

        return $xml;
    }

    /**
     * Devuelve xml en formato SITEMAP
     * 
     * @param array $rows La información del feed
     * @return string xml
     */
    static function getSiteMap($rows) {

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
            xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" 
            xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';

        $urls = "";
        foreach ($rows as $row) {
            $urls .= "<url>"
                    . "<loc>{$_SESSION['appUrl']}{$row['UrlFriendly']}</loc>"
                    . "<lastmod>{$row['ModifiedAt']}</lastmod>"
                    . "<changefreq>{$row['ChangeFreqSitemap']}</changefreq>"
                    . "<priority>{$row['ImportanceSitemap']}</priority>"
                    . "</url>";
        }

        $xml = $xml . $urls . "</urlset>";

        return $xml;
    }

    /**
     * Devuelve xml en formato SITEMAPINDEX
     * 
     * @param array $rows La información del feed
     * @return string xml
     */
    static function getSiteMapIndex($rows) {

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
            xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" 
            xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';

        $urls = "";
        foreach ($rows as $row) {
            $urls .= "<sitemap>"
                    . "<loc>{$_SESSION['appUrl']}{$row['UrlFriendly']}</loc>"
                    . "</sitemap>";
        }

        $xml = $xml . $urls . "</sitemapindex>";

        return $xml;
    }

}
