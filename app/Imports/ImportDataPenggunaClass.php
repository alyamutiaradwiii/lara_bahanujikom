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
use App\Models\users;
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
                $namerole=isset($row[3])?($row[3]):'';

                //COSTUM REQUEST
                
                // $dbKategoriBarang = users::where('name', '=',''.$kategori.'')->first();
                // $kategori_id=$dbKategoriBarang->id??'';
      
                //READY REQUEST
                $trDt[$idx]['name'] = $name;
                $trDt[$idx]['email'] = $email;
                $trDt[$idx]['namerole'] = $namerole;

                $data = DtPengguna::where('email', '=',''.$trDt[$idx]['email'].'')->first();
                if ($data) {//UPDATE DATA
                    $data->updated_us   = auth()->user()->id;
                    $data->name         = $trDt[$idx]['name'];
                    $data->email         = $trDt[$idx]['email'];
                    $data->namerole         = $trDt[$idx]['namerole'];
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
                        $data->email         = $trDt[$idx]['email'];
                        $data->namerole         = $trDt[$idx]['namerole'];
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
