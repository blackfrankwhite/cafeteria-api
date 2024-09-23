<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Entity;
use App\Models\EntityMap;

class Ingredients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ingredients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dishes = [
            'ქათმის ვრაპი' => [
                'ქათმის ფილე' => '0.150',
                'სომხური ლავაში' => 1,
                'სალათის ფოთოლი' => '0.020',
                'პომიდორი' => '0.030',
                'ძაძიკის სოუსი' => '0.080',
                'amount' => 1,
            ],
            'ძაძიკის სოუსი' => [
                'ძაძიკის სოუსი' => '1კგ',
                'არაჟანი' => '0.700',
                'მაწონი' => '0.100',
                'პიტნა' => '0.050',
                'ქინძი' => '0.050',
                'მარილი' => '0.005',
                'პილპილი' => '0.005',
                'კიტრი' => '0.300',
                "amount" => 1,
                'measurment_type' => 'kg',
                'type' => 'mix'
            ],
            'ბოსტნეულის ვრაპი' => [
                'სომხური ლავაში' => 1,
                'სალათის ფოთოლი' => '0.020',
                'პომიდორი' => '0.030',
                'კიტრი' => '0.060',
                'სტაფილო' => '0.060',
                'წითელი ბულგარული' => '0.060',
                'წითელი კომბოსტო' => '0.050',
                'არაჟანი' => '0.080',
                'პაპრიკა' => '0.001',
                "amount" => 1,
            ],
            'ბოულ სალათი ცეზარი' => [
                'ქათმის ფილე' => '0.200',
                'სალათის ფოთოლი' => '0.100',
                'კვერცხი' => 1,
                'ყველი პარმეზანი' => '0.030',
                'კრუტონები' => '0.020',
                'ცეზარის დრესინგი' => '0.050',
                "amount" => 1,
            ],
            'ცეზარის დრესინგი' => [
                'ცეზარის დრესინგი' => '1კგ',
                'კვერცხი' => '12 ცალი',
                'ვორჩესტერ სოუსი' => '0.100',
                'მდოგვი მარცვლოვანი' => '0.050',
                'მარილი' => '0.005',
                'პილპილი' => '0.005',
                'ანჩოუსი' => '0.005',
                'ზეთი მზესუმზირის' => '0.500',
                "amount" => 1,
                'measurment_type' => 'kg',
                'type' => 'mix'
            ],
            'ბოულ სალათი ბერძნული' => [
                'პომიდორი' => '0.150',
                'კიტრი' => '0.150',
                'წითლი ბულგარული' => '0.100',
                'წითელი ხახვი' => '0.080',
                'ორეგანო' => '0.001',
                'ლიმონი' => '0.030',
                'ფეტა' => '0.050',
                'შავი ზეთისხილი უკურკო' => '0.050',
                'ზეთი მზესუმზირის' => '0.050',
                'მარილი' => '0.001',
                "amount" => 1,
            ],
            'ბოულ სალათი ბოსტნეულით და ქინოათი' => [
                'ქინოა' => '0.080',
                'წითელი კომბოსტო' => '0.100',
                'ჩერი პომიდორი' => '0.050',
                'კიტრი' => '0.120',
                'სიმინდის კონსერვი' => '0.050',
                'ზეთი მზესუმზირი' => '0.050',
                'მაირლი' => '0.002',
                'ლიმონი' => '0.050',
                "amount" => 1,
            ],
            'კრუასანის სენდვიჩი სასენდვიჩე ლორით' => [
                'კრუასანი' => 1,
                'სასენდვიჩე ღორის ლორი' => '0.050',
                'პომიდორი' => '0.040',
                'სასლათის ფოთოლი' => '0.010',
                'კიტრი' => '0.030',
                'შებოლილი გერმანული ყველი ლორით' => '0.020',
                'ცეზარის დრესინგი' => '0.050'
            ],
            'კრუასანის სენდვიჩი ინდაურის ლორით' => [
                'კრუასანი' => 1,
                'პომიდორი' => '0.040',
                'სალათის ფოთოლი' => '0.010',
                'შებოლილი გერმანული ყველი ლორით' => '0.020',
                'ინდაურის ლორი' => '0.050',
                'კიტრი' => '0.030',
                'არაჟნის სოუსი' => '0.050'
            ],
            'არაჟნის სოუსი' => [
                'არაჟანი' => '0.800',
                'მჟავე კიტრი' => '0.100',
                'ორეგანო' => '0.005',
                'ხახვი' => '0.100',
                'ვორჩესტერ სოუსი' => '0.050',
                'მდოგვი მარცვლოვანი' => '0.050',
                'amount' => 1,
                'measurment_type' => 'kg',
                'type' => 'mix'
            ],
            'სალიამის სენდვიჩი - ტოსტის პური / ბაგეტის პური' => [
                'სალიამი' => '0.040',
                'ედამერის ყველი' => '0.030',
                'სალათის ფოთოლი' => '0.010',
                'ცეზარის დრესინგი' => '0.030',
                'მდოგვი მარცვლოვანი' => '0.020',
                'მჟვე კიტრი' => '0.050'
            ],
            'ბოსტნეულის სენდვიჩი - ტოსტის პური / ბაგეტის პური' => [
                'კიტრი' => '0.100',
                'პომიდორი' => '0.100',
                'სალათის ფოთოლი' => '0.010',
                'სტაფილო' => '0.050',
                'წითელი კომბსოტო' => '0.040',
                'თეთრი კომბოსტოს მწნილი' => '0.030',
                'ედამერის ყველი' => '0.030',
                'ძაძიკის სოუსი' => '0.050'
            ],
            'ჰოთ დოგი' => [
                'სოსისი ლიდერფუდის სპეცი' => '0.080',
                'თეთრი ხახვი' => '0.030',
                'თეთრი ძმარი' => '0.010',
                'ჩვენს მიერ მომზადებული მაიონეზი' => '0.050',
                'მდოგვი მარცვლოვანი' => '0.020',
                'მჟავე კიტრი' => '0.020'
            ],
            'პასტა პომიდვრით' => [
                'სპაგეტი' => '0.120',
                'ზეთი მზესუმზირის' => '0.050',
                'კანგაცლილი პომიდორი' => '0.100',
                'ორეგანო' => '0.002',
                'მარილი' => '0.002',
                'პილპილი' => '0.001',
                'ნიორი' => '0.030',
                'ტომატ პასტა' => '0.020',
                'ჩერი პომიდორი' => '0.050'
            ],
            'პასტა ყველით' => [
                'სპაგეტი' => '0.120',
                'ყველი' => '0.030',
                'კარტოფილი' => '0.800',
                'რძე' => '0.100',
                'კარაქი' => '0.100',
                'მარილი' => '0.001',
                'პიურე' => '0.200'
            ],
            'პიურე' => [
                'პიურე' => '0.250',
                'ყველი' => '0.100',
                'type' => 'mix',
                'amount' => '1'

            ],
            'ქათმის ქაბაბი ' => [
                'ქათმის ფილე' => '4.500კგ',
                'ხახვი' => '0.500',
                'ნიორი' => '0.050',
                'ქინძი' => '0.050',
                'უცხო სუნელი' => '0.010',
                'მარილი' => '0.010',
                'ძირა' => '0.005',
                'კვერცხი' => '5ცალი',
                'ზეთი მზესუმზირის' => '0.200',
                'პორცია ქათმის ქაბაბი' => '0.250 1 ცალი',
                'სომხური ლავაში' => 1,
                'სუმახი' => '0.001',
                'ქინძი' => '0.005',
                'ხახვი' => '0.010',
                "amount" => 5,
                'measurment_type' => 'kg'
            ],
            'ბოლონეზე' => [
                'საქონლის ხორცი' => '3კგ',
                'ღორის ხორცი' => '2 კგ',
                'ხახვი' => '0.500',
                'სტაფილო' => '0.500',
                'მარილი' => '0.010',
                'პილპილი' => '0.005',
                'ორეგანო' => '0.010',
                'ზეთი' => '0.010',
                'ტომატ პასტა' => '0.500',
                'ხახვი' => '0.010',
                "amount" => 5,
                'measurment_type' => 'kg'
            ],
            'პასტა ბოლონეზე' => [
                'სპაგეტი' => '0.120',
                'პასტა ბოლონეზე პორცია' => '0.100',
                'კანგაცლილი პომიდორი' => '0.080',
                'მარილი' => '0.001'
            ],
            'პასტა ქათმის ხორცით და ნაღებით' => [
                'სპაგეტი' => '0.120',
                'ქათმის ფილე' => '0.100',
                'ისპანახი' => '0.200',
                'ნაღები' => '0.050',
                'მარილი' => '0.001',
                'პილპილი' => '0.001',
                'ორეგანო' => '0.001'
            ],
            'ხაჭოს დესერტი ჭიქაში' => [
                'ხაჭო' => '0.500',
                'არაჟანი' => '0.200',
                'ვანილი' => '0.003',
                'შაქარი' => '0.150',
                'პორცია ხაჭოს დესერტი ჭიქაში' => '0.200 ',
                'ორცხობილა' => '0.100',
                'ბანანი' => '0.050',
                'ნუტელა' => '0.050',
                'კაკაო' => '0.001'
            ],
            'არაჟნის კრემი' => [
                'არაჟანი' => '0.800',
                'შაქრის პუდრა' => '0.200',
                'ვანილი' => '0.005',
                'არაჟნის კრემი პორცია' => '0.200',
                'ორცხობილა' => '0.100',
                'კენკრის ჯემი' => '0.080'
            ],
            'იოგურტი' => [
                'არაჟანი' => '0.400',
                'მაწონი' => '0.400',
                'კრემ ყველი' => '200',
                'შაქრის პუდრა' => '0.100',
                'ვანილი' => '0.005',
                'ფორთოხალი' => '0.100'
            ]
        ];

        foreach ($dishes as $key => $values) {
            $dishID = Entity::where('title', $key)
                ->first();

            foreach ($values as $chKey => $chValue) {
                if (in_array($chKey, ['amount','measurment_type','type'])) {
                    continue;
                }
                $ingreduentID = Entity::where('title', $chKey)
                    ->first();

                if (is_int($chValue)) {
                    $measurment = 'unit';
                }else{
                    $measurment = 'kg';
                }

                $amount = floatval($chValue);

                EntityMap::create([
                    'parent_id' => $dishID->id,
                    'child_id' => $ingreduentID->id,
                    'measurement_type' => $measurment,
                    'measurement_amount' => $amount,
                ]);
            }
        }
    }
}
