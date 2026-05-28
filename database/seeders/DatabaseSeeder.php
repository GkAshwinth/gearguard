<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Equipment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Test Users ─────────────────────────────────────────────────────
        // Password for both accounts: "password"

        User::create([
            'name'           => 'Admin Owner',
            'email'          => 'admin@gearguard.com',
            'password'       => Hash::make('password'),
            'role'           => 'owner',
            'contact_number' => '0771234567',
        ]);

        User::create([
            'name'           => 'Test Client',
            'email'          => 'client@gearguard.com',
            'password'       => Hash::make('password'),
            'role'           => 'client',
            'contact_number' => '0777654321',
        ]);

        // ── Equipment Seed Data ────────────────────────────────────────────

        $items = [
            // Cameras
            ['name' => 'Sony A7 IV',              'category' => 'Cameras',  'daily_rate' => 15000, 'image_path' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=800&q=80', 'description' => '33MP Full-frame Hybrid Camera'],
            ['name' => 'Canon EOS R5',            'category' => 'Cameras',  'daily_rate' => 18000, 'image_path' => 'https://images.unsplash.com/photo-1519638831568-d9897f54ed69?auto=format&fit=crop&w=800&q=80', 'description' => '8K Video, 45MP Stills'],
            ['name' => 'Nikon Z9',                'category' => 'Cameras',  'daily_rate' => 20000, 'image_path' => 'https://images.unsplash.com/photo-1564466021183-a42fdd043ce4?auto=format&fit=crop&w=800&q=80', 'description' => 'Flagship Mirrorless'],
            ['name' => 'Fujifilm X-T4',           'category' => 'Cameras',  'daily_rate' => 12000, 'image_path' => 'https://images.unsplash.com/photo-1500634245200-e5245c7574ef?auto=format&fit=crop&w=800&q=80', 'description' => 'APS-C Mirrorless with IBIS'],
            ['name' => 'Blackmagic Pocket 6K',    'category' => 'Cameras',  'daily_rate' => 14000, 'image_path' => 'https://images.unsplash.com/photo-1589718427771-4603a11585c2?auto=format&fit=crop&w=800&q=80', 'description' => 'Cinema Camera'],
            ['name' => 'Panasonic GH6',           'category' => 'Cameras',  'daily_rate' => 11000, 'image_path' => 'https://images.unsplash.com/photo-1599665063467-92d86161c56e?auto=format&fit=crop&w=800&q=80', 'description' => 'Micro Four Thirds beast'],
            // Lenses
            ['name' => 'Sony 24-70mm GM II',      'category' => 'Lenses',   'daily_rate' =>  8000, 'image_path' => 'https://images.unsplash.com/photo-1617005082133-548c4dd27f35?auto=format&fit=crop&w=800&q=80', 'description' => 'Standard Zoom'],
            ['name' => 'Canon RF 50mm f/1.2',     'category' => 'Lenses',   'daily_rate' =>  7500, 'image_path' => 'https://images.unsplash.com/photo-1616423664033-630e25ed4b15?auto=format&fit=crop&w=800&q=80', 'description' => 'Prime Portrait Lens'],
            ['name' => 'Sigma 85mm Art',          'category' => 'Lenses',   'daily_rate' =>  6000, 'image_path' => 'https://images.unsplash.com/photo-1623838891637-236b28b7e8d3?auto=format&fit=crop&w=800&q=80', 'description' => 'Sharp Portrait Lens'],
            ['name' => 'Nikon Z 70-200mm f/2.8',  'category' => 'Lenses',   'daily_rate' =>  9000, 'image_path' => 'https://images.unsplash.com/photo-1606558661641-fcda95eb5d56?auto=format&fit=crop&w=800&q=80', 'description' => 'Telephoto Zoom'],
            ['name' => 'Sony 16-35mm GM',         'category' => 'Lenses',   'daily_rate' =>  7800, 'image_path' => 'https://images.unsplash.com/photo-1499544426868-b78cc2d005ea?auto=format&fit=crop&w=800&q=80', 'description' => 'Wide Angle Zoom'],
            ['name' => 'Canon RF 100mm Macro',    'category' => 'Lenses',   'daily_rate' =>  6500, 'image_path' => 'https://images.unsplash.com/photo-1563290680-36d538eecf97?auto=format&fit=crop&w=800&q=80', 'description' => 'Macro photography'],
            // Lighting
            ['name' => 'Aputure 120d II',         'category' => 'Lighting', 'daily_rate' =>  5000, 'image_path' => 'https://images.unsplash.com/photo-1527011046414-4781f1f94f8c?auto=format&fit=crop&w=800&q=80', 'description' => 'COB Light'],
            ['name' => 'Godox V1 Flash',          'category' => 'Lighting', 'daily_rate' =>  2000, 'image_path' => 'https://images.unsplash.com/photo-1585223393081-3444ce315c61?auto=format&fit=crop&w=800&q=80', 'description' => 'Round Head Flash'],
            ['name' => 'Nanlite Pavotube',        'category' => 'Lighting', 'daily_rate' =>  3000, 'image_path' => 'https://images.unsplash.com/photo-1562913075-8cece1fe94bb?auto=format&fit=crop&w=800&q=80', 'description' => 'RGB Tube Light'],
            ['name' => 'Profoto B10',             'category' => 'Lighting', 'daily_rate' => 10000, 'image_path' => 'https://images.unsplash.com/photo-1543884880-2a2ec4c8b8dc?auto=format&fit=crop&w=800&q=80', 'description' => 'Studio Strobe'],
            ['name' => 'Westcott Flex',           'category' => 'Lighting', 'daily_rate' =>  4500, 'image_path' => 'https://images.unsplash.com/photo-1620359850989-299f19ad5d32?auto=format&fit=crop&w=800&q=80', 'description' => 'Flexible LED Panel'],
            ['name' => 'Amaran 200x',             'category' => 'Lighting', 'daily_rate' =>  4000, 'image_path' => 'https://images.unsplash.com/photo-1544431526-884ecb2a4729?auto=format&fit=crop&w=800&q=80', 'description' => 'Bi-color LED'],
            // Audio
            ['name' => 'Rode NTG3',              'category' => 'Audio',    'daily_rate' =>  4000, 'image_path' => 'https://images.unsplash.com/photo-1590845947698-8924d7409b56?auto=format&fit=crop&w=800&q=80', 'description' => 'Shotgun Mic'],
            ['name' => 'Zoom H6 Recorder',       'category' => 'Audio',    'daily_rate' =>  3500, 'image_path' => 'https://images.unsplash.com/photo-1590845947676-fa2576f401b2?auto=format&fit=crop&w=800&q=80', 'description' => 'Handy Recorder'],
            ['name' => 'Sennheiser MKH 416',     'category' => 'Audio',    'daily_rate' =>  6000, 'image_path' => 'https://images.unsplash.com/photo-1524678606372-569663278282?auto=format&fit=crop&w=800&q=80', 'description' => 'Industry Standard Shotgun'],
            ['name' => 'Sony UWP-D21',           'category' => 'Audio',    'daily_rate' =>  4500, 'image_path' => 'https://images.unsplash.com/photo-1549488497-60dc92f7ea89?auto=format&fit=crop&w=800&q=80', 'description' => 'Wireless Lav System'],
            ['name' => 'Shure SM7B',             'category' => 'Audio',    'daily_rate' =>  5500, 'image_path' => 'https://images.unsplash.com/photo-1520529986492-5b326d547159?auto=format&fit=crop&w=800&q=80', 'description' => 'Podcast Mic'],
            ['name' => 'Rode Wireless Go II',    'category' => 'Audio',    'daily_rate' =>  3000, 'image_path' => 'https://images.unsplash.com/photo-1615923985160-5a04ce45bbf3?auto=format&fit=crop&w=800&q=80', 'description' => 'Compact Wireless'],
        ];

        foreach ($items as $item) {
            Equipment::create(array_merge($item, ['status' => 'available']));
        }
    }
}
