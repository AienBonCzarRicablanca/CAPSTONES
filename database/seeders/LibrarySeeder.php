<?php

namespace Database\Seeders;

use App\Models\LibraryCategory;
use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        // Get admin user for created_by
        $admin = User::whereHas('role', function($q) {
            $q->where('name', 'ADMIN');
        })->first();

        // Create Categories
        $categories = [
            'Alphabet & Letters' => 'Learning letters and basic sounds',
            'Numbers & Counting' => 'Basic mathematics and counting',
            'Reading & Comprehension' => 'Stories and reading exercises',
            'Grammar & Writing' => 'Language rules and writing skills',
            'Science & Nature' => 'Science topics for kids',
            'Filipino Language' => 'Tagalog language learning',
            'Life Skills' => 'Daily life knowledge',
        ];

        $categoryModels = [];
        foreach ($categories as $name => $description) {
            $categoryModels[$name] = LibraryCategory::firstOrCreate(['name' => $name]);
        }

        // ==================== ENGLISH CONTENT ====================

        // 1. ALPHABET & LETTERS - BEGINNER (English)
        $alphabetContent = [
            [
                'title' => 'Learning the Alphabet: A to Z',
                'content' => "The Alphabet Song\n\nA B C D E F G\nH I J K L M N O P\nQ R S T U V\nW X Y and Z\n\nNow I know my ABCs,\nNext time won't you sing with me!\n\nPractice:\n• Point to each letter as you say it\n• Try writing each letter\n• Can you find these letters around you?"
            ],
            [
                'title' => 'Vowels: The Special Letters',
                'content' => "The 5 Vowels\n\nA - E - I - O - U\n\nVowels are special letters that we use in every word!\n\nExamples:\n• A - Apple, Ant, Alligator\n• E - Elephant, Egg, Elbow\n• I - Ice cream, Igloo, Insect\n• O - Orange, Octopus, Owl\n• U - Umbrella, Under, Up\n\nTry it: Say each vowel out loud!"
            ],
            [
                'title' => 'Consonants: The Other Letters',
                'content' => "Consonants\n\nAll the other letters are consonants:\nB C D F G H J K L M N P Q R S T V W X Y Z\n\nExamples:\n• B - Ball, Bat, Book\n• C - Cat, Cup, Car\n• D - Dog, Duck, Door\n• F - Fish, Frog, Fan\n• G - Goat, Girl, Gate\n\nPractice saying the sounds!"
            ],
        ];

        foreach ($alphabetContent as $item) {
            LibraryItem::create([
                'library_category_id' => $categoryModels['Alphabet & Letters']->id,
                'created_by' => $admin?->id,
                'title' => $item['title'],
                'language' => 'English',
                'difficulty' => 'Beginner',
                'text_content' => $item['content'],
            ]);
        }

        // ALPHABET & LETTERS - INTERMEDIATE (English)
        $alphabetIntermediate = [
            [
                'title' => 'Letter Sounds and Phonics',
                'content' => "Learning Letter Sounds\n\nEach letter makes a sound!\n\nA says /a/ as in Apple\nB says /b/ as in Ball\nC says /k/ as in Cat\nD says /d/ as in Dog\n\nBlending Sounds:\nC + A + T = CAT\nD + O + G = DOG\nB + A + T = BAT\n\nTry blending these sounds to make words!"
            ],
            [
                'title' => 'Capital and Lowercase Letters',
                'content' => "Big and Small Letters\n\nCapital (Big): A B C D E\nLowercase (Small): a b c d e\n\nCapital (Big): F G H I J\nLowercase (Small): f g h i j\n\nWhen do we use capital letters?\n• At the start of a sentence\n• For names (John, Mary, Manila)\n• For the word 'I'\n\nPractice writing both big and small letters!"
            ],
        ];

        foreach ($alphabetIntermediate as $item) {
            LibraryItem::create([
                'library_category_id' => $categoryModels['Alphabet & Letters']->id,
                'created_by' => $admin?->id,
                'title' => $item['title'],
                'language' => 'English',
                'difficulty' => 'Intermediate',
                'text_content' => $item['content'],
            ]);
        }

        // 2. NUMBERS & COUNTING - BEGINNER (English)
        $numbersContent = [
            [
                'title' => 'Counting 1 to 10',
                'content' => "Let's Count!\n\n1 - One\n2 - Two\n3 - Three\n4 - Four\n5 - Five\n6 - Six\n7 - Seven\n8 - Eight\n9 - Nine\n10 - Ten\n\nCount with your fingers!\nCan you count to 10?"
            ],
            [
                'title' => 'Numbers 11 to 20',
                'content' => "Counting Higher!\n\n11 - Eleven\n12 - Twelve\n13 - Thirteen\n14 - Fourteen\n15 - Fifteen\n16 - Sixteen\n17 - Seventeen\n18 - Eighteen\n19 - Nineteen\n20 - Twenty\n\nPractice counting from 1 to 20!"
            ],
            [
                'title' => 'Simple Addition',
                'content' => "Adding Numbers\n\n1 + 1 = 2 (One plus one equals two)\n2 + 1 = 3 (Two plus one equals three)\n2 + 2 = 4 (Two plus two equals four)\n3 + 2 = 5 (Three plus two equals five)\n\nUse your fingers to add!\n\nExample:\nIf you have 2 apples and get 3 more,\nyou have 2 + 3 = 5 apples!"
            ],
        ];

        foreach ($numbersContent as $item) {
            LibraryItem::create([
                'library_category_id' => $categoryModels['Numbers & Counting']->id,
                'created_by' => $admin?->id,
                'title' => $item['title'],
                'language' => 'English',
                'difficulty' => 'Beginner',
                'text_content' => $item['content'],
            ]);
        }

        // NUMBERS & COUNTING - INTERMEDIATE (English)
        LibraryItem::create([
            'library_category_id' => $categoryModels['Numbers & Counting']->id,
            'created_by' => $admin?->id,
            'title' => 'Subtraction Basics',
            'language' => 'English',
            'difficulty' => 'Intermediate',
            'text_content' => "Taking Away (Subtraction)\n\n5 - 1 = 4 (Five minus one equals four)\n5 - 2 = 3 (Five minus two equals three)\n10 - 5 = 5 (Ten minus five equals five)\n\nExample:\nIf you have 5 candies and eat 2,\nyou have 5 - 2 = 3 candies left!\n\nPractice:\n• 4 - 1 = ?\n• 6 - 3 = ?\n• 10 - 4 = ?",
        ]);

        // 3. READING & COMPREHENSION - BEGINNER (English)
        $readingContent = [
            [
                'title' => 'The Three Little Pigs',
                'content' => "The Three Little Pigs\n\nOnce upon a time, there were three little pigs.\n\nThe first pig built a house of straw.\nThe second pig built a house of sticks.\nThe third pig built a house of bricks.\n\nA big bad wolf came and said:\n\"Little pig, little pig, let me in!\"\n\nThe wolf blew down the straw house.\nThe wolf blew down the stick house.\nBut he could not blow down the brick house!\n\nThe three pigs were safe in the strong brick house.\n\nThe End.\n\nWhat did we learn?\n• Working hard pays off\n• Strong things last longer",
            ],
            [
                'title' => 'The Ant and the Grasshopper',
                'content' => "The Ant and the Grasshopper\n\nIt was summer. The ant worked hard. He collected food for winter.\n\nThe grasshopper played all day. He sang and danced.\n\n\"Why do you work so hard?\" asked the grasshopper.\n\"I am saving food for winter,\" said the ant.\n\nWinter came. It was very cold.\n\nThe ant had lots of food.\nThe grasshopper had no food. He was hungry.\n\nThe kind ant shared his food with the grasshopper.\n\nThe grasshopper learned his lesson.\n\nMoral: Plan for the future and work hard.",
            ],
        ];

        foreach ($readingContent as $item) {
            LibraryItem::create([
                'library_category_id' => $categoryModels['Reading & Comprehension']->id,
                'created_by' => $admin?->id,
                'title' => $item['title'],
                'language' => 'English',
                'difficulty' => 'Beginner',
                'text_content' => $item['content'],
            ]);
        }

        // 4. GRAMMAR & WRITING - BEGINNER (English)
        LibraryItem::create([
            'library_category_id' => $categoryModels['Grammar & Writing']->id,
            'created_by' => $admin?->id,
            'title' => 'Simple Sentences',
            'language' => 'English',
            'difficulty' => 'Beginner',
            'text_content' => "Making Simple Sentences\n\nA sentence tells a complete thought.\nIt starts with a capital letter.\nIt ends with a period (.)\n\nExamples:\n• The cat is black.\n• I like apples.\n• We go to school.\n• She has a ball.\n• The dog runs fast.\n\nParts of a sentence:\nWHO + DOES WHAT\n• The boy (who) plays (does what).\n• The bird (who) flies (does what).\n\nTry making your own sentences!",
        ]);

        // ==================== TAGALOG CONTENT ====================

        // 1. FILIPINO LANGUAGE - BEGINNER (Tagalog)
        $filipinoContent = [
            [
                'title' => 'Ang Alpabetong Filipino',
                'content' => "Ang Alpabetong Filipino\n\nA B C D E F G\nH I J K L M N\nÑ O P Q R S T\nU V W X Y Z\n\nMay 28 na letra ang alpabetong Filipino!\n\nMga Halimbawa:\n• A - Aso\n• B - Bata\n• K - Kamatis\n• P - Pusa\n• S - Saging\n\nSubukang sabihin ang bawat letra!",
            ],
            [
                'title' => 'Mga Patinig (Vowels)',
                'content' => "Ang 5 Patinig\n\nA - E - I - O - U\n\nAng mga patinig ay nasa bawat salita!\n\nMga Halimbawa:\n• A - Anak, Araw, Ama\n• E - Eroplano, Estudyante\n• I - Ibon, Ina, Itlog\n• O - Oso, Oras, Okra\n• U - Ulan, Ube, Upo\n\nPagsasanay: Sabihin ang bawat patinig!",
            ],
        ];

        foreach ($filipinoContent as $item) {
            LibraryItem::create([
                'library_category_id' => $categoryModels['Filipino Language']->id,
                'created_by' => $admin?->id,
                'title' => $item['title'],
                'language' => 'Tagalog',
                'difficulty' => 'Beginner',
                'text_content' => $item['content'],
            ]);
        }

        // FILIPINO LANGUAGE - INTERMEDIATE (Tagalog)
        $filipinoIntermediate = [
            [
                'title' => 'Mga Pangngalan (Nouns)',
                'content' => "Mga Pangngalan\n\nAng pangngalan ay ngalan ng tao, hayop, bagay, o lugar.\n\nMga Tao:\n• bata, guro, nanay, tatay\n\nMga Hayop:\n• aso, pusa, ibon, isda\n\nMga Bagay:\n• libro, lapis, mesa, upuan\n\nMga Lugar:\n• bahay, paaralan, parke, palengke\n\nSubukang maghanap ng mas maraming pangngalan sa paligid mo!",
            ],
            [
                'title' => 'Mga Pang-uri (Adjectives)',
                'content' => "Mga Pang-uri\n\nAng pang-uri ay naglalarawan ng pangngalan.\n\nLaki:\n• malaki, maliit\n\nKulay:\n• pula, asul, dilaw, luntian\n\nLasa:\n• matamis, maalat, maasim\n\nDamdam:\n• masaya, malungkot, galit\n\nMga Halimbawa:\n• Malaking bahay\n• Pulang bulaklak\n• Matamis na kendi\n• Masayang bata",
            ],
        ];

        foreach ($filipinoIntermediate as $item) {
            LibraryItem::create([
                'library_category_id' => $categoryModels['Filipino Language']->id,
                'created_by' => $admin?->id,
                'title' => $item['title'],
                'language' => 'Tagalog',
                'difficulty' => 'Intermediate',
                'text_content' => $item['content'],
            ]);
        }

        // 2. NUMBERS IN TAGALOG - BEGINNER
        LibraryItem::create([
            'library_category_id' => $categoryModels['Numbers & Counting']->id,
            'created_by' => $admin?->id,
            'title' => 'Pagbibilang 1 hanggang 10',
            'language' => 'Tagalog',
            'difficulty' => 'Beginner',
            'text_content' => "Magturo ng Bilang!\n\n1 - Isa\n2 - Dalawa\n3 - Tatlo\n4 - Apat\n5 - Lima\n6 - Anim\n7 - Pito\n8 - Walo\n9 - Siyam\n10 - Sampu\n\nBilang gamit ang iyong mga daliri!\nKaya mo bang magbilang hanggang sampu?",
        ]);

        // 3. READING IN TAGALOG - BEGINNER
        LibraryItem::create([
            'library_category_id' => $categoryModels['Reading & Comprehension']->id,
            'created_by' => $admin?->id,
            'title' => 'Ang Tamad na Matsing',
            'language' => 'Tagalog',
            'difficulty' => 'Beginner',
            'text_content' => "Ang Tamad na Matsing\n\nNoong unang panahon, may isang matsingi ng tamad.\n\nAraw-araw, humihiga lang siya sa puno ng saging.\n\"Gutom na ako!\" sabi niya.\nNgunit ayaw niyang kumuha ng saging.\n\nIsang araw, puno na ng hinog na saging ang puno.\nNagbigay ng maraming saging ang puno.\n\nNgunit bumagsak ang lahat ng saging!\nWala nang natira para sa tamad na matsing.\n\nNailalaman:\nAng tamad ay nagugutom.\nKailangan tayong magsikap!\n\nWakas",
        ]);

        // 4. SCIENCE & NATURE - BEGINNER (English)
        $scienceContent = [
            [
                'title' => 'The Five Senses',
                'content' => "Our Five Senses\n\n1. SIGHT (Eyes)\nWe see with our eyes.\n• colors, shapes, people\n\n2. HEARING (Ears)\nWe hear with our ears.\n• music, voices, sounds\n\n3. SMELL (Nose)\nWe smell with our nose.\n• flowers, food, perfume\n\n4. TASTE (Tongue)\nWe taste with our tongue.\n• sweet, sour, salty, bitter\n\n5. TOUCH (Skin)\nWe feel with our skin.\n• soft, hard, hot, cold\n\nOur senses help us learn about the world!",
            ],
            [
                'title' => 'Plants and How They Grow',
                'content' => "How Plants Grow\n\nWhat do plants need?\n\n1. SUNLIGHT ☀️\nPlants need sun to make food.\n\n2. WATER 💧\nPlants drink water through their roots.\n\n3. AIR 🌬️\nPlants breathe air through their leaves.\n\n4. SOIL 🌱\nPlants get food from the soil.\n\nParts of a Plant:\n• Roots - hold the plant in the ground\n• Stem - carries water up\n• Leaves - make food from sunlight\n• Flowers - make seeds\n\nTry growing your own plant!",
            ],
        ];

        foreach ($scienceContent as $item) {
            LibraryItem::create([
                'library_category_id' => $categoryModels['Science & Nature']->id,
                'created_by' => $admin?->id,
                'title' => $item['title'],
                'language' => 'English',
                'difficulty' => 'Beginner',
                'text_content' => $item['content'],
            ]);
        }

        // 5. LIFE SKILLS - BEGINNER (English)
        LibraryItem::create([
            'library_category_id' => $categoryModels['Life Skills']->id,
            'created_by' => $admin?->id,
            'title' => 'Good Manners and Right Conduct',
            'language' => 'English',
            'difficulty' => 'Beginner',
            'text_content' => "Good Manners for Kids\n\nMagic Words:\n• Please - when asking for something\n• Thank you - when receiving something\n• Sorry - when you make a mistake\n• Excuse me - when you need to pass\n\nGood Habits:\n✓ Say \"Good morning\" to your teacher\n✓ Listen when others are talking\n✓ Share with your friends\n✓ Clean up after yourself\n✓ Help others when they need it\n\nAt Home:\n✓ Respect your parents\n✓ Do your chores\n✓ Be kind to your siblings\n✓ Say \"I love you\"\n\nRemember: Good manners make everyone happy!",
        ]);

        // LIFE SKILLS IN TAGALOG - BEGINNER
        LibraryItem::create([
            'library_category_id' => $categoryModels['Life Skills']->id,
            'created_by' => $admin?->id,
            'title' => 'Magandang Asal at Wastong Pag-uugali',
            'language' => 'Tagalog',
            'difficulty' => 'Beginner',
            'text_content' => "Magandang Asal para sa mga Bata\n\nMga Mahiwagang Salita:\n• Paki - kapag humihingi\n• Salamat - kapag tumatanggap\n• Pasensya na/Sorry - kapag nagkamali\n• Excuse me/Makikiraan - kapag daraan\n\nMabubuting Gawi:\n✓ Pagmamano sa matatanda\n✓ Makinig kapag may nagsasalita\n✓ Magbahagi sa kaibigan\n✓ Maglinis pagkatapos\n✓ Tumulong sa iba\n\nSa Bahay:\n✓ Igalang ang magulang\n✓ Gawin ang iyong gawain\n✓ Maging mabait sa kapatid\n✓ Sabihing \"Mahal kita\"\n\nTandaan: Ang magandang asal ay nagpapasaya sa lahat!",
        ]);

        // ADVANCED CONTENT
        
        // GRAMMAR - ADVANCED (English)
        LibraryItem::create([
            'library_category_id' => $categoryModels['Grammar & Writing']->id,
            'created_by' => $admin?->id,
            'title' => 'Parts of Speech: Nouns, Verbs, and Adjectives',
            'language' => 'English',
            'difficulty' => 'Advanced',
            'text_content' => "Understanding Parts of Speech\n\n1. NOUNS (Naming Words)\nNouns name people, places, things, or ideas.\n• Person: teacher, doctor, friend\n• Place: school, park, home\n• Thing: book, table, car\n• Idea: love, happiness, freedom\n\n2. VERBS (Action Words)\nVerbs show action or being.\n• Action: run, jump, eat, read\n• Being: is, am, are, was, were\n\nExamples:\n• The dog runs fast. (runs = verb)\n• She is happy. (is = verb)\n\n3. ADJECTIVES (Describing Words)\nAdjectives describe nouns.\n• Size: big, small, tall, short\n• Color: red, blue, green\n• Quality: beautiful, smart, kind\n\nExample: The big, red balloon floats high.\n(big = adjective, red = adjective)\n\nPractice: Can you identify the parts of speech in this sentence?\n\"The happy child reads an interesting book.\"",
        ]);

        // SCIENCE - ADVANCED (English)
        LibraryItem::create([
            'library_category_id' => $categoryModels['Science & Nature']->id,
            'created_by' => $admin?->id,
            'title' => 'The Water Cycle',
            'language' => 'English',
            'difficulty' => 'Advanced',
            'text_content' => "The Water Cycle\n\nWater moves around Earth in a cycle!\n\n1. EVAPORATION\nThe sun heats water in oceans, rivers, and lakes.\nWater turns into water vapor (gas) and rises into the sky.\n\n2. CONDENSATION\nThe water vapor cools down high in the sky.\nIt forms tiny water droplets.\nThese droplets make clouds.\n\n3. PRECIPITATION\nWhen clouds get heavy with water, it falls back to Earth.\nThis can be:\n• Rain 🌧️\n• Snow ❄️\n• Hail 🧊\n• Sleet\n\n4. COLLECTION\nWater collects in oceans, rivers, and lakes.\nSome water soaks into the ground.\nThen the cycle starts again!\n\nWhy is the water cycle important?\n• Provides fresh water for plants and animals\n• Helps plants grow\n• Keeps Earth's temperature balanced\n\nFun Fact: The water you drink today might have been drunk by a dinosaur millions of years ago!",
        ]);

        // FILIPINO - ADVANCED (Tagalog)
        LibraryItem::create([
            'library_category_id' => $categoryModels['Filipino Language']->id,
            'created_by' => $admin?->id,
            'title' => 'Mga Uri ng Pangungusap',
            'language' => 'Tagalog',
            'difficulty' => 'Advanced',
            'text_content' => "Mga Uri ng Pangungusap\n\n1. PASALAYSAY (Declarative)\nNagpapahayag ng kaisipan o ideya.\nNagtatapos sa tuldok (.).\n\nHalimbawa:\n• Maganda ang panahon ngayon.\n• Pumunta kami sa palengke kahapon.\n\n2. PATANONG (Interrogative)\nNagtatanong ng katanungan.\nNagtatapos sa tandang pananong (?).\n\nHalimbawa:\n• Kumusta ka na?\n• Nasaan ang aking libro?\n\n3. PAKIUSAP (Imperative)\nNag-uutos o nakikiusap.\nNagtatapos sa tuldok (.) o padamdam (!).\n\nHalimbawa:\n• Pakisara ang pinto.\n• Makinig kayo!\n\n4. PADAMDAM (Exclamatory)\nNagpapahayag ng matinding damdamin.\nNagtatapos sa tandang padamdam (!).\n\nHalimbawa:\n• Ang ganda ng bulaklak!\n• Ang sakit!\n\nPagsasanay:\nAnong uri ng pangungusap?\n1. Kumain ka na.\n2. Wow, napakagaling mo!\n3. Saan ka pupunta?\n4. Masipag ang batang iyon.",
        ]);

        echo "✓ Library seeded successfully with " . LibraryItem::count() . " items!\n";
        echo "✓ Categories: " . LibraryCategory::count() . "\n";
        echo "✓ English content: " . LibraryItem::where('language', 'English')->count() . " items\n";
        echo "✓ Tagalog content: " . LibraryItem::where('language', 'Tagalog')->count() . " items\n";
        echo "✓ Beginner: " . LibraryItem::where('difficulty', 'Beginner')->count() . " items\n";
        echo "✓ Intermediate: " . LibraryItem::where('difficulty', 'Intermediate')->count() . " items\n";
        echo "✓ Advanced: " . LibraryItem::where('difficulty', 'Advanced')->count() . " items\n";
    }
}
