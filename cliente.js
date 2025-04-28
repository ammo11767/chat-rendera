const socket = new WebSocket('ws://localhost:3000');
const peerConnection = new RTCPeerConnection();
const remoteAudio    = document.getElementById("remoteAudio");
const estadoLlamada  = document.getElementById("estadoLlamada");
const mensajesDiv    = document.getElementById("mensajes");
let   streamLocal;

// audio
navigator.mediaDevices.getUserMedia({audio:true}).then(stream=>{
  streamLocal = stream;
  stream.getTracks().forEach(t=>peerConnection.addTrack(t, stream));
});
peerConnection.ontrack = e => { remoteAudio.srcObject = e.streams[0]; };
peerConnection.onicecandidate = e => {
  if(e.candidate){
    socket.send(JSON.stringify({type:'ice', from:yo, to:peer, candidate:e.candidate}));
  }
};

// chat texto
async function cargarMensajes(){
  const r = await fetch(`mensajes.php?yo=${encodeURIComponent(yo)}&peer=${encodeURIComponent(peer)}`);
  const data = await r.json();
  mensajesDiv.innerHTML='';
  data.forEach(msg=>{
    const p=document.createElement('p');
    p.innerHTML = `<strong>${escapeHTML(msg.emisor)}:</strong> ${escapeHTML(msg.mensaje)}`;
    mensajesDiv.appendChild(p);
  });
  mensajesDiv.scrollTop = mensajesDiv.scrollHeight;
}
setInterval(cargarMensajes, 1000);
cargarMensajes();

function escapeHTML(str){
  return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

async function enviarMensaje(){
  const input=document.getElementById('mensaje');
  const texto=input.value.trim();
  if(!texto) return;
  await fetch('enviar.php',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:new URLSearchParams({de:yo, para:peer, mensaje:texto})
  });
  input.value='';
  cargarMensajes();
}

// llamada
let llamadaPendiente=null;
socket.onmessage = async e=>{
  const data = JSON.parse(await e.data.text());
  if(data.type==='offer' && data.to===yo){
    llamadaPendiente=data;
    estadoLlamada.textContent='📞 Llamada entrante de '+data.from;
    if(typeof onOfferRecibida==='function') onOfferRecibida();
  }
  if(data.type==='answer' && data.to===yo){
    await peerConnection.setRemoteDescription(new RTCSessionDescription(data.answer));
    estadoLlamada.textContent='✅ En llamada con '+peer;
    document.getElementById('btnColgar').style.display='inline-block';
  }
  if(data.type==='ice' && data.to===yo && data.candidate){
    await peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate));
  }
};

function iniciarLlamada(){
  estadoLlamada.textContent='📤 Enviando llamada a '+peer;
  peerConnection.createOffer()
    .then(o=>peerConnection.setLocalDescription(o))
    .then(()=>{
      socket.send(JSON.stringify({type:'offer', from:yo, to:peer, offer:peerConnection.localDescription}));
      document.getElementById('btnColgar').style.display='inline-block';
    });
}

function aceptarLlamada(){
  if(!llamadaPendiente) return;
  estadoLlamada.textContent='🔄 Estableciendo llamada...';
  const data=llamadaPendiente;
  peerConnection.setRemoteDescription(new RTCSessionDescription(data.offer))
    .then(()=>peerConnection.createAnswer())
    .then(ans=>peerConnection.setLocalDescription(ans))
    .then(()=>{
      socket.send(JSON.stringify({type:'answer', from:yo, to:peer, answer:peerConnection.localDescription}));
      estadoLlamada.textContent='✅ En llamada con '+peer;
      document.getElementById('btnColgar').style.display='inline-block';
      document.getElementById('btnAceptar').classList.remove('parpadea');
      llamadaPendiente=null;
    });
}

function colgarLlamada(){
  streamLocal?.getTracks().forEach(t=>t.stop());
  peerConnection.close();
  location.reload();
}
