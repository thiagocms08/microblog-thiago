<?php

use Microblog\Helpers\Utils;
use Microblog\Services\CategoriaServico;
use Microblog\Services\NoticiaServico;

require_once __DIR__ . '/vendor/autoload.php';

$idCategoria = Utils::normalizarEntrada($_GET['id'] ?? null, 'inteiro');
Utils::verificarId($idCategoria);

$dadosCategoria = null;
$listaDeNoticias = [];
$mensagemErro = '';

try {
    $categoriaServico = new CategoriaServico();
    $dadosCategoria = $categoriaServico->buscarPorId($idCategoria);

    if ($dadosCategoria !== null) {
        $noticiaServico = new NoticiaServico();
        $listaDeNoticias = $noticiaServico->listarPorCategoria($idCategoria);
    }
} catch (Throwable $e) {
    Utils::registrarLog($e);
    $mensagemErro = 'Não foi possível carregar as notícias desta categoria.';
}

if ($dadosCategoria === null && $mensagemErro === '') {
    http_response_code(404);
}

require_once __DIR__ . '/includes/cabecalho.php';
?>

<div class="row my-1 mx-md-n1">
    <article class="col-12">
        <?php if ($mensagemErro !== ''): ?>
            <div class="alert alert-danger text-center">
                <?= Utils::escaparHtml($mensagemErro) ?>
            </div>
        <?php elseif ($dadosCategoria === null): ?>
            <div class="alert alert-warning text-center">
                Categoria não encontrada.
            </div>
        <?php else: ?>
            <h2 class="text-center">
                Notícias sobre
                <span class="badge bg-primary"><?= Utils::escaparHtml($dadosCategoria['nome']) ?></span>
            </h2>

            <?php if ($listaDeNoticias === []): ?>
                <p class="alert alert-info text-center">
                    Não há notícias cadastradas nesta categoria.
                </p>
            <?php else: ?>
                <div class="list-group my-3">
                    <?php foreach ($listaDeNoticias as $itemNoticia): ?>
                        <?php
                        $idNoticia = (int) $itemNoticia['id'];
                        $autor = trim((string) ($itemNoticia['autor'] ?? ''));
                        if ($autor === '') {
                            $autor = 'Equipe Microblog';
                        }
                        ?>
                        <a
                            href="noticia.php?id=<?= $idNoticia ?>"
                            class="list-group-item list-group-item-action">
                            <h3 class="fs-5">
                                <?= Utils::escaparHtml($itemNoticia['titulo']) ?>
                            </h3>
                            <p>
                                <time><?= Utils::escaparHtml(Utils::formataData((string) $itemNoticia['data'])) ?></time>
                                - <?= Utils::escaparHtml($autor) ?>
                            </p>
                            <p class="mb-0">
                                <?= Utils::escaparHtml($itemNoticia['resumo']) ?>
                            </p>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </article>
</div>

<?php
require_once __DIR__ . '/includes/rodape.php';
?>
