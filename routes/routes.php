<?php
/**
 * ==============================================================================================================
 *
 * Rotas: Arquivo para declaração das rotas
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */ 
$route->get(['route' => 'contatos', 'as' => 'contatos.index'], "ContatosController@index");
$route->get(['route' => 'contatos/create', 'as' => 'contatos.create'], "ContatosController@create");
$route->get(['route' => 'contatos/{id}/edit', 'as' => 'contatos.edit'], "ContatosController@edit");
$route->post(['route' => 'contatos/store', 'as' => 'contatos.store'], "ContatosController@store");
$route->post(['route' => 'contatos/{id}/update', 'as' => 'contatos.update'], "ContatosController@update");
$route->get(['route' => 'contatos/{id}/destroy', 'as' => 'contatos.destroy'], "ContatosController@destroy");



