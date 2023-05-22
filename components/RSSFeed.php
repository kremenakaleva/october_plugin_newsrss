<?php namespace Pensoft\NewsRSS\Components;

use Cms\Classes\ComponentBase;
use Pensoft\Articles\Models\Article;
use Carbon\Carbon;

class RSSFeed extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'News RSS Feed',
            'description' => 'Generates an RSS feed for the news section.'
        ];
    }

    public function onRun()
    {
        $articles = Article::news()
        ->where('published_at', '<=', 'now()')
        ->where('published', 'true')
        ->orderBy('published_at', 'DESC')
        ->get();

        $doc = new \DOMDocument('1.0', 'UTF-8');
        $rss = $doc->createElement('rss');
        $rss->setAttribute('version', '2.0');
        $rss->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom'); 
        $doc->appendChild($rss);

        $channel = $doc->createElement('channel');
        $rss->appendChild($channel);

        $atomLink = $doc->createElement('atom:link');
        $atomLink->setAttribute('href', url('/news/rss'));    
        $atomLink->setAttribute('rel', 'self');
        $atomLink->setAttribute('type', 'application/rss+xml');
        $channel->appendChild($atomLink);

        $title = $doc->createElement('title', 'News');
        $channel->appendChild($title);

        $description = $doc->createElement('description', 'Latest from news');
        $channel->appendChild($description);

        $link = $doc->createElement('link', url('/'));
        $channel->appendChild($link);

        $lastBuildDate = $doc->createElement('lastBuildDate', (new Carbon())->toRssString());
        $channel->appendChild($lastBuildDate);

        $generator = $doc->createElement('generator', 'Pensoft NewsRSS Plugin');
        $channel->appendChild($generator);

        foreach ($articles as $article) {

            $item = $doc->createElement('item');
            $channel->appendChild($item);

            $guid = $doc->createElement('guid', url($article->url));
            $guid->setAttribute('isPermaLink', 'true');
            $item->appendChild($guid);


            $title = $doc->createElement('title', $article->title);
            $item->appendChild($title);

            $link = $doc->createElement('link', url($article->url));
            $item->appendChild($link);

            $description = $doc->createElement('description');
            $cdata = $doc->createCDATASection($article->content);
            $description->appendChild($cdata);
            $item->appendChild($description);

            $category = $doc->createElement('category');
            if ($article->type === Article::TYPE_NEWS) {
                $category->nodeValue = 'Article';
            } elseif ($article->type === Article::TYPE_PUBLICATIONS) {
                $category->nodeValue = 'Publication';
            }
            $item->appendChild($category);

            $pubDate = $doc->createElement('pubDate', (new Carbon($article->published_at))->toRssString());
            $item->appendChild($pubDate);
        }

        return \Response::make($doc->saveXML(), '200', ['Content-Type' => 'text/xml']);

    }

}
