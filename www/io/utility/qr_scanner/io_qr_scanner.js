// AUDIO

let scan_succes_sfx = new Audio('utility/qr_scanner/sounds/scan_succes.mp3');
let scan_fail_sfx = new Audio('utility/qr_scanner/sounds/scan_fail.mp3');

//Scanner
let macroCam;

window.addEventListener("orientationchange", function () {
   stopScanner().then((ignore) => {
      startScanner(macroCam.id);
   });
})

const toastLiveExample = document.getElementById('scan_toast');
const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample);

//let toastOverwriteAllowed = true;

function showToast(message, color) {
   document.getElementById("scan_result").innerHTML = "<b style='color: " + color + ";'>" + message + "</b>";
   toastLiveExample.style.display = "block";
   toastBootstrap.show();
}


//Creating Qr reader
const QrReader = new Html5Qrcode("reader");
let QrReaderStarted = false;
let ExternalScanEnabled = false;

//Qr reader settings
const qrconstraints = {
   facingMode: "environment"
};
const qrConfig = {
   fps: 10,
   qrbox: {
      width: 200,
      height: 150
   },
   showTorchButtonIfSupported: true
};

// Methods: start / stop
const startScanner = (camera) => {

   if (!QrReaderStarted && camera != null) {
      console.log("Reader started! - with specific camera: " + camera);
      QrReaderStarted = true;
      return QrReader.start(
         camera,
         qrConfig,
         qrOnSuccess,
      ).then().catch(console.error);
   }
   else if (!QrReaderStarted && camera == null) {
      QrReaderStarted = true;
      console.log("Reader started! - environment");
      return QrReader.start(
         qrconstraints,
         qrConfig,
         qrOnSuccess,
      ).then().catch(console.error);
   }
   else if (camera == null) {
      QrReader.resume();
      console.log("Unpaused!");
   }
};

const pauseScanner = () => {
   QrReader.pause();
};

const stopScanner = () => {
   return QrReader.stop().then(ignore => {
      QrReaderStarted = false;
      console.log("Reader stopped!");
   }).catch(err => {
      console.log("Error while stopping: " + err);
   });
};

// Start scanner on button click

let available_cams;

function showScannerModal() {
   if (ExternalScanEnabled) {
      $('#scanner_Modal').modal('show');
      setTimeout(function () {
         $('#ext_scan_input').focus();
      }, 500);
   }
   else if (QrReaderStarted) {
      startScanner(null);
      $('#scanner_Modal').modal('show');
   }
   else {
      Html5Qrcode.getCameras().then(devices => {
         available_cams = devices;
         for (i = 0; i < available_cams.length; i++) {
            if (available_cams[i].label.toLowerCase().includes("dual") == false) {

               $('#av_cams').append('<li><a class="dropdown-item" href="#" onclick="switchCamera(\'' + available_cams[i].id + '\');">' + available_cams[i].label + '</a></li>');
            }
         }
         $('#scanner_Modal').modal('show');
         macroCam = available_cams.find(cam => cam.label.toLowerCase().includes("ultra wide"));
         if (macroCam == undefined) {
            macroCam = null;
            console.log("No telephoto camera found, starting default camera");
         }
         else {
            macroCam = macroCam.id;
            console.log("Macro camera found: " + macroCam.label);
         }

         startScanner(macroCam).then((ignore) => {
            settings = QrReader.getRunningTrackSettings();
            // If zoom available, display button
            if ("zoom" in settings == false) {
               console.log("Zoom unavailable");
               $('#zoom_btn').remove();
            }
            if ("torch" in settings == false) {
               console.log("Torch unavailable");
               $('#torch_btn').remove();
            }
         });
      });
   }

}

function switchCamera(nextCamId) {
   stopScanner().then((ignore) => {
      let nextCam = available_cams.find(cam => cam.id === nextCamId);
      if (nextCam) {
         console.log("Switching camera to: " + nextCam.label);
         startScanner(nextCam.id);
      } else {
         console.log("Camera not found: " + nextCamId);
      }
   });
}


function pauseCamera() {
   if (QrReaderStarted) {
      console.log("Pausing camera");
      pauseScanner();
   }
}

function zoomCamera() {
   let settings = QrReader.getRunningTrackSettings();
   let currentZoom = settings.zoom;
   let nextzoom;
   switch (currentZoom) {
      case 1:
         nextzoom = 2;
         console.log("Zooming 2x");
         break;
      case 2:
         nextzoom = 1;
         console.log("Zooming 1x");
         break;
      default:
         nextzoom = 1;
         break;
   }

   let constraints = {
      "zoom": nextzoom,
      "advanced": [{ "zoom": nextzoom }]
   };
   QrReader.applyVideoConstraints(constraints);
   console.log("Zoomed");
   document.getElementById('zoom_btn').innerHTML = "Zoom: " + currentZoom + "x";
}

let torchOn = false;

function startTorch() {
   if (torchOn == false) {
      torchOn = true;
      let constraints = {
         "torch": true,
         "advanced": [{ "torch": true }]
      };
      QrReader.applyVideoConstraints(constraints);
   }
   else {
      torchOn = false;
      let constraints = {
         "torch": false,
         "advanced": [{ "torch": false }]
      };
      QrReader.applyVideoConstraints(constraints);
   }
   console.log("Torch toggled");
}

function ExternalScan() {
   if ($('#ext_scanner').hasClass('btn-outline-dark')) {
      console.log("Checked");
      ExternalScanEnabled = true;
      $('#ext_scanner').removeClass('btn-outline-dark');
      $('#ext_scanner').addClass('btn-dark');
      $('#scanner_body').prepend('<input id="ext_scan_input" type="text"/>');
      $('#ext_scan_input').focus();
      $('#ext_scan_input').on('keypress', function (e) {
         if (e.which == 13) {
            var input = $('#ext_scan_input').val();
            qrOnSuccess(input, null);
            $('#ext_scan_input').val('');
         }
      });
      stopScanner().then((ignore) => { });
   } else {
      console.log("Unchecked");
      ExternalScanEnabled = false;
      $('#ext_scan_input').remove();
      $('#ext_scanner').removeClass('btn-dark');
      $('#ext_scanner').addClass('btn-outline-dark');
      startScanner(macroCam).then((ignore) => {

      });
   }

}

