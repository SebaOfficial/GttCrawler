function previewVideo(start){
    const overlay = document.getElementById("qr-preview-container");

    overlay.style.display = start ? "flex" : "none";

    document.body.style.overflow = start ? "hidden" : "";

}

function scanQR(start = true){
    const video = document.getElementById('qr-scanner-preview');
    let scanner = new Instascan.Scanner({ video: video});

    if(!start){
        previewVideo(false);

        scanner.stop();
        scanner.video.srcObject.getVideoTracks()[0].stop();

        return;
    }

    scanner.addListener('scan', function (content) {
        if(content.includes("gtt.to.it/cms/percorari/arrivi?palina=")){
            const searchInput = document.getElementById('searchInput');
            stopID = content.split('=').pop();

            searchInput.value = stopID;
            getStopInfo(stopID);
        }
    });

    Instascan.Camera.getCameras().then(function (cameras){

        if(cameras.length > 0){

            scanner.start(cameras[0]);
            scanner.addListener('scan', function(content) {
                previewVideo(false);    
                scanner.stop(cameras);
            });

            previewVideo(true);

        } else {
            resultsContainer.innerHTML = "{{noCamerasError}}";
        }

    }).catch(function(e){
        resultsContainer.innerHTML = "{{cameraPermissionDenied}}";
        console.error(e);
    });
}