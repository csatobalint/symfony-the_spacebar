<?php
/**
 * Created by PhpStorm.
 * User: EDU_UGEX_7219
 * Date: 6/1/2019
 * Time: 3:06 PM
 */

namespace App\Service;


use Michelf\MarkdownInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class MarkdownHelper
{

    private $cache;
    private $markdown;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var
     */
    private $isDebug;

    public function __construct(AdapterInterface $cache,MarkdownInterface $markdown, LoggerInterface $markdownLogger, bool $isDebug)
    {
        $this->cache = $cache;
        $this->markdown = $markdown;
        $this->logger = $markdownLogger;
        $this->isDebug = $isDebug;
    }

    public function parse(string $source): string
    {
        if(stripos($source,'bacon') !== false){
            $this->logger->info('They are talking about bacon again');
        }

        //if it is dev, than no caching
        if($this->isDebug){
            return $this->markdown->transform($source);
        }

        $item = $this->cache->getItem('markdown_'.md5($source));
        if (!$item->isHit()) {
            $item->set($this->markdown->transform($source));
            $this->cache->save($item);
        }

        return $item->get();
    }

}