<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Payroll</title>
        <script>
            // Load API data and populate the table
            async function loadData() {
                const response = await fetch('/api/payroll', {
                    headers: {
                        'Authorization': 'Bearer {{env('SANCTUM_API_TOKEN')}}'
                    }
                });

                const data = await response.json();

                const tbody = document.querySelector('#table tbody');
                tbody.innerHTML = '';

                data.forEach(row => {
                    const tr = document.createElement('tr');

                    tr.innerHTML = `
                        <td>${row.month}</td>
                        <td>${row.salaryPaymentDate}</td>
                        <td>${row.bonusPaymentDate}</td>
                    `;

                    tbody.appendChild(tr);
                });
            }

            // Download CSV file from API
            async function downloadCsv() {
                const response = await fetch('/api/payroll/exporter', {
                    headers: {
                        'Authorization': 'Bearer {{env('SANCTUM_API_TOKEN')}}'
                    }
                });

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);

                const a = document.createElement('a');
                a.href = url;
                a.download = 'payroll.csv';
                a.click();
            }
        </script>
    </head>
    <body>
        <h1>Payroll Overview</h1>

        <button onclick="loadData()">Load Payroll</button>
        <button onclick="downloadCsv()">Download CSV</button>

        <table border="1" id="table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Salary</th>
                    <th>Bonus</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </body>
</html>
