@extends('kepalasekolah')

@section('contentkepsek')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Kelas</h4>
  <div class="row">
    <div class="card" style="padding: 20px">
      <div class="table-responsive text-nowrap">
        <table class="table table-hover" id="dataTable">
          <thead>
            <tr>
              <td>Mata Pelajaran :</th>
              <th>{{ $detail->kelas->nama_mapel }}</th>
            </tr>
            <tr>
              <td>Hari :</th>
              <th>{{ $detail->kelas->hari }}</th>
            </tr>
            <tr>
              <td>Jam :</th>
              <th>
                @if ($detail->kelas->jam == '07')
                  07.50-09.50
                @elseif ($detail->kelas->jam == '10')
                  10.10-11.30
                @elseif ($detail->kelas->jam == '11')
                  11.30-12.50    
                @endif
              </th>
            </tr>
            <tr>
              <td>Guru :</th>
              <th>{{ $detail->kelas->gurus->nama }}</th>
            </tr>
          </thead>
          
        </table>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="card" style="padding: 20px">
      <div id="chart"></div>
    </div>
  </div>
</div>

    
@endsection

@push('scripts')

<script>
    function loadData() {
        var routeName = 'absen.json'; // Ganti 'nama_route' dengan nama route yang sesuai
        var url = '{{ route("absen.json") }}'; // Ganti 'route_name' dengan nama route yang sesuai dalam sintaks Blade Laravel

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = xhr.responseText;
                var dataArray = JSON.parse(response);
                drawChart(dataArray);
            }
        };
        xhr.open('GET', url, true);
        xhr.send();
    }

    var options = {
        series: [{
              name: 'Masuk',
              data: [<?php foreach($masuk as $data): echo "'".number_format(str_replace(':', '.', substr($data->jam_masuk, 11,5)), 2)."',"; endforeach; ?>]
            }, {
              name: 'Keluar',
              data: [<?php foreach($masuk as $data): echo "'".number_format(str_replace(':', '.', substr($data->jam_keluar, 11,5)), 2)."',"; endforeach; ?>]
            }],
              chart: {
              type: 'bar',
              height: 350
            },
            plotOptions: {
              bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
              },
            },
            dataLabels: {
              enabled: false
            },
            stroke: {
              show: true,
              width: 2,
              colors: ['transparent']
            },
            xaxis: {
              categories: [<?php foreach($masuk as $data): echo "'".substr($data->created_at, 0, 10)."',"; endforeach; ?>],
            },
            yaxis: {
              title: {
                text: 'pukul (jam)'
              }
            },
            fill: {
              opacity: 1
            },
            tooltip: {
              y: {
                formatter: function (val) {
                  return "Jam " + val + " WITA"
                }
              }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
</script>


@endpush