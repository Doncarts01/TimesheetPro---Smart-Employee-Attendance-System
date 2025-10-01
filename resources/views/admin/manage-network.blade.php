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
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Network </a></li>
                                <li class="breadcrumb-item active">Settings</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Network Settings</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Office Network Settings</h4>
                            <table id="basic-datatables" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>WIFI Name</th>
                                        <th>IP Addrress</th>
                                        <th>MAC Address (BSSID)</th>
                                        <th>Last Updated</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>


                                <tbody>
                                        <tr>
                                            <td>{{ $network->network_name }}</td>
                                            <td>{{ $network->ip_address }}</td>
                                            <td>{{ $network->bssid }}</td>
                                            <td>{{ \Carbon\Carbon::parse($network->updated_at)->format('jS, M Y') }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#editModal-{{ $network->id }}">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>


                                        <div class="modal fade" id="editModal-{{ $network->id }}" tabindex="-1"
                                            aria-labelledby="editModalLabel-{{ $network->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editModalLabel-{{ $network->id }}">
                                                            Edit Network Settings</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route("admin_update_settings") }}"
                                                            id="updateEmployee" method="POST">
                                                            @csrf
                                                            <div class="row">
                                                                <input type="hidden" name="network_id"
                                                                    value="{{ $network->id }}">

                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="field-1"
                                                                            class="form-label">WIFI Name</label>
                                                                        <input type="text" class="form-control"
                                                                            id="field-1"
                                                                            value="{{ $network->network_name }}"
                                                                            name="network_name">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="field-2"
                                                                            class="form-label">IP Address</label>
                                                                        <input type="text" class="form-control"
                                                                            id="field-2"
                                                                            value="{{ $network->ip_address }}"
                                                                            name="ip_address">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="mb-3">
                                                                        <label for="field-3"
                                                                            class="form-label">MAC
                                                                            Address</label>
                                                                        <input type="text" class="form-control"
                                                                            id="field-3"
                                                                            value="{{ $network->bssid }}"
                                                                            name="bssid">
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
    <script>
        $(document).ready(function() {

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
