@extends('admin_master')


@section('admin')
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Create New Employee </a></li>
                                <li class="breadcrumb-item active">All Employees</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Employees</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">


                <div class="col-6">
                    <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog"
                        aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Create New Employee</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <form action="{{ route('admin_store_employees') }}" id="addEmployee" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="field-1" class="form-label">Firstname</label>
                                                    <input type="text" class="form-control" id="field-1"
                                                        placeholder="John" name="firstname">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="field-2" class="form-label">Lastname</label>
                                                    <input type="text" class="form-control" id="field-2"
                                                        placeholder="Doe" name="lastname">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="field-3" class="form-label">Organization Email
                                                        Address</label>
                                                    <input type="email" class="form-control" id="field-3"
                                                        placeholder="email address" name="org_email">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="field-4" class="form-label">Gmail Address</label>
                                                    <input type="email" class="form-control" id="field-4"
                                                        placeholder="gmail address" name="email">
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary waves-effect"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" form="addEmployee" name="addEmployee"
                                        class="btn btn-info waves-effect waves-light">Create</button>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.modal -->


                    <div class="button-list">
                        <!-- Responsive modal -->
                        <button type="button" class="btn btn-success waves-effect waves-light"
                            data-bs-toggle="modal"data-bs-target="#con-close-modal">Create New Employee</button>
                    </div>
                </div>













                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Employees Data ({{ $emp_count }})</h4>
                            <table id="basic-datatables" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Firstname</th>
                                        <th>Lastname</th>
                                        <th>Org. Email</th>
                                        <th>Gmail</th>
                                        <th>Date Created</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>


                                <tbody>
                                    @foreach ($employees as $employee)
                                        <tr>
                                            <td>{{ $employee->firstname }}</td>
                                            <td>{{ $employee->lastname }}</td>
                                            <td>{{ $employee->org_email ? $employee->org_email : 'N/A' }}</td>
                                            <td>{{ $employee->email }}</td>
                                            <td>{{ \Carbon\Carbon::parse($employee->created_at)->format('jS, M Y') }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#editModal-{{ $employee->id }}">
                                                    Edit
                                                </button>
                                                <a href="{{ route('admin_delete_employees', base64_encode($employee->id)) }}"
                                                    id="delete" class="btn btn-sm btn-danger">Delete</a>
                                            </td>
                                        </tr>





                                        <div class="modal fade" id="editModal-{{ $employee->id }}" tabindex="-1"
                                            aria-labelledby="editModalLabel-{{ $employee->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editModalLabel-{{ $employee->id }}">
                                                            Edit Employee {{ $employee->firstname }}
                                                            {{ $employee->lastname }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('admin_update_employees') }}"
                                                            id="updateEmployee" method="POST">
                                                            @csrf
                                                            <div class="row">
                                                                <input type="hidden" name="employee_id"
                                                                    value="{{ $employee->id }}">

                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="field-1"
                                                                            class="form-label">Firstname</label>
                                                                        <input type="text" class="form-control"
                                                                            id="field-1"
                                                                            value="{{ $employee->firstname }}"
                                                                            name="firstname">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="field-2"
                                                                            class="form-label">Lastname</label>
                                                                        <input type="text" class="form-control"
                                                                            id="field-2"
                                                                            value="{{ $employee->lastname }}"
                                                                            name="lastname">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="mb-3">
                                                                        <label for="field-3"
                                                                            class="form-label">Organization Email
                                                                            Address</label>
                                                                        <input type="email" class="form-control"
                                                                            id="field-3"
                                                                            value="{{ $employee->org_email }}"
                                                                            name="org_email">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="mb-3">
                                                                        <label for="field-4" class="form-label">Gmail
                                                                            Address</label>
                                                                        <input type="email" class="form-control"
                                                                            id="field-4" value="{{ $employee->email }}"
                                                                            name="email">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" form="updateEmployee"
                                                            name="updateEmployee"
                                                            class="btn btn-info waves-effect waves-light">Save
                                                            changes</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </tbody>
                            </table>

                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div><!-- end col-->
            </div>
            <!-- end row-->

        </div> <!-- container -->

    </div> <!-- content -->


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


            $('#image').change(function(e) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#showImage').attr('src', e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
            });


            $('#basic-datatables').DataTable();
        });
    </script>
@endsection
