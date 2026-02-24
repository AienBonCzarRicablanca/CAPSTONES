<?php

namespace Database\Seeders;

use App\Models\ReadingPassage;
use Illuminate\Database\Seeder;

class ReadingPassageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $passages = [
            // English - Beginner
            [
                'title' => 'The Cat and the Hat',
                'content' => 'The cat sat on a mat. The cat had a hat. The hat was red. The cat liked the hat very much.',
                'language' => 'English',
                'difficulty' => 'Beginner',
                'expected_wpm' => 40,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'My Pet Dog',
                'content' => 'I have a pet dog. His name is Max. Max is brown and white. He likes to play with a ball. Max is my best friend.',
                'language' => 'English',
                'difficulty' => 'Beginner',
                'expected_wpm' => 40,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'The Sun and Moon',
                'content' => 'The sun is bright. It shines during the day. The moon is white. It comes out at night. I can see stars at night too.',
                'language' => 'English',
                'difficulty' => 'Beginner',
                'expected_wpm' => 45,
                'is_active' => true,
                'order' => 3,
            ],

            // English - Intermediate
            [
                'title' => 'The Little Bird',
                'content' => 'Once upon a time, there was a little bird who lived in a tall tree. Every morning, the bird would wake up and sing a beautiful song. The other animals in the forest loved to hear the bird sing. One day, the bird decided to fly to a new forest to make new friends.',
                'language' => 'English',
                'difficulty' => 'Intermediate',
                'expected_wpm' => 60,
                'is_active' => true,
                'order' => 4,
            ],
            [
                'title' => 'Going to School',
                'content' => 'Maria wakes up early every morning. She brushes her teeth and eats breakfast with her family. Then she puts on her uniform and walks to school with her friends. At school, Maria learns many things like reading, writing, and math. She enjoys spending time with her classmates during recess.',
                'language' => 'English',
                'difficulty' => 'Intermediate',
                'expected_wpm' => 65,
                'is_active' => true,
                'order' => 5,
            ],

            // English - Advanced
            [
                'title' => 'The Importance of Reading',
                'content' => 'Reading is one of the most important skills that children can develop. Through reading, we can explore new worlds, learn about different cultures, and discover fascinating facts. Reading improves our vocabulary and helps us become better writers. It also stimulates our imagination and creativity. By reading regularly, we can expand our knowledge and understanding of the world around us.',
                'language' => 'English',
                'difficulty' => 'Advanced',
                'expected_wpm' => 80,
                'is_active' => true,
                'order' => 6,
            ],

            // Tagalog - Beginner
            [
                'title' => 'Ang Pusa at ang Daga',
                'content' => 'May isang pusa. May isang daga. Ang pusa ay kumakain ng isda. Ang daga ay kumakain ng keso. Sila ay magkaibigan.',
                'language' => 'Tagalog',
                'difficulty' => 'Beginner',
                'expected_wpm' => 35,
                'is_active' => true,
                'order' => 7,
            ],
            [
                'title' => 'Ang Aking Pamilya',
                'content' => 'Ako ay may pamilya. Mayroon akong tatay at nanay. Mayroon din akong dalawang kapatid. Mahal ko ang aking pamilya. Kami ay masaya.',
                'language' => 'Tagalog',
                'difficulty' => 'Beginner',
                'expected_wpm' => 35,
                'is_active' => true,
                'order' => 8,
            ],
            [
                'title' => 'Ang Magandang Araw',
                'content' => 'Ngayong umaga ay maganda ang panahon. Ang araw ay sumisikat. Ang mga ibon ay umaawit. Masaya ako dahil makakapaglaro ako sa labas.',
                'language' => 'Tagalog',
                'difficulty' => 'Beginner',
                'expected_wpm' => 40,
                'is_active' => true,
                'order' => 9,
            ],

            // Tagalog - Intermediate
            [
                'title' => 'Ang Masipag na Langgam',
                'content' => 'Noong unang panahon, may isang masipag na langgam. Araw-araw, ang langgam ay nagtitipun ng pagkain. Sinabi ng tipaklong, "Bakit ka nagtitipun ng pagkain? Maglaro ka na lang!" Ngunit ang langgam ay patuloy sa paggawa. Nang dumating ang tag-ulan, ang langgam ay may pagkain habang ang tipaklong ay gutom.',
                'language' => 'Tagalog',
                'difficulty' => 'Intermediate',
                'expected_wpm' => 55,
                'is_active' => true,
                'order' => 10,
            ],
            [
                'title' => 'Ang Pagpunta sa Paaralan',
                'content' => 'Si Juan ay gumigising nang maaga tuwing umaga. Siya ay naliligo at kumakain ng almusal. Pagkatapos, sumusuot siya ng uniporme at naglalakad papunta sa paaralan. Sa paaralan, natututo siya ng maraming bagay. Masaya si Juan dahil marami siyang kaibigan sa klase.',
                'language' => 'Tagalog',
                'difficulty' => 'Intermediate',
                'expected_wpm' => 60,
                'is_active' => true,
                'order' => 11,
            ],

            // Tagalog - Advanced
            [
                'title' => 'Ang Kahalagahan ng Edukasyon',
                'content' => 'Ang edukasyon ay susi sa tagumpay. Sa pamamagitan ng pag-aaral, natututo tayo ng mga bagong kaalaman at kasanayan. Ang edukasyon ay nagbubukas ng mga pinto ng oportunidad. Ito ay tumutulong sa atin na maging mas matalinong mamamayan. Kaya naman, mahalaga na magpatuloy tayo sa pag-aaral at pagsisikap upang makamit ang ating mga pangarap.',
                'language' => 'Tagalog',
                'difficulty' => 'Advanced',
                'expected_wpm' => 70,
                'is_active' => true,
                'order' => 12,
            ],
            [
                'title' => 'Ang Pagmamahal sa Kalikasan',
                'content' => 'Ang kalikasan ay nagbibigay sa atin ng maraming biyaya. Ang mga puno ay nagbibigay ng hangin na ating nilalanghap. Ang mga ilog at dagat ay nagbibigay ng tubig at pagkain. Kaya naman, tungkulin nating alagaan ang ating kapaligiran. Magtanim tayo ng mga puno, huwag magtapon ng basura kung saan-saan, at pahalagahan ang mga likas na yaman ng ating bansa.',
                'language' => 'Tagalog',
                'difficulty' => 'Advanced',
                'expected_wpm' => 75,
                'is_active' => true,
                'order' => 13,
            ],
        ];

        foreach ($passages as $passage) {
            // Calculate word count
            $passage['word_count'] = ReadingPassage::calculateWordCount($passage['content']);
            
            ReadingPassage::create($passage);
        }
    }
}
