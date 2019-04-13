<!--
/**
 * ==============================================================================================================
 *
 * View de formulário: gera a formulário para aplicativo exemplo
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */ -->
<div class="container">

    <?php 
    if(session()->anyError()) {
        echo "<ul>";
        foreach(session()->errors() as $error) {

            echo "<li>{$error}</li>";

        } 
        echo "</ul>";
    }
    ?>

    <form action="<?= isset($contato->id) ? route('contatos.update', $contato->id) : route('contatos.store') ?>" method="post" >

        <div class="card" style="top:40px">
            <div class="card-header">
                <span class="card-title">Contatos</span>
            </div>
            <div class="card-body">
            </div>
            <div class="form-group form-row">
                <label class="col-sm-2 col-form-label text-right">Nome:</label>
                <input type="text" class="form-control col-sm-8" name="nome" id="nome" value="<?php
                echo isset($contato->nome) ? $contato->nome : old('nome');
                ?>" />
            </div>
            <div class="form-group form-row">
                <label class="col-sm-2 col-form-label text-right">Telefone:</label>
                <input type="text" class="form-control col-sm-8" name="telefone" id="telefone" value="<?php
                echo isset($contato->telefone) ? $contato->telefone : old('telefone');
                ?>" />
            </div>
            <div class="form-group form-row">
                <label class="col-sm-2 col-form-label text-right">Email:</label>
                <input type="text" class="form-control col-sm-8" name="email" id="email" value="<?php
                echo isset($contato->email) ? $contato->email : old('email');
                ?>" />
            </div>
            <div class="card-footer">
                <input type="hidden" name="id" id="id" value="<?php echo isset($contato->id) ? $contato->id : null; ?>" />
                <button class="btn btn-success" type="submit">Salvar</button>
                <button class="btn btn-secondary" type="reset">Limpar</button>
                <a class="btn btn-danger" href="<?= route('contatos.index') ?>">Cancelar</a>
            </div>
        </div>
    </form>
</div>