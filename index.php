<?php
  // Protect access to the website by password (remove this if password protection is not needed)
  //require_once('secure.php'); // Link to security (not included on Git)
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title> ITU Chat 2020 </title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="application-name" content="ITU Chat 2020" />
    <meta name="author" content="Roman Janiczek" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Ionicons - https://ionicons.com/ -->
    <script type="module" src="https://unpkg.com/ionicons@5.2.3/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule="" src="https://unpkg.com/ionicons@5.2.3/dist/ionicons/ionicons.js"></script>
    <!-- CSS for ITU CHAT 2020 -->
    <style type="text/css">
      /* width */
      #chatArea::-webkit-scrollbar {
        width: 5px;
      }

      /* Track */
      #chatArea::-webkit-scrollbar-track {
        background: #222222; 
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
	      border-radius: 3px;
      }
      
      /* Handle */
      #chatArea::-webkit-scrollbar-thumb {
        background: #949494; 
        border-radius: 3px;
	      -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
      }

      /* Handle on hover */
      #chatArea::-webkit-scrollbar-thumb:hover {
        background: #555; 
      }
      html, body { 
        height: 100%; 
        width: 100%; 
        margin: 0; 
        overflow: hidden; 
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        background-image: radial-gradient(circle at 0% 0%, #373b52, #252736 51%, #1d1e26);
      }
      div, input { 
        margin: 10px;
      }
      .submitForm, #status, #chatArea {
        width: 80%; 
        margin: 10px auto; 
        padding: 10px; 
        box-shadow: 0px 0px 12px 1px rgba(0, 0, 0, 0.66); 
        border-radius: 7px;
        border: 1px solid #222222; 
        background-color: #222222; 
        color: #f8f8f8;
      }
      #status {
        color: #949494;
      }
      .submitForm {
        width: 80%; 
        margin: 10px auto;
        padding: 10px;
      }
      .inputBox {
        width: 79%;
        margin: 0px auto; 
        background-color: #222222; 
        border-style: none; 
        background: transparent; 
        outline: none; 
        color: #f8f8f8;
      }
      .inputSub {
        float: right; 
        width: 20%; 
        background-color: #222222; 
        padding: 0; 
        background: none; 
        border: none; 
        outline: none;
      }
      ion-icon {
        color: #f8f8f8;
        font-size: 16px;
      }
    </style>
  </head>

  <body>
    <!-- chatArea (all the msgs will be displayed here -->
    <div id="chatArea" style="height: 80%; overflow:auto;"></div> 

    <!-- form for sending new msgs -->
    <div class="submitForm">
      <form onsubmit="return uploadData()">
        <input class="inputBox" type="text" id="newMessageString" placeholder="Your message here ..." minlength="1" maxlength="1024" autocomplete="off" required>
        <button class="inputSub" type="submit" value="send"><ion-icon name="arrow-forward-outline"></ion-icon></button>
      </form>
    </div>

    <!-- status bar (basic debug output) -->
    <div id="status"></div> 
  </body>

  <script type="text/javascript">
    // Help
    help();
    function help() {
      alert("You will be asked for address of API, if you don't remember this you can just continue and default API will be selected.\n" +
            "Afterwards you will be asked to select your username (max 8 characters, depending on API).\n" +
            "You can use Alt + S to switch between default APIs.");
    }
    // List of default APIs servers 
    let defaultAPI = ["https://ituchat.czleteron.net/api.php", "http://pckiss.fit.vutbr.cz/itu/api.php", "http://www.stud.fit.vutbr.cz/~xmlich02/itu-ajax/api.php"];
    var currentAPI = 0;
    var totalAPI = 3;
    // Address of API server
    var apiAddress = prompt("Enter API address", "");
    if (apiAddress == null || apiAddress == "") {
      apiAddress = defaultAPI[0];
      totalAPI = 2;
    } else {
      defaultAPI.push(apiAddress);
    }
    // Other variables
    var debug = false;  // Allow debug output to console
    var lastMsgId = 0;
    // Username setup (doesn't work on school API)
    var user = prompt("Please enter your name", "xlogin00");
    // -- default user setup if not selected
    if (user == null || user == "") {
      user = "xlogin00";
    }

    // Switch API
    document.addEventListener('keydown', function(event) {
      // === DEBUG OUTPUT --- KEY PRESS DETECT ===
      if (debug) {
        console.log('%c [DEBUG] ', 'background: #222; color: #0000FF', 'Key Press Detected');
      }
      // ====
      if (event.altKey && event.key === 's') {
        switchAPI();
      }
    })
    function switchAPI() {
      // === DEBUG OUTPUT --- CURRENTLY RUNNING FUNC ===
      if (debug) {
        console.log('%c [DEBUG] ', 'background: #222; color: #0000FF', 'Currently running function switchAPI()');
      }
      // ====
      // Currently running function - status bar update
      document.getElementById("status").innerHTML = "switchAPI()";
      var date = new Date();

      // Move in the list of APIs
      currentAPI++;
      if (currentAPI > totalAPI) {
        currentAPI = 0; // Got too far, go back to start
      }
      // Change API
      apiAddress = defaultAPI[currentAPI];
      lastMsgId = 0;  // Different chat == different IDs
      document.getElementById('chatArea').innerHTML += '<span title="API change to ' + apiAddress + '"><span style="color: #949494;">['+ (date.getHours()<10?'0':'') + date.getHours() + ':' 
                                                    + (date.getMinutes()<10?'0':'') + date.getMinutes() + ']</span> <b style="color: #CD5C5C">CHAT NOW CONNECTED TO</b><i style="color: #00BFFF"> ' + apiAddress + '</i><br>';
    }
    function addAndSwitchAPI(server) {
      var date = new Date();
      defaultAPI.push(server);
      totalAPI++;
      currentAPI = totalAPI - 1;
      apiAddress = server;
      lastMsgId = 0;
      document.getElementById('chatArea').innerHTML += '<span title="API change to ' + apiAddress + '"><span style="color: #949494;">['+ (date.getHours()<10?'0':'') + date.getHours() + ':' 
                                                    + (date.getMinutes()<10?'0':'') + date.getMinutes() + ']</span> <b style="color: #CD5C5C">CHAT NOW CONNECTED TO</b><i style="color: #00BFFF"> ' + apiAddress + '</i><br>';
    }

    /***
      * XMLHttpRequest object constructor (for compatibility with various browsers)
      */
    function createXmlHttpRequestObject() {
      // === DEBUG OUTPUT --- CURRENTLY RUNNING FUNC ===
      if (debug) {
        console.log('%c [DEBUG] ', 'background: #222; color: #0000FF', 'Currently running function createXmlHttpRequestObject()');
      }
      // ====

      var xmlhttp;
      try {
        xmlHttp = new XMLHttpRequest(); //should work on all browsers except IE6 or older
      } catch (e) { 
        try {
          xmlHttp = new ActiveXObject("Microsoft.XMLHttp"); //browser is IE6 or older
        } catch (e) {
          // ignore error
        }
      }
      if (!xmlHttp) {
        alert ("Error creating the XMLHttpRequest object.");
      } else {
        return xmlHttp;
      }
    }

    /**
     * Upload data function - Runs when you send a msg (send a msg to the API which saves it to db)
     */
    function uploadData() {
      // === DEBUG OUTPUT --- CURRENTLY RUNNING FUNC ===
      if (debug) {
        console.log('%c [DEBUG] ', 'background: #222; color: #0000FF', 'Currently running function uploadData()');
      }
      // ====
      // Currently running function - status bar update
      document.getElementById("status").innerHTML = "uploadData()";

      try {
        var xmlHttp = createXmlHttpRequestObject();                                                     // Stores XMLHttpRequestObject

        var params = "data=" + document.getElementById('newMessageString').value + "&user=" + user;     // Data string to be sent
        xmlHttp.open("POST", apiAddress, true);                                                         // Open HTTP REQ 'POST' for API ADDRESS
        xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;");
        xmlHttp.onreadystatechange = downloadData;                                                      // Function to run after sending the data
        xmlHttp.send(params);

        document.getElementById('newMessageString').value = "";

      } catch (e) {
        alert(e.toString());  // Alert box error
      }

      return false; // to avoid default form submit behavior 
    }

    /**
     * Download data function - Runs every time you request new msgs from the API (download new msgs from db using the API)
     */
    function downloadData() {
      // === DEBUG OUTPUT --- CURRENTLY RUNNING FUNC ===
      if (debug) {
        console.log('%c [DEBUG] ', 'background: #222; color: #0000FF', 'Currently running function downloadData()');
      }
      // ====
      // Currently running function - status bar update
      document.getElementById("status").innerHTML = "downloadData()";

      var xmlHttp = createXmlHttpRequestObject();   // Stores XMLHttpRequestObject

      xmlHttp.open("GET", apiAddress, true);        // Open HTTP REQ 'GET' for API ADDRESS
      xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;");
      xmlHttp.onreadystatechange = receiveData;     // Function to run on received data
      xmlHttp.send(null);

    }

    /**
     * Receive data - Run with every download data call (format received data and display them to chatArea)
     */
    function receiveData() {
      // === DEBUG OUTPUT --- CURRENTLY RUNNING FUNC ===
      if (debug) {
        console.log('%c [DEBUG] ', 'background: #222; color: #0000FF', 'Currently running function receiveData()');
      }
      // ====
      // Currently running function - status bar update
      document.getElementById("status").innerHTML = "receiveData()";
      var newMsg = false; // Local function variable to mark that new msgs were received

      if ((xmlHttp.readyState==4) && (xmlHttp.status==200)) { //process is completed and http status is OK
        // Parse JSON and get MSGs
        var msgArray = JSON.parse(xmlHttp.responseText);
        // === DEBUG OUTPUT --- PARSED ARRAY ===
        if (debug) {
              console.log('%c [DEBUG] ', 'background: #222; color: #0000FF', msgArray);
            }
        // ====
        for ( var i in msgArray ) {
          // Check if msg is already in chatArea
          if (lastMsgId < Number(msgArray[i].id)) {
            // === DEBUG OUTPUT --- CURRENTLY PARSIN MSG ===
            if (debug) {
              console.log('%c [DEBUG] ', 'background: #222; color: #0000FF', msgArray[i]);
            }
            // ====

            newMsg = true;                        // Local function variable to mark that new msgs were received
            let date = new Date(msgArray[i].dts); // Get msg timestamp for future use

            // Add MSG to chatArea and status bar
            document.getElementById('chatArea').innerHTML += '<span title="' + msgArray[i].id + '" id="' + msgArray[i].id + '"><span style="color: #949494;">['+ (date.getHours()<10?'0':'') + date.getHours() + ':' 
                                                          + (date.getMinutes()<10?'0':'') + date.getMinutes() + ']</span> <b>' + sanitizeHTML(msgArray[i].login) + ':</b> ' + sanitizeHTML(msgArray[i].cnt) + '</span><br>';
            document.getElementById("status").innerHTML = ' ID of the last msg: ' + msgArray[i].id + ' from ' + sanitizeHTML(msgArray[i].login);

            // === DEBUG OUTPUT --- LOG ID OF EVERY NEW MSG TO CONSOLE ===
            console.log('%c [NEW MSG] ', 'background: #222; color: #FFA500', msgArray[i].id);
            // ===
            // Remember last msg
            lastMsgId = msgArray[i].id;

            // Scroll to the newest msg
            //var element = document.getElementById(lastMsgId);
            //element.scrollIntoView({ behavior: 'smooth' });
            var objDiv = document.getElementById("chatArea");
            objDiv.scrollTop = objDiv.scrollHeight;
          }
        }
        // === DEBUG OUTPUT --- LOG THAT NO NEW MSGs WERE RECEIVED ===
        if (!newMsg) {
          console.log('%c [NO NEW MSG]', 'background: #222; color: #008000');
        }
        // ===
        // Update status bar
        document.getElementById("status").innerHTML = ' ID of the last msg: ' + lastMsgId + ' from ' + sanitizeHTML(msgArray[i].login);
      } 
    }

    /**
     * Sanitize data (as some of the APIs are not protected against XSS)
     */
    function sanitizeHTML(text) {
      var element = document.createElement('span');
      element.innerText = text;
      return element.innerHTML;
    }

    downloadData();                    // Initial download
    setInterval(downloadData, 1000);   // Download newest msgs every 1s (1000ms)
  </script>
</html>
