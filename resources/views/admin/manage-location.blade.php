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
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Location </a></li>
                                <li class="breadcrumb-item active">Settings</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Location Settings</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Office Location Settings</h4>
                            <table id="basic-datatables" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Office Radius (in meters)</th>
                                        <th>Last Updated</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>


                                <tbody>
                                        <tr>
                                            <td>{{ $network->lat }}</td>
                                            <td>{{ $network->lon }}</td>
                                            <td>{{ $network->meters_allowed }}</td>
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
                                                            Edit Location Settings</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route("admin_update_location_settings") }}"
                                                            id="updateEmployee" method="POST">
                                                            @csrf
                                                            <div class="row">
                                                                <input type="hidden" name="network_id"
                                                                    value="{{ $network->id }}">

                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="field-1"
                                                                            class="form-label">Office Latitude</label>
                                                                        <input type="text" class="form-control"
                                                                            id="lat"
                                                                            value="{{ $network->lat }}"
                                                                            name="lat">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="field-2"
                                                                            class="form-label">Office Longitude</label>
                                                                        <input type="text" class="form-control"
                                                                            id="lon"
                                                                            value="{{ $network->lon }}"
                                                                            name="lon">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="mb-3">
                                                                        <label for="field-3"
                                                                            class="form-label">Office Radius (in meters)</label>
                                                                        <input type="number" class="form-control"
                                                                            id="field-3"
                                                                            value="{{ $network->meters_allowed }}"
                                                                            name="meters_allowed">
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
    <script>
    $(function () {
        function isValidLatLon(value) {
            // Regex: optional minus, up to 2 digits before decimal, and up to 6 digits after
            let regex = /^-?\d{1,2}(\.\d{1,7})?$/;
            return regex.test(value);
        }

        function sanitizeInput(input) {
            let value = $(input).val();
            if (value !== '' && !isValidLatLon(value)) {
                // Strip invalid characters (fallback)
                let cleaned = value.match(/^-?\d{0,2}(\.\d{0,6})?/);
                $(input).val(cleaned ? cleaned[0] : '');
            }
        }

        // Handle typing
        $('#lat, #lon').on('input', function () {
            sanitizeInput(this);
        });

        // Handle pasting
        $('#lat, #lon').on('paste', function (e) {
            let pasteData = (e.originalEvent || e).clipboardData.getData('text');
            if (!isValidLatLon(pasteData)) {
                e.preventDefault();
                alert("Invalid format. Use format like 6.713363 or -1.234567");
            }
        });
    });
</script>
@endsection
