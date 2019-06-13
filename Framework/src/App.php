<?php

namespace Abbarbosa\LittleBoy\Framework;

use \Abbarbosa\LittleBoy\Framework\Request;
use \Abbarbosa\LittleBoy\Framework\Response;
use \Abbarbosa\LittleBoy\Framework\Router;
use \Abbarbosa\LittleBoy\Framework\Flash;

class App
{
    protected $request;
    protected $router;
    protected $response;

    public function __construct(Request $request)
    {
        $this->router = new Router();
        $this->request = $request;
        $this->response = new Response();
        $session = new Flash;
        $this->response->setRequest($this->request)
            ->setSession($session);
    }

    public function find()
    {
        if ($this->request) {
            $route = $this->router->find($this->request->method(), $this->request);
            if ($route) {
                /**
                 * Por enquanto: Apos router encontrar a rota despacha e recebe um retorno do
                 * conroller que deve ser encapsulado na response e enviado para browser junto
                 * aos cabeçalhos.
                 */
                $this->response->setContent($route->dispatch());
            }
        }
        return $this;
    }

    public function handle()
    {
        // Manipular algo: Um middleware poderá ser implementado aqui
    }

    public function send()
    {
        if ($this->response) {
            return $this->response->send();
        }
    }
}
