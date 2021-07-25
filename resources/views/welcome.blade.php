<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel + AWS Rekognition</title>
    <!-- <script type="text/javascript" src="https://unpkg.com/webcam-easy/dist/webcam-easy.min.js"></script> -->
    <script type="text/javascript" src="{{ asset('js/webcam-easy.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<body>

<div class="container">

    <div class="jumbotron">
        <h3>Image Recognition SDK Integration</h3>
        <p>This project demonstrates the integration of the Image Recognition and Verification</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <div class="form-group">{{ session('success') }}</div>
            <a href="/" class="btn btn-success">Try Again</a>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <div class="form-group">{{ session('error') }}</div>
        </div>
    @endif

    @if(isset($results))
        {{ dd($results) }}
    @else
        <form action="" method="post" enctype="multipart/form-data">
            @csrf
            <!-- <div class="form-group">
                <button class="btn btn-sm btn-primary" type="button" onclick="triggerCam()">Use Webcam</button>
                <button class="btn btn-sm btn-dark" type="button" onclick="flip()">Flip</button>
                <button class="btn btn-sm btn-success" type="button" onclick="snap()">Snap</button>
                <button class="btn btn-sm btn-danger" type="button" onclick="stop()">Stop</button>
            </div> -->
            <div class="form-group">
                <label for="photo">Upload a Photo 1 (A very old picture of yours)</label>
                <input type="file" name="photo1" id="photo1" accept="image/" class="form-control" onchange="previewImg('photo1_preview')">
            </div>
            <div class="form-group">
                <label for="photo">Capture photo</label>
                <!-- <input type="file" name="photo2" id="photo2" accept="image/" class="form-control" onchange="previewImg('photo2_preview')"> -->
                <input type="hidden" name="photo2" id="photo2" class="form-control">
                <button class="btn btn-sm btn-primary" type="button" onclick="triggerCam()">Use Webcam</button>
                <button class="btn btn-sm btn-dark" type="button" onclick="flip()">Flip</button>
                <button class="btn btn-sm btn-success" type="button" onclick="snap()">Snap</button>
                <button class="btn btn-sm btn-danger" type="button" onclick="stop()">Stop</button>
            </div>
            <div class="form-group">
                <input type="submit" value="Submit" class="btn btn-success btn-lg">
            </div>
        </form>

        <div class="row">
                <div class="col-sm-4">
                    <video id="webcam" autoplay playsinline width="640" height="480"></video>
                    <canvas id="canvas" class="d-none"></canvas>
                    <!-- <audio id="snapSound" src="audio/snap.wav" preload = "auto"></audio> -->
                </div>
                <div class="col-sm-4">
                    <img id="photo1_preview"  width="100%" class="img-responsive"/>
                </div>
                <div class="col-sm-4">
                    <img id="photo2_preview"  width="100%" class="img-responsive"/>
                </div>
        </div>
    @endif

</div>

<script>
    function previewImg(placeholder){
        $('#' + placeholder).attr('src', URL.createObjectURL(event.target.files[0]));
    }

    const webcamElement = document.getElementById('webcam');
    const canvasElement = document.getElementById('canvas');
    const snapSoundElement = document.getElementById('snapSound');
    const webcam = new Webcam(webcamElement, 'user', canvasElement, snapSoundElement);
    const _webcamList = [];

    function triggerCam() {
        webcam.start()
            .then(result =>{
                console.log("webcam started");
            })
            .catch(err => {
                console.log(err);
            });
    }

    function errorCallback(error) {
        console.log(error)
    }

    function flip() {
        webcam.flip();
        webcam.start();  
    }

    function snap() {
        let picture = webcam.snap();
        $('#photo2').val(picture);
    }

    function stop() {
        webcam.stop();
    }
</script>

</body>
</html>
