<?php

namespace App\Http\Controllers;
use App\Models\Mahasiswa;
use App\Models\Kelas;
use App\Models\Mahasiswa_MataKuliah;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\PDF;


use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //         //fungsi eloquent menampilkan data menggunakan pagination
        // $mahasiswas = Mahasiswa::all(); // Mengambil semua isi tabel
        // $posts = Mahasiswa::orderBy('Nim', 'desc')->paginate(6);
        // return view('mahasiswas.index', compact('mahasiswas')) ->with('i', (request()->input('page', 1) - 1) * 5);

        // $user = Auth::user();
        $mahasiswas = Mahasiswa::where([
            ['Nama', '!=' , Null],
            [function ($query) use ($request){
                if (($search = $request->search)){
                    $query->orWhere('Nama', 'Like', '%' . $search . '%')->get();
                }
            }]
        ])->paginate(5);
        $posts = Mahasiswa::orderBy('Nim', 'desc');
        return view('mahasiswas.index', compact('mahasiswas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kelas = Kelas::all(); //mendapatkan data dari tabel kelas
        return view('mahasiswas.create', ['kelas' => $kelas]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            //melakukan validasi data   
        $request->validate([
        'Nim' => 'required',
        'Nama' => 'required',
        'Kelas' => 'required',
        'Jurusan' => 'required',
        'No_Handphone' => 'required',
        'Email' => 'required',
        'TanggalLahir' => 'required',
        ]);
        // //fungsi eloquent untuk menambah data
        // Mahasiswa::create($request->all());
        // //jika data berhasil ditambahkan, akan kembali ke halaman utama
        // return redirect()->route('mahasiswas.index') ->with('success', 'Mahasiswa Berhasil Ditambahkan');

        //fungsi eloquent untuk menambah data
        if ($request->file('image')){
            $image_name = $request->file('image')->store('images', 'public');
        }
        $mahasiswas= new Mahasiswa;
        $mahasiswas->Nim=$request->get('Nim');
        $mahasiswas->Nama=$request->get('Nama');
        $mahasiswas->featured_image=$image_name;
        $mahasiswas->Jurusan=$request->get('Jurusan');
        $mahasiswas->No_Handphone=$request->get('No_Handphone');
        $mahasiswas->Email=$request->get('Email');
        $mahasiswas->TanggalLahir=$request->get('TanggalLahir');
        

        //fungsi eloquent untuk menambah data dengan relasi belongs to
        $kelas = new Kelas;
        $kelas->id = $request->get('Kelas');

        $mahasiswas->kelas()->associate($kelas);
        $mahasiswas->save();

        //jika data berhasil ditambahkan, akan kembali ke halaman utama
        return redirect()->route('mahasiswas.index') ->with('success', 'Mahasiswa Berhasil Ditambahkan');
        }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($Nim)
    {
        //menampilkan detail data dengan menemukan/berdasarkan Nim Mahasiswa
         $Mahasiswa = Mahasiswa::find($Nim);
         return view('mahasiswas.detail', compact('Mahasiswa'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($Nim)
    {
        //menampilkan detail data dengan menemukan berdasarkan Nim Mahasiswa untuk  diedit
        $Mahasiswa = Mahasiswa::find($Nim);
        $kelas = Kelas::all();
        return view('mahasiswas.edit', compact('Mahasiswa', 'kelas'));


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $Nim)
    {

        
        //melakukan validasi data
        $request->validate([
        'Nim' => 'required',
        'Nama' => 'required',
        'Kelas' => 'required',
        'Jurusan' => 'required',
        'No_Handphone' => 'required',
        'Email' => 'required',
        'TanggalLahir' => 'required',
    ]);
    $Mahasiswa = Mahasiswa::find($Nim);
        if ($Mahasiswa->featured_image && file_exists(storage_path('app/public/' . $Mahasiswa->featured_image))){
            Storage::delete('public/' . $Mahasiswa->featured_image);
        }
       

        $mahasiswas = Mahasiswa::with('Kelas')->where('Nim', $Nim)->first();
        $mahasiswas->Nim = $request->get('Nim');
        $mahasiswas->Nama = $request->get('Nama');
        $mahasiswas->Jurusan = $request->get('Jurusan');
        $mahasiswas->No_Handphone = $request->get('No_Handphone');
        $mahasiswas->Email = $request->get('Email');
        $mahasiswas->TanggalLahir = $request->get('TanggalLahir');

        $image_name = $request->file('image')->store('images', 'public');
        $mahasiswas->featured_image = $image_name;
        
        $kelas = new Kelas;
        $kelas->id = $request->get('Kelas');


        //fungsi eloquent untuk mengupdate data inputan kita
        //Mahasiswa::find($Nim)->update($request->all());

        //Fungsi eloquent untuk mengupdate data dengan relasi belongs to
        $mahasiswas->kelas()->associate($kelas);
        $mahasiswas->save();

        //jika data berhasil diupdate, akan kembali ke halaman utama
        return redirect()->route('mahasiswas.index') ->with('success', 'Mahasiswa Berhasil Diupdate');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($Nim)
    {
         Mahasiswa::find($Nim)->delete();
        return redirect()->route('mahasiswas.index') -> with('success', 'Mahasiswa Berhasil Dihapus');
    }

    public function khs($Nim)
    {
        $mahasiswa = Mahasiswa::find($Nim);

        return view('mahasiswas.khs', compact('mahasiswa'));
    }
    

    public function cetak_khs($Nim){
        $mahasiswa = Mahasiswa::find($Nim);
        
        $pdf = PDF::loadview('mahasiswas.cetakKHS',['mahasiswa'=>$mahasiswa]);
        return $pdf->stream();
    }
}
