<?php

namespace Abbarbosa\LittleBoy\Framework;

use \Abbarbosa\LittleBoy\Framework\Flash;
use \Abbarbosa\LittleBoy\Framework\Request;

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
    protected $request;
    protected $session;
    protected $content;
    protected $status;
    protected $version;
    protected $charset;
    protected $cookies = [];
    protected $secure = false;
    protected $statusDescription = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    public function __construct($content = '', $status = 200, $headers = [], $charset = 'UTF-8')
    {
        $this->setHeaders($headers);
        $this->setContent($content);
        $this->setStatus($status);
        $this->setVersion();
        $this->charset = $charset;
        $this->session = new Flash; // TO DO
        $this->request = new Request; // TO DO (Eliminar daqui)
    }

    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    public function setSession($session)
    {
        $this->session = $session;
        return $this;
    }

    public function isSecure(bool $option)
    {
        $this->secure = $option;
        return $this;
    }

    public function getProtocol()
    {
        return $this->secure ? 'https' : 'http';
    }

    public function setContentType(string $type = 'html')
    {
        if (strtolower($type) == 'html') {
            $type = 'text/html; charset=' . $this->charset;
        } else if (strtolower($type) == 'json') {
            $type = 'application/json; charset=' . $this->charset;
        }
        $this->setHeader('Content-Type', $type);
        return $this;
    }

    public function redirect(string $redirection, bool $old = false)
    {
        if (((!!strpos(strtolower($redirection), 'http://') === false) && (!!strpos(strtolower($redirection), 'https://') === false)) === false) {
            $redirection = $this->getProtocol() . '://' . $redirection;
        }
        if ($old == true) {
            foreach ((array) $this->request->all() as $key => $value) {
                $this->session->setOld($key, $value);
            }
        }
        /*
        if (is_null($uri)) {
        $uri = $this->request->url();
        }*/

        $this->setHeader('Location', "Location: {$redirection}");
        $this->send();
    }

    public function setVersion(string $version = '1.1')
    {
        $this->version = $version;
        return $this;
    }

    public function sendHeaders()
    {
        if (headers_sent()) {
            return $this;
        }

        if (isset($this->headers['Location'])) {
            header($this->headers['Location'], $this->status);
            exit();
        }

        if ($this->version === '1.0' && !!strpos($this->headers['Cache-Control'], 'no-cache') !== false) {
            $this->headers['pragma'] = 'no-cache';
            $this->headers['expires'] = -1;
        }

        if (!isset($this->headers['Cache-Control'])) {
            $this->headers['Cache-Control'] = '';
        }

        if (!isset($this->headers['Date'])) {
            $this->setDate(new \DateTime("NOW"));
        }

        if (!isset($this->headers['Content-Type'])) {
            $this->headers['Content-Type'] = 'text/html; charset=' . $this->charset;
        }

        // headers
        foreach ($this->headers as $header => $values) {
            if (is_string($header)) {
                header($header . ': ' . $values, $header, $this->status);
            } else {
                header($values, $this->status);
            }
        }
        header(sprintf('HTTP/%s %s %s', $this->version, $this->status, $this->statusDescription[$this->status]), true, $this->status);
    }

    public function setCookie($name, $value, $path = '/', $domain = null, $expires = null, $maxAge = 0, bool $isRaw = false, bool $onlyHttp = false, bool $secure = false)
    {
        if (!is_null($maxAge) && is_null($expires)) {
            $maxAge = $expires - time();
        }

        $str = ($isRaw ? $name : urlencode($name)) . '=';
        if ('' === (string) $value) {
            $str .= 'deleted; expires=' . gmdate('D, d-M-Y H:i:s T', time() - 31536001) . '; Max-Age=0';
        } else {
            $str .= $isRaw ? $value : rawurlencode($value);
            if (is_null($expires)) {
                $str .= '; expires=' . gmdate('D, d-M-Y H:i:s T', $expires) . '; Max-Age=' . $maxAge;
            }
        }
        $str .= '; path=' . $path;

        if ($domain) {
            $str .= '; domain=' . $domain;
        }
        if ($secure) {
            $str .= '; secure';
        }
        if ($onlyHttp) {
            $str .= '; httponly';
        }
        $this->cookies[] = $str;
        return $this;
    }

    public function sendContent()
    {
        echo $this->getContent();
        return $this;
    }

    public function getContent(): string
    {
        return (string) $this->content;
    }

    public function setStatus(int $status)
    {
        $this->status = $status;
        return $this;
    }

    public function addStatusCode(int $code, string $description = 'Unknown status')
    {
        $this->statusDescription[$code] = $description;

        return $this;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }

    public function withHeader($header, $description = null)
    {
        $this->setHeader($header, $description);
        return $this;
    }

    public function withHeaders($arguments)
    {
        $numArgs = func_num_args();
        if ($numArgs > 0) {
            $args = func_get_args();
            $this->setHeaders($args);
        }
        return $this;
    }

    public function setHeaders(array $headers)
    {
        if (count($headers) > 0) {
            foreach ($headers as $header => $description) {
                if (is_string($header)) {
                    $this->setHeader($header, $description);
                    continue;
                }
                $this->setHeader($description);
            }
        }
        return $this;
    }

    public function setHeader($header, $description = null)
    {
        if (!is_null($description)) {
            $this->headers[$header] = $description;
        } else {
            $this->headers[] = $header;
        }
        return $this;
    }

    public function setDate(\DateTime $date)
    {
        $date->setTimezone(new \DateTimeZone('UTC'));
        $this->headers['Date'] = $date->format('D, d M Y H:i:s') . ' GMT';
        return $this;
    }

    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
        return $this;
    }

    public function back($old = false)
    {
        if ($old == true) {
            foreach ((array) $this->request->all() as $key => $value) {
                $this->session->setOld($key, $value);
            }
        }
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function json($data)
    {
        $this->setContentType('json');
        if (!is_array($data)) {
            $data = (array) $data;
        }
        $this->setContent($data);
        return $this;
    }

}
