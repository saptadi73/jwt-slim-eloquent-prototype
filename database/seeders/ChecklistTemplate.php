<?php
namespace Database\Seeders;

use App\Models\ChecklistTemplate;

class ChecklistTemplateSeeder
{
    public function run()
    {
        ChecklistTemplate::create([
            'no_urut' => 1,
            'kode_checklist' => 'A01',
            'title' => 'Bagian Indoor',
            'checklist' => 'Evaporator',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);

        ChecklistTemplate::create([
            'no_urut' => 2,
            'kode_checklist' => 'A02',
            'title' => 'Bagian Indoor',
            'checklist' => 'Fan/Blower Indoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 3,
            'kode_checklist' => 'A03',
            'title' => 'Bagian Indoor',
            'checklist' => 'Kondisi Swing',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 4,
            'kode_checklist' => 'A04',
            'title' => 'Bagian Indoor',
            'checklist' => 'Tegangan Input',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 5,
            'kode_checklist' => 'A05',
            'title' => 'Bagian Indoor',
            'checklist' => 'Thermis/Temp Sensor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 6,
            'kode_checklist' => 'A06',
            'title' => 'Bagian Indoor',
            'checklist' => 'Check temperature',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 7,
            'kode_checklist' => 'A07',
            'title' => 'Bagian Indoor',
            'checklist' => 'Lain-lain',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 8,
            'kode_checklist' => 'B01',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Kondensor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 9,
            'kode_checklist' => 'B02',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Fan Outdoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 10,
            'kode_checklist' => 'B03',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Capasitor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 11,
            'kode_checklist' => 'B04',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Tekanan Freon',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 12,
            'kode_checklist' => 'B05',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Arus/Amperer',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 13,
            'kode_checklist' => 'B06',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Check Temperatur',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 14,
            'kode_checklist' => 'B07',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Lain-lain',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
    }
}
