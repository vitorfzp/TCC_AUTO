// Inicializa o mapa único
var map = L.map('map').setView([-22.435, -46.965], 13);

// Adiciona a camada do OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Dados dos marcadores
const markersData = [
  {
    coords: [-22.43101092556756, -46.959985676139645],
    popup: "<b>Autonowe</b><br>Mogi Mirim-SP."
  },
];

// Adiciona cada marcador ao mapa
markersData.forEach(item => {
  const marker = L.marker(item.coords).addTo(map);
  marker.bindPopup(item.popup).openPopup();
});
