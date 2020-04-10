const socket = io('http://localhost:3000')


const messageContainer = document.getElementById('message-container')
const messageForm = document.getElementById('send-container')
const messageInput = document.getElementById('message-input')
if(!socket.connected){
 $('#offline').addClass("hidden");
}else{
  $('#message-input').val("");
    $('#message-input').attr("placeholder", "##CHAT OFFLINE##");
    $('#message-input').prop("disabled", true);

}
socket.on('error', function(){
  socket.socket.reconnect();
});
function isBlank(str) {
    return (!str || /^\s*$/.test(str));
}
String.prototype.isEmpty = function() {
    return (this.length === 0 || !this.trim());
};
function validURL(str) {
  var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
    '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
    '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
    '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
    '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
    '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
  return !!pattern.test(str);
}
function checker(value) {
  var prohibited = ['oneultimate', 'domination', 'dominationspace', '.com', '.net'];

  for (var i = 0; i < prohibited.length; i++) {
    if (value.indexOf(prohibited[i]) > -1) {
      return false;
    }
  }
  return true;
}
function system(value) {
  var prohibited = ['System'];

  for (var i = 0; i < prohibited.length; i++) {
    if (value.indexOf(prohibited[i]) > -1) {
      return false;
    }
  }
  return true;
}
const name = Math.random();
appendMessage('System: Welcome to DeathSpaces Chat')
appendMessage('System: use /duel name to invite someone to duel')
socket.emit('new-user', name)

socket.on('chat-message', data => {
    appendMessage(`<a onclick="$('#message-input').val('/w ${data.name} ');">${data.name}</a>: ${data.message}`)
})

socket.on('user-connected', name => {
  appendMessage(`${name} connected`)
})

socket.on('user-disconnected', name => {
  appendMessage(`${name} disconnected`)
})

messageForm.addEventListener('submit', e => {
  e.preventDefault()
  const message = messageInput.value
  if(!checker(message)){
    appendMessage(`<div style="color:red;">The System Discconect you. NOT SPAM</div>`)
    socket.emit("manual-disconnection", socket.id);
    
    socket.close();
    $('#message-input').val("");
    $('#message-input').attr("placeholder", "##Disconnected##");
    $('#message-input').prop("disabled", true);
    console.log("Socket Closed. ");

  }
  if(!isBlank(message) && !validURL(message) && checker(message)){
    appendMessage(`<a>You</a>: ${message}`)
    socket.emit('send-chat-message', message)
    messageInput.value = ''
  }
})


function appendMessage(message) {
  const chatbox = document.getElementById("chat");
  const messageElement = document.createElement('div')
  if(!system(message)){
    messageElement.setAttribute('class', 'system');
    messageElement.innerHTML = "<div'>" +  message + "</div>";
  }else{
     messageElement.innerHTML = "<div style='color:white;'>" +  message + "</div>";
     $(chatbox).stop().animate({ scrollTop: $(chatbox)[0].scrollHeight}, 1000);
    
  }
 
 // messageElement.innerText = message
  messageContainer.append(messageElement)
}