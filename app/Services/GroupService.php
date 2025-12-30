<?php

namespace App\Services;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

class GroupService
{
    public function getAll($page = 1, $limit = 10)
    {
        return Group::paginate($limit, ['*'], 'page', $page);
    }

    public function getById($id)
    {
        return Group::findOrFail($id);
    }

    public function store($data)
    {
        return Group::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update($id, $data)
    {
        $group = Group::findOrFail($id);

        $group->update([
            'name' => $data['name'] ?? $group->name,
            'description' => $data['description'] ?? $group->description,
            'is_active' => isset($data['is_active']) ? $data['is_active'] : $group->is_active,
        ]);

        return $group;
    }

    public function delete($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();
        return $group;
    }

    public function getActive()
    {
        return Group::where('is_active', true)->get();
    }
}
