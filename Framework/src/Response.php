<?php

namespace Abbarbosa\LittleBoy\Framework;

use Abbarbosa\LittleBoy\Framework\Collection;

/**
 * ==============================================================================================================
 *
 * Response: Classe para tratar retorno
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */
class Response
{
    protected $headers;
    protected $version;
    protected $content;
    protected $charset;
    protected $statusCode;

    public function __construct($content = null, int $statusCode = 200,
                                array $headers = [], string $charset = 'UTF-8')
    {
        $this->setHeaders($headers);
        $this->setContent($content);
        $this->statusCode($statusCode);
        $this->setCharset($charset);
        $this->setVersion();
    }

    public function setHeaders(array $headers)
    {
        if (is_null($this->headers)) {
            $this->headers = new Collection;
        }
        $this->headers->add($headers);
        return $this;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }

    public function setStatus(int $code)
    {
        $this->statusCode = $code;
        return $this;
    }

    public function setVersion(string $version = '1.1')
    {
        $this->version = $version;
        return $this;
    }

    public function setCharset(int $charset)
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     *
     * ========================================================================
     *
     * Adaptação temporária (para baixo) -- Não corresponde a ideia final
     *
     * Os métodos abaixo estão apenas pegando espaços emprestados
     *
     * ========================================================================
     *
     */
    public function back($old = false)
    {
        if ($old == true) {

            foreach ((array) request()->all() as $key => $value) {
                session()->setOld($key, $value);
            }
        }

        return header('Location: '.$_SERVER['HTTP_REFERER']);
    }

    public function redirect($uri = null, $old = false)
    {

        if ($old == true) {

            foreach ((array) request()->all() as $key => $value) {
                session()->setOld($key, $value);
            }
        }

        if (is_null($uri)) {
            $uri = request()->url();
        }
        return header("Location:{$uri}");
    }

    public function json($data)
    {

        if (!is_array($data)) {
            $data = (array) $data;
        }

        return json_encode($data);
    }

    function route($name)
    {

        if (func_num_args() > 1) {
            $args   = func_get_args();
            $name   = array_shift($args);
            $params = $args;
        } else {
            $params = [];
        }

        return router()->name($name, $params);
    }
}