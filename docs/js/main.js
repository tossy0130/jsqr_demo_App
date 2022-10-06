     var test_btn2 = document.getElementById('test_btn2');
         //   var test_btn3 = document.getElementById('test_btn3');
            var barcode_scanner = document.getElementById('barcode_scanner');

            test_btn2.onclick = function() {

                     Quagga.init({
                    locate: true,
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        constraints: {
                            width: 100,
                            height: 400,
                        },
                        target: document.querySelector("#barcode_scanner"),
                    },
                    decoder: {
                        readers: ["ean_reader"],
                        multiple: false
                    },
                    locator: {
                        halfSample: false,
                        patchSize: "medium"
                    }
                }, (err) => {
                    if (!err) {
                        Quagga.start();
                        barcode_scanner.style.display = "block";
                    }
                });

                //バーコードをスキャンできた際のイベント
                Quagga.onDetected((data) => {
                    var code = data.codeResult.code;
                    alert(code);

                     // カメラを停止
                    Quagga.stop();
                    barcode_scanner.style.display = "none";

                });
                    

            };
