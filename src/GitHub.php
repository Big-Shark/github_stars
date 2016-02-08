<?php

class GitHub
{
    protected $userName;
    protected $accessToken;
    protected $cache;

    public function __construct($userName, $accessToken, Psr\Cache\CacheItemPoolInterface $cache,  Psr\Log\LoggerInterface $logger)
    {
        $this->userName = $userName;
        $this->accessToken = $accessToken;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function exec($url, $query = [])
    {
        $query['access_token'] = $this->accessToken;

        $url = 'https://api.github.com/'.$url.'?'.http_build_query($query);

        $cache = $this->cache->getItem(md5($url));
        if(!$cache->isHit())
        {
            $c = curl_init($url);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_HEADER, false);
            curl_setopt($c, CURLINFO_HEADER_OUT, true);
            curl_setopt($c, CURLOPT_HTTPHEADER, ["User-Agent: ".$this->userName, "Accept: application/vnd.github.v3+json", "Content-Type: application/json; charset=utf-8"]);
            $content = curl_exec($c);
            $code = curl_getinfo($c, CURLINFO_HTTP_CODE);
            $this->logger->info('Loading "'.$url.'"');
            if($code == 200 || $code == 404) {
                $cache->set($content);
                $this->cache->save($cache);
            } else {
                $this->logger->error(json_encode(curl_getinfo($c)));
            }

            curl_close ($c);
        }

        $content = $cache->get();
        return $content === FALSE ? FALSE : json_decode($content, TRUE);
    }
}