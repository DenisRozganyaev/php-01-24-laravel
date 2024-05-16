<?php

namespace App\Services;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UsersCsvExport implements Contract\UsersCsvExportContract
{

    public function generate(): string|false
    {
        $users = User::select(['id', 'name', 'lastname', 'phone', 'email', 'birthdate', 'created_at', 'updated_at'])
            ->withCount('orders')
            ->role(Roles::CUSTOMER)
            ->get();
        $csvFileName = 'csv/users1.csv';

        $tempFile = tmpFile();
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];

        $handle = fopen($tempFilePath, 'w');

        try {
            fputcsv($handle, ['ID', 'Name', 'Surname', 'Phone', 'Email', 'Birthdate', 'Orders count', 'Created', 'Updated']); // Add more headers as needed

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->lastname,
                    $user->phone,
                    $user->email,
                    $user->birthdate,
                    $user->orders_count,
                    $user->created_at,
                    $user->updated_at,
                ]);
            }
            fclose($handle);

            if (Storage::put($csvFileName, file_get_contents($tempFilePath))) {
                return Storage::url($csvFileName);
            }
        } catch (\Exception $exception) {
            logs()->error('[UsersCsvExport] Unexpected error', [
                'error' => $exception->getMessage()
            ]);
        } finally {
            fclose($tempFile);
        }

        return false;
    }
}
