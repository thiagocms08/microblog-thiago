<?php

use Microblog\Helpers\Utils;
use Microblog\Services\NoticiaServico;

require_once __DIR__ . '/vendor/autoload.php';

$id = Utils::normalizarEntrada($_GET['id'] ?? null, 'inteiro');
Utils::verificarId($id);

$dadosNoticia = null;
$mensagemErro = '';

try {
    $noticiaServico = new NoticiaServico();
    $dadosNoticia = $noticiaServico->buscarPorIdPublico($id);
} catch (Throwable $e) {
    Utils::registrarLog($e);
    $mensagemErro = 'Não foi possível carregar a notícia. Tente novamente.';
}

if ($dadosNoticia === null && $mensagemErro === '') {
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
        <?php elseif ($dadosNoticia === null): ?>
            <div class="alert alert-warning text-center">
                Notícia não encontrada.
            </div>
        <?php else: ?>
            <?php
            $autor = trim((string) ($dadosNoticia['autor'] ?? ''));
            if ($autor === '') {
                $autor = 'Equipe Microblog';
            }
            $imagem = rawurlencode((string) $dadosNoticia['imagem']);
            ?>
            <h2><?= Utils::escaparHtml($dadosNoticia['titulo']) ?></h2>
            <p class="font-weight-light">
                <time><?= Utils::escaparHtml(Utils::formataData((string) $dadosNoticia['data'])) ?></time>
                - <span><?= Utils::escaparHtml($autor) ?></span>
            </p>
            <img
                src="images/<?= Utils::escaparHtml($imagem) ?>"
                alt="<?= Utils::escaparHtml($dadosNoticia['titulo']) ?>"
                class="float-start pe-2 img-fluid">
            <p class="ajusta-texto"><?= Utils::escaparHtml($dadosNoticia['texto']) ?></p>
        <?php endif; ?>
    </article>
</div>

<?php
if ($dadosNoticia !== null && $mensagemErro === '') {
    $idNoticiaAberta = (int) $dadosNoticia['id'];
    require __DIR__ . '/includes/todas-noticias.php';
}

require_once __DIR__ . '/includes/rodape.php';
?>
