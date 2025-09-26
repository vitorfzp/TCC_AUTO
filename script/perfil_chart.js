// script/perfil_chart.js

document.addEventListener('DOMContentLoaded', () => {
    // A variável 'feedbacksData' é fornecida pelo PHP no final do arquivo HTML.
    if (typeof feedbacksData === 'undefined' || feedbacksData.length === 0) {
        // Se não houver dados, não tenta renderizar o gráfico.
        return;
    }

    const ctx = document.getElementById('profileFeedbackChart').getContext('2d');

    // 1. Agrega os dados: conta quantas vezes cada nota (1 a 5) aparece.
    const counts = [0, 0, 0, 0, 0]; // Posições 0-4 para notas 1-5
    feedbacksData.forEach(fb => {
        const nota = parseInt(fb.nota);
        if (nota >= 1 && nota <= 5) {
            counts[nota - 1]++;
        }
    });

    // 2. Configuração do Gráfico
    const chartData = {
        labels: ['1 Estrela', '2 Estrelas', '3 Estrelas', '4 Estrelas', '5 Estrelas'],
        datasets: [{
            label: 'Número de Avaliações',
            data: counts,
            backgroundColor: [
                'rgba(239, 68, 68, 0.7)',  // Vermelho
                'rgba(249, 115, 22, 0.7)', // Laranja
                'rgba(234, 179, 8, 0.7)',  // Amarelo
                'rgba(59, 130, 246, 0.7)', // Azul
                'rgba(34, 197, 94, 0.7)'   // Verde
            ],
            borderColor: [
                '#b91c1c', '#d97706', '#ca8a04', '#2563eb', '#15803d'
            ],
            borderWidth: 1,
            borderRadius: 5
        }]
    };

    // 3. Renderização do Gráfico
    new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: {
            indexAxis: 'y', // Deixa o gráfico na horizontal para melhor visualização
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1 // Garante que a contagem seja em números inteiros
                    }
                }
            },
            plugins: {
                legend: {
                    display: false // O título já é autoexplicativo
                },
                title: {
                    display: true,
                    text: 'Distribuição das Notas Recebidas',
                    font: {
                        size: 16
                    },
                    color: '#103352'
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
});