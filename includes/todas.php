<?php

use Microblog\Helpers\Utils;
use Microblog\Services\NoticiaServico;

require_once __DIR__ . '/../vendor/autoload.php';

$listaDeDestaques = [];
$mensagemErro = '';

try {
    $noticiaServico = new NoticiaServico();
    $listaDeDestaques = $noticiaServico->listarDestaques();
} catch (Throwable $e) {
    Utils::registrarLog($e);
    $mensagemErro = 'Não foi possível carregar as notícias em destaque.';
}
?>

<section class="my-4" aria-labelledby="titulo-destaques">
    <h2 id="titulo-destaques" class="text-center mb-4">Notícias em destaque</h2>

    <?php if ($mensagemErro !== ''): ?>
        <div class="alert alert-danger text-center">
            <?= Utils::escaparHtml($mensagemErro) ?>
        </div>
    <?php elseif ($listaDeDestaques === []): ?>
        <p class="alert alert-info text-center">Não há notícias em destaque no momento.</p>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($listaDeDestaques as $itemNoticia): ?>
                <?php
                $idNoticia = (int) $itemNoticia['id'];
                $imagem = rawurlencode((string) $itemNoticia['imagem']);
                ?>
                <div class="col-md-6">
                    <article class="card shadow-sm h-100">
                        <a href="noticia.php?id=<?= $idNoticia ?>" class="card-link d-block h-100">
                            <img
                                src="images/<?= Utils::escaparHtml($imagem) ?>"
                                class="card-img-top"
                                alt="<?= Utils::escaparHtml($itemNoticia['titulo']) ?>">
                            <div class="card-body">
                                <h3 class="fs-4 card-title">
                                    <?= Utils::escaparHtml($itemNoticia['titulo']) ?>
                                </h3>
                                <p class="card-text">
                                    <?= Utils::escaparHtml($itemNoticia['resumo']) ?>
                                </p>
                            </div>
                        </a>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
