<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\status_rfid;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;

class AdminController extends Controller
{
    public function index(){
        return view('page.administrator');
    }

    public function guru(){
        $scan = status_rfid::find(1);
        return view('page.guru', ['statusscan' => $scan->scan]);
    }

    public function kelas(){
        $guru = Guru::all();
        return view('page.kelas', ['guru' => $guru]);
    }

    public function gurujson(){
        $dataguru = Guru::all();
        return DataTables::of($dataguru)->make(true);
    }

    public function kelasjson(){
        $dataguru = Kelas::with('gurus')->get();
        return DataTables::of($dataguru)->make(true);
    }

    public function cekhari(){
        $hariIni = Carbon::now()->format('l');
        Carbon::setLocale('id');
        echo $hariIni;
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
        $hariIni = Carbon::now()->format('l');
        Carbon::setLocale('id');
        $hariIndo = $this->konversiHari($hariIni);

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
                return response([
                    'status' => 1,
                    'nama' => $ada->nama,
                    'absen' => 0,
                    'scan' => 0,
                ]);
            }else{
                return response([
                    'status' => 0,
                    'nama' => "",
                    'absen' => 0,
                    'scan' => 0,
                ]);
            }
        }
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
}
