<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

//LOAD MODELS
use App\Models\DtPengguna;
use App\Models\User;
//LOAD HELPER
use Tanggal;
use Konversi;

class ImportDataPenggunaClass implements ToCollection, WithCalculatedFormulas
{
    /**
     * @param Collection $rows
     * @return MsHdCashflow
     */

    public  $insert;
    public  $edit;
    public  $gagal;
    public  $listgagal;

    public function __construct(){
        $this->Tanggal = new Tanggal();
        $this->Konversi = new Konversi();
    }

    public function collection(Collection $rows)
    {
        $trDt = [];
        $this->insert = 0;
        $this->edit = 0; 
        $this->gagal = 0; 
        $this->listgagal = "";

        foreach ($rows as $idx => $row) {
            if ($idx > 0) {
                //DECLARE REQUEST
                $no=isset($row[0])?($row[0]):'';
                $name=isset($row[1])?($row[1]):'';
                $email=isset($row[2])?($row[2]):'';
                $password=isset($row[3])?($row[3]):'';
                $namerole=isset($row[4])?($row[4]):'';

                //COSTUM REQUEST
                // if($tgl_produksi){
                //     $tgl_produksi=$this->Tanggal->tanggalDatabase($tgl_produksi);
                // }else{
                //     $tgl_produksi=null;
                // }
                // if($tgl_expired){
                //     $tgl_expired=$this->Tanggal->tanggalDatabase($tgl_expired);
                // }else{
                //     $tgl_expired=null;
                // }
                // if($harga_satuan){
                //     $harga_satuan=$this->Konversi->numberonly($harga_satuan);
                // }else{
                //     $harga_satuan=null;
                // }
                // if($harga_satuan){
                //     $harga_jual=$this->Konversi->numberonly($harga_jual);
                // }else{
                //     $harga_jual=null;
                // }
                // $dbKategoriBarang = TmKategoriBarang::where('nama', '=',''.$kategori.'')->first();
                // $kategori_id=$dbKategoriBarang->id??'';
      
                //READY REQUEST
                $trDt[$idx]['name'] = $name;
                $trDt[$idx]['email'] = $email;
                $trDt[$idx]['password'] = $password;
                $trDt[$idx]['namerole'] = $namerole;
                $data = DtPengguna::where('email', '=',''.$trDt[$idx]['email'].'')->first();
                if ($data) {//UPDATE DATA
                    $data->updated_us   = auth()->user()->id;
                    $data->name        = $trDt[$idx]['name'];
                    $data->email        = $trDt[$idx]['email'];
                    $data->password        = $trDt[$idx]['password'];
                    $data->namerole = $trDt[$idx]['namerole'];
                    // SAVE THE DATA
                    if ($data->save()) {
                        // SUCCESS
                        ++$this->edit;
                    }
                } else {//INSERT DATA
                    if($trDt[$idx]['email']){
                        $data =  new DtPengguna();
                        // $data->created_us   = auth()->user()->id;
                        // $data->updated_us   = auth()->user()->id;
                        $data->name         = $trDt[$idx]['name'];
                        $data->email        = $trDt[$idx]['email'];
                        $data->password     = $trDt[$idx]['password'];
                        $data->namerole     = $trDt[$idx]['namerole'];
                        // SAVE THE DATA
                        if ($data->save()) {
                            // SUCCESS
                            ++$this->insert;
                        }
                    }else{
                        // FAILED
                        ++$this->gagal;
                        $this->listgagal.="(".$trDt[$idx]['email']." - ".$trDt[$idx]['name']."),<br>";
                    }
                }
            }
        }
    }
}