// Seleciona o input range e o span que mostra o valor
const priceRange = document.getElementById('priceRange');
const priceValue = document.getElementById('priceValue');

priceRange.addEventListener('input', () => {
  priceValue.textContent = `R$ ${priceRange.value}`;
});

// Exemplo de clique no botão "Encontrar"
const btnBuscar = document.getElementById('btnBuscar');
btnBuscar.addEventListener('click', () => {
  const city = document.getElementById('city').value;
  const checkin = document.getElementById('checkin').value;
  const checkout = document.getElementById('checkout').value;
  const guests = document.getElementById('guests').value;
  
  // Aqui você pode implementar a lógica para buscar hotéis
  // de acordo com a cidade, datas e número de hóspedes.
  // Por enquanto, apenas exibimos um alerta com os valores:
  alert(`Buscar hotéis em: ${city}\nCheck-in: ${checkin}\nCheck-out: ${checkout}\nHóspedes: ${guests}`);
});
