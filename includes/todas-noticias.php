<?php

use Microblog\Helpers\Utils;
use Microblog\Services\NoticiaServico;

require_once __DIR__ . '/../vendor/autoload.php';

$listaDeNoticias = [];
$mensagemErro = '';

try {
    $noticiaServico = new NoticiaServico();
    $listaDeNoticias = $noticiaServico->listarOutrasPublicas($idNoticiaAberta);
} catch (Throwable $e) {
    Utils::registrarLog($e);
    $mensagemErro = 'Não foi possível carregar as outras notícias.';
}
?>

<section class="my-5" aria-labelledby="titulo-todas-noticias">
    <h3 id="titulo-todas-noticias" class="text-center mb-4">Todas as notícias</h3>

    <?php if ($mensagemErro !== ''): ?>
        <div class="alert alert-danger text-center">
            <?= Utils::escaparHtml($mensagemErro) ?>
        </div>
    <?php elseif ($listaDeNoticias === []): ?>
        <p class="alert alert-info text-center">Não há outras notícias no momento.</p>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($listaDeNoticias as $itemNoticia): ?>
                <?php $idNoticia = (int) $itemNoticia['id']; ?>
                <a
                    class="list-group-item list-group-item-action"
                    href="noticia.php?id=<?= $idNoticia ?>">
                    <div class="d-flex justify-content-between gap-3">
                        <h3 class="fs-5 mb-1">
                            <?= Utils::escaparHtml($itemNoticia['titulo']) ?>
                        </h3>
                        <time class="text-nowrap">
                            <?= Utils::escaparHtml(Utils::formataData((string) $itemNoticia['data'])) ?>
                        </time>
                    </div>
                    <p class="mb-0">
                        <?= Utils::escaparHtml($itemNoticia['resumo']) ?>
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
