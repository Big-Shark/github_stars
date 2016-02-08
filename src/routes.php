<?php
// Routes

$app->get('/', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {

    return $this->renderer->render($response, 'index.phtml');
});

$app->get('/repositories/{userName}', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
    $repositories = [];
    for($page = 1; $page; $page++) {
        $pageRepositories = $this->github->exec('users/'.$args['userName'].'/starred', ['page' => $page]);
        if(!$pageRepositories) break;

        foreach($pageRepositories as $repository) {
            $repositories[] = [
                'full_name' => $repository['full_name'],
                'stargazers_count' => $repository['stargazers_count'],
            ];
        }
    }

    $response->getBody()->write(json_encode($repositories, JSON_PRETTY_PRINT));

    return $response;
});

$app->get('/composer/{userName}/{repositoryName}', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {

    $repositoryFullName = $args['userName'].'/'.$args['repositoryName'];
    $composer = $this->github->exec('repos/'.$repositoryFullName.'/contents/composer.json');

    $result = null;
    if(isset($composer['content'])) {
        $composer = json_decode(base64_decode($composer['content']), true);
        if(isset($composer['keywords'])) {
            $result = [
                'keywords' => $composer['keywords'],
                'description' => $composer['description']
            ];
        }
    }

    $response->getBody()->write(json_encode($result, JSON_PRETTY_PRINT));

    return $response;
});

/*
$app->get('/stars', function ($request, $response, $args) {
    $user_name = $args['user_name'] ?? $this->github->getUserName();
    $repos = [];
    for($page = 1; $page; $page++) {

        $starred = $this->github->exec('users/'.$user_name.'/starred', ['page' => $page]);
        if(!$starred) break;

        foreach ($starred as $repository) {
            $composer = $this->github->exec('repos/'.$repository['full_name'].'/contents/composer.json');
            if($composer === false or (isset($composer['message']) and $composer['message'] === 'Not Found')) //404
                continue;

            //$json = json_decode(base64_decode($composer->content));
            //if(isset($json))
            //{
            $repos[$repository['full_name']] = $repository;
            //}
        }
    }

    // Render index view
    return $this->renderer->render($response, 'index.phtml', ['repos' => $repos]);
});

$app->get('/statistic', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {

    $user_name = $args['user_name'] ?? $this->github->getUserName();

    $keywords = [];
    $repositories = [];
    for($page = 1; $page; $page++) {

        $starred = $this->github->exec('users/'.$user_name.'/starred', ['page' => $page]);
        if(!$starred) break;

        foreach ($starred as $repository) {
            //if($repository->language !== 'PHP')
            //    continue;

            $composer = $this->github->exec('repos/'.$repository['full_name'].'/contents/composer.json');
            if($composer === false or (isset($composer['message']) and $composer['message'] === 'Not Found')) //404
                continue;

            $composer = json_decode(base64_decode($composer['content']));
            isset($composer['keywords']) && $repositories[$repository['full_name']] = $composer['keywords'];
            /*$json =  $repository['keywords']
            if(isset($json->keywords))
            {
                foreach ($json->keywords as $keyword) {
                    if(!isset($keywords[$keyword]))
                        $keywords[$keyword] = [];

                    $keywords[$keyword][] = $repository->full_name;
                }
                //$keywords = array_merge($keywords, $json->keywords);
            }
        }
    }
/*
    $keywords = array_filter($keywords, function($items)
    {
        return count($items) > 1;
    });
    uasort($keywords, function ($a, $b) {
        $a = count($a);
        $b = count($b);
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    });
    //$counts = array_count_values($keywords);
    //arsort($counts);
    var_dump($keywords);die();
    $response->getBody()->write(json_encode($repositories, JSON_PRETTY_PRINT));

    return $response;
});
*/
