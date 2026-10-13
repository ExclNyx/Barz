<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(['email' => 'admin@barz.com'], ['name' => 'Admin Barz', 'phone' => '081234567890', 'password' => Hash::make('barz2024'), 'role' => 'admin', 'updated_at' => now(), 'created_at' => now()]);
        DB::table('users')->updateOrInsert(['email' => 'superadmin@barz.com'], ['name' => 'Super Admin Barz', 'phone' => '081234567891', 'password' => Hash::make('superadmin'), 'role' => 'super_admin', 'updated_at' => now(), 'created_at' => now()]);
        $services = [
            ['name'=>'Haircut Basic','description'=>'Potong rambut standar dengan teknik dasar','price'=>35000,'duration'=>30],
            ['name'=>'Haircut Premium','description'=>'Potong rambut dengan styling modern dan konsultasi','price'=>75000,'duration'=>45],
            ['name'=>'Haircut + Shave','description'=>'Paket lengkap potong rambut dan cukur jenggot','price'=>85000,'duration'=>60],
            ['name'=>'Kids Haircut','description'=>'Potong rambut khusus anak-anak','price'=>30000,'duration'=>25],
            ['name'=>'Beard Trim','description'=>'Rapikan jenggot dan kumis','price'=>25000,'duration'=>20],
            ['name'=>'Hair Coloring','description'=>'Pewarnaan rambut dengan produk premium','price'=>150000,'duration'=>90],
        ];
        foreach ($services as $service) if (!DB::table('services')->where('name', $service['name'])->exists()) DB::table('services')->insert($service + ['is_active'=>1,'created_at'=>now(),'updated_at'=>now()]);
        foreach ([['name'=>'Andi','specialization'=>'Classic & Modern Cuts','experience_years'=>5],['name'=>'Budi','specialization'=>'Fade & Undercut Specialist','experience_years'=>3],['name'=>'Cecep','specialization'=>'Beard & Styling Expert','experience_years'=>7]] as $barber) if (!DB::table('barbers')->where('name',$barber['name'])->exists()) DB::table('barbers')->insert($barber + ['is_active'=>1,'created_at'=>now(),'updated_at'=>now()]);
        foreach (['shop_name'=>'Barz Barbershop','opening_time'=>'09:00','closing_time'=>'21:00','slot_duration'=>'15'] as $key=>$value) if (!DB::table('settings')->where('setting_key',$key)->exists()) DB::table('settings')->insert(['setting_key'=>$key,'setting_value'=>$value,'created_at'=>now(),'updated_at'=>now()]);
    }
}
