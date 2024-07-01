<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\status_rfid;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index(){
        return view('page.administrator');
    }
    
    public function cekjadwal($id){
        
        Carbon::setToStringFormat('Y-m-d H:i:s.u');
        Carbon::setTestNow(Carbon::parse('2023-07-17 10:00:00', 'UTC'));
        
        Carbon::setToStringFormat('Y-m-d H:i:s.u');
        Carbon::setTestNow(Carbon::parse('2023-07-17 10:00:00', 'Asia/Singapore'));
        
        $carbonInstance = Carbon::now();
        $carbonInstance->timezone = 'Asia/Singapore';
        
        $carbonInstance->setTimezone('Asia/Singapore');
        
        $hariIni = Carbon::now()->format('l');
        $hariId = $this->konversiHari($hariIni);
        $kelas = Kelas::where('hari', $hariId)->get();
        $kelasp = Kelas::where('hari', $hariId)->first();
        if(count($kelas) > 0){
            // $absen = Absen::where('')
            $absenhariini = Absen::whereDate('created_at', Carbon::today())->get();
            if(count($absenhariini) > 0){
                $dataabsen = Absen::whereDate('created_at', Carbon::today())->first();
                if($dataabsen->stat == 'masuk'){
                    return response([
                        'jadwal' => 'Tersedia',
                        'jumlah' => count($kelas),
                        'masuk' => $dataabsen->jam_masuk,
                        'keluar' => 'Pelajaran sedang berlangsung',
                    ]);
                }else{
                    return response([
                        'jadwal' => 'Tersedia',
                        'jumlah' => count($kelas),
                        'masuk' => $dataabsen->jam_masuk,
                        'keluar' => $dataabsen->jam_keluar,
                    ]);
                }
                
            }else{
                return response([
                        'jadwal' => 'Tersedia',
                        'jumlah' => count($kelas),
                        'masuk' => 'Belum Absen',
                        'keluar' => 'Belum Absen',
                ]);
            }
        }else{
            return response([
                    'jadwal' => 'Tidak tersedia',
                    'jumlah' => count($kelas),
                    'masuk' => 'Bukan Jadwal Absen',
                    'keluar' => 'Bukan Jadwal Absen',
            ]);
        }
    }
    
    public function kelas(){
        $guru = Guru::all();
        return view('page.kelas', ['guru' => $guru]);
    }
    
    public function loginguru(Request $request){
        $email = $request->email;
        $password = $request->password;
        $cek = User::where('email', $email)->first();
        if($cek){
            if(Hash::check($password, $cek->password)){
                return response([
                    'status' => 'OK',
                    'message' => 'Berhasil Login',
                    'id_guru' => $cek->id,
                    'nama_guru' => $cek->name,
                ]);
            }else{
                return response([
                    'status' => 'NO',
                    'message' => 'Password Tidak Cocok'
                ]);
            }
        }else{
            return response([
                'status' => 'NO',
                'message' => 'Username Tidak di Temukan'
            ]);
        }

    }

    public function guru(){
        $scan = status_rfid::find(1);
        return view('page.guru', ['statusscan' => $scan->scan]);
    }

    public function gurujson(){
        $dataguru = Guru::all();
        return DataTables::of($dataguru)->make(true);
    }

    public function kelasjson(){
        $dataguru = Kelas::with('gurus')->get();
        return DataTables::of($dataguru)->make(true);
    }
    
    private function konversiHari($hari)
    {
        $namaHariInggris = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $namaHariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    
        $index = array_search($hari, $namaHariInggris);
        if ($index !== false) {
            return $namaHariIndonesia[$index];
        }
    
        return $hari;
    }

    public function rfid(Request $req){
        $carbonInstance = Carbon::now();
        $carbonInstance->timezone = 'Asia/Singapore';
        
        $carbonInstance->setTimezone('Asia/Singapore');
        
        $cek = status_rfid::find(1);
        if($cek->scan == 'T'){
            status_rfid::where('id', 1)->update([
                'scan' => 'Y'
            ]);
           $user = new User(); 
           $user->name = $req->id;
           $user->email = $req->id;
           $user->password = bcrypt($req->id);
           $user->roles_id = 2;
           $user->save();

           $data = User::latest()->first();
           $guru = new Guru();
           $guru->nik = $req->id;
           $guru->nama = '';
           $guru->jenis_kelamin = 'Laki-laki';
           $guru->alamat = '';
           $guru->users_id = $data->id;
           $guru->save();
           return response([
                'status' => 1,
                'nama' => "",
                'absen' => 1,
                'scan' => 1,
            ]);
        }else{
            $ada = Guru::where('nik', $req->id)->first();
            if($ada){
                $boleh = 0;
                $kelasHadir = '';
                $hariIni = Carbon::now()->format('l');
                $jamSekarang = Carbon::now();
                $jamSekarang->timezone = 'Asia/Singapore';
        
                $jamSekarang->setTimezone('Asia/Singapore');
                
                $hariId = $this->konversiHari($hariIni);
                $kelas = Kelas::where('hari', $hariId)->where('gurus_id', $ada->id)->get();
                if(count($kelas) > 0){
                    foreach($kelas as $row){
                        if($row->jam  == '07'){
                            // $batasBawah = Carbon::createFromTime(7, 50, 0); 
                            // $batasAtas = Carbon::createFromTime(9, 50, 0);
                            $batasBawah = Carbon::createFromTime(0, 0, 0); 
                            $batasAtas = Carbon::createFromTime(23, 50, 0);
                            if($jamSekarang->between($batasBawah, $batasAtas)){
                                $boleh++;
                                $kelasHadir = $row->id;
                            }
                        }else if($row->jam  == '10'){
                            // $batasBawah = Carbon::createFromTime(10, 10, 0); 
                            // $batasAtas = Carbon::createFromTime(11, 30, 0);
                            $batasBawah = Carbon::createFromTime(0, 0, 0); 
                            $batasAtas = Carbon::createFromTime(23, 50, 0);
                            if($jamSekarang->between($batasBawah, $batasAtas)){
                                $boleh++;
                                $kelasHadir = $row->id;
                            }
                        }else if($row->jam  == '11'){
                            $batasBawah = Carbon::createFromTime(0, 0, 0); 
                            $batasAtas = Carbon::createFromTime(23, 50, 0);
                            // $batasBawah = Carbon::createFromTime(11, 30, 0); 
                            // $batasAtas = Carbon::createFromTime(12, 50, 0);
                            if($jamSekarang->between($batasBawah, $batasAtas)){
                                $boleh++;
                                $kelasHadir = $row->id;
                            }
                        }
                    }
                    if($boleh > 0){
                        $absen = new Absen(); 
                        $absen->kelas_is = $kelasHadir;
                        $absen->jam_masuk = $jamSekarang;
                        $absen->jam_keluar = $jamSekarang;
                        $absen->stat = 'masuk';
                        $absen->save();

                        return response([
                            'status' => 1,
                            'nama' => $ada->nama,
                            'absen' => 0,
                            'scan' => 0,
                            'message' => 'Sukses Absen',
                        ]);
                    }else{
                        return response([
                            'status' => 0,
                            'nama' => "",
                            'absen' => 0,
                            'scan' => 0,
                            'message' => 'Tidak Ada Jam Pelajaran',
                        ]);
                    }
                }else{
                    return response([
                        'status' => 0,
                        'nama' => "",
                        'absen' => 0,
                        'scan' => 0,
                        'message' => 'Tidak Ada Kelas',
                    ]);
                }
            }else{
                return response([
                    'status' => 0,
                    'nama' => "",
                    'absen' => 0,
                    'scan' => 0,
                    'message' => 'Tidak ada data guru',
                ]);
            }
        }
    }

    public function absenkeluar(Request $req){
        
        Absen::where('id', $id)->update([
            'stat' => 'keluar',
            'jam_keluar' => Carbon::now(),
        ]);
    }


    public function gantiscan(){
        $cek = status_rfid::find(1);
        if($cek->scan == 'T'){
            status_rfid::where('id', 1)->update([
                'scan' => 'Y'
            ]);
            return redirect('guru')->with('success', 'Sukses update');
           
        }else{
            status_rfid::where('id', 1)->update([
                'scan' => 'T'
            ]);
            return redirect('guru')->with('success', 'Sukses update');
            
        }
    }

    public function simpan(Request $req){
        $req->validate([
            'nama' => ['required'],
            'nik' => ['required'],
            'jk' => ['required'],
            'alamat' => ['required'],
        ]);

        $update = Guru::where('nik', $req->nik)->update([
            'nama' => $req->nama,
            'jenis_kelamin' => $req->jk,
            'alamat' => $req->alamat,
        ]);

        if($update){
            return redirect('guru')->with('success', 'Sukses update');
        }else{
            return back()->with('error', 'Gagal melakukan update');
        }
    }

    public function hapus(Request $req){
        $req->validate([
            'id' => ['required'],
            'nik' => ['required'],
        ]);

        $kelas = Kelas::where('gurus_id', $req->id)->delete();
        $guru = Guru::where('id', $req->id)->delete();
        $user = User::where('email', $req->nik)->delete();

        if($user && $kelas && $guru){
            return redirect('guru')->with('success', 'Sukses Hapus');
        }else{
            return back()->with('error', 'Gagal melakukan hapus');
        }
    }
    
    public function simpankelas(Request $req){
        $req->validate([
            'nama' => ['required'],
            'hari' => ['required'],
            'jam' => ['required'],
            'guru' => ['required'],
        ]);

        $kelas = new Kelas();
        $kelas->nama_mapel = $req->nama;
        $kelas->hari = $req->hari;
        $kelas->jam = $req->jam;
        $kelas->gurus_id = $req->guru;
        

        if($kelas->save()){
            return redirect('kelas')->with('success', 'Sukses tambah');
        }else{
            return back()->with('error', 'Gagal tambah');
        }
    }

    public function grafik($id){
        $absen = Absen::where('kelas_is', $id)->get();

        return view('page.grafik', ['masuk' => $absen]);
    }

    public function absenjson(){
        $dataguru = Absen::with('kelas')->get();
        return response($dataguru);
    }

    public function kepsekjson(){
        $dataguru = Kelas::with('gurus')->get();
        return DataTables::of($dataguru)->make(true);        
    }

    public function kepsek(){
        return view('page.kepsek');

    }

    public function grafikdetail($id){
        $absen = Absen::where('kelas_is', $id)->get();
        $detail = Absen::with('kelas', 'kelas.gurus')->where('kelas_is', $id)->first();
        

        return view('page.grafik', ['masuk' => $absen, 'detail' => $detail]);
    }
}
