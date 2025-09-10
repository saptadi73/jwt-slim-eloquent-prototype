<?php
namespace App\Services;

use App\Models\Absen;
use App\Models\Pegawai;
use App\Models\JatahCuti;
use App\Models\Departemen;
use App\Models\Gaji;
use App\Models\Group;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Support\RequestHelper;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\UploadedFileInterface as File;

class OrganisasiService
{
    public function createDepartemen(Request $request, Response $response, array $data)
    {
        try {
            $departemen = new Departemen($data);
            $departemen->save();

            return JsonResponder::success($response, $departemen);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th);
        }
    }

    public function createGroup(Request $request, Response $response, array $data)
    {
        try {
            $group = new Group($data);
            $group->save();

            return JsonResponder::success($response, $group);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th);
        }
    }

    public function getPegawai(Response $response)
    {
        try {
            $pegawai = Pegawai::join('departemen', 'pegawai.departemen_id', '=', 'departemen.id')
                ->join('groups', 'pegawai.group_id', '=', 'groups.id')
                ->select('pegawai.*', 'departemen.nama as departemen_nama', 'groups.nama as group_nama')
                ->get();
            return JsonResponder::success($response, $pegawai);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th);
        }
    }
}

?>