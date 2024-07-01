@extends('layout')

@section('content')
    
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> User</h4>
        @if (Session::has('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {!! Session::get('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @foreach ($errors->all() as $error)
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ $error }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>                                      
        @endforeach
        <div class="row mb-4" id="editform">
            <div class="card" style="padding: 20px">
                <form action="{{ route('simpan') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col mb-3">
                        <label for="inputnama" class="form-label">Nama</label>
                        <input
                            type="text"
                            name="nama"
                            id="inputnama"
                            class="form-control"
                            placeholder="-"
                            required
                        />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">RFID</label>
                            <input
                            type="text"
                            name="nik"
                            id="inputnik"
                            class="form-control"
                            placeholder="-"
                            readonly
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="jenisKelamin" class="form-label">Jenis Kelamin</label>
                            <input
                            type="radio"
                            name="jk"
                            id="inputlaki"
                            value="Laki-laki"
                            class="form-check-input"
                            placeholder="-"
                            />Laki-laki
                            <input
                            type="radio"
                            name="jk"
                            id="inputperempuan"
                            value="Perempuan"
                            class="form-check-input"
                            placeholder="-"
                            />Perempuan
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="inputalamat" class="form-label">Alamat</label>
                            <input
                            type="text"
                            name="alamat"
                            id="inputalamat"
                            class="form-control"
                            placeholder="-"
                            required
                            />
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-secondary" onclick="return $('#tambahform').hide(500)">
                        Tutup
                    </button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="card" style="padding: 20px">

                <h5 class="card-header">Data Guru 
                    <div class="dt-action-buttons text-end pt-3 pt-md-0">
                        <div class="dt-buttons btn-group flex-wrap">  
                            <form action="{{ route('gantiscan') }}" method="get">
@if ($statusscan == "Y")
    
<button class="btn btn-secondary create-new btn-primary" type="submit">
    <span>
        <i class="bx bx-plus me-sm-1"></i> 
        <span class="d-none d-sm-inline-block">Buka Scan</span>
    </span>
</button> 
    
@else
<button class="btn btn-secondary create-new btn-primary" type="submit">
    <span>
        <i class="bx bx-plus me-sm-1"></i> 
        <span class="d-none d-sm-inline-block">Tutup Scan</span>
    </span>
</button> 
@endif

                            </form>
                        </div>
                    </div>
                </h5>
                <div class="table-responsive text-nowrap">
                  <table class="table table-hover" id="dataTable">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>RFID</th>
                        <th>Jenis Kelamin</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    
                  </table>
                </div>
              </div>
            </div>
        
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
        $(document).ready(function() {
            $("#editform").hide();

            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('gurujson') !!}',
                columns: [
                    {
                        data: null,
                        name: 'nomor_urut',
                        render: function(data, type, row, meta) {
                            // Mengambil nomor urut berdasarkan posisi row
                            var start = meta.settings._iDisplayStart;
                            var rowIdx = meta.row + start + 1;
                            return rowIdx;
                        }
                    },
                    { data: 'nama', name: 'nama' },
                    { data: 'nik', name: 'nik' },
                    { data: 'jenis_kelamin', name: 'jenis_kelamin' },
                    { data: 'alamat', name: 'alamat' },
                    { 
                        data: null, 
                        name: 'action',
                        render: function(data, type, row){
                            return `<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                <a class="dropdown-item editBtn" href="javascript:void(0);"
                                    ><i class="bx bx-edit-alt me-1"></i> Edit</a
                                >
                                <a class="dropdown-item delBtn" href="javascript:void(0);"
                                    ><i class="bx bx-trash me-1"></i> Delete</a
                                >
                                </div>
                            </div>`;
                        }
                     },
                    // Tambahkan kolom lain sesuai kebutuhan
                ],
                rowCallback: function(row, data, index) {
                    // Mengatur nomor urut pada kolom pertama dalam setiap baris
                    $('td:eq(0)', row).html(index + 1);
                }
            });
            $('#dataTable').on('click', '.editBtn', function() {
                var table = $('#dataTable').DataTable();
                var row = $(this).closest('tr');
                var data = table.row(row).data();
                $('#inputnama').val(data.nama);
                $('#inputnik').val(data.nik);
                if(data.jenis_kelamin === "Laki-laki"){
                    $('#inputperempuan').prop('checked', false);
                    $('#inputlaki').prop('checked', true);
                }else{
                    $('#inputlaki').prop('checked', false);
                    $('#inputperempuan').prop('checked', true);
                }
                $('#inputalamat').val(data.alamat);
                $('#editform').show(500);
            });
        });
    </script>
@endpush