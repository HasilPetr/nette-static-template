<?php

namespace App;

use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Http\IRequest;
use Nette\Http\Url;
use Nette\Object;

class CustomRouter extends Object implements IRouter
{
    const pagesDir = '/../pages';
    private $origUrl;
    private $origParams;

    public function match(IRequest $httpRequest)
    {
        $dir = rtrim($httpRequest->getUrl()->path, '/');
        $pagesDir = __DIR__ . self::pagesDir;
        $this->origUrl = rtrim($httpRequest->getUrl(), '/');

        $template = $pagesDir . $dir . '/default.latte';
        if (!file_exists($template)) {
            $template = $pagesDir . $dir . '.latte';
            if (!file_exists($template))
                return null;
        } else
            $this->origUrl .= '/';

        do {
            $layout = $pagesDir . $dir . '/@layout.latte';
            if (file_exists($layout)) break;
            $dir = dirname($dir);
        } while ($dir);

        $params = $this->origParams = $httpRequest->getQuery();
        $params['template'] = $template;
        $params['layout'] = $layout;
        return new Request(
            'Page',
            $httpRequest->getMethod(),
            $params,
            $httpRequest->getPost(),
            $httpRequest->getFiles(),
            array('secured' => $httpRequest->isSecured())
        );
    }

    public function constructUrl(Request $appRequest, Url $refUrl)
    {
        $params = $appRequest->getParameters();
        unset($params['template'], $params['layout'], $params['action']);
        $url = new Url($this->origUrl);
        $url->appendQuery($params);
        return $url;
    }

}