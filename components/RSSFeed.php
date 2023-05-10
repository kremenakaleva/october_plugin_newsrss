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
                     ->descPublished()
                     ->get();
    
        $feed = new \SimpleXMLElement('<rss version="2.0"/>');
        $channel = $feed->addChild('channel');
    
        $channel->addChild('title', 'News');
        $channel->addChild('link', url('/'));
        $channel->addChild('description', 'Latest from news');
    
        foreach ($articles as $article) {
            $item = $channel->addChild('item');
            $item->addChild('title', $article->title);
            $item->addChild('link', url($article->url));
            $item->addChild('description', $article->content_limit);
            $category = $item->addChild('category');
            if ($article->type === Article::TYPE_NEWS) {
                $category[0] = 'Article';
            } elseif ($article->type === Article::TYPE_PUBLICATIONS) {
                $category[0] = 'Publication';
            }
            $item->addChild('pubDate', (new Carbon($article->published_at))->toRssString());
        }
    
        return \Response::make($feed->asXML(), '200', ['Content-Type' => 'application/rss+xml']);
    }
}
