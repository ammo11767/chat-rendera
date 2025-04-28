const WebSocket = require('ws');
const wss = new WebSocket.Server({ port: 3000 });
const clients = {};

wss.on('connection', ws => {
  ws.on('message', message => {
    const data = JSON.parse(message);
    clients[data.from] = ws;
    if (clients[data.to]) {
      clients[data.to].send(message);
    }
  });
});
console.log("🔌 WebSocket corriendo en puerto 3000");