<?php

namespace Database\Seeders;

use App\Models\Ielts\IeltsPassage;
use Illuminate\Database\Seeder;

/**
 * Ещё восемь записей для IELTS Listening — по две на каждый уровень от A2 до C1.
 *
 * Отдельный сидер, а не дополнение IeltsPassageSeeder: тот перезаписывает
 * вопросы существующих записей (questions()->delete()), и повторный прогон
 * обнулял бы попытки-ориентиры учеников по старым записям.
 *
 * Тексты повторяют четыре части экзамена: бытовой диалог, монолог на бытовую
 * тему, учебный разговор нескольких человек и академическая лекция. Озвучивает
 * их speechSynthesis прямо в браузере, поэтому реплики подписаны именами —
 * так ученик слышит, где меняется говорящий.
 */
class IeltsListeningBatch2Seeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->passages() as $data) {
            $questions = $data['questions'];
            unset($data['questions']);

            // Идемпотентность по названию: повторный прогон не плодит дубли,
            // но и не трогает вопросы уже существующей записи.
            $passage = IeltsPassage::firstOrCreate(
                ['skill' => 'listening', 'title' => $data['title']],
                $data
            );

            if ($passage->questions()->exists()) {
                continue;
            }

            foreach ($questions as $order => $q) {
                $passage->questions()->create([
                    'question' => $q['question'],
                    'options' => $q['options'],
                    'correct_index' => $q['correct'],
                    'order_number' => $order,
                ]);
            }
        }
    }

    private function passages(): array
    {
        return [
            // ===== A2: бытовые диалоги (Section 1) =====
            [
                'skill' => 'listening',
                'title' => 'Joining the City Library',
                'level' => 'A2',
                'passage_text' => "Librarian: Good morning! How can I help you?\n"
                    ."Visitor: Hello. I'd like to join the library, please.\n"
                    ."Librarian: Of course. Have you lived in the city for more than three months?\n"
                    ."Visitor: Yes, I moved here in January, so about eight months now.\n"
                    ."Librarian: Perfect. Membership is free for residents. I just need to see a document with your address on it — a bank letter or an electricity bill works well.\n"
                    ."Visitor: I have a bank letter here.\n"
                    ."Librarian: That's fine. Now, can I take your full name?\n"
                    ."Visitor: It's Daniel Ward. That's W-A-R-D.\n"
                    ."Librarian: Thank you. And a phone number?\n"
                    ."Visitor: 0161 496 7720.\n"
                    ."Librarian: Great. Your card will be ready in about five minutes. You can borrow up to eight books at a time, for three weeks each.\n"
                    ."Visitor: Can I renew them if I need more time?\n"
                    ."Librarian: Yes, twice online, unless somebody else has reserved the book. After that you have to bring it back.\n"
                    ."Visitor: And what happens if I return something late?\n"
                    ."Librarian: There's a small charge of twenty pence per day, but we send a reminder email three days before the due date.",
                'questions' => [
                    ['question' => 'How long has the visitor lived in the city?', 'options' => ['Three weeks', 'About eight months', 'Two years', 'Since last week'], 'correct' => 1],
                    ['question' => 'What does he show to prove his address?', 'options' => ['A passport', 'A driving licence', 'A bank letter', 'A student card'], 'correct' => 2],
                    ['question' => 'How many books can he borrow at one time?', 'options' => ['Three', 'Five', 'Eight', 'Twelve'], 'correct' => 2],
                    ['question' => 'How many times can he renew a book online?', 'options' => ['Once', 'Twice', 'Three times', 'As often as he likes'], 'correct' => 1],
                    ['question' => 'What is the charge for returning a book late?', 'options' => ['Ten pence a day', 'Twenty pence a day', 'One pound a day', 'There is no charge'], 'correct' => 1],
                ],
            ],
            [
                'skill' => 'listening',
                'title' => 'Reporting a Lost Bag',
                'level' => 'A2',
                'passage_text' => "Staff: Lost property office, good afternoon.\n"
                    ."Passenger: Hi. I think I left my bag on a train this morning.\n"
                    ."Staff: I'm sorry to hear that. Which train were you on?\n"
                    ."Passenger: The 8:15 from Oxford to London. I got off at platform four.\n"
                    ."Staff: All right. Can you describe the bag for me?\n"
                    ."Passenger: It's a dark green rucksack, quite small, with a broken zip on the front pocket.\n"
                    ."Staff: And what was inside?\n"
                    ."Passenger: A grey laptop, a blue notebook, and my umbrella. Oh, and a set of keys.\n"
                    ."Staff: Was there anything with your name on it?\n"
                    ."Passenger: Yes, there's a luggage label with my name and mobile number.\n"
                    ."Staff: That helps a lot. Let me take your details. Your name?\n"
                    ."Passenger: Elena Petrova. My number is 07700 900318.\n"
                    ."Staff: Thank you. Items from that line usually arrive here the next working day. If we find your bag, we'll call you before five o'clock.\n"
                    ."Passenger: Do I have to pay anything to collect it?\n"
                    ."Staff: There's a three-pound handling fee, and you'll need some photo identification when you come in.",
                'questions' => [
                    ['question' => 'Which train was the passenger on?', 'options' => ['The 8:15 from Oxford', 'The 9:15 from London', 'The 8:50 from Reading', 'The last train home'], 'correct' => 0],
                    ['question' => 'What colour is the rucksack?', 'options' => ['Black', 'Dark green', 'Blue', 'Grey'], 'correct' => 1],
                    ['question' => 'Which of these was NOT in the bag?', 'options' => ['A laptop', 'An umbrella', 'A camera', 'A set of keys'], 'correct' => 2],
                    ['question' => 'When do items from that line usually arrive at the office?', 'options' => ['Within an hour', 'The next working day', 'After one week', 'They never arrive'], 'correct' => 1],
                    ['question' => 'What must she bring to collect the bag?', 'options' => ['Her train ticket', 'Photo identification', 'A letter from the police', 'Nothing at all'], 'correct' => 1],
                ],
            ],

            // ===== B1: монологи на бытовую тему (Section 2) =====
            [
                'skill' => 'listening',
                'title' => 'Museum Visitor Information',
                'level' => 'B1',
                'passage_text' => "Welcome to the Northgate Museum. Before you begin your visit, here is some information that should make your day easier.\n\n"
                    ."The museum has three floors. The ground floor holds our permanent collection of local history, including the Roman coins found during the building of the railway station in 1897. The first floor is dedicated to natural history, and the second floor hosts temporary exhibitions, which change roughly every ten weeks.\n\n"
                    ."Photography is welcome throughout the building, but please switch off your flash — strong light gradually damages the older textiles and paintings. We also ask you not to bring food or drink beyond the entrance hall.\n\n"
                    ."Free guided tours leave from this desk at eleven o'clock and again at two o'clock, and they last about an hour. If you prefer to explore alone, audio guides are available in six languages for four pounds, and they can be returned at any of the exits.\n\n"
                    ."Our café is on the ground floor, behind the gift shop, and serves hot food until half past three. Finally, please note that the natural history floor closes half an hour before the rest of the museum, at half past four, so visit it early if it interests you.",
                'questions' => [
                    ['question' => 'What can visitors see on the ground floor?', 'options' => ['Natural history', 'Temporary exhibitions', 'Local history and Roman coins', 'The art gallery'], 'correct' => 2],
                    ['question' => 'How often do the temporary exhibitions change?', 'options' => ['Every week', 'About every ten weeks', 'Twice a year', 'Every three years'], 'correct' => 1],
                    ['question' => 'Why must visitors switch off the flash?', 'options' => ['It disturbs other visitors', 'It damages old textiles and paintings', 'It is against the law', 'It drains the camera battery'], 'correct' => 1],
                    ['question' => 'How much does an audio guide cost?', 'options' => ['It is free', 'Two pounds', 'Four pounds', 'Six pounds'], 'correct' => 2],
                    ['question' => 'When does the natural history floor close?', 'options' => ['At half past three', 'At four o\'clock', 'At half past four', 'At five o\'clock'], 'correct' => 2],
                ],
            ],
            [
                'skill' => 'listening',
                'title' => 'Volunteering at the River Clean-Up',
                'level' => 'B1',
                'passage_text' => "Thanks for coming to this briefing about Saturday's river clean-up. My name is Priya, and I coordinate volunteers for the Greenway Trust.\n\n"
                    ."We'll meet at nine o'clock at the footbridge near the old mill, not at the car park as we did last year — the car park is being resurfaced. There's a bus that stops two minutes away, and it runs every twenty minutes on Saturday mornings.\n\n"
                    ."Please wear boots or old trainers you don't mind ruining, and bring a waterproof jacket, because the path gets very muddy even when the weather is dry. We supply gloves, litter pickers and bags, so there's no need to buy anything.\n\n"
                    ."We'll work in teams of four. Two teams will cover the bank between the bridge and the weir, and the others will work upstream towards the nature reserve. Please don't go into the water itself: the current is stronger than it looks, and everything we need to collect is on the bank.\n\n"
                    ."We stop for lunch at half past twelve. Sandwiches and hot drinks are provided free, but if you have any dietary requirements, email me by Thursday evening so we can order accordingly. We usually finish around three, and last year's group collected over two hundred kilograms of rubbish in a single morning.",
                'questions' => [
                    ['question' => 'Why has the meeting point changed this year?', 'options' => ['The bridge is closed', 'The car park is being resurfaced', 'The bus route has changed', 'More volunteers are expected'], 'correct' => 1],
                    ['question' => 'What should volunteers bring themselves?', 'options' => ['Gloves and litter pickers', 'Bags for rubbish', 'Boots and a waterproof jacket', 'Their own lunch'], 'correct' => 2],
                    ['question' => 'How large is each team?', 'options' => ['Two people', 'Four people', 'Six people', 'Ten people'], 'correct' => 1],
                    ['question' => 'Why must volunteers stay out of the water?', 'options' => ['The water is polluted', 'The current is stronger than it looks', 'It is private property', 'They have no insurance'], 'correct' => 1],
                    ['question' => 'What should volunteers do by Thursday evening?', 'options' => ['Confirm they are coming', 'Collect their equipment', 'Email any dietary requirements', 'Pay a small fee'], 'correct' => 2],
                ],
            ],

            // ===== B2: учебные разговоры (Section 3) =====
            [
                'skill' => 'listening',
                'title' => 'Planning a Group Presentation',
                'level' => 'B2',
                'passage_text' => "Tutor: So, you three are presenting on urban green space next Thursday. How far have you got?\n"
                    ."Marcus: We've collected the data, but we're arguing about the structure, to be honest.\n"
                    ."Tutor: Tell me about the disagreement.\n"
                    ."Leyla: I think we should open with the health evidence — the studies linking park access to lower stress levels. It's the strongest material we have.\n"
                    ."Marcus: And I'd rather start with the economics, because that's what actually persuades city councils. Health benefits sound nice, but budgets decide policy.\n"
                    ."Tutor: Both are defensible. What does your third member think?\n"
                    ."Sofia: I'd keep the health evidence first, but only briefly — say three minutes — and then use the economic argument as the main section. That way the human case frames the numbers rather than competing with them.\n"
                    ."Tutor: That's a sensible compromise. What worries me more is your source base. How many of your studies are from the last decade?\n"
                    ."Leyla: About half. The rest are from the nineteen-nineties.\n"
                    ."Tutor: Then replace the oldest ones. Urban populations have changed enormously since then, and an examiner will notice immediately. Aim for nothing older than 2015 unless the study is genuinely foundational.\n"
                    ."Marcus: That's going to cost us a few days.\n"
                    ."Tutor: Better than losing marks on methodology. And keep to twenty minutes — last term a group ran to thirty-five and lost a full grade for it.",
                'questions' => [
                    ['question' => 'What is the main disagreement between Marcus and Leyla?', 'options' => ['Who will speak first', 'Which argument should open the presentation', 'How long the talk should be', 'Whether to use slides'], 'correct' => 1],
                    ['question' => 'What compromise does Sofia suggest?', 'options' => ['Dropping the health evidence entirely', 'A brief health opening, then economics as the main section', 'Giving each argument ten minutes', 'Letting the tutor decide'], 'correct' => 1],
                    ['question' => 'What concerns the tutor most?', 'options' => ['The structure of the talk', 'The age of the sources', 'The size of the group', 'The choice of topic'], 'correct' => 1],
                    ['question' => 'What does the tutor say about study dates?', 'options' => ['Anything from the 1990s is acceptable', 'Nothing older than 2015 unless it is foundational', 'Only this year\'s research counts', 'Dates do not matter'], 'correct' => 1],
                    ['question' => 'What happened to a group last term?', 'options' => ['They failed the module', 'They lost a grade for running to thirty-five minutes', 'They were asked to present again', 'They won a prize'], 'correct' => 1],
                ],
            ],
            [
                'skill' => 'listening',
                'title' => 'Choosing a Dissertation Topic',
                'level' => 'B2',
                'passage_text' => "Supervisor: Come in, Hannah. You wanted to talk about narrowing your dissertation topic.\n"
                    ."Hannah: Yes. At the moment I've written down 'social media and teenage sleep', but I know that's far too broad.\n"
                    ."Supervisor: It is. What specifically interests you within it?\n"
                    ."Hannah: I keep coming back to the timing question — whether it matters when in the evening young people use their phones, not just how long.\n"
                    ."Supervisor: Now that's a workable question. It's specific, it's measurable, and surprisingly little has been published on it. How would you gather the data?\n"
                    ."Hannah: I was thinking of a survey. Maybe four hundred students.\n"
                    ."Supervisor: A survey of that size gives you self-reported bedtimes, which are notoriously unreliable — people underestimate screen time by about an hour on average. Have you considered a smaller sample with actual device logs?\n"
                    ."Hannah: I hadn't. Wouldn't sixty or seventy participants be too few?\n"
                    ."Supervisor: For a master's dissertation, sixty participants with reliable data beats four hundred with unreliable data every time. Examiners care about the quality of your method far more than the size of your sample.\n"
                    ."Hannah: That makes sense. What about ethics approval?\n"
                    ."Supervisor: Device logs count as sensitive personal data, so the committee will want a detailed consent process. Start that paperwork now — it takes six weeks in term time, and students who leave it until March always regret it.",
                'questions' => [
                    ['question' => 'What is wrong with Hannah\'s original topic?', 'options' => ['It has been studied too often', 'It is far too broad', 'It is not relevant to her course', 'It requires expensive equipment'], 'correct' => 1],
                    ['question' => 'What specific question does she want to investigate?', 'options' => ['How long teenagers use phones', 'Whether the timing of evening phone use matters', 'Which apps teenagers prefer', 'How parents control screen time'], 'correct' => 1],
                    ['question' => 'Why does the supervisor doubt the survey approach?', 'options' => ['Surveys are too expensive', 'Self-reported bedtimes are unreliable', 'Four hundred students is too few', 'Surveys take too long to write'], 'correct' => 1],
                    ['question' => 'What does the supervisor say examiners value most?', 'options' => ['The size of the sample', 'The quality of the method', 'The number of sources', 'The length of the dissertation'], 'correct' => 1],
                    ['question' => 'How long does ethics approval take in term time?', 'options' => ['One week', 'Three weeks', 'Six weeks', 'Six months'], 'correct' => 2],
                ],
            ],

            // ===== C1: академические лекции (Section 4) =====
            [
                'skill' => 'listening',
                'title' => 'Lecture: How Cities Cool Themselves',
                'level' => 'C1',
                'passage_text' => "Today I want to examine why cities are consistently warmer than the countryside around them, and what can actually be done about it.\n\n"
                    ."The phenomenon is known as the urban heat island effect. On a still summer night, the centre of a large city can be as much as seven degrees Celsius warmer than farmland twenty kilometres away. Three mechanisms explain most of this difference. First, dark surfaces such as asphalt absorb far more solar radiation than vegetation does. Second, the vertical geometry of streets traps heat that would otherwise radiate upwards — a narrow street between tall buildings behaves rather like a canyon. Third, cities generate their own heat, from vehicles, from industry, and, ironically, from air conditioning units pumping warmth out of buildings and into the street.\n\n"
                    ."Now, the responses. The most widely discussed is the so-called cool roof: painting roof surfaces white or coating them with reflective material. Measurements in Los Angeles suggest a well-executed cool roof can lower the temperature of the roof surface itself by some thirty degrees. However — and this is the point students often miss — the effect on street-level temperature, where people actually walk, is far more modest, typically under one degree.\n\n"
                    ."Vegetation performs differently. Trees cool not only by shading but through transpiration: water evaporating from leaves carries heat away. A mature street tree can have a cooling effect comparable to several air conditioning units running continuously. The difficulty is time. A cool roof works from the day it is painted; a tree planted this year delivers meaningful shade in perhaps fifteen years, and only if it survives, which in harsh urban soils is far from guaranteed.\n\n"
                    ."This is why the current consensus favours combining approaches rather than choosing between them, and why the most effective interventions are those planned decades before the heatwave arrives.",
                'questions' => [
                    ['question' => 'How much warmer can a city centre be on a still summer night?', 'options' => ['About two degrees', 'About four degrees', 'As much as seven degrees', 'Around fifteen degrees'], 'correct' => 2],
                    ['question' => 'Why does the lecturer compare a narrow street to a canyon?', 'options' => ['Because it is dark', 'Because its geometry traps heat that would radiate upwards', 'Because it channels wind', 'Because it floods easily'], 'correct' => 1],
                    ['question' => 'What does the lecturer call ironic?', 'options' => ['That trees need water', 'That air conditioning pumps heat into the street', 'That white paint is cheap', 'That cities were built on farmland'], 'correct' => 1],
                    ['question' => 'What point about cool roofs do students often miss?', 'options' => ['They are expensive to install', 'They only work in Los Angeles', 'Their effect at street level is under one degree', 'They must be repainted every year'], 'correct' => 2],
                    ['question' => 'Besides shade, how do trees cool a street?', 'options' => ['By blocking wind', 'By absorbing carbon dioxide', 'Through transpiration from their leaves', 'By reflecting light upwards'], 'correct' => 2],
                    ['question' => 'What is the main disadvantage of planting trees?', 'options' => ['They are more expensive than cool roofs', 'They take about fifteen years to give meaningful shade', 'They raise humidity to dangerous levels', 'They do not work at night'], 'correct' => 1],
                ],
            ],
            [
                'skill' => 'listening',
                'title' => 'Lecture: The Economics of Food Waste',
                'level' => 'C1',
                'passage_text' => "In this session we'll look at food waste — not as an environmental slogan, but as an economic problem with a measurable structure.\n\n"
                    ."Roughly a third of all food produced for human consumption is never eaten. That figure is widely quoted, but the interesting question is where in the chain the loss occurs, because the answer differs sharply by country. In low-income economies, the majority of loss happens early: crops spoil in the field, during storage, or in transport, largely because refrigeration and road infrastructure are inadequate. In high-income economies, the pattern inverts. Production and distribution are comparatively efficient, and most waste occurs at the very end of the chain — in retail, in restaurants, and above all in households.\n\n"
                    ."That distinction matters enormously for policy, because the two problems have almost nothing in common. Early-chain losses are an engineering and investment problem: build cold storage, improve roads, and the losses fall. Late-chain waste is a behavioural problem, and behavioural problems resist capital spending.\n\n"
                    ."Consider date labelling. Most consumers treat 'best before' as a safety deadline, when it is in fact a quality estimate — the manufacturer's judgement about when texture or flavour begins to decline. Studies in the United Kingdom estimated that confusion over these labels alone accounted for around ten per cent of avoidable household food waste. Simplifying the labels is close to costless, and yet the reform took years, because retailers feared liability and manufacturers feared complaints.\n\n"
                    ."I want you to take away one methodological caution. Nearly all household waste figures rest on diaries kept by participants themselves, and people systematically under-record waste — by a factor of two in some validation studies that weighed the bins afterwards. So when you read that a country wastes a certain tonnage per head, treat that number as a floor, not an estimate.",
                'questions' => [
                    ['question' => 'Approximately how much food produced for people is never eaten?', 'options' => ['About a tenth', 'About a third', 'About a half', 'About two thirds'], 'correct' => 1],
                    ['question' => 'Where does most food loss occur in low-income economies?', 'options' => ['In households', 'In restaurants', 'Early in the chain, in fields, storage and transport', 'In supermarkets'], 'correct' => 2],
                    ['question' => 'Why does the lecturer say the two problems have little in common?', 'options' => ['They occur in different seasons', 'One is an engineering problem, the other behavioural', 'One is legal and the other is not', 'They involve different foods'], 'correct' => 1],
                    ['question' => 'What does a "best before" date actually indicate?', 'options' => ['A safety deadline', 'A quality estimate', 'The date of packaging', 'The legal sale limit'], 'correct' => 1],
                    ['question' => 'Why did simplifying date labels take years?', 'options' => ['The change was expensive', 'Retailers feared liability and manufacturers feared complaints', 'Consumers opposed it', 'No research existed'], 'correct' => 1],
                    ['question' => 'What methodological caution does the lecturer give?', 'options' => ['Waste figures are usually exaggerated', 'People under-record waste, so figures are a floor', 'Diaries are more accurate than weighing', 'Only supermarket data is reliable'], 'correct' => 1],
                ],
            ],
        ];
    }
}
