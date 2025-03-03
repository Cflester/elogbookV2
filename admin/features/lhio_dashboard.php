<?php

require '../../config/kon.php';
require '../../vendor/autoload.php';
require '../sidebar/index.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Load the existing spreadsheet
$inputFileName = 'C:\\xampp\\htdocs\\elogbook\\phpspreadsheet\\elogbook.xlsx'; // Path to your Excel file
$spreadsheet = IOFactory::load($inputFileName);

// Get the names of the sheets
$sheetNames = $spreadsheet->getSheetNames();

// Prepare data array for the active sheet
$activeSheet = $spreadsheet->getActiveSheet();
$highestRow = $activeSheet->getHighestRow();
$data = [];

for ($row = 2; $row <= $highestRow; $row++) { // Assuming the first row is headers
    $data[] = [
        'provider' => $activeSheet->getCell('A' . $row)->getValue(),
        'pmcc' => $activeSheet->getCell('B' . $row)->getValue(),
        'received' => $activeSheet->getCell('C' . $row)->getValue(),
        'transmitted' => $activeSheet->getCell('D' . $row)->getValue(),
        'posted' => $activeSheet->getCell('E' . $row)->getValue(),
        'turnaroundWorking' => $activeSheet->getCell('F' . $row)->getValue(),
        'turnaroundAQAS' => $activeSheet->getCell('G' . $row)->getValue(),
    ];
}

// Fetch submitted data for the logged-in LHIO account
//$lhio_id = $_SESSION['lhio_id'];
$stmt = $conn->prepare("SELECT provider_name, pmcc_no, transmitted_date FROM pending_submission WHERE lhio_id = ?");
$stmt->bind_param("i", $lhio_id);
$stmt->execute();
$result = $stmt->get_result();

$submittedData = [];
while ($row = $result->fetch_assoc()) {
    $submittedData[] = $row;
}
$stmt->close();

// Convert submitted data to JSON format for use in JavaScript
$jsonSubmittedData = json_encode($submittedData);
$jsonData = json_encode($data); // Existing data for the spreadsheet

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eLog Book Monitoring</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    <div class="container mt-4">
        <div class="avatar-container text-center mb-4">
            <img src="path/to/avatar.png" class="avatar rounded-circle" alt="Avatar" style="width: 100px; height: 100px;">
            <!-- <h5 class="username"><?php echo htmlspecialchars($_SESSION['role']); ?></h5> -->
        </div>

        <div class="main-content">
            <header class="mb-4">
                <h1>Data Overview</h1>
                <div class="input-group mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search..." onkeyup="filterTable()">
                </div>
            </header>

            <button class="btn btn-primary mb-3" id="openModalBtn" onclick="openModal()">Add New Entry</button>

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Name of Health Care Provider</th>
                            <th>PMCC# (unique#)</th>
                            <th>Date Transmitted</th>
                            <th>Date Received</th>
                            <th>Date Posted (AQAS)</th>
                            <th>Turnaround Time (Working Days)</th>
                            <th>Turnaround Time (AQAS)</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody">
                        <!-- Data rows will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <button class="btn btn-secondary" id="prevBtn" onclick="changePage(-1)">Previous</button>
                <span id="pageInfo"></span>
                <button class="btn btn-secondary" id="nextBtn" onclick="changePage(1)">Next</button>
            </div>
        </div>

        <!-- Modal Structure -->
        <div id="modal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Entry</h5>
                        <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="dataForm" action="../handler/submit.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" id="lhio_id" name="lhio_id" value="<?php echo htmlspecialchars($_SESSION['lhio_id']); ?>"> 

                            <div class="form-group">
                                <label for="provider">Name of Health Care Provider:</label>
                                <input type="text" id="provider" name="provider" class="form-control" required autocomplete="off">
                                <div id="providerList" class="border" style="display:none; max-height:150px; overflow-y:auto;"></div>
                            </div>

                            <div class="form-group">
                                <label for="pmcc">PMCC# (unique#):</label>
                                <input type="text" id="pmcc" name="pmcc" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="upload">Type of Application:</label>
                                <input type="text" id="upload" name="upload" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="file">Attach Files:</label>
                                <input type ="file" id="file" name="files[]" class="form-control-file" accept="*/*" multiple required>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const submittedData = <?php echo $jsonSubmittedData; ?>; // Load submitted data from PHP
            const data = <?php echo $jsonData; ?>; // Load data from PHP
            let currentPage = 1;
            const rowsPerPage = 10;

            function displayData(filteredData) {
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const paginatedData = filteredData.slice(start, end);
                const tableBody = document.querySelector("#dataTableBody");
                tableBody.innerHTML = "";

                paginatedData.forEach(item => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${item.provider_name || 'Pending'}</td>
                        <td>${item.pmcc_no || 'Pending'}</td>
                        <td>${item.transmitted_date || 'Pending'}</td>
                        <td>${item.received || 'Pending'}</td>
                        <td>${item.posted || 'Pending'}</td>
                        <td>${item.turnaroundWorking || 'Pending'}</td>
                        <td>${item.turnaroundAQAS || 'Pending'}</td>
                    `;
                    tableBody.appendChild(row);
                });

                document.getElementById("pageInfo").innerText = `Page ${currentPage} of ${Math.ceil(filteredData.length / rowsPerPage)}`;
                document.getElementById("prevBtn").disabled = currentPage === 1;
                document.getElementById("nextBtn").disabled = end >= filteredData.length;
            }

            function changePage(direction) {
                currentPage += direction;
                displayData(submittedData);
            }

            function openModal() {
                $('#modal').modal('show');
            }

            function closeModal() {
                $('#modal').modal('hide');
            }

            // Submission
            document.getElementById('dataForm').onsubmit = function(event) {
                event.preventDefault();
                
                const newRow = {
                    lhio_id: document.getElementById('lhio_id').value,
                    provider: document.getElementById('provider').value,
                    pmcc: document.getElementById('pmcc').value,
                    application_type: document.getElementById('upload').value,
                    files: document.getElementById('file').files
                };

                const formData = new FormData();
                for (const key in newRow) {
                    if (key === 'files') {
                        for (let i = 0; i < newRow[key].length; i++) {
                            formData.append('files[]', newRow[key][i]);
                        }
                    } else {
                        formData.append(key, newRow[key]);
                    }
                }

                fetch('../handler/submit.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    closeModal();
                    document.getElementById('dataForm').reset();
                    submittedData.push(newRow);
                    displayData(submittedData);
                })
                .catch(error => console.error('Error:', error));
            }

            function filterTable() {
                const searchInput = document.getElementById('searchInput').value.toLowerCase();
                const filteredData = submittedData.filter(item => {
                    return Object.values(item).some(value => 
                        value.toString().toLowerCase().includes(searchInput)
                    );
                });
                currentPage = 1;
                displayData(filteredData);
            }

            // Initial display of data
            displayData(submittedData);

            // Search provider
            $(document).ready(function() {
                $('#provider').keyup(function() {
                    let query = $(this).val();
                    if (query != '') {
                        $.ajax({
                            url: "../handler/search_provider.php",
                            method: "POST",
                            data: {query: query},
                            success: function(data) {
                                $('#providerList').fadeIn();
                                $('#providerList').html(data);
                            }
                        });
                    } else {
                        $('#providerList').fadeOut();
                    }
                });

                $(document).on('click', '.provider-item', function() {
                    let providerName = $(this).data('name');
                    let pmccNo = $(this).data('pmcc');

                    $('#provider').val(providerName);
                    $('#pmcc').val(pmccNo);
                    $('#providerList').fadeOut();
                });
            });
        </script>
    </div>
</body>
</html>