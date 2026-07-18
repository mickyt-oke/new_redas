<?php

namespace Database\Seeders;

use App\Models\NisDirectory;
use Illuminate\Database\Seeder;

class NisDirectorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NisDirectory::truncate();

        $data = array_merge(
            $this->passportCentres(),
            $this->stateCommands(),
            $this->zonalCommands(),
            $this->borderPosts(),
            $this->airports(),
            $this->marineCommands(),
            $this->trainingSchools(),
            $this->foreignMissions()
        );

        $data = array_map(function ($row) {
            if ($row['category'] === 'foreign_mission' && empty($row['name'])) {
                $row['name'] = trim(($row['type'] ?? 'Mission') . ' of Nigeria, ' . ($row['country'] ?? ''));
            }
            return $row;
        }, $data);

        foreach ($data as $row) {
            NisDirectory::create($row);
        }
    }

    private function passportCentres(): array
    {
        return [
            ['category' => 'passport', 'name' => 'Abakaliki', 'state' => 'Ebonyi', 'address' => 'No. 2, Afor Esuna Road, Abakaliki'],
            ['category' => 'passport', 'name' => 'Abeokuta', 'state' => 'Ogun', 'address' => 'Old Party Secretariat, Near the Governor’s Office, Oke Mosan, Abeokuta'],
            ['category' => 'passport', 'name' => 'Sagamu', 'state' => 'Ogun', 'address' => 'Passport Office, GRA, Sagamu'],
            ['category' => 'passport', 'name' => 'Ado-Ekiti', 'state' => 'Ekiti', 'address' => 'Km. 5, Afe Babalola Road, Ado-Ekiti'],
            ['category' => 'passport', 'name' => 'Akure', 'state' => 'Ondo', 'address' => 'Federal Secretariat Complex, Akure'],
            ['category' => 'passport', 'name' => 'Alausa', 'state' => 'Lagos', 'address' => 'Alausa Passport Office, Asbiffi Road, Near State Secretariat, Alausa, Ikeja'],
            ['category' => 'passport', 'name' => 'Asaba', 'state' => 'Delta', 'address' => 'Command Headquarters, Ogwashiku Road, Asaba'],
            ['category' => 'passport', 'name' => 'Awka', 'state' => 'Anambra', 'address' => 'Command Headquarters, Awka'],
            ['category' => 'passport', 'name' => 'Bauchi', 'state' => 'Bauchi', 'address' => 'Federal Secretariat, Bauchi'],
            ['category' => 'passport', 'name' => 'Benin', 'state' => 'Edo', 'address' => 'Command Headquarters, Ikpoba Hill, Benin City'],
            ['category' => 'passport', 'name' => 'Birnin Kebbi', 'state' => 'Kebbi', 'address' => 'By New Government House, Argungu Road, Birnin Kebbi'],
            ['category' => 'passport', 'name' => 'Calabar', 'state' => 'Cross River', 'address' => 'No. 2, Otuansa Street, Opposite Airport, Calabar'],
            ['category' => 'passport', 'name' => 'Damaturu', 'state' => 'Yobe', 'address' => 'Gashua Road, Near Federal Secretariat, Damaturu'],
            ['category' => 'passport', 'name' => 'Dutse', 'state' => 'Jigawa', 'address' => 'Old State Secretariat, Dutse'],
            ['category' => 'passport', 'name' => 'Enugu', 'state' => 'Enugu', 'address' => 'KM 5, Enugu/Abakaliki Expressway, Emene, Enugu'],
            ['category' => 'passport', 'name' => 'Festac', 'state' => 'Lagos', 'address' => 'Festac Passport Office, 3rd Avenue, A Close, Festac Town, Lagos'],
            ['category' => 'passport', 'name' => 'Gombe', 'state' => 'Gombe', 'address' => 'Immigration Barracks, Bauchi Road, Gombe'],
            ['category' => 'passport', 'name' => 'Alimosho', 'state' => 'Lagos', 'address' => 'Alimosho Passport Office, Alimosho Area, Lagos'],
            ['category' => 'passport', 'name' => 'Gusau', 'state' => 'Zamfara', 'address' => 'Near Government House, Gusau'],
            ['category' => 'passport', 'name' => 'Gwagwalada', 'state' => 'FCT', 'address' => 'Old Secretariat Road, Gwagwalada, Abuja'],
            ['category' => 'passport', 'name' => 'Ibadan', 'state' => 'Oyo', 'address' => 'Agodi Gate Area, Agodi, Ibadan'],
            ['category' => 'passport', 'name' => 'Ikoyi', 'state' => 'Lagos', 'address' => 'Ikoyi Passport Office, Alagbon Close, Ikoyi, Lagos'],
            ['category' => 'passport', 'name' => 'Ilorin', 'state' => 'Kwara', 'address' => 'Federal Secretariat, Fate Road, Ilorin'],
            ['category' => 'passport', 'name' => 'Jalingo', 'state' => 'Taraba', 'address' => 'Mile 6, Jalingo-Numan Road, Jalingo'],
            ['category' => 'passport', 'name' => 'Jos', 'state' => 'Plateau', 'address' => 'Federal Secretariat Complex, Jos'],
            ['category' => 'passport', 'name' => 'Kaduna', 'state' => 'Kaduna', 'address' => 'Passport Office, Independence Way, Kaduna'],
            ['category' => 'passport', 'name' => 'Kano', 'state' => 'Kano', 'address' => 'By Farm Settlement, Off Maiduguri Road, Kano'],
            ['category' => 'passport', 'name' => 'Katsina', 'state' => 'Katsina', 'address' => 'Hassan Usman Road, Katsina'],
            ['category' => 'passport', 'name' => 'Lafia', 'state' => 'Nasarawa', 'address' => 'Lafia-Makurdi Road, LGA Secretariat, Lafia-Makurdi'],
            ['category' => 'passport', 'name' => 'Lokoja', 'state' => 'Kogi', 'address' => 'Off Mount Patti Road, Lokoja'],
            ['category' => 'passport', 'name' => 'Maiduguri', 'state' => 'Borno', 'address' => 'Off Bama Road, Maiduguri'],
            ['category' => 'passport', 'name' => 'Makurdi', 'state' => 'Benue', 'address' => 'No. 6, Kashim Ibrahim Road, GRA, Makurdi'],
            ['category' => 'passport', 'name' => 'Minna', 'state' => 'Niger', 'address' => 'Old State Secretariat, Minna'],
            ['category' => 'passport', 'name' => 'Osogbo', 'state' => 'Osun', 'address' => 'Gbongan Road, Oshogbo'],
            ['category' => 'passport', 'name' => 'Owerri', 'state' => 'Imo', 'address' => 'Federal Secretariat, Onitsha Road, Owerri'],
            ['category' => 'passport', 'name' => 'Port Harcourt', 'state' => 'Rivers', 'address' => 'Federal Secretariat, Abak Road, Port Harcourt'],
            ['category' => 'passport', 'name' => 'Sokoto', 'state' => 'Sokoto', 'address' => 'Command Complex, Gusau Road, Sokoto'],
            ['category' => 'passport', 'name' => 'Umuahia', 'state' => 'Abia', 'address' => 'No. 72, School Road, Umuahia'],
            ['category' => 'passport', 'name' => 'Uyo', 'state' => 'Akwa Ibom', 'address' => 'Federal Secretariat, Abak Road, Uyo'],
            ['category' => 'passport', 'name' => 'Warri', 'state' => 'Delta', 'address' => 'Edjeba, Warri'],
            ['category' => 'passport', 'name' => 'Yenogoa', 'state' => 'Bayelsa', 'address' => 'Road Safety Road, Yenogoa'],
            ['category' => 'passport', 'name' => 'Yola', 'state' => 'Adamawa', 'address' => 'Federal Secretariat Complex, Yola'],
            ['category' => 'passport', 'name' => 'SHQ', 'state' => 'FCT', 'address' => 'Nigeria Immigration Headquarters, Airport Road, Sauka, Abuja'],
            ['category' => 'passport', 'name' => 'Dawankin Kudu', 'state' => 'Kano', 'address' => 'Kombotso Area, Zaria Road, Kano'],
        ];
    }

    private function stateCommands(): array
    {
        return [
            ['category' => 'state_command', 'name' => 'Abia State Command', 'state' => 'Abia', 'address' => 'Ubakal Village, Along Enugu-Port Harcourt Expressway, Umuahia South, Umuahia', 'email' => 'nis.abia@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Adamawa State Command', 'state' => 'Adamawa', 'address' => '2nd Floor, Federal Secretariat, Jimeta Road, Yola', 'email' => 'nis.adamawa@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Akwa Ibom State Command', 'state' => 'Akwa Ibom', 'address' => '1st Floor, Federal Secretariat, Abak Road, Uyo', 'email' => 'nis.akwaibom@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Anambra State Command', 'state' => 'Anambra', 'address' => 'Nnewi Street, Agu Awka, Awka', 'email' => 'nis.anambra@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Bauchi State Command', 'state' => 'Bauchi', 'address' => '2nd Floor, Federal Secretariat, Yakubun Bauchi Street, Bauchi', 'email' => 'nis.bauchi@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Bayelsa State Command', 'state' => 'Bayelsa', 'address' => 'Bayelsa Palms, Along Elebele Road, Yenagoa', 'email' => 'nis.bayelsa@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Benue State Command', 'state' => 'Benue', 'address' => '6, Kashim Ibrahim Way, Old GRA, Makurdi', 'email' => 'nis.benue@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Borno State Command', 'state' => 'Borno', 'address' => 'Federal Secretariat, Kano Road, Maiduguri', 'email' => 'nis.borno@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Cross River State Command', 'state' => 'Cross River', 'address' => '2, Otuansa Street, Calabar, Opposite Margaret Ekpo International Airport Road', 'email' => 'nis.crossriver@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Delta State Command', 'state' => 'Delta', 'address' => 'Ogwashi Uku Road, Asaba', 'email' => 'nis.delta@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Ebonyi State Command', 'state' => 'Ebonyi', 'address' => 'Enugu-Abakaliki Highway, Umuoghara Abakaliki', 'email' => 'nis.ebonyi@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Edo State Command', 'state' => 'Edo', 'address' => 'Ikpobu Hill, Benin City', 'email' => 'nis.edo@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Ekiti State Command', 'state' => 'Ekiti', 'address' => 'Km 5, Afe Babalola/Fed Poly Road, Ado Ekiti', 'email' => 'nis.ekiti@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Enugu State Command', 'state' => 'Enugu', 'address' => 'Federal Secretariat Complex, Independence Layout, Enugu', 'email' => 'nis.enugu@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Gombe State Command', 'state' => 'Gombe', 'address' => 'Adjacent to NIPOST, Bauchi Road, Gombe', 'email' => 'nis.gombe@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Imo State Command', 'state' => 'Imo', 'address' => 'Federal Secretariat Complex, Port Harcourt Road, Owerri', 'email' => 'nis.imo@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Jigawa State Command', 'state' => 'Jigawa', 'address' => 'Federal Secretariat Complex, Sani Abacha Way, Dutse', 'email' => 'nis.jigawa@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Kaduna State Command', 'state' => 'Kaduna', 'address' => 'Independence Way, Kaduna', 'email' => 'nis.kaduna@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Kano State Command', 'state' => 'Kano', 'address' => '1, Police Barracks Road, Kano', 'email' => 'nis.kano@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Katsina State Command', 'state' => 'Katsina', 'address' => 'Federal Secretariat Complex, IBB Way, Katsina', 'email' => 'nis.katsina@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Kebbi State Command', 'state' => 'Kebbi', 'address' => '2, Lokoja Road GRA, Birnin-Kebbi', 'email' => 'nis.kebbi@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Kogi State Command', 'state' => 'Kogi', 'address' => '28, Mount Patti Road, New Layout, Lokoja', 'email' => 'nis.kogi@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Kwara State Command', 'state' => 'Kwara', 'address' => 'Federal Secretariat Complex, GRA, Ilorin', 'email' => 'nis.kwara@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Lagos State Command', 'state' => 'Lagos', 'address' => '2, Alagbon Close, Ikoyi-Lagos', 'email' => 'nis.lagos@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Nasarawa State Command', 'state' => 'Nasarawa', 'address' => 'Behind LGA Secretariat, Makurdi-Lafia Road, Lafia', 'email' => 'nis.nasarawa@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Niger State Command', 'state' => 'Niger', 'address' => 'Block E, Old Secretariat Complex, Muazu Mohammed Road, Minna', 'email' => 'nis.niger@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Ogun State Command', 'state' => 'Ogun', 'address' => 'Oke-Mosan, Abeokuta', 'email' => 'nis.ogun@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Ondo State Command', 'state' => 'Ondo', 'address' => 'Beside Federal Secretariat Complex, Igbatoro Road, Akure', 'email' => 'nis.ondo@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Osun State Command', 'state' => 'Osun', 'address' => 'Federal Secretariat Complex, Ogo-Oluwa, Oshogbo', 'email' => 'nis.osun@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Oyo State Command', 'state' => 'Oyo', 'address' => 'Opposite Government House, Agodi Gate, Ibadan', 'email' => 'nis.oyo@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Plateau State Command', 'state' => 'Plateau', 'address' => 'Federal Secretariat, Tudun Wada, Jos', 'email' => 'nis.plateau@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Rivers State Command', 'state' => 'Rivers', 'address' => 'Federal Secretariat, Aba Road, Port Harcourt', 'email' => 'nis.rivers@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Sokoto State Command', 'state' => 'Sokoto', 'address' => 'Federal Secretariat, Along Kaduna Road, Sokoto', 'email' => 'nis.sokoto@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Taraba State Command', 'state' => 'Taraba', 'address' => 'Jalingo-Numan Road, Mile 6, Jalingo', 'email' => 'nis.taraba@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Yobe State Command', 'state' => 'Yobe', 'address' => 'Adjacent to Federal Secretariat, Along Gashua Road, Damaturu', 'email' => 'nis.yobe@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Zamfara State Command', 'state' => 'Zamfara', 'address' => '1, Sokoto Road, Behind Government House, Gusau', 'email' => 'nis.zamfara@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'FCT Command', 'state' => 'FCT', 'address' => 'Gwagwalada', 'email' => 'nis.fct@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Seaport State Command', 'state' => 'Lagos', 'address' => 'Immigration Command, Shed 6, Lagos Sea Port, Apapa', 'email' => 'nis.apapa@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Seme State Command', 'state' => 'Lagos', 'address' => 'Badagry-Seme Road, Seme', 'email' => 'nis.seme@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Onne Marine Command', 'state' => 'Rivers', 'address' => 'Onne Port, Rivers State', 'email' => 'nis.onneport@nigeriaimmigration.gov.ng'],
            ['category' => 'state_command', 'name' => 'Lagos Border Patrol', 'state' => 'Lagos', 'address' => 'Seme', 'email' => 'nis.lgsbpc@nigeriaimmigration.gov.ng'],
        ];
    }

    private function zonalCommands(): array
    {
        return [
            ['category' => 'zonal', 'name' => "Zone 'A' (Lagos)", 'address' => 'PWD Bus Stop, Former Passport Office, Ikeja', 'email' => 'nis.zoneA@nigeriaimmigration.gov.ng'],
            ['category' => 'zonal', 'name' => "Zone 'B' (Kaduna)", 'address' => 'Federal Secretariat, Ahmadu Bello Way, Kaduna', 'email' => 'nis.zoneB@nigeriaimmigration.gov.ng'],
            ['category' => 'zonal', 'name' => "Zone 'C' (Bauchi)", 'address' => 'Customs House, Yelwa, Bauchi', 'email' => 'nis.zoneC@nigeriaimmigration.gov.ng'],
            ['category' => 'zonal', 'name' => "Zone 'D' (Minna)", 'address' => 'Niger State Secretariat, Minna', 'email' => 'nis.zoneD@nigeriaimmigration.gov.ng'],
            ['category' => 'zonal', 'name' => "Zone 'E' (Owerri)", 'address' => 'Customs House, Onitsha Road, Owerri', 'email' => 'nis.zoneE@nigeriaimmigration.gov.ng'],
            ['category' => 'zonal', 'name' => "Zone 'F' (Ibadan)", 'address' => 'Customs Building, Bodija, Ibadan', 'email' => 'nis.zoneF@nigeriaimmigration.gov.ng'],
            ['category' => 'zonal', 'name' => "Zone 'G' (Benin City)", 'address' => 'Ikpoba Hill, By Command Headquarters, Benin City', 'email' => 'nis.zoneG@nigeriaimmigration.gov.ng'],
            ['category' => 'zonal', 'name' => "Zone 'H' (Makurdi)", 'address' => '6, Kashim Ibrahim Road, Command Headquarters, Makurdi', 'email' => 'nis.zoneH@nigeriaimmigration.gov.ng'],
        ];
    }

    private function borderPosts(): array
    {
        return [
            ['category' => 'border', 'name' => 'Banki Control Post', 'state' => 'Borno', 'address' => 'Banki, Borno State', 'email' => 'nis.banki@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Belel Control Post', 'state' => 'Adamawa', 'address' => 'Belel, Adamawa State', 'email' => 'nis.belel@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Baban Mutum Control Post', 'state' => 'Jigawa', 'address' => 'Baban Mutum, Jigawa State', 'email' => 'nis.babanmittum@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Chikanda Control Post', 'state' => 'Kwara', 'address' => 'Chikanda, Kwara State', 'email' => 'nis.chikandag@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Ekang Control Post', 'state' => 'Cross River', 'address' => 'Ekang, Cross River State', 'email' => 'nis.ekang@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Gamboru-Ngala Border Patrol', 'state' => 'Borno', 'address' => 'Gamboru-Ngala, Borno State', 'email' => 'nis.gamborungala@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Jibiya Control Post', 'state' => 'Katsina', 'address' => 'Jibiya, Katsina State', 'email' => 'nis.jibiya@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Illela Control Post', 'state' => 'Sokoto', 'address' => 'Illela, Sokoto State', 'email' => 'nis.illella@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Idi-Iroko Border Patrol', 'state' => 'Ogun', 'address' => 'Idi-Iroko, Ogun State', 'email' => 'nis.idi-iroko@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Ikang Control Post', 'state' => 'Cross River', 'address' => 'Ikang, Cross River State', 'email' => 'nis.ikang@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Imeko Control Post', 'state' => 'Ogun', 'address' => 'Imeko, Ogun State', 'email' => 'nis.imeko@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Kamba Control Post', 'state' => 'Kebbi', 'address' => 'Kamba, Kebbi State', 'email' => 'nis.kamba@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Kongolan Control Post', 'state' => 'Jigawa', 'address' => 'Kongolan, Jigawa State', 'email' => 'nis.kongolan@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Jato Akaa Control Post', 'state' => 'Benue', 'address' => 'Jato Akaa, Benue State', 'email' => 'nis.jatoakaa@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Maitagari Control Post', 'state' => 'Jigawa', 'address' => 'Maitagari, Jigawa State', 'email' => 'nis.maitagari@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Mfum Border Patrol', 'state' => 'Cross River', 'address' => 'Mfum, Cross River State', 'email' => 'nis.mfum@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Mambila Plateau Control Post', 'state' => 'Taraba', 'address' => 'Mambila Plateau, Taraba State', 'email' => 'nis.mambila@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Yusufari Control Post', 'state' => 'Yobe', 'address' => 'Yusufari, Yobe State', 'email' => 'nis.mambila@nigeriaimmigration.gov.ng'],
            ['category' => 'border', 'name' => 'Zangon Daura Control Post', 'state' => 'Katsina', 'address' => 'Zangon Daura, Katsina State', 'email' => 'nis.zangodaura@nigeriaimmigration.gov.ng'],
        ];
    }

    private function airports(): array
    {
        return [
            ['category' => 'airport', 'name' => 'Murtala Muhammed International Airport', 'code' => 'MMIA', 'state' => 'Lagos', 'address' => 'MMIA, Ikeja, Lagos', 'email' => 'mmia@nigeriaimmigration.gov.ng'],
            ['category' => 'airport', 'name' => 'Nnamdi Azikiwe International Airport', 'code' => 'NAIA', 'state' => 'FCT', 'address' => 'NAIA, Abuja', 'email' => 'naia@nigeriaimmigration.gov.ng'],
            ['category' => 'airport', 'name' => 'Akanu Ibiam International Airport', 'code' => 'AIIA', 'state' => 'Enugu', 'address' => 'AIIA, Enugu', 'email' => 'aiia@nigeriaimmigration.gov.ng'],
            ['category' => 'airport', 'name' => 'Mallam Aminu Kano International Airport', 'code' => 'MAKIA', 'state' => 'Kano', 'address' => 'MAKIA, Kano', 'email' => 'makia@nigeriaimmigration.gov.ng'],
            ['category' => 'airport', 'name' => 'Port Harcourt International Airport', 'code' => 'PHIA', 'state' => 'Rivers', 'address' => 'PHIA, Port Harcourt', 'email' => 'phia@nigeriaimmigration.gov.ng'],
        ];
    }

    private function marineCommands(): array
    {
        return [
            ['category' => 'marine', 'name' => 'Seaport State Command', 'state' => 'Lagos', 'address' => 'Immigration Command, Shed 6, Lagos Sea Port, Apapa', 'email' => 'nis.apapa@nigeriaimmigration.gov.ng'],
            ['category' => 'marine', 'name' => 'Onne Marine Command', 'state' => 'Rivers', 'address' => 'Onne Port, Rivers State', 'email' => 'nis.onneport@nigeriaimmigration.gov.ng'],
            ['category' => 'marine', 'name' => 'Seme State Command', 'state' => 'Lagos', 'address' => 'Badagry-Seme Road, Seme', 'email' => 'nis.seme@nigeriaimmigration.gov.ng'],
            ['category' => 'marine', 'name' => 'Lagos Border Patrol', 'state' => 'Lagos', 'address' => 'Seme, Lagos State', 'email' => 'nis.lgsbpc@nigeriaimmigration.gov.ng'],
        ];
    }

    private function trainingSchools(): array
    {
        return [
            ['category' => 'training', 'name' => 'Immigration Training School, Ahoada', 'state' => 'Rivers', 'address' => 'Ahoada Town, Rivers State', 'email' => 'nis.nitsa@nigeriaimmigration.gov.ng'],
            ['category' => 'training', 'name' => 'Immigration Training School, Kano', 'state' => 'Kano', 'address' => 'Airport Road, Sabongari, Kano', 'email' => 'nis.itsk@nigeriaimmigration.gov.ng'],
            ['category' => 'training', 'name' => 'Immigration Training School, Orlu', 'state' => 'Imo', 'address' => 'Umo-Owa, Orlu', 'email' => 'nis.nitsol@nigeriaimmigration.gov.ng'],
            ['category' => 'training', 'name' => 'Command & Staff College, Sokoto', 'state' => 'Sokoto', 'address' => 'Old Airport, Sokoto', 'email' => 'nis.csc-sokoto@nigeriaimmigration.gov.ng'],
        ];
    }

    private function foreignMissions(): array
    {
        return [
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Algeria', 'city' => 'Algiers', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Algiers, Algeria'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Angola', 'city' => 'Luanda', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Luanda, Angola'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Botswana', 'city' => 'Gaborone', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Gaborone, Botswana'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Burkina Faso', 'city' => 'Ouagadougou', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Ouagadougou, Burkina Faso'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Burundi', 'city' => 'Bujumbura', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Bujumbura, Burundi'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Cameroon', 'city' => 'Yaounde', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Yaounde, Cameroon'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Cameroon', 'city' => 'Buea', 'type' => 'Consulate General', 'address' => 'Consulate General of Nigeria, Buea, Cameroon'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Cameroon', 'city' => 'Douala', 'type' => 'Consulate General', 'address' => 'Consulate General of Nigeria, Douala, Cameroon'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Central African Republic', 'city' => 'Bangui', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Bangui, Central African Republic'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Chad', 'city' => 'N’Djamena', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, N’Djamena, Chad'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Congo (Democratic Republic)', 'city' => 'Kinshasa', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Kinshasa, DR Congo'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Congo (Republic)', 'city' => 'Brazzaville', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Brazzaville, Congo'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Cote d’Ivoire', 'city' => 'Abidjan', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Abidjan, Cote d’Ivoire'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Egypt', 'city' => 'Cairo', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Cairo, Egypt'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Equatorial Guinea', 'city' => 'Malabo', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Malabo, Equatorial Guinea'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Equatorial Guinea', 'city' => 'Bata', 'type' => 'Consulate', 'address' => 'Consulate of Nigeria, Bata, Equatorial Guinea'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Eritrea', 'city' => 'Asmara', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Asmara, Eritrea'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Ethiopia', 'city' => 'Addis Ababa', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Addis Ababa, Ethiopia'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Gabon', 'city' => 'Libreville', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Libreville, Gabon'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Gambia', 'city' => 'Banjul', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Banjul, Gambia'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Ghana', 'city' => 'Accra', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Accra, Ghana'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Guinea', 'city' => 'Conakry', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Conakry, Guinea'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Guinea-Bissau', 'city' => 'Bissau', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Bissau, Guinea-Bissau'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Kenya', 'city' => 'Nairobi', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Nairobi, Kenya'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Liberia', 'city' => 'Monrovia', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Monrovia, Liberia'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Libya', 'city' => 'Tripoli', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Tripoli, Libya'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Malawi', 'city' => 'Lilongwe', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Lilongwe, Malawi'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Mali', 'city' => 'Bamako', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Bamako, Mali'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Mauritania', 'city' => 'Nouakchott', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Nouakchott, Mauritania'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Morocco', 'city' => 'Rabat', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Rabat, Morocco'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Mozambique', 'city' => 'Maputo', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Maputo, Mozambique'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Namibia', 'city' => 'Windhoek', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Windhoek, Namibia'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Niger', 'city' => 'Niamey', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Niamey, Niger'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Rwanda', 'city' => 'Kigali', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Kigali, Rwanda'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Sao Tome and Principe', 'city' => 'Sao Tome', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Sao Tome, Sao Tome and Principe'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Senegal', 'city' => 'Dakar', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Dakar, Senegal'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Sierra Leone', 'city' => 'Freetown', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Freetown, Sierra Leone'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'South Africa', 'city' => 'Pretoria', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Pretoria, South Africa'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'South Africa', 'city' => 'Johannesburg', 'type' => 'Consulate General', 'address' => 'Consulate General of Nigeria, Johannesburg, South Africa'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'South Sudan', 'city' => 'Juba', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Juba, South Sudan'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Sudan', 'city' => 'Khartoum', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Khartoum, Sudan'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Tanzania', 'city' => 'Dar-es-Salaam', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Dar-es-Salaam, Tanzania'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Togo', 'city' => 'Lome', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Lome, Togo'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Tunisia', 'city' => 'Tunis', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Tunis, Tunisia'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Uganda', 'city' => 'Kampala', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Kampala, Uganda'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Zambia', 'city' => 'Lusaka', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Lusaka, Zambia'],
            ['category' => 'foreign_mission', 'region' => 'Africa', 'country' => 'Zimbabwe', 'city' => 'Harare', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Harare, Zimbabwe'],
            ['category' => 'foreign_mission', 'region' => 'Americas', 'country' => 'Argentina', 'city' => 'Buenos Aires', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Buenos Aires, Argentina'],
            ['category' => 'foreign_mission', 'region' => 'Americas', 'country' => 'Brazil', 'city' => 'Brasilia', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Brasilia, Brazil'],
            ['category' => 'foreign_mission', 'region' => 'Americas', 'country' => 'Canada', 'city' => 'Ottawa', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Ottawa, Canada'],
            ['category' => 'foreign_mission', 'region' => 'Americas', 'country' => 'Cuba', 'city' => 'Havana', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Havana, Cuba'],
            ['category' => 'foreign_mission', 'region' => 'Americas', 'country' => 'Jamaica', 'city' => 'Kingston', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Kingston, Jamaica'],
            ['category' => 'foreign_mission', 'region' => 'Americas', 'country' => 'Mexico', 'city' => 'Mexico City', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Mexico City, Mexico'],
            ['category' => 'foreign_mission', 'region' => 'Americas', 'country' => 'Trinidad and Tobago', 'city' => 'Port-of-Spain', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Port-of-Spain, Trinidad and Tobago'],
            ['category' => 'foreign_mission', 'region' => 'Americas', 'country' => 'United States', 'city' => 'Washington, D.C.', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Washington, D.C., USA'],
            ['category' => 'foreign_mission', 'region' => 'Americas', 'country' => 'United States', 'city' => 'Atlanta', 'type' => 'Consulate General', 'address' => 'Consulate General of Nigeria, Atlanta, USA'],
            ['category' => 'foreign_mission', 'region' => 'Americas', 'country' => 'United States', 'city' => 'New York', 'type' => 'Consulate General', 'address' => 'Consulate General of Nigeria, New York, USA'],
            ['category' => 'foreign_mission', 'region' => 'Americas', 'country' => 'Venezuela', 'city' => 'Caracas', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Caracas, Venezuela'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'China', 'city' => 'Beijing', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Beijing, China'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'China', 'city' => 'Guangzhou', 'type' => 'Consulate General', 'address' => 'Consulate General of Nigeria, Guangzhou, China'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'China', 'city' => 'Shanghai', 'type' => 'Consulate General', 'address' => 'Consulate General of Nigeria, Shanghai, China'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Hong Kong', 'city' => 'Hong Kong', 'type' => 'Consulate General', 'address' => 'Consulate General of Nigeria, Hong Kong'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'India', 'city' => 'New Delhi', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, New Delhi, India'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Indonesia', 'city' => 'Jakarta', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Jakarta, Indonesia'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Iran', 'city' => 'Tehran', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Tehran, Iran'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Israel', 'city' => 'Tel Aviv', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Tel Aviv, Israel'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Japan', 'city' => 'Tokyo', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Tokyo, Japan'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Jordan', 'city' => 'Amman', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Amman, Jordan'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Korea (Democratic Republic)', 'city' => 'Pyongyang', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Pyongyang, North Korea'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Korea (Republic)', 'city' => 'Seoul', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Seoul, South Korea'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Kuwait', 'city' => 'Kuwait City', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Kuwait City, Kuwait'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Lebanon', 'city' => 'Beirut', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Beirut, Lebanon'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Malaysia', 'city' => 'Kuala Lumpur', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Kuala Lumpur, Malaysia'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Pakistan', 'city' => 'Islamabad', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Islamabad, Pakistan'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Philippines', 'city' => 'Manila', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Manila, Philippines'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Qatar', 'city' => 'Doha', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Doha, Qatar'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Saudi Arabia', 'city' => 'Riyadh', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Riyadh, Saudi Arabia'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Saudi Arabia', 'city' => 'Jeddah', 'type' => 'Consulate General', 'address' => 'Consulate General of Nigeria, Jeddah, Saudi Arabia'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Singapore', 'city' => 'Singapore', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, Singapore'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Syria', 'city' => 'Damascus', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Damascus, Syria'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Thailand', 'city' => 'Bangkok', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Bangkok, Thailand'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'United Arab Emirates', 'city' => 'Abu Dhabi', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Abu Dhabi, UAE'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'United Arab Emirates', 'city' => 'Dubai', 'type' => 'Consulate General', 'address' => 'Consulate General of Nigeria, Dubai, UAE'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Vietnam', 'city' => 'Hanoi', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Hanoi, Vietnam'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Austria', 'city' => 'Vienna', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Vienna, Austria'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Belgium', 'city' => 'Brussels', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Brussels, Belgium'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Bulgaria', 'city' => 'Sofia', 'type' => 'Embassy Office', 'address' => 'Embassy of Nigeria, Sofia, Bulgaria'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Czech Republic', 'city' => 'Prague', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Prague, Czech Republic'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'European Union', 'city' => 'Brussels', 'type' => 'Mission', 'address' => 'Mission of Nigeria to the EU, Brussels, Belgium'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'France', 'city' => 'Paris', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Paris, France'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Germany', 'city' => 'Berlin', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Berlin, Germany'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Germany', 'city' => 'Frankfurt am Main', 'type' => 'Consulate General', 'address' => 'Consulate General of Nigeria, Frankfurt, Germany'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Greece', 'city' => 'Athens', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Athens, Greece'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Holy See', 'city' => 'Vatican City', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria to the Holy See, Vatican City'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Hungary', 'city' => 'Budapest', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Budapest, Hungary'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Ireland', 'city' => 'Dublin', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Dublin, Ireland'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Italy', 'city' => 'Rome', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Rome, Italy'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Netherlands', 'city' => 'The Hague', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, The Hague, Netherlands'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Poland', 'city' => 'Warsaw', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Warsaw, Poland'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Portugal', 'city' => 'Lisbon', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Lisbon, Portugal'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Romania', 'city' => 'Bucharest', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Bucharest, Romania'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Russia', 'city' => 'Moscow', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Moscow, Russia'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Spain', 'city' => 'Madrid', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Madrid, Spain'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Sweden', 'city' => 'Stockholm', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Stockholm, Sweden'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Switzerland', 'city' => 'Bern', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Bern, Switzerland'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'Ukraine', 'city' => 'Kyiv', 'type' => 'Embassy', 'address' => 'Embassy of Nigeria, Kyiv, Ukraine'],
            ['category' => 'foreign_mission', 'region' => 'Europe', 'country' => 'United Kingdom', 'city' => 'London', 'type' => 'High Commission', 'address' => 'High Commission of Nigeria, London, UK'],
            ['category' => 'foreign_mission', 'region' => 'International Organisations', 'country' => 'United Nations', 'city' => 'New York', 'type' => 'Permanent Mission', 'address' => 'Permanent Mission of Nigeria to the UN, New York, USA'],
            ['category' => 'foreign_mission', 'region' => 'International Organisations', 'country' => 'United Nations', 'city' => 'Geneva', 'type' => 'Permanent Mission', 'address' => 'Permanent Mission of Nigeria to the UN, Geneva, Switzerland'],
            ['category' => 'foreign_mission', 'region' => 'Asia', 'country' => 'Taiwan', 'city' => 'Taipei', 'type' => 'Representative Office', 'address' => 'Nigeria Trade Office, Taipei, Taiwan'],
        ];
    }
}
