@extends('kepalasekolah')

@section('contentkepsek')
    
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Kelas</h4>
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

        <div class="row">
            <div class="card" style="padding: 20px">

                <h5 class="card-header">Data Guru 
                    <div class="dt-action-buttons text-end pt-3 pt-md-0">
                        <div class="dt-buttons btn-group flex-wrap">  
                            {{-- <button class="btn btn-secondary create-new btn-primary" tabindex="0" type="button" id="tampilform">
                                <span>
                                    <i class="bx bx-plus me-sm-1"></i> 
                                    <span class="d-none d-sm-inline-block">Tambah Data</span>
                                </span>
                            </button>  --}}
                        </div>
                    </div>
                </h5>
                <div class="table-responsive text-nowrap">
                  <table class="table table-hover" id="dataTable">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Guru</th>
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
            $("#tambahform").hide();
            $('#tampilform').on('click', function() {
                $("#tambahform").show(500);
            });
            
            
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('kelasjson') !!}',
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
                    { data: 'nama_mapel', name: 'nama_mapel' },
                    { data: 'hari', name: 'hari' },
                    { 
                        data: 'jam', 
                        name: 'jam',
                        render: function(data, type, row) {
                            var jam = '';
                            if (data == '07') {
                                jam = '07.50-09.50';
                            } else if (data == '10') {
                                jam = '10.10-11.30';
                            } else if (data == '11') {
                                jam = '11.30-12.50';
                            }
                            return jam;
                        }
                     },
                    { data: 'gurus.nama', name: 'gurus.nama' },
                    { 
                        data: null, 
                        name: 'action',
                        render: function(data, type, row){
                            return `<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                <a class="dropdown-item editBtn" href="grafikdetail/${row.id}"
                                    ><i class="bx bx-edit-alt me-1"></i> Grafik</a
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
        });
    </script>
@endpush