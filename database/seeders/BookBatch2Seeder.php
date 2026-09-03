<?php

namespace Database\Seeders;

use App\Models\Book\Book;
use App\Models\Book\BookPage;
use App\Models\System\Level;
use Illuminate\Database\Seeder;

/**
 * Second batch of adapted readers (see BookSeeder). Ten original graded
 * stories that complete the ladder: the first batch only covered A1-B1,
 * so this one adds material at every level up to C2. Sentence length,
 * tense range and vocabulary grow with the level.
 *
 * All texts are written for this project — no copyrighted work is
 * reproduced. Idempotent: a book whose title already exists is skipped.
 */
class BookBatch2Seeder extends Seeder
{
    public function run(): void
    {
        $levels = Level::pluck('id', 'code');

        $created = 0;
        $skipped = 0;

        foreach ($this->books() as $bookData) {
            if (Book::where('title', $bookData['title'])->exists()) {
                $skipped++;

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
                    'title' => $page['title'],
                    'content' => $page['content'],
                ]);
            }

            $created++;
            $this->command?->info("Seeded book: {$bookData['title']} ({$bookData['level']}, ".count($bookData['pages']).' pages)');
        }

        $this->command?->info("BookBatch2Seeder: +{$created} books, {$skipped} skipped as already existing.");
    }

    /**
     * @return array<int, array{title: string, author: string, level: string, description: string, pages: array<int, array{title: string, content: string}>}>
     */
    private function books(): array
    {
        return [
            // ============================ A1 ============================
            [
                'title' => 'The Red Bicycle',
                'author' => 'Emma Ward',
                'level' => 'A1',
                'description' => 'Sam wants a red bicycle. He works, he saves, and he waits.',
                'pages' => [
                    ['title' => 'The Shop Window', 'content' => "Sam walks home from school every day. He always stops at the same shop. In the window there is a red bicycle. It is bright and clean. The wheels are big. The bell is silver. Sam looks at the price. It is sixty pounds. Sam has four pounds in his pocket. He counts the money again. It is still four pounds. Sam is nine years old. He does not have a job. But he wants this bicycle very much. He walks home slowly and thinks about it."],
                    ['title' => 'A Plan', 'content' => "At dinner, Sam tells his father about the bicycle. \"It is very expensive,\" his father says. \"I know,\" Sam says. \"Can I work? I can help people.\" His father smiles. \"That is a good idea. Small jobs, small money. But every week the money grows.\" Sam takes an old box from his room. He writes BICYCLE on the box with a black pen. He puts his four pounds inside. The box is almost empty. Sam looks at it and says, \"Not for long.\""],
                    ['title' => 'The Garden', 'content' => "On Saturday, Sam knocks on Mrs. Blake's door. She is old and she lives next to Sam. \"Can I help you in the garden?\" Sam asks. \"Yes, please,\" she says. Sam takes the leaves from the grass. He puts them in a big bag. The bag is heavy. His hands are dirty. After two hours, the garden is clean. Mrs. Blake gives him five pounds and a glass of juice. \"Thank you, Sam. You work well.\" Sam runs home and puts the money in the box."],
                    ['title' => 'The Cars', 'content' => "Next Saturday, Sam washes cars. He takes a bucket, water and a big yellow sponge. His neighbour Mr. Kern has a small blue car. \"Two pounds,\" Sam says. \"Good price,\" Mr. Kern says. Sam washes the car. Then he washes another car, and another one. His arms are tired. His shoes are wet. But in the evening he has ten pounds. Now the box is not empty. Sam counts everything: nineteen pounds. He needs more, but he is happy."],
                    ['title' => 'Waiting', 'content' => "Autumn comes. It is cold and it rains a lot. There are no leaves and no dirty cars. Sam does not earn money for three weeks. One evening he looks in the box and feels sad. \"It is too slow,\" he says. His mother sits next to him. \"Look at the box in September,\" she says. \"Four pounds. Look at it now. Thirty-one pounds. That is not slow. That is you.\" Sam looks at the box again. She is right. He decides to wait."],
                    ['title' => 'The Red Bicycle', 'content' => "In March, Sam has sixty pounds. He goes to the shop with his father. The red bicycle is still in the window. \"I want this one, please,\" Sam says. He gives the money to the man. The man gives him the bicycle and the silver bell. Outside, Sam sits on it. His father holds the back. \"Ready?\" \"Ready.\" Sam rides down the street. The bell is loud and bright. He rides past Mrs. Blake's garden and past Mr. Kern's blue car, and he smiles all the way home."],
                ],
            ],
            [
                'title' => 'A Day at the Zoo',
                'author' => 'Peter Hall',
                'level' => 'A1',
                'description' => 'Mia visits the zoo with her class and meets a very noisy parrot.',
                'pages' => [
                    ['title' => 'The Yellow Bus', 'content' => "Today is Friday. Mia does not go to the classroom. Her class goes to the zoo! A big yellow bus waits outside the school. Mia sits next to her friend Ella. Their teacher, Mr. Ford, counts the children. \"One, two, three... twenty-two. Good, everybody is here.\" The bus starts. The children sing a song. Mia looks out of the window. She sees houses, then trees, then a big green gate. \"We are here!\" Ella says."],
                    ['title' => 'The Monkeys', 'content' => "The first animals are the monkeys. They are small and brown. They jump from tree to tree. One monkey has a baby on its back. \"Look!\" Mia says. \"The baby is very small.\" The monkeys are fast. They eat fruit with their hands. One monkey looks at Mia and puts its head on one side. Mia laughs. \"He looks at me!\" Mr. Ford says, \"Monkeys are clever. They watch people, and people watch them.\""],
                    ['title' => 'The Elephants', 'content' => "Next, the class sees the elephants. They are very big and grey. One elephant drinks water with its long nose. It puts the water in its mouth. Then it puts water on its back. \"Why?\" Ella asks. \"The sun is hot,\" Mr. Ford says. \"Water is cold. It helps them.\" Mia takes a photo with her small camera. The elephant walks slowly to the tree and eats green leaves. Mia thinks elephants are quiet and kind."],
                    ['title' => 'Lunch by the Lake', 'content' => "At one o'clock, the class has lunch. They sit on the grass near the lake. Mia has a cheese sandwich, an apple and water. Ella has rice and chicken. They talk about the animals. \"I like the monkeys,\" Ella says. \"I like the elephants,\" Mia says. A small bird comes near their bags. It wants bread. Mia gives it one small piece. The bird takes it and flies to a tree. \"Now he has lunch too,\" Mia says."],
                    ['title' => 'The Noisy Parrot', 'content' => "After lunch they go to the bird house. There are red birds, blue birds and one big green parrot. The parrot looks at the children. Then it says, \"Hello! Hello!\" All the children laugh. \"It can talk!\" Mia says. \"Hello!\" the parrot says again, very loudly. Ella says hello to the parrot, and the parrot says hello again. Mr. Ford smiles. \"He says hello all day,\" the zoo woman tells them. \"He is never tired.\""],
                    ['title' => 'Home Again', 'content' => "At four o'clock, the children walk back to the yellow bus. Mia is tired but happy. On the bus, she looks at her photos: the monkeys, the elephants, the green parrot. Ella sleeps. At school, Mia's mother waits at the gate. \"How was the zoo?\" she asks. \"Very good!\" Mia says. \"I saw a parrot and it can say hello.\" \"Really?\" her mother says. \"Yes,\" Mia says. \"Hello! Hello!\" And they laugh together."],
                ],
            ],

            // ============================ A2 ============================
            [
                'title' => 'The Birthday Surprise',
                'author' => 'Clara Nowak',
                'level' => 'A2',
                'description' => 'Three friends plan a secret party, and almost everything goes wrong.',
                'pages' => [
                    ['title' => 'A Secret Plan', 'content' => "Nina's birthday was on Saturday, but she thought nobody remembered it. Her friends Omar and Ruby remembered very well. On Wednesday, they met after school in the park. \"We need a plan,\" Omar said. \"A surprise party at my house,\" Ruby said. \"My parents are away until Sunday.\" They wrote a list: cake, music, decorations and twelve guests. Omar took the cake. Ruby took the decorations. \"And nobody tells Nina,\" Ruby said. \"Nobody,\" Omar agreed. They shook hands like people in a film."],
                    ['title' => 'The Cake Problem', 'content' => "On Friday evening, Omar tried to bake a chocolate cake. He read the recipe twice, but he still put salt in the bowl instead of sugar. When the cake came out of the oven, it looked perfect and tasted terrible. Omar sat down and put his head in his hands. Then he had an idea. He cut the cake into small pieces, threw them away, and started again at nine o'clock. The second cake was ready at midnight. This time he tasted it before he stopped."],
                    ['title' => 'Keeping the Secret', 'content' => "On Saturday morning, Nina met Ruby in town. \"What are you doing today?\" Nina asked. Ruby's face went red. \"Nothing. Homework. Very boring homework,\" she said quickly. She was carrying a bag full of balloons, so she held it behind her back. \"Are you okay?\" Nina asked. \"You look strange.\" \"I'm fine!\" Ruby said, and she walked away too fast. Nina watched her friend go and thought, \"Everybody is acting strangely today. Maybe they really did forget my birthday.\""],
                    ['title' => 'Everything Goes Wrong', 'content' => "At five o'clock, the guests arrived at Ruby's house. At half past five, the music player broke. At six o'clock, it started to rain, and two guests were still outside without umbrellas. Then Omar phoned. \"I'm at the bus stop with the cake and the bus isn't coming,\" he said. Ruby looked at the empty table and the silent room. \"This is a disaster,\" she said quietly. But she took a breath, found an old radio in the kitchen, and turned it on."],
                    ['title' => 'The Door Opens', 'content' => "At seven o'clock, Nina knocked on the door. Ruby had invited her for boring homework. Nina came in slowly and everybody shouted, \"Surprise!\" Nina stopped. She looked at the balloons, the twelve faces, the old radio playing music in the corner. Then she started to cry and laugh at the same time. \"I thought you all forgot,\" she said. \"Forgot?\" Ruby said. \"We have talked about nothing else for four days.\" Omar arrived one minute later, wet from the rain, holding the cake above his head."],
                    ['title' => 'The Best Part', 'content' => "Later, when the guests were eating cake, Nina sat on the stairs with her two friends. \"The music player is broken, the radio only plays old songs, and Omar is completely wet,\" she said. \"Sorry,\" Ruby said. \"It wasn't the party we planned.\" Nina shook her head. \"It's the best birthday I've had,\" she said. \"You made a plan for me. That's the part I'll remember.\" Omar took a big piece of cake. \"Taste it first,\" he said. \"Then decide.\""],
                ],
            ],
            [
                'title' => 'Rain on the Camping Trip',
                'author' => 'Martin Vale',
                'level' => 'A2',
                'description' => 'A family camping weekend goes wrong, and turns into something better.',
                'pages' => [
                    ['title' => 'Leaving on Friday', 'content' => "The Ortiz family left the city on Friday afternoon. The car was full: a tent, four sleeping bags, a box of food and Dad's old guitar. \"The weather will be perfect,\" Dad said. \"Sunshine all weekend.\" Lucia, who was thirteen, looked at the grey sky and said nothing. Her little brother Mateo asked, \"Are we there yet?\" eleven times in two hours. They arrived at the lake at six o'clock. The air smelled of pine trees and water. It was beautiful, and it was completely dry."],
                    ['title' => 'Putting Up the Tent', 'content' => "Putting up the tent took much longer than Dad promised. \"Ten minutes,\" he said at six o'clock. At half past seven, the tent was still on the ground and Mum was laughing. Mateo held a pole in each hand and looked confused. Lucia read the instructions out loud, and slowly the tent stood up. It leaned a little to the left, but it stood. \"Perfect,\" Dad said. \"It is not perfect,\" Mum said, \"but it is a tent.\" They cooked sausages over a small fire and went to sleep happy."],
                    ['title' => 'Rain at Midnight', 'content' => "At midnight, the rain started. It was not gentle rain. It hit the tent like small stones, and it did not stop. At two o'clock, Lucia felt water under her sleeping bag. \"Dad,\" she said. \"Dad. There's water.\" Everybody woke up. They moved the bags to one side and put a towel on the floor. The towel was wet in five minutes. Outside, the rain became louder. Mateo started to cry. \"I want to go home,\" he said. Nobody answered, because everybody wanted the same thing."],
                    ['title' => 'The Long Morning', 'content' => "In the morning, the rain was still falling. Everything was wet: their clothes, the bread, the matches. Dad tried to make a fire four times. \"We could drive home,\" Mum said carefully. Dad looked at the grey lake for a long moment. Then he opened the car and took out his old guitar, which had stayed dry in its case. \"Or,\" he said, \"we could stay in the tent and be terrible at singing.\" Mateo stopped crying. \"Can I be terrible too?\" he asked."],
                    ['title' => 'Songs in the Tent', 'content' => "They spent five hours in that tent. Dad played every song he knew, and then he played them again. Mum invented a game with a pen and the back of the map. Lucia taught Mateo to play cards, and Mateo won three times, probably because Lucia let him. Outside, the rain kept falling on the lake. Inside, it was warm and loud and very close. At some point Lucia realised she had not looked at her phone since the morning. There was no signal anyway."],
                    ['title' => 'Sunday Light', 'content' => "On Sunday, the rain stopped. The sun came out at nine o'clock, and the lake turned silver. They put their wet clothes on the rocks to dry and ate the last dry bread with jam. \"So,\" Dad said. \"Worst trip ever?\" Mateo shook his head hard. \"Best trip,\" he said. Lucia laughed. \"We were wet for eighteen hours.\" \"Yes,\" Mum said, \"and in ten years, that is exactly the part you will tell people about.\" Lucia thought about it, and knew her mother was right."],
                ],
            ],

            // ============================ B1 ============================
            [
                'title' => 'The Letter in the Attic',
                'author' => 'Helen Marsh',
                'level' => 'B1',
                'description' => 'While clearing her grandmother\'s house, Zoe finds a letter that was never sent.',
                'pages' => [
                    ['title' => 'Clearing the House', 'content' => "The house had to be empty by the end of the month. Zoe had agreed to help her mother, though she had not expected it to feel like this. Every object had a story attached to it, and her grandmother was no longer there to tell any of them. They worked through the kitchen on Saturday and the bedrooms on Sunday. By the second week, only the attic was left. \"There won't be much up there,\" her mother said. \"Just old furniture.\" She was wrong, but neither of them knew that yet."],
                    ['title' => 'The Wooden Box', 'content' => "The attic smelled of dust and warm wood. Zoe pushed aside two broken chairs and a rolled-up carpet before she saw the box. It was small, made of dark wood, and it had her grandmother's initials burned into the lid: E.M. Inside there were photographs, a train ticket from 1962, a dried flower that fell apart when she touched it, and an envelope. The envelope had a name and an address on it, written in careful handwriting. It had a stamp. It had never been posted."],
                    ['title' => 'A Name She Did Not Know', 'content' => "The name on the envelope was Thomas Farrell, and the address was in a town Zoe had never heard her grandmother mention. She carried the letter downstairs and showed it to her mother, who turned it over twice without opening it. \"I have no idea who that is,\" her mother said at last. \"She never spoke about anyone called Thomas.\" They sat at the kitchen table with the envelope between them. \"Should we open it?\" Zoe asked. Her mother looked at the sealed edge for a long time and finally nodded."],
                    ['title' => 'What the Letter Said', 'content' => "The letter was two pages long and dated April 1963. In it, a young woman explained that she was not going to come to the station on Friday, that her father was ill and needed her, and that she hoped Thomas would understand and would write to her again. The last line said: \"I am not saying no. I am saying not yet.\" Zoe read it twice. \"She never sent it,\" she said quietly. \"He waited at the station,\" her mother said, \"and nobody came, and nobody explained.\""],
                    ['title' => 'Looking for Thomas', 'content' => "That evening, Zoe searched online for Thomas Farrell. She found a retired music teacher of the right age, living forty minutes away in the same town written on the envelope. She wrote to him carefully, explaining who she was and what she had found, and she did not really expect an answer. He replied the next morning, in three short sentences. He remembered the name Elena Marsh perfectly well, he wrote. He had wondered about that Friday for almost sixty years. He asked whether he could read the letter."],
                    ['title' => 'The Visit', 'content' => "They met in a café near the library. Thomas Farrell was a tall, thin man who apologised for being early. Zoe gave him the envelope and watched him read it with both hands flat on the table. When he finished, he did not say anything for almost a minute. \"I thought she had simply changed her mind,\" he said finally. \"I was twenty-three. I decided not to make a fool of myself by asking.\" He folded the pages very carefully. \"It would have been a different life,\" he said, \"but not necessarily a better one.\""],
                    ['title' => 'What Zoe Kept', 'content' => "Thomas asked to keep the letter, and Zoe agreed at once. In exchange, he gave her a photograph from 1962: two young people outside a cinema, laughing at something outside the frame. Her grandmother looked impossibly young. On the drive home, Zoe's mother said, \"I keep thinking she should have posted it.\" Zoe watched the road and thought about the last line. Not a refusal, just a delay that had lasted a lifetime. She decided, without saying it out loud, to be much quicker about the letters in her own life."],
                ],
            ],
            [
                'title' => 'The Football Trial',
                'author' => 'Owen Blake',
                'level' => 'B1',
                'description' => 'Karim fails at a trial he has trained a year for, and has to decide what comes next.',
                'pages' => [
                    ['title' => 'One Year of Mornings', 'content' => "For a whole year, Karim got up at half past five. He ran four kilometres before school, whatever the weather, and he practised in the park until it was too dark to see the ball. His sister thought he was mad. His father said very little, but he always left breakfast on the table. All of it was for one Saturday in April, when the city club held open trials for the under-seventeen team. Fifty boys would come. Four would be chosen. Karim had counted the days since September."],
                    ['title' => 'The Trial', 'content' => "The trial started at nine, on a pitch that was harder and faster than he was used to. Karim played well for the first twenty minutes. He won the ball twice and made a pass that a coach actually wrote down. Then, in a small, stupid moment, he tried something clever near his own goal, lost the ball, and the other team scored. After that, he could not find his rhythm again. He knew, an hour before the end, that it was already over. He kept running anyway."],
                    ['title' => 'The List', 'content' => "At four o'clock, a coach read four names from a clipboard. Karim's name was not one of them. Around him, boys were shaking hands and pretending they did not mind. Karim's father was waiting by the fence with the car keys in his hand and a careful expression on his face. \"Well played,\" he said. \"I wasn't,\" Karim said. They drove home without the radio on. That night Karim put his boots in the cupboard and told himself he would not take them out again."],
                    ['title' => 'Three Weeks of Nothing', 'content' => "For three weeks he did nothing. He slept late, avoided the park, and told his friends he was busy. It was surprisingly easy to stop. What he had not expected was how loud his mornings became without training in them. His sister eventually sat down opposite him and said, \"You're allowed to be sad. You're not allowed to be boring.\" Karim laughed for the first time in a month. Later that evening, he took the boots out of the cupboard and left them by the door, without deciding anything."],
                    ['title' => 'A Different Phone Call', 'content' => "In May, one of the coaches from the trial phoned. He was not calling about the team. A local club needed someone to help train the under-elevens on Tuesdays and Thursdays, and he had remembered a boy who kept running when he already knew he had lost. \"It's four hours a week and the money is terrible,\" the coach said. Karim said yes before he had properly thought about it. He put the phone down and stood in the hall, slightly confused about why he felt so much lighter."],
                    ['title' => 'Tuesdays and Thursdays', 'content' => "The under-elevens were chaos. They argued about positions, fell over their own feet, and once lost a ball in a river. Karim discovered that he was good at explaining things, and better at noticing which child was about to give up. He started planning sessions on paper. He learned the difference between a player who needed pushing and one who needed praise. By July, two of the coaches were asking his opinion about the team, and he was arriving early because he wanted to, not because he had to."],
                    ['title' => 'The Second Saturday', 'content' => "The next April, Karim went back to the open trials, but this time with a clipboard in his hand, standing next to the coach who had phoned him. Fifty boys ran onto the same hard, fast pitch. He watched one of them lose the ball near his own goal and then keep running for the whole hour afterwards. Karim wrote the name down. \"That one,\" he said. The coach raised an eyebrow. \"He made a mistake.\" \"He made one mistake,\" Karim said, \"and then he made none.\""],
                ],
            ],

            // ============================ B2 ============================
            [
                'title' => 'The Night Shift',
                'author' => 'Priya Raman',
                'level' => 'B2',
                'description' => 'A nurse on a quiet night shift has to decide when to trust her own judgement.',
                'pages' => [
                    ['title' => 'Ten to Six', 'content' => "The night shift ran from ten in the evening until six in the morning, and Dana had worked enough of them to know that the difficulty was never the emergencies. Emergencies were loud and obvious and everybody moved at once. The difficulty was the long, quiet hours in between, when the ward settled into breathing and beeping and the temptation was simply to let the night pass undisturbed. On that particular Tuesday, there were nineteen patients on the ward, one junior doctor on call, and no reason at all to expect trouble."],
                    ['title' => 'Bed Fourteen', 'content' => "Mr. Oyelaran in bed fourteen had come in two days earlier with a routine infection and was due to be discharged in the morning. At half past midnight, Dana took his observations and found them almost normal. Almost. His heart rate had risen by twelve beats since the previous check, and his blood pressure had drifted down slightly. Individually, both numbers meant nothing. She wrote them down and moved on to bed fifteen, and then found herself standing in the corridor a minute later, still thinking about them."],
                    ['title' => 'A Feeling Is Not a Number', 'content' => "She went back and looked at him properly. He was awake, polite, and insisted he felt fine, just a little cold. His skin was slightly grey in a way that could easily have been the lighting. Everything measurable was within acceptable limits, and the protocol she was trained to follow did not require her to escalate anything. Dana had been a nurse for eleven years, and in that time she had learned to distrust the phrase \"a feeling\", because feelings could not be written in a chart or defended at a review meeting."],
                    ['title' => 'The Junior Doctor', 'content' => "She phoned the on-call doctor anyway. He arrived nine minutes later, visibly exhausted, and listened while she explained numbers that sounded thinner out loud than they had in her head. \"His obs are basically fine,\" he said, not unkindly. \"I know how they read,\" Dana said. \"I'm telling you what he looks like.\" There was a pause in which she could see him weighing his own tiredness against her eleven years. Then he put his bag down and said, \"All right. Let's do bloods and a lactate.\""],
                    ['title' => 'Three in the Morning', 'content' => "The results came back at ten past three and they were not fine at all. The lactate was high, the inflammatory markers had climbed steeply, and what had looked like a resolving infection was quietly becoming sepsis. Antibiotics went up at twenty past. Fluids followed. By four o'clock, Mr. Oyelaran had been moved to a bed where he could be watched continuously, and the grey colour Dana had blamed on the lighting was fading from his face. Nobody in the corridor said anything dramatic. The ward simply carried on."],
                    ['title' => 'What Nobody Writes Down', 'content' => "At six, Dana handed over to the morning staff in the usual flat, factual language: patient in bed fourteen, deteriorated overnight, treated, stable. There was no line on the form for the ninety seconds she had spent standing in the corridor deciding whether to go back. She went home, slept badly, and woke at two in the afternoon thinking about how easily she might have written those numbers down and simply continued to bed fifteen, and how nobody, including her, would ever have known."],
                    ['title' => 'The Following Tuesday', 'content' => "A week later, the junior doctor found her in the staff kitchen. \"I nearly didn't come up that night,\" he admitted. \"I'd been on for fourteen hours and the numbers really did look fine.\" Dana passed him a cup of coffee. \"So why did you?\" He thought about it. \"Because you didn't say you were worried about the obs. You said you were worried about him.\" She nodded slowly. It was, she thought, the most useful thing anybody had said to her about the job in years."],
                ],
            ],
            [
                'title' => 'The Bookshop on Carter Street',
                'author' => 'Julian Frost',
                'level' => 'B2',
                'description' => 'A failing bookshop gets three months to prove it deserves to exist.',
                'pages' => [
                    ['title' => 'Three Months', 'content' => "The letter from the landlord was polite, which somehow made it worse. The rent on the Carter Street property would rise by forty per cent from January, and if Halloran Books could not meet the new figure, the lease would not be renewed. Ivy Halloran read it twice at the counter, then folded it and put it under the till, where she kept everything she did not want to look at. Her father had opened the shop in 1974. She had three months to decide whether to be the one who closed it."],
                    ['title' => 'The Numbers', 'content' => "That evening she did what she had been avoiding for two years and went through the accounts properly. The picture was not dramatic; it was worse than that, it was slow. Sales had fallen by roughly six per cent every year for a decade. The shop was not failing because of one catastrophe but because of a thousand small Tuesdays on which four people came in and two of them bought nothing. She could see clearly that continuing exactly as she was would take her, at a comfortable pace, directly out of business."],
                    ['title' => 'A Bad Idea', 'content' => "Her nephew Callum, who was twenty and studying something involving spreadsheets, suggested she sell online. Ivy pointed out that roughly every bookshop in the country had already had that idea and that she could not compete with a warehouse. \"Then don't sell books,\" Callum said. \"Sell the thing you actually have.\" She asked him what he thought she actually had. He gestured at the worn armchairs, the handwritten recommendation cards, the regulars who came in on Thursdays and stayed for an hour without buying anything. \"This,\" he said."],
                    ['title' => 'Thursday Evenings', 'content' => "They started small, because small was all she could afford. From October, the shop stayed open until nine on Thursdays. There was a table, a kettle, and one author from within thirty miles talking for forty minutes about a book almost nobody had read. The first evening drew eleven people. The second drew nine, and Ivy nearly stopped. The fourth drew thirty-one, because a local historian had brought photographs of Carter Street in 1935 and half the street wanted to see whether their houses were in them."],
                    ['title' => 'What People Came For', 'content' => "By December, the Thursday evenings had a waiting list, which was an absurd phrase for a room with twenty-two chairs. Ivy noticed something she had not predicted: people who came for the talks bought books, but not the books they had come to hear about. They bought whatever they had picked up while waiting for the chairs to be arranged. Her stock started moving in patterns that no algorithm would have suggested, and for the first time in a decade, she ordered more copies of things instead of fewer."],
                    ['title' => 'The Meeting', 'content' => "In January, Ivy met the landlord with a folder and, for the first time, an argument. Footfall on Thursdays was up by a factor of six. Two neighbouring businesses had extended their own opening hours. She was not asking for charity; she was proposing a rent that rose in three stages over two years, tied to figures she was prepared to show him quarterly. He listened, asked three sharp questions, and said he would think about it. He phoned four days later and accepted the second of her three options."],
                    ['title' => 'Still Open', 'content' => "The shop did not become successful in any way that would interest a business magazine. It became, precisely, sustainable, which Ivy came to understand was a much rarer thing. On the anniversary of her father's opening day, she put a card in the window that simply read: FIFTY-ONE YEARS. A woman she had never met came in specifically to say she was glad, bought a novel she clearly had not planned to buy, and left. Ivy put the landlord's original letter in a frame in the back office, where she could see it."],
                ],
            ],

            // ============================ C1 ============================
            [
                'title' => 'The Weight of Small Decisions',
                'author' => 'Adele Ferrand',
                'level' => 'C1',
                'description' => 'A structural engineer discovers an error in a finished building, and learns what professional courage actually costs.',
                'pages' => [
                    ['title' => 'The Query', 'content' => "The email arrived on a Thursday afternoon, buried between a meeting invitation and a reminder about car park permits. A graduate engineer at the firm had been running through the calculations for the Meridian Centre as a training exercise, and had found something she could not reconcile. She apologised twice in three sentences, which told Nadia Kovač more about the culture of her own office than she was comfortable admitting. The building had been finished for fourteen months. Nadia read the attachment, and then read it again with her coat still on."],
                    ['title' => 'What the Numbers Showed', 'content' => "The discrepancy was not enormous, and it was not obvious. A load assumption for the third-floor plant room had been carried through from an earlier version of the design in which the ventilation equipment was significantly lighter. Somewhere in the endless revisions, the change had propagated into the architectural drawings but not into the structural model. The margin of safety on two transfer beams was consequently smaller than the standard required — not catastrophically so, but measurably, and in a building where several hundred people worked every day."],
                    ['title' => 'The Comfortable Interpretations', 'content' => "Over that weekend, Nadia assembled a small collection of reasons why the finding might not matter. The safety factors in the code were conservative by design. The actual equipment installed might well be lighter than the specification allowed. Nothing had cracked, deflected or complained in fourteen months of occupation. Each argument was individually defensible, and she recognised, with the particular discomfort of a competent person watching her own mind at work, that she was constructing them in a specific direction rather than following the evidence wherever it went."],
                    ['title' => 'The Partner', 'content' => "On Monday she took it to Duncan Reid, the partner who had signed off the design. He was sixty-one, three years from retirement, and genuinely well liked. He listened without interrupting, studied the calculation for a long time, and then said something Nadia had not anticipated. \"You're right,\" he said. \"And if we raise this formally, the firm is looking at remedial works, a professional indemnity claim, and a client who will never instruct us again.\" He paused. \"I'm not telling you not to. I'm telling you what it is.\""],
                    ['title' => 'The Cost of Being Right', 'content' => "The next four days were the clearest illustration Nadia had ever encountered of how institutions absorb inconvenient facts. Nobody instructed her to stay silent. Instead, meetings were rescheduled, the graduate was moved to another project for reasons of resourcing, and a senior colleague mentioned, apparently in passing, that the firm's bonus pool was under pressure this year. None of it was improper. All of it was perfectly legible. She understood that she was being invited, with great courtesy, to lose interest in the matter."],
                    ['title' => 'The Letter', 'content' => "She wrote to the client on the Friday, having first told Duncan she was going to. The letter set out the error plainly, without hedging and without dramatising it, and recommended an immediate load survey and a temporary restriction on additional plant. She copied in the firm's insurers, which meant the matter could no longer be quietly managed. Duncan read it before it went, made two small corrections to the wording, and signed it as well. \"For what it's worth,\" he said, \"I'd have regretted the other thing.\""],
                    ['title' => 'Consequences', 'content' => "The survey confirmed the discrepancy and, as it turned out, the installed equipment was indeed lighter than specified, so the building had never been at meaningful risk. Nadia found this less reassuring than everyone expected her to. The strengthening works cost the firm a great deal of money and the client relationship did not survive. She was not promoted that year. The graduate who had found the error was, at Nadia's specific and repeated insistence, kept on and moved into the structures team."],
                    ['title' => 'The Lecture', 'content' => "Some years later, Nadia was asked to speak to final-year students about professional ethics, a subject she had come to regard with suspicion because it was usually taught through dramatic scenarios involving collapsing bridges. She told them instead about a Thursday afternoon email and a weekend spent inventing reasons. \"You will not face one enormous decision,\" she said. \"You will face forty small ones, most of which you can lose without anybody noticing, including yourself. That is the actual test, and it is much harder than the one you are expecting.\""],
                ],
            ],

            // ============================ C2 ============================
            [
                'title' => "The Cartographer's Daughter",
                'author' => 'Nikolai Brandt',
                'level' => 'C2',
                'description' => 'A translator returns to her father\'s archive of maps and confronts the difference between accuracy and truth.',
                'pages' => [
                    ['title' => 'The Inheritance', 'content' => "What Vera inherited was not property in any sense the lawyer found straightforward to describe: eleven filing cabinets, some four thousand sheets of paper, and a lifetime's obsession with the coastline of a country that had, in the intervening decades, twice changed its name. Her father had been a cartographer of the old school, one of the last men to have surveyed a shoreline with instruments he could carry, and he had left instructions that were characteristically precise about the storage conditions and characteristically silent about what she was supposed to do with any of it."],
                    ['title' => 'Fieldwork', 'content' => "She had not thought about the maps for twenty years, having built a life in another language entirely, translating technical documents for people who paid promptly and asked nothing further of her. Yet within a fortnight of the funeral she found herself unrolling sheets on the floor of the empty flat, weighing the corners down with books, and recognising, with something between irritation and vertigo, the particular quality of attention in his hand. He had drawn the same forty kilometres of coast in 1971, in 1983, and again in 1996, and the three versions did not agree."],
                    ['title' => 'Three Coastlines', 'content' => "The discrepancies were not errors, as she first assumed, but the coast itself: a shoreline of soft cliffs and shifting spits that genuinely relocated itself, dozens of metres in some places, across the twenty-five years he had documented it. What unsettled her was not the movement but her father's response to it. Each successive map was drawn with the same absolute confidence, the same unwavering line, as though this particular arrangement of land and water were permanent. Nowhere in four thousand sheets had he indicated that any of it was provisional."],
                    ['title' => 'The Correspondence', 'content' => "In the fourth cabinet she found letters, and in the letters an argument that had apparently consumed a decade. A colleague in the hydrographic service had pressed him repeatedly to publish the surveys as a series, with dates and uncertainties marked, so that the rate of erosion could be studied. Her father had refused, in increasingly cold prose. A map, he wrote in 1988, is an instrument of navigation, not a diary. A sailor requires a line he can steer by. Give him three lines and a probability and you have given him nothing at all."],
                    ['title' => 'The Village', 'content' => "Vera drove out to the coast in November, largely to prove to herself that she could treat the whole business as a documentary exercise. The village marked on the 1971 sheet had lost its harbour road; the 1996 sheet showed a sea wall which had itself since failed. An elderly man in the single remaining shop told her, without prompting and with considerable feeling, that the maps had been wrong for years and that people had built where they should not have built because a chart had told them the land was theirs."],
                    ['title' => 'Two Kinds of Honesty', 'content' => "She thought about that conversation for a long time afterwards, because it cut directly against her instinct to defend him. Her father had not falsified anything; each survey had been meticulous on the day it was made. His omission was subtler and, she suspected, more common than outright error: he had presented provisional knowledge in a form that concealed its provisionality, because the alternative was less useful, and because usefulness was the value he had organised his entire professional life around. It was, she recognised, precisely the argument she made about her own translations."],
                    ['title' => 'The Decision', 'content' => "The hydrographic institute was willing to take the archive, on the condition that it be catalogued, which would occupy someone for the better part of two years. Vera negotiated a modest fee and an office with a window, and told her clients she would be unavailable until further notice. She began with 1971 and worked forwards, recording for each sheet the date, the instruments, the weather, and — in a column her father would have regarded as an act of vandalism — the estimated uncertainty of every line."],
                    ['title' => 'The Atlas', 'content' => "The volume that eventually resulted was not a map but an atlas of a moving thing, forty kilometres of coast rendered as a sequence rather than a state, each shoreline dated and hedged and openly unreliable. It was, by her father's standards, useless: no sailor could steer by it. Reviewers in two countries described it as a significant contribution. Vera kept a single sheet from 1983 framed above the desk, the confident unbroken line of a man who had genuinely believed that clarity was a form of kindness, and who had been, she thought, about half right."],
                ],
            ],
        ];
    }
}
