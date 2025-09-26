// script/estatistica.js

// Variáveis globais para armazenar os dados e o gráfico
let allFeedbacks = [];
let feedbackChart = null;

// Função para atualizar os cards de KPI
function updateKpis(feedbacks) {
    const totalAvaliacoesEl = document.getElementById('kpi-total-avaliacoes');
    const mediaGeralEl = document.getElementById('kpi-media-geral');
    const prestadorDestaqueEl = document.getElementById('kpi-prestador-destaque');

    // Se não houver feedbacks para exibir, mostra traços
    if (feedbacks.length === 0) {
        totalAvaliacoesEl.textContent = '0';
        mediaGeralEl.textContent = 'N/A';
        prestadorDestaqueEl.textContent = 'N/A';
        return;
    }

    // 1. Calcula o Total de Avaliações
    totalAvaliacoesEl.textContent = feedbacks.length;

    // 2. Calcula a Média Geral
    const totalNotas = feedbacks.reduce((sum, fb) => sum + parseInt(fb.nota), 0);
    const mediaGeral = (totalNotas / feedbacks.length).toFixed(1);
    mediaGeralEl.textContent = `${mediaGeral} ⭐`;

    // 3. Encontra o Prestador em Destaque
    const prestadorStats = {};
    feedbacks.forEach(fb => {
        if (!prestadorStats[fb.nome_prestador]) {
            prestadorStats[fb.nome_prestador] = { total: 0, count: 0 };
        }
        prestadorStats[fb.nome_prestador].total += parseInt(fb.nota);
        prestadorStats[fb.nome_prestador].count++;
    });

    let melhorPrestador = 'N/A';
    let maiorMedia = 0;
    for (const nome in prestadorStats) {
        const media = prestadorStats[nome].total / prestadorStats[nome].count;
        if (media > maiorMedia) {
            maiorMedia = media;
            melhorPrestador = nome;
        }
    }
    prestadorDestaqueEl.textContent = melhorPrestador;
}

// Função principal para buscar dados e inicializar a página
async function initializeStatistics() {
    try {
        const response = await fetch('php/get_feedback.php');
        if (!response.ok) {
            throw new Error('Falha ao buscar os dados do servidor.');
        }
        allFeedbacks = await response.json();

        populateFilters();
        setupEventListeners();
        renderChart(); // A renderChart agora também chama a updateKpis

    } catch (error) {
        console.error('Erro ao inicializar estatísticas:', error);
        const chartContainer = document.getElementById('feedbackChart').getContext('2d');
        chartContainer.canvas.parentElement.innerHTML = '<p style="color: red;">Não foi possível carregar os dados para o gráfico.</p>';
    }
}

// Popula os filtros com dados únicos do servidor
function populateFilters() {
    const prestadorFilter = document.getElementById('prestadorFilter');
    const profissaoFilter = document.getElementById('profissaoFilter');

    const uniquePrestadores = [...new Set(allFeedbacks.map(fb => fb.nome_prestador))];
    const uniqueProfissoes = [...new Set(allFeedbacks.map(fb => fb.profissao))];

    uniquePrestadores.sort().forEach(prestador => {
        const option = document.createElement('option');
        option.value = prestador;
        option.textContent = prestador;
        prestadorFilter.appendChild(option);
    });

    uniqueProfissoes.sort().forEach(profissao => {
        const option = document.createElement('option');
        option.value = profissao;
        option.textContent = profissao;
        profissaoFilter.appendChild(option);
    });
}

// Configura os listeners para os filtros
function setupEventListeners() {
    document.getElementById('prestadorFilter').addEventListener('change', renderChart);
    document.getElementById('profissaoFilter').addEventListener('change', renderChart);
}

// Renderiza ou atualiza o gráfico e os KPIs
function renderChart() {
    const ctx = document.getElementById('feedbackChart').getContext('2d');
    const selectedPrestador = document.getElementById('prestadorFilter').value;
    const selectedProfissao = document.getElementById('profissaoFilter').value;

    const filteredFeedbacks = allFeedbacks.filter(fb => {
        const prestadorMatch = (selectedPrestador === 'all' || fb.nome_prestador === selectedPrestador);
        const profissaoMatch = (selectedProfissao === 'all' || fb.profissao === selectedProfissao);
        return prestadorMatch && profissaoMatch;
    });

    // ATUALIZADO: Atualiza os KPIs com os dados filtrados
    updateKpis(filteredFeedbacks);

    // Agrega dados para o gráfico
    const counts = [0, 0, 0, 0, 0];
    filteredFeedbacks.forEach(fb => {
        const nota = parseInt(fb.nota);
        if (nota >= 1 && nota <= 5) {
            counts[nota - 1]++;
        }
    });

    const chartData = {
        labels: ['1 ⭐', '2 ⭐⭐', '3 ⭐⭐⭐', '4 ⭐⭐⭐⭐', '5 ⭐⭐⭐⭐⭐'],
        datasets: [{
            label: 'Quantidade de Feedbacks',
            data: counts,
            backgroundColor: [
                'rgba(239, 68, 68, 0.6)', 'rgba(249, 115, 22, 0.6)',
                'rgba(234, 179, 8, 0.6)', 'rgba(59, 130, 246, 0.6)',
                'rgba(34, 197, 94, 0.6)'
            ],
            borderColor: [
                '#b91c1c', '#d97706', '#ca8a04', '#2563eb', '#15803d'
            ],
            borderWidth: 1.5,
            borderRadius: 5
        }]
    };

    if (feedbackChart) {
        feedbackChart.destroy();
    }
    feedbackChart = new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: {
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, title: { display: true, text: 'Quantidade de Avaliações' } }
            },
            plugins: { legend: { display: false } },
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

// Inicia o processo quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', initializeStatistics);