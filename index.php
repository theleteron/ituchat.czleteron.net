<?php
  // Protect access to the website by password (remove this if password protection is not needed)
  require_once('secure.php');
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
    <div id="chatArea" style="height: 80%; overflow:auto;"></div> 

    <div class="submitForm">
      <form onsubmit="return uploadData()">
        <input class="inputBox" type="text" id="newMessageString" placeholder="Your message here ..." minlength="1" maxlength="1024" required>
        <button class="inputSub" type="submit" value="send"><ion-icon name="arrow-forward-outline"></ion-icon></button>
      </form>
    </div>

    <div id="status"></div> 
  </body>

  <script type="text/javascript">
    // Address of API server
    var apiAddress = "https://ituchat.czleteron.net/api.php";
    // Other variables
    var lastMsgId = 0;
    var user = prompt("Please enter your name", "xlogin00");

    if (user == null || user == "") {
      user = "xlogin00";
    }

    /***
      * XMLHttpRequest object constructor (for compatibility with various browsers)
      */
    function createXmlHttpRequestObject() {
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

    function uploadData() {
      document.getElementById("status").innerHTML = "uploadData()";

      try {
        var xmlHttp = createXmlHttpRequestObject(); //stores XMLHttpRequestObject

        var params = "data=" + document.getElementById('newMessageString').value + "&user=" + user;
        xmlHttp.open("POST", apiAddress, true);
        xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;");
        xmlHttp.onreadystatechange = downloadData;
        xmlHttp.send(params);

        document.getElementById('newMessageString').value = "";

      } catch (e) {
        alert(e.toString());
      }

      return false; // to avoid default form submit behavior 
    }

    function downloadData() {
      document.getElementById("status").innerHTML = "downloadData()";

      var xmlHttp = createXmlHttpRequestObject();
      xmlHttp.open("GET", apiAddress, true);
      xmlHttp.onreadystatechange = receiveData;
      xmlHttp.send(null);

    }

    function receiveData() {
      document.getElementById("status").innerHTML = "receiveData()";
      var newMsg = false;

      if ((xmlHttp.readyState==4) && (xmlHttp.status==200)) { //process is completed and http status is OK
        var pole = JSON.parse(xmlHttp.responseText);
        for ( var i in pole ) {
          if (lastMsgId < pole[i].id) {
            newMsg = true;
            let date = new Date(pole[i].dts);
            document.getElementById('chatArea').innerHTML += '<span title="' + pole[i].id + '" id="' + pole[i].id + '"><span style="color: #949494;">['+ date.getHours() + ':' + date.getMinutes() +
                                                            ']</span> <b>' + pole[i].login + ':</b> ' + pole[i].cnt + '</span><br>';
            document.getElementById("status").innerHTML = ' ID of the last msg: ' + pole[i].id + ' from ' + pole[i].login;
            console.log('%c [NEW MSG] ', 'background: #222; color: #FFA500', pole[i].id);
            lastMsgId = pole[i].id;

            var element = document.getElementById(lastMsgId);
            element.scrollIntoView({ behavior: 'smooth' });
          }
        }
        if (!newMsg) {
          console.log('%c [NO NEW MSG]', 'background: #222; color: #008000');
        }
        document.getElementById("status").innerHTML = ' ID of the last msg: ' + lastMsgId + ' from ' + pole[i].login;
      } 
    }
    downloadData();
    setInterval(downloadData, 1000);
  </script>
</html>
