<?php

namespace Abbarbosa\LittleBoy\Framework;

/**
 * ==============================================================================================================
 *
 * WebCache: Classe para acelerar o carregamento de páginas
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */
class WebCache
{
    public static $enabled;
    protected $start;
    protected $end;
    protected $cache_file_name;
    protected $cache_folder;
    protected $cache_time;
    protected $last_modified;
    protected $total;

    public function __construct(int $seconds = 3)
    {
        if (self::$enabled == true) {
            $this->cache_time   = $seconds; // Time in seconds to keep a page cached  3600
            $this->cache_folder = __DIR__.'/../../temp/'; // Folder to store cached files (no trailing slash)  
        }
    }

    public function caching()
    {
        if (self::$enabled == true) {
            if ((time() - $this->wasCached()) < $this->cache_time) {

                $this->readCacheFile();
                $this->end   = microtime(true);
                $this->total = number_format($this->end - $this->start, 4);
                exit;
            } else {
                ob_start();
            }
        }
    }

    public function createCacheFile()
    {
        if (self::$enabled == true) {
            /** do your work here echoing data to the screen */
            $storedData = ob_get_contents();
            ob_end_flush();

            // create the cachefile for the data.
            //pega o nome do arquivo de cache que criamos em topo_cache se ele não existe ou se já passou sua vida útil
            $cached = fopen($this->cache_file_name, 'w');
            fwrite($cached, $storedData); //escreva todo o conteúdo do arquivo atual
            fclose($cached);
        }
    }

    /**
     * Check to see if this file has already been cached  
     * If it has get and store the file creation time
     * @return type
     */
    protected function wasCached()
    {
        $this->start           = microtime(true);
        // Location to lookup or store cached file
        $this->cache_file_name = $this->cache_folder.md5(",a1b2c3d4e5f6g7h8i9ABB!");
        return file_exists($this->cache_file_name) ? filemtime($this->cache_file_name) : 0;
    }

    protected function readCacheFile()
    {
        include($this->cache_file_name);
    }

    public static function enable(bool $option = false)
    {
        self::$enabled = $option;
    }
}