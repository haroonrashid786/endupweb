const SERVER_PORT = 6003

//

var fs = require('fs')
var https = require('https')

var express = require('express')
var app = express()

var options = {
    key: fs.readFileSync('/etc/letsencrypt/live/birdino.com/privkey.pem'),
    cert: fs.readFileSync('/etc/letsencrypt/live/birdino.com/fullchain.pem'),
}

var server = https.createServer(options, app)
var io = require('socket.io')(server, {
    cors: {
      origin: '*',
    }
  });

var redis = require('redis')
var ioredis = require('socket.io-redis')

// Multi-server socket handling allowing you to scale horizontally
// or use a load balancer with Redis distributing messages across servers.
io.adapter(ioredis({host: 'localhost', port: 6379}))

//

/*
 * Redis pub/sub
 */

// Listen to local Redis broadcasts
var sub = redis.createClient()

sub.on('error', function (error) {
    console.log('ERROR ' + error)
})

sub.on('subscribe', function (channel, count) {
    console.log('SUBSCRIBE', channel, count)
})

// Handle messages from channels we're subscribed to
sub.on('message', function (channel, payload) {
    console.log('INCOMING MESSAGE', channel, payload)

    payload = JSON.parse(payload)

    // Merge channel into payload
    payload.data._channel = channel

    // Send the data through to any client in the channel room (!)
    // (i.e. server room, usually being just the one user)
    io.sockets.in(channel).emit(payload.event, payload.data)
})

/*
 * Server
 */

// Start listening for incoming client connections
io.sockets.on('connection', function (socket) {

    console.log('NEW CLIENT CONNECTED')

    socket.on('subscribe-to-channel', function (data) {
        console.log('SUBSCRIBE TO CHANNEL', data)

        // Subscribe to the Redis channel using our global subscriber
        sub.subscribe(data.channel)

        // Join the (somewhat local) server room for this channel. This
        // way we can later pass our channel events right through to
        // the room instead of broadcasting them to every client.
        socket.join(data.channel)
    })

    socket.on('disconnect', function () {
        console.log('DISCONNECT')
    })

})

// Start listening for client connections
server.listen(SERVER_PORT, function () {
    console.log('Listening to incoming client connections on port ' + SERVER_PORT)
})
