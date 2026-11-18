'use strict';

const formularioBusca = document.querySelector('#form-busca');
const campoBusca = document.querySelector('#campo-busca');
const painelResultados = document.querySelector('#resultados');

let temporizadorBusca = null;
let controladorBusca = null;

function limparResultados() {
    if (controladorBusca !== null) {
        controladorBusca.abort();
        controladorBusca = null;
    }

    painelResultados.innerHTML = '';
    painelResultados.hidden = true;
    painelResultados.setAttribute('aria-busy', 'false');
}

async function pesquisarNoticias() {
    const termo = campoBusca.value.trim();

    if (termo === '') {
        limparResultados();
        return;
    }

    if (controladorBusca !== null) {
        controladorBusca.abort();
    }

    const controladorAtual = new AbortController();
    controladorBusca = controladorAtual;
    painelResultados.hidden = false;
    painelResultados.setAttribute('aria-busy', 'true');
    painelResultados.textContent = 'Pesquisando...';

    try {
        const resposta = await fetch(formularioBusca.action, {
            method: 'POST',
            headers: {
                'Accept': 'text/html',
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
            },
            body: new URLSearchParams({ busca: termo }),
            signal: controladorAtual.signal
        });

        const conteudo = await resposta.text();

        if (!resposta.ok) {
            throw new Error('A pesquisa não pôde ser concluída.');
        }

        if (controladorBusca === controladorAtual) {
            painelResultados.innerHTML = conteudo;
        }
    } catch (erro) {
        if (erro.name !== 'AbortError' && controladorBusca === controladorAtual) {
            painelResultados.textContent = 'Não foi possível pesquisar as notícias.';
        }
    } finally {
        if (controladorBusca === controladorAtual) {
            controladorBusca = null;
            painelResultados.setAttribute('aria-busy', 'false');
        }
    }
}

formularioBusca.addEventListener('submit', function(evento) {
    evento.preventDefault();
    window.clearTimeout(temporizadorBusca);
    pesquisarNoticias();
});

campoBusca.addEventListener('input', function() {
    window.clearTimeout(temporizadorBusca);

    if (controladorBusca !== null) {
        controladorBusca.abort();
        controladorBusca = null;
    }

    if (campoBusca.value.trim() === '') {
        limparResultados();
        return;
    }

    painelResultados.hidden = false;
    painelResultados.setAttribute('aria-busy', 'true');
    painelResultados.textContent = 'Pesquisando...';
    temporizadorBusca = window.setTimeout(pesquisarNoticias, 300);
});

campoBusca.addEventListener('keydown', function(evento) {
    if (evento.key === 'Escape') {
        campoBusca.value = '';
        window.clearTimeout(temporizadorBusca);
        limparResultados();
    }
});

