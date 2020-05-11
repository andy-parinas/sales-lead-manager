<?php

use App\LeadSource;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sources = [
            'Newspaper', 'Magazine', 'Radio',
            'Self Genrated', 'Website', 'Friend',
            'Yello Pages', 'Yellow Pages Online',
            'Refferal', 'Pamphlets', 'Walk-In',
            'Return Customer', 'Home Show', 'Sit Sign',
            'Local Directory', 'Trade Contact', 'Facbook',
            'Google or Search Engine', 'Insurance Company', 'Local Newspaper',
            'Vehicle Sign', 'Hi Pages', 'Chronicle', 'Shopping Centre', 'Direct Mail Out',
            'DL Intro Card', 'Ag Show', 'Facebook', 'Twitter', 'LinkedIn', 'Instagram', 'Unknown'
        ];

        foreach ($sources as $source){
            factory(LeadSource::class)->create(['name' => $source]);
        }
    }
}
