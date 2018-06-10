<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>扫描二维码</title>
    <style type="text/css">
        .big{
            overflow: auto;
            width:80%;
            border:black solid 1px;
            position:absolute;
            margin: auto;
            left:0px;
            right: 0px;
        }
        .dimension {
            border: red solid 2px;
            width: 400px;
            height: 400px;
            position: relative;
            margin: auto;
            left: 0px;
            right: 0px;
        }
        .elementAlign {

        }
    </style>

    <script type="text/javascript" src="jsqrcode\src\grid.js"></script>
    <script type="text/javascript" src="jsqrcode\src\version.js"></script>
    <script type="text/javascript" src="jsqrcode\src\detector.js"></script>
    <script type="text/javascript" src="jsqrcode\src\formatinf.js"></script>
    <script type="text/javascript" src="jsqrcode\src\errorlevel.js"></script>
    <script type="text/javascript" src="jsqrcode\src\bitmat.js"></script>
    <script type="text/javascript" src="jsqrcode\src\datablock.js"></script>
    <script type="text/javascript" src="jsqrcode\src\bmparser.js"></script>
    <script type="text/javascript" src="jsqrcode\src\datamask.js"></script>
    <script type="text/javascript" src="jsqrcode\src\rsdecoder.js"></script>
    <script type="text/javascript" src="jsqrcode\src\gf256poly.js"></script>
    <script type="text/javascript" src="jsqrcode\src\gf256.js"></script>
    <script type="text/javascript" src="jsqrcode\src\decoder.js"></script>
    <script type="text/javascript" src="jsqrcode\src\qrcode.js"></script>
    <script type="text/javascript" src="jsqrcode\src\findpat.js"></script>
    <script type="text/javascript" src="jsqrcode\src\alignpat.js"></script>
    <script type="text/javascript" src="jsqrcode\src\databr.js"></script>

</head>
<body >
<div class="big">
    <div class="elementAlign" style="display: none"><video id="qrVideo" width="400" height="400"></video></div>
    <div class="elementAlign"><canvas id="qrCanvas" width="400"  height="400"></canvas></div>
    <div class="elementAlign"><button id="qrBtn" style="width: 200px; height: 150px" >扫码</button></div>
    <div class="elementAlign"><input type="text" id="qrText">扫码结果</input></div>
</div>

<script type="text/javascript">
    var video = document.getElementById('qrVideo');
    var canvas = document.getElementById('qrCanvas');
    var text = document.getElementById('qrText');
    var timer = null;
    var exArray = []; //存储设备源ID

    function checkCode(url) {
        qrcode.callback = function (data) {
            text.setAttribute('value', data);
            if (data == 'error decoding QR Code') {

            } else {               
                //window.clearInterval(timer);
				window.location.href = data;
            }
        }
        qrcode.decode(url);
    }

    function drawImageOnCanvas() {
        timer = window.setInterval(function () {
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);        
            checkCode(canvas.toDataURL("image/png"))
        }, 500);
    }

    function onVideoStream(stream) {
        alert("onVideoStream");
        if ("srcObject" in video) {	
            video.srcObject = stream
        } else {		
            video.src = window.URL && window.URL.createObjectURL(stream) || stream
        }
		
        video.play();
        video.addEventListener('play', drawImageOnCanvas);
    }

    function onVideoErr(err) {
        alert(err.name + ", " + err.message);
        console.log(err.name + ", " + err.message);        
    }

    function startCamera() {             
        if(exArray.length == 0) {
			alert("没有可用的相机");
            return;
        }
        var options = {};

        if(exArray.length > 0) {
            options = {
                audio: false,
                video: {
                    deviceId: exArray[exArray.length - 1]
                }
            };
        } else {
            options = {
                audio: false,
                video: true
            };
        }

        if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia(options).then(onVideoStream).catch(onVideoErr);
        }

        /*var getUserMedia = (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia);
        if(!getUserMedia) {
            alertMsg('navigator不支持getUserMedia');
        } else {
            //navigator.getUserMedia(options, onVideoStream, onVideoErr);
            getUserMedia.call(navigator, {
                'video': {
                    'optional': [{
                        'sourceId': exArray[exArray.length - 1]
                    }]
                }
            }, onVideoStream, onVideoErr);
        }*/
    };
	
	document.getElementById("qrBtn").addEventListener("click", function() {
		checkCode(canvas.toDataURL("image/png"));
	});

	/*var fncMsg = "";

	if(navigator.mediaDevices == undefined) {
	    fncMsg = "navigator."
    }*/
    if(!navigator.mediaDevices) {
        alert('navigator has no mediaDevice');
    } else {
        if (navigator.mediaDevices.enumerateDevices) {
            navigator.mediaDevices.enumerateDevices().then(function (devices) {
                //alert("devices length=" + devices.length);
                for (var len = 0; len < devices.length; len++) {
                    var device = devices[len];
                    if (device.kind.indexOf("video") >= 0) {
                        console.log("enumerateDevices: " + device.label + ", " + device.kind + ", " + device.deviceId);
                        //alert("enumerateDevices: " + device.kind );
                        exArray.push(device.deviceId);
                    }
                }
                startCamera();
            }).catch(function (err) {
                alert(err.name + ", " + err.message);
            });
        } else {
            alert("no navigator.mediaDevices.enumerateDevices");
        }
    }
</script>
</body>
</html>