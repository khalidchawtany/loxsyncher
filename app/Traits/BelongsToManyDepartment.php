<?php

namespace App\Traits;

use App\Models\Department;

trait BelongsToManyDepartment
{
    /**
     * Assign the given department to the user.
     *
     * @param  array|string|\Spatie\Permission\Contracts\Role  ...$roles
     * @return $this
     */
    public function assignDepartment(...$departments)
    {
        $departments = collect($departments)
            ->flatten()
            ->map(function ($department) {
                if (empty($department)) {
                    return false;
                }

                return Department::where('name', $department)->firstOrFail();
            })
            ->filter(function ($department) {
                return $department instanceof Department;
            })
            ->map->id
            ->all();

        $this->departments()->sync($departments, false);

        return $this;
    }

    /**
     * Remove all current departments and set the given ones.
     *
     * @param  array|\App\Models\Department|string  ...$departments
     * @return $this
     */
    public function syncDepartments(...$departments)
    {
        $this->departments()->detach();

        return $this->assignDepartment($departments);
    }
}
