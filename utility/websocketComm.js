/*
            Későbbi CHAT funkció alapja
            if ("WebSocket" in window) {
               console.log("WebSocket is supported by your Browser!");
               var ws = new WebSocket("ws://192.168.0.24:3000/ws");
               // Let us open a web socket
				
               ws.onopen = function() {
                  
                  // Web Socket is connected, send data using send()
                  //ws.send("I Joined the network!");
                  document.getElementById('webSocketState').style.backgroundColor = ('lime');
                  console.log("Message is sent to the network");
               };
				
               ws.onmessage = function (evt) { 
                var received_msg = evt.data;

                try {
                    let m = JSON.parse(evt.data);
                     handleMessage(m);
                } catch (err) {
                    console.log('[Client] Message is not parseable to JSON.');
                }

                  console.log("Message recieved: " + received_msg);
                  document.getElementById('recMsg').innerHTML = (received_msg);
               };
				
               ws.onclose = function() { 
                  
                  // websocket is closed.
                  console.log("Connection is closed..."); 
                  document.getElementById('webSocketState').style.backgroundColor = ('red');
                  document.getElementById("ServerMsg").style.backgroundColor = ('LightCoral');
                  document.getElementById("ServerMsg").style.color = ('white');
                  document.getElementById('ServerMsg').innerHTML = ('A szerverrel való kommunikáció megszakadt. Próbáld meg újratölteni az oldalt.');
               };

               let handlers = {
                "set-background-color": function(m) {
        // ...*/