@extends('admin_master')

@section('admin')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Timesheet </a></li>
                                <li class="breadcrumb-item active">All Employees Timesheet</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Timesheet</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Employees Data</h4>

                            <h5>Filter By Date Range</h5>
                            <div class="mb-3 d-flex align-items-center">
                                <label for="startDate" class="me-2">Start Date:</label>
                                <input type="date" id="startDate" class="form-control w-auto me-3">

                                <label for="endDate" class="me-2">End Date:</label>
                                <input type="date" id="endDate" class="form-control w-auto me-3">

                                <button id="filterByDate" class="btn btn-primary me-2">Filter</button>
                                <button id="resetFilter" class="btn btn-secondary">Reset</button>
                            </div>

                            <table id="basic-datatables" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>SN</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Gmail</th>
                                        <th>Clock In Time</th>
                                        <th>Clock Out Time</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($timesheet as $sheet)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $sheet->getEmployee->firstname }} {{ $sheet->getEmployee->lastname }}
                                            </td>
                                            <td>{{ $sheet->getEmployee->org_email }}</td>
                                            <td>{{ $sheet->getEmployee->email }}</td>
                                            <td>
                                                {{ $sheet->clock_in_time ? \Carbon\Carbon::parse($sheet->clock_in_time)->format('g:i A') : '-' }}
                                            </td>
                                            <td>
                                                {{ $sheet->clock_out_time ? \Carbon\Carbon::parse($sheet->clock_out_time)->format('g:i A') : '-' }}
                                            </td>
                                            <td data-order="{{ $sheet->date }}" data-search="{{ $sheet->date }}">
                                                {{ \Carbon\Carbon::parse($sheet->date)->format('jS, M Y') }}
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('backend/assets/js/jquery.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#basic-datatables').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'print',
                    text: '🖨️ Print',
                    className: 'btn btn-primary'
                }, {
                    extend: 'excelHtml5',
                    text: '📁 Excel',
                    className: 'btn btn-success'
                }, {
                    extend: 'csvHtml5',
                    text: '📄 CSV',
                    className: 'btn btn-dark'
                }],
            });


            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var startDate = $('#startDate').val();
                    var endDate = $('#endDate').val();
                    var recordDate = $(table.row(dataIndex).node()).find('td:last').data('order');



                    if (!recordDate) return false; // skip if no date

                    if ((startDate === '' || recordDate >= startDate) &&
                        (endDate === '' || recordDate <= endDate)) {
                        return true;
                    }
                    return false;
                }
            );

            // Event listener for the "Filter" button
            $('#filterByDate').click(function() {
                table.draw();
            });

            $('#startDate, #endDate').on('change', function() {
                table.draw();
            });

            $('#resetFilter').click(function() {
                $('#startDate').val('');
                $('#endDate').val('');
                table.draw();
            });


        });
    </script>
@endsection
