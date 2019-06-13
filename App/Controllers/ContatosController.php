<?php

namespace App\Controllers;

use Abbarbosa\LittleBoy\Framework\Controller;
use App\Models\Contato;

/**
 * ==============================================================================================================
 *
 * ContatosController: Classe para criar controles do aplicativo exemplo
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */
class ContatosController extends Controller
{

    /**
     * Lista os contatos
     */
    public function index()
    {
        $contatos = Contato::all();
        return view('grade', compact('contatos'));
    }

    /**
     * Mostrar formulario para criar um novo contato
     */
    public function create()
    {
        return view('form');
    }

    /**
     * Mostrar formulário para editar um contato
     */
    public function edit($id)
    {
        $contato = Contato::find($id);
        return view('form', compact('contato'));
    }

    /**
     * Salvar o contato submetido pelo formulário
     */
    public function store()
    {
        $data = request()->validate([
            'nome' => 'required|min:10',
            'email' => 'email',
        ]);

        if ($data->fails()) {
            return response()->back(true);
        }
        if (Contato::create(request()->all())) {
            return response()->redirect(route('contatos.index'));
        }
        return response()->back(true);
    }

    /**
     * Atualizar o contato conforme dados submetidos
     */
    public function update($id)
    {
        $data = request()->validate([
            'nome' => 'required|min:10',
            'email' => 'email',
        ]);

        if ($data->fails()) {
            return response()->back(true);
        }

        if ( Contato::where('id', $id)->update($data->all()) ) {
            return response()->redirect(route('contatos.index'));
        }
        return response()->back(true);

    }

    /**
     * Apagar um contato conforme o id informado
     */
    public function destroy($id)
    {
        if (Contato::destroy($id)) {
            return response()->redirect(route('contatos.index'));
        }
        return response()->back(true);
    }
}
