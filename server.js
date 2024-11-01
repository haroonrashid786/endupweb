const express = require("express");

const domain = "localhost";

const app = express();
const fs = require("fs");


// const server = require('https').createServer({
//     key: fs.readFileSync('/etc/letsencrypt/live/enduptech.tijarah.ae/privkey.pem'),
//     cert: fs.readFileSync('/etc/letsencrypt/live/enduptech.tijarah.ae/fullchain.pem')
// }, app);
const server = require('http').createServer(app);


const io = require("socket.io")(server, {
    cors: { origin: "*:*" },
});
require("dotenv").config();

var redisPort = process.env.REDIS_PORT;

var redisHost = process.env.REDIS_HOST;
console.log(redisHost);
var ioRedis = require("ioredis");
var redis = new ioRedis(redisPort, redisHost);

redis.subscribe(
    "user-channel",
    "liveLocation",
);

redis.on("message", function (channel, message) {


    message = JSON.parse(message);

    io.emit(channel + ":" + message.event, message.data);

});

io.on('connection', (socket) => {
    socket.on('liveLocation:rider1', (msg) => {
        io.emit('liveLocation:rider1', msg);
    });
});

var broadcastPort = process.env.BROADCAST_PORT;


server.listen(6003,() => {
    console.log("Server is running");
});




// var app = require('express')();
// const fs = require("fs");
// var server = require('https').Server({
//     key: fs.readFileSync('/etc/letsencrypt/live/enduptech.tijarah.ae/privkey.pem'),
//     cert: fs.readFileSync('/etc/letsencrypt/live/enduptech.tijarah.ae/fullchain.pem')
//  },app);

// // var server = require('http').Server(app);
// var io = require('socket.io')(server);
// var redis = require('redis');
// server.listen(6003, () => {
//     console.log("Server is running");
// });
// users = {};

// var redisClient = redis.createClient();

// redisClient.subscribe("user-channel", "liveLocation");

// redisClient.on("message", function (channel, data) {
//     message = JSON.parse(message);

//     io.emit(channel + ":" + message.event, message.data);
// });

// io.on('connection', (socket) => {
//     socket.on('liveLocation:rider1', (msg) => {
//         io.emit('liveLocation:rider1', msg);
//     });
// });
