const socket = io("http://localhost:3000");

const messageContainer = document.getElementById("message-container");
const messageForm = document.getElementById("send-container");
const messageInput = document.getElementById("message-input");
if (!socket.connected) {
  $("#offline").addClass("hidden");
} else {
  $("#message-input").val("");
  $("#message-input").attr("placeholder", "##CHAT OFFLINE##");
  $("#message-input").prop("disabled", true);
}

function isBlank(str) {
  return !str || /^\s*$/.test(str);
}
String.prototype.isEmpty = function () {
  return this.length === 0 || !this.trim();
};
function validURL(str) {
  var pattern = new RegExp(
    "^(https?:\\/\\/)?" + // protocol
    "((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|" + // domain name
    "((\\d{1,3}\\.){3}\\d{1,3}))" + // OR ip (v4) address
    "(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*" + // port and path
    "(\\?[;&a-z\\d%_.~+=-]*)?" + // query string
      "(\\#[-a-z\\d_]*)?$",
    "i"
  ); // fragment locator
  return !!pattern.test(str);
}
function checker(value) {
  var prohibited = [
    "oneultimate",
    "domination",
    "dominationspace",
    ".com",
    ".net",
  ];

  for (var i = 0; i < prohibited.length; i++) {
    if (value.indexOf(prohibited[i]) > -1) {
      return false;
    }
  }
  return true;
}
function commands(value) {
  var prohibited = ["/w", "/duel"];

  for (var i = 0; i < prohibited.length; i++) {
    if (value.indexOf(prohibited[i]) > -1) {
      return false;
    }
  }
  return true;
}
function system(value) {
  var prohibited = ["System"];

  for (var i = 0; i < prohibited.length; i++) {
    if (value.indexOf(prohibited[i]) > -1) {
      return false;
    }
  }
  return true;
}
var name = Math.random();
appendMessage("System: Welcome to DeathSpaces Chat");
appendMessage("System: use /duel name to invite someone to duel");
socket.emit("new-user", name);

socket.on("chat-message", (data) => {
  appendMessage(
    `<a onclick="$('#message-input').val('/w ${data.name} ');">${data.name}</a>: ${data.message}`
  );
});

socket.on("chat-whisper", (data) => {
  appendMessage(
    `<div style="color:yellow;" ><a style="color:yellow;" onclick="$('#message-input').val('/w ${data.name} ');">${data.name}</a> Wispers:</div> ${data.message}`
  );
});

socket.on("chat-flod", (data) => {
  appendMessage(
    `System: Youre chating too fast, slow down or your will be kicked. Warning: ${data.flod}/3`
  );
});
socket.on("chat-duel", (data) => {
  appendMessage(
    `<div style="color:yellow;" ><a style="color:yellow;" >${data.name}</a> invited you to a duel:</div> <a onclick="duel('${data.name}','${data.from}');">Accept duel!</a>`
  );
});
socket.on("user-connected", (name) => {
  appendMessage(`${name} connected`);
});

socket.on("user-disconnected", (name) => {
  appendMessage(`${name} disconnected`);
});

messageForm.addEventListener("submit", (e) => {
  e.preventDefault();
  const message = messageInput.value;

  if (!commands(message)) {
    if (!checker(message)) {
      appendMessage(
        `<div style="color:red;">The System Discconect you. NOT SPAM</div>`
      );
      socket.emit("manual-disconnection", socket.id, message);

      socket.close();
      $("#message-input").val("");
      $("#message-input").attr("placeholder", "##Disconnected##");
      $("#message-input").prop("disabled", true);
      console.log("Socket Closed. ");
    } else {
      switch (true) {
        case message.indexOf("/w") >= 0:
          var values = message.split(" ");
          var user_to = values[1];
          var whisper_message = message.split(" ").slice(2).join(" ");
          appendMessage(
            `<div style="color:yellow;">You're Wispering to ${user_to}:</div> ${whisper_message}`
          );
          socket.emit("whisper", {
            toid: user_to,
            msg: whisper_message,
          });

          messageInput.value = "";
          break;
        case message.indexOf("/duel") >= 0:
          var values = message.split(" ");
          var user_to = values[1];
          appendMessage("System: You invited " + user_to);
          socket.emit("duel", {
            toid: user_to,
          });
          messageInput.value = "";
          break;
        case message.indexOf("Aqui otro comando") >= 0:
          document.write('code contains "ST3"');
          break;
      }
    }

    //socket.emit('whisper', message: whisper_message, toid: user_to);
  } else {
    if (!checker(message)) {
      appendMessage(
        `<div style="color:red;">The System Discconect you. NOT SPAM</div>`
      );
      socket.emit("manual-disconnection", { id: socket.id, message: message });

      socket.close();
      $("#message-input").val("");
      $("#message-input").attr("placeholder", "##Disconnected##");
      $("#message-input").prop("disabled", true);
      console.log("Socket Closed. ");
    }
    socket.on("flodding", function (data) {
      appendMessage(
        `<div style="color:red;">The System Discconect you for flooding.</div>`
      );
      $("#message-input").val("");
      $("#message-input").attr("placeholder", "##DISCONNECTED. Reload Chat##");
      $("#message-input").prop("disabled", true);
      socket.close();
    });
    socket.on("eventToClient", function (data) {
      appendMessage(
        `<div style="color:red;">The System Discconect you for being inactive.</div>`
      );
      $("#message-input").val("");
      $("#message-input").attr("placeholder", "##DISCONNECTED. Reload Chat##");
      $("#message-input").prop("disabled", true);
      socket.close();
    });

    if (!isBlank(message) && !validURL(message) && checker(message)) {
      appendMessage(`<a>You</a>: ${message}`);

      socket.emit("send-chat-message", message);
      messageInput.value = "";
    }
  }
});

function makeid(length) {
  var result = "";
  var characters =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  var charactersLength = characters.length;
  for (var i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
  }
  return result;
}
/* test every 100 milliseconds 
setInterval(function(){
  let text = makeid(Math.floor(Math.random() * 10));
  appendMessage(`<a>You</a>: ${text}`)
  socket.emit('send-chat-message', text);
}, 100)

*/
function appendMessage(message) {
  const chatbox = document.getElementById("chat");
  const messageElement = document.createElement("div");
  if (!system(message)) {
    messageElement.setAttribute("class", "system");
    messageElement.innerHTML = "<div'>" + message + "</div>";
  } else {
    messageElement.innerHTML =
      "<div style='color:white;'>" + message + "</div>";
    $(chatbox)
      .stop()
      .animate({ scrollTop: $(chatbox)[0].scrollHeight }, 1000);
  }

  // messageElement.innerText = message
  messageContainer.append(messageElement);
}

$("#message-input").attr("autocomplete", "randomString");
$("#detailBoxSmall").click(function () {
  $(".detailBox").toggle("slide");
  $(".detailBoxSmall").toggle("slide");
});
$("#mini").click(function () {
  $(".detailBox").toggle("slide");
  $(".detailBoxSmall").toggle("slide");
});
$(function () {
  $(".detailBoxSmall").toggle("slide");
});

window.onload = addListeners();

function addListeners() {
  document
    .getElementById("titleBox")
    .addEventListener("mousedown", mouseDown, false);
  window.addEventListener("mouseup", mouseUp, false);
}

function mouseUp() {
  window.removeEventListener("mousemove", divMove, true);
}

function mouseDown(e) {
  window.addEventListener("mousemove", divMove, true);
}

function divMove(e) {
  var div = document.getElementById("draggable_test");
  div.style.position = "absolute";
  div.style.top = e.clientY - 50 + "px";
  div.style.left = e.clientX - 200 + "px";
}

function duel(user1, user2) {
  alert("Duels are not available yet...");
  console.log(user1, user2);
}
