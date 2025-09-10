<?php
$specialization = "";
$doctors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["symptom"])) {
    $symptom = strtolower(trim($_POST["symptom"]));
    $conn = new mysqli("localhost", "root", "", "hospitalms");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Step 1: Get specialization for symptom
    $stmt = $conn->prepare("SELECT specialization FROM symptom_specialization WHERE LOWER(symptom) = ? LIMIT 1");
    $stmt->bind_param("s", $symptom);
    $stmt->execute();
    $stmt->bind_result($specialization);
    $stmt->fetch();
    $stmt->close();

    if ($specialization) {
        // Step 2: Get doctors from doctb
        $stmt2 = $conn->prepare("SELECT doctorname, email, docFees FROM doctb WHERE spec = ?");
        $stmt2->bind_param("s", $specialization);
        $stmt2->execute();
        $result = $stmt2->get_result();
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
        $stmt2->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <title>Find Specialist & Nearby Hospitals</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f9fc;
            padding: 40px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        input[type=text], button, select {
            padding: 10px;
            margin-top: 10px;
            width: calc(100% - 140px);
            max-width: 300px;
            margin-right: 10px;
        }
        .doctor-card {
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f4f8ff;
            max-width: 600px;
        }
        .book-btn, .map-btn {
            padding: 10px 18px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
            cursor: pointer;
        }
        .book-btn { background-color: #007bff; }
        .map-btn { background-color: #28a745; border: none; }
        .map-btn:hover { background-color: #218838; }
        #map { height: 400px; width: 100%; margin-top: 20px; border: 1px solid #ccc; }
        #manual-location { margin-top: 15px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Find a Specialist Doctor & Nearby Hospitals Based on Symptom</h2>

    <form method="post">
        <label for="symptom">Enter your symptom:</label><br>
        <input type="text" name="symptom" required placeholder="e.g. headache, cough">
        <button type="submit">Find Specialist</button>
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <hr>
        <?php if ($specialization): ?>
            <h3>Suggested Specialist: <?= htmlspecialchars($specialization) ?></h3>

            <?php if (!empty($doctors)): ?>
                <p>Available Doctors in Our Hospital:</p>
                <?php foreach ($doctors as $doc): ?>
                    <div class="doctor-card">
                        <strong>Dr. <?= htmlspecialchars($doc['doctorname']) ?></strong><br>
                        Email: <?= htmlspecialchars($doc['email']) ?><br>
                        Fee: ₹<?= htmlspecialchars($doc['docFees']) ?><br>
                        <a href="admin-panel.php?doctor=<?= urlencode($doc['doctorname']) ?>&specialization=<?= urlencode($specialization) ?>" class="book-btn">Book Appointment</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No doctor found in our hospital for <strong><?= htmlspecialchars($specialization) ?></strong>.</p>

                <!-- Show nearby hospital search only if no doctor is available -->
                <label for="distance">Search nearby hospitals within:</label>
                <select id="distance">
                    <option value="500">500 mts</option>
                    <option value="1000">1 km</option>
                    <option value="5000">5 km</option>
                    <option value="10000">10 km</option>
                    <option value="20000">20 km</option>
                </select>

                <!-- Map Search Button -->
                <button type="button" onclick="findNearby('<?= $specialization ?>')" class="map-btn">
                    Find Nearby <?= $specialization ?> Hospitals
                </button>

                <!-- Manual Location (fallback if geolocation denied) -->
                <div id="manual-location" style="display:none;">
                    <label for="locationInput">Enter your city/locality:</label>
                    <input type="text" id="locationInput" placeholder="e.g. Hyderabad, Banjara Hills">
                    <button type="button" onclick="useManualLocation('<?= $specialization ?>')" class="map-btn">Search</button>
                </div>

                <!-- Map -->
                <div id="map"></div>
            <?php endif; ?>

        <?php else: ?>
            <p>No specialization found for this symptom. Please consult a General Physician.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function findNearby(specialization) {
    const radius = document.getElementById("distance").value || 5000;

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            searchNearby(position.coords.latitude, position.coords.longitude, specialization, radius);
        }, function() {
            document.getElementById("manual-location").style.display = "block";
            alert("Location access denied. Please enter your city/locality.");
        });
    } else {
        document.getElementById("manual-location").style.display = "block";
        alert("Geolocation not supported. Please enter your city/locality.");
    }
}

function useManualLocation(specialization) {
    const radius = document.getElementById("distance").value || 5000;
    const address = document.getElementById("locationInput").value;

    if (!address) { alert("Please enter a location."); return; }

    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: address }, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
            const lat = results[0].geometry.location.lat();
            const lng = results[0].geometry.location.lng();
            searchNearby(lat, lng, specialization, radius);
        } else {
            alert("Could not find location. Please try again.");
        }
    });
}

function searchNearby(lat, lng, specialization, radius) {
    const userLocation = { lat: lat, lng: lng };
    const map = new google.maps.Map(document.getElementById('map'), {
        center: userLocation,
        zoom: 14
    });

    new google.maps.Marker({
        position: userLocation,
        map: map,
        title: "You are here"
    });

    const request = {
        location: userLocation,
        radius: parseInt(radius),
        keyword: specialization + " hospital"
    };

    const service = new google.maps.places.PlacesService(map);
    service.nearbySearch(request, function(results, status) {
        if (status === google.maps.places.PlacesServiceStatus.OK) {
            results.forEach(function(place) {
                new google.maps.Marker({
                    map: map,
                    position: place.geometry.location,
                    title: place.name
                });
            });
        } else {
            alert("No nearby results found.");
        }
    });
}
</script>
</body>
</html>
