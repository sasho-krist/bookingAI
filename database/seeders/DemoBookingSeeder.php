<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoBookingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();

        $type = BusinessType::query()->firstOrCreate(
            ['name' => 'Фризьорски салон'],
        );

        $business = Business::query()->create([
            'business_type_id' => $type->id,
            'user_id' => $user?->id,
            'name' => 'Веригa Demo',
        ]);

        $venue = Venue::query()->create([
            'business_id' => $business->id,
            'name' => 'Demo Salon — център',
            'type' => 'salon',
            'timezone' => 'Europe/Sofia',
            'business_hours' => [
                'mon' => ['09:00', '18:00'],
                'tue' => ['09:00', '18:00'],
                'wed' => ['09:00', '18:00'],
                'thu' => ['09:00', '18:00'],
                'fri' => ['09:00', '18:00'],
                'sat' => ['10:00', '14:00'],
            ],
        ]);

        $venueBranch = Venue::query()->create([
            'business_id' => $business->id,
            'name' => 'Demo Salon — клон Лозенец',
            'type' => 'salon',
            'timezone' => 'Europe/Sofia',
            'business_hours' => [
                'mon' => ['10:00', '19:00'],
                'tue' => ['10:00', '19:00'],
                'wed' => ['10:00', '19:00'],
                'thu' => ['10:00', '19:00'],
                'fri' => ['10:00', '19:00'],
            ],
        ]);

        $cut = Service::query()->create([
            'venue_id' => $venue->id,
            'name' => 'Haircut',
            'duration_minutes' => 45,
        ]);

        $color = Service::query()->create([
            'venue_id' => $venue->id,
            'name' => 'Color',
            'duration_minutes' => 90,
        ]);

        $branchCut = Service::query()->create([
            'venue_id' => $venueBranch->id,
            'name' => 'Haircut',
            'duration_minutes' => 45,
        ]);

        $alice = Customer::query()->create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'phone' => '+35980000001',
        ]);

        $bob = Customer::query()->create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'phone' => '+35980000002',
        ]);

        $tz = $venue->timezone;

        foreach ([-10, -7, -3, 2, 5] as $i) {
            $starts = Carbon::parse('next monday 10:00', $tz)->addDays($i * 3);
            Booking::query()->create([
                'venue_id' => $venue->id,
                'service_id' => $cut->id,
                'customer_id' => $alice->id,
                'starts_at' => $starts,
                'ends_at' => $starts->copy()->addMinutes($cut->duration_minutes),
                'status' => $i < 0 ? 'completed' : 'confirmed',
                'attended' => $i < 0 ? ($i === -7 ? false : true) : null,
            ]);
        }

        $startsBob = Carbon::parse('next tuesday 11:30', $tz);
        Booking::query()->create([
            'venue_id' => $venue->id,
            'service_id' => $color->id,
            'customer_id' => $bob->id,
            'starts_at' => $startsBob,
            'ends_at' => $startsBob->copy()->addMinutes($color->duration_minutes),
            'status' => 'confirmed',
            'attended' => null,
        ]);

        $branchStart = Carbon::parse('next wednesday 14:00', $tz);
        Booking::query()->create([
            'venue_id' => $venueBranch->id,
            'service_id' => $branchCut->id,
            'customer_id' => $bob->id,
            'starts_at' => $branchStart,
            'ends_at' => $branchStart->copy()->addMinutes(45),
            'status' => 'confirmed',
            'attended' => null,
        ]);
    }
}
