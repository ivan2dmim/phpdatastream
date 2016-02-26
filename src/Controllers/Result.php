<?php
namespace PHPDatastream\Controllers;

use Http\Response;
use Http\Request;
use PHPDatastream\Template\Renderer;

class Result
{

    private $request;

    private $response;

    private $renderer;

    public function __construct(Request $request, Response $response, Renderer $renderer)
    {
        $this->request = $request;
        $this->response = $response;
        $this->renderer = $renderer;
    }

    public function result()
    {
        //var_dump($this->request);die;
        $series = $this->request->getParameter('series');
        var_dump($series);
        die;
        $data = ['name' => $this->request->getParameter('name', 'result')];
        
        $html = $this->renderer->render('Homepage', $data);
        
        $this->response->setContent($html);
    }
}