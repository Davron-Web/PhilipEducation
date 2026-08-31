<?php

namespace Database\Seeders;

use App\Models\Ielts\IeltsPassage;
use Illuminate\Database\Seeder;

class IeltsPassageSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->passages() as $data) {
            $questions = $data['questions'];
            unset($data['questions']);

            $passage = IeltsPassage::updateOrCreate(
                ['skill' => $data['skill'], 'title' => $data['title']],
                $data
            );

            $passage->questions()->delete();
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
            // ===== READING =====
            [
                'skill' => 'reading',
                'title' => 'The Rise of Remote Work',
                'level' => 'B1',
                'passage_text' => "Over the past decade, remote work has grown from a rare perk offered by a handful of tech companies into a mainstream way of working. Advances in internet speed, video conferencing software, and cloud-based tools have made it possible for many employees to do their jobs from anywhere with a stable connection.\n\n"
                    ."Supporters of remote work point to several benefits. Employees save time and money by not commuting, and many report feeling more productive when they can design their own schedule. Companies, in turn, can hire talented people regardless of where they live, and often save money on office space.\n\n"
                    ."However, remote work also brings challenges. Some employees feel isolated without daily face-to-face contact with colleagues, and it can be harder for managers to build a strong team culture online. Communication misunderstandings are also more common when people rely mainly on written messages.\n\n"
                    ."As a result, many organisations have adopted a hybrid model, in which staff split their time between the office and home. This approach attempts to combine the flexibility of remote work with the social and collaborative benefits of being physically together.",
                'questions' => [
                    ['question' => 'What has mainly made remote work possible on a large scale?', 'options' => ['Cheaper office rent', 'Advances in internet and software technology', 'Government regulations', 'Shorter working hours'], 'correct' => 1],
                    ['question' => 'According to the text, what is one benefit for companies that allow remote work?', 'options' => ['They pay lower taxes', 'They can hire people from anywhere', 'They need fewer employees', 'They avoid using technology'], 'correct' => 1],
                    ['question' => 'What is mentioned as a challenge of remote work?', 'options' => ['Higher salaries', 'Feelings of isolation', 'Faster internet', 'Shorter meetings'], 'correct' => 1],
                    ['question' => 'What is a "hybrid model" in this context?', 'options' => ['Working only from home', 'Working only in the office', 'Splitting time between home and office', 'Working night shifts'], 'correct' => 2],
                ],
            ],
            [
                'skill' => 'reading',
                'title' => 'Coral Reefs Under Threat',
                'level' => 'B2',
                'passage_text' => "Coral reefs are among the most biologically diverse ecosystems on Earth, supporting roughly a quarter of all marine species despite covering less than one percent of the ocean floor. These underwater structures are built over thousands of years by tiny organisms called coral polyps, which form hard skeletons that gradually accumulate into vast reef systems.\n\n"
                    ."In recent decades, however, reefs worldwide have suffered significant damage. Rising sea temperatures caused by climate change lead to a process known as coral bleaching, in which corals expel the colourful algae living in their tissues and turn white. If temperatures remain high for too long, the coral can die entirely.\n\n"
                    ."Pollution and unsustainable fishing practices compound the problem. Agricultural runoff introduces excess nutrients into coastal waters, encouraging algae growth that can smother coral. Meanwhile, destructive fishing methods, such as the use of explosives or cyanide, physically destroy reef structures that took centuries to form.\n\n"
                    ."Scientists and conservationists are pursuing several strategies to protect remaining reefs, including establishing marine protected areas, breeding heat-resistant coral strains, and reducing carbon emissions globally. While these efforts show promise, most experts agree that only a substantial and rapid reduction in global warming will guarantee the long-term survival of coral reef ecosystems.",
                'questions' => [
                    ['question' => 'What percentage of the ocean floor do coral reefs cover?', 'options' => ['About 25%', 'About 10%', 'Less than 1%', 'About 50%'], 'correct' => 2],
                    ['question' => 'What causes coral bleaching?', 'options' => ['Overfishing alone', 'Rising sea temperatures', 'Too much rainfall', 'Lack of sunlight'], 'correct' => 1],
                    ['question' => 'How does agricultural runoff harm reefs?', 'options' => ['It cools the water', 'It encourages algae growth that smothers coral', 'It increases fish populations', 'It has no effect on reefs'], 'correct' => 1],
                    ['question' => 'According to most experts, what is ultimately needed to save coral reefs?', 'options' => ['More marine protected areas only', 'Breeding programmes alone', 'A significant reduction in global warming', 'Banning all fishing'], 'correct' => 2],
                ],
            ],

            // ===== LISTENING =====
            [
                'skill' => 'listening',
                'title' => 'Booking a Hotel Room',
                'level' => 'A2',
                'passage_text' => "Receptionist: Good afternoon, Lakeside Hotel, how can I help you?\n"
                    ."Caller: Hi, I'd like to book a room for two nights, please. Checking in on Friday the 12th.\n"
                    ."Receptionist: Sure. Would you like a single or a double room?\n"
                    ."Caller: A double room, please. Is breakfast included?\n"
                    ."Receptionist: Yes, breakfast is included in the price, which is 90 dollars per night.\n"
                    ."Caller: That sounds good. Do you have a room with a view of the lake?\n"
                    ."Receptionist: Let me check... Yes, we have one lake-view double room available for those dates. It's 15 dollars extra per night.\n"
                    ."Caller: That's fine, I'll take it. Can I pay by credit card when I arrive?\n"
                    ."Receptionist: Of course. Can I take your name and phone number to confirm the booking?\n"
                    ."Caller: Yes, it's Sarah Collins, and my number is 555-2317.\n"
                    ."Receptionist: Perfect, Ms. Collins. Your room is booked for two nights, Friday to Sunday, with breakfast and a lake view.",
                'questions' => [
                    ['question' => 'How many nights does the caller want to stay?', 'options' => ['One night', 'Two nights', 'Three nights', 'A week'], 'correct' => 1],
                    ['question' => 'What type of room does she book?', 'options' => ['Single room', 'Double room', 'Family room', 'Suite'], 'correct' => 1],
                    ['question' => 'What is included in the room price?', 'options' => ['Dinner', 'Airport transfer', 'Breakfast', 'Parking'], 'correct' => 2],
                    ['question' => 'How much extra does the lake view cost per night?', 'options' => ['$5', '$10', '$15', '$20'], 'correct' => 2],
                ],
            ],
            [
                'skill' => 'listening',
                'title' => 'A Campus Tour Announcement',
                'level' => 'B1',
                'passage_text' => "Good morning, everyone, and welcome to Riverdale University. My name is Tom, and I'll be your guide for today's campus tour.\n\n"
                    ."We'll start here at the main library, which is open twenty-four hours a day during exam periods. Inside, you'll find over two million books, as well as quiet study rooms on the third and fourth floors.\n\n"
                    ."After the library, we'll walk to the Student Centre, where you can find the cafeteria, a small cinema, and the office for student clubs. There are more than one hundred and fifty student clubs at Riverdale, ranging from robotics to hiking.\n\n"
                    ."Our next stop will be the Science Building, home to the chemistry and biology departments. Please note that visitors must wear safety glasses if we enter any of the laboratories.\n\n"
                    ."Finally, we'll finish the tour at the sports complex, which includes a swimming pool, a gym, and six tennis courts. The whole tour should take about forty-five minutes. If you have any questions along the way, feel free to ask.",
                'questions' => [
                    ['question' => 'When is the library open twenty-four hours a day?', 'options' => ['Every day of the year', 'Only on weekends', 'During exam periods', 'Never'], 'correct' => 2],
                    ['question' => 'Where are the quiet study rooms located?', 'options' => ['First and second floors', 'Third and fourth floors', 'Basement', 'Fifth floor only'], 'correct' => 1],
                    ['question' => 'Approximately how many student clubs are there?', 'options' => ['About 50', 'About 100', 'More than 150', 'Exactly 200'], 'correct' => 2],
                    ['question' => 'What must visitors wear in the laboratories?', 'options' => ['Lab coats', 'Safety glasses', 'Gloves', 'Helmets'], 'correct' => 1],
                ],
            ],
        ];
    }
}
