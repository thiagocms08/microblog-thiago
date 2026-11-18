<?php

use Microblog\Helpers\Utils;
use Microblog\Services\NoticiaServico;

require_once __DIR__ . '/vendor/autoload.php';

header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo '<p class="alert alert-danger">Método de requisição inválido.</p>';
    exit;
}

$termo = Utils::normalizarEntrada($_POST['busca'] ?? '');

if ($termo === '') {
    echo '<p class="text-muted mb-0">Digite o título de uma notícia para pesquisar.</p>';
    exit;
}

if (mb_strlen($termo) > 100) {
    http_response_code(400);
    echo '<p class="alert alert-warning mb-0">A pesquisa deve ter no máximo 100 caracteres.</p>';
    exit;
}

try {
    $noticiaServico = new NoticiaServico();
    $listaDeNoticias = $noticiaServico->buscarPorTitulo($termo);
} catch (Throwable $e) {
    Utils::registrarLog($e);
    http_response_code(500);
    echo '<p class="alert alert-danger mb-0">Não foi possível pesquisar as notícias.</p>';
    exit;
}
?>

<h2 class="fs-5">
    Resultados:
    <span class="badge rounded-pill text-bg-success"><?= count($listaDeNoticias) ?></span>
</h2>

<?php if ($listaDeNoticias === []): ?>
    <p class="text-muted mb-0">Nenhuma notícia encontrada.</p>
<?php else: ?>
    <div class="list-group">
        <?php foreach ($listaDeNoticias as $itemNoticia): ?>
            <?php $idNoticia = (int) $itemNoticia['id']; ?>
            <a
                class="list-group-item list-group-item-action"
                href="noticia.php?id=<?= $idNoticia ?>">
                <?= Utils::escaparHtml($itemNoticia['titulo']) ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
