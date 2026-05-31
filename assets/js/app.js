/* =====================================================================
   Plataforma de Delivery — JavaScript do cliente (AJAX + mapas)
   ===================================================================== */

/** Pedido AJAX (POST) com token CSRF do CodeIgniter. */
async function postAjax(url, dados) {
    const corpo = new URLSearchParams(dados);
    if (window.CSRF) corpo.append(window.CSRF.nome, window.CSRF.hash);
    const resp = await fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: corpo,
    });
    const json = await resp.json();
    // Actualiza o token CSRF para o próximo pedido (regenera a cada pedido).
    if (json._csrf && window.CSRF) window.CSRF.hash = json._csrf;
    return json;
}

/** Captura a localização GPS do utilizador e preenche os campos lat/lng. */
function capturarLocalizacao(idLat, idLng, callback) {
    if (!navigator.geolocation) {
        alert('O seu navegador não suporta geolocalização.');
        return;
    }
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude.toFixed(7);
            const lng = pos.coords.longitude.toFixed(7);
            if (idLat) document.getElementById(idLat).value = lat;
            if (idLng) document.getElementById(idLng).value = lng;
            if (callback) callback(lat, lng);
        },
        () => alert('Não foi possível obter a sua localização.'),
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

/** Inicializa um mapa Leaflet/OpenStreetMap com um marcador opcional. */
function iniciarMapa(idElemento, lat, lng, arrastavel = false, aoMover = null) {
    // Centro por omissão: Maputo, Moçambique.
    lat = parseFloat(lat) || -25.9692;
    lng = parseFloat(lng) || 32.5732;

    const mapa = L.map(idElemento).setView([lat, lng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19,
    }).addTo(mapa);

    const marcador = L.marker([lat, lng], { draggable: arrastavel }).addTo(mapa);
    if (arrastavel && aoMover) {
        marcador.on('dragend', (e) => {
            const p = e.target.getLatLng();
            aoMover(p.lat.toFixed(7), p.lng.toFixed(7));
        });
    }
    setTimeout(() => mapa.invalidateSize(), 200);
    return { mapa, marcador };
}

/** Traça uma linha entre dois pontos (loja → cliente) e ajusta o zoom. */
function tracarRota(idElemento, origem, destino) {
    const mapa = L.map(idElemento);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap', maxZoom: 19,
    }).addTo(mapa);

    const a = [parseFloat(origem.lat), parseFloat(origem.lng)];
    const b = [parseFloat(destino.lat), parseFloat(destino.lng)];
    L.marker(a).addTo(mapa).bindPopup(origem.rotulo || 'Loja');
    L.marker(b).addTo(mapa).bindPopup(destino.rotulo || 'Entrega');
    L.polyline([a, b], { color: '#e63946', weight: 4, dashArray: '8 6' }).addTo(mapa);
    mapa.fitBounds(L.latLngBounds([a, b]).pad(0.3));
    setTimeout(() => mapa.invalidateSize(), 200);
}
