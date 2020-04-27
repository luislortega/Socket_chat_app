const io = require("socket.io")(3000);
var fs = require("fs");
const { RateLimiterMemory } = require("rate-limiter-flexible");
const users = {};
var d = new Date();
var flod = 0;

const rateLimiter = new RateLimiterMemory({
  points: 1, // 5 points
  duration: 1, // per second
});
io.on("connection", (socket) => {
  socket.on("new-user", (name) => {
    flod = 0;
    if (Object.values(users).indexOf(name) > -1) {
      console.log("has " + name);
    } else {
      users[socket.id] = name;
      console.log("Users Online: " + Object.keys(users).length);
      socket.broadcast.emit("user-connected", name);
    }
  });
  //whisper
  socket.on("whisper", function (data) {
    var to = Object.keys(users).find((key) => users[key] == data.toid);
    io.to(`${to}`).emit("chat-whisper", {
      message: data.msg,
      name: users[socket.id],
    });
  });
  //duel
  socket.on("duel", function (data) {
    var to = Object.keys(users).find((key) => users[key] == data.toid);
    io.to(`${to}`).emit("chat-duel", {
      from: data.toid,
      name: users[socket.id],
    });
  });

  socket.on("send-chat-message", async (data) => {
    var messagelength = data.toString().length;
    console.log(messagelength);
    if(users[socket.id] == undefined){
    if (messagelength < 46) {
      if (flod >= 3) {
        socket.emit("flodding", { data: null });
      } else {
        try {
          await rateLimiter.consume(socket.handshake.address); // consume 1 point per event from IP

          socket.broadcast.emit("chat-message", {
            message: data,
            name: users[socket.id],
          });
        } catch (rejRes) {
          // no available points to consume
          // emit error or warning message
          flod++;
          var to = socket.id;
          io.to(`${to}`).emit("chat-flod", {
            flod: flod,
          });
        }
      }
    } else {
      var to = socket.id;
          io.to(`${to}`).emit("chat-flod", {
            flod: flod,
          });
    }
  }else{
    socket.broadcast.emit('chat-message', { message: message, name: users[socket.id] })
  }

  });

  /*socket.on('send-chat-message', message => {
    if(users[socket.id] == undefined){
      //socket.broadcast.emit('timeout', { name:null })
      socket.emit('eventToClient',{ data: null });
      delete users[socket.id]
    }else{
      socket.broadcast.emit('chat-message', { message: message, name: users[socket.id] })
    }
    
  })*/
  socket.on("manual-disconnection", function (data) {
    const stringText =
      d.toLocaleString() +
      ": User Manually Disconnected. trying to do SPAM:. NAME: " +
      users[data.id] +
      " Message: " +
      data.message +
      "\n";
    fs.appendFile("SPAM-users-disconnected.txt", stringText, function (error) {
      if (error) throw error;
      // Handle the error just in case
      else console.log("Spam detected check SPAM-users-disconnected.txt!");
    });
  });
  socket.on("disconnect", () => {
    socket.broadcast.emit("user-disconnected", users[socket.id]);
    delete users[socket.id];
    console.log("Users Online: " + Object.keys(users).length);
  });
});

console.log("Connected");
