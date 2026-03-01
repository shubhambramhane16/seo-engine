@extends('admin.layout.default')

@section('enquiry-master', 'active menu-item-open')

@section('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.1/daterangepicker.min.css" />
@endsection

@section('content')
    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-3 pb-0">
            <div class="card-title">
                <h3 class="card-label">Enquiry List</h3>
            </div>
            <div class="card-toolbar">
                @include('admin.layout.partials.filters.common-filter')
                <!--begin::Button-->
                <a href="{{ url('/admin/enquiry/import') }}" class="btn btn-light-primary font-weight-bolder">
                    <span class="svg-icon svg-icon-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24"
                            version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24" />
                                <path
                                    d="M19,11 L18,11 L18,4.00990796 C18,2.89936801 17.1017742,2 16.0099079,2 L3.99009204,2 C2.89102257,2 2,2.88772962 2,4.00990796 L2,19.990092 C2,21.100632 2.89822582,22 3.99009204,22 L16.0099079,22 C17.1089774,22 18,21.1122704 18,19.990092 L18,13 L19,13 L19,19.990092 C19,21.100632 19.8982258,22 20.990092,22 C22.1089364,22 23,21.1122704 23,19.990092 L23,4.00990796 C23,2.89936801 22.1017742,2 21.0099079,2 L21,2 C19.8954284,2 19,2.88772962 19,4.00990796 L19,11 Z M8,14 L8,16 L16,16 L16,14 L8,14 Z M8,9 L8,11 L16,11 L16,9 L8,9 Z"
                                    fill="#000000" />
                            </g>
                        </svg>
                    </span>Import
                </a>
                &nbsp;
                <a href="{{ url('/admin/enquiry/sync') }}" class="btn btn-primary font-weight-bolder">
                    <i class="la la-sync"></i>Sync</a>
                &nbsp;

                <a href="{{ url('/admin/enquiry/add') }}" class="btn btn-primary font-weight-bolder">
                    <i class="la la-plus"></i>Add Enquiry</a>
                <!--end::Button-->
            </div>
        </div>

        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-6">
                <div class="col-lg-3">
                    <label>Status</label>
                    <select id="statusFilter" class="form-control">
                        <option value="-1">All Status</option>
                        <option value="0">InActive</option>
                        <option value="1">Active</option>
                    </select>
                </div>

                <div class="col-lg-4">
                    <label>Date Range</label>
                    <input type="text" id="dateRange" class="form-control" readonly placeholder="Select date range..." />
                </div>

                <div class="col-lg-5 align-self-end">
                    <button id="btnFilter" class="btn btn-success mr-3">Filter</button>
                    <button id="btnReset" class="btn btn-light">Reset</button>
                </div>
            </div>

            <!-- Table -->
            <table class="table table-bordered table-hover" id="enquiryTable">
                <thead>
                    <tr>
                        <th class="custom_sno">SNo.</th>
                        <th>Enquiry Name</th>
                        <th>Number</th>
                        <th>City</th>
                        <th>Locality</th>
                        <th>Item Reference</th>
                        <th>Form</th>
                        <th>Query</th>
                        <th>Created At</th>
                        <th class="custom_status">Status</th>
                        <th class="custom_action">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection
@section('styles')
    <!-- <link href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> -->
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection


{{-- Scripts Section --}}
@section('scripts')
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.1/daterangepicker.min.js"></script>

    <script>
        var table;

        $(document).ready(function() {

            // Date range picker
            $('#dateRange').daterangepicker({
                opens: 'left',
                locale: {
                    format: 'DD-MM-YYYY'
                }
            });

            table = $('#enquiryTable').DataTable({
                processing: true,
                serverSide: true,
                scrollY: '500px',
                scrollCollapse: true,
                paging: true,

                ajax: {
                    url: "{{ url('admin/enquiry/list') }}", // ← fixed with named route
                    type: "GET",
                    data: function(d) {
                        let minimal = {
                            draw: d.draw,
                            start: d.start,
                            length: d.length,
                            search: d.search ? {
                                value: d.search.value
                            } : {
                                value: ''
                            },
                            order: d.order || [],
                            status: $('#statusFilter').val(),
                            start_date: '',
                            end_date: ''
                        };

                        var range = $('#dateRange').val();
                        if (range) {
                            var dates = range.split(' - ');
                            minimal.start_date = moment(dates[0], 'DD-MM-YYYY').format('YYYY-MM-DD');
                            minimal.end_date = moment(dates[1], 'DD-MM-YYYY').format('YYYY-MM-DD');
                        }

                        return minimal;

                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },

                columns: [{
                        data: 'counter',
                        name: 'counter',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'number',
                        name: 'number'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'locality',
                        name: 'locality'
                    },
                    {
                        data: 'item_reference',
                        name: 'item_reference'
                    },
                    {
                        data: 'form',
                        name: 'form'
                    },
                    {
                        data: 'query',
                        name: 'query'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],

                pageLength: 20,
                lengthMenu: [
                    [10, 20, 50, 100, -1],
                    [10, 20, 50, 100, "All"]
                ],
                // order: [
                //     [8, 'desc']
                // ],
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x text-primary"></i> Loading...'
                },
                drawCallback: function() {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            $('#btnFilter').on('click', function() {
                table.ajax.reload();
            });

            $('#btnReset').on('click', function() {
                $('#statusFilter').val('-1');
                $('#dateRange').val('');
                table.ajax.reload();
            });

            $('#statusFilter').on('change', function() {
                table.ajax.reload();
            });
        });

        function changeStatus(el) {
            var url = $(el).data('url');
            if (confirm('Are you sure you want to change the status?')) {
                // For now redirect (as original)
                window.location.href = url;

                // Uncomment for AJAX status change (better UX)
                /*
                $.post(url, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, function() {
                    table.ajax.reload(null, false); // reload without reset page
                }).fail(function() {
                    alert('Failed to update status');
                });
                */
            }
        }
    </script>
@endsection
