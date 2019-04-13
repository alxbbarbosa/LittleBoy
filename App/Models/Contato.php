<?php

namespace App\Models;

use Abbarbosa\LittleBoy\Framework\Model;
use Abbarbosa\LittleBoy\Framework\Conexao;

/**
 * ==============================================================================================================
 *
 * Contato: Classe para criar modelo do aplicativo exemplo
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */
class Contato extends Model
{
    protected $logTimestamp = FALSE;
    protected $table        = "contatos";

    public static function listarRecentes(int $dias = 10)
    {
        return self::all("created_at >= '".date('Y-m-d h:m:i', strtotime("-{$dias} days"))."'");
    }

    public static function numTotal()
    {
        return self::count();
    }
}