<?php

namespace Database\Seeders;

use App\Models\Book\Book;
use App\Models\Book\BookPage;
use App\Models\System\Level;
use Illuminate\Database\Seeder;

/**
 * Seeds 5 original, level-appropriate adapted short stories for the
 * Books reader feature. Idempotent: skips a book if one with the same
 * title already exists.
 */
class BookSeeder extends Seeder
{
    public function run(): void
    {
        $levels = Level::pluck('id', 'code');

        foreach ($this->books() as $bookData) {
            if (Book::where('title', $bookData['title'])->exists()) {
                continue;
            }

            $book = Book::create([
                'title' => $bookData['title'],
                'author' => $bookData['author'],
                'level_id' => $levels[$bookData['level']] ?? null,
                'description' => $bookData['description'],
                'is_published' => true,
            ]);

            foreach ($bookData['pages'] as $index => $page) {
                BookPage::create([
                    'book_id' => $book->id,
                    'page_number' => $index + 1,
                    'title' => $page['title'] ?? null,
                    'content' => $page['content'],
                ]);
            }

            $this->command?->info("Seeded book: {$bookData['title']} (".count($bookData['pages']).' pages)');
        }
    }

    private function books(): array
    {
        return [
            [
                'title' => "Anna's New School",
                'author' => 'Laura Bennett',
                'level' => 'A1',
                'description' => 'Anna starts at a new school and makes her first friends.',
                'pages' => [
                    ['title' => 'A New Morning', 'content' => 'Anna wakes up early. It is her first day at a new school. She feels a little nervous. She puts on her blue backpack. Her mother makes breakfast: eggs and toast. "Good luck, Anna," says her mother. Anna smiles and says, "Thank you, Mom." She walks to the bus stop. The morning is cold, but the sun is bright. Anna looks at her new school on the map. It is not far from her house. She takes a deep breath and waits for the bus.'],
                    ['title' => 'The Bus Ride', 'content' => "The bus arrives at eight o'clock. Anna sits near the window. She sees tall buildings and green parks. A boy sits next to her. \"Hi, I'm Tom,\" he says. \"Are you new here?\" Anna nods. \"Yes, this is my first day,\" she says. \"Don't worry,\" Tom says. \"The teachers are very nice.\" Anna feels better now. They talk about their favorite subjects. Tom likes math. Anna likes art. The bus stops in front of a big yellow building. \"This is our school,\" Tom says. They walk inside together."],
                    ['title' => 'Meeting the Teacher', 'content' => "Anna's classroom is on the second floor. The teacher's name is Mrs. Green. \"Welcome, Anna,\" Mrs. Green says with a warm smile. \"Please sit here, next to Sara.\" Sara is a friendly girl with curly hair. \"Hi, Anna! I like your backpack,\" Sara says. Anna says, \"Thank you! I like your pencil case.\" The class starts. They learn about animals in science class. Anna answers a question correctly. Mrs. Green says, \"Well done, Anna!\" Anna feels proud and happy. Her nervous feeling is gone now."],
                    ['title' => 'Lunchtime', 'content' => 'At lunchtime, Anna sits with Tom and Sara. They eat sandwiches and apples. "Do you like our school?" Sara asks. "Yes, I do," Anna says. "Everyone is friendly." After lunch, they play in the school yard. There is a big playground with swings and a slide. Anna, Tom, and Sara play a game together. They laugh and run around. A boy named Max joins them too. "Can I play with you?" Max asks. "Of course!" they all say. Now Anna has three new friends.'],
                    ['title' => 'Art Class', 'content' => 'In the afternoon, Anna has art class. The teacher gives them paper and colors. "Draw something you love," the teacher says. Anna draws her family and her old house. Sara draws a cat. Tom draws a football. Max draws a spaceship. They show their pictures to each other. "Your drawing is beautiful, Anna," says Sara. Anna feels very happy. Art is her favorite subject, and now it feels even better with new friends around her. The teacher hangs their pictures on the classroom wall for everyone to see.'],
                    ['title' => 'Going Home', 'content' => "The school day ends at three o'clock. Anna says goodbye to her new friends. \"See you tomorrow!\" Tom says. \"Bye, Anna!\" Sara and Max say together. Anna walks to the bus stop with a big smile. On the bus, she thinks about her day. It was better than she expected. She has new friends, a nice teacher, and a good school. When she gets home, her mother asks, \"How was your first day?\" Anna says, \"It was wonderful! I can't wait for tomorrow.\""],
                ],
            ],
            [
                'title' => 'The Lost Cat',
                'author' => 'David Cole',
                'level' => 'A2',
                'description' => "Ben's cat goes missing, and he searches the neighborhood to find him.",
                'pages' => [
                    ['title' => 'Whiskers is Missing', 'content' => "Ben loved his cat, Whiskers, more than anything. Whiskers was small, orange, and very playful. One morning, Ben woke up and called for his cat. \"Whiskers! Breakfast time!\" But Whiskers didn't come. Ben checked the kitchen, the bedroom, and the living room. His cat was nowhere to be found. Ben's heart began to race. He ran outside and looked under the porch. No cat. He checked the garden. No cat. Ben felt worried. He decided to ask his neighbors for help before school."],
                    ['title' => 'Asking the Neighbors', 'content' => "Ben knocked on Mrs. Parker's door first. \"Have you seen my cat?\" he asked. \"He's orange with white paws.\" Mrs. Parker shook her head. \"Sorry, dear, I haven't.\" Ben tried the next house. Mr. Wilson was watering his plants. \"A little orange cat? I think I saw one near the park this morning,\" he said. Ben thanked him and ran toward the park. His backpack bounced on his shoulders as he ran. He hoped Whiskers was safe. The park was large, with many trees and bushes to search."],
                    ['title' => 'Searching the Park', 'content' => "At the park, Ben looked everywhere. He checked under benches and behind trees. \"Whiskers, where are you?\" he called out. Suddenly, he heard a soft meow. It came from a tall tree near the pond. Ben looked up and saw his cat sitting on a branch, looking scared. \"There you are!\" Ben shouted with relief. But Whiskers was too high to reach. Ben didn't know what to do. He looked around for someone who could help him. The park suddenly felt very quiet and empty."],
                    ['title' => 'A Helpful Stranger', 'content' => "A woman walking her dog stopped to help. \"Is that your cat?\" she asked kindly. \"Yes! He climbed too high,\" Ben explained. The woman smiled. \"I have an idea. Cats often come down when they feel safe.\" She suggested Ben sit quietly and call his cat gently. Ben sat under the tree and spoke softly. \"Come on, Whiskers. It's okay.\" Slowly, the cat began to climb down, branch by branch. Ben held out his arms, ready to catch him. His hands were shaking, but he didn't move an inch."],
                    ['title' => 'Reunited', 'content' => "Whiskers jumped the last bit into Ben's arms. \"I got you!\" Ben laughed with joy. He hugged his cat tightly. The cat purred and rubbed against Ben's cheek. \"Thank you so much,\" Ben said to the kind woman. \"You're welcome,\" she replied. \"Take good care of him.\" Ben carried Whiskers home carefully, making sure not to drop him. He was so happy that his search was finally over. His cat was safe again. Ben promised himself he would watch the door more carefully from now on."],
                    ['title' => 'A Lesson Learned', 'content' => "When Ben got home, his mother was waiting at the door. \"I was so worried!\" she said. Ben told her the whole story. \"Next time, let's put a bell on his collar,\" his mother suggested. \"That way, we can always find him.\" Ben agreed. That evening, they bought a small bell for Whiskers. The cat didn't seem to mind at all. From that day on, Ben always checked the bell before Whiskers went outside to play. It gave the whole family peace of mind."],
                ],
            ],
            [
                'title' => 'A Trip to the Market',
                'author' => 'Sofia Reyes',
                'level' => 'A2',
                'description' => 'Layla and her father spend a Saturday morning shopping at the local market.',
                'pages' => [
                    ['title' => 'Saturday Morning', 'content' => "Every Saturday, Layla and her father go to the local market together. It is Layla's favorite day of the week. \"Get your basket ready,\" her father says with a smile. They walk down the busy street, past colorful shops and friendly neighbors. The market is full of fresh fruit, vegetables, and delicious smells. Layla loves looking at all the different stalls. \"What are we buying today?\" she asks. Her father checks his list. \"Tomatoes, apples, bread, and cheese,\" he says."],
                    ['title' => 'The Fruit Stall', 'content' => "At the fruit stall, a friendly seller greets them. \"Good morning! What can I get you today?\" she asks. Layla points to the bright red apples. \"Those look delicious,\" she says. Her father buys a bag of apples and some juicy oranges. Layla picks up a strange purple fruit. \"What is this one?\" she asks curiously. \"That's a dragon fruit,\" the seller explains. \"It's sweet and very healthy.\" Layla decides to try something new and adds it to their basket."],
                    ['title' => 'Meeting a Neighbor', 'content' => "While walking to the next stall, they meet Mrs. Ahmadi, their kind neighbor. \"Hello! How are you both today?\" she asks warmly. \"We're great, thank you,\" Layla's father replies. Mrs. Ahmadi is buying fresh flowers for her garden. \"Your flowers are always beautiful,\" Layla says politely. Mrs. Ahmadi smiles. \"Thank you, dear. Maybe I'll teach you how to plant some someday.\" Layla loves this idea. She waves goodbye as they continue shopping for more items on their list. She hopes they will visit Mrs. Ahmadi's garden very soon."],
                    ['title' => 'Bargaining for Bread', 'content' => "At the bread stall, the smell of fresh bread fills the air. \"How much for two loaves?\" Layla's father asks. The baker tells him the price. Layla's father politely asks for a small discount, since they always buy from this stall. The baker laughs and agrees. \"For loyal customers, of course!\" Layla watches carefully and learns something new about shopping. \"Can I choose the bread this time?\" she asks. Her father nods, and Layla picks the biggest, softest loaf. She feels very proud of her careful choice."],
                    ['title' => 'A Small Problem', 'content' => "Suddenly, Layla realizes she cannot find her favorite hat. \"Dad, I lost my hat!\" she says, worried. They both look around near the last few stalls they visited. Layla feels a bit upset. Her father stays calm. \"Let's ask the sellers if they've seen it,\" he suggests. They return to the fruit stall first. The kind seller smiles and holds up Layla's hat. \"You left this here!\" she says. Layla feels relieved and thanks her happily. She promises to be more careful with her things next time."],
                    ['title' => 'Heading Home', 'content' => "With their baskets full, Layla and her father start walking home. The bags are heavy, but Layla doesn't mind. \"This was a great morning,\" she says happily. Her father agrees. \"Markets are more fun with good company,\" he says. At home, they unpack the fresh food together. Layla tries the dragon fruit first. \"It's delicious!\" she says with a big smile. She already can't wait for next Saturday's trip to the market with her father. Markets, she decides, are one of her favorite places in the whole town."],
                ],
            ],
            [
                'title' => 'The Mystery of the Old House',
                'author' => 'Michael Turner',
                'level' => 'B1',
                'description' => 'Three curious friends explore an abandoned house and uncover its secret.',
                'pages' => [
                    ['title' => 'The Empty House', 'content' => "At the end of Maple Street stood an old, abandoned house. Its windows were broken, and the garden was overgrown with weeds. Most children avoided it, convinced that something strange lived inside. Jake, Priya, and Oliver were different. They were curious, and curiosity often led them into trouble. \"I heard someone saw a light in there last night,\" Priya whispered as they stood outside the rusty gate. Jake raised an eyebrow. \"A light? Maybe it's just old wiring,\" he said, though he didn't sound entirely convinced."],
                    ['title' => 'A Bold Decision', 'content' => "Oliver, the boldest of the three, pushed open the creaking gate. \"Let's find out for ourselves,\" he said confidently. Priya hesitated. \"Are you sure this is a good idea?\" she asked. Jake shrugged. \"We're just looking. What's the worst that could happen?\" The three friends walked carefully through the overgrown garden, stepping over broken branches and tall grass. The front door of the house was slightly open, as if someone, or something, had left in a hurry. A cold breeze made them shiver."],
                    ['title' => 'Inside the House', 'content' => "Inside, dust covered every surface, and old furniture was scattered around the room. Cobwebs hung from the ceiling like curtains. \"This place is creepy,\" Priya said, holding onto Jake's arm. Suddenly, they heard a soft scratching sound coming from upstairs. All three froze. \"Did you hear that?\" Oliver whispered. Jake nodded slowly, his heart pounding. Despite their fear, they decided to investigate further, climbing the old wooden stairs one careful step at a time, trying not to make any noise."],
                    ['title' => 'The Scratching Sound', 'content' => "Upstairs, the sound grew louder. It was coming from behind a closed door at the end of the hallway. Oliver reached for the handle, his hand trembling slightly. \"On the count of three,\" he said. \"One, two, three!\" He pushed the door open quickly. To their surprise, a small gray cat jumped out, startled by their sudden appearance. Priya laughed with relief. \"It's just a cat!\" she exclaimed. The tension in the room disappeared instantly, replaced by laughter and relief."],
                    ['title' => 'A New Friend', 'content' => "The cat seemed thin and hungry. It must have been living in the house for weeks. \"Poor thing,\" Priya said softly, kneeling down to pet it. The cat purred and rubbed against her hand, clearly grateful for the attention. \"We can't just leave it here,\" Jake said. Oliver agreed immediately. \"Let's take it home. My mom loves animals.\" They found an old blanket to wrap the cat in, keeping it warm and safe for the walk back to Oliver's house."],
                    ['title' => 'The Real Mystery', 'content' => "As they left, Jake noticed old letters scattered on a table near the door. He picked one up carefully. It was from years ago, written by a woman who once lived there. The letter explained that she had moved away suddenly to care for a sick relative and never returned. \"So that's the mystery,\" Priya said thoughtfully. \"No ghosts, just a sad story.\" The house wasn't haunted after all, just forgotten, like the cat that had been living inside it."],
                    ['title' => 'A Happy Ending', 'content' => "Oliver's mother welcomed the cat warmly, naming her Luna because of her bright eyes. The three friends visited Luna often, telling everyone at school about their adventure. Some classmates didn't believe the story, but Jake, Priya, and Oliver knew the truth. The old house was cleaned up soon after, and new owners eventually moved in. Every time the friends walked past Maple Street, they smiled, remembering the day their curiosity led them to a new furry friend instead of a frightening ghost."],
                ],
            ],
            [
                'title' => "Samira's First Job",
                'author' => 'Elena Petrova',
                'level' => 'B1',
                'description' => 'Samira takes her first part-time job at a café and learns valuable lessons.',
                'pages' => [
                    ['title' => 'The Interview', 'content' => 'Samira had been waiting for this moment all summer. At seventeen, she was finally old enough to apply for a part-time job. The small café near her school, Sunny Corner, needed a weekend server. She walked in, hands slightly shaking, and asked to speak with the manager. "You must be Samira," said Mr. Bakhtiyor, the owner, checking his schedule. "Tell me, why do you want to work here?" Samira took a breath. "I love meeting new people, and I want to learn responsibility," she answered honestly.'],
                    ['title' => 'The First Day', 'content' => "Mr. Bakhtiyor hired her on the spot, impressed by her confidence. On her first day, Samira arrived fifteen minutes early, wearing a neat apron. \"Nervous?\" asked Elin, an experienced server who would train her. \"A little,\" Samira admitted. Elin smiled. \"Everyone feels that way at first. You'll be fine.\" Samira learned how to take orders, carry trays, and use the coffee machine. It was busier than she expected, but she enjoyed the fast pace and the friendly customers who came in every hour."],
                    ['title' => 'A Difficult Customer', 'content' => "By her second week, Samira felt more confident. Then, one afternoon, a customer complained loudly about his cold coffee. \"This is unacceptable!\" he said, raising his voice. Samira felt her face turn red with embarrassment. She remembered what Elin had taught her: stay calm and listen first. \"I'm very sorry, sir. Let me make you a fresh cup right away,\" she said politely. The man calmed down and even apologized for shouting. Samira felt proud of how she handled the situation."],
                    ['title' => 'Learning Responsibility', 'content' => "As weeks passed, Samira took on more responsibilities. She learned to manage the cash register and close the café at night. One evening, she noticed the day's earnings didn't match the receipts. Instead of ignoring it, she carefully checked every transaction until she found her small mistake. She reported it honestly to Mr. Bakhtiyor the next morning. \"Thank you for telling me,\" he said. \"That kind of honesty is exactly what I look for in an employee.\" Samira felt relieved and respected."],
                    ['title' => 'Making a Friend', 'content' => "Working at Sunny Corner also gave Samira a new friend. Elin, once just her trainer, became someone she talked to about everything: school, family, and future plans. During slow afternoons, they shared stories and laughed together while wiping down tables. \"I want to study business someday,\" Samira told her. \"You'd be great at it,\" Elin said encouragingly. \"You're already good with people and numbers.\" Their friendship made the job feel less like work and more like spending time with someone who understood her."],
                    ['title' => 'A Busy Weekend', 'content' => "One Saturday, the café became unexpectedly crowded during a town festival. Every table was full, and the line stretched out the door. Samira and Elin worked quickly, taking orders and delivering food without a single mistake. Mr. Bakhtiyor watched proudly from behind the counter. By closing time, both girls were exhausted but happy. \"You handled that like a professional,\" Mr. Bakhtiyor said. \"I couldn't have done it without Elin,\" Samira replied humbly, though she knew she had grown a lot that day."],
                    ['title' => 'Looking Forward', 'content' => 'By the end of summer, Samira had saved enough money for a new laptop for school. More importantly, she had learned lessons no classroom could teach: responsibility, patience, and how to work well with others. On her last shift before school started again, Mr. Bakhtiyor gave her a small gift, a notebook. "For writing down your business ideas," he said with a wink. Samira smiled, thinking about her future. Her first job had taught her more than she ever expected it would.'],
                ],
            ],
        ];
    }
}
