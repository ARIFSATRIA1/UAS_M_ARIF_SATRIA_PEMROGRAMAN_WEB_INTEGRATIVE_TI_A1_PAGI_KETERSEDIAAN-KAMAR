<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Ketersediaan Kamar Rumah Sakit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f0f4f8;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        h2 {
            color: #555;
            margin-bottom: 15px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .result {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 20px;
            color: #333;
        }
        .result p {
            margin: 0;
            padding: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sistem Ketersediaan Kamar Rumah Sakit</h1>

        <form id="checkAvailabilityForm">
            <button type="submit">Cek Ketersediaan Kamar</button>
        </form>

        <div id="availabilityResult" class="result"></div>

        <form id="admitPatientForm">
            <h2>Masukkan Pasien</h2>
            <label for="patientName">Nama Pasien:</label>
            <input type="text" id="patientName" name="name" required>

            <label for="admissionDate">Tanggal Masuk:</label>
            <input type="date" id="admissionDate" name="admission_date" required>

            <label for="roomId">ID Kamar:</label>
            <input type="number" id="roomId" name="room_id" required>

            <label for="level">Level:</label>
            <input type="number" id="level" name="level" required>

            <button type="submit">Masukkan Pasien</button>
        </form>

        <form id="dischargePatientForm">
            <h2>Keluarkan Pasien</h2>
            <label for="hospitalizationId">ID Rawat Inap:</label>
            <input type="number" id="patientId" name="hospitalization_id" required>

            <button type="submit">Keluarkan Pasien</button>
        </form>

        <div id="operationResult" class="result"></div>
    </div>

    <script>
        document.getElementById('admitPatientForm').addEventListener('submit', function(event) {
            event.preventDefault();
            let formData = new FormData(this);
            let jsonObject = {};
            formData.forEach((value, key) => { jsonObject[key] = value; });

            jsonObject.room_id = parseInt(jsonObject.room_id);

            fetch('http://localhost:8000/api/patients/admit', {
                method: 'POST',
                body: JSON.stringify(jsonObject),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                let operationResult = document.getElementById('operationResult');
                operationResult.innerHTML = `<h2>Hasil Operasi</h2><p>${data.message}</p>`;
                checkAvailability(); // Update availability after admitting a patient
            })
            .catch(error => console.error('Error:', error));
        });

        document.getElementById('dischargePatientForm').addEventListener('submit', function(event) {
            event.preventDefault();
            let patientId = document.getElementById('patientId').value;

            fetch(`http://localhost:8000/api/patients/${patientId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                let operationResult = document.getElementById('operationResult');
                operationResult.innerHTML = `<h2>Hasil Operasi</h2><p>${data.message}</p>`;
                checkAvailability(); // Update availability after discharging a patient
            })
            .catch(error => console.error('Error:', error));
        });

        // Function to check room availability and display the result
        function checkAvailability() {
            fetch('http://localhost:8000/api/patients')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                let availabilityResult = document.getElementById('availabilityResult');
                availabilityResult.innerHTML = `<h2>Daftar Pasien Dan Kamar</h2>`;
                if (data.length > 0) {
                    data.forEach(patient => {
                        availabilityResult.innerHTML += `<p>Id: ${patient.id}, Nama: ${patient.name}, Tanggal Masuk: ${patient.admission_date}, Level: ${patient.level}</p>`;
                    });
                } else {
                    availabilityResult.innerHTML += `<p>Tidak ada pasien tersedia.</p>`;
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Automatically check room availability when the page loads
        window.onload = checkAvailability;
    </script>
</body>
</html>
