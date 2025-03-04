<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>DataTables Example</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
        .dataTables_wrapper .dataTables_length, 
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 10px;
        }
        table.dataTable thead th {
            background-color: #f8f9fc;
            color: #4e73df;
            text-align: left;
        }
        table.dataTable tfoot th {
            background-color: #f8f9fc;
            color: #4e73df;
            text-align: left;
        }
        .pagination .page-item.active .page-link {
            background-color: #4e73df;
            border-color: #4e73df;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Health Care Provider</th>
                                <th>PMCC</th>
                                <th>Date Transmitted</th>
                                <th>Date Received</th>
                                <th>Date Transmitted</th>
                                <th>Date Received</th>
                                <th>Date Posted (AQAS)</th>
                                <th>Turnaround Time (Working Days)</th>
                                <th>Turnaround Time (AQAS)</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Health Care Provider</th>
                                <th>PMCC</th>
                                <th>Date Transmitted</th>
                                <th>Date Received</th>
                                <th>Date Transmitted</th>
                                <th>Date Received</th>
                                <th>Date Posted (AQAS)</th>
                                <th>Turnaround Time (Working Days)</th>
                                <th>Turnaround Time (AQAS)</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr>
                                <td>Airi Satou</td>
                                <td>Accountant</td>
                                <td>Tokyo</td>
                                <td>33</td>
                                <td>2008/11/28</td>
                                <td>$162,700</td>
                                <td>$162,700</td>
                                <td>$162,700</td>
                                <td>$162,700</td>
                            </tr>
                            <tr>
                            <td>Airi Satou</td>
                                <td>Accountant</td>
                                <td>Tokyo</td>
                                <td>33</td>
                                <td>2008/11/28</td>
                                <td>$162,700</td>
                                <td>$162,700</td>
                                <td>$162,700</td>
                                <td>$162,700</td>
                            </tr>
                            <tr>
                            <td>Airi Satou</td>
                                <td>Accountant</td>
                                <td>Tokyo</td>
                                <td>33</td>
                                <td>2008/11/28</td>
                                <td>$162,700</td>
                                <td>$162,700</td>
                                <td>$162,700</td>
                                <td>$162,700</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "paging": true,
                "language": {
                    "lengthMenu": "Show _MENU_ entries",
                    "search": "Search:",
                }
            });
        });
    </script>
</body>
</html>
