<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

echo "=== Checking Sales/Marketing Department ===\n\n";

// Check department exists
$dept = App\Models\Departemen::find('b0e8d3cd-e102-4a03-821e-75b5b391d870');
if ($dept) {
    echo "Department found:\n";
    echo "ID: " . $dept->id . "\n";
    echo "Nama: " . $dept->nama . "\n\n";
} else {
    echo "Department NOT FOUND!\n\n";
}

// Check pegawai with this department
echo "=== Pegawai with Sales/Marketing Department ===\n\n";
$pegawai = App\Models\Pegawai::where('departemen_id', 'b0e8d3cd-e102-4a03-821e-75b5b391d870')->get();
echo "Count: " . $pegawai->count() . "\n";

if ($pegawai->count() > 0) {
    foreach ($pegawai as $p) {
        echo "- " . $p->nama . " (ID: " . $p->id . ")\n";
    }
} else {
    echo "No pegawai found with this department!\n";
}

echo "\n=== All Departments with Count ===\n\n";
$allDepts = App\Models\Departemen::withCount('pegawai')->get();
foreach ($allDepts as $d) {
    echo $d->nama . " (" . $d->pegawai_count . " pegawai)\n";
}
