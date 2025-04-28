const WebSocket = require('ws');
const port = process.env.PORT || 3000;
const wss  = new WebSocket.Server({ port });

const clients = {};

wss.on('connection', ws => {
  ws.on('message', msg => {
    const data = JSON.parse(msg);
    clients[data.from] = ws;
    if (clients[data.to]) {
      clients[data.to].send(msg);
    }
  });
});

console.log('🔌 WebSocket server listening on', port);
