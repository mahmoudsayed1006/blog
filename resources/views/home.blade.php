<!DOCTYPE html>
<html lang="en">

<head>
    <title>Laravel 8 Firebase Web Push Notification Tutorial</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>

    <div class="container" style="margin-top:50px;">

        <div style="text-align: center;">

            <h4>Laravel 8 Firebase Web Push Notification Tutorial</h4>

            <button id="btn-nft-enable" onclick="initFirebaseMessagingRegistration()"
                class="btn btn-danger btn-xs btn-flat">Click here - Allow Notification</button>
        </div>

        <form action="{{ route('push-notificaiton') }}" method="post">
            @csrf

            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" class="form-control" id="title" placeholder="Notification Title" name="title">
            </div>
            <div class="form-group">
                <label for="body">body:</label>
                <input type="text" class="form-control" id="body" placeholder="Notification Body" name="body">
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.23.0/firebase.js"></script>
    <script>
    var firebaseConfig = {
        apiKey: "AIzaSyCfW5lWJ7E4Ae0iqCMv1d6Qzswynr9HVv8",
  authDomain: "laravelnotification-1b01e.firebaseapp.com",
  projectId: "laravelnotification-1b01e",
  storageBucket: "laravelnotification-1b01e.appspot.com",
  messagingSenderId: "1007896237749",
  appId: "1:1007896237749:web:ad01fceb9415b73d85d0fc",
  measurementId: "G-P0FFG0BVLB"
    };

    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    function initFirebaseMessagingRegistration() {
        messaging
            .requestPermission()
            .then(function() {
                console.log("token", messaging.getToken())
                return messaging.getToken()
            })
            .then(function(token) {
                console.log(token);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });

                $.ajax({
                    url: '{{ route("store.token") }}',
                    type: 'POST',
                    data: {
                        token: token
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        alert('Token saved successfully.');
                    },
                    error: function(err) {
                        console.log('User Chat Token Error' + err);
                    },
                });

            }).catch(function(err) {
                console.log('User Chat Token Error' + err);
            });
    }

    messaging.onMessage(function(payload) {
        const noteTitle = payload.notification.title;
        const noteOptions = {
            body: payload.notification.body,
            icon: payload.notification.icon,
        };
        new Notification(noteTitle, noteOptions);
    });
    </script>
</body>

</html>