<!--
/**
 * ==============================================================================================================
 *
 * View de grade: gera a grade para aplicativo exemplo
 *
 * ----------------------------------------------------
 *
 * @author Alexandre Bezerra Barbosa <alxbbarbosa@yahoo.com.br>
 * @copyright (c) 2018, Alexandre Bezerra Barbosa
 * @version 1.00
 * ==============================================================================================================
 */ -->
<h1>Contatos</h1>
<hr>
<div class="container">
    <table class="table table-bordered table-striped" style="top:40px;">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th>Email</th>
                <th><a href="<?= route('contatos.create') ?>" class="btn btn-success btn-sm">Novo</a></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($contatos) {
                foreach ($contatos as $contato) {
                    ?>
                    <tr>
                        <td><?php echo $contato->nome; ?></td>
                        <td><?php echo $contato->telefone; ?></td>
                        <td><?php echo $contato->email; ?></td>
                        <td>
                            <a href="<?= route('contatos.edit', $contato->id) ?>" class="btn btn-primary btn-sm">Editar</a>
                            <a href="<?= route('contatos.destroy', $contato->id) ?>" class="btn btn-danger btn-sm">Excluir</a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="5">Nenhum registro encontrado</td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>