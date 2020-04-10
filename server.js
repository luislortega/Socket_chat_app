const io = require('socket.io')(3000)
var fs = require('fs');
const users = {}
var d = new Date();
io.on('connection', socket => {

  socket.on('new-user', name => {
    users[socket.id] = name
    console.log("Users Online: "+Object.keys(users).length);
    socket.broadcast.emit('user-connected', name)
  })

  socket.on('send-chat-message', message => {
    socket.broadcast.emit('chat-message', { message: message, name: users[socket.id] })
  })
socket.on("manual-disconnection", function(data) {
  const stringText = d.toLocaleString()+": User Manually Disconnected. trying to do SPAM:. NAME: "+users[data]+"\n";
  fs.appendFile("SPAM-users-disconnected.txt", stringText, function(error) {
    if(error) throw error; // Handle the error just in case
    else console.log("Spam detected check SPAM-users-disconnected.txt!");
}); 
        
    });
  socket.on('disconnect', () => {
    socket.broadcast.emit('user-disconnected', users[socket.id])
    delete users[socket.id]
    console.log("Users Online: "+Object.keys(users).length);
  })

})

console.log("Connected");