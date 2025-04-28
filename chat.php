<?php
$yo = $_GET['yo'] ?? '';
$peer = $_GET['peer'] ?? '';
if (!$yo || !$peer) {
  echo "<h3>Faltan parámetros ?yo=usuario&peer=destino</h3>";
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Chat + Llamada</title>
  <style>
    #mensajes { border: 1px solid #ccc; height: 200px; overflow-y: auto; padding: 5px; }
    .estado { font-weight: bold; margin-top: 10px; }
    audio { display: block; margin-top: 10px; }
    button { margin: 5px; }

    @keyframes parpadeo {
      0% { background-color: #4CAF50; }
      50% { background-color: #f1c40f; }
      100% { background-color: #4CAF50; }
    }
    .parpadea {
      animation: parpadeo 1s infinite;
    }
  </style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
</div>

<div id="mapaTab">
  <div id="panel">
    <div id="lista"><strong>Usuarios</strong><ul id="usuariosUL"></ul></div>
    <div id="map"></div>
  </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
#tabs button{margin-right:8px;}
#chatTab,#panel{display:flex;gap:12px;}#lista{width:180px;border:1px solid #ccc;padding:8px;overflow-y:auto;}#map{flex:1;height:260px;}
#map{width:100%;height:260px;}
</style>

</head>
<body>

<div id="tabs">
  <button onclick="mostrarTab('chatTab')">💬 Chat</button>
  <button onclick="mostrarTab('mapaTab')">🗺️ Mapa</button>
</div>

<div id="chatTab">

<h2>Chat entre <?= htmlspecialchars($yo) ?> y <?= htmlspecialchars($peer) ?></h2>

<div id="mensajes"></div>
<div>
  <input id="mensaje" placeholder="Escribe un mensaje...">
  <button onclick="enviarMensaje()">📨 Enviar</button>
</div>

<h3>Llamada de Audio</h3>
<div class="estado" id="estadoLlamada">🟡 Sin llamada</div>
<button onclick="iniciarLlamada()">📞 Llamar</button>
<button onclick="aceptarLlamada()" id="btnAceptar">✅ Aceptar</button>
<button onclick="colgarLlamada()" style="display:none;" id="btnColgar">❌ Colgar</button>
<audio id="remoteAudio" autoplay></audio>

<script>
const yo = "<?= $yo ?>";
const peer = "<?= $peer ?>";

// Mostrar botón de aceptar solo al recibir oferta
function mostrarBotonAceptar() {
  const btn = document.getElementById("btnAceptar");
  
  btn.classList.add("parpadea");
}

// cliente.js debe llamar esto cuando llegue una oferta:
function onOfferRecibida() {
  mostrarBotonAceptar();
}
</script>
<script src="cliente.js"></script>




<script>
window.mostrarTab = function(id){
  document.getElementById('chatTab').style.display = (id==='chatTab')?'block':'none';
  document.getElementById('mapaTab').style.display = (id==='mapaTab')?'block':'none';
  if(id==='mapaTab' && window.map) map.invalidateSize();
  refrescarTodo();
};
mostrarTab('chatTab');

const map = L.map('map').setView([0,0],2);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(map);

const marcadores = new Map();
const ul = document.getElementById('usuariosUL');

function refrescarTodo(){
  fetch('usuarios_all.php')
    .then(r => r.json())
    .then(lista => {
      // lista de nombres
      ul.innerHTML = '';
      lista.forEach(u=>{
        const li=document.createElement('li');
        li.textContent = u.nombre + ((u.lat===null || u.lng===null)?' (sin ubicación)':'');
        ul.appendChild(li);
        if(u.lat===null || u.lng===null) return;
        if(!marcadores.has(u.nombre)){
          marcadores.set(u.nombre, L.marker([u.lat,u.lng]).addTo(map).bindPopup(u.nombre));
        }else{
          marcadores.get(u.nombre).setLatLng([u.lat,u.lng]);
        }
      });
    })
    .catch(console.error);
}
setInterval(refrescarTodo, 2000);
refrescarTodo();

// envío de mi ubicación
if(navigator.geolocation){
  navigator.geolocation.watchPosition(pos=>{
    fetch('update_location.php',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:new URLSearchParams({
        nombre:yo,
        lat:pos.coords.latitude,
        lng:pos.coords.longitude
      })
    });
  },console.error,{enableHighAccuracy:true});
}
</script>

</body>
</html>