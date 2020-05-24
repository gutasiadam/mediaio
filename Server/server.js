// Mediaio_websocket server
const express = require('express');
const WebSocket = require('ws');
const SocketServer = require('ws').Server;

const server = express().listen(3000); //setting up a port number, to run the websocket on.

const wss = new SocketServer({ server });
console.log("MediaIO Websocket Comm estabilished.")

wss.on('connection', (ws) => {
    console.log('[Server] A client was connected.')

    ws.on('close', () => console.log('[Server] Client disconnected.'));

    ws.on('message', (message) => {
        
        console.log('[Server] Recieved message: %s', message);
        // broadcast msg to everyone else
        wss.clients.forEach(function each(client){
            if((client !== ws)){ // && client.readystate === WebSocket.OPEN
                client.send(message);
            }
        })
    });
});
/*
wss.broadcast = function broadcast(msg) {
    console.log(msg);
    wss.clients.forEach(function each(client) {
        client.send(msg);
     });
 };

wss.on('connection', function connection(ws) {
    ws.on('message', function(message) {
       wss.broadcast(message);
    });

});*/