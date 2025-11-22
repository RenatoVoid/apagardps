// Aguarda o documento HTML ser completamente carregado
document.addEventListener('DOMContentLoaded', function() {
    // 1. Seleciona o botão e a div que será alterada
    const botao = document.getElementById('mudarCorBtn');
    const divJs = document.querySelector('.js-info');
    const mensagem = document.getElementById('mensagemJS');

    // Array de cores para alternar
    const cores = ['#ffe0b2', '#ffcdd2', '#c8e6c9', '#bbdefb'];
    let indiceCorAtual = 0;

    // 2. Adiciona um "ouvinte de evento" ao botão (ação de clique)
    botao.addEventListener('click', function() {
        // Lógica: Alterna entre as cores do array
        indiceCorAtual = (indiceCorAtual + 1) % cores.length;
        const novaCor = cores[indiceCorAtual];

        // 3. Altera o estilo da div com a nova cor
        divJs.style.backgroundColor = novaCor;
        
        // 4. Altera a mensagem para confirmar a ação JS
        mensagem.textContent = 'A cor de fundo foi alterada pelo JavaScript!';
    });
});