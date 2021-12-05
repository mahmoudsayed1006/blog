var app = require('express')();
//var server = require('http').Server(app);
//var io = require('socket.io')(server);
var Redis = require('ioredis');
var redis = new Redis();
var socket = require('socket.io');

var server = app.listen(3000, function(){
    console.log('listening for requests on port 3000,');
});
console.log('server run')
var io = socket(server);
io.on('connection', function (socket) {
  socket.on('test', function(data){
    socket.broadcast.emit('test', data);
  });
  console.log("client connected");
  var redisClient = redis.createClient();
  redisClient.subscribe('message');
 
  redisClient.on("message", function(channel, data) {
    console.log("mew message add in queue "+ data['message'] + " channel");
    socket.emit(channel, data);
  });
 
  socket.on('disconnect', function() {
    redisClient.quit();
  });
 
});
