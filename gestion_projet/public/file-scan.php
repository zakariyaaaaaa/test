<!DOCTYPE html>
<html>
<head>
    <title>Scan QR</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body>

<h2>📷 Scan QR Code</h2>

<div id="reader" style="width:300px;"></div>

<script>
function onScanSuccess(decodedText) {
    window.location.href = decodedText;
}

new Html5QrcodeScanner("reader", {
    fps: 10,
    qrbox: 250
}).render(onScanSuccess);
</script>

</body>
</html>